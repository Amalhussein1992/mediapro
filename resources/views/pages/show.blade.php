@extends('layouts.public')

@section('title', $page->localized_title)

@section('meta')
<meta name="description" content="{{ $page->localized_meta_description ?? '' }}">
<meta name="keywords" content="{{ $page->meta_keywords ?? '' }}">
@endsection

@section('content')
<!-- Hero Section with Animation -->
<div class="page-hero">
    <div class="hero-background">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>
    <div class="container">
        <div class="hero-content">
            <div class="breadcrumb content-en">
                <a href="/">Home</a>
                <span class="separator">→</span>
                <span class="current">{{ $page->title }}</span>
            </div>
            <div class="breadcrumb content-ar">
                <a href="/">الرئيسية</a>
                <span class="separator">←</span>
                <span class="current">{{ $page->title_ar ?? $page->title }}</span>
            </div>

            <h1 class="page-title content-en" data-aos="fade-up">{{ $page->title }}</h1>
            <h1 class="page-title content-ar" data-aos="fade-up">{{ $page->title_ar ?? $page->title }}</h1>

            @if($page->meta_description)
            <p class="page-subtitle content-en" data-aos="fade-up" data-aos-delay="100">
                {{ $page->meta_description }}
            </p>
            @endif
            @if($page->meta_description_ar)
            <p class="page-subtitle content-ar" data-aos="fade-up" data-aos-delay="100">
                {{ $page->meta_description_ar }}
            </p>
            @endif
        </div>
    </div>
</div>

<!-- Main Content Section -->
<section class="content-section">
    <div class="container">
        <div class="content-wrapper">
            <!-- Sidebar Navigation (if applicable) -->
            <aside class="content-sidebar" data-aos="fade-right">
                <div class="sidebar-card">
                    <h3 class="sidebar-title content-en">Quick Navigation</h3>
                    <h3 class="sidebar-title content-ar">التنقل السريع</h3>
                    <ul class="sidebar-menu" id="sidebarMenu">
                        <!-- Will be populated by JavaScript -->
                    </ul>
                </div>

                <div class="sidebar-card cta-card">
                    <div class="cta-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="content-en">Get Started Today</h4>
                    <h4 class="content-ar">ابدأ اليوم</h4>
                    <p class="content-en">Join thousands of creators managing their social media with ease.</p>
                    <p class="content-ar">انضم إلى آلاف المبدعين الذين يديرون وسائل التواصل الاجتماعي بسهولة.</p>
                    <a href="/pricing" class="cta-button">
                        <span class="content-en">View Plans</span>
                        <span class="content-ar">عرض الخطط</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <article class="page-content content-en" data-aos="fade-up">
                    {!! $page->content !!}
                </article>
                <article class="page-content content-ar" data-aos="fade-up">
                    {!! $page->content_ar ?? $page->content !!}
                </article>

                <!-- Share Section -->
                <div class="share-section" data-aos="fade-up">
                    <h4 class="content-en">Share this page</h4>
                    <h4 class="content-ar">شارك هذه الصفحة</h4>
                    <div class="share-buttons">
                        <button class="share-btn twitter" onclick="shareOn('twitter')">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </button>
                        <button class="share-btn facebook" onclick="shareOn('facebook')">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </button>
                        <button class="share-btn linkedin" onclick="shareOn('linkedin')">
                            <i class="fab fa-linkedin-in"></i>
                            <span>LinkedIn</span>
                        </button>
                        <button class="share-btn copy" onclick="copyLink()">
                            <i class="fas fa-link"></i>
                            <span class="content-en">Copy Link</span>
                            <span class="content-ar">نسخ الرابط</span>
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>

<style>
    /* Hero Section */
    .page-hero {
        position: relative;
        padding: 120px 0 80px;
        overflow: hidden;
        background: linear-gradient(135deg,
            rgba(15, 23, 42, 0.95) 0%,
            rgba(30, 41, 59, 0.95) 50%,
            rgba(15, 23, 42, 0.95) 100%
        );
    }

    .hero-background {
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
        filter: blur(80px);
        opacity: 0.3;
        animation: float 20s infinite ease-in-out;
    }

    .orb-1 {
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, var(--primary-blue), transparent);
        top: -250px;
        left: -100px;
        animation-delay: 0s;
    }

    .orb-2 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, var(--primary-purple), transparent);
        top: 100px;
        right: -150px;
        animation-delay: 7s;
    }

    .orb-3 {
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, #3b82f6, transparent);
        bottom: -100px;
        left: 50%;
        animation-delay: 14s;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-30px, 30px) scale(0.9); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
        font-size: 0.95rem;
        color: var(--text-gray);
    }

    .breadcrumb a {
        color: var(--primary-blue);
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb a:hover {
        color: var(--primary-purple);
    }

    .breadcrumb .separator {
        color: rgba(99, 102, 241, 0.5);
    }

    .breadcrumb .current {
        color: var(--text-light);
        font-weight: 600;
    }

    .page-title {
        font-size: 3.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-align: center;
        margin: 0 0 20px 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .page-subtitle {
        font-size: 1.25rem;
        color: var(--text-gray);
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Content Section */
    .content-section {
        padding: 80px 0;
        background: var(--dark-bg);
    }

    .content-wrapper {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 40px;
        align-items: start;
    }

    /* Sidebar */
    .content-sidebar {
        position: sticky;
        top: 100px;
    }

    .sidebar-card {
        background: var(--dark-card);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid rgba(99, 102, 241, 0.15);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .sidebar-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 8px 30px rgba(99, 102, 241, 0.1);
    }

    .sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-light);
        margin: 0 0 16px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(99, 102, 241, 0.2);
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu li {
        margin-bottom: 8px;
    }

    .sidebar-menu a {
        display: block;
        padding: 10px 12px;
        color: var(--text-gray);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .sidebar-menu a:hover {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary-blue);
        padding-left: 16px;
    }

    .sidebar-menu a.active {
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        color: white;
    }

    /* CTA Card */
    .cta-card {
        background: linear-gradient(135deg,
            rgba(99, 102, 241, 0.1),
            rgba(139, 92, 246, 0.1)
        );
        border-color: rgba(99, 102, 241, 0.3);
        text-align: center;
    }

    .cta-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.8rem;
        color: white;
    }

    .cta-card h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-light);
        margin: 0 0 12px 0;
    }

    .cta-card p {
        font-size: 0.9rem;
        color: var(--text-gray);
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .cta-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }

    /* Main Content */
    .main-content {
        min-width: 0;
    }

    .page-content {
        padding: 50px;
        background: var(--dark-card);
        border-radius: 20px;
        border: 1px solid rgba(99, 102, 241, 0.15);
        line-height: 1.8;
        color: var(--text-gray);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .page-content h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-light);
        margin-top: 3rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        padding-bottom: 1rem;
    }

    .page-content h1::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-purple));
        border-radius: 2px;
    }

    .page-content h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-light);
        margin-top: 3rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(99, 102, 241, 0.2);
        position: relative;
        padding-left: 20px;
    }

    .page-content h2::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--primary-blue), var(--primary-purple));
        border-radius: 2px;
    }

    .page-content h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-blue);
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-content h3::before {
        content: '▸';
        color: var(--primary-purple);
        font-size: 1.2rem;
    }

    .page-content p {
        margin-bottom: 1.5rem;
        font-size: 1.125rem;
        color: var(--text-gray);
        line-height: 1.9;
        text-align: justify;
    }

    .page-content p:first-of-type {
        font-size: 1.25rem;
        color: var(--text-light);
        font-weight: 500;
        line-height: 2;
    }

    /* Enhanced Lists */
    .page-content ul, .page-content ol {
        margin-bottom: 2rem;
        padding-left: 0;
        list-style: none;
    }

    .page-content ul li, .page-content ol li {
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: var(--text-gray);
        padding-left: 2.5rem;
        position: relative;
        line-height: 1.8;
        transition: all 0.3s;
    }

    .page-content ul li:hover, .page-content ol li:hover {
        color: var(--text-light);
        transform: translateX(5px);
    }

    .page-content ul li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.6rem;
        width: 12px;
        height: 12px;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
    }

    .page-content ol {
        counter-reset: custom-counter;
    }

    .page-content ol li::before {
        counter-increment: custom-counter;
        content: counter(custom-counter);
        position: absolute;
        left: 0;
        top: 0;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }

    /* Links */
    .page-content a {
        color: var(--primary-blue);
        text-decoration: none;
        position: relative;
        font-weight: 600;
        transition: all 0.3s ease;
        border-bottom: 2px solid rgba(99, 102, 241, 0.3);
        padding-bottom: 2px;
    }

    .page-content a:hover {
        color: var(--primary-purple);
        border-bottom-color: var(--primary-purple);
    }

    .page-content a::after {
        content: '→';
        margin-left: 5px;
        opacity: 0;
        transition: all 0.3s;
        display: inline-block;
    }

    .page-content a:hover::after {
        opacity: 1;
        transform: translateX(3px);
    }

    /* Strong Text */
    .page-content strong {
        color: var(--text-light);
        font-weight: 700;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* Code */
    .page-content code {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.15));
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-family: 'Courier New', 'Consolas', monospace;
        color: var(--primary-blue);
        font-size: 0.95rem;
        border: 1px solid rgba(99, 102, 241, 0.2);
        font-weight: 600;
    }

    /* Blockquote */
    .page-content blockquote {
        border-left: 4px solid var(--primary-blue);
        padding: 1.5rem 1.5rem 1.5rem 2rem;
        margin: 2.5rem 0;
        font-style: italic;
        color: var(--text-light);
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.1), transparent);
        border-radius: 0 12px 12px 0;
        position: relative;
    }

    .page-content blockquote::before {
        content: '"';
        font-size: 4rem;
        color: var(--primary-blue);
        opacity: 0.3;
        position: absolute;
        top: -10px;
        left: 10px;
        font-family: Georgia, serif;
    }

    /* Tables */
    .page-content table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 2.5rem 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .page-content th, .page-content td {
        padding: 1.2rem 1.5rem;
        text-align: left;
        border-bottom: 1px solid rgba(99, 102, 241, 0.1);
    }

    .page-content th {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
        color: var(--text-light);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }

    .page-content td {
        color: var(--text-gray);
        background: var(--dark-card);
    }

    .page-content tr:hover td {
        background: rgba(99, 102, 241, 0.05);
        color: var(--text-light);
    }

    .page-content tr:last-child td {
        border-bottom: none;
    }

    /* Images */
    .page-content img {
        max-width: 100%;
        height: auto;
        border-radius: 16px;
        margin: 2.5rem 0;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
    }

    .page-content img:hover {
        transform: scale(1.02);
        box-shadow: 0 12px 40px rgba(99, 102, 241, 0.3);
    }

    /* Horizontal Rule */
    .page-content hr {
        border: none;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--primary-blue), var(--primary-purple), transparent);
        margin: 3rem 0;
        border-radius: 2px;
    }

    /* Share Section */
    .share-section {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 2px solid rgba(99, 102, 241, 0.15);
        text-align: center;
    }

    .share-section h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-light);
        margin-bottom: 20px;
    }

    .share-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .share-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border: 2px solid rgba(99, 102, 241, 0.2);
        background: transparent;
        color: var(--text-gray);
        border-radius: 12px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .share-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
    }

    .share-btn.twitter:hover {
        background: #1DA1F2;
        border-color: #1DA1F2;
        color: white;
    }

    .share-btn.facebook:hover {
        background: #4267B2;
        border-color: #4267B2;
        color: white;
    }

    .share-btn.linkedin:hover {
        background: #0077B5;
        border-color: #0077B5;
        color: white;
    }

    .share-btn.copy:hover {
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
        border-color: var(--primary-blue);
        color: white;
    }

    /* RTL Support for Arabic */
    html[dir="rtl"] .page-content {
        text-align: right;
        direction: rtl;
    }

    html[dir="rtl"] .page-content ul li,
    html[dir="rtl"] .page-content ol li {
        padding-left: 0;
        padding-right: 2.5rem;
    }

    html[dir="rtl"] .page-content ul li::before {
        left: auto;
        right: 0;
    }

    html[dir="rtl"] .page-content ol li::before {
        left: auto;
        right: 0;
    }

    html[dir="rtl"] .page-content ul li:hover,
    html[dir="rtl"] .page-content ol li:hover {
        transform: translateX(-5px);
    }

    html[dir="rtl"] .page-content h1::after {
        left: auto;
        right: 0;
    }

    html[dir="rtl"] .page-content h2 {
        padding-left: 0;
        padding-right: 20px;
    }

    html[dir="rtl"] .page-content h2::before {
        left: auto;
        right: 0;
    }

    html[dir="rtl"] .page-content h3::before {
        content: '◂';
    }

    html[dir="rtl"] .page-content a::after {
        content: '←';
        margin-left: 0;
        margin-right: 5px;
    }

    html[dir="rtl"] .page-content a:hover::after {
        transform: translateX(-3px);
    }

    html[dir="rtl"] .page-content blockquote {
        border-left: none;
        border-right: 4px solid var(--primary-blue);
        padding-left: 1.5rem;
        padding-right: 2rem;
        border-radius: 12px 0 0 12px;
        background: linear-gradient(270deg, rgba(99, 102, 241, 0.1), transparent);
    }

    html[dir="rtl"] .page-content blockquote::before {
        left: auto;
        right: 10px;
    }

    html[dir="rtl"] .breadcrumb {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .sidebar-menu a:hover {
        padding-left: 12px;
        padding-right: 16px;
    }

    html[dir="rtl"] .cta-button i {
        transform: scaleX(-1);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .content-wrapper {
            grid-template-columns: 1fr;
        }

        .content-sidebar {
            position: static;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .page-hero {
            padding: 80px 0 50px;
        }

        .page-title {
            font-size: 2.5rem;
            line-height: 1.3;
        }

        .page-subtitle {
            font-size: 1.05rem;
        }

        .content-sidebar {
            grid-template-columns: 1fr;
        }

        .page-content {
            padding: 30px 20px;
        }

        .page-content h1 {
            font-size: 2rem;
            margin-top: 2rem;
        }

        .page-content h2 {
            font-size: 1.6rem;
            margin-top: 2rem;
            padding-left: 15px;
        }

        html[dir="rtl"] .page-content h2 {
            padding-left: 0;
            padding-right: 15px;
        }

        .page-content h3 {
            font-size: 1.3rem;
        }

        .page-content p {
            font-size: 1.05rem;
            text-align: left;
        }

        html[dir="rtl"] .page-content p {
            text-align: right;
        }

        .page-content ul li,
        .page-content ol li {
            font-size: 1rem;
            padding-left: 2rem;
        }

        html[dir="rtl"] .page-content ul li,
        html[dir="rtl"] .page-content ol li {
            padding-left: 0;
            padding-right: 2rem;
        }

        .page-content ol li::before {
            width: 24px;
            height: 24px;
            font-size: 0.8rem;
        }

        .page-content table {
            font-size: 0.9rem;
        }

        .page-content th,
        .page-content td {
            padding: 0.8rem 1rem;
        }

        .share-section {
            margin-top: 40px;
        }

        .share-buttons {
            flex-direction: column;
        }

        .share-btn {
            width: 100%;
            justify-content: center;
        }

        .gradient-orb {
            filter: blur(60px);
        }

        .orb-1 {
            width: 350px;
            height: 350px;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
        }

        .orb-3 {
            width: 250px;
            height: 250px;
        }
    }

    @media (max-width: 480px) {
        .page-hero {
            padding: 60px 0 40px;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .page-subtitle {
            font-size: 1rem;
        }

        .breadcrumb {
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .page-content {
            padding: 25px 15px;
        }

        .page-content h1 {
            font-size: 1.6rem;
        }

        .page-content h2 {
            font-size: 1.4rem;
            padding-left: 12px;
        }

        html[dir="rtl"] .page-content h2 {
            padding-left: 0;
            padding-right: 12px;
        }

        .page-content h2::before {
            width: 3px;
        }

        .page-content h3 {
            font-size: 1.2rem;
        }

        .page-content p {
            font-size: 1rem;
        }

        .page-content ul li,
        .page-content ol li {
            font-size: 0.95rem;
            padding-left: 1.8rem;
        }

        html[dir="rtl"] .page-content ul li,
        html[dir="rtl"] .page-content ol li {
            padding-left: 0;
            padding-right: 1.8rem;
        }

        .sidebar-card {
            padding: 20px;
        }

        .cta-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }
    }
</style>

<script>
    // Auto-generate sidebar navigation from headings
    document.addEventListener('DOMContentLoaded', function() {
        const content = document.querySelector('.page-content:not([style*="display: none"])');
        const sidebarMenu = document.getElementById('sidebarMenu');

        if (content && sidebarMenu) {
            const headings = content.querySelectorAll('h2, h3');

            if (headings.length > 0) {
                headings.forEach((heading, index) => {
                    const id = 'heading-' + index;
                    heading.id = id;

                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.href = '#' + id;
                    a.textContent = heading.textContent;
                    a.onclick = function(e) {
                        e.preventDefault();
                        heading.scrollIntoView({ behavior: 'smooth', block: 'start' });

                        // Update active state
                        document.querySelectorAll('.sidebar-menu a').forEach(link => {
                            link.classList.remove('active');
                        });
                        a.classList.add('active');
                    };

                    if (heading.tagName === 'H3') {
                        a.style.paddingLeft = '24px';
                        a.style.fontSize = '0.9rem';
                    }

                    li.appendChild(a);
                    sidebarMenu.appendChild(li);
                });
            } else {
                // Hide sidebar if no headings
                document.querySelector('.content-sidebar').style.display = 'none';
                document.querySelector('.content-wrapper').style.gridTemplateColumns = '1fr';
            }
        }

        // Highlight active section on scroll
        let observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    document.querySelectorAll('.sidebar-menu a').forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, { rootMargin: '-100px 0px -80% 0px' });

        document.querySelectorAll('.page-content h2, .page-content h3').forEach(heading => {
            observer.observe(heading);
        });
    });

    // Share functions
    function shareOn(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);

        let shareUrl;
        switch(platform) {
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                break;
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'linkedin':
                shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                break;
        }

        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }

    function copyLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const btn = event.target.closest('.share-btn');
            const originalText = btn.querySelector('span').textContent;
            const lang = document.documentElement.getAttribute('lang') || 'en';

            btn.querySelector('span').textContent = lang === 'ar' ? 'تم النسخ!' : 'Copied!';
            btn.style.background = 'linear-gradient(135deg, var(--primary-blue), var(--primary-purple))';
            btn.style.color = 'white';

            setTimeout(() => {
                btn.querySelector('span').textContent = originalText;
                btn.style.background = 'transparent';
                btn.style.color = 'var(--text-gray)';
            }, 2000);
        });
    }
</script>
@endsection
