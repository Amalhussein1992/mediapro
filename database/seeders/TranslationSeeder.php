<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Hero Section
            [
                'key' => 'hero.title',
                'value_en' => 'Manage All Your Social Media in One Place',
                'value_ar' => 'إدارة جميع وسائل التواصل الاجتماعي في مكان واحد',
                'group' => 'hero',
            ],
            [
                'key' => 'hero.subtitle',
                'value_en' => 'Schedule posts, track analytics, and grow your audience across all platforms with our powerful social media management tool.',
                'value_ar' => 'جدولة المنشورات، تتبع التحليلات، وزيادة جمهورك عبر جميع المنصات باستخدام أداة إدارة وسائل التواصل الاجتماعي القوية.',
                'group' => 'hero',
            ],
            [
                'key' => 'hero.cta_primary',
                'value_en' => 'Start Free Trial',
                'value_ar' => 'ابدأ النسخة التجريبية المجانية',
                'group' => 'hero',
            ],
            [
                'key' => 'hero.cta_secondary',
                'value_en' => 'Watch Demo',
                'value_ar' => 'شاهد العرض التوضيحي',
                'group' => 'hero',
            ],

            // Features Section
            [
                'key' => 'features.title',
                'value_en' => 'Everything You Need to Succeed',
                'value_ar' => 'كل ما تحتاجه للنجاح',
                'group' => 'features',
            ],
            [
                'key' => 'features.subtitle',
                'value_en' => 'Powerful features to help you manage your social media presence effectively',
                'value_ar' => 'ميزات قوية لمساعدتك في إدارة تواجدك على وسائل التواصل الاجتماعي بفعالية',
                'group' => 'features',
            ],
            [
                'key' => 'features.scheduling.title',
                'value_en' => 'Smart Scheduling',
                'value_ar' => 'جدولة ذكية',
                'group' => 'features',
            ],
            [
                'key' => 'features.scheduling.description',
                'value_en' => 'Schedule your posts in advance and publish them at the perfect time for maximum engagement.',
                'value_ar' => 'جدول منشوراتك مسبقاً وانشرها في الوقت المثالي لتحقيق أقصى قدر من التفاعل.',
                'group' => 'features',
            ],
            [
                'key' => 'features.analytics.title',
                'value_en' => 'Advanced Analytics',
                'value_ar' => 'تحليلات متقدمة',
                'group' => 'features',
            ],
            [
                'key' => 'features.analytics.description',
                'value_en' => 'Track your performance with detailed analytics and insights across all your social channels.',
                'value_ar' => 'تتبع أدائك من خلال التحليلات والرؤى التفصيلية عبر جميع قنوات التواصل الاجتماعي الخاصة بك.',
                'group' => 'features',
            ],
            [
                'key' => 'features.collaboration.title',
                'value_en' => 'Team Collaboration',
                'value_ar' => 'تعاون الفريق',
                'group' => 'features',
            ],
            [
                'key' => 'features.collaboration.description',
                'value_en' => 'Work together with your team to create, approve, and publish content seamlessly.',
                'value_ar' => 'اعمل مع فريقك لإنشاء المحتوى والموافقة عليه ونشره بسلاسة.',
                'group' => 'features',
            ],
            [
                'key' => 'features.multiplatform.title',
                'value_en' => 'Multi-Platform Support',
                'value_ar' => 'دعم متعدد المنصات',
                'group' => 'features',
            ],
            [
                'key' => 'features.multiplatform.description',
                'value_en' => 'Manage Facebook, Twitter, Instagram, LinkedIn, and more from a single dashboard.',
                'value_ar' => 'إدارة فيسبوك وتويتر وإنستغرام ولينكد إن والمزيد من لوحة تحكم واحدة.',
                'group' => 'features',
            ],
            [
                'key' => 'features.content_library.title',
                'value_en' => 'Content Library',
                'value_ar' => 'مكتبة المحتوى',
                'group' => 'features',
            ],
            [
                'key' => 'features.content_library.description',
                'value_en' => 'Store and organize all your media assets in one centralized content library.',
                'value_ar' => 'خزن ونظم جميع أصول الوسائط الخاصة بك في مكتبة محتوى مركزية واحدة.',
                'group' => 'features',
            ],
            [
                'key' => 'features.automation.title',
                'value_en' => 'Smart Automation',
                'value_ar' => 'أتمتة ذكية',
                'group' => 'features',
            ],
            [
                'key' => 'features.automation.description',
                'value_en' => 'Automate repetitive tasks and focus on creating engaging content for your audience.',
                'value_ar' => 'أتمتة المهام المتكررة والتركيز على إنشاء محتوى جذاب لجمهورك.',
                'group' => 'features',
            ],

            // Stats Section
            [
                'key' => 'stats.users',
                'value_en' => '10,000+ Active Users',
                'value_ar' => '10,000+ مستخدم نشط',
                'group' => 'stats',
            ],
            [
                'key' => 'stats.posts',
                'value_en' => '1M+ Posts Published',
                'value_ar' => '1+ مليون منشور',
                'group' => 'stats',
            ],
            [
                'key' => 'stats.platforms',
                'value_en' => '15+ Platforms Supported',
                'value_ar' => '15+ منصة مدعومة',
                'group' => 'stats',
            ],
            [
                'key' => 'stats.satisfaction',
                'value_en' => '98% Customer Satisfaction',
                'value_ar' => '98% رضا العملاء',
                'group' => 'stats',
            ],

            // Pricing Section
            [
                'key' => 'pricing.title',
                'value_en' => 'Simple, Transparent Pricing',
                'value_ar' => 'أسعار بسيطة وشفافة',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.subtitle',
                'value_en' => 'Choose the perfect plan for your needs',
                'value_ar' => 'اختر الخطة المثالية لاحتياجاتك',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.free.name',
                'value_en' => 'Free',
                'value_ar' => 'مجاني',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.free.description',
                'value_en' => 'Perfect for individuals getting started',
                'value_ar' => 'مثالي للأفراد الذين يبدأون',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.pro.name',
                'value_en' => 'Professional',
                'value_ar' => 'احترافي',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.pro.description',
                'value_en' => 'For growing businesses and teams',
                'value_ar' => 'للشركات والفرق المتنامية',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.enterprise.name',
                'value_en' => 'Enterprise',
                'value_ar' => 'مؤسسي',
                'group' => 'pricing',
            ],
            [
                'key' => 'pricing.enterprise.description',
                'value_en' => 'Advanced features for large organizations',
                'value_ar' => 'ميزات متقدمة للمؤسسات الكبيرة',
                'group' => 'pricing',
            ],

            // Footer Section
            [
                'key' => 'footer.company.title',
                'value_en' => 'Company',
                'value_ar' => 'الشركة',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.company.about',
                'value_en' => 'About Us',
                'value_ar' => 'من نحن',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.company.careers',
                'value_en' => 'Careers',
                'value_ar' => 'الوظائف',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.company.press',
                'value_en' => 'Press',
                'value_ar' => 'الصحافة',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.product.title',
                'value_en' => 'Product',
                'value_ar' => 'المنتج',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.product.features',
                'value_en' => 'Features',
                'value_ar' => 'الميزات',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.product.pricing',
                'value_en' => 'Pricing',
                'value_ar' => 'الأسعار',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.product.integrations',
                'value_en' => 'Integrations',
                'value_ar' => 'التكاملات',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.support.title',
                'value_en' => 'Support',
                'value_ar' => 'الدعم',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.support.help_center',
                'value_en' => 'Help Center',
                'value_ar' => 'مركز المساعدة',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.support.contact',
                'value_en' => 'Contact Us',
                'value_ar' => 'اتصل بنا',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.support.documentation',
                'value_en' => 'Documentation',
                'value_ar' => 'التوثيق',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.legal.title',
                'value_en' => 'Legal',
                'value_ar' => 'قانوني',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.legal.privacy',
                'value_en' => 'Privacy Policy',
                'value_ar' => 'سياسة الخصوصية',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.legal.terms',
                'value_en' => 'Terms of Service',
                'value_ar' => 'شروط الخدمة',
                'group' => 'footer',
            ],
            [
                'key' => 'footer.copyright',
                'value_en' => '2025 Social Media Manager. All rights reserved.',
                'value_ar' => '2025 مدير وسائل التواصل الاجتماعي. جميع الحقوق محفوظة.',
                'group' => 'footer',
            ],

            // Common/General Translations
            [
                'key' => 'common.login',
                'value_en' => 'Login',
                'value_ar' => 'تسجيل الدخول',
                'group' => 'general',
            ],
            [
                'key' => 'common.register',
                'value_en' => 'Register',
                'value_ar' => 'التسجيل',
                'group' => 'general',
            ],
            [
                'key' => 'common.logout',
                'value_en' => 'Logout',
                'value_ar' => 'تسجيل الخروج',
                'group' => 'general',
            ],
            [
                'key' => 'common.dashboard',
                'value_en' => 'Dashboard',
                'value_ar' => 'لوحة التحكم',
                'group' => 'general',
            ],
            [
                'key' => 'common.save',
                'value_en' => 'Save',
                'value_ar' => 'حفظ',
                'group' => 'general',
            ],
            [
                'key' => 'common.cancel',
                'value_en' => 'Cancel',
                'value_ar' => 'إلغاء',
                'group' => 'general',
            ],
            [
                'key' => 'common.delete',
                'value_en' => 'Delete',
                'value_ar' => 'حذف',
                'group' => 'general',
            ],
            [
                'key' => 'common.edit',
                'value_en' => 'Edit',
                'value_ar' => 'تعديل',
                'group' => 'general',
            ],
            [
                'key' => 'common.search',
                'value_en' => 'Search',
                'value_ar' => 'بحث',
                'group' => 'general',
            ],
            [
                'key' => 'common.loading',
                'value_en' => 'Loading...',
                'value_ar' => 'جاري التحميل...',
                'group' => 'general',
            ],
        ];

        foreach ($translations as $translation) {
            Translation::create($translation);
        }
    }
}
