<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\BrandKit;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ComprehensiveDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds with comprehensive realistic data
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive database seeding...');

        // Clear existing data
        $this->command->info('🗑️  Clearing existing data...');
        $this->clearData();

        // Seed in order
        $this->command->info('📦 Seeding subscription plans...');
        $this->seedSubscriptionPlans();

        $this->command->info('👥 Seeding users...');
        $this->seedUsers();

        $this->command->info('💳 Seeding subscriptions...');
        $this->seedSubscriptions();

        $this->command->info('🔗 Seeding social accounts...');
        $this->seedSocialAccounts();

        $this->command->info('🎨 Seeding brand kits...');
        $this->seedBrandKits();

        $this->command->info('📝 Seeding posts...');
        $this->seedPosts();

        $this->command->info('💰 Seeding payments...');
        $this->seedPayments();

        $this->command->info('🔔 Seeding notifications...');
        $this->seedNotifications();

        $this->command->info('⚙️  Seeding app settings...');
        $this->seedAppSettings();

        $this->command->info('✅ Database seeding completed successfully!');
        $this->printSummary();
    }

    /**
     * Clear existing data
     */
    private function clearData(): void
    {
        Notification::truncate();
        Payment::truncate();
        Post::truncate();
        BrandKit::truncate();
        SocialAccount::truncate();
        Subscription::truncate();
        User::where('email', '!=', 'admin@mediapro.social')->delete();
    }

    /**
     * Seed subscription plans
     */
    private function seedSubscriptionPlans(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'name_ar' => 'مجاني',
                'slug' => 'free',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'max_posts' => 10,
                    'max_accounts' => 2,
                    'ai_credits' => 10,
                    'analytics' => false,
                    'team_members' => 1,
                    'brand_kits' => 1,
                ]),
                'description' => 'Perfect for individuals getting started',
                'description_ar' => 'مثالي للأفراد المبتدئين',
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Starter',
                'name_ar' => 'مبتدئ',
                'slug' => 'starter',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'max_posts' => 50,
                    'max_accounts' => 5,
                    'ai_credits' => 100,
                    'analytics' => true,
                    'team_members' => 2,
                    'brand_kits' => 3,
                ]),
                'description' => 'Great for small businesses and content creators',
                'description_ar' => 'رائع للشركات الصغيرة ومنشئي المحتوى',
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Professional',
                'name_ar' => 'احترافي',
                'slug' => 'professional',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'max_posts' => 200,
                    'max_accounts' => 15,
                    'ai_credits' => 500,
                    'analytics' => true,
                    'team_members' => 5,
                    'brand_kits' => 10,
                ]),
                'description' => 'For growing businesses and agencies',
                'description_ar' => 'للشركات النامية والوكالات',
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'name' => 'Enterprise',
                'name_ar' => 'مؤسسة',
                'slug' => 'enterprise',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'max_posts' => -1, // Unlimited
                    'max_accounts' => -1, // Unlimited
                    'ai_credits' => -1, // Unlimited
                    'analytics' => true,
                    'team_members' => -1, // Unlimited
                    'brand_kits' => -1, // Unlimited
                ]),
                'description' => 'For large organizations with unlimited needs',
                'description_ar' => 'للمؤسسات الكبيرة ذات الاحتياجات غير المحدودة',
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }

    /**
     * Seed users with realistic data
     */
    private function seedUsers(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mediapro.social',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'role' => 'admin',
        ]);

        // Arabic names
        $arabicNames = [
            'أحمد محمد', 'فاطمة علي', 'محمد حسن', 'سارة أحمد', 'خالد عبدالله',
            'نور الدين', 'ليلى يوسف', 'عمر إبراهيم', 'مريم خالد', 'يوسف محمود',
            'هند سعيد', 'كريم حسام', 'رانيا مصطفى', 'طارق فتحي', 'دينا محمد',
        ];

        // English names
        $englishNames = [
            'John Smith', 'Emma Johnson', 'Michael Williams', 'Olivia Brown', 'David Jones',
            'Sophia Davis', 'James Miller', 'Isabella Wilson', 'Robert Moore', 'Mia Taylor',
            'William Anderson', 'Charlotte Thomas', 'Richard Jackson', 'Amelia White', 'Joseph Harris',
        ];

        $names = array_merge($arabicNames, $englishNames);

        // Create 100 users
        for ($i = 0; $i < 100; $i++) {
            $name = $names[array_rand($names)];
            $createdAt = Carbon::now()->subDays(rand(1, 365));

            User::create([
                'name' => $name,
                'email' => 'user' . ($i + 1) . '@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => rand(0, 1) ? $createdAt->addDays(rand(1, 7)) : null,
                'is_active' => rand(0, 100) > 10, // 90% active
                'role' => 'user',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    /**
     * Seed subscriptions
     */
    private function seedSubscriptions(): void
    {
        $users = User::where('role', 'user')->get();
        $plans = SubscriptionPlan::all();

        foreach ($users as $user) {
            // 70% of users have a subscription
            if (rand(0, 100) < 70) {
                $plan = $plans->random();
                $startDate = $user->created_at->addDays(rand(1, 30));
                $endDate = $startDate->copy()->addMonth();
                $status = 'active';

                // 10% expired, 5% cancelled
                $rand = rand(0, 100);
                if ($rand < 10) {
                    $status = 'expired';
                    $endDate = $startDate->copy()->subDays(rand(1, 30));
                } elseif ($rand < 15) {
                    $status = 'cancelled';
                }

                Subscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => $status,
                    'starts_at' => $startDate,
                    'ends_at' => $endDate,
                    'auto_renew' => rand(0, 1),
                    'created_at' => $startDate,
                    'updated_at' => $startDate,
                ]);
            }
        }
    }

    /**
     * Seed social accounts
     */
    private function seedSocialAccounts(): void
    {
        $users = User::where('role', 'user')->get();
        $platforms = ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok', 'youtube'];

        foreach ($users as $user) {
            // Each user has 1-3 social accounts
            $numAccounts = rand(1, 3);

            for ($i = 0; $i < $numAccounts; $i++) {
                $platform = $platforms[array_rand($platforms)];

                SocialAccount::create([
                    'user_id' => $user->id,
                    'platform' => $platform,
                    'platform_user_id' => Str::random(15),
                    'username' => $this->generateUsername($user->name, $platform),
                    'access_token' => Str::random(100),
                    'refresh_token' => Str::random(100),
                    'expires_at' => Carbon::now()->addDays(rand(30, 90)),
                    'is_active' => rand(0, 100) > 15, // 85% active
                    'created_at' => $user->created_at->addDays(rand(1, 10)),
                ]);
            }
        }
    }

    /**
     * Seed brand kits
     */
    private function seedBrandKits(): void
    {
        $users = User::where('role', 'user')->limit(50)->get();

        $colorPalettes = [
            ['#667eea', '#764ba2', '#f093fb', '#4facfe'],
            ['#ff6b6b', '#4ecdc4', '#45b7d1', '#ffd93d'],
            ['#6c5ce7', '#fd79a8', '#fdcb6e', '#00b894'],
            ['#2d3436', '#636e72', '#b2bec3', '#dfe6e9'],
        ];

        $fonts = [
            ['primary' => 'Inter', 'secondary' => 'Roboto'],
            ['primary' => 'Poppins', 'secondary' => 'Open Sans'],
            ['primary' => 'Montserrat', 'secondary' => 'Lato'],
        ];

        foreach ($users as $user) {
            BrandKit::create([
                'user_id' => $user->id,
                'name' => $user->name . "'s Brand",
                'colors' => json_encode($colorPalettes[array_rand($colorPalettes)]),
                'fonts' => json_encode($fonts[array_rand($fonts)]),
                'logo_url' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
                'is_default' => true,
                'created_at' => $user->created_at->addDays(rand(5, 15)),
            ]);
        }
    }

    /**
     * Seed posts
     */
    private function seedPosts(): void
    {
        $users = User::whereHas('socialAccounts')->with('socialAccounts')->get();

        $postContents = [
            "Excited to share our latest update! 🚀 #innovation #tech",
            "Just launched our new product line. Check it out! 💼",
            "Thank you for 10K followers! Your support means everything 🙏",
            "Monday motivation: Dream big, work hard! 💪 #MondayMotivation",
            "Behind the scenes of our photoshoot today 📸",
            "New blog post is live! Link in bio 📝",
            "Weekend vibes ✨ How are you spending yours?",
            "Throwback to our amazing event last month! 🎉",
            "Product of the week: Our bestseller is back in stock! 🛍️",
            "Tutorial Tuesday: Learn how to maximize your productivity 📚",
        ];

        $statuses = ['published' => 60, 'scheduled' => 25, 'draft' => 10, 'failed' => 5];

        foreach ($users as $user) {
            $numPosts = rand(5, 20);

            for ($i = 0; $i < $numPosts; $i++) {
                $account = $user->socialAccounts->random();
                $status = $this->getWeightedStatus($statuses);
                $createdAt = Carbon::now()->subDays(rand(1, 180));

                $publishedAt = null;
                $scheduledAt = null;

                if ($status === 'published') {
                    $publishedAt = $createdAt->copy()->addHours(rand(1, 24));
                } elseif ($status === 'scheduled') {
                    $scheduledAt = Carbon::now()->addDays(rand(1, 30))->addHours(rand(0, 23));
                }

                Post::create([
                    'user_id' => $user->id,
                    'social_account_id' => $account->id,
                    'content' => $postContents[array_rand($postContents)],
                    'platform' => $account->platform,
                    'status' => $status,
                    'scheduled_at' => $scheduledAt,
                    'published_at' => $publishedAt,
                    'media_urls' => rand(0, 1) ? json_encode(['https://picsum.photos/800/600?random=' . rand(1, 100)]) : null,
                    'analytics' => $status === 'published' ? json_encode([
                        'likes' => rand(10, 10000),
                        'comments' => rand(0, 500),
                        'shares' => rand(0, 200),
                        'reach' => rand(100, 50000),
                        'impressions' => rand(200, 100000),
                    ]) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    /**
     * Seed payments
     */
    private function seedPayments(): void
    {
        $subscriptions = Subscription::where('status', '!=', 'cancelled')->get();

        foreach ($subscriptions as $subscription) {
            $numPayments = $subscription->status === 'active' ? rand(1, 12) : rand(1, 3);

            for ($i = 0; $i < $numPayments; $i++) {
                $paymentDate = $subscription->starts_at->copy()->addMonths($i);

                if ($paymentDate->isFuture()) {
                    break;
                }

                Payment::create([
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->id,
                    'amount' => $subscription->plan->price,
                    'currency' => 'USD',
                    'status' => rand(0, 100) > 5 ? 'completed' : 'failed', // 95% success
                    'payment_method' => ['credit_card', 'paypal', 'stripe'][array_rand(['credit_card', 'paypal', 'stripe'])],
                    'transaction_id' => 'txn_' . Str::random(20),
                    'paid_at' => $paymentDate,
                    'created_at' => $paymentDate,
                ]);
            }
        }
    }

    /**
     * Seed notifications
     */
    private function seedNotifications(): void
    {
        $users = User::where('role', 'user')->limit(50)->get();

        $notificationTypes = [
            ['type' => 'post_published', 'title' => 'Post Published', 'title_ar' => 'تم نشر المنشور'],
            ['type' => 'post_failed', 'title' => 'Post Failed', 'title_ar' => 'فشل نشر المنشور'],
            ['type' => 'subscription_expiring', 'title' => 'Subscription Expiring Soon', 'title_ar' => 'اشتراكك سينتهي قريباً'],
            ['type' => 'payment_successful', 'title' => 'Payment Successful', 'title_ar' => 'تم الدفع بنجاح'],
            ['type' => 'new_follower', 'title' => 'New Follower', 'title_ar' => 'متابع جديد'],
        ];

        foreach ($users as $user) {
            $numNotifications = rand(5, 20);

            for ($i = 0; $i < $numNotifications; $i++) {
                $notification = $notificationTypes[array_rand($notificationTypes)];
                $createdAt = Carbon::now()->subDays(rand(1, 60));

                Notification::create([
                    'user_id' => $user->id,
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                    'title_ar' => $notification['title_ar'],
                    'message' => 'This is a notification message',
                    'message_ar' => 'هذه رسالة إشعار',
                    'is_read' => rand(0, 100) > 40, // 60% read
                    'created_at' => $createdAt,
                ]);
            }
        }
    }

    /**
     * Seed app settings
     */
    private function seedAppSettings(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Media Pro', 'type' => 'string'],
            ['key' => 'app_url', 'value' => 'https://www.mediapro.social', 'type' => 'string'],
            ['key' => 'contact_email', 'value' => 'support@mediapro.social', 'type' => 'string'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'allow_registration', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'default_language', 'value' => 'ar', 'type' => 'string'],
            ['key' => 'max_upload_size', 'value' => '10', 'type' => 'integer'],
            ['key' => 'posts_per_page', 'value' => '20', 'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Helper: Generate username from name
     */
    private function generateUsername(string $name, string $platform): string
    {
        $username = strtolower(str_replace(' ', '_', $name));
        return $username . rand(100, 999);
    }

    /**
     * Helper: Get weighted random status
     */
    private function getWeightedStatus(array $weights): string
    {
        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return array_key_first($weights);
    }

    /**
     * Print summary
     */
    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 ========== Seeding Summary ==========');
        $this->command->info('👥 Users: ' . User::count());
        $this->command->info('📦 Subscription Plans: ' . SubscriptionPlan::count());
        $this->command->info('💳 Subscriptions: ' . Subscription::count());
        $this->command->info('🔗 Social Accounts: ' . SocialAccount::count());
        $this->command->info('🎨 Brand Kits: ' . BrandKit::count());
        $this->command->info('📝 Posts: ' . Post::count());
        $this->command->info('💰 Payments: ' . Payment::count());
        $this->command->info('🔔 Notifications: ' . Notification::count());
        $this->command->info('⚙️  App Settings: ' . AppSetting::count());
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('🎉 Database is now ready with realistic data!');
        $this->command->info('');
        $this->command->info('📧 Admin Login:');
        $this->command->info('   Email: admin@mediapro.social');
        $this->command->info('   Password: password');
        $this->command->info('');
    }
}
