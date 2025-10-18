<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\Subscription;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ApiUsageLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class EnhancedAdminController extends Controller
{
    /**
     * Get enhanced dashboard statistics with real-time data
     */
    public function getEnhancedDashboardStats(Request $request): JsonResponse
    {
        try {
            // Cache for 5 minutes
            $stats = Cache::remember('admin_dashboard_stats', 300, function () {
                return [
                    'users' => $this->getUserStats(),
                    'posts' => $this->getPostStats(),
                    'socialAccounts' => $this->getSocialAccountStats(),
                    'subscriptions' => $this->getSubscriptionStats(),
                    'system' => $this->getSystemStats(),
                    'apiUsage' => $this->getApiUsageStats(),
                    'recentActivity' => $this->getRecentActivity(),
                    'topUsers' => $this->getTopUsers(),
                    'contentModeration' => $this->getContentModerationStats(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toIso8601String(),
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
     * Get content moderation queue
     */
    public function getModerationQueue(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status', 'pending');
            $perPage = $request->input('per_page', 20);

            $posts = Post::with(['user:id,name,email'])
                ->where('moderation_status', $status)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch moderation queue',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Moderate post (approve/reject)
     */
    public function moderatePost(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject,flag',
                'note' => 'nullable|string|max:500',
                'flag_reasons' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $post = Post::findOrFail($id);
            $action = $request->input('action');

            $updateData = [
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
                'moderation_note' => $request->input('note'),
            ];

            switch ($action) {
                case 'approve':
                    $updateData['moderation_status'] = 'approved';
                    $updateData['is_flagged'] = false;
                    break;
                case 'reject':
                    $updateData['moderation_status'] = 'rejected';
                    break;
                case 'flag':
                    $updateData['is_flagged'] = true;
                    $updateData['flag_reasons'] = $request->input('flag_reasons');
                    break;
            }

            $post->update($updateData);

            // Log the action
            AuditLog::logAction(
                "post_{$action}",
                auth()->id(),
                'Post',
                $id,
                null,
                $updateData,
                "Post {$action}ed by admin"
            );

            // Notify user
            Notification::createNotification(
                $post->user_id,
                "post_{$action}",
                "Post {$action}ed",
                "Your post has been {$action}ed by a moderator.",
                ['post_id' => $id],
                $action === 'reject' ? 'high' : 'normal'
            );

            return response()->json([
                'success' => true,
                'message' => "Post {$action}ed successfully",
                'data' => $post->fresh(['user']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to moderate post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk operations on users
     */
    public function bulkUserOperations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'action' => 'required|in:activate,deactivate,delete,change_role',
                'role' => 'required_if:action,change_role|in:user,moderator,admin',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userIds = $request->input('user_ids');
            $action = $request->input('action');
            $affected = 0;

            foreach ($userIds as $userId) {
                // Don't allow operations on self
                if ($userId === auth()->id()) {
                    continue;
                }

                $user = User::find($userId);
                if (!$user) continue;

                switch ($action) {
                    case 'activate':
                        $user->update(['is_active' => true]);
                        $affected++;
                        break;
                    case 'deactivate':
                        $user->update(['is_active' => false]);
                        $affected++;
                        break;
                    case 'delete':
                        $user->delete();
                        $affected++;
                        break;
                    case 'change_role':
                        $user->update(['role' => $request->input('role')]);
                        $affected++;
                        break;
                }

                // Log the action
                AuditLog::logAction(
                    "bulk_user_{$action}",
                    auth()->id(),
                    'User',
                    $userId,
                    null,
                    ['action' => $action],
                    "Bulk operation: {$action} on user {$userId}"
                );
            }

            return response()->json([
                'success' => true,
                'message' => "{$affected} users processed successfully",
                'data' => ['affected' => $affected],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk operations on posts
     */
    public function bulkPostOperations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'post_ids' => 'required|array',
                'post_ids.*' => 'exists:posts,id',
                'action' => 'required|in:approve,reject,delete,publish,unpublish',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $postIds = $request->input('post_ids');
            $action = $request->input('action');
            $affected = 0;

            foreach ($postIds as $postId) {
                $post = Post::find($postId);
                if (!$post) continue;

                switch ($action) {
                    case 'approve':
                        $post->update([
                            'moderation_status' => 'approved',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ]);
                        $affected++;
                        break;
                    case 'reject':
                        $post->update([
                            'moderation_status' => 'rejected',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ]);
                        $affected++;
                        break;
                    case 'delete':
                        $post->delete();
                        $affected++;
                        break;
                    case 'publish':
                        $post->update(['status' => 'published', 'published_at' => now()]);
                        $affected++;
                        break;
                    case 'unpublish':
                        $post->update(['status' => 'draft']);
                        $affected++;
                        break;
                }

                // Log the action
                AuditLog::logAction(
                    "bulk_post_{$action}",
                    auth()->id(),
                    'Post',
                    $postId,
                    null,
                    ['action' => $action],
                    "Bulk operation: {$action} on post {$postId}"
                );
            }

            return response()->json([
                'success' => true,
                'message' => "{$affected} posts processed successfully",
                'data' => ['affected' => $affected],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get API usage analytics
     */
    public function getApiUsageAnalytics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', now()->subDays(7));
            $endDate = $request->input('end_date', now());

            $analytics = [
                'totalRequests' => ApiUsageLog::dateRange($startDate, $endDate)->count(),
                'successRate' => $this->calculateSuccessRate($startDate, $endDate),
                'avgResponseTime' => ApiUsageLog::dateRange($startDate, $endDate)->avg('response_time_ms'),
                'topEndpoints' => $this->getTopEndpoints($startDate, $endDate),
                'errorRate' => $this->calculateErrorRate($startDate, $endDate),
                'requestsByDay' => $this->getRequestsByDay($startDate, $endDate),
                'topUsers' => $this->getTopApiUsers($startDate, $endDate),
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch API usage analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 50);
            $action = $request->input('action');
            $userId = $request->input('user_id');
            $modelType = $request->input('model_type');

            $query = AuditLog::with(['user:id,name,email']);

            if ($action) {
                $query->where('action', $action);
            }

            if ($userId) {
                $query->where('user_id', $userId);
            }

            if ($modelType) {
                $query->where('model_type', $modelType);
            }

            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $logs,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch audit logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subscription management data
     */
    public function getSubscriptionManagement(Request $request): JsonResponse
    {
        try {
            $stats = [
                'overview' => [
                    'active' => Subscription::where('status', 'active')->count(),
                    'expiring_soon' => Subscription::where('status', 'active')
                        ->where('end_date', '<=', now()->addDays(7))
                        ->count(),
                    'expired' => Subscription::where('status', 'expired')->count(),
                    'cancelled' => Subscription::where('status', 'cancelled')->count(),
                    'revenue_this_month' => $this->calculateMonthlyRevenue(),
                ],
                'recent_subscriptions' => Subscription::with(['user:id,name,email'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
                'expiring_soon' => Subscription::with(['user:id,name,email'])
                    ->where('status', 'active')
                    ->where('end_date', '<=', now()->addDays(7))
                    ->orderBy('end_date', 'asc')
                    ->limit(10)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate comprehensive report
     */
    public function generateReport(Request $request): JsonResponse
    {
        try {
            $reportType = $request->input('type', 'overview'); // overview, users, posts, analytics
            $startDate = $request->input('start_date', now()->subDays(30));
            $endDate = $request->input('end_date', now());

            $report = [
                'type' => $reportType,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
                'generated_at' => now()->toIso8601String(),
                'data' => $this->generateReportData($reportType, $startDate, $endDate),
            ];

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Report generated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'newToday' => User::whereDate('created_at', today())->count(),
            'newThisWeek' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'newThisMonth' => User::whereMonth('created_at', now()->month)->count(),
            'byRole' => User::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get()
                ->pluck('count', 'role'),
        ];
    }

    private function getPostStats(): array
    {
        return [
            'total' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'scheduled' => Post::where('status', 'scheduled')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'failed' => Post::where('status', 'failed')->count(),
            'todayPublished' => Post::where('status', 'published')
                ->whereDate('published_at', today())->count(),
            'thisWeekPublished' => Post::where('status', 'published')
                ->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }

    private function getSocialAccountStats(): array
    {
        return [
            'total' => SocialAccount::count(),
            'active' => SocialAccount::where('is_active', true)->count(),
            'inactive' => SocialAccount::where('is_active', false)->count(),
            'byPlatform' => SocialAccount::select('platform', DB::raw('count(*) as count'))
                ->groupBy('platform')
                ->get()
                ->pluck('count', 'platform'),
        ];
    }

    private function getSubscriptionStats(): array
    {
        return [
            'active' => Subscription::where('status', 'active')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'expiringSoon' => Subscription::where('status', 'active')
                ->where('end_date', '<=', now()->addDays(7))
                ->count(),
        ];
    }

    private function getSystemStats(): array
    {
        return [
            'storageUsed' => $this->getStorageUsed(),
            'databaseSize' => $this->getDatabaseSize(),
            'totalApiCalls' => ApiUsageLog::count(),
            'totalAuditLogs' => AuditLog::count(),
        ];
    }

    private function getApiUsageStats(): array
    {
        $today = ApiUsageLog::whereDate('created_at', today())->count();
        $yesterday = ApiUsageLog::whereDate('created_at', today()->subDay())->count();

        return [
            'today' => $today,
            'yesterday' => $yesterday,
            'change' => $yesterday > 0 ? (($today - $yesterday) / $yesterday) * 100 : 0,
            'avgResponseTime' => ApiUsageLog::whereDate('created_at', today())->avg('response_time_ms'),
            'errorRate' => $this->calculateErrorRate(today(), now()),
        ];
    }

    private function getContentModerationStats(): array
    {
        return [
            'pending' => Post::where('moderation_status', 'pending')->count(),
            'approved' => Post::where('moderation_status', 'approved')->count(),
            'rejected' => Post::where('moderation_status', 'rejected')->count(),
            'flagged' => Post::where('is_flagged', true)->count(),
        ];
    }

    private function getRecentActivity(): array
    {
        return AuditLog::with(['user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getTopUsers(): array
    {
        return User::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    private function calculateSuccessRate($startDate, $endDate): float
    {
        $total = ApiUsageLog::dateRange($startDate, $endDate)->count();
        if ($total === 0) return 100;

        $successful = ApiUsageLog::dateRange($startDate, $endDate)
            ->where('response_code', '<', 400)
            ->count();

        return ($successful / $total) * 100;
    }

    private function calculateErrorRate($startDate, $endDate): float
    {
        $total = ApiUsageLog::dateRange($startDate, $endDate)->count();
        if ($total === 0) return 0;

        $errors = ApiUsageLog::dateRange($startDate, $endDate)->errors()->count();

        return ($errors / $total) * 100;
    }

    private function getTopEndpoints($startDate, $endDate): array
    {
        return ApiUsageLog::select('endpoint', DB::raw('count(*) as count'))
            ->dateRange($startDate, $endDate)
            ->groupBy('endpoint')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getRequestsByDay($startDate, $endDate): array
    {
        return ApiUsageLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->dateRange($startDate, $endDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    private function getTopApiUsers($startDate, $endDate): array
    {
        return ApiUsageLog::select('user_id', DB::raw('count(*) as request_count'))
            ->dateRange($startDate, $endDate)
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('request_count', 'desc')
            ->limit(10)
            ->with('user:id,name,email')
            ->get()
            ->toArray();
    }

    private function calculateMonthlyRevenue(): float
    {
        // This is a placeholder - implement based on your payment system
        return 0.00;
    }

    private function generateReportData(string $type, $startDate, $endDate): array
    {
        // Generate comprehensive report data based on type
        return [
            'summary' => 'Report data generated',
            'type' => $type,
        ];
    }

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
}
