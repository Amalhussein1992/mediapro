@extends('layouts.public')

@section('title', 'About Us')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">About Media Pro</h1>
        <h1 class="page-title content-ar">من نحن</h1>
        <p class="page-subtitle content-en">
            Empowering creators and businesses to succeed on social media
        </p>
        <p class="page-subtitle content-ar">
            تمكين المبدعين والشركات من النجاح على وسائل التواصل الاجتماعي
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .about-content {
                max-width: 900px;
                margin: 0 auto;
            }

            .about-section {
                background: var(--dark-card);
                padding: 50px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                margin-bottom: 30px;
            }

            .about-section h2 {
                font-size: 2.5rem;
                margin-bottom: 25px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .about-section p {
                color: var(--text-gray);
                line-height: 2;
                font-size: 1.1rem;
                margin-bottom: 20px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 30px;
                margin: 40px 0;
            }

            .stat-box {
                text-align: center;
                padding: 30px;
                background: rgba(99, 102, 241, 0.05);
                border-radius: 15px;
                border: 1px solid rgba(99, 102, 241, 0.1);
            }

            .stat-number {
                font-size: 3rem;
                font-weight: 900;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 10px;
            }

            .stat-label {
                color: var(--text-gray);
                font-size: 1.1rem;
            }
        </style>

        <div class="about-content">
            <div class="about-section">
                <h2 class="content-en">Our Story</h2>
                <h2 class="content-ar">قصتنا</h2>
                <p class="content-en">
                    Media Pro was founded in 2023 with a simple mission: make social media management accessible,
                    powerful, and efficient for everyone. We've grown from a small startup to serving thousands
                    of creators, businesses, and agencies worldwide.
                </p>
                <p class="content-ar">
                    تأسست ميديا برو في عام 2023 بمهمة بسيطة: جعل إدارة وسائل التواصل الاجتماعي سهلة الوصول
                    وقوية وفعالة للجميع. لقد نمونا من شركة ناشئة صغيرة لخدمة آلاف المبدعين والشركات والوكالات في جميع أنحاء العالم.
                </p>
            </div>

            <div class="about-section">
                <h2 class="content-en">Our Mission</h2>
                <h2 class="content-ar">مهمتنا</h2>
                <p class="content-en">
                    We believe that every creator and business deserves access to professional-grade social media
                    management tools. Our platform combines cutting-edge AI technology with intuitive design to
                    help you grow your audience, engage with your community, and achieve your goals.
                </p>
                <p class="content-ar">
                    نؤمن بأن كل مبدع وشركة يستحق الوصول إلى أدوات إدارة وسائل التواصل الاجتماعي بمستوى احترافي.
                    تجمع منصتنا بين تكنولوجيا الذكاء الاصطناعي المتطورة والتصميم البديهي لمساعدتك على تنمية جمهورك والتفاعل مع مجتمعك وتحقيق أهدافك.
                </p>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label content-en">Active Users</div>
                    <div class="stat-label content-ar">مستخدم نشط</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">5M+</div>
                    <div class="stat-label content-en">Posts Managed</div>
                    <div class="stat-label content-ar">منشور مُدار</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">150+</div>
                    <div class="stat-label content-en">Countries</div>
                    <div class="stat-label content-ar">دولة</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label content-en">Uptime</div>
                    <div class="stat-label content-ar">وقت التشغيل</div>
                </div>
            </div>

            <div class="about-section">
                <h2 class="content-en">Why Choose Us?</h2>
                <h2 class="content-ar">لماذا تختارنا؟</h2>
                <p class="content-en">
                    • <strong>Innovation First:</strong> We continuously improve our platform with the latest technology<br>
                    • <strong>Customer Success:</strong> Your success is our success - we're here to support you<br>
                    • <strong>Security & Privacy:</strong> Your data is protected with enterprise-grade security<br>
                    • <strong>Global Reach:</strong> Supporting creators and businesses in over 150 countries
                </p>
                <p class="content-ar">
                    • <strong>الابتكار أولاً:</strong> نحسن منصتنا باستمرار بأحدث التقنيات<br>
                    • <strong>نجاح العملاء:</strong> نجاحك هو نجاحنا - نحن هنا لدعمك<br>
                    • <strong>الأمان والخصوصية:</strong> بياناتك محمية بأمان على مستوى المؤسسات<br>
                    • <strong>الوصول العالمي:</strong> دعم المبدعين والشركات في أكثر من 150 دولة
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
