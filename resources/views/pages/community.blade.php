@extends('layouts.public')

@section('title', 'Community')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Join Our Community</h1>
        <h1 class="page-title content-ar">انضم إلى مجتمعنا</h1>
        <p class="page-subtitle content-en">
            Connect with creators, share tips, and grow together
        </p>
        <p class="page-subtitle content-ar">
            تواصل مع المبدعين، شارك النصائح، وانمو معاً
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .community-platforms {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                max-width: 1100px;
                margin: 0 auto 60px;
            }

            .platform-card {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                transition: all 0.4s ease;
                text-align: center;
            }

            .platform-card:hover {
                transform: translateY(-10px);
                border-color: var(--primary-purple);
                box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);
            }

            .platform-icon {
                font-size: 4rem;
                margin-bottom: 20px;
            }

            .platform-name {
                font-size: 1.8rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 15px;
            }

            .platform-desc {
                color: var(--text-gray);
                line-height: 1.8;
                margin-bottom: 25px;
            }

            .platform-stats {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-bottom: 25px;
            }

            .stat {
                text-align: center;
            }

            .stat-number {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--primary-blue);
            }

            .stat-label {
                font-size: 0.85rem;
                color: var(--text-muted);
            }

            .join-button {
                display: inline-block;
                padding: 12px 30px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                color: white;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .join-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
            }

            .community-benefits {
                max-width: 900px;
                margin: 60px auto;
                background: var(--dark-card);
                padding: 50px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
            }

            .benefits-title {
                font-size: 2.5rem;
                margin-bottom: 30px;
                text-align: center;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .benefits-list {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 25px;
            }

            .benefit-item {
                display: flex;
                align-items: flex-start;
                gap: 15px;
            }

            .benefit-icon {
                font-size: 1.8rem;
                flex-shrink: 0;
            }

            .benefit-text {
                color: var(--text-gray);
                line-height: 1.6;
            }
        </style>

        <div class="community-platforms">
            <div class="platform-card">
                <div class="platform-icon">💬</div>
                <h3 class="platform-name content-en">Discord</h3>
                <h3 class="platform-name content-ar">ديسكورد</h3>
                <p class="platform-desc content-en">
                    Join our Discord server to chat with other users, get real-time support, and participate in events.
                </p>
                <p class="platform-desc content-ar">
                    انضم إلى خادم Discord الخاص بنا للدردشة مع مستخدمين آخرين، والحصول على دعم فوري، والمشاركة في الأحداث.
                </p>
                <div class="platform-stats">
                    <div class="stat">
                        <div class="stat-number">12K+</div>
                        <div class="stat-label content-en">Members</div>
                        <div class="stat-label content-ar">عضو</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label content-en">Active</div>
                        <div class="stat-label content-ar">نشط</div>
                    </div>
                </div>
                <a href="#" class="join-button content-en">Join Discord →</a>
                <a href="#" class="join-button content-ar">انضم إلى ديسكورد ←</a>
            </div>

            <div class="platform-card">
                <div class="platform-icon">🐦</div>
                <h3 class="platform-name content-en">Twitter</h3>
                <h3 class="platform-name content-ar">تويتر</h3>
                <p class="platform-desc content-en">
                    Follow us for product updates, tips, and social media insights from industry experts.
                </p>
                <p class="platform-desc content-ar">
                    تابعنا للحصول على تحديثات المنتج والنصائح ورؤى وسائل التواصل الاجتماعي من خبراء الصناعة.
                </p>
                <div class="platform-stats">
                    <div class="stat">
                        <div class="stat-number">25K+</div>
                        <div class="stat-label content-en">Followers</div>
                        <div class="stat-label content-ar">متابع</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">Daily</div>
                        <div class="stat-label content-en">Updates</div>
                        <div class="stat-label content-ar">تحديثات</div>
                    </div>
                </div>
                <a href="#" class="join-button content-en">Follow Us →</a>
                <a href="#" class="join-button content-ar">تابعنا ←</a>
            </div>

            <div class="platform-card">
                <div class="platform-icon">📺</div>
                <h3 class="platform-name content-en">YouTube</h3>
                <h3 class="platform-name content-ar">يوتيوب</h3>
                <p class="platform-desc content-en">
                    Watch tutorials, case studies, and learn best practices from successful creators.
                </p>
                <p class="platform-desc content-ar">
                    شاهد الدروس ودراسات الحالة وتعلم أفضل الممارسات من المبدعين الناجحين.
                </p>
                <div class="platform-stats">
                    <div class="stat">
                        <div class="stat-number">15K+</div>
                        <div class="stat-label content-en">Subscribers</div>
                        <div class="stat-label content-ar">مشترك</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">100+</div>
                        <div class="stat-label content-en">Videos</div>
                        <div class="stat-label content-ar">فيديو</div>
                    </div>
                </div>
                <a href="#" class="join-button content-en">Subscribe →</a>
                <a href="#" class="join-button content-ar">اشترك ←</a>
            </div>
        </div>

        <div class="community-benefits">
            <h2 class="benefits-title content-en">Community Benefits</h2>
            <h2 class="benefits-title content-ar">فوائد المجتمع</h2>

            <div class="benefits-list">
                <div class="benefit-item">
                    <span class="benefit-icon">🎓</span>
                    <p class="benefit-text content-en">
                        <strong>Learn & Grow:</strong> Access exclusive tutorials, webinars, and workshops
                    </p>
                    <p class="benefit-text content-ar">
                        <strong>تعلم وانمو:</strong> الوصول إلى دروس حصرية وندوات عبر الإنترنت وورش عمل
                    </p>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">🤝</span>
                    <p class="benefit-text content-en">
                        <strong>Network:</strong> Connect with creators and marketers from around the world
                    </p>
                    <p class="benefit-text content-ar">
                        <strong>التواصل:</strong> تواصل مع المبدعين والمسوقين من جميع أنحاء العالم
                    </p>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">💡</span>
                    <p class="benefit-text content-en">
                        <strong>Get Inspired:</strong> See how others use Media Pro to achieve their goals
                    </p>
                    <p class="benefit-text content-ar">
                        <strong>احصل على الإلهام:</strong> شاهد كيف يستخدم الآخرون ميديا برو لتحقيق أهدافهم
                    </p>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">🎁</span>
                    <p class="benefit-text content-en">
                        <strong>Exclusive Perks:</strong> Early access to new features and special discounts
                    </p>
                    <p class="benefit-text content-ar">
                        <strong>امتيازات حصرية:</strong> الوصول المبكر إلى الميزات الجديدة والخصومات الخاصة
                    </p>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">🗣️</span>
                    <p class="benefit-text content-en">
                        <strong>Share Feedback:</strong> Help shape the future of Media Pro
                    </p>
                    <p class="benefit-text content-ar">
                        <strong>شارك الملاحظات:</strong> ساعد في تشكيل مستقبل ميديا برو
                    </p>
                </div>

                <div class="benefit-item">
                    <span class="benefit-icon">🏆</span>
                    <p class="benefit-text content-en">
                        <strong>Recognition:</strong> Get featured in our success stories and case studies
                    </p>
                    <p class="benefit-text content-ar">
                        <strong>التقدير:</strong> احصل على ظهور في قصص نجاحنا ودراسات الحالة
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
