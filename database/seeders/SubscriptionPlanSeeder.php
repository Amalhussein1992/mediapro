<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with social media management',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Unlimited posts',
                    '2 social accounts',
                    'Basic scheduling',
                    'Mobile app access',
                    'Email support',
                ],
                'max_posts_per_month' => null,
                'max_social_accounts' => 2,
                'max_team_members' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Great for individuals and small businesses',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Unlimited posts',
                    '5 social accounts',
                    'Advanced scheduling',
                    'Content calendar',
                    'Mobile app access',
                    'Basic analytics',
                    'Email support',
                ],
                'max_posts_per_month' => null,
                'max_social_accounts' => 5,
                'max_team_members' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Perfect for growing businesses and agencies',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Unlimited posts',
                    '15 social accounts',
                    'Advanced scheduling',
                    'Content calendar',
                    'Mobile app access',
                    'Advanced analytics',
                    'Team collaboration',
                    'Brand kit',
                    'Custom templates',
                    'Priority email support',
                    'API access',
                ],
                'max_posts_per_month' => null,
                'max_social_accounts' => 15,
                'max_team_members' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations requiring unlimited resources',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Unlimited posts',
                    'Unlimited social accounts',
                    'Advanced scheduling',
                    'Content calendar',
                    'Mobile app access',
                    'Advanced analytics',
                    'Custom reports',
                    'Unlimited team members',
                    'Brand kit',
                    'Custom templates',
                    'White-label options',
                    'Dedicated account manager',
                    'Priority support (24/7)',
                    'API access',
                    'Custom integrations',
                    'SLA guarantee',
                ],
                'max_posts_per_month' => null,
                'max_social_accounts' => null,
                'max_team_members' => null,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
