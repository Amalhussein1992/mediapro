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
            Last updated: January 21, 2025
        </p>
        <p style="color: var(--text-muted); margin-top: 20px; position: relative; z-index: 1;" class="content-ar">
            آخر تحديث: 21 يناير 2025
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            :root {
                --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                --dark-bg: #0f0f23;
                --dark-card: #1a1a2e;
                --dark-card-hover: #252540;
                --text-light: #ffffff;
                --text-gray: #a0aec0;
                --text-muted: #718096;
                --border-color: rgba(99, 102, 241, 0.15);
                --border-hover: rgba(99, 102, 241, 0.3);
            }

            .legal-content {
                max-width: 1000px;
                margin: 0 auto;
                padding-bottom: 60px;
            }

            .legal-section {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid var(--border-color);
                margin-bottom: 30px;
                transition: all 0.3s ease;
            }

            .legal-section:hover {
                border-color: var(--border-hover);
                box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
                transform: translateY(-2px);
            }

            .legal-section h2 {
                font-size: 1.9rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid rgba(99, 102, 241, 0.2);
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .legal-section h2::before {
                content: '';
                width: 6px;
                height: 35px;
                background: var(--primary-gradient);
                border-radius: 10px;
            }

            .legal-section h3 {
                font-size: 1.4rem;
                font-weight: 600;
                color: var(--text-light);
                margin-top: 25px;
                margin-bottom: 15px;
            }

            .legal-section p {
                color: var(--text-gray);
                line-height: 1.9;
                margin-bottom: 18px;
                font-size: 1.05rem;
            }

            .legal-section ul {
                color: var(--text-gray);
                line-height: 1.9;
                padding-left: 0;
                list-style: none;
                margin-bottom: 20px;
            }

            .legal-section ul li {
                margin-bottom: 12px;
                padding-left: 35px;
                position: relative;
                font-size: 1.05rem;
            }

            .legal-section ul li::before {
                content: '✓';
                position: absolute;
                left: 0;
                top: 0;
                width: 25px;
                height: 25px;
                background: var(--primary-gradient);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 0.85rem;
            }

            .highlight-box {
                background: rgba(99, 102, 241, 0.1);
                border-left: 4px solid #667eea;
                padding: 20px 25px;
                border-radius: 10px;
                margin: 25px 0;
            }

            .highlight-box p {
                margin: 0;
                color: var(--text-light);
                font-weight: 500;
            }

            .contact-cta {
                background: var(--primary-gradient);
                padding: 40px;
                border-radius: 20px;
                text-align: center;
                margin-top: 40px;
                box-shadow: 0 15px 50px rgba(102, 126, 234, 0.3);
            }

            .contact-cta h3 {
                color: white;
                font-size: 1.8rem;
                margin-bottom: 15px;
                font-weight: 700;
            }

            .contact-cta p {
                color: rgba(255, 255, 255, 0.9);
                font-size: 1.1rem;
                margin-bottom: 25px;
            }

            .contact-cta a {
                display: inline-block;
                padding: 15px 40px;
                background: white;
                color: #667eea;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.3s ease;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .contact-cta a:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            }

            .number-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 35px;
                height: 35px;
                background: var(--primary-gradient);
                border-radius: 50%;
                color: white;
                font-weight: bold;
                font-size: 1.1rem;
                margin-right: 15px;
            }

            /* Responsive Design */
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

                .legal-section p,
                .legal-section ul li {
                    font-size: 0.95rem;
                }

                .contact-cta {
                    padding: 30px 20px;
                }

                .contact-cta h3 {
                    font-size: 1.4rem;
                }

                .contact-cta a {
                    padding: 12px 30px;
                    font-size: 1rem;
                }
            }

            /* RTL Support */
            [dir="rtl"] .legal-section ul li {
                padding-left: 0;
                padding-right: 35px;
            }

            [dir="rtl"] .legal-section ul li::before {
                left: auto;
                right: 0;
            }

            [dir="rtl"] .highlight-box {
                border-left: none;
                border-right: 4px solid #667eea;
            }

            [dir="rtl"] .number-badge {
                margin-right: 0;
                margin-left: 15px;
            }

            strong {
                color: var(--text-light);
                font-weight: 600;
            }
        </style>

        <div class="legal-content">
            <!-- Introduction -->
            <div class="legal-section">
                <h2 class="content-en">📋 Agreement to Terms</h2>
                <h2 class="content-ar">📋 الموافقة على الشروط</h2>

                <p class="content-en">
                    Welcome to <strong>Media Pro</strong>! These Terms of Service ("Terms") govern your access to and use of our social media management platform,
                    including our website, mobile applications, and all related services (collectively, the "Services").
                </p>
                <p class="content-ar">
                    مرحباً بك في <strong>ميديا برو</strong>! تحكم شروط الخدمة هذه ("الشروط") وصولك إلى منصة إدارة وسائل التواصل الاجتماعي الخاصة بنا واستخدامها،
                    بما في ذلك موقعنا الإلكتروني وتطبيقات الجوال وجميع الخدمات ذات الصلة (يشار إليها مجتمعة باسم "الخدمات").
                </p>

                <div class="highlight-box">
                    <p class="content-en">
                        ⚠️ <strong>Important:</strong> By creating an account or using our Services, you agree to be bound by these Terms.
                        If you do not agree, please do not use our Services.
                    </p>
                    <p class="content-ar">
                        ⚠️ <strong>مهم:</strong> من خلال إنشاء حساب أو استخدام خدماتنا، فإنك توافق على الالتزام بهذه الشروط.
                        إذا كنت لا توافق، يرجى عدم استخدام خدماتنا.
                    </p>
                </div>
            </div>

            <!-- Description of Services -->
            <div class="legal-section">
                <h2 class="content-en">🚀 Our Services</h2>
                <h2 class="content-ar">🚀 خدماتنا</h2>

                <p class="content-en">
                    Media Pro is a comprehensive social media management platform designed to help individuals and businesses optimize their social media presence.
                    Our Services include:
                </p>
                <p class="content-ar">
                    ميديا برو هي منصة شاملة لإدارة وسائل التواصل الاجتماعي مصممة لمساعدة الأفراد والشركات على تحسين تواجدهم على وسائل التواصل الاجتماعي.
                    تشمل خدماتنا:
                </p>

                <h3 class="content-en">Core Features:</h3>
                <h3 class="content-ar">الميزات الأساسية:</h3>

                <ul class="content-en">
                    <li><strong>Content Scheduling:</strong> Plan and schedule posts across multiple social media platforms</li>
                    <li><strong>Analytics & Insights:</strong> Track performance metrics, engagement rates, and audience growth</li>
                    <li><strong>Multi-Platform Support:</strong> Manage Facebook, Instagram, Twitter, LinkedIn, TikTok, and more</li>
                    <li><strong>AI Content Generation:</strong> Create captions, hashtags, and images using artificial intelligence</li>
                    <li><strong>Team Collaboration:</strong> Work together with team members on content creation and approval</li>
                    <li><strong>Brand Kit Management:</strong> Store and manage your brand assets, colors, and fonts</li>
                    <li><strong>Link Shortening:</strong> Create and track shortened URLs for your campaigns</li>
                    <li><strong>Content Calendar:</strong> Visualize your posting schedule in an intuitive calendar view</li>
                    <li><strong>Media Library:</strong> Store and organize your images, videos, and other media files</li>
                    <li><strong>Competitor Analysis:</strong> Monitor and analyze your competitors' social media activity</li>
                </ul>

                <ul class="content-ar">
                    <li><strong>جدولة المحتوى:</strong> خطط وجدول المنشورات عبر منصات التواصل الاجتماعي المتعددة</li>
                    <li><strong>التحليلات والرؤى:</strong> تتبع مقاييس الأداء ومعدلات التفاعل ونمو الجمهور</li>
                    <li><strong>دعم منصات متعددة:</strong> إدارة فيسبوك وإنستغرام وتويتر ولينكد إن وتيك توك والمزيد</li>
                    <li><strong>إنشاء محتوى بالذكاء الاصطناعي:</strong> إنشاء تسميات توضيحية وهاشتاجات وصور باستخدام الذكاء الاصطناعي</li>
                    <li><strong>التعاون الجماعي:</strong> العمل مع أعضاء الفريق على إنشاء المحتوى والموافقة عليه</li>
                    <li><strong>إدارة حزمة العلامة التجارية:</strong> تخزين وإدارة أصول علامتك التجارية وألوانها وخطوطها</li>
                    <li><strong>اختصار الروابط:</strong> إنشاء وتتبع الروابط المختصرة لحملاتك</li>
                    <li><strong>تقويم المحتوى:</strong> تصور جدول النشر الخاص بك في عرض تقويم بديهي</li>
                    <li><strong>مكتبة الوسائط:</strong> تخزين وتنظيم الصور ومقاطع الفيديو وملفات الوسائط الأخرى</li>
                    <li><strong>تحليل المنافسين:</strong> مراقبة وتحليل نشاط منافسيك على وسائل التواصل الاجتماعي</li>
                </ul>
            </div>

            <!-- User Accounts -->
            <div class="legal-section">
                <h2 class="content-en">👤 User Accounts & Registration</h2>
                <h2 class="content-ar">👤 حسابات المستخدمين والتسجيل</h2>

                <h3 class="content-en">Account Creation</h3>
                <h3 class="content-ar">إنشاء الحساب</h3>

                <p class="content-en">
                    To use our Services, you must create an account by providing accurate and complete information. You agree to:
                </p>
                <p class="content-ar">
                    لاستخدام خدماتنا، يجب عليك إنشاء حساب من خلال تقديم معلومات دقيقة وكاملة. أنت توافق على:
                </p>

                <ul class="content-en">
                    <li>Provide truthful, accurate, current, and complete information during registration</li>
                    <li>Maintain and promptly update your account information to keep it accurate and complete</li>
                    <li>Be at least 18 years old or have parental consent to use our Services</li>
                    <li>Maintain the security and confidentiality of your login credentials</li>
                    <li>Immediately notify us of any unauthorized use of your account</li>
                    <li>Accept all responsibility for activities that occur under your account</li>
                    <li>Not create multiple accounts or share your account with others</li>
                    <li>Not use automated bots or scripts to create accounts</li>
                </ul>

                <ul class="content-ar">
                    <li>تقديم معلومات صحيحة ودقيقة وحديثة وكاملة أثناء التسجيل</li>
                    <li>صيانة وتحديث معلومات حسابك بسرعة للحفاظ على دقتها واكتمالها</li>
                    <li>أن يكون عمرك 18 عاماً على الأقل أو لديك موافقة الوالدين لاستخدام خدماتنا</li>
                    <li>الحفاظ على أمان وسرية بيانات اعتماد تسجيل الدخول الخاصة بك</li>
                    <li>إخطارنا فوراً بأي استخدام غير مصرح به لحسابك</li>
                    <li>قبول جميع المسؤوليات عن الأنشطة التي تحدث تحت حسابك</li>
                    <li>عدم إنشاء حسابات متعددة أو مشاركة حسابك مع الآخرين</li>
                    <li>عدم استخدام روبوتات أو نصوص آلية لإنشاء حسابات</li>
                </ul>

                <h3 class="content-en">Account Security</h3>
                <h3 class="content-ar">أمان الحساب</h3>

                <p class="content-en">
                    You are solely responsible for maintaining the security of your account. We recommend:
                </p>
                <p class="content-ar">
                    أنت المسؤول الوحيد عن الحفاظ على أمان حسابك. نوصي بما يلي:
                </p>

                <ul class="content-en">
                    <li>Using a strong, unique password</li>
                    <li>Enabling two-factor authentication (2FA)</li>
                    <li>Never sharing your password with anyone</li>
                    <li>Logging out from shared devices</li>
                </ul>

                <ul class="content-ar">
                    <li>استخدام كلمة مرور قوية وفريدة</li>
                    <li>تمكين المصادقة الثنائية (2FA)</li>
                    <li>عدم مشاركة كلمة المرور الخاصة بك مع أي شخص</li>
                    <li>تسجيل الخروج من الأجهزة المشتركة</li>
                </ul>
            </div>

            <!-- Acceptable Use Policy -->
            <div class="legal-section">
                <h2 class="content-en">✅ Acceptable Use Policy</h2>
                <h2 class="content-ar">✅ سياسة الاستخدام المقبول</h2>

                <p class="content-en">
                    When using Media Pro, you agree to comply with all applicable laws and regulations. You may NOT use our Services to:
                </p>
                <p class="content-ar">
                    عند استخدام ميديا برو، فإنك توافق على الالتزام بجميع القوانين واللوائح المعمول بها. لا يجوز لك استخدام خدماتنا من أجل:
                </p>

                <ul class="content-en">
                    <li>Violate any local, state, national, or international law or regulation</li>
                    <li>Infringe upon or misappropriate any intellectual property rights</li>
                    <li>Distribute spam, malware, viruses, or any harmful code</li>
                    <li>Harass, abuse, threaten, or harm any individual or group</li>
                    <li>Engage in fraudulent, deceptive, or misleading activities</li>
                    <li>Post illegal, obscene, defamatory, or offensive content</li>
                    <li>Impersonate any person or entity, or falsely state your affiliation</li>
                    <li>Interfere with or disrupt the integrity or performance of our Services</li>
                    <li>Attempt to gain unauthorized access to our systems or user accounts</li>
                    <li>Scrape, crawl, or use automated means to access our Services</li>
                    <li>Reverse engineer, decompile, or disassemble our software</li>
                    <li>Use our Services to compete with us or create a similar service</li>
                </ul>

                <ul class="content-ar">
                    <li>انتهاك أي قانون أو لائحة محلية أو إقليمية أو وطنية أو دولية</li>
                    <li>التعدي على أي حقوق ملكية فكرية أو إساءة استخدامها</li>
                    <li>توزيع الرسائل غير المرغوب فيها أو البرامج الضارة أو الفيروسات أو أي رمز ضار</li>
                    <li>مضايقة أو إساءة معاملة أو تهديد أو إيذاء أي فرد أو مجموعة</li>
                    <li>الانخراط في أنشطة احتيالية أو خادعة أو مضللة</li>
                    <li>نشر محتوى غير قانوني أو فاحش أو تشهيري أو مسيء</li>
                    <li>انتحال شخصية أي شخص أو كيان، أو تزوير انتمائك</li>
                    <li>التدخل في سلامة أو أداء خدماتنا أو تعطيلها</li>
                    <li>محاولة الوصول غير المصرح به إلى أنظمتنا أو حسابات المستخدمين</li>
                    <li>استخراج البيانات أو الزحف أو استخدام وسائل آلية للوصول إلى خدماتنا</li>
                    <li>الهندسة العكسية أو فك تجميع برامجنا</li>
                    <li>استخدام خدماتنا للمنافسة معنا أو إنشاء خدمة مماثلة</li>
                </ul>

                <div class="highlight-box">
                    <p class="content-en">
                        ⚠️ Violation of this policy may result in immediate termination of your account and legal action.
                    </p>
                    <p class="content-ar">
                        ⚠️ قد يؤدي انتهاك هذه السياسة إلى إنهاء حسابك فوراً واتخاذ إجراءات قانونية.
                    </p>
                </div>
            </div>

            <!-- Subscription & Payment -->
            <div class="legal-section">
                <h2 class="content-en">💳 Subscription Plans & Payment</h2>
                <h2 class="content-ar">💳 خطط الاشتراك والدفع</h2>

                <h3 class="content-en">Subscription Plans</h3>
                <h3 class="content-ar">خطط الاشتراك</h3>

                <p class="content-en">
                    Media Pro offers various subscription plans (Free, Starter, Professional, Enterprise). Each plan includes different features and usage limits.
                </p>
                <p class="content-ar">
                    يوفر ميديا برو خطط اشتراك مختلفة (مجاني، مبتدئ، احترافي، مؤسسة). تتضمن كل خطة ميزات وحدود استخدام مختلفة.
                </p>

                <h3 class="content-en">Billing</h3>
                <h3 class="content-ar">الفوترة</h3>

                <ul class="content-en">
                    <li><strong>Recurring Charges:</strong> Paid subscriptions are billed in advance on a monthly or annual basis</li>
                    <li><strong>Auto-Renewal:</strong> Subscriptions automatically renew unless canceled before the renewal date</li>
                    <li><strong>Payment Methods:</strong> We accept credit cards, debit cards, and other payment methods</li>
                    <li><strong>Failed Payments:</strong> If payment fails, we'll retry and may suspend your account after several attempts</li>
                    <li><strong>Taxes:</strong> Prices exclude applicable taxes unless stated otherwise</li>
                </ul>

                <ul class="content-ar">
                    <li><strong>الرسوم المتكررة:</strong> يتم إصدار فواتير الاشتراكات المدفوعة مقدماً على أساس شهري أو سنوي</li>
                    <li><strong>التجديد التلقائي:</strong> تتجدد الاشتراكات تلقائياً ما لم يتم إلغاؤها قبل تاريخ التجديد</li>
                    <li><strong>طرق الدفع:</strong> نقبل بطاقات الائتمان وبطاقات الخصم وطرق الدفع الأخرى</li>
                    <li><strong>فشل الدفع:</strong> إذا فشل الدفع، سنعيد المحاولة وقد نعلق حسابك بعد عدة محاولات</li>
                    <li><strong>الضرائب:</strong> الأسعار لا تشمل الضرائب المعمول بها ما لم ينص على خلاف ذلك</li>
                </ul>

                <h3 class="content-en">Refunds & Cancellations</h3>
                <h3 class="content-ar">الاسترداد والإلغاء</h3>

                <ul class="content-en">
                    <li>You can cancel your subscription at any time through your account settings</li>
                    <li>Cancellations take effect at the end of the current billing period</li>
                    <li>We offer a 14-day money-back guarantee for first-time annual subscriptions</li>
                    <li>No refunds for partial months or unused features</li>
                    <li>Refunds for accounts terminated due to violations are not provided</li>
                </ul>

                <ul class="content-ar">
                    <li>يمكنك إلغاء اشتراكك في أي وقت من خلال إعدادات حسابك</li>
                    <li>تسري الإلغاءات في نهاية فترة الفوترة الحالية</li>
                    <li>نقدم ضمان استرداد الأموال لمدة 14 يوماً للاشتراكات السنوية لأول مرة</li>
                    <li>لا استرداد للأشهر الجزئية أو الميزات غير المستخدمة</li>
                    <li>لا يتم توفير استرداد للحسابات المنتهية بسبب الانتهاكات</li>
                </ul>

                <h3 class="content-en">Price Changes</h3>
                <h3 class="content-ar">تغييرات الأسعار</h3>

                <p class="content-en">
                    We reserve the right to modify our pricing with at least 30 days' notice. Price changes will not affect your current billing period.
                </p>
                <p class="content-ar">
                    نحتفظ بالحق في تعديل أسعارنا مع إشعار مدته 30 يوماً على الأقل. لن تؤثر تغييرات الأسعار على فترة الفوترة الحالية الخاصة بك.
                </p>
            </div>

            <!-- Content & Intellectual Property -->
            <div class="legal-section">
                <h2 class="content-en">📝 Content & Intellectual Property</h2>
                <h2 class="content-ar">📝 المحتوى والملكية الفكرية</h2>

                <h3 class="content-en">Your Content</h3>
                <h3 class="content-ar">المحتوى الخاص بك</h3>

                <p class="content-en">
                    You retain all ownership rights to content you create, upload, or publish through our Services. By using our Services, you grant us a limited license to:
                </p>
                <p class="content-ar">
                    تحتفظ بجميع حقوق الملكية للمحتوى الذي تنشئه أو تحمّله أو تنشره من خلال خدماتنا. باستخدام خدماتنا، فإنك تمنحنا ترخيصاً محدوداً لـ:
                </p>

                <ul class="content-en">
                    <li>Store, process, and transmit your content to provide our Services</li>
                    <li>Display your content as necessary to operate the platform</li>
                    <li>Make backups and ensure service reliability</li>
                    <li>Use aggregated, anonymized data for analytics and improvement</li>
                </ul>

                <ul class="content-ar">
                    <li>تخزين ومعالجة ونقل المحتوى الخاص بك لتقديم خدماتنا</li>
                    <li>عرض المحتوى الخاص بك حسب الضرورة لتشغيل المنصة</li>
                    <li>عمل نسخ احتياطية وضمان موثوقية الخدمة</li>
                    <li>استخدام البيانات المجمعة المجهولة للتحليلات والتحسين</li>
                </ul>

                <h3 class="content-en">Our Intellectual Property</h3>
                <h3 class="content-ar">ملكيتنا الفكرية</h3>

                <p class="content-en">
                    Media Pro and all associated trademarks, logos, software, and technology are owned by us. You may not:
                </p>
                <p class="content-ar">
                    ميديا برو وجميع العلامات التجارية والشعارات والبرامج والتقنيات المرتبطة بها مملوكة لنا. لا يجوز لك:
                </p>

                <ul class="content-en">
                    <li>Copy, modify, or create derivative works from our platform</li>
                    <li>Use our trademarks or branding without written permission</li>
                    <li>Remove or alter any copyright notices or proprietary markings</li>
                </ul>

                <ul class="content-ar">
                    <li>نسخ أو تعديل أو إنشاء أعمال مشتقة من منصتنا</li>
                    <li>استخدام علاماتنا التجارية أو علامتنا التجارية بدون إذن كتابي</li>
                    <li>إزالة أو تغيير أي إشعارات حقوق النشر أو العلامات الملكية</li>
                </ul>
            </div>

            <!-- Third-Party Services -->
            <div class="legal-section">
                <h2 class="content-en">🔗 Third-Party Services & Integrations</h2>
                <h2 class="content-ar">🔗 خدمات وتكاملات الطرف الثالث</h2>

                <p class="content-en">
                    Media Pro integrates with various third-party social media platforms (Facebook, Instagram, Twitter, LinkedIn, TikTok, etc.).
                    When you connect these accounts:
                </p>
                <p class="content-ar">
                    يتكامل ميديا برو مع منصات التواصل الاجتماعي المختلفة للطرف الثالث (فيسبوك، إنستغرام، تويتر، لينكد إن، تيك توك، إلخ).
                    عند توصيل هذه الحسابات:
                </p>

                <ul class="content-en">
                    <li>You authorize us to access and manage your connected accounts on your behalf</li>
                    <li>You must comply with each platform's terms of service and community guidelines</li>
                    <li>We are not responsible for actions taken by third-party platforms</li>
                    <li>Changes to third-party APIs may affect our Services</li>
                    <li>You can disconnect accounts at any time through your settings</li>
                </ul>

                <ul class="content-ar">
                    <li>أنت تفوضنا بالوصول إلى حساباتك المتصلة وإدارتها نيابة عنك</li>
                    <li>يجب عليك الالتزام بشروط الخدمة وإرشادات المجتمع لكل منصة</li>
                    <li>نحن لسنا مسؤولين عن الإجراءات التي تتخذها منصات الطرف الثالث</li>
                    <li>قد تؤثر التغييرات في واجهات برمجة التطبيقات للطرف الثالث على خدماتنا</li>
                    <li>يمكنك فصل الحسابات في أي وقت من خلال إعداداتك</li>
                </ul>
            </div>

            <!-- Termination -->
            <div class="legal-section">
                <h2 class="content-en">🚫 Account Termination & Suspension</h2>
                <h2 class="content-ar">🚫 إنهاء الحساب وتعليقه</h2>

                <h3 class="content-en">Your Right to Terminate</h3>
                <h3 class="content-ar">حقك في الإنهاء</h3>

                <p class="content-en">
                    You may terminate your account at any time by:
                </p>
                <p class="content-ar">
                    يمكنك إنهاء حسابك في أي وقت عن طريق:
                </p>

                <ul class="content-en">
                    <li>Going to Settings → Account → Delete Account</li>
                    <li>Following the account deletion process</li>
                    <li>Confirming your decision (this action is irreversible)</li>
                </ul>

                <ul class="content-ar">
                    <li>الانتقال إلى الإعدادات ← الحساب ← حذف الحساب</li>
                    <li>اتباع عملية حذف الحساب</li>
                    <li>تأكيد قرارك (هذا الإجراء لا رجعة فيه)</li>
                </ul>

                <h3 class="content-en">Our Right to Terminate</h3>
                <h3 class="content-ar">حقنا في الإنهاء</h3>

                <p class="content-en">
                    We may suspend or terminate your account immediately if:
                </p>
                <p class="content-ar">
                    يمكننا تعليق حسابك أو إنهائه فوراً إذا:
                </p>

                <ul class="content-en">
                    <li>You violate these Terms of Service</li>
                    <li>You engage in fraudulent or illegal activities</li>
                    <li>Your payment fails repeatedly</li>
                    <li>Your account poses a security risk</li>
                    <li>You abuse or misuse our Services</li>
                    <li>Required by law or legal process</li>
                </ul>

                <ul class="content-ar">
                    <li>انتهكت شروط الخدمة هذه</li>
                    <li>شاركت في أنشطة احتيالية أو غير قانونية</li>
                    <li>فشل الدفع الخاص بك بشكل متكرر</li>
                    <li>يشكل حسابك خطراً أمنياً</li>
                    <li>أسأت استخدام خدماتنا</li>
                    <li>مطلوب بموجب القانون أو العملية القانونية</li>
                </ul>

                <h3 class="content-en">Effect of Termination</h3>
                <h3 class="content-ar">تأثير الإنهاء</h3>

                <ul class="content-en">
                    <li>Your access to the Services will be immediately revoked</li>
                    <li>Your content may be deleted after 30 days</li>
                    <li>No refunds will be provided for prepaid periods</li>
                    <li>You remain liable for any outstanding fees</li>
                </ul>

                <ul class="content-ar">
                    <li>سيتم إلغاء وصولك إلى الخدمات على الفور</li>
                    <li>قد يتم حذف المحتوى الخاص بك بعد 30 يوماً</li>
                    <li>لن يتم توفير أي استرداد للفترات المدفوعة مسبقاً</li>
                    <li>تظل مسؤولاً عن أي رسوم مستحقة</li>
                </ul>
            </div>

            <!-- Disclaimers & Limitations -->
            <div class="legal-section">
                <h2 class="content-en">⚖️ Disclaimers & Limitation of Liability</h2>
                <h2 class="content-ar">⚖️ إخلاء المسؤولية وحدود المسؤولية</h2>

                <h3 class="content-en">Service "As Is"</h3>
                <h3 class="content-ar">الخدمة "كما هي"</h3>

                <p class="content-en">
                    Our Services are provided "AS IS" and "AS AVAILABLE" without warranties of any kind, either express or implied, including but not limited to:
                </p>
                <p class="content-ar">
                    يتم توفير خدماتنا "كما هي" و"كما هي متاحة" بدون ضمانات من أي نوع، صريحة أو ضمنية، بما في ذلك على سبيل المثال لا الحصر:
                </p>

                <ul class="content-en">
                    <li>Warranties of merchantability or fitness for a particular purpose</li>
                    <li>Warranties of non-infringement</li>
                    <li>Warranties of uninterrupted or error-free service</li>
                    <li>Warranties of security or accuracy</li>
                </ul>

                <ul class="content-ar">
                    <li>ضمانات القابلية للتسويق أو الملاءمة لغرض معين</li>
                    <li>ضمانات عدم الانتهاك</li>
                    <li>ضمانات الخدمة دون انقطاع أو خالية من الأخطاء</li>
                    <li>ضمانات الأمان أو الدقة</li>
                </ul>

                <h3 class="content-en">Limitation of Liability</h3>
                <h3 class="content-ar">حدود المسؤولية</h3>

                <p class="content-en">
                    To the maximum extent permitted by law, Media Pro shall not be liable for:
                </p>
                <p class="content-ar">
                    إلى أقصى حد يسمح به القانون، لن يكون ميديا برو مسؤولاً عن:
                </p>

                <ul class="content-en">
                    <li>Any indirect, incidental, special, consequential, or punitive damages</li>
                    <li>Loss of profits, revenue, data, or business opportunities</li>
                    <li>Service interruptions or downtime</li>
                    <li>Actions or inactions of third-party services</li>
                    <li>Unauthorized access to or alteration of your content</li>
                    <li>Damages arising from your violation of these Terms</li>
                </ul>

                <ul class="content-ar">
                    <li>أي أضرار غير مباشرة أو عرضية أو خاصة أو تبعية أو عقابية</li>
                    <li>فقدان الأرباح أو الإيرادات أو البيانات أو الفرص التجارية</li>
                    <li>انقطاعات الخدمة أو التوقف عن العمل</li>
                    <li>إجراءات أو تقاعس خدمات الطرف الثالث</li>
                    <li>الوصول غير المصرح به إلى المحتوى الخاص بك أو تغييره</li>
                    <li>الأضرار الناجمة عن انتهاكك لهذه الشروط</li>
                </ul>

                <div class="highlight-box">
                    <p class="content-en">
                        Our total liability for any claims arising from your use of the Services shall not exceed the amount you paid us in the 12 months preceding the claim.
                    </p>
                    <p class="content-ar">
                        لن تتجاوز مسؤوليتنا الإجمالية عن أي مطالبات ناشئة عن استخدامك للخدمات المبلغ الذي دفعته لنا في الـ 12 شهراً السابقة للمطالبة.
                    </p>
                </div>
            </div>

            <!-- Indemnification -->
            <div class="legal-section">
                <h2 class="content-en">🛡️ Indemnification</h2>
                <h2 class="content-ar">🛡️ التعويض</h2>

                <p class="content-en">
                    You agree to indemnify, defend, and hold harmless Media Pro, its affiliates, officers, directors, employees, and agents from any claims,
                    liabilities, damages, losses, or expenses (including legal fees) arising from:
                </p>
                <p class="content-ar">
                    أنت توافق على تعويض ميديا برو والشركات التابعة لها والمسؤولين والمديرين والموظفين والوكلاء والدفاع عنهم وحمايتهم من أي مطالبات
                    أو التزامات أو أضرار أو خسائر أو نفقات (بما في ذلك الرسوم القانونية) الناشئة عن:
                </p>

                <ul class="content-en">
                    <li>Your violation of these Terms</li>
                    <li>Your content or actions on the platform</li>
                    <li>Your violation of any third-party rights</li>
                    <li>Your misuse of the Services</li>
                </ul>

                <ul class="content-ar">
                    <li>انتهاكك لهذه الشروط</li>
                    <li>المحتوى الخاص بك أو أفعالك على المنصة</li>
                    <li>انتهاكك لأي حقوق للطرف الثالث</li>
                    <li>إساءة استخدامك للخدمات</li>
                </ul>
            </div>

            <!-- Changes to Terms -->
            <div class="legal-section">
                <h2 class="content-en">🔄 Changes to These Terms</h2>
                <h2 class="content-ar">🔄 التغييرات على هذه الشروط</h2>

                <p class="content-en">
                    We may update these Terms from time to time to reflect changes in our Services, legal requirements, or business practices.
                    When we make material changes:
                </p>
                <p class="content-ar">
                    قد نقوم بتحديث هذه الشروط من وقت لآخر لتعكس التغييرات في خدماتنا أو المتطلبات القانونية أو الممارسات التجارية.
                    عندما نجري تغييرات جوهرية:
                </p>

                <ul class="content-en">
                    <li>We will notify you via email or in-app notification at least 30 days before the changes take effect</li>
                    <li>The updated Terms will be posted on our website with a new "Last Updated" date</li>
                    <li>Continued use of our Services after changes constitutes acceptance of the new Terms</li>
                    <li>If you disagree with the changes, you may terminate your account</li>
                </ul>

                <ul class="content-ar">
                    <li>سنخطرك عبر البريد الإلكتروني أو إشعار داخل التطبيق قبل 30 يوماً على الأقل من سريان التغييرات</li>
                    <li>سيتم نشر الشروط المحدثة على موقعنا الإلكتروني مع تاريخ "آخر تحديث" جديد</li>
                    <li>يشكل الاستخدام المستمر لخدماتنا بعد التغييرات قبولاً للشروط الجديدة</li>
                    <li>إذا كنت لا توافق على التغييرات، يمكنك إنهاء حسابك</li>
                </ul>
            </div>

            <!-- Governing Law -->
            <div class="legal-section">
                <h2 class="content-en">⚖️ Governing Law & Dispute Resolution</h2>
                <h2 class="content-ar">⚖️ القانون الحاكم وحل النزاعات</h2>

                <p class="content-en">
                    These Terms shall be governed by and construed in accordance with the laws of [Your Jurisdiction],
                    without regard to its conflict of law provisions.
                </p>
                <p class="content-ar">
                    تخضع هذه الشروط وتُفسر وفقاً لقوانين [الاختصاص القضائي الخاص بك]،
                    دون الأخذ في الاعتبار أحكام تنازع القوانين.
                </p>

                <h3 class="content-en">Dispute Resolution</h3>
                <h3 class="content-ar">حل النزاعات</h3>

                <ul class="content-en">
                    <li>Any disputes shall first be attempted to be resolved through good-faith negotiation</li>
                    <li>If negotiation fails, disputes may be resolved through arbitration</li>
                    <li>You agree to waive any right to a jury trial</li>
                    <li>Class action lawsuits are not permitted</li>
                </ul>

                <ul class="content-ar">
                    <li>يجب أولاً محاولة حل أي نزاعات من خلال التفاوض بحسن نية</li>
                    <li>إذا فشل التفاوض، يمكن حل النزاعات من خلال التحكيم</li>
                    <li>أنت توافق على التنازل عن أي حق في محاكمة أمام هيئة محلفين</li>
                    <li>الدعاوى الجماعية غير مسموح بها</li>
                </ul>
            </div>

            <!-- Miscellaneous -->
            <div class="legal-section">
                <h2 class="content-en">📄 General Provisions</h2>
                <h2 class="content-ar">📄 أحكام عامة</h2>

                <ul class="content-en">
                    <li><strong>Entire Agreement:</strong> These Terms constitute the entire agreement between you and Media Pro</li>
                    <li><strong>Severability:</strong> If any provision is found invalid, the remaining provisions remain in effect</li>
                    <li><strong>No Waiver:</strong> Our failure to enforce any right does not waive that right</li>
                    <li><strong>Assignment:</strong> You may not assign these Terms; we may assign them to an affiliate or successor</li>
                    <li><strong>Survival:</strong> Provisions that should survive termination will continue after termination</li>
                    <li><strong>Force Majeure:</strong> We are not liable for delays or failures due to circumstances beyond our control</li>
                </ul>

                <ul class="content-ar">
                    <li><strong>الاتفاقية الكاملة:</strong> تشكل هذه الشروط الاتفاقية الكاملة بينك وبين ميديا برو</li>
                    <li><strong>قابلية الفصل:</strong> إذا تبين أن أي حكم غير صالح، فإن الأحكام المتبقية تظل سارية المفعول</li>
                    <li><strong>عدم التنازل:</strong> عدم ممارستنا لأي حق لا يعني التنازل عن هذا الحق</li>
                    <li><strong>التنازل:</strong> لا يجوز لك التنازل عن هذه الشروط؛ يمكننا التنازل عنها لشركة تابعة أو خلف</li>
                    <li><strong>الاستمرارية:</strong> الأحكام التي يجب أن تظل سارية بعد الإنهاء ستستمر بعد الإنهاء</li>
                    <li><strong>القوة القاهرة:</strong> نحن لسنا مسؤولين عن التأخيرات أو الإخفاقات بسبب ظروف خارجة عن سيطرتنا</li>
                </ul>
            </div>

            <!-- Contact Section -->
            <div class="contact-cta">
                <h3 class="content-en">📞 Questions About These Terms?</h3>
                <h3 class="content-ar">📞 أسئلة حول هذه الشروط؟</h3>

                <p class="content-en">
                    If you have any questions or concerns about these Terms of Service, our Legal Team is here to help.
                </p>
                <p class="content-ar">
                    إذا كان لديك أي أسئلة أو مخاوف بشأن شروط الخدمة هذه، فريقنا القانوني هنا للمساعدة.
                </p>

                <a href="mailto:legal@mediapro.social" class="content-en">Contact Legal Team</a>
                <a href="mailto:legal@mediapro.social" class="content-ar">اتصل بالفريق القانوني</a>
            </div>
        </div>
    </div>
</section>

<script>
    // Language Toggle Functionality
    const savedLang = localStorage.getItem('preferredLanguage') || 'ar';
    document.documentElement.setAttribute('lang', savedLang);
    document.documentElement.setAttribute('dir', savedLang === 'ar' ? 'rtl' : 'ltr');

    // Show content based on language
    const contentEn = document.querySelectorAll('.content-en');
    const contentAr = document.querySelectorAll('.content-ar');

    if (savedLang === 'ar') {
        contentEn.forEach(el => el.style.display = 'none');
        contentAr.forEach(el => el.style.display = 'block');
    } else {
        contentEn.forEach(el => el.style.display = 'block');
        contentAr.forEach(el => el.style.display = 'none');
    }
</script>
@endsection
