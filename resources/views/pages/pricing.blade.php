@extends('layouts.public')

@section('title', $page->title ?? 'Pricing')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">{{ $page->title ?? 'Simple, Transparent Pricing' }}</h1>
        <h1 class="page-title content-ar">{{ $page->title_ar ?? 'أسعار بسيطة وشفافة' }}</h1>
        <p class="page-subtitle content-en">
            {{ $page->meta_description ?? 'Choose the perfect plan for your social media management needs' }}
        </p>
        <p class="page-subtitle content-ar">
            {{ $page->meta_description_ar ?? 'اختر الخطة المثالية لاحتياجات إدارة وسائل التواصل الاجتماعي الخاصة بك' }}
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .pricing-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 30px;
                margin-top: 40px;
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
            }

            .pricing-card {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                transition: all 0.4s ease;
                position: relative;
            }

            .pricing-card.featured {
                border: 2px solid var(--primary-purple);
                transform: scale(1.05);
                box-shadow: 0 20px 60px rgba(139, 92, 246, 0.4);
            }

            .pricing-card:hover:not(.featured) {
                transform: translateY(-10px);
                border-color: var(--primary-blue);
                box-shadow: 0 15px 40px rgba(99, 102, 241, 0.3);
            }

            .featured-badge {
                position: absolute;
                top: -15px;
                right: 30px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                color: white;
                padding: 8px 20px;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 700;
                box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
            }

            .plan-name {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 10px;
                color: var(--text-light);
            }

            .plan-desc {
                color: var(--text-gray);
                margin-bottom: 30px;
                font-size: 0.95rem;
            }

            .plan-price {
                font-size: 3.5rem;
                font-weight: 900;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 5px;
            }

            .plan-period {
                color: var(--text-muted);
                margin-bottom: 30px;
            }

            .plan-features {
                list-style: none;
                margin-bottom: 30px;
            }

            .plan-features li {
                padding: 12px 0;
                border-bottom: 1px solid rgba(99, 102, 241, 0.1);
                color: var(--text-gray);
            }

            .plan-features li:before {
                content: "✓";
                color: var(--primary-blue);
                font-weight: bold;
                margin-right: 10px;
            }

            .plan-button {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                color: white;
                border: none;
                border-radius: 12px;
                font-weight: 700;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .plan-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
            }

            .plan-button.secondary {
                background: transparent;
                border: 2px solid rgba(99, 102, 241, 0.5);
            }
        </style>

        <div class="pricing-grid">
            <!-- Starter Plan -->
            <div class="pricing-card">
                <h3 class="plan-name content-en">Starter</h3>
                <h3 class="plan-name content-ar">المبتدئ</h3>
                <p class="plan-desc content-en">Perfect for individuals and small creators</p>
                <p class="plan-desc content-ar">مثالي للأفراد والمبدعين الصغار</p>

                <div class="plan-price">$19</div>
                <p class="plan-period content-en">per month</p>
                <p class="plan-period content-ar">شهرياً</p>

                <ul class="plan-features content-en">
                    <li>5 Social accounts</li>
                    <li>30 Scheduled posts/month</li>
                    <li>Basic analytics</li>
                    <li>AI content suggestions</li>
                    <li>Email support</li>
                </ul>
                <ul class="plan-features content-ar">
                    <li>5 حسابات وسائل تواصل</li>
                    <li>30 منشور مجدول شهرياً</li>
                    <li>تحليلات أساسية</li>
                    <li>اقتراحات المحتوى بالذكاء الاصطناعي</li>
                    <li>دعم عبر البريد الإلكتروني</li>
                </ul>

                <button class="plan-button secondary content-en">Get Started</button>
                <button class="plan-button secondary content-ar">ابدأ الآن</button>
            </div>

            <!-- Professional Plan -->
            <div class="pricing-card featured">
                <div class="featured-badge content-en">Most Popular</div>
                <div class="featured-badge content-ar">الأكثر شعبية</div>

                <h3 class="plan-name content-en">Professional</h3>
                <h3 class="plan-name content-ar">المحترف</h3>
                <p class="plan-desc content-en">For growing businesses and agencies</p>
                <p class="plan-desc content-ar">للشركات والوكالات النامية</p>

                <div class="plan-price">$49</div>
                <p class="plan-period content-en">per month</p>
                <p class="plan-period content-ar">شهرياً</p>

                <ul class="plan-features content-en">
                    <li>15 Social accounts</li>
                    <li>Unlimited scheduled posts</li>
                    <li>Advanced analytics & reports</li>
                    <li>AI content generation</li>
                    <li>Team collaboration (5 members)</li>
                    <li>Brand kit management</li>
                    <li>Priority support</li>
                </ul>
                <ul class="plan-features content-ar">
                    <li>15 حساب وسائل تواصل</li>
                    <li>منشورات مجدولة غير محدودة</li>
                    <li>تحليلات وتقارير متقدمة</li>
                    <li>إنشاء محتوى بالذكاء الاصطناعي</li>
                    <li>تعاون الفريق (5 أعضاء)</li>
                    <li>إدارة مجموعة العلامة التجارية</li>
                    <li>دعم ذو أولوية</li>
                </ul>

                <button class="plan-button content-en">Get Started</button>
                <button class="plan-button content-ar">ابدأ الآن</button>
            </div>

            <!-- Enterprise Plan -->
            <div class="pricing-card">
                <h3 class="plan-name content-en">Enterprise</h3>
                <h3 class="plan-name content-ar">المؤسسات</h3>
                <p class="plan-desc content-en">Custom solutions for large organizations</p>
                <p class="plan-desc content-ar">حلول مخصصة للمنظمات الكبيرة</p>

                <div class="plan-price content-en">Custom</div>
                <div class="plan-price content-ar">مخصص</div>
                <p class="plan-period content-en">contact us</p>
                <p class="plan-period content-ar">اتصل بنا</p>

                <ul class="plan-features content-en">
                    <li>Unlimited social accounts</li>
                    <li>Unlimited scheduled posts</li>
                    <li>Custom analytics & BI integration</li>
                    <li>Advanced AI features</li>
                    <li>Unlimited team members</li>
                    <li>White-label options</li>
                    <li>API access</li>
                    <li>Dedicated account manager</li>
                    <li>24/7 Premium support</li>
                </ul>
                <ul class="plan-features content-ar">
                    <li>حسابات وسائل تواصل غير محدودة</li>
                    <li>منشورات مجدولة غير محدودة</li>
                    <li>تحليلات مخصصة وتكامل BI</li>
                    <li>ميزات ذكاء اصطناعي متقدمة</li>
                    <li>أعضاء فريق غير محدودين</li>
                    <li>خيارات العلامة البيضاء</li>
                    <li>وصول إلى API</li>
                    <li>مدير حساب مخصص</li>
                    <li>دعم متميز على مدار الساعة</li>
                </ul>

                <button class="plan-button secondary content-en">Contact Sales</button>
                <button class="plan-button secondary content-ar">تواصل مع المبيعات</button>
            </div>
        </div>
    </div>
</section>
@endsection
