@extends('layouts.public')

@section('title', 'Privacy Policy')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Privacy Policy</h1>
        <h1 class="page-title content-ar">سياسة الخصوصية</h1>
        <p class="page-subtitle content-en">
            Your privacy is important to us. Learn how we protect and handle your data.
        </p>
        <p class="page-subtitle content-ar">
            خصوصيتك مهمة بالنسبة لنا. تعرف على كيفية حماية بياناتك والتعامل معها.
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
                <h2 class="content-en">1. Information We Collect</h2>
                <h2 class="content-ar">1. المعلومات التي نجمعها</h2>
                <p class="content-en">
                    We collect information you provide directly to us when you create an account, use our services,
                    or communicate with us. This includes:
                </p>
                <p class="content-ar">
                    نجمع المعلومات التي تقدمها لنا مباشرة عند إنشاء حساب أو استخدام خدماتنا أو التواصل معنا. يتضمن ذلك:
                </p>
                <ul class="content-en">
                    <li>Account information (name, email, password)</li>
                    <li>Profile information and preferences</li>
                    <li>Content you create, upload, or share</li>
                    <li>Communications with us and other users</li>
                    <li>Payment and billing information</li>
                </ul>
                <ul class="content-ar">
                    <li>معلومات الحساب (الاسم، البريد الإلكتروني، كلمة المرور)</li>
                    <li>معلومات الملف الشخصي والتفضيلات</li>
                    <li>المحتوى الذي تنشئه أو تحمله أو تشاركه</li>
                    <li>الاتصالات معنا ومع مستخدمين آخرين</li>
                    <li>معلومات الدفع والفوترة</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">2. How We Use Your Information</h2>
                <h2 class="content-ar">2. كيف نستخدم معلوماتك</h2>
                <p class="content-en">
                    We use the information we collect to:
                </p>
                <p class="content-ar">
                    نستخدم المعلومات التي نجمعها من أجل:
                </p>
                <ul class="content-en">
                    <li>Provide, maintain, and improve our services</li>
                    <li>Process transactions and send related information</li>
                    <li>Send technical notices and support messages</li>
                    <li>Respond to your comments and questions</li>
                    <li>Analyze usage patterns and trends</li>
                    <li>Detect, prevent, and address fraud and security issues</li>
                </ul>
                <ul class="content-ar">
                    <li>توفير وصيانة وتحسين خدماتنا</li>
                    <li>معالجة المعاملات وإرسال المعلومات ذات الصلة</li>
                    <li>إرسال الإشعارات الفنية ورسائل الدعم</li>
                    <li>الرد على تعليقاتك وأسئلتك</li>
                    <li>تحليل أنماط الاستخدام والاتجاهات</li>
                    <li>اكتشاف ومنع ومعالجة مشاكل الاحتيال والأمان</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">3. Data Sharing and Disclosure</h2>
                <h2 class="content-ar">3. مشاركة البيانات والإفصاح عنها</h2>
                <p class="content-en">
                    We do not sell your personal information. We may share your information in the following circumstances:
                </p>
                <p class="content-ar">
                    نحن لا نبيع معلوماتك الشخصية. قد نشارك معلوماتك في الحالات التالية:
                </p>
                <ul class="content-en">
                    <li>With your consent or at your direction</li>
                    <li>With service providers who perform services on our behalf</li>
                    <li>To comply with legal obligations</li>
                    <li>To protect our rights, privacy, safety, or property</li>
                    <li>In connection with a merger, sale, or acquisition</li>
                </ul>
                <ul class="content-ar">
                    <li>بموافقتك أو بناءً على توجيهك</li>
                    <li>مع مزودي الخدمات الذين يؤدون خدمات نيابة عنا</li>
                    <li>للامتثال للالتزامات القانونية</li>
                    <li>لحماية حقوقنا وخصوصيتنا وسلامتنا أو ممتلكاتنا</li>
                    <li>فيما يتعلق بالاندماج أو البيع أو الاستحواذ</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">4. Data Security</h2>
                <h2 class="content-ar">4. أمان البيانات</h2>
                <p class="content-en">
                    We implement appropriate technical and organizational measures to protect your personal information.
                    However, no security system is impenetrable, and we cannot guarantee the security of our systems 100%.
                </p>
                <p class="content-ar">
                    نطبق تدابير تقنية وتنظيمية مناسبة لحماية معلوماتك الشخصية. ومع ذلك، لا يوجد نظام أمان منيع،
                    ولا يمكننا ضمان أمان أنظمتنا بنسبة 100%.
                </p>
            </div>

            <div class="legal-section">
                <h2 class="content-en">5. Your Rights and Choices</h2>
                <h2 class="content-ar">5. حقوقك وخياراتك</h2>
                <p class="content-en">
                    You have the right to:
                </p>
                <p class="content-ar">
                    لديك الحق في:
                </p>
                <ul class="content-en">
                    <li>Access and update your personal information</li>
                    <li>Request deletion of your data</li>
                    <li>Object to processing of your data</li>
                    <li>Request data portability</li>
                    <li>Withdraw consent at any time</li>
                </ul>
                <ul class="content-ar">
                    <li>الوصول إلى معلوماتك الشخصية وتحديثها</li>
                    <li>طلب حذف بياناتك</li>
                    <li>الاعتراض على معالجة بياناتك</li>
                    <li>طلب نقل البيانات</li>
                    <li>سحب الموافقة في أي وقت</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2 class="content-en">6. Contact Us</h2>
                <h2 class="content-ar">6. اتصل بنا</h2>
                <p class="content-en">
                    If you have any questions about this Privacy Policy, please contact us at: privacy@mediapro.com
                </p>
                <p class="content-ar">
                    إذا كان لديك أي أسئلة حول سياسة الخصوصية هذه، فيرجى الاتصال بنا على: privacy@mediapro.com
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
