@extends('layouts.public')

@section('title', 'Careers')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Join Our Team</h1>
        <h1 class="page-title content-ar">انضم إلى فريقنا</h1>
        <p class="page-subtitle content-en">
            Build the future of social media management with us
        </p>
        <p class="page-subtitle content-ar">
            ابنِ مستقبل إدارة وسائل التواصل الاجتماعي معنا
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .careers-content {
                max-width: 1000px;
                margin: 0 auto;
            }

            .values-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 25px;
                margin: 40px 0;
            }

            .value-card {
                background: var(--dark-card);
                padding: 35px;
                border-radius: 15px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                text-align: center;
            }

            .value-icon {
                font-size: 3rem;
                margin-bottom: 15px;
            }

            .value-title {
                font-size: 1.3rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 10px;
            }

            .value-desc {
                color: var(--text-gray);
                line-height: 1.6;
            }

            .job-listings {
                margin-top: 60px;
            }

            .job-card {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                margin-bottom: 20px;
                transition: all 0.3s ease;
            }

            .job-card:hover {
                border-color: var(--primary-purple);
                transform: translateX(10px);
            }

            .job-title {
                font-size: 1.8rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 15px;
            }

            .job-meta {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }

            .job-meta-item {
                color: var(--text-gray);
                font-size: 0.95rem;
            }

            .job-desc {
                color: var(--text-gray);
                line-height: 1.8;
            }

            .apply-button {
                margin-top: 20px;
                padding: 12px 30px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                color: white;
                text-decoration: none;
                border-radius: 10px;
                display: inline-block;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .apply-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
            }
        </style>

        <div class="careers-content">
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🚀</div>
                    <h3 class="value-title content-en">Innovation</h3>
                    <h3 class="value-title content-ar">الابتكار</h3>
                    <p class="value-desc content-en">We push boundaries and embrace new ideas</p>
                    <p class="value-desc content-ar">ندفع الحدود ونتبنى أفكاراً جديدة</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3 class="value-title content-en">Collaboration</h3>
                    <h3 class="value-title content-ar">التعاون</h3>
                    <p class="value-desc content-en">Together we achieve more</p>
                    <p class="value-desc content-ar">معاً نحقق المزيد</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">🎯</div>
                    <h3 class="value-title content-en">Excellence</h3>
                    <h3 class="value-title content-ar">التميز</h3>
                    <p class="value-desc content-en">We strive for excellence in everything</p>
                    <p class="value-desc content-ar">نسعى للتميز في كل شيء</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">🌍</div>
                    <h3 class="value-title content-en">Diversity</h3>
                    <h3 class="value-title content-ar">التنوع</h3>
                    <p class="value-desc content-en">Different perspectives make us stronger</p>
                    <p class="value-desc content-ar">وجهات النظر المختلفة تجعلنا أقوى</p>
                </div>
            </div>

            <div class="job-listings">
                <h2 style="font-size: 2.5rem; margin-bottom: 30px; text-align: center;" class="content-en">Open Positions</h2>
                <h2 style="font-size: 2.5rem; margin-bottom: 30px; text-align: center;" class="content-ar">الوظائف المتاحة</h2>

                <div class="job-card">
                    <h3 class="job-title content-en">Senior Full Stack Developer</h3>
                    <h3 class="job-title content-ar">مطور Full Stack أول</h3>
                    <div class="job-meta">
                        <span class="job-meta-item content-en">📍 Remote</span>
                        <span class="job-meta-item content-ar">📍 عن بُعد</span>
                        <span class="job-meta-item content-en">⏰ Full-time</span>
                        <span class="job-meta-item content-ar">⏰ دوام كامل</span>
                        <span class="job-meta-item content-en">💼 Engineering</span>
                        <span class="job-meta-item content-ar">💼 الهندسة</span>
                    </div>
                    <p class="job-desc content-en">
                        Join our engineering team to build and scale our platform. You'll work with React, Node.js,
                        Laravel, and cutting-edge technologies to create amazing experiences for our users.
                    </p>
                    <p class="job-desc content-ar">
                        انضم إلى فريق الهندسة لدينا لبناء منصتنا وتوسيع نطاقها. ستعمل مع React و Node.js
                        و Laravel وأحدث التقنيات لإنشاء تجارب مذهلة لمستخدمينا.
                    </p>
                    <a href="#" class="apply-button content-en">Apply Now →</a>
                    <a href="#" class="apply-button content-ar">قدّم الآن ←</a>
                </div>

                <div class="job-card">
                    <h3 class="job-title content-en">Product Designer</h3>
                    <h3 class="job-title content-ar">مصمم منتجات</h3>
                    <div class="job-meta">
                        <span class="job-meta-item content-en">📍 Remote</span>
                        <span class="job-meta-item content-ar">📍 عن بُعد</span>
                        <span class="job-meta-item content-en">⏰ Full-time</span>
                        <span class="job-meta-item content-ar">⏰ دوام كامل</span>
                        <span class="job-meta-item content-en">🎨 Design</span>
                        <span class="job-meta-item content-ar">🎨 التصميم</span>
                    </div>
                    <p class="job-desc content-en">
                        Shape the future of our product through thoughtful design. Create beautiful, intuitive
                        interfaces that delight our users and solve real problems.
                    </p>
                    <p class="job-desc content-ar">
                        اصنع مستقبل منتجنا من خلال التصميم المدروس. أنشئ واجهات جميلة وبديهية
                        تسعد مستخدمينا وتحل مشاكل حقيقية.
                    </p>
                    <a href="#" class="apply-button content-en">Apply Now →</a>
                    <a href="#" class="apply-button content-ar">قدّم الآن ←</a>
                </div>

                <div class="job-card">
                    <h3 class="job-title content-en">Customer Success Manager</h3>
                    <h3 class="job-title content-ar">مدير نجاح العملاء</h3>
                    <div class="job-meta">
                        <span class="job-meta-item content-en">📍 Remote</span>
                        <span class="job-meta-item content-ar">📍 عن بُعد</span>
                        <span class="job-meta-item content-en">⏰ Full-time</span>
                        <span class="job-meta-item content-ar">⏰ دوام كامل</span>
                        <span class="job-meta-item content-en">💬 Support</span>
                        <span class="job-meta-item content-ar">💬 الدعم</span>
                    </div>
                    <p class="job-desc content-en">
                        Help our customers succeed by providing exceptional support and building lasting relationships.
                        Be the voice of our customers and help improve our product.
                    </p>
                    <p class="job-desc content-ar">
                        ساعد عملاءنا على النجاح من خلال توفير دعم استثنائي وبناء علاقات دائمة.
                        كن صوت عملائنا وساعد في تحسين منتجنا.
                    </p>
                    <a href="#" class="apply-button content-en">Apply Now →</a>
                    <a href="#" class="apply-button content-ar">قدّم الآن ←</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
