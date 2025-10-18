@extends('layouts.public')

@section('title', 'Blog')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Blog & Resources</h1>
        <h1 class="page-title content-ar">المدونة والموارد</h1>
        <p class="page-subtitle content-en">
            Tips, strategies, and insights for social media success
        </p>
        <p class="page-subtitle content-ar">
            نصائح واستراتيجيات ورؤى للنجاح على وسائل التواصل الاجتماعي
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .blog-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 30px;
                margin-top: 40px;
            }

            .blog-card {
                background: var(--dark-card);
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                overflow: hidden;
                transition: all 0.4s ease;
            }

            .blog-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);
                border-color: var(--primary-purple);
            }

            .blog-image {
                width: 100%;
                height: 200px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
            }

            .blog-content {
                padding: 30px;
            }

            .blog-date {
                color: var(--text-muted);
                font-size: 0.9rem;
                margin-bottom: 10px;
            }

            .blog-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 15px;
            }

            .blog-excerpt {
                color: var(--text-gray);
                line-height: 1.8;
                margin-bottom: 20px;
            }

            .read-more {
                color: var(--primary-blue);
                text-decoration: none;
                font-weight: 600;
                transition: color 0.3s ease;
            }

            .read-more:hover {
                color: var(--primary-purple);
            }

            .empty-state {
                text-align: center;
                padding: 80px 20px;
                color: var(--text-gray);
            }

            .empty-state-icon {
                font-size: 5rem;
                margin-bottom: 20px;
                opacity: 0.5;
            }
        </style>

        <div class="blog-grid">
            <div class="blog-card">
                <div class="blog-image">📱</div>
                <div class="blog-content">
                    <div class="blog-date content-en">December 15, 2024</div>
                    <div class="blog-date content-ar">15 ديسمبر 2024</div>
                    <h3 class="blog-title content-en">10 Social Media Trends for 2025</h3>
                    <h3 class="blog-title content-ar">10 اتجاهات وسائل التواصل الاجتماعي لعام 2025</h3>
                    <p class="blog-excerpt content-en">
                        Discover the latest trends that will shape social media marketing in the coming year...
                    </p>
                    <p class="blog-excerpt content-ar">
                        اكتشف أحدث الاتجاهات التي ستشكل التسويق عبر وسائل التواصل الاجتماعي في العام المقبل...
                    </p>
                    <a href="#" class="read-more content-en">Read More →</a>
                    <a href="#" class="read-more content-ar">اقرأ المزيد ←</a>
                </div>
            </div>

            <div class="blog-card">
                <div class="blog-image">🎯</div>
                <div class="blog-content">
                    <div class="blog-date content-en">December 10, 2024</div>
                    <div class="blog-date content-ar">10 ديسمبر 2024</div>
                    <h3 class="blog-title content-en">How to Create Engaging Content</h3>
                    <h3 class="blog-title content-ar">كيفية إنشاء محتوى جذاب</h3>
                    <p class="blog-excerpt content-en">
                        Learn proven strategies to boost engagement and grow your audience on social media...
                    </p>
                    <p class="blog-excerpt content-ar">
                        تعلم الاستراتيجيات المثبتة لتعزيز التفاعل وتنمية جمهورك على وسائل التواصل الاجتماعي...
                    </p>
                    <a href="#" class="read-more content-en">Read More →</a>
                    <a href="#" class="read-more content-ar">اقرأ المزيد ←</a>
                </div>
            </div>

            <div class="blog-card">
                <div class="blog-image">🤖</div>
                <div class="blog-content">
                    <div class="blog-date content-en">December 5, 2024</div>
                    <div class="blog-date content-ar">5 ديسمبر 2024</div>
                    <h3 class="blog-title content-en">AI in Social Media Management</h3>
                    <h3 class="blog-title content-ar">الذكاء الاصطناعي في إدارة وسائل التواصل</h3>
                    <p class="blog-excerpt content-en">
                        Explore how AI is revolutionizing content creation and social media strategy...
                    </p>
                    <p class="blog-excerpt content-ar">
                        استكشف كيف يُحدث الذكاء الاصطناعي ثورة في إنشاء المحتوى واستراتيجية وسائل التواصل الاجتماعي...
                    </p>
                    <a href="#" class="read-more content-en">Read More →</a>
                    <a href="#" class="read-more content-ar">اقرأ المزيد ←</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
