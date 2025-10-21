@extends('layouts.public')

@section('title', 'Privacy Policy - سياسة الخصوصية')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Privacy Policy</h1>
        <h1 class="page-title content-ar">سياسة الخصوصية</h1>
        <p class="page-subtitle content-en">
            We respect your privacy and are committed to protecting your personal data
        </p>
        <p class="page-subtitle content-ar">
            نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية
        </p>
        <p style="color: var(--text-muted); margin-top: 20px; position: relative; z-index: 1;" class="content-en">
            Last updated: October 21, 2024
        </p>
        <p style="color: var(--text-muted); margin-top: 20px; position: relative; z-index: 1;" class="content-ar">
            آخر تحديث: 21 أكتوبر 2024
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .legal-content {
                max-width: 1000px;
                margin: 0 auto;
            }

            .legal-section {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                margin-bottom: 30px;
                transition: all 0.3s ease;
            }

            .legal-section:hover {
                border-color: rgba(99, 102, 241, 0.3);
                box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
            }

            .legal-section h2 {
                font-size: 1.8rem;
                color: var(--primary-blue);
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid rgba(99, 102, 241, 0.2);
                position: relative;
            }

            .legal-section h2::before {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 80px;
                height: 2px;
                background: linear-gradient(90deg, var(--primary-blue), var(--primary-purple));
            }

            [dir="rtl"] .legal-section h2::before {
                left: auto;
                right: 0;
            }

            .legal-section h3 {
                font-size: 1.4rem;
                color: var(--text-light);
                margin: 30px 0 15px 0;
                font-weight: 600;
            }

            .legal-section p {
                color: var(--text-gray);
                line-height: 2;
                margin-bottom: 20px;
                font-size: 1.05rem;
            }

            .legal-section ul {
                color: var(--text-gray);
                line-height: 2;
                padding-left: 0;
                list-style: none;
                margin-bottom: 20px;
            }

            .legal-section ul li {
                margin-bottom: 12px;
                padding-left: 30px;
                position: relative;
            }

            [dir="rtl"] .legal-section ul li {
                padding-left: 0;
                padding-right: 30px;
            }

            .legal-section ul li::before {
                content: '✓';
                position: absolute;
                left: 0;
                color: var(--primary-blue);
                font-weight: bold;
                font-size: 1.2rem;
            }

            [dir="rtl"] .legal-section ul li::before {
                left: auto;
                right: 0;
            }

            .legal-section strong {
                color: var(--text-light);
                font-weight: 600;
            }

            .highlight-box {
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
                border-left: 4px solid var(--primary-blue);
                padding: 20px;
                border-radius: 12px;
                margin: 25px 0;
            }

            [dir="rtl"] .highlight-box {
                border-left: none;
                border-right: 4px solid var(--primary-blue);
            }

            .highlight-box strong {
                color: var(--primary-blue);
            }

            .contact-cta {
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                padding: 40px;
                border-radius: 20px;
                text-align: center;
                margin-top: 40px;
            }

            .contact-cta h3 {
                font-size: 1.8rem;
                margin-bottom: 15px;
                color: white;
            }

            .contact-cta p {
                font-size: 1.1rem;
                margin-bottom: 25px;
                opacity: 0.95;
            }

            .contact-cta a {
                display: inline-block;
                padding: 15px 35px;
                background: rgba(255,255,255,0.2);
                color: white;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                transition: all 0.3s;
                backdrop-filter: blur(10px);
                margin: 0 10px;
            }

            .contact-cta a:hover {
                background: rgba(255,255,255,0.3);
                transform: translateY(-2px);
            }

            @media (max-width: 768px) {
                .legal-section {
                    padding: 25px;
                }

                .legal-section h2 {
                    font-size: 1.5rem;
                }

                .legal-section h3 {
                    font-size: 1.2rem;
                }

                .contact-cta {
                    padding: 30px 20px;
                }

                .contact-cta a {
                    display: block;
                    margin: 10px 0;
                }
            }
        </style>

        <div class="legal-content">
            <!-- Introduction -->
            <div class="legal-section">
                <h2 class="content-en">Introduction</h2>
                <h2 class="content-ar">المقدمة</h2>
                <p class="content-en">
                    Welcome to <strong>Media Pro</strong>. We value your trust and are committed to protecting your privacy. This Privacy Policy explains how we collect, use, protect, and handle your personal information when you use our application and services.
                </p>
                <p class="content-ar">
                    مرحباً بك في <strong>ميديا برو</strong>. نحن نقدر ثقتك بنا ونلتزم بحماية خصوصيتك. تشرح سياسة الخصوصية هذه كيفية جمع واستخدام وحماية ومعالجة معلوماتك الشخصية عند استخدام تطبيقنا وخدماتنا.
                </p>
                <p class="content-en">
                    By using Media Pro, you agree to the collection and use of information in accordance with this policy.
                </p>
                <p class="content-ar">
                    باستخدامك لتطبيق ميديا برو، فإنك توافق على جمع واستخدام المعلومات وفقاً لهذه السياسة.
                </p>
            </div>

            <!-- Information We Collect -->
            <div class="legal-section">
                <h2 class="content-en">Information We Collect</h2>
                <h2 class="content-ar">المعلومات التي نجمعها</h2>

                <h3 class="content-en">1. Information You Provide to Us:</h3>
                <h3 class="content-ar">1. المعلومات التي تقدمها لنا:</h3>
                <ul class="content-en">
                    <li><strong>Account Information:</strong> Name, email address, password</li>
                    <li><strong>Profile Information:</strong> Profile picture, bio, business information</li>
                    <li><strong>Content:</strong> Posts, images, videos you create or schedule</li>
                    <li><strong>Payment Information:</strong> Credit card details (encrypted) for paid subscriptions</li>
                    <li><strong>Communications:</strong> Messages, support tickets, feedback</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>معلومات الحساب:</strong> الاسم، البريد الإلكتروني، كلمة المرور</li>
                    <li><strong>معلومات الملف الشخصي:</strong> الصورة الشخصية، السيرة الذاتية، معلومات الأعمال</li>
                    <li><strong>المحتوى:</strong> المنشورات، الصور، مقاطع الفيديو التي تقوم بإنشائها أو جدولتها</li>
                    <li><strong>معلومات الدفع:</strong> تفاصيل بطاقة الائتمان (مشفرة) عند الاشتراك في الخطط المدفوعة</li>
                    <li><strong>الاتصالات:</strong> الرسائل، تذاكر الدعم، التعليقات</li>
                </ul>

                <h3 class="content-en">2. Information We Collect Automatically:</h3>
                <h3 class="content-ar">2. المعلومات التي نجمعها تلقائياً:</h3>
                <ul class="content-en">
                    <li><strong>Device Information:</strong> Device type, operating system, browser type</li>
                    <li><strong>Usage Data:</strong> How you use the app, features accessed, time spent</li>
                    <li><strong>Location Information:</strong> (Only if permission granted) To enhance user experience</li>
                    <li><strong>Cookies:</strong> To improve performance and remember your preferences</li>
                    <li><strong>Log Data:</strong> IP address, access times, pages viewed</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>معلومات الجهاز:</strong> نوع الجهاز، نظام التشغيل، نوع المتصفح</li>
                    <li><strong>بيانات الاستخدام:</strong> كيفية استخدامك للتطبيق، الميزات المستخدمة، الوقت المستغرق</li>
                    <li><strong>معلومات الموقع:</strong> (فقط إذا منحت الإذن) لتحسين تجربة المستخدم</li>
                    <li><strong>ملفات تعريف الارتباط:</strong> لتحسين الأداء وتذكر تفضيلاتك</li>
                    <li><strong>بيانات السجل:</strong> عنوان IP، أوقات الوصول، الصفحات المعروضة</li>
                </ul>

                <h3 class="content-en">3. Information from Social Media Accounts:</h3>
                <h3 class="content-ar">3. معلومات من حسابات التواصل الاجتماعي:</h3>
                <p class="content-en">
                    When you connect your social media accounts (Facebook, Instagram, Twitter, LinkedIn, TikTok), we collect limited information necessary to provide our services, such as profile data and posting permissions.
                </p>
                <p class="content-ar">
                    عند ربط حساباتك الاجتماعية (فيسبوك، انستجرام، تويتر، لينكد إن، تيك توك)، نجمع معلومات محدودة ضرورية لتقديم خدماتنا، مثل بيانات الملف الشخصي وأذونات النشر.
                </p>
            </div>

            <!-- How We Use Your Information -->
            <div class="legal-section">
                <h2 class="content-en">How We Use Your Information</h2>
                <h2 class="content-ar">كيف نستخدم معلوماتك</h2>
                <p class="content-en">We use the information we collect for the following purposes:</p>
                <p class="content-ar">نستخدم المعلومات التي نجمعها للأغراض التالية:</p>
                <ul class="content-en">
                    <li><strong>Provide Services:</strong> Manage your account, publish content, schedule posts</li>
                    <li><strong>Improve the App:</strong> Develop new features, enhance performance, fix bugs</li>
                    <li><strong>Communicate with You:</strong> Send notifications, updates, special offers (you can opt-out)</li>
                    <li><strong>Security:</strong> Protect your account, prevent fraud, maintain platform security</li>
                    <li><strong>Analytics:</strong> Understand app usage to improve user experience</li>
                    <li><strong>Customer Support:</strong> Respond to inquiries and assist with issues</li>
                    <li><strong>Legal Compliance:</strong> Comply with applicable laws and regulations</li>
                    <li><strong>AI Features:</strong> Generate content, captions, and hashtags using AI services</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>تقديم الخدمات:</strong> إدارة حسابك، نشر المحتوى، جدولة المنشورات</li>
                    <li><strong>تحسين التطبيق:</strong> تطوير ميزات جديدة، تحسين الأداء، إصلاح الأخطاء</li>
                    <li><strong>التواصل معك:</strong> إرسال إشعارات، تحديثات، عروض خاصة (يمكنك إلغاء الاشتراك)</li>
                    <li><strong>الأمان:</strong> حماية حسابك، منع الاحتيال، الحفاظ على أمان المنصة</li>
                    <li><strong>التحليلات:</strong> فهم استخدام التطبيق لتحسين تجربة المستخدم</li>
                    <li><strong>دعم العملاء:</strong> الرد على الاستفسارات والمساعدة في حل المشاكل</li>
                    <li><strong>الامتثال القانوني:</strong> الالتزام بالقوانين واللوائح المعمول بها</li>
                    <li><strong>ميزات الذكاء الاصطناعي:</strong> إنشاء محتوى، تعليقات، وهاشتاجات باستخدام خدمات AI</li>
                </ul>
                <div class="highlight-box">
                    <p class="content-en">
                        <strong>Important Note:</strong> We never sell or rent your personal information to third parties.
                    </p>
                    <p class="content-ar">
                        <strong>ملاحظة مهمة:</strong> نحن لا نبيع أو نؤجر معلوماتك الشخصية لأطراف ثالثة أبداً.
                    </p>
                </div>
            </div>

            <!-- Data Sharing -->
            <div class="legal-section">
                <h2 class="content-en">Sharing Your Information</h2>
                <h2 class="content-ar">مشاركة معلوماتك</h2>
                <p class="content-en">We may share your information only in the following cases:</p>
                <p class="content-ar">قد نشارك معلوماتك في الحالات التالية فقط:</p>

                <h3 class="content-en">1. With Service Providers:</h3>
                <h3 class="content-ar">1. مع مقدمي الخدمات:</h3>
                <ul class="content-en">
                    <li><strong>Hosting Services:</strong> To securely store your data</li>
                    <li><strong>Payment Processors:</strong> To process payments securely (Stripe, PayPal)</li>
                    <li><strong>Analytics Services:</strong> To understand app usage</li>
                    <li><strong>AI Services:</strong> To provide content generation features (Gemini, OpenAI, Claude)</li>
                    <li><strong>Email Services:</strong> To send you important notifications</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>خدمات الاستضافة:</strong> لتخزين بياناتك بشكل آمن</li>
                    <li><strong>معالجات الدفع:</strong> لمعالجة المدفوعات بأمان (Stripe، PayPal)</li>
                    <li><strong>خدمات التحليلات:</strong> لفهم استخدام التطبيق</li>
                    <li><strong>خدمات الذكاء الاصطناعي:</strong> لتوفير ميزات إنشاء المحتوى (Gemini، OpenAI، Claude)</li>
                    <li><strong>خدمات البريد الإلكتروني:</strong> لإرسال إشعارات مهمة لك</li>
                </ul>

                <h3 class="content-en">2. With Social Media Platforms:</h3>
                <h3 class="content-ar">2. مع منصات التواصل الاجتماعي:</h3>
                <p class="content-en">
                    To publish the content you schedule on your connected accounts and to retrieve analytics data.
                </p>
                <p class="content-ar">
                    لنشر المحتوى الذي تقوم بجدولته على حساباتك المتصلة ولاسترجاع بيانات التحليلات.
                </p>

                <h3 class="content-en">3. For Legal Compliance:</h3>
                <h3 class="content-ar">3. للامتثال القانوني:</h3>
                <ul class="content-en">
                    <li>When requested by competent legal authorities</li>
                    <li>To protect our rights or the safety of our users</li>
                    <li>To comply with court orders or legal proceedings</li>
                </ul>
                <ul class="content-ar">
                    <li>عند الطلب من السلطات القانونية المختصة</li>
                    <li>لحماية حقوقنا أو سلامة مستخدمينا</li>
                    <li>للامتثال بالأوامر القضائية أو الإجراءات القانونية</li>
                </ul>
            </div>

            <!-- Data Security -->
            <div class="legal-section">
                <h2 class="content-en">Data Security</h2>
                <h2 class="content-ar">أمان البيانات</h2>
                <p class="content-en">We take data security seriously and implement advanced security measures:</p>
                <p class="content-ar">نأخذ أمان البيانات على محمل الجد ونستخدم تدابير أمنية متقدمة:</p>
                <ul class="content-en">
                    <li><strong>Encryption:</strong> All data encrypted in transit and at rest using SSL/TLS</li>
                    <li><strong>Password Protection:</strong> Passwords stored with industry-standard encryption</li>
                    <li><strong>Two-Factor Authentication:</strong> Optional feature for additional account protection</li>
                    <li><strong>Limited Access:</strong> Only authorized personnel can access data</li>
                    <li><strong>Continuous Monitoring:</strong> Systems monitored 24/7 for security threats</li>
                    <li><strong>Regular Backups:</strong> Automated backups to prevent data loss</li>
                    <li><strong>Security Audits:</strong> Regular security testing and vulnerability assessments</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>التشفير:</strong> جميع البيانات مشفرة أثناء النقل والتخزين باستخدام SSL/TLS</li>
                    <li><strong>حماية كلمات المرور:</strong> كلمات المرور مخزنة بتشفير معياري صناعي</li>
                    <li><strong>المصادقة الثنائية:</strong> ميزة اختيارية لحماية إضافية للحساب</li>
                    <li><strong>وصول محدود:</strong> فقط الموظفون المصرح لهم يمكنهم الوصول للبيانات</li>
                    <li><strong>مراقبة مستمرة:</strong> مراقبة الأنظمة على مدار الساعة للكشف عن التهديدات الأمنية</li>
                    <li><strong>نسخ احتياطية منتظمة:</strong> نسخ احتياطي تلقائي لمنع فقدان البيانات</li>
                    <li><strong>تدقيق أمني:</strong> اختبارات أمنية منتظمة وتقييمات للثغرات</li>
                </ul>
            </div>

            <!-- Your Rights -->
            <div class="legal-section">
                <h2 class="content-en">Your Rights</h2>
                <h2 class="content-ar">حقوقك</h2>
                <p class="content-en">You have the following rights regarding your personal data:</p>
                <p class="content-ar">لديك الحقوق التالية فيما يتعلق ببياناتك الشخصية:</p>
                <ul class="content-en">
                    <li><strong>Access:</strong> Request a copy of your personal data at any time</li>
                    <li><strong>Correction:</strong> Update or correct your information from account settings</li>
                    <li><strong>Deletion:</strong> Request deletion of your account and associated data</li>
                    <li><strong>Object:</strong> Object to processing of your data in certain cases</li>
                    <li><strong>Portability:</strong> Request transfer of your data to another service</li>
                    <li><strong>Withdraw Consent:</strong> Withdraw consent for data processing at any time</li>
                    <li><strong>Restrict Processing:</strong> Request limitation on how we use your data</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>الوصول:</strong> طلب نسخة من بياناتك الشخصية في أي وقت</li>
                    <li><strong>التصحيح:</strong> تحديث أو تصحيح معلوماتك من إعدادات الحساب</li>
                    <li><strong>الحذف:</strong> طلب حذف حسابك والبيانات المرتبطة</li>
                    <li><strong>الاعتراض:</strong> الاعتراض على معالجة بياناتك في حالات معينة</li>
                    <li><strong>النقل:</strong> طلب نقل بياناتك إلى خدمة أخرى</li>
                    <li><strong>سحب الموافقة:</strong> سحب الموافقة على معالجة بياناتك في أي وقت</li>
                    <li><strong>تقييد المعالجة:</strong> طلب تقييد كيفية استخدامنا لبياناتك</li>
                </ul>
                <p class="content-en">
                    To exercise any of these rights, please contact us at: <strong>privacy@mediapro.social</strong>
                </p>
                <p class="content-ar">
                    لممارسة أي من هذه الحقوق، يرجى التواصل معنا عبر: <strong>privacy@mediapro.social</strong>
                </p>
            </div>

            <!-- Cookies -->
            <div class="legal-section">
                <h2 class="content-en">Cookies & Similar Technologies</h2>
                <h2 class="content-ar">ملفات تعريف الارتباط والتقنيات المماثلة</h2>
                <p class="content-en">We use cookies and similar technologies to enhance your experience:</p>
                <p class="content-ar">نستخدم ملفات تعريف الارتباط وتقنيات مماثلة لتحسين تجربتك:</p>
                <ul class="content-en">
                    <li><strong>Essential Cookies:</strong> Necessary for basic app functionality (login, security)</li>
                    <li><strong>Functional Cookies:</strong> Remember your preferences (language, settings)</li>
                    <li><strong>Analytics Cookies:</strong> Help us understand app usage and improve it</li>
                    <li><strong>Advertising Cookies:</strong> Display relevant ads (you can opt-out)</li>
                </ul>
                <ul class="content-ar">
                    <li><strong>ملفات ضرورية:</strong> لازمة للوظائف الأساسية للتطبيق (تسجيل الدخول، الأمان)</li>
                    <li><strong>ملفات وظيفية:</strong> تذكر تفضيلاتك (اللغة، الإعدادات)</li>
                    <li><strong>ملفات تحليلية:</strong> تساعدنا على فهم استخدام التطبيق وتحسينه</li>
                    <li><strong>ملفات إعلانية:</strong> عرض إعلانات ذات صلة (يمكنك الرفض)</li>
                </ul>
                <p class="content-en">
                    You can control cookies through your browser or app settings.
                </p>
                <p class="content-ar">
                    يمكنك التحكم في ملفات تعريف الارتباط من خلال إعدادات المتصفح أو التطبيق.
                </p>
            </div>

            <!-- Children's Privacy -->
            <div class="legal-section">
                <h2 class="content-en">Children's Privacy</h2>
                <h2 class="content-ar">خصوصية الأطفال</h2>
                <p class="content-en">
                    Media Pro is intended for users aged 18 and above. We do not knowingly collect personal information from children under 18. If we learn that we have collected information from a child under 18, we will take steps to delete it as soon as possible.
                </p>
                <p class="content-ar">
                    تطبيق ميديا برو مخصص للمستخدمين الذين تبلغ أعمارهم 18 عاماً فأكثر. نحن لا نجمع عن قصد معلومات شخصية من الأطفال دون سن 18. إذا علمنا أننا جمعنا معلومات من طفل دون سن 18، سنتخذ خطوات لحذفها في أقرب وقت ممكن.
                </p>
            </div>

            <!-- Changes to Policy -->
            <div class="legal-section">
                <h2 class="content-en">Changes to This Policy</h2>
                <h2 class="content-ar">التغييرات على هذه السياسة</h2>
                <p class="content-en">
                    We may update this Privacy Policy from time to time. We will notify you of any material changes through:
                </p>
                <p class="content-ar">
                    قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر. سنخطرك بأي تغييرات جوهرية من خلال:
                </p>
                <ul class="content-en">
                    <li>In-app notification</li>
                    <li>Email to your registered address</li>
                    <li>Updating the "Last Updated" date at the top of this page</li>
                </ul>
                <ul class="content-ar">
                    <li>إشعار داخل التطبيق</li>
                    <li>بريد إلكتروني إلى عنوانك المسجل</li>
                    <li>تحديث تاريخ "آخر تحديث" أعلى هذه الصفحة</li>
                </ul>
                <p class="content-en">
                    We recommend reviewing this Privacy Policy periodically to stay informed about any changes.
                </p>
                <p class="content-ar">
                    ننصح بمراجعة سياسة الخصوصية هذه بشكل دوري للبقاء على اطلاع بأي تغييرات.
                </p>
            </div>

            <!-- Contact -->
            <div class="contact-cta">
                <h3 class="content-en">Have Questions About Your Privacy?</h3>
                <h3 class="content-ar">هل لديك أسئلة حول خصوصيتك؟</h3>
                <p class="content-en">We're here to help! Don't hesitate to contact us</p>
                <p class="content-ar">نحن هنا للمساعدة! لا تتردد في التواصل معنا</p>
                <a href="mailto:privacy@mediapro.social">privacy@mediapro.social</a>
                <a href="mailto:support@mediapro.social">support@mediapro.social</a>
            </div>
        </div>
    </div>
</section>
@endsection
