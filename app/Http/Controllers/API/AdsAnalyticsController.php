<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdsCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdsAnalyticsController extends Controller
{
    /**
     * Get ads dashboard metrics
     */
    public function getDashboardMetrics(Request $request)
    {
        $user = $request->user();
        $timeframe = $request->input('timeframe', '7d'); // 7d, 30d, 90d

        // Parse timeframe
        $days = match($timeframe) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 7,
        };

        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Get campaigns in the timeframe
        $campaigns = $user->adsCampaigns()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate metrics
        $totalCampaigns = $campaigns->count();
        $activeCampaigns = $campaigns->where('status', 'active')->count();

        $totalSpend = $campaigns->sum(function ($campaign) {
            return $campaign->analytics['spend'] ?? 0;
        });

        $totalImpressions = $campaigns->sum(function ($campaign) {
            return $campaign->analytics['impressions'] ?? 0;
        });

        $totalClicks = $campaigns->sum(function ($campaign) {
            return $campaign->analytics['clicks'] ?? 0;
        });

        $totalConversions = $campaigns->sum(function ($campaign) {
            return $campaign->analytics['conversions'] ?? 0;
        });

        // Calculate averages
        $avgCTR = $totalImpressions > 0
            ? round(($totalClicks / $totalImpressions) * 100, 2)
            : 0;

        $avgCPC = $totalClicks > 0
            ? round($totalSpend / $totalClicks, 2)
            : 0;

        $avgROAS = $totalSpend > 0
            ? round($totalConversions / $totalSpend, 2)
            : 0;

        // Budget utilization
        $totalBudget = $campaigns->sum('budget');
        $budgetUtilization = $totalBudget > 0
            ? round(($totalSpend / $totalBudget) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'totalCampaigns' => $totalCampaigns,
                'activeCampaigns' => $activeCampaigns,
                'totalSpend' => $totalSpend,
                'totalImpressions' => $totalImpressions,
                'totalClicks' => $totalClicks,
                'totalConversions' => $totalConversions,
                'avgCTR' => $avgCTR,
                'avgCPC' => $avgCPC,
                'avgROAS' => $avgROAS,
                'budgetUtilization' => $budgetUtilization,
                'timeframe' => $timeframe,
            ]
        ]);
    }

    /**
     * Get campaign analytics
     */
    public function campaignAnalytics(Request $request, $campaignId)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($campaignId);

        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);

        // Get daily performance data
        $dailyPerformance = $this->getDailyPerformance($campaign, $startDate);

        // Platform breakdown
        $platformBreakdown = $this->getPlatformBreakdown($campaign);

        // Ad set performance
        $adSetPerformance = $campaign->adSets()->get()->map(function ($adSet) {
            return [
                'id' => $adSet->id,
                'name' => $adSet->name,
                'spend' => $adSet->analytics['spend'] ?? 0,
                'impressions' => $adSet->analytics['impressions'] ?? 0,
                'clicks' => $adSet->analytics['clicks'] ?? 0,
                'conversions' => $adSet->analytics['conversions'] ?? 0,
                'ctr' => $this->calculateCTR($adSet),
                'cpc' => $this->calculateCPC($adSet),
                'status' => $adSet->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'campaign' => [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'objective' => $campaign->objective,
                    'status' => $campaign->status,
                    'budget' => $campaign->budget,
                    'spend' => $campaign->analytics['spend'] ?? 0,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                ],
                'daily_performance' => $dailyPerformance,
                'platform_breakdown' => $platformBreakdown,
                'ad_set_performance' => $adSetPerformance,
                'total_metrics' => $campaign->analytics ?? [],
            ]
        ]);
    }

    /**
     * Get campaign insights
     */
    public function campaignInsights(Request $request, $campaignId)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($campaignId);

        $analytics = $campaign->analytics ?? [];

        // Generate insights
        $insights = [];

        // CTR insight
        $ctr = $this->calculateCTR($campaign);
        if ($ctr < 1) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Low Click-Through Rate',
                'message' => "Your CTR is {$ctr}%. Consider improving your ad creative or targeting.",
                'metric' => 'ctr',
                'value' => $ctr,
            ];
        } elseif ($ctr > 5) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Excellent Click-Through Rate',
                'message' => "Your CTR is {$ctr}%. Your ads are resonating well with your audience!",
                'metric' => 'ctr',
                'value' => $ctr,
            ];
        }

        // Budget insight
        $spent = $analytics['spend'] ?? 0;
        $budget = $campaign->budget;
        $utilization = $budget > 0 ? ($spent / $budget) * 100 : 0;

        if ($utilization > 80) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Budget Nearly Exhausted',
                'message' => "You've used {$utilization}% of your budget. Consider increasing it if the campaign is performing well.",
                'metric' => 'budget',
                'value' => $utilization,
            ];
        }

        // Performance trend
        $recentPerformance = $this->getRecentPerformanceTrend($campaign);
        if ($recentPerformance['trend'] === 'improving') {
            $insights[] = [
                'type' => 'success',
                'title' => 'Performance Improving',
                'message' => 'Your campaign performance has been improving over the last 7 days.',
                'metric' => 'trend',
                'value' => 'up',
            ];
        } elseif ($recentPerformance['trend'] === 'declining') {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Performance Declining',
                'message' => 'Your campaign performance has been declining. Consider refreshing your creatives or adjusting targeting.',
                'metric' => 'trend',
                'value' => 'down',
            ];
        }

        // Best performing platform
        $platformBreakdown = $this->getPlatformBreakdown($campaign);
        if ($platformBreakdown->isNotEmpty()) {
            $bestPlatform = $platformBreakdown->sortByDesc('conversions')->first();
            $insights[] = [
                'type' => 'info',
                'title' => 'Top Performing Platform',
                'message' => "{$bestPlatform['platform']} is your best performing platform with {$bestPlatform['conversions']} conversions.",
                'metric' => 'platform',
                'value' => $bestPlatform['platform'],
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'insights' => $insights,
                'recommendations' => $this->generateRecommendations($campaign),
            ]
        ]);
    }

    /**
     * Calculate CTR
     */
    private function calculateCTR($campaign)
    {
        $analytics = $campaign->analytics ?? [];
        $impressions = $analytics['impressions'] ?? 0;
        $clicks = $analytics['clicks'] ?? 0;

        return $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;
    }

    /**
     * Calculate CPC
     */
    private function calculateCPC($campaign)
    {
        $analytics = $campaign->analytics ?? [];
        $spend = $analytics['spend'] ?? 0;
        $clicks = $analytics['clicks'] ?? 0;

        return $clicks > 0 ? round($spend / $clicks, 2) : 0;
    }

    /**
     * Get daily performance
     */
    private function getDailyPerformance($campaign, $startDate)
    {
        // Mock daily data - In production, this would come from a daily_analytics table
        $days = Carbon::now()->diffInDays($startDate);
        $dailyData = [];

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dailyData[] = [
                'date' => $date,
                'impressions' => rand(100, 1000),
                'clicks' => rand(10, 100),
                'conversions' => rand(1, 20),
                'spend' => rand(10, 100),
            ];
        }

        return collect($dailyData)->reverse()->values();
    }

    /**
     * Get platform breakdown
     */
    private function getPlatformBreakdown($campaign)
    {
        $platforms = $campaign->platforms ?? [];
        $analytics = $campaign->analytics ?? [];

        return collect($platforms)->map(function ($platform) use ($analytics) {
            // In production, you'd have per-platform analytics
            $platformAnalytics = $analytics['platforms'][$platform] ?? [];

            return [
                'platform' => $platform,
                'impressions' => $platformAnalytics['impressions'] ?? 0,
                'clicks' => $platformAnalytics['clicks'] ?? 0,
                'conversions' => $platformAnalytics['conversions'] ?? 0,
                'spend' => $platformAnalytics['spend'] ?? 0,
            ];
        });
    }

    /**
     * Get recent performance trend
     */
    private function getRecentPerformanceTrend($campaign)
    {
        // Mock trend analysis - In production, compare last 7 days vs previous 7 days
        $trends = ['improving', 'declining', 'stable'];

        return [
            'trend' => $trends[array_rand($trends)],
            'percentage_change' => rand(-30, 30),
        ];
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations($campaign)
    {
        $recommendations = [];

        $analytics = $campaign->analytics ?? [];
        $ctr = $this->calculateCTR($campaign);

        if ($ctr < 2) {
            $recommendations[] = [
                'title' => 'Improve Ad Creative',
                'description' => 'Try using more eye-catching visuals and compelling copy to increase clicks.',
                'priority' => 'high',
            ];
        }

        if (($analytics['conversions'] ?? 0) < 10) {
            $recommendations[] = [
                'title' => 'Optimize Landing Page',
                'description' => 'Ensure your landing page is optimized for conversions with clear CTAs.',
                'priority' => 'medium',
            ];
        }

        $recommendations[] = [
            'title' => 'Test Different Audiences',
            'description' => 'Create additional ad sets targeting different audience segments to find what works best.',
            'priority' => 'low',
        ];

        return $recommendations;
    }
}
