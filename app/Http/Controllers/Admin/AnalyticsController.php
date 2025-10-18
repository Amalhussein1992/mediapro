<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Get total users
        $totalUsers = User::count();

        // Get users from last month for comparison
        $lastMonthUsers = User::where('created_at', '<', Carbon::now()->subMonth())->count();
        $usersGrowth = $lastMonthUsers > 0
            ? (($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100
            : 0;

        // Get total posts
        $totalPosts = Post::count();

        // Get posts from last month for comparison
        $lastMonthPosts = Post::where('created_at', '<', Carbon::now()->subMonth())->count();
        $postsGrowth = $lastMonthPosts > 0
            ? (($totalPosts - $lastMonthPosts) / $lastMonthPosts) * 100
            : 0;

        // Get total engagement (from analytics JSON field)
        $totalEngagement = 0;
        $lastMonthEngagement = 0;

        $allPosts = Post::whereNotNull('analytics')->get();
        foreach ($allPosts as $post) {
            $analytics = json_decode($post->analytics, true) ?? [];
            $engagement = ($analytics['likes'] ?? 0) + ($analytics['comments'] ?? 0) + ($analytics['shares'] ?? 0);
            $totalEngagement += $engagement;

            if ($post->created_at < Carbon::now()->subMonth()) {
                $lastMonthEngagement += $engagement;
            }
        }

        $engagementGrowth = $lastMonthEngagement > 0
            ? (($totalEngagement - $lastMonthEngagement) / $lastMonthEngagement) * 100
            : 0;

        // AI Generations count (posts with ai_generated = true or has ai_content)
        $aiGenerations = Post::where('ai_generated', true)
            ->orWhereNotNull('ai_content')
            ->count();

        $lastMonthAiGenerations = Post::where(function($query) {
                $query->where('ai_generated', true)
                      ->orWhereNotNull('ai_content');
            })
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $aiGrowth = $lastMonthAiGenerations > 0
            ? (($aiGenerations - $lastMonthAiGenerations) / $lastMonthAiGenerations) * 100
            : 0;

        // Get platform performance
        $platformStats = DB::table('posts')
            ->select('platform', DB::raw('COUNT(*) as post_count'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->get();

        $platforms = [];
        $totalPlatformPosts = $platformStats->sum('post_count');

        foreach ($platformStats as $stat) {
            $percentage = $totalPlatformPosts > 0
                ? ($stat->post_count / $totalPlatformPosts) * 100
                : 0;

            // Get engagement for this platform from analytics JSON
            $platformEngagement = 0;
            $platformPosts = Post::where('platforms', 'LIKE', '%' . $stat->platform . '%')
                ->whereNotNull('analytics')
                ->get();

            foreach ($platformPosts as $post) {
                $analytics = json_decode($post->analytics, true) ?? [];
                $platformEngagement += ($analytics['likes'] ?? 0) + ($analytics['comments'] ?? 0) + ($analytics['shares'] ?? 0);
            }

            $platforms[] = [
                'name' => ucfirst($stat->platform),
                'percentage' => round($percentage, 1),
                'posts' => $stat->post_count,
                'engagement' => $platformEngagement,
            ];
        }

        // Sort by percentage desc
        usort($platforms, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        return view('admin.analytics.index', [
            'totalUsers' => $totalUsers,
            'usersGrowth' => round($usersGrowth, 1),
            'totalPosts' => $totalPosts,
            'postsGrowth' => round($postsGrowth, 1),
            'totalEngagement' => $totalEngagement,
            'engagementGrowth' => round($engagementGrowth, 1),
            'aiGenerations' => $aiGenerations,
            'aiGrowth' => round($aiGrowth, 1),
            'platforms' => $platforms,
        ]);
    }
}
