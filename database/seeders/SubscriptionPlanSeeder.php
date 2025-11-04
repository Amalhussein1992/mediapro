<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'الباقة المجانية',
                'slug' => 'free',
                'description' => 'باقة مجانية للبدء',
                'type' => 'monthly',
                'price' => 0,
                'currency' => 'USD',
                'max_accounts' => 1,
                'max_posts' => 10,
                'ai_features' => false,
                'analytics' => false,
                'scheduling' => false,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    'حساب واحد',
                    '10 منشورات شهرياً',
                    'دعم أساسي',
                ],
            ],
            [
                'name' => 'الباقة الاحترافية',
                'slug' => 'pro',
                'description' => 'الباقة الأكثر شعبية للمحترفين',
                'type' => 'monthly',
                'price' => 29.99,
                'currency' => 'USD',
                'max_accounts' => 5,
                'max_posts' => 100,
                'ai_features' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    '5 حسابات',
                    '100 منشور شهرياً',
                    'ميزات الذكاء الاصطناعي',
                    'تحليلات متقدمة',
                    'جدولة المنشورات',
                    'دعم ذو أولوية',
                ],
            ],
            [
                'name' => 'باقة الأعمال',
                'slug' => 'business',
                'description' => 'للشركات والفرق الكبيرة',
                'type' => 'monthly',
                'price' => 99.99,
                'currency' => 'USD',
                'max_accounts' => 20,
                'max_posts' => 500,
                'ai_features' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    '20 حساب',
                    '500 منشور شهرياً',
                    'جميع ميزات الذكاء الاصطناعي',
                    'تحليلات شاملة',
                    'جدولة غير محدودة',
                    'إدارة الفريق',
                    'دعم مخصص 24/7',
                    'API كامل',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
