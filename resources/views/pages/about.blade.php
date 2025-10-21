@extends('layouts.public')

@section('title', 'About Us')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">About Media Pro</h1>
        <h1 class="page-title content-ar">من نحن - ميديا برو</h1>
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
            :root {
                --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                --dark-bg: #0f0f23;
                --dark-card: #1a1a2e;
                --dark-card-hover: #252540;
                --text-light: #ffffff;
                --text-gray: #a0aec0;
                --text-muted: #718096;
                --border-color: rgba(99, 102, 241, 0.15);
                --border-hover: rgba(99, 102, 241, 0.3);
            }

            .about-content {
                max-width: 1200px;
                margin: 0 auto;
                padding-bottom: 60px;
            }

            .about-hero {
                background: var(--primary-gradient);
                padding: 80px 60px;
                border-radius: 30px;
                text-align: center;
                margin-bottom: 50px;
                box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
                position: relative;
                overflow: hidden;
            }

            .about-hero::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                animation: rotate 20s linear infinite;
            }

            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .about-hero h2 {
                font-size: 2.8rem;
                font-weight: 800;
                color: white;
                margin-bottom: 20px;
                position: relative;
                z-index: 1;
            }

            .about-hero p {
                font-size: 1.3rem;
                color: rgba(255, 255, 255, 0.95);
                line-height: 1.8;
                max-width: 800px;
                margin: 0 auto;
                position: relative;
                z-index: 1;
            }

            .about-section {
                background: var(--dark-card);
                padding: 50px;
                border-radius: 25px;
                border: 1px solid var(--border-color);
                margin-bottom: 35px;
                transition: all 0.3s ease;
            }

            .about-section:hover {
                border-color: var(--border-hover);
                box-shadow: 0 15px 50px rgba(99, 102, 241, 0.15);
                transform: translateY(-5px);
            }

            .about-section h2 {
                font-size: 2.3rem;
                margin-bottom: 30px;
                font-weight: 700;
                background: var(--primary-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .about-section h2::before {
                content: '';
                width: 8px;
                height: 45px;
                background: var(--primary-gradient);
                border-radius: 10px;
            }

            .about-section h3 {
                font-size: 1.6rem;
                color: var(--text-light);
                margin-top: 35px;
                margin-bottom: 20px;
                font-weight: 600;
            }

            .about-section p {
                color: var(--text-gray);
                line-height: 1.9;
                font-size: 1.1rem;
                margin-bottom: 25px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 30px;
                margin: 50px 0;
            }

            .stat-box {
                text-align: center;
                padding: 40px 30px;
                background: var(--dark-card);
                border-radius: 20px;
                border: 2px solid var(--border-color);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .stat-box::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 5px;
                background: var(--primary-gradient);
                transform: scaleX(0);
                transition: transform 0.3s ease;
            }

            .stat-box:hover::before {
                transform: scaleX(1);
            }

            .stat-box:hover {
                border-color: var(--border-hover);
                transform: translateY(-10px);
                box-shadow: 0 20px 50px rgba(99, 102, 241, 0.2);
            }

            .stat-number {
                font-size: 3.5rem;
                font-weight: 900;
                background: var(--primary-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 15px;
                display: block;
            }

            .stat-label {
                color: var(--text-gray);
                font-size: 1.2rem;
                font-weight: 500;
            }

            .values-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                margin: 40px 0;
            }

            .value-card {
                background: var(--dark-card);
                padding: 35px;
                border-radius: 20px;
                border: 1px solid var(--border-color);
                transition: all 0.3s ease;
            }

            .value-card:hover {
                border-color: var(--border-hover);
                transform: translateY(-5px);
                box-shadow: 0 15px 40px rgba(99, 102, 241, 0.15);
            }

            .value-icon {
                font-size: 3rem;
                margin-bottom: 20px;
                display: block;
            }

            .value-card h4 {
                font-size: 1.5rem;
                color: var(--text-light);
                margin-bottom: 15px;
                font-weight: 600;
            }

            .value-card p {
                color: var(--text-gray);
                line-height: 1.7;
                margin-bottom: 0;
                font-size: 1.05rem;
            }

            .team-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 35px;
                margin: 40px 0;
            }

            .team-member {
                background: var(--dark-card);
                padding: 40px 30px;
                border-radius: 20px;
                border: 1px solid var(--border-color);
                text-align: center;
                transition: all 0.3s ease;
            }

            .team-member:hover {
                border-color: var(--border-hover);
                transform: translateY(-10px);
                box-shadow: 0 20px 50px rgba(99, 102, 241, 0.2);
            }

            .team-avatar {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                background: var(--primary-gradient);
                margin: 0 auto 25px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
                color: white;
                font-weight: bold;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            }

            .team-name {
                font-size: 1.5rem;
                color: var(--text-light);
                font-weight: 600;
                margin-bottom: 8px;
            }

            .team-role {
                color: var(--text-gray);
                font-size: 1.1rem;
                margin-bottom: 15px;
            }

            .team-bio {
                color: var(--text-muted);
                font-size: 0.95rem;
                line-height: 1.6;
            }

            .timeline {
                position: relative;
                padding: 40px 0;
            }

            .timeline-item {
                position: relative;
                padding-left: 60px;
                margin-bottom: 40px;
            }

            .timeline-item::before {
                content: '';
                position: absolute;
                left: 18px;
                top: 30px;
                bottom: -40px;
                width: 2px;
                background: var(--primary-gradient);
            }

            .timeline-item:last-child::before {
                display: none;
            }

            .timeline-dot {
                position: absolute;
                left: 0;
                top: 0;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: var(--primary-gradient);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            }

            .timeline-content {
                background: var(--dark-card);
                padding: 25px;
                border-radius: 15px;
                border: 1px solid var(--border-color);
            }

            .timeline-year {
                font-size: 1.8rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 10px;
            }

            .timeline-text {
                color: var(--text-gray);
                line-height: 1.7;
                font-size: 1.05rem;
            }

            .cta-section {
                background: var(--primary-gradient);
                padding: 60px;
                border-radius: 30px;
                text-align: center;
                margin-top: 60px;
                box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
            }

            .cta-section h2 {
                color: white;
                font-size: 2.5rem;
                font-weight: 800;
                margin-bottom: 20px;
            }

            .cta-section p {
                color: rgba(255, 255, 255, 0.95);
                font-size: 1.3rem;
                margin-bottom: 35px;
            }

            .cta-buttons {
                display: flex;
                gap: 20px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .cta-button {
                padding: 18px 45px;
                border-radius: 50px;
                font-size: 1.2rem;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-block;
            }

            .cta-button-primary {
                background: white;
                color: #667eea;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .cta-button-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            }

            .cta-button-secondary {
                background: transparent;
                color: white;
                border: 2px solid white;
            }

            .cta-button-secondary:hover {
                background: white;
                color: #667eea;
                transform: translateY(-3px);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .about-hero {
                    padding: 50px 30px;
                }

                .about-hero h2 {
                    font-size: 2rem;
                }

                .about-hero p {
                    font-size: 1.1rem;
                }

                .about-section {
                    padding: 30px 25px;
                }

                .about-section h2 {
                    font-size: 1.8rem;
                }

                .stat-number {
                    font-size: 2.5rem;
                }

                .cta-section {
                    padding: 40px 25px;
                }

                .cta-section h2 {
                    font-size: 1.8rem;
                }

                .cta-buttons {
                    flex-direction: column;
                    align-items: stretch;
                }
            }

            /* RTL Support */
            [dir="rtl"] .timeline-item {
                padding-left: 0;
                padding-right: 60px;
            }

            [dir="rtl"] .timeline-item::before {
                left: auto;
                right: 18px;
            }

            [dir="rtl"] .timeline-dot {
                left: auto;
                right: 0;
            }

            [dir="rtl"] .about-section h2::before {
                order: 2;
            }

            strong {
                color: var(--text-light);
                font-weight: 600;
            }
        </style>

        <div class="about-content">
            <!-- Hero Section -->
            <div class="about-hero">
                <h2 class="content-en">Building the Future of Social Media Management</h2>
                <h2 class="content-ar">بناء مستقبل إدارة وسائل التواصل الاجتماعي</h2>
                <p class="content-en">
                    We're on a mission to empower creators, marketers, and businesses with the most powerful,
                    intuitive, and intelligent social media management platform in the world.
                </p>
                <p class="content-ar">
                    نحن في مهمة لتمكين المبدعين والمسوقين والشركات بأقوى منصة لإدارة وسائل التواصل الاجتماعي
                    وأكثرها سهولة وذكاءً في العالم.
                </p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-box">
                    <span class="stat-number">100K+</span>
                    <div class="stat-label content-en">Active Users</div>
                    <div class="stat-label content-ar">مستخدم نشط</div>
                </div>
                <div class="stat-box">
                    <span class="stat-number">10M+</span>
                    <div class="stat-label content-en">Posts Scheduled</div>
                    <div class="stat-label content-ar">منشور مُجدول</div>
                </div>
                <div class="stat-box">
                    <span class="stat-number">180+</span>
                    <div class="stat-label content-en">Countries</div>
                    <div class="stat-label content-ar">دولة</div>
                </div>
                <div class="stat-box">
                    <span class="stat-number">99.9%</span>
                    <div class="stat-label content-en">Uptime SLA</div>
                    <div class="stat-label content-ar">وقت التشغيل</div>
                </div>
            </div>

            <!-- Our Story -->
            <div class="about-section">
                <h2 class="content-en">📖 Our Story</h2>
                <h2 class="content-ar">📖 قصتنا</h2>

                <p class="content-en">
                    Media Pro was born from a simple frustration: managing social media shouldn't be complicated.
                    Founded in 2023 by a team of digital marketers and software engineers, we set out to create
                    a platform that combines powerful features with elegant simplicity.
                </p>
                <p class="content-ar">
                    وُلدت ميديا برو من إحباط بسيط: إدارة وسائل التواصل الاجتماعي لا ينبغي أن تكون معقدة.
                    تأسست في عام 2023 من قبل فريق من المسوقين الرقميين ومهندسي البرمجيات، شرعنا في إنشاء
                    منصة تجمع بين الميزات القوية والبساطة الأنيقة.
                </p>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot">🚀</div>
                        <div class="timeline-content">
                            <div class="timeline-year">2023</div>
                            <p class="timeline-text content-en">
                                <strong>January:</strong> Media Pro founded with a vision to revolutionize social media management.<br>
                                <strong>March:</strong> Beta launch with 100 early adopters providing valuable feedback.<br>
                                <strong>June:</strong> Official public launch - reached 5,000 users in first month.
                            </p>
                            <p class="timeline-text content-ar">
                                <strong>يناير:</strong> تأسست ميديا برو برؤية لإحداث ثورة في إدارة وسائل التواصل الاجتماعي.<br>
                                <strong>مارس:</strong> إطلاق تجريبي مع 100 مستخدم أوائل يقدمون ملاحظات قيمة.<br>
                                <strong>يونيو:</strong> الإطلاق العام الرسمي - وصلنا إلى 5,000 مستخدم في الشهر الأول.
                            </p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot">📈</div>
                        <div class="timeline-content">
                            <div class="timeline-year">2024</div>
                            <p class="timeline-text content-en">
                                <strong>Q1:</strong> Introduced AI-powered content generation and hashtag suggestions.<br>
                                <strong>Q2:</strong> Expanded to support 10+ social media platforms.<br>
                                <strong>Q3:</strong> Reached 50,000 users across 150 countries.<br>
                                <strong>Q4:</strong> Launched Enterprise plan for large teams and agencies.
                            </p>
                            <p class="timeline-text content-ar">
                                <strong>الربع الأول:</strong> قدمنا إنشاء محتوى مدعوم بالذكاء الاصطناعي واقتراحات الهاشتاج.<br>
                                <strong>الربع الثاني:</strong> توسعنا لدعم أكثر من 10 منصات تواصل اجتماعي.<br>
                                <strong>الربع الثالث:</strong> وصلنا إلى 50,000 مستخدم في 150 دولة.<br>
                                <strong>الربع الرابع:</strong> أطلقنا خطة المؤسسات للفرق الكبيرة والوكالات.
                            </p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot">🎯</div>
                        <div class="timeline-content">
                            <div class="timeline-year">2025</div>
                            <p class="timeline-text content-en">
                                <strong>Current:</strong> Serving 100,000+ users, managing 10M+ posts monthly.<br>
                                <strong>Focus:</strong> Advanced analytics, AI-driven insights, and automation.<br>
                                <strong>Goal:</strong> Become the #1 social media management platform globally.
                            </p>
                            <p class="timeline-text content-ar">
                                <strong>الحالي:</strong> نخدم أكثر من 100,000 مستخدم، ونُدير أكثر من 10 ملايين منشور شهرياً.<br>
                                <strong>التركيز:</strong> تحليلات متقدمة ورؤى مدفوعة بالذكاء الاصطناعي والأتمتة.<br>
                                <strong>الهدف:</strong> أن نصبح منصة إدارة وسائل التواصل الاجتماعي رقم 1 عالمياً.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="about-section">
                <h2 class="content-en">🎯 Our Mission & Vision</h2>
                <h2 class="content-ar">🎯 مهمتنا ورؤيتنا</h2>

                <h3 class="content-en">Mission</h3>
                <h3 class="content-ar">المهمة</h3>
                <p class="content-en">
                    To democratize professional social media management by providing accessible, powerful,
                    and intelligent tools that help creators and businesses of all sizes grow their online presence,
                    engage authentically with their audience, and achieve measurable results.
                </p>
                <p class="content-ar">
                    جعل إدارة وسائل التواصل الاجتماعي الاحترافية ديمقراطية من خلال توفير أدوات سهلة الوصول وقوية
                    وذكية تساعد المبدعين والشركات من جميع الأحجام على تنمية تواجدهم عبر الإنترنت والتفاعل
                    بشكل حقيقي مع جمهورهم وتحقيق نتائج قابلة للقياس.
                </p>

                <h3 class="content-en">Vision</h3>
                <h3 class="content-ar">الرؤية</h3>
                <p class="content-en">
                    To become the world's most trusted and innovative social media management platform,
                    empowering millions of creators and businesses to tell their stories, build communities,
                    and create meaningful impact in the digital world.
                </p>
                <p class="content-ar">
                    أن نصبح منصة إدارة وسائل التواصل الاجتماعي الأكثر موثوقية وابتكاراً في العالم، وتمكين
                    ملايين المبدعين والشركات من سرد قصصهم وبناء المجتمعات وخلق تأثير ذي مغزى في العالم الرقمي.
                </p>
            </div>

            <!-- Our Values -->
            <div class="about-section">
                <h2 class="content-en">💎 Our Core Values</h2>
                <h2 class="content-ar">💎 قيمنا الأساسية</h2>

                <div class="values-grid">
                    <div class="value-card">
                        <span class="value-icon">🚀</span>
                        <h4 class="content-en">Innovation First</h4>
                        <h4 class="content-ar">الابتكار أولاً</h4>
                        <p class="content-en">
                            We constantly push boundaries and embrace new technologies to deliver cutting-edge features
                            that keep our users ahead of the curve.
                        </p>
                        <p class="content-ar">
                            نحن نتجاوز الحدود باستمرار ونتبنى التقنيات الجديدة لتقديم ميزات متطورة
                            تجعل مستخدمينا في الطليعة.
                        </p>
                    </div>

                    <div class="value-card">
                        <span class="value-icon">🤝</span>
                        <h4 class="content-en">Customer Success</h4>
                        <h4 class="content-ar">نجاح العميل</h4>
                        <p class="content-en">
                            Your success is our success. We're committed to providing exceptional support and
                            continuously improving based on your feedback.
                        </p>
                        <p class="content-ar">
                            نجاحك هو نجاحنا. نحن ملتزمون بتقديم دعم استثنائي والتحسين المستمر
                            بناءً على ملاحظاتك.
                        </p>
                    </div>

                    <div class="value-card">
                        <span class="value-icon">🔒</span>
                        <h4 class="content-en">Security & Privacy</h4>
                        <h4 class="content-ar">الأمان والخصوصية</h4>
                        <p class="content-en">
                            We treat your data with the utmost care, implementing enterprise-grade security
                            and respecting your privacy at every step.
                        </p>
                        <p class="content-ar">
                            نتعامل مع بياناتك بأقصى قدر من العناية، وننفذ أماناً على مستوى المؤسسات
                            ونحترم خصوصيتك في كل خطوة.
                        </p>
                    </div>

                    <div class="value-card">
                        <span class="value-icon">🌍</span>
                        <h4 class="content-en">Global Accessibility</h4>
                        <h4 class="content-ar">الوصول العالمي</h4>
                        <p class="content-en">
                            We believe great tools should be accessible to everyone, everywhere.
                            Our platform supports multiple languages and serves users in 180+ countries.
                        </p>
                        <p class="content-ar">
                            نحن نؤمن بأن الأدوات الرائعة يجب أن تكون متاحة للجميع في كل مكان.
                            تدعم منصتنا لغات متعددة وتخدم المستخدمين في أكثر من 180 دولة.
                        </p>
                    </div>

                    <div class="value-card">
                        <span class="value-icon">⚡</span>
                        <h4 class="content-en">Simplicity & Power</h4>
                        <h4 class="content-ar">البساطة والقوة</h4>
                        <p class="content-en">
                            We design interfaces that are intuitive and easy to use, without compromising
                            on the powerful features professionals need.
                        </p>
                        <p class="content-ar">
                            نصمم واجهات بديهية وسهلة الاستخدام، دون المساس
                            بالميزات القوية التي يحتاجها المحترفون.
                        </p>
                    </div>

                    <div class="value-card">
                        <span class="value-icon">🌱</span>
                        <h4 class="content-en">Continuous Growth</h4>
                        <h4 class="content-ar">النمو المستمر</h4>
                        <p class="content-en">
                            We're always learning, evolving, and improving. Every day we work to make
                            Media Pro better for our users.
                        </p>
                        <p class="content-ar">
                            نحن دائماً نتعلم ونتطور ونتحسن. كل يوم نعمل على جعل
                            ميديا برو أفضل لمستخدمينا.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Team -->
            <div class="about-section">
                <h2 class="content-en">👥 Meet Our Team</h2>
                <h2 class="content-ar">👥 تعرف على فريقنا</h2>

                <p class="content-en">
                    Behind Media Pro is a passionate team of designers, developers, marketers, and support specialists
                    dedicated to making your social media management experience exceptional.
                </p>
                <p class="content-ar">
                    وراء ميديا برو فريق شغوف من المصممين والمطورين والمسوقين وأخصائيي الدعم
                    المكرسين لجعل تجربة إدارة وسائل التواصل الاجتماعي الخاصة بك استثنائية.
                </p>

                <div class="team-grid">
                    <div class="team-member">
                        <div class="team-avatar">AK</div>
                        <div class="team-name content-en">Ahmed Khalil</div>
                        <div class="team-name content-ar">أحمد خليل</div>
                        <div class="team-role content-en">Founder & CEO</div>
                        <div class="team-role content-ar">المؤسس والرئيس التنفيذي</div>
                        <p class="team-bio content-en">
                            Visionary leader with 10+ years in social media and tech entrepreneurship.
                        </p>
                        <p class="team-bio content-ar">
                            قائد صاحب رؤية مع أكثر من 10 سنوات في وسائل التواصل الاجتماعي وريادة الأعمال التقنية.
                        </p>
                    </div>

                    <div class="team-member">
                        <div class="team-avatar">SF</div>
                        <div class="team-name content-en">Sarah Foster</div>
                        <div class="team-name content-ar">سارة فوستر</div>
                        <div class="team-role content-en">Head of Product</div>
                        <div class="team-role content-ar">رئيسة المنتج</div>
                        <p class="team-bio content-en">
                            Product strategist obsessed with creating delightful user experiences.
                        </p>
                        <p class="team-bio content-ar">
                            استراتيجية منتجات مهووسة بإنشاء تجارب مستخدم رائعة.
                        </p>
                    </div>

                    <div class="team-member">
                        <div class="team-avatar">MR</div>
                        <div class="team-name content-en">Mohammed Rahman</div>
                        <div class="team-name content-ar">محمد رحمن</div>
                        <div class="team-role content-en">CTO</div>
                        <div class="team-role content-ar">المدير التقني</div>
                        <p class="team-bio content-en">
                            Tech wizard building scalable systems that power millions of posts.
                        </p>
                        <p class="team-bio content-ar">
                            ساحر تقني يبني أنظمة قابلة للتوسع تعمل على تشغيل ملايين المنشورات.
                        </p>
                    </div>

                    <div class="team-member">
                        <div class="team-avatar">LC</div>
                        <div class="team-name content-en">Lisa Chen</div>
                        <div class="team-name content-ar">ليزا تشن</div>
                        <div class="team-role content-en">Head of Marketing</div>
                        <div class="team-role content-ar">رئيسة التسويق</div>
                        <p class="team-bio content-en">
                            Growth expert helping thousands discover the power of Media Pro.
                        </p>
                        <p class="team-bio content-ar">
                            خبيرة نمو تساعد الآلاف على اكتشاف قوة ميديا برو.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="about-section">
                <h2 class="content-en">⭐ Why Choose Media Pro?</h2>
                <h2 class="content-ar">⭐ لماذا تختار ميديا برو؟</h2>

                <h3 class="content-en">What Sets Us Apart</h3>
                <h3 class="content-ar">ما يميزنا</h3>

                <p class="content-en">
                    <strong>🤖 AI-Powered Intelligence:</strong> Our platform uses advanced AI to suggest content,
                    optimize posting times, and generate captions that resonate with your audience.<br><br>

                    <strong>📊 Comprehensive Analytics:</strong> Get deep insights into your performance across all platforms
                    with intuitive dashboards and actionable recommendations.<br><br>

                    <strong>🎨 Unified Content Calendar:</strong> Visualize and manage all your social content in one place
                    with our beautiful, drag-and-drop calendar interface.<br><br>

                    <strong>👥 Team Collaboration:</strong> Work seamlessly with your team using roles, permissions,
                    approval workflows, and real-time notifications.<br><br>

                    <strong>🔗 Multi-Platform Support:</strong> Connect and manage all major social platforms including
                    Facebook, Instagram, Twitter, LinkedIn, TikTok, YouTube, and more.<br><br>

                    <strong>🚀 Lightning Fast:</strong> Our infrastructure is built for speed and reliability,
                    ensuring your content goes live exactly when you want it.<br><br>

                    <strong>💬 24/7 Support:</strong> Our dedicated support team is always here to help you succeed,
                    with multilingual support and comprehensive documentation.<br><br>

                    <strong>🔐 Enterprise Security:</strong> Bank-level encryption, SOC 2 compliance, and regular security
                    audits keep your data safe.
                </p>

                <p class="content-ar">
                    <strong>🤖 ذكاء مدعوم بالذكاء الاصطناعي:</strong> تستخدم منصتنا الذكاء الاصطناعي المتقدم لاقتراح المحتوى
                    وتحسين أوقات النشر وإنشاء تسميات توضيحية تتناسب مع جمهورك.<br><br>

                    <strong>📊 تحليلات شاملة:</strong> احصل على رؤى عميقة حول أدائك عبر جميع المنصات
                    مع لوحات معلومات بديهية وتوصيات قابلة للتنفيذ.<br><br>

                    <strong>🎨 تقويم محتوى موحد:</strong> تصور وإدارة جميع محتوى الوسائط الاجتماعية الخاص بك في مكان واحد
                    مع واجهة التقويم الجميلة بالسحب والإفلات.<br><br>

                    <strong>👥 التعاون الجماعي:</strong> اعمل بسلاسة مع فريقك باستخدام الأدوار والأذونات
                    وسير عمل الموافقة والإشعارات في الوقت الفعلي.<br><br>

                    <strong>🔗 دعم منصات متعددة:</strong> اتصل وإدارة جميع المنصات الاجتماعية الرئيسية بما في ذلك
                    فيسبوك وإنستغرام وتويتر ولينكد إن وتيك توك ويوتيوب والمزيد.<br><br>

                    <strong>🚀 سريع كالبرق:</strong> بنيتنا التحتية مصممة للسرعة والموثوقية،
                    مما يضمن نشر المحتوى الخاص بك بالضبط عندما تريد.<br><br>

                    <strong>💬 دعم 24/7:</strong> فريق الدعم المخصص لدينا موجود دائماً لمساعدتك على النجاح،
                    مع دعم متعدد اللغات وتوثيق شامل.<br><br>

                    <strong>🔐 أمان المؤسسات:</strong> التشفير على مستوى البنوك والامتثال لـ SOC 2 وعمليات التدقيق الأمني المنتظمة
                    تحافظ على أمان بياناتك.
                </p>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h2 class="content-en">Ready to Transform Your Social Media?</h2>
                <h2 class="content-ar">هل أنت مستعد لتحويل وسائل التواصل الاجتماعي الخاصة بك؟</h2>

                <p class="content-en">
                    Join 100,000+ creators and businesses who trust Media Pro to manage their social media presence.
                </p>
                <p class="content-ar">
                    انضم إلى أكثر من 100,000 مبدع وشركة يثقون في ميديا برو لإدارة تواجدهم على وسائل التواصل الاجتماعي.
                </p>

                <div class="cta-buttons">
                    <a href="/register" class="cta-button cta-button-primary content-en">Start Free Trial</a>
                    <a href="/register" class="cta-button cta-button-primary content-ar">ابدأ التجربة المجانية</a>

                    <a href="/contact" class="cta-button cta-button-secondary content-en">Contact Sales</a>
                    <a href="/contact" class="cta-button cta-button-secondary content-ar">اتصل بالمبيعات</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Language Toggle Functionality
    const savedLang = localStorage.getItem('preferredLanguage') || 'ar';
    document.documentElement.setAttribute('lang', savedLang);
    document.documentElement.setAttribute('dir', savedLang === 'ar' ? 'rtl' : 'ltr');

    // Show content based on language
    const contentEn = document.querySelectorAll('.content-en');
    const contentAr = document.querySelectorAll('.content-ar');

    if (savedLang === 'ar') {
        contentEn.forEach(el => el.style.display = 'none');
        contentAr.forEach(el => el.style.display = 'block');
    } else {
        contentEn.forEach(el => el.style.display = 'block');
        contentAr.forEach(el => el.style.display = 'none');
    }
</script>
@endsection
