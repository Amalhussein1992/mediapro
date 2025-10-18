@extends('layouts.public')

@section('title', 'Terms of Service')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Terms of Service</h1>
        <h1 class="page-title content-ar">شروط الخدمة</h1>
        <p class="page-subtitle content-en">
            Please read these terms carefully before using our services
        </p>
        <p class="page-subtitle content-ar">
            يرجى قراءة هذه الشروط بعناية قبل استخدام خدماتنا
        </p>
        <p style="color: var(--text-muted); margin-top: 20px; position: relative; z-index: 1;" class="content-en">
            Last updated: January 1, 2025
        </p>
        <p style="color: var(--text-muted); margin-top: 20px; position: relative; z-index: 1;" class="content-ar">
            آخر تحديث: 1 يناير 2025
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .legal-content {
                max-width: 900px;
                margin: 0 auto;
                background: var(--dark-card);
                padding: 50px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
            }

            .legal-section {
                margin-bottom: 40px;
            }

            .legal-section h2 {
                font-size: 1.8rem;
                color: var(--text-light);
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px solid rgba(99, 102, 241, 0.2);
            }

            .legal-section p {
                color: var(--text-gray);
                line-height: 2;
                margin-bottom: 15px;
            }

            .legal-section ul {
                color: var(--text-gray);
                line-height: 2;
                padding-left: 30px;
                margin-bottom: 15px;
            }

            .legal-section li {
                margin-bottom: 10px;
            }
        </style>

        <div class="legal-content">
            <div class="legal-section">
                <h2 class="content-en">1. Acceptance of Terms</h2>
                <h2 class="content-ar">1. قبول الشروط</h2>
                <p class="content-en">
                    By accessing and using Media Pro's services, you accept and agree to be bound by these Terms of Service.
                    If you do not agree to these terms, please do not use our services.
                </p>
                <p class="content-ar">
                    من خلال الوصول إلى خدمات ميديا برو واستخدامها، فإنك تقبل وتوافق على الالتزام بشروط الخدمة هذه.
                    إذا كنت لا توافق على هذه الشروط، يرجى عدم استخدام خدماتنا.
                </p>
            </div>

            <div class="legal-section">
                <h2 class="content-en">2. Description of Service</h2>
                <h2 class="content-ar">2. وصف الخدمة</h2>
                <p class="content-en">
                    Media Pro provides a social media management platform that allows users to schedule posts,
                    analyze performance, and manage multiple social media accounts. Our services include but are
                    not limited to:
                </p>
                <p class="content-ar">
                    يوفر ميديا برو منصة لإدارة وسائل التواصل الاجتماعي تتيح للمستخدمين جدولة المنشورات وتحليل الأداء
                    وإدارة حسابات وسائل التواصل الاجتماعي المتعددة. تشمل خدماتنا على سبيل المثال لا الحصر:
                </p>
                <ul class="content-en">
                    <li>Content scheduling and publishing</li>
                    <li>Analytics and reporting</li>
                    <li>AI-powered content generation</li>
                    <li>Team collaboration tools</li>
                    <li>Brand management features</li>
                </ul>
                <ul class="content-ar">
                    <li>جدولة ونشر المحتوى</li>
                    <li>التحليلات والتقارير</li>
                    <li>إنشاء محتوى بالذكاء الاصطناعي</li>
                    <li>أدوات التعاون الجماعي</li>
                    <li>ميزات إدارة العلامة التجارية</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">3. User Accounts</h2>
                <h2 class="content-ar">3. حسابات المستخدمين</h2>
                <p class="content-en">
                    You are responsible for maintaining the security of your account and password. You agree to:
                </p>
                <p class="content-ar">
                    أنت مسؤول عن الحفاظ على أمان حسابك وكلمة المرور الخاصة بك. أنت توافق على:
                </p>
                <ul class="content-en">
                    <li>Provide accurate and complete information</li>
                    <li>Keep your password secure and confidential</li>
                    <li>Notify us immediately of any unauthorized access</li>
                    <li>Be responsible for all activities under your account</li>
                    <li>Not share your account with others</li>
                </ul>
                <ul class="content-ar">
                    <li>تقديم معلومات دقيقة وكاملة</li>
                    <li>الحفاظ على أمان كلمة المرور الخاصة بك وسريتها</li>
                    <li>إخطارنا فوراً بأي وصول غير مصرح به</li>
                    <li>تحمل المسؤولية عن جميع الأنشطة تحت حسابك</li>
                    <li>عدم مشاركة حسابك مع الآخرين</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">4. Acceptable Use</h2>
                <h2 class="content-ar">4. الاستخدام المقبول</h2>
                <p class="content-en">
                    You agree not to use our services to:
                </p>
                <p class="content-ar">
                    أنت توافق على عدم استخدام خدماتنا من أجل:
                </p>
                <ul class="content-en">
                    <li>Violate any laws or regulations</li>
                    <li>Infringe on intellectual property rights</li>
                    <li>Distribute spam, malware, or harmful content</li>
                    <li>Harass, abuse, or harm others</li>
                    <li>Engage in fraudulent activities</li>
                    <li>Interfere with the proper functioning of our services</li>
                </ul>
                <ul class="content-ar">
                    <li>انتهاك أي قوانين أو لوائح</li>
                    <li>التعدي على حقوق الملكية الفكرية</li>
                    <li>توزيع الرسائل غير المرغوب فيها أو البرامج الضارة أو المحتوى الضار</li>
                    <li>مضايقة الآخرين أو إساءة معاملتهم أو إيذائهم</li>
                    <li>الانخراط في أنشطة احتيالية</li>
                    <li>التدخل في الأداء السليم لخدماتنا</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">5. Payment and Billing</h2>
                <h2 class="content-ar">5. الدفع والفوترة</h2>
                <p class="content-en">
                    Paid subscriptions are billed in advance on a recurring basis. You agree to pay all fees
                    associated with your subscription. We reserve the right to change our pricing with 30 days notice.
                </p>
                <p class="content-ar">
                    يتم إصدار فواتير الاشتراكات المدفوعة مقدماً على أساس متكرر. أنت توافق على دفع جميع الرسوم
                    المرتبطة باشتراكك. نحتفظ بالحق في تغيير أسعارنا مع إشعار مدته 30 يوماً.
                </p>
            </div>

            <div class="legal-section">
                <h2 class="content-en">6. Termination</h2>
                <h2 class="content-ar">6. الإنهاء</h2>
                <p class="content-en">
                    We may terminate or suspend your account at any time for violations of these terms. You may
                    cancel your account at any time through your account settings.
                </p>
                <p class="content-ar">
                    يمكننا إنهاء حسابك أو تعليقه في أي وقت بسبب انتهاكات لهذه الشروط. يمكنك
                    إلغاء حسابك في أي وقت من خلال إعدادات حسابك.
                </p>
            </div>

            <div class="legal-section">
                <h2 class="content-en">7. Limitation of Liability</h2>
                <h2 class="content-ar">7. حدود المسؤولية</h2>
                <p class="content-en">
                    Media Pro is provided "as is" without warranties of any kind. We shall not be liable for any
                    indirect, incidental, special, consequential, or punitive damages.
                </p>
                <p class="content-ar">
                    يتم توفير ميديا برو "كما هو" دون ضمانات من أي نوع. لن نكون مسؤولين عن أي
                    أضرار غير مباشرة أو عرضية أو خاصة أو تبعية أو عقابية.
                </p>
            </div>

            <div class="legal-section">
                <h2 class="content-en">8. Contact Information</h2>
                <h2 class="content-ar">8. معلومات الاتصال</h2>
                <p class="content-en">
                    For questions about these Terms of Service, please contact us at: legal@mediapro.com
                </p>
                <p class="content-ar">
                    للأسئلة حول شروط الخدمة هذه، يرجى الاتصال بنا على: legal@mediapro.com
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
