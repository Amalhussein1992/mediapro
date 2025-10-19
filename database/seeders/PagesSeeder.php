<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $pages = [
            ['slug' => 'features', 'title' => 'Features', 'title_ar' => 'الميزات', 'content' => '<h1>Features</h1>', 'content_ar' => '<h1>الميزات</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'pricing', 'title' => 'Pricing', 'title_ar' => 'الأسعار', 'content' => '<h1>Pricing</h1>', 'content_ar' => '<h1>الأسعار</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'api', 'title' => 'API', 'title_ar' => 'واجهة برمجية', 'content' => '<h1>API</h1>', 'content_ar' => '<h1>واجهة برمجية</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'about', 'title' => 'About', 'title_ar' => 'من نحن', 'content' => '<h1>About</h1>', 'content_ar' => '<h1>من نحن</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'contact', 'title' => 'Contact', 'title_ar' => 'اتصل بنا', 'content' => '<h1>Contact</h1>', 'content_ar' => '<h1>اتصل بنا</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'privacy', 'title' => 'Privacy', 'title_ar' => 'الخصوصية', 'content' => '<h1>Privacy</h1>', 'content_ar' => '<h1>الخصوصية</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'terms', 'title' => 'Terms', 'title_ar' => 'الشروط', 'content' => '<h1>Terms</h1>', 'content_ar' => '<h1>الشروط</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'security', 'title' => 'Security', 'title_ar' => 'الأمان', 'content' => '<h1>Security</h1>', 'content_ar' => '<h1>الأمان</h1>', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(['slug' => $page['slug']], $page);
        }
    }
}
