<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Services\SocialMediaPublisher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the posts for authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $status = $request->input('status');
            $platform = $request->input('platform');

            $query = Post::where('user_id', Auth::id())
                ->with('user:id,name,email');

            // Filter by status if provided
            if ($status) {
                $query->where('status', $status);
            }

            // Filter by platform if provided
            if ($platform) {
                $query->whereJsonContains('platforms', $platform);
            }

            // Order by created_at descending
            $query->orderBy('created_at', 'desc');

            $posts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Posts retrieved successfully',
                'data' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created post in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string',
                'media' => 'nullable|array',
                'media.*' => 'nullable|string',
                'platforms' => 'required|array',
                'platforms.*' => 'required|string|in:facebook,twitter,instagram,linkedin,tiktok,youtube',
                'status' => 'required|string|in:draft,scheduled,published,failed',
                'scheduled_at' => 'nullable|date|after:now',
                'published_at' => 'nullable|date',
                'analytics' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check subscription limits for published posts
            $user = Auth::user();
            if (in_array($request->status, ['published', 'scheduled'])) {
                // Get user's subscription plan
                $subscriptionPlan = \DB::table('subscription_plans')
                    ->where('id', $user->subscription_plan_id)
                    ->first();

                if ($subscriptionPlan) {
                    // Count posts this month
                    $postsThisMonth = Post::where('user_id', $user->id)
                        ->whereIn('status', ['published', 'scheduled'])
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)
                        ->count();

                    if ($postsThisMonth >= $subscriptionPlan->max_posts_per_month) {
                        return response()->json([
                            'success' => false,
                            'message' => 'You have reached your monthly post limit. Please upgrade your plan.',
                            'limit' => $subscriptionPlan->max_posts_per_month,
                            'current' => $postsThisMonth,
                        ], 403);
                    }
                }
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            $post = Post::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post->load('user:id,name,email'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified post.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $post = Post::where('id', $id)
                ->where('user_id', Auth::id())
                ->with('user:id,name,email')
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Post retrieved successfully',
                'data' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified post in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $post = Post::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'content' => 'sometimes|required|string',
                'media' => 'nullable|array',
                'media.*' => 'nullable|string',
                'platforms' => 'sometimes|required|array',
                'platforms.*' => 'required|string|in:facebook,twitter,instagram,linkedin,tiktok,youtube',
                'status' => 'sometimes|required|string|in:draft,scheduled,published,failed',
                'scheduled_at' => 'nullable|date',
                'published_at' => 'nullable|date',
                'analytics' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $post->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => $post->load('user:id,name,email'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $post = Post::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found',
                ], 404);
            }

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
     * Publish a post to social media platforms
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function publish(Request $request, int $id): JsonResponse
    {
        try {
            $post = Post::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            // Get social accounts for the selected platforms
            $socialAccounts = SocialAccount::where('user_id', Auth::id())
                ->whereIn('platform', $post->platforms ?? [])
                ->where('is_active', true)
                ->get();

            if ($socialAccounts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active social accounts found for selected platforms',
                ], 400);
            }

            $publisher = new SocialMediaPublisher();
            $results = [];
            $hasSuccess = false;
            $hasFailure = false;

            // Publish to each platform
            foreach ($socialAccounts as $account) {
                $result = $publisher->publish($post, $account);
                $results[$account->platform] = $result;

                if ($result['success']) {
                    $hasSuccess = true;

                    // Update post platforms array with published status
                    $platforms = $post->platforms ?? [];
                    $platformIndex = array_search($account->platform, $platforms);

                    if ($platformIndex !== false) {
                        $platforms[$platformIndex] = [
                            'platform' => $account->platform,
                            'status' => 'published',
                            'platform_post_id' => $result['platform_post_id'],
                            'platform_url' => $result['platform_url'],
                            'published_at' => $result['published_at'],
                        ];
                    }

                    $post->platforms = $platforms;
                } else {
                    $hasFailure = true;
                }
            }

            // Update post status
            if ($hasSuccess && !$hasFailure) {
                $post->status = 'published';
                $post->published_at = now();
            } elseif ($hasSuccess && $hasFailure) {
                $post->status = 'published'; // Partially published
            } else {
                $post->status = 'failed';
            }

            $post->save();

            return response()->json([
                'success' => true,
                'message' => $hasSuccess ? 'Post published successfully' : 'Failed to publish to all platforms',
                'data' => [
                    'post' => $post,
                    'results' => $results
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
