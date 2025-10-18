<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdsCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdsCampaignController extends Controller
{
    /**
     * Display a listing of campaigns
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $campaigns = $user->adsCampaigns()
            ->with(['adSets', 'ads'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'objective' => $campaign->objective,
                    'status' => $campaign->status,
                    'budget' => $campaign->budget,
                    'spend' => $campaign->analytics['spend'] ?? 0,
                    'platforms' => $campaign->platforms,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'metrics' => [
                        'impressions' => $campaign->analytics['impressions'] ?? 0,
                        'clicks' => $campaign->analytics['clicks'] ?? 0,
                        'conversions' => $campaign->analytics['conversions'] ?? 0,
                        'ctr' => $this->calculateCTR($campaign),
                    ],
                    'created_at' => $campaign->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'objective' => 'required|string|in:awareness,traffic,engagement,leads,conversions,sales',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,linkedin,tiktok,google',
            'budget.type' => 'required|string|in:daily,lifetime',
            'budget.amount' => 'required|numeric|min:1',
            'schedule.startDate' => 'required|date',
            'schedule.endDate' => 'nullable|date|after:schedule.startDate',
            'schedule.timezone' => 'required|string',
            'status' => 'nullable|string|in:draft,active,paused,completed',
        ]);

        $user = $request->user();

        $campaign = $user->adsCampaigns()->create([
            'name' => $request->input('name'),
            'objective' => $request->input('objective'),
            'platforms' => $request->input('platforms'),
            'budget_type' => $request->input('budget.type'),
            'budget' => $request->input('budget.amount'),
            'start_date' => $request->input('schedule.startDate'),
            'end_date' => $request->input('schedule.endDate'),
            'timezone' => $request->input('schedule.timezone'),
            'status' => $request->input('status', 'draft'),
            'analytics' => [
                'impressions' => 0,
                'clicks' => 0,
                'conversions' => 0,
                'spend' => 0,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'data' => $campaign
        ], 201);
    }

    /**
     * Display the specified campaign
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->with(['adSets', 'ads'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'objective' => $campaign->objective,
                'status' => $campaign->status,
                'budget_type' => $campaign->budget_type,
                'budget' => $campaign->budget,
                'spend' => $campaign->analytics['spend'] ?? 0,
                'platforms' => $campaign->platforms,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'timezone' => $campaign->timezone,
                'analytics' => $campaign->analytics,
                'ad_sets' => $campaign->adSets,
                'ads' => $campaign->ads,
                'created_at' => $campaign->created_at,
                'updated_at' => $campaign->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'objective' => 'nullable|string|in:awareness,traffic,engagement,leads,conversions,sales',
            'platforms' => 'nullable|array',
            'budget' => 'nullable|numeric|min:1',
            'status' => 'nullable|string|in:draft,active,paused,completed',
        ]);

        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($id);

        $campaign->update($request->only([
            'name',
            'objective',
            'platforms',
            'budget',
            'status',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Campaign updated successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Remove the specified campaign
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($id);

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campaign deleted successfully'
        ]);
    }

    /**
     * Build campaign with AI
     */
    public function buildWithAI(Request $request)
    {
        $request->validate([
            'objective' => 'required|string|in:awareness,traffic,engagement,leads,conversions,sales',
            'budget' => 'required|numeric|min:1',
            'duration' => 'required|integer|min:1|max:365',
            'productInfo' => 'required|string|max:1000',
            'targetAudience' => 'required|string|max:500',
        ]);

        $objective = $request->input('objective');
        $budget = $request->input('budget');
        $duration = $request->input('duration');
        $productInfo = $request->input('productInfo');
        $targetAudience = $request->input('targetAudience');

        // AI-generated campaign structure
        $aiCampaign = $this->generateAICampaignStructure(
            $objective,
            $budget,
            $duration,
            $productInfo,
            $targetAudience
        );

        return response()->json([
            'success' => true,
            'message' => 'AI campaign structure generated successfully',
            'data' => $aiCampaign
        ]);
    }

    /**
     * Publish a campaign
     */
    public function publish(Request $request, $id)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($id);

        if ($campaign->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft campaigns can be published'
            ], 422);
        }

        $campaign->update([
            'status' => 'active',
            'published_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign published successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Pause a campaign
     */
    public function pause(Request $request, $id)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($id);

        if ($campaign->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Only active campaigns can be paused'
            ], 422);
        }

        $campaign->update(['status' => 'paused']);

        return response()->json([
            'success' => true,
            'message' => 'Campaign paused successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Resume a paused campaign
     */
    public function resume(Request $request, $id)
    {
        $user = $request->user();
        $campaign = $user->adsCampaigns()->findOrFail($id);

        if ($campaign->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Only paused campaigns can be resumed'
            ], 422);
        }

        $campaign->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Campaign resumed successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Generate AI campaign structure
     */
    private function generateAICampaignStructure($objective, $budget, $duration, $productInfo, $targetAudience)
    {
        // Campaign name suggestion
        $campaignName = $this->generateCampaignName($objective, $productInfo);

        // Platform recommendations based on objective and audience
        $recommendedPlatforms = $this->recommendPlatforms($objective, $targetAudience);

        // Budget allocation across platforms
        $budgetAllocation = $this->allocateBudget($budget, $recommendedPlatforms);

        // Ad set suggestions
        $adSets = $this->generateAdSetSuggestions($objective, $targetAudience, $recommendedPlatforms);

        // Creative suggestions
        $creativeSuggestions = $this->generateCreativeSuggestions($objective, $productInfo);

        // Audience targeting suggestions
        $audienceTargeting = $this->generateAudienceTargeting($targetAudience);

        // Schedule recommendations
        $scheduleRecommendations = $this->generateScheduleRecommendations($duration);

        return [
            'campaign_name' => $campaignName,
            'recommended_platforms' => $recommendedPlatforms,
            'budget_allocation' => $budgetAllocation,
            'ad_sets' => $adSets,
            'creative_suggestions' => $creativeSuggestions,
            'audience_targeting' => $audienceTargeting,
            'schedule_recommendations' => $scheduleRecommendations,
            'estimated_reach' => $this->estimateReach($budget, $duration, $recommendedPlatforms),
            'success_metrics' => $this->defineSuccessMetrics($objective),
        ];
    }

    /**
     * Generate campaign name
     */
    private function generateCampaignName($objective, $productInfo)
    {
        $keywords = explode(' ', $productInfo);
        $mainKeyword = $keywords[0] ?? 'Product';

        $objectiveNames = [
            'awareness' => 'Brand Awareness',
            'traffic' => 'Drive Traffic',
            'engagement' => 'Boost Engagement',
            'leads' => 'Lead Generation',
            'conversions' => 'Conversion',
            'sales' => 'Sales',
        ];

        $objectiveName = $objectiveNames[$objective] ?? 'Marketing';

        return "{$mainKeyword} {$objectiveName} Campaign " . date('M Y');
    }

    /**
     * Recommend platforms based on objective
     */
    private function recommendPlatforms($objective, $targetAudience)
    {
        $platformsByObjective = [
            'awareness' => ['facebook', 'instagram', 'tiktok'],
            'traffic' => ['google', 'facebook', 'linkedin'],
            'engagement' => ['instagram', 'tiktok', 'facebook'],
            'leads' => ['linkedin', 'facebook', 'google'],
            'conversions' => ['facebook', 'google', 'instagram'],
            'sales' => ['facebook', 'instagram', 'google'],
        ];

        $platforms = $platformsByObjective[$objective] ?? ['facebook', 'instagram'];

        return array_map(function ($platform) {
            return [
                'platform' => $platform,
                'priority' => 'high',
                'reason' => $this->getPlatformReason($platform),
            ];
        }, $platforms);
    }

    /**
     * Get platform recommendation reason
     */
    private function getPlatformReason($platform)
    {
        $reasons = [
            'facebook' => 'Large user base with advanced targeting options',
            'instagram' => 'Visual platform with high engagement rates',
            'tiktok' => 'Growing platform with viral potential',
            'linkedin' => 'Best for B2B and professional audiences',
            'google' => 'Intent-based targeting with search ads',
            'twitter' => 'Real-time engagement and trending topics',
        ];

        return $reasons[$platform] ?? 'Great platform for your campaign';
    }

    /**
     * Allocate budget across platforms
     */
    private function allocateBudget($totalBudget, $platforms)
    {
        $platformCount = count($platforms);
        $baseAllocation = $totalBudget / $platformCount;

        return array_map(function ($platform, $index) use ($baseAllocation, $totalBudget) {
            // High priority platforms get slightly more budget
            $allocation = $platform['priority'] === 'high'
                ? $baseAllocation * 1.2
                : $baseAllocation * 0.8;

            return [
                'platform' => $platform['platform'],
                'amount' => round($allocation, 2),
                'percentage' => round(($allocation / $totalBudget) * 100, 1),
            ];
        }, $platforms, array_keys($platforms));
    }

    /**
     * Generate ad set suggestions
     */
    private function generateAdSetSuggestions($objective, $targetAudience, $platforms)
    {
        return [
            [
                'name' => 'Core Audience',
                'targeting' => [
                    'age' => '25-45',
                    'interests' => $this->extractInterests($targetAudience),
                    'behavior' => 'Active users',
                ],
                'budget_split' => 50,
                'description' => 'Main target audience based on your inputs',
            ],
            [
                'name' => 'Lookalike Audience',
                'targeting' => [
                    'type' => 'lookalike',
                    'source' => 'existing customers',
                    'similarity' => '1-2%',
                ],
                'budget_split' => 30,
                'description' => 'Users similar to your existing customers',
            ],
            [
                'name' => 'Retargeting',
                'targeting' => [
                    'type' => 'retargeting',
                    'source' => 'website visitors',
                    'window' => '30 days',
                ],
                'budget_split' => 20,
                'description' => 'Re-engage previous website visitors',
            ],
        ];
    }

    /**
     * Generate creative suggestions
     */
    private function generateCreativeSuggestions($objective, $productInfo)
    {
        return [
            [
                'type' => 'image',
                'format' => '1080x1080',
                'headline' => $this->generateHeadline($objective, $productInfo),
                'description' => $this->generateDescription($objective, $productInfo),
                'cta' => $this->generateCTA($objective),
                'tips' => [
                    'Use high-quality, eye-catching visuals',
                    'Include your brand logo prominently',
                    'Keep text minimal and impactful',
                ],
            ],
            [
                'type' => 'video',
                'format' => '9:16 (Stories)',
                'duration' => '15-30 seconds',
                'headline' => $this->generateHeadline($objective, $productInfo),
                'cta' => $this->generateCTA($objective),
                'tips' => [
                    'Grab attention in first 3 seconds',
                    'Include captions for sound-off viewing',
                    'End with clear call-to-action',
                ],
            ],
        ];
    }

    /**
     * Generate headline
     */
    private function generateHeadline($objective, $productInfo)
    {
        $headlines = [
            'awareness' => "Discover {$productInfo}",
            'traffic' => "Learn More About {$productInfo}",
            'engagement' => "Join Thousands Using {$productInfo}",
            'leads' => "Get Started with {$productInfo} Today",
            'conversions' => "Limited Time Offer on {$productInfo}",
            'sales' => "Shop {$productInfo} Now",
        ];

        return $headlines[$objective] ?? "Explore {$productInfo}";
    }

    /**
     * Generate description
     */
    private function generateDescription($objective, $productInfo)
    {
        return "Transform your experience with {$productInfo}. Join thousands of satisfied customers today.";
    }

    /**
     * Generate CTA
     */
    private function generateCTA($objective)
    {
        $ctas = [
            'awareness' => 'Learn More',
            'traffic' => 'Visit Now',
            'engagement' => 'Join Us',
            'leads' => 'Sign Up',
            'conversions' => 'Get Started',
            'sales' => 'Shop Now',
        ];

        return $ctas[$objective] ?? 'Learn More';
    }

    /**
     * Generate audience targeting
     */
    private function generateAudienceTargeting($targetAudience)
    {
        return [
            'demographics' => [
                'age_range' => '25-54',
                'gender' => 'all',
                'location' => 'United States',
            ],
            'interests' => $this->extractInterests($targetAudience),
            'behaviors' => [
                'Online shoppers',
                'Tech early adopters',
                'Frequent travelers',
            ],
            'custom_audiences' => [
                'Website visitors (30 days)',
                'Email subscribers',
                'Social media engagers',
            ],
        ];
    }

    /**
     * Extract interests from target audience description
     */
    private function extractInterests($targetAudience)
    {
        // Simple keyword extraction - in production, use NLP
        $commonInterests = [
            'Technology', 'Shopping', 'Business', 'Health & Wellness',
            'Travel', 'Food & Dining', 'Entertainment', 'Sports'
        ];

        return array_slice($commonInterests, 0, 5);
    }

    /**
     * Generate schedule recommendations
     */
    private function generateScheduleRecommendations($duration)
    {
        return [
            'recommended_start' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'recommended_end' => Carbon::now()->addDays($duration)->format('Y-m-d'),
            'best_days' => ['Tuesday', 'Wednesday', 'Thursday'],
            'best_hours' => ['9:00 AM - 12:00 PM', '6:00 PM - 9:00 PM'],
            'timezone' => 'UTC',
        ];
    }

    /**
     * Estimate reach
     */
    private function estimateReach($budget, $duration, $platforms)
    {
        // Simplified estimation - in production, use platform APIs
        $avgCPM = 10; // $10 per 1000 impressions
        $totalImpressions = ($budget / $avgCPM) * 1000;

        return [
            'estimated_impressions' => round($totalImpressions),
            'estimated_clicks' => round($totalImpressions * 0.02), // 2% CTR
            'estimated_conversions' => round($totalImpressions * 0.001), // 0.1% conversion
            'estimated_reach' => round($totalImpressions * 0.7), // Reach is typically 70% of impressions
        ];
    }

    /**
     * Define success metrics
     */
    private function defineSuccessMetrics($objective)
    {
        $metrics = [
            'awareness' => [
                'primary' => 'Impressions',
                'secondary' => 'Reach',
                'target_cpm' => '$5-$15',
            ],
            'traffic' => [
                'primary' => 'Link Clicks',
                'secondary' => 'CTR',
                'target_ctr' => '2-5%',
            ],
            'engagement' => [
                'primary' => 'Engagement Rate',
                'secondary' => 'Social Actions',
                'target_rate' => '3-8%',
            ],
            'leads' => [
                'primary' => 'Lead Form Submissions',
                'secondary' => 'Cost Per Lead',
                'target_cpl' => '$10-$50',
            ],
            'conversions' => [
                'primary' => 'Conversions',
                'secondary' => 'Conversion Rate',
                'target_rate' => '2-5%',
            ],
            'sales' => [
                'primary' => 'Purchase Value',
                'secondary' => 'ROAS',
                'target_roas' => '3-5x',
            ],
        ];

        return $metrics[$objective] ?? [
            'primary' => 'Impressions',
            'secondary' => 'Clicks',
        ];
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
}
