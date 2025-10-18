<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\Analytics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get dashboard overview analytics
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Date range (default: last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30));
        $endDate = $request->input('end_date', Carbon::now());

        // Basic stats
        $stats = [
            'total_posts' => $user->posts()->count(),
            'published_posts' => $user->posts()->where('status', 'published')->count(),
            'scheduled_posts' => $user->posts()->where('status', 'scheduled')->count(),
            'draft_posts' => $user->posts()->where('status', 'draft')->count(),
            'total_social_accounts' => $user->socialAccounts()->count(),
            'active_social_accounts' => $user->socialAccounts()->where('is_active', true)->count(),
        ];

        // Posts by status in date range
        $postsTimeline = $user->posts()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                'status'
            )
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        // Platform distribution
        $platformStats = $user->posts()
            ->where('status', 'published')
            ->get()
            ->flatMap(function ($post) {
                return $post->platforms ?? [];
            })
            ->countBy()
            ->map(function ($count, $platform) {
                return [
                    'platform' => $platform,
                    'count' => $count
                ];
            })
            ->values();

        // Engagement metrics
        $engagementMetrics = [
            'total_likes' => $user->posts()->sum(DB::raw("JSON_EXTRACT(analytics, '$.likes')")),
            'total_comments' => $user->posts()->sum(DB::raw("JSON_EXTRACT(analytics, '$.comments')")),
            'total_shares' => $user->posts()->sum(DB::raw("JSON_EXTRACT(analytics, '$.shares')")),
            'total_views' => $user->posts()->sum(DB::raw("JSON_EXTRACT(analytics, '$.views')")),
        ];

        // Best performing posts
        $topPosts = $user->posts()
            ->where('status', 'published')
            ->orderByRaw("JSON_EXTRACT(analytics, '$.likes') DESC")
            ->take(5)
            ->get(['id', 'content', 'analytics', 'published_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'posts_timeline' => $postsTimeline,
                'platform_stats' => $platformStats,
                'engagement' => $engagementMetrics,
                'top_posts' => $topPosts,
            ]
        ]);
    }

    /**
     * Get detailed post analytics
     */
    public function postAnalytics(Request $request, $postId)
    {
        $user = $request->user();
        $post = $user->posts()->findOrFail($postId);

        $analytics = [
            'post' => $post,
            'metrics' => $post->analytics ?? [],
            'engagement_rate' => $this->calculateEngagementRate($post),
            'best_time_posted' => $post->published_at,
            'platforms_performance' => $this->getPlatformPerformance($post),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get social account analytics
     */
    public function accountAnalytics(Request $request, $accountId)
    {
        $user = $request->user();
        $account = $user->socialAccounts()->findOrFail($accountId);

        $posts = $user->posts()
            ->where('status', 'published')
            ->whereJsonContains('platforms', $account->platform)
            ->get();

        $analytics = [
            'account' => $account,
            'total_posts' => $posts->count(),
            'total_engagement' => $posts->sum(function ($post) {
                $analytics = $post->analytics ?? [];
                return ($analytics['likes'] ?? 0) +
                       ($analytics['comments'] ?? 0) +
                       ($analytics['shares'] ?? 0);
            }),
            'average_engagement' => $posts->count() > 0
                ? $posts->avg(function ($post) {
                    $analytics = $post->analytics ?? [];
                    return ($analytics['likes'] ?? 0) +
                           ($analytics['comments'] ?? 0) +
                           ($analytics['shares'] ?? 0);
                })
                : 0,
            'followers' => $account->followers_count ?? 0,
            'posts_list' => $posts->take(10),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get engagement trends
     */
    public function engagementTrends(Request $request)
    {
        $user = $request->user();
        $days = $request->input('days', 30);

        $startDate = Carbon::now()->subDays($days);

        $trends = $user->posts()
            ->where('status', 'published')
            ->whereBetween('published_at', [$startDate, Carbon::now()])
            ->select(
                DB::raw('DATE(published_at) as date'),
                DB::raw('SUM(JSON_EXTRACT(analytics, "$.likes")) as total_likes'),
                DB::raw('SUM(JSON_EXTRACT(analytics, "$.comments")) as total_comments'),
                DB::raw('SUM(JSON_EXTRACT(analytics, "$.shares")) as total_shares'),
                DB::raw('SUM(JSON_EXTRACT(analytics, "$.views")) as total_views')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $days . ' days',
                'trends' => $trends
            ]
        ]);
    }

    /**
     * Get best posting times
     */
    public function bestPostingTimes(Request $request)
    {
        $user = $request->user();

        $postsByHour = $user->posts()
            ->where('status', 'published')
            ->select(
                DB::raw('HOUR(published_at) as hour'),
                DB::raw('AVG(JSON_EXTRACT(analytics, "$.likes") + JSON_EXTRACT(analytics, "$.comments") + JSON_EXTRACT(analytics, "$.shares")) as avg_engagement')
            )
            ->groupBy('hour')
            ->orderBy('avg_engagement', 'desc')
            ->get();

        $postsByDay = $user->posts()
            ->where('status', 'published')
            ->select(
                DB::raw('DAYNAME(published_at) as day'),
                DB::raw('AVG(JSON_EXTRACT(analytics, "$.likes") + JSON_EXTRACT(analytics, "$.comments") + JSON_EXTRACT(analytics, "$.shares")) as avg_engagement')
            )
            ->groupBy('day')
            ->orderBy('avg_engagement', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'best_hours' => $postsByHour,
                'best_days' => $postsByDay,
                'recommendation' => $this->generatePostingRecommendation($postsByHour, $postsByDay)
            ]
        ]);
    }

    /**
     * Calculate engagement rate
     */
    private function calculateEngagementRate($post)
    {
        $analytics = $post->analytics ?? [];
        $views = $analytics['views'] ?? 0;

        if ($views == 0) return 0;

        $engagement = ($analytics['likes'] ?? 0) +
                     ($analytics['comments'] ?? 0) +
                     ($analytics['shares'] ?? 0);

        return round(($engagement / $views) * 100, 2);
    }

    /**
     * Get platform performance for a post
     */
    private function getPlatformPerformance($post)
    {
        $platforms = $post->platforms ?? [];
        $analytics = $post->analytics ?? [];

        return collect($platforms)->map(function ($platform) use ($analytics) {
            return [
                'platform' => $platform,
                'likes' => $analytics['likes'] ?? 0,
                'comments' => $analytics['comments'] ?? 0,
                'shares' => $analytics['shares'] ?? 0,
                'views' => $analytics['views'] ?? 0,
            ];
        });
    }

    /**
     * Generate posting recommendation
     */
    private function generatePostingRecommendation($hourData, $dayData)
    {
        $bestHour = $hourData->first();
        $bestDay = $dayData->first();

        return [
            'best_hour' => $bestHour->hour ?? 12,
            'best_day' => $bestDay->day ?? 'Monday',
            'message' => "Best time to post is on " . ($bestDay->day ?? 'Monday') .
                        " at " . ($bestHour->hour ?? 12) . ":00"
        ];
    }
}
