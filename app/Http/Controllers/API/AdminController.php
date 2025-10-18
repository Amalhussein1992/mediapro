<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = [
                'users' => [
                    'total' => User::count(),
                    'active' => User::where('is_active', true)->count(),
                    'newToday' => User::whereDate('created_at', today())->count(),
                    'newThisWeek' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'newThisMonth' => User::whereMonth('created_at', now()->month)->count(),
                ],
                'posts' => [
                    'total' => Post::count(),
                    'published' => Post::where('status', 'published')->count(),
                    'scheduled' => Post::where('status', 'scheduled')->count(),
                    'draft' => Post::where('status', 'draft')->count(),
                    'failed' => Post::where('status', 'failed')->count(),
                    'todayPublished' => Post::where('status', 'published')
                        ->whereDate('published_at', today())->count(),
                ],
                'socialAccounts' => [
                    'total' => SocialAccount::count(),
                    'active' => SocialAccount::where('is_active', true)->count(),
                    'byPlatform' => SocialAccount::select('platform', DB::raw('count(*) as count'))
                        ->groupBy('platform')
                        ->get()
                        ->pluck('count', 'platform'),
                ],
                'subscriptions' => [
                    'active' => Subscription::where('status', 'active')->count(),
                    'expired' => Subscription::where('status', 'expired')->count(),
                    'cancelled' => Subscription::where('status', 'cancelled')->count(),
                ],
                'system' => [
                    'storageUsed' => $this->getStorageUsed(),
                    'databaseSize' => $this->getDatabaseSize(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all users with filters
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $search = $request->input('search');
            $status = $request->input('status');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $query = User::query()->with(['subscription']);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }

            $users = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user details
     */
    public function getUserDetails(int $id): JsonResponse
    {
        try {
            $user = User::with(['subscription', 'socialAccounts', 'posts'])->findOrFail($id);

            $details = [
                'user' => $user,
                'stats' => [
                    'totalPosts' => $user->posts()->count(),
                    'publishedPosts' => $user->posts()->where('status', 'published')->count(),
                    'connectedAccounts' => $user->socialAccounts()->count(),
                    'totalEngagement' => $user->posts()->sum('total_likes') +
                                        $user->posts()->sum('total_comments') +
                                        $user->posts()->sum('total_shares'),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $details,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Don't allow deleting yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account',
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all posts with filters (admin view)
     */
    public function getAllPosts(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $status = $request->input('status');
            $userId = $request->input('user_id');

            $query = Post::with(['user:id,name,email']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $posts = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete post (admin)
     */
    public function deletePost(int $id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get system logs
     */
    public function getSystemLogs(Request $request): JsonResponse
    {
        try {
            $lines = $request->input('lines', 100);
            $logFile = storage_path('logs/laravel.log');

            if (!file_exists($logFile)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ], 200);
            }

            $logs = $this->tailFile($logFile, $lines);

            return response()->json([
                'success' => true,
                'data' => $logs,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper methods
     */

    private function getStorageUsed(): string
    {
        $bytes = 0;
        $path = storage_path('app');

        if (file_exists($path)) {
            $bytes = $this->getDirSize($path);
        }

        return $this->formatBytes($bytes);
    }

    private function getDatabaseSize(): string
    {
        try {
            $database = env('DB_DATABASE');
            $result = DB::select("
                SELECT
                    SUM(data_length + index_length) as size
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$database]);

            $bytes = $result[0]->size ?? 0;
            return $this->formatBytes($bytes);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getDirSize(string $dir): int
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->getDirSize($each);
        }
        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function tailFile(string $file, int $lines): array
    {
        $handle = fopen($file, 'r');
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = [];

        while ($linecounter > 0) {
            $t = ' ';
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[] = fgets($handle);
            if ($beginning) break;
        }

        fclose($handle);
        return array_reverse($text);
    }
}
