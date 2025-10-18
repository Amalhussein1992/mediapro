<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $plans = [
            // باقة الأفراد - 130 درهم شهرياً
            [
                'name' => 'Individual Plan - باقة الأفراد',
                'description' => 'Perfect for content creators and influencers | مثالية لمنشئي المحتوى والمؤثرين',
                'price' => 130.00,
                'interval' => 'monthly',
                'is_active' => true,
                'max_posts' => 100,
                'max_accounts' => 5,
                'ai_features' => true,
                'analytics' => true,
                'priority_support' => false,
                'features' => json_encode([
                    'en' => [
                        'Connect up to 5 social media accounts',
                        'Schedule up to 100 posts per month',
                        'AI Content Generation (50 requests/month)',
                        'AI Image Generation (30 images/month)',
                        'Smart Hashtag Suggestions',
                        'Voice to Text (20 transcriptions/month)',
                        'Basic Analytics & Reports',
                        'Content Calendar',
                        'Post Scheduler',
                        'Best Time to Post Suggestions',
                        'Standard Support (24-48 hours)',
                        'Mobile App Access',
                    ],
                    'ar' => [
                        'ربط حتى 5 حسابات سوشيال ميديا',
                        'جدولة حتى 100 منشور شهرياً',
                        'توليد محتوى بالذكاء الاصطناعي (50 طلب/شهر)',
                        'إنشاء صور بالذكاء الاصطناعي (30 صورة/شهر)',
                        'اقتراحات هاشتاقات ذكية',
                        'تحويل الصوت لنص (20 تحويل/شهر)',
                        'تحليلات وتقارير أساسية',
                        'تقويم المحتوى',
                        'جدولة المنشورات',
                        'اقتراحات أفضل أوقات النشر',
                        'دعم قياسي (24-48 ساعة)',
                        'الوصول لتطبيق الموبايل',
                    ]
                ]),
                'stripe_price_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // باقة الشركات - 170 درهم شهرياً
            [
                'name' => 'Business Plan - باقة الشركات',
                'description' => 'Comprehensive solution for businesses and agencies | حل شامل للشركات ووكالات التسويق',
                'price' => 170.00,
                'interval' => 'monthly',
                'is_active' => true,
                'max_posts' => 300,
                'max_accounts' => 15,
                'ai_features' => true,
                'analytics' => true,
                'priority_support' => true,
                'features' => json_encode([
                    'en' => [
                        '✨ Everything in Individual Plan',
                        'Connect up to 15 social media accounts',
                        'Schedule up to 300 posts per month',
                        'UNLIMITED AI Content Generation',
                        'UNLIMITED AI Image Generation',
                        'UNLIMITED Voice to Text',
                        'Advanced Analytics & Custom Reports',
                        'Competitor Analysis',
                        'Team Collaboration (up to 5 members)',
                        'Ads Campaign Management',
                        'Ads Performance Tracking',
                        'ROI Analytics',
                        'Link Shortener with Analytics',
                        'Unified Inbox (manage all messages)',
                        'Content Approval Workflow',
                        'White Label Reports (coming soon)',
                        'Priority Support (4-8 hours)',
                        'Dedicated Account Manager',
                        'API Access',
                    ],
                    'ar' => [
                        '✨ كل مميزات باقة الأفراد',
                        'ربط حتى 15 حساب سوشيال ميديا',
                        'جدولة حتى 300 منشور شهرياً',
                        'توليد محتوى بالذكاء الاصطناعي بدون حدود',
                        'إنشاء صور بالذكاء الاصطناعي بدون حدود',
                        'تحويل الصوت لنص بدون حدود',
                        'تحليلات متقدمة وتقارير مخصصة',
                        'تحليل المنافسين',
                        'تعاون الفريق (حتى 5 أعضاء)',
                        'إدارة الحملات الإعلانية',
                        'تتبع أداء الإعلانات',
                        'تحليلات عائد الاستثمار',
                        'اختصار روابط مع تحليلات',
                        'صندوق وارد موحد (إدارة جميع الرسائل)',
                        'سير عمل الموافقة على المحتوى',
                        'تقارير بالعلامة البيضاء (قريباً)',
                        'دعم أولوية (4-8 ساعات)',
                        'مدير حساب مخصص',
                        'الوصول لـ API',
                    ]
                ]),
                'stripe_price_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Clear existing plans (disable foreign key checks temporarily)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('subscription_plans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert new plans
        DB::table('subscription_plans')->insert($plans);

        $this->command->info('✅ Subscription plans seeded successfully!');
        $this->command->info('💰 Individual Plan: 130 AED/month');
        $this->command->info('💰 Business Plan: 170 AED/month');
    }
}
