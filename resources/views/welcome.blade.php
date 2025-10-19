<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Manager - مدير وسائل التواصل الاجتماعي</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #6366F1;
            --primary-purple: #A855F7;
            --accent-pink: #EC4899;
            --accent-cyan: #06B6D4;
            --dark-bg: #0A0F1E;
            --dark-secondary: #151B2E;
            --dark-card: #1A2235;
            --text-light: #F8FAFC;
            --text-gray: #94A3B8;
            --text-muted: #64748B;
        }

        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            background: var(--dark-bg);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
        }

        [dir="rtl"] {
            font-family: 'Cairo', 'Inter', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            border-radius: 10px;
        }

        /* Navigation Bar */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(10, 15, 30, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            padding: 1rem 2rem;
            transition: all 0.3s ease;
        }

        nav.scrolled {
            padding: 0.75rem 2rem;
            background: rgba(10, 15, 30, 0.95);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .lang-switcher {
            display: flex;
            gap: 8px;
            background: var(--dark-secondary);
            padding: 6px;
            border-radius: 10px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .lang-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: var(--text-gray);
            cursor: pointer;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .lang-btn.active {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .lang-btn:hover:not(.active) {
            color: white;
            background: rgba(99, 102, 241, 0.1);
        }

        .content-en, .content-ar {
            display: none;
        }

        [data-lang="en"] .content-en {
            display: block;
        }

        [data-lang="ar"] .content-ar {
            display: block;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 150px 20px 100px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            z-index: 0;
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.6;
            animation: float 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            top: -200px;
            right: -200px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--accent-pink), var(--accent-cyan));
            bottom: -150px;
            left: -150px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--primary-purple));
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(100px, -50px) scale(1.1); }
            50% { transform: translate(-50px, 100px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 1000px;
        }

        .hero-badge {
            display: inline-block;
            padding: 10px 24px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50px;
            color: var(--primary-blue);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease;
        }

        h1 {
            font-size: 5rem;
            font-weight: 900;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #FFF, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            letter-spacing: -2px;
            animation: fadeInUp 1s ease;
        }

        .hero-gradient-text {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1.4rem;
            color: var(--text-gray);
            margin-bottom: 50px;
            font-weight: 400;
            line-height: 1.8;
            animation: fadeInUp 1.2s ease;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.4s ease;
        }

        .cta-button {
            padding: 18px 45px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.4);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(99, 102, 241, 0.6);
        }

        .cta-secondary {
            background: transparent;
            border: 2px solid rgba(99, 102, 241, 0.5);
            box-shadow: none;
        }

        .cta-secondary:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: var(--primary-blue);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dashboard Preview */
        .dashboard-preview {
            margin-top: 80px;
            position: relative;
            animation: fadeInUp 1.6s ease;
        }

        .dashboard-img {
            width: 100%;
            max-width: 1100px;
            border-radius: 20px;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(99, 102, 241, 0.2);
            background: linear-gradient(135deg, var(--dark-secondary), var(--dark-card));
            padding: 20px;
        }

        /* Features Section */
        .features {
            padding: 120px 20px;
            background: linear-gradient(180deg, var(--dark-bg) 0%, var(--dark-secondary) 100%);
            position: relative;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(168, 85, 247, 0.1);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 50px;
            color: var(--primary-purple);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .section-desc {
            font-size: 1.3rem;
            color: var(--text-gray);
            max-width: 700px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--dark-card);
            padding: 45px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-purple), var(--accent-pink));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-purple);
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.3);
            background: linear-gradient(135deg, var(--dark-card), rgba(99, 102, 241, 0.05));
        }

        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 25px;
            display: inline-block;
            filter: drop-shadow(0 5px 15px rgba(99, 102, 241, 0.4));
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

        /* Stats Section */
        .stats {
            padding: 120px 20px;
            background: var(--dark-bg);
            position: relative;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            text-align: center;
        }

        .stat-item {
            padding: 40px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(168, 85, 247, 0.05));
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: scale(1.05);
            border-color: var(--primary-purple);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.2);
        }

        .stat-number {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            letter-spacing: -2px;
        }

        .stat-label {
            color: var(--text-gray);
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 120px 20px;
            background: linear-gradient(180deg, var(--dark-bg) 0%, var(--dark-secondary) 100%);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .testimonial-card {
            background: var(--dark-card);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.15);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.2);
        }

        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-gray);
            margin-bottom: 25px;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .author-info h4 {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 3px;
        }

        .author-info p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 120px 20px;
            background: var(--dark-bg);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            border-radius: 50%;
            filter: blur(150px);
            opacity: 0.2;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 25px;
            letter-spacing: -1px;
        }

        .cta-desc {
            font-size: 1.3rem;
            color: var(--text-gray);
            margin-bottom: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        footer {
            padding: 60px 20px 30px;
            background: var(--dark-secondary);
            border-top: 1px solid rgba(99, 102, 241, 0.1);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: var(--text-gray);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-blue);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(99, 102, 241, 0.1);
            color: var(--text-muted);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
                letter-spacing: -1px;
            }

            .subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .features-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .cta-button {
                width: 100%;
            }

            .nav-container {
                flex-direction: column;
                gap: 15px;
            }

            .orb-1, .orb-2, .orb-3 {
                display: none;
            }
        }
    </style>
</head>
<body data-lang="en">
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="{{ asset('logo.jpeg') }}" alt="SocialHub Logo">
                <div class="logo-text content-en">SocialHub</div>
                <div class="logo-text content-ar">سوشيال هب</div>
            </div>

            <div class="lang-switcher">
                <button class="lang-btn active" onclick="switchLang('en')">English</button>
                <button class="lang-btn" onclick="switchLang('ar')">العربية</button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
        </div>

        <div class="hero-content">
            <div class="hero-badge content-en">🚀 The Future of Social Media Management</div>
            <div class="hero-badge content-ar">🚀 مستقبل إدارة وسائل التواصل الاجتماعي</div>

            <h1 class="content-en">
                Manage <span class="hero-gradient-text">All Your Socials</span><br>In One Place
            </h1>
            <h1 class="content-ar">
                إدارة <span class="hero-gradient-text">جميع حساباتك</span><br>في مكان واحد
            </h1>

            <p class="subtitle content-en">
                Streamline your social media workflow with AI-powered tools,<br>
                analytics, and scheduling - all in one powerful platform.
            </p>
            <p class="subtitle content-ar">
                بسّط سير عمل وسائل التواصل الاجتماعي باستخدام أدوات الذكاء الاصطناعي<br>
                والتحليلات والجدولة - كل ذلك في منصة واحدة قوية
            </p>

            <div class="hero-buttons">
                <a href="#features" class="cta-button content-en">Start Free Trial →</a>
                <a href="#features" class="cta-button content-ar">ابدأ النسخة التجريبية المجانية ←</a>

                <a href="#demo" class="cta-button cta-secondary content-en">Watch Demo</a>
                <a href="#demo" class="cta-button cta-secondary content-ar">شاهد العرض التوضيحي</a>
            </div>

            <div class="dashboard-preview">
                <div class="dashboard-img">
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #64748B;">
                        📊 Dashboard Preview
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <div class="section-badge content-en">✨ Features</div>
                <div class="section-badge content-ar">✨ الميزات</div>

                <h2 class="section-title content-en">Everything You Need to Succeed</h2>
                <h2 class="section-title content-ar">كل ما تحتاجه لتحقيق النجاح</h2>

                <p class="section-desc content-en">
                    Powerful tools designed to help you grow your social media presence
                </p>
                <p class="section-desc content-ar">
                    أدوات قوية مصممة لمساعدتك على تنمية تواجدك على وسائل التواصل
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title content-en">Advanced Analytics</h3>
                    <h3 class="feature-title content-ar">تحليلات متقدمة</h3>
                    <p class="feature-desc content-en">Track performance with real-time insights and detailed reports across all platforms</p>
                    <p class="feature-desc content-ar">تتبع الأداء بتقارير مفصلة ورؤى فورية عبر جميع المنصات</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⏰</div>
                    <h3 class="feature-title content-en">Smart Scheduling</h3>
                    <h3 class="feature-title content-ar">جدولة ذكية</h3>
                    <p class="feature-desc content-en">Schedule posts at optimal times with AI-powered recommendations</p>
                    <p class="feature-desc content-ar">جدولة المنشورات في الأوقات المثالية بتوصيات الذكاء الاصطناعي</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3 class="feature-title content-en">AI Content Generator</h3>
                    <h3 class="feature-title content-ar">مولد المحتوى بالذكاء الاصطناعي</h3>
                    <p class="feature-desc content-en">Create engaging content with our advanced AI writing assistant</p>
                    <p class="feature-desc content-ar">إنشاء محتوى جذاب مع مساعد الكتابة بالذكاء الاصطناعي المتقدم</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔗</div>
                    <h3 class="feature-title content-en">Multi-Platform Support</h3>
                    <h3 class="feature-title content-ar">دعم متعدد المنصات</h3>
                    <p class="feature-desc content-en">Connect all major social networks: Instagram, Facebook, Twitter, LinkedIn & more</p>
                    <p class="feature-desc content-ar">ربط جميع الشبكات الاجتماعية الرئيسية: إنستغرام، فيسبوك، تويتر، لينكدإن والمزيد</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3 class="feature-title content-en">Brand Management</h3>
                    <h3 class="feature-title content-ar">إدارة العلامة التجارية</h3>
                    <p class="feature-desc content-en">Maintain consistent branding with custom templates and assets library</p>
                    <p class="feature-desc content-ar">الحفاظ على علامة تجارية متسقة مع قوالب مخصصة ومكتبة الأصول</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3 class="feature-title content-en">Team Collaboration</h3>
                    <h3 class="feature-title content-ar">تعاون الفريق</h3>
                    <p class="feature-desc content-en">Work seamlessly with your team with roles, permissions, and approval workflows</p>
                    <p class="feature-desc content-ar">العمل بسلاسة مع فريقك بأدوار وأذونات وسير عمل الموافقة</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="section-header">
                <div class="section-badge content-en">🏆 Trusted Worldwide</div>
                <div class="section-badge content-ar">🏆 موثوق به عالمياً</div>

                <h2 class="section-title content-en">Join Thousands of Happy Users</h2>
                <h2 class="section-title content-ar">انضم إلى آلاف المستخدمين السعداء</h2>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label content-en">Active Users</div>
                    <div class="stat-label content-ar">مستخدم نشط</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5M+</div>
                    <div class="stat-label content-en">Posts Scheduled</div>
                    <div class="stat-label content-ar">منشور مجدول</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label content-en">Platforms</div>
                    <div class="stat-label content-ar">منصة</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label content-en">Uptime</div>
                    <div class="stat-label content-ar">وقت التشغيل</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <div class="section-badge content-en">💬 Testimonials</div>
                <div class="section-badge content-ar">💬 آراء العملاء</div>

                <h2 class="section-title content-en">What Our Users Say</h2>
                <h2 class="section-title content-ar">ماذا يقول مستخدمونا</h2>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text content-en">"This platform has completely transformed how we manage our social media. The AI features are incredible!"</p>
                    <p class="testimonial-text content-ar">"لقد غيرت هذه المنصة طريقة إدارتنا لوسائل التواصل الاجتماعي بالكامل. ميزات الذكاء الاصطناعي رائعة!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">SM</div>
                        <div class="author-info">
                            <h4 class="content-en">Sarah Miller</h4>
                            <h4 class="content-ar">سارة ميلر</h4>
                            <p class="content-en">Marketing Director</p>
                            <p class="content-ar">مديرة التسويق</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text content-en">"The analytics are game-changing. We've seen a 300% increase in engagement since switching."</p>
                    <p class="testimonial-text content-ar">"التحليلات غيرت قواعد اللعبة. لقد شهدنا زيادة بنسبة 300% في التفاعل منذ التبديل."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">JD</div>
                        <div class="author-info">
                            <h4 class="content-en">John Davis</h4>
                            <h4 class="content-ar">جون ديفيس</h4>
                            <p class="content-en">Content Creator</p>
                            <p class="content-ar">منشئ محتوى</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text content-en">"Best investment for our agency. Saves us 20+ hours per week on social media management."</p>
                    <p class="testimonial-text content-ar">"أفضل استثمار لوكالتنا. يوفر لنا أكثر من 20 ساعة أسبوعياً في إدارة وسائل التواصل."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">EL</div>
                        <div class="author-info">
                            <h4 class="content-en">Emily Lee</h4>
                            <h4 class="content-ar">إيميلي لي</h4>
                            <p class="content-en">Agency Owner</p>
                            <p class="content-ar">صاحبة وكالة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <div class="container">
                <h2 class="cta-title content-en">Ready to Transform Your Social Media?</h2>
                <h2 class="cta-title content-ar">هل أنت مستعد لتحويل وسائل التواصل الخاصة بك؟</h2>

                <p class="cta-desc content-en">Join thousands of successful brands and creators. Start your free 14-day trial today.</p>
                <p class="cta-desc content-ar">انضم إلى آلاف العلامات التجارية والمبدعين الناجحين. ابدأ تجربتك المجانية لمدة 14 يوماً اليوم.</p>

                <a href="#" class="cta-button content-en">Start Free Trial - No Credit Card Required</a>
                <a href="#" class="cta-button content-ar">ابدأ النسخة التجريبية المجانية - لا حاجة لبطاقة ائتمان</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="content-en">Product</h3>
                <h3 class="content-ar">المنتج</h3>
                <ul class="footer-links">
                    <li><a href="#" class="content-en">Features</a><a href="#" class="content-ar">الميزات</a></li>
                    <li><a href="#" class="content-en">Pricing</a><a href="#" class="content-ar">الأسعار</a></li>
                    <li><a href="#" class="content-en">API</a><a href="#" class="content-ar">واجهة برمجية</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="content-en">Company</h3>
                <h3 class="content-ar">الشركة</h3>
                <ul class="footer-links">
                    <li><a href="#" class="content-en">About</a><a href="#" class="content-ar">من نحن</a></li>
                    <li><a href="#" class="content-en">Blog</a><a href="#" class="content-ar">المدونة</a></li>
                    <li><a href="#" class="content-en">Careers</a><a href="#" class="content-ar">الوظائف</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="content-en">Resources</h3>
                <h3 class="content-ar">الموارد</h3>
                <ul class="footer-links">
                    <li><a href="#" class="content-en">Help Center</a><a href="#" class="content-ar">مركز المساعدة</a></li>
                    <li><a href="#" class="content-en">Community</a><a href="#" class="content-ar">المجتمع</a></li>
                    <li><a href="#" class="content-en">Contact</a><a href="#" class="content-ar">اتصل بنا</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="content-en">Legal</h3>
                <h3 class="content-ar">قانوني</h3>
                <ul class="footer-links">
                    <li><a href="#" class="content-en">Privacy</a><a href="#" class="content-ar">الخصوصية</a></li>
                    <li><a href="#" class="content-en">Terms</a><a href="#" class="content-ar">الشروط</a></li>
                    <li><a href="#" class="content-en">Security</a><a href="#" class="content-ar">الأمان</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="content-en">&copy; 2025 SocialHub. All rights reserved. Built with ❤️ for creators.</p>
            <p class="content-ar">&copy; 2025 سوشيال هب. جميع الحقوق محفوظة. صنع بـ ❤️ للمبدعين.</p>
        </div>
    </footer>

    <script>
        // Language Switcher
        function switchLang(lang) {
            const html = document.documentElement;
            const body = document.body;

            html.setAttribute('lang', lang === 'ar' ? 'ar' : 'en');
            html.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
            body.setAttribute('data-lang', lang);

            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar Scroll Effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Intersection Observer for Animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.feature-card, .stat-item, .testimonial-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>
