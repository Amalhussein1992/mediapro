@extends('layouts.public')

@section('title', 'Help Center')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Help Center</h1>
        <h1 class="page-title content-ar">مركز المساعدة</h1>
        <p class="page-subtitle content-en">
            Find answers to common questions and get support
        </p>
        <p class="page-subtitle content-ar">
            اعثر على إجابات للأسئلة الشائعة واحصل على الدعم
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .help-categories {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                margin-bottom: 60px;
            }

            .help-category {
                background: var(--dark-card);
                padding: 35px;
                border-radius: 15px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                transition: all 0.3s ease;
                cursor: pointer;
                text-align: center;
            }

            .help-category:hover {
                transform: translateY(-5px);
                border-color: var(--primary-purple);
                box-shadow: 0 15px 40px rgba(99, 102, 241, 0.3);
            }

            .category-icon {
                font-size: 3rem;
                margin-bottom: 15px;
            }

            .category-title {
                font-size: 1.3rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 10px;
            }

            .category-desc {
                color: var(--text-gray);
                font-size: 0.95rem;
            }

            .faq-section {
                max-width: 900px;
                margin: 0 auto;
            }

            .faq-title {
                font-size: 2.5rem;
                margin-bottom: 40px;
                text-align: center;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .faq-item {
                background: var(--dark-card);
                padding: 30px;
                border-radius: 15px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                margin-bottom: 20px;
            }

            .faq-question {
                font-size: 1.3rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 15px;
            }

            .faq-answer {
                color: var(--text-gray);
                line-height: 1.8;
            }

            .contact-support {
                text-align: center;
                margin-top: 60px;
                padding: 50px;
                background: var(--dark-card);
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
            }

            .support-button {
                display: inline-block;
                margin-top: 20px;
                padding: 15px 40px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                color: white;
                text-decoration: none;
                border-radius: 12px;
                font-weight: 700;
                transition: all 0.3s ease;
            }

            .support-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
            }
        </style>

        <div class="help-categories">
            <div class="help-category">
                <div class="category-icon">🚀</div>
                <h3 class="category-title content-en">Getting Started</h3>
                <h3 class="category-title content-ar">البدء</h3>
                <p class="category-desc content-en">Learn the basics of Media Pro</p>
                <p class="category-desc content-ar">تعلم أساسيات ميديا برو</p>
            </div>

            <div class="help-category">
                <div class="category-icon">⏰</div>
                <h3 class="category-title content-en">Scheduling</h3>
                <h3 class="category-title content-ar">الجدولة</h3>
                <p class="category-desc content-en">Schedule and manage your posts</p>
                <p class="category-desc content-ar">جدولة وإدارة منشوراتك</p>
            </div>

            <div class="help-category">
                <div class="category-icon">📊</div>
                <h3 class="category-title content-en">Analytics</h3>
                <h3 class="category-title content-ar">التحليلات</h3>
                <p class="category-desc content-en">Understand your performance metrics</p>
                <p class="category-desc content-ar">فهم مقاييس أدائك</p>
            </div>

            <div class="help-category">
                <div class="category-icon">🔗</div>
                <h3 class="category-title content-en">Integrations</h3>
                <h3 class="category-title content-ar">التكاملات</h3>
                <p class="category-desc content-en">Connect your social accounts</p>
                <p class="category-desc content-ar">ربط حساباتك الاجتماعية</p>
            </div>

            <div class="help-category">
                <div class="category-icon">👥</div>
                <h3 class="category-title content-en">Team Management</h3>
                <h3 class="category-title content-ar">إدارة الفريق</h3>
                <p class="category-desc content-en">Collaborate with your team</p>
                <p class="category-desc content-ar">التعاون مع فريقك</p>
            </div>

            <div class="help-category">
                <div class="category-icon">⚙️</div>
                <h3 class="category-title content-en">Account Settings</h3>
                <h3 class="category-title content-ar">إعدادات الحساب</h3>
                <p class="category-desc content-en">Manage your account preferences</p>
                <p class="category-desc content-ar">إدارة تفضيلات حسابك</p>
            </div>
        </div>

        <div class="faq-section">
            <h2 class="faq-title content-en">Frequently Asked Questions</h2>
            <h2 class="faq-title content-ar">الأسئلة الشائعة</h2>

            <div class="faq-item">
                <h3 class="faq-question content-en">How do I connect my social media accounts?</h3>
                <h3 class="faq-question content-ar">كيف أربط حسابات وسائل التواصل الاجتماعي الخاصة بي؟</h3>
                <p class="faq-answer content-en">
                    Go to Settings > Social Accounts and click "Add Account". Follow the authentication
                    process for each platform you want to connect. We support Instagram, Facebook, Twitter,
                    LinkedIn, and many more platforms.
                </p>
                <p class="faq-answer content-ar">
                    انتقل إلى الإعدادات > الحسابات الاجتماعية وانقر على "إضافة حساب". اتبع عملية المصادقة
                    لكل منصة تريد ربطها. ندعم إنستغرام وفيسبوك وتويتر ولينكدإن والعديد من المنصات الأخرى.
                </p>
            </div>

            <div class="faq-item">
                <h3 class="faq-question content-en">Can I schedule posts to multiple platforms at once?</h3>
                <h3 class="faq-question content-ar">هل يمكنني جدولة المنشورات لمنصات متعددة في آن واحد؟</h3>
                <p class="faq-answer content-en">
                    Yes! When creating a post, simply select multiple accounts from different platforms.
                    Media Pro will optimize your content for each platform automatically.
                </p>
                <p class="faq-answer content-ar">
                    نعم! عند إنشاء منشور، ما عليك سوى تحديد حسابات متعددة من منصات مختلفة.
                    سيقوم ميديا برو بتحسين محتواك لكل منصة تلقائياً.
                </p>
            </div>

            <div class="faq-item">
                <h3 class="faq-question content-en">How does the AI content generator work?</h3>
                <h3 class="faq-question content-ar">كيف يعمل مولد المحتوى بالذكاء الاصطناعي؟</h3>
                <p class="faq-answer content-en">
                    Our AI analyzes your brand, audience, and trending topics to generate engaging captions,
                    hashtags, and content ideas. Simply provide a topic or theme, and our AI will create
                    multiple variations for you to choose from.
                </p>
                <p class="faq-answer content-ar">
                    يقوم الذكاء الاصطناعي لدينا بتحليل علامتك التجارية وجمهورك والمواضيع الرائجة لإنشاء تعليقات
                    وهاشتاجات وأفكار محتوى جذابة. ما عليك سوى تقديم موضوع أو فكرة، وسينشئ الذكاء الاصطناعي لدينا
                    عدة تنويعات لك للاختيار من بينها.
                </p>
            </div>

            <div class="faq-item">
                <h3 class="faq-question content-en">What's included in the free trial?</h3>
                <h3 class="faq-question content-ar">ما المتضمن في الفترة التجريبية المجانية؟</h3>
                <p class="faq-answer content-en">
                    The 14-day free trial includes full access to all Professional plan features: unlimited
                    posts, advanced analytics, AI content generation, team collaboration, and priority support.
                    No credit card required.
                </p>
                <p class="faq-answer content-ar">
                    تتضمن الفترة التجريبية المجانية لمدة 14 يوماً وصولاً كاملاً إلى جميع ميزات خطة المحترف: منشورات
                    غير محدودة، تحليلات متقدمة، إنشاء محتوى بالذكاء الاصطناعي، تعاون الفريق، ودعم ذو أولوية.
                    لا حاجة لبطاقة ائتمان.
                </p>
            </div>
        </div>

        <div class="contact-support">
            <h2 style="font-size: 2rem; margin-bottom: 15px;" class="content-en">Still Need Help?</h2>
            <h2 style="font-size: 2rem; margin-bottom: 15px;" class="content-ar">لا تزال بحاجة إلى مساعدة؟</h2>
            <p style="color: var(--text-gray); font-size: 1.1rem;" class="content-en">
                Our support team is here to help you 24/7
            </p>
            <p style="color: var(--text-gray); font-size: 1.1rem;" class="content-ar">
                فريق الدعم لدينا هنا لمساعدتك على مدار الساعة
            </p>
            <a href="{{ route('contact') }}" class="support-button content-en">Contact Support</a>
            <a href="{{ route('contact') }}" class="support-button content-ar">تواصل مع الدعم</a>
        </div>
    </div>
</section>
@endsection
