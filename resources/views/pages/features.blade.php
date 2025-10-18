@extends('layouts.public')

@section('title', 'Features')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Powerful Features</h1>
        <h1 class="page-title content-ar">ميزات قوية</h1>
        <p class="page-subtitle content-en">
            Everything you need to manage, grow, and optimize your social media presence
        </p>
        <p class="page-subtitle content-ar">
            كل ما تحتاجه لإدارة وتنمية وتحسين تواجدك على وسائل التواصل الاجتماعي
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 30px;
                margin-top: 40px;
            }

            .feature-card {
                background: var(--dark-card);
                padding: 45px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                transition: all 0.4s ease;
            }

            .feature-card:hover {
                transform: translateY(-10px);
                border-color: var(--primary-purple);
                box-shadow: 0 20px 60px rgba(139, 92, 246, 0.3);
            }

            .feature-icon {
                font-size: 3.5rem;
                margin-bottom: 25px;
                display: inline-block;
            }

            .feature-title {
                font-size: 1.6rem;
                font-weight: 700;
                margin-bottom: 15px;
                color: var(--text-light);
            }

            .feature-desc {
                color: var(--text-gray);
                line-height: 1.8;
                font-size: 1.05rem;
            }

            .feature-list {
                margin-top: 20px;
                padding-left: 20px;
                color: var(--text-gray);
            }

            .feature-list li {
                margin-bottom: 10px;
                line-height: 1.6;
            }
        </style>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title content-en">Advanced Analytics</h3>
                <h3 class="feature-title content-ar">تحليلات متقدمة</h3>
                <p class="feature-desc content-en">
                    Get deep insights into your social media performance with comprehensive analytics and reporting tools.
                </p>
                <p class="feature-desc content-ar">
                    احصل على رؤى عميقة حول أداء وسائل التواصل الاجتماعي الخاصة بك مع أدوات التحليلات والتقارير الشاملة.
                </p>
                <ul class="feature-list content-en">
                    <li>Real-time performance tracking</li>
                    <li>Detailed engagement metrics</li>
                    <li>Audience demographics</li>
                    <li>Custom report generation</li>
                </ul>
                <ul class="feature-list content-ar">
                    <li>تتبع الأداء في الوقت الفعلي</li>
                    <li>مقاييس التفاعل التفصيلية</li>
                    <li>التركيبة السكانية للجمهور</li>
                    <li>إنشاء تقارير مخصصة</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⏰</div>
                <h3 class="feature-title content-en">Smart Scheduling</h3>
                <h3 class="feature-title content-ar">جدولة ذكية</h3>
                <p class="feature-desc content-en">
                    Schedule your posts at the perfect time with AI-powered recommendations based on your audience's activity.
                </p>
                <p class="feature-desc content-ar">
                    جدولة منشوراتك في الوقت المثالي مع توصيات الذكاء الاصطناعي بناءً على نشاط جمهورك.
                </p>
                <ul class="feature-list content-en">
                    <li>AI-powered best time suggestions</li>
                    <li>Bulk scheduling</li>
                    <li>Queue management</li>
                    <li>Timezone optimization</li>
                </ul>
                <ul class="feature-list content-ar">
                    <li>اقتراحات أفضل وقت بالذكاء الاصطناعي</li>
                    <li>جدولة جماعية</li>
                    <li>إدارة قائمة الانتظار</li>
                    <li>تحسين المناطق الزمنية</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <h3 class="feature-title content-en">AI Content Generator</h3>
                <h3 class="feature-title content-ar">مولد المحتوى بالذكاء الاصطناعي</h3>
                <p class="feature-desc content-en">
                    Create engaging content effortlessly with our advanced AI writing assistant powered by GPT technology.
                </p>
                <p class="feature-desc content-ar">
                    إنشاء محتوى جذاب بسهولة مع مساعد الكتابة بالذكاء الاصطناعي المتقدم المدعوم بتقنية GPT.
                </p>
                <ul class="feature-list content-en">
                    <li>Auto-generate captions</li>
                    <li>Hashtag suggestions</li>
                    <li>Content variations</li>
                    <li>Multi-language support</li>
                </ul>
                <ul class="feature-list content-ar">
                    <li>إنشاء تلقائي للتعليقات</li>
                    <li>اقتراحات الهاشتاج</li>
                    <li>تنويعات المحتوى</li>
                    <li>دعم متعدد اللغات</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🔗</div>
                <h3 class="feature-title content-en">Multi-Platform Support</h3>
                <h3 class="feature-title content-ar">دعم متعدد المنصات</h3>
                <p class="feature-desc content-en">
                    Connect and manage all your social media accounts from one unified dashboard.
                </p>
                <p class="feature-desc content-ar">
                    ربط وإدارة جميع حسابات وسائل التواصل الاجتماعي الخاصة بك من لوحة تحكم واحدة موحدة.
                </p>
                <ul class="feature-list content-en">
                    <li>Instagram, Facebook, Twitter</li>
                    <li>LinkedIn, TikTok, YouTube</li>
                    <li>Pinterest, Snapchat</li>
                    <li>And more platforms...</li>
                </ul>
                <ul class="feature-list content-ar">
                    <li>إنستغرام، فيسبوك، تويتر</li>
                    <li>لينكدإن، تيك توك، يوتيوب</li>
                    <li>بينتيريست، سناب شات</li>
                    <li>والمزيد من المنصات...</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🎨</div>
                <h3 class="feature-title content-en">Brand Management</h3>
                <h3 class="feature-title content-ar">إدارة العلامة التجارية</h3>
                <p class="feature-desc content-en">
                    Maintain consistent branding across all platforms with custom templates and asset libraries.
                </p>
                <p class="feature-desc content-ar">
                    الحفاظ على علامة تجارية متسقة عبر جميع المنصات مع قوالب مخصصة ومكتبات الأصول.
                </p>
                <ul class="feature-list content-en">
                    <li>Custom brand kits</li>
                    <li>Template library</li>
                    <li>Media asset management</li>
                    <li>Brand guidelines</li>
                </ul>
                <ul class="feature-list content-ar">
                    <li>مجموعات العلامة التجارية المخصصة</li>
                    <li>مكتبة القوالب</li>
                    <li>إدارة أصول الوسائط</li>
                    <li>إرشادات العلامة التجارية</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3 class="feature-title content-en">Team Collaboration</h3>
                <h3 class="feature-title content-ar">تعاون الفريق</h3>
                <p class="feature-desc content-en">
                    Work seamlessly with your team using roles, permissions, and approval workflows.
                </p>
                <p class="feature-desc content-ar">
                    العمل بسلاسة مع فريقك باستخدام الأدوار والأذونات وسير عمل الموافقة.
                </p>
                <ul class="feature-list content-en">
                    <li>Role-based access control</li>
                    <li>Approval workflows</li>
                    <li>Team comments & notes</li>
                    <li>Activity logs</li>
                </ul>
                <ul class="feature-list content-ar">
                    <li>التحكم في الوصول على أساس الدور</li>
                    <li>سير عمل الموافقة</li>
                    <li>تعليقات وملاحظات الفريق</li>
                    <li>سجلات النشاط</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
