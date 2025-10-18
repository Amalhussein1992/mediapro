<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Media Pro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(10, 15, 30, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            padding: 1rem 2rem;
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

        main {
            padding-top: 100px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
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

        .page-title {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #FFF, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            letter-spacing: -2px;
            position: relative;
            z-index: 1;
        }

        .page-subtitle {
            font-size: 1.3rem;
            color: var(--text-gray);
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .content-section {
            padding: 60px 20px;
            background: var(--dark-secondary);
        }

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

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .page-subtitle {
                font-size: 1.1rem;
            }

            .nav-container {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>

    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @yield('styles')
    @yield('meta')
</head>
<body data-lang="en">
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('logo.jpeg') }}" alt="Media Pro Logo">
                <div class="logo-text content-en">Media Pro</div>
                <div class="logo-text content-ar">ميديا برو</div>
            </a>

            <div class="lang-switcher">
                <button class="lang-btn active" onclick="switchLang('en')">English</button>
                <button class="lang-btn" onclick="switchLang('ar')">العربية</button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="content-en">Product</h3>
                <h3 class="content-ar">المنتج</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('features') }}" class="content-en">Features</a><a href="{{ route('features') }}" class="content-ar">الميزات</a></li>
                    <li><a href="{{ route('pricing') }}" class="content-en">Pricing</a><a href="{{ route('pricing') }}" class="content-ar">الأسعار</a></li>
                    <li><a href="{{ route('api') }}" class="content-en">API</a><a href="{{ route('api') }}" class="content-ar">واجهة برمجية</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="content-en">Company</h3>
                <h3 class="content-ar">الشركة</h3>
                <ul class="footer-links">
                    <li><a href="/about" class="content-en">About</a><a href="/about" class="content-ar">من نحن</a></li>
                    <li><a href="/blog" class="content-en">Blog</a><a href="/blog" class="content-ar">المدونة</a></li>
                    <li><a href="/careers" class="content-en">Careers</a><a href="/careers" class="content-ar">الوظائف</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="content-en">Resources</h3>
                <h3 class="content-ar">الموارد</h3>
                <ul class="footer-links">
                    <li><a href="/help" class="content-en">Help Center</a><a href="/help" class="content-ar">مركز المساعدة</a></li>
                    <li><a href="/community" class="content-en">Community</a><a href="/community" class="content-ar">المجتمع</a></li>
                    <li><a href="/contact" class="content-en">Contact</a><a href="/contact" class="content-ar">اتصل بنا</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="content-en">Legal</h3>
                <h3 class="content-ar">قانوني</h3>
                <ul class="footer-links">
                    <li><a href="/privacy" class="content-en">Privacy</a><a href="/privacy" class="content-ar">الخصوصية</a></li>
                    <li><a href="/terms" class="content-en">Terms</a><a href="/terms" class="content-ar">الشروط</a></li>
                    <li><a href="/security" class="content-en">Security</a><a href="/security" class="content-ar">الأمان</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="content-en">&copy; 2025 Media Pro. All rights reserved. Built with ❤️ for creators.</p>
            <p class="content-ar">&copy; 2025 ميديا برو. جميع الحقوق محفوظة. صنع بـ ❤️ للمبدعين.</p>
        </div>
    </footer>

    <script>
        // Load saved language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('mediapro_lang') || 'en';
            applyLanguage(savedLang);
        });

        function switchLang(lang) {
            // Save language preference
            localStorage.setItem('mediapro_lang', lang);
            applyLanguage(lang);
        }

        function applyLanguage(lang) {
            const html = document.documentElement;
            const body = document.body;

            html.setAttribute('lang', lang === 'ar' ? 'ar' : 'en');
            html.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
            body.setAttribute('data-lang', lang);

            // Update active button
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
                if ((lang === 'ar' && btn.textContent.includes('العربية')) ||
                    (lang === 'en' && btn.textContent.includes('English'))) {
                    btn.classList.add('active');
                }
            });
        }
    </script>

    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>

    @yield('scripts')
</body>
</html>
