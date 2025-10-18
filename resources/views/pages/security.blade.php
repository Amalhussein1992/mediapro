@extends('layouts.public')

@section('title', 'Security')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Security</h1>
        <h1 class="page-title content-ar">الأمان</h1>
        <p class="page-subtitle content-en">
            Your security is our top priority. Learn how we protect your data.
        </p>
        <p class="page-subtitle content-ar">
            أمانك هو أولويتنا القصوى. تعرف على كيفية حماية بياناتك.
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .security-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 30px;
                margin-bottom: 60px;
            }

            .security-card {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                transition: all 0.4s ease;
            }

            .security-card:hover {
                transform: translateY(-10px);
                border-color: var(--primary-purple);
                box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);
            }

            .security-icon {
                font-size: 3.5rem;
                margin-bottom: 25px;
                display: inline-block;
            }

            .security-title {
                font-size: 1.6rem;
                font-weight: 700;
                color: var(--text-light);
                margin-bottom: 15px;
            }

            .security-desc {
                color: var(--text-gray);
                line-height: 1.8;
            }

            .security-details {
                max-width: 900px;
                margin: 0 auto;
                background: var(--dark-card);
                padding: 50px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
            }

            .security-section {
                margin-bottom: 40px;
            }

            .security-section h2 {
                font-size: 2rem;
                color: var(--text-light);
                margin-bottom: 20px;
            }

            .security-section p {
                color: var(--text-gray);
                line-height: 2;
                margin-bottom: 15px;
            }

            .security-section ul {
                color: var(--text-gray);
                line-height: 2;
                padding-left: 30px;
            }

            .security-section li {
                margin-bottom: 10px;
            }

            .compliance-badges {
                display: flex;
                justify-content: center;
                gap: 30px;
                flex-wrap: wrap;
                margin-top: 40px;
            }

            .badge {
                padding: 15px 30px;
                background: rgba(99, 102, 241, 0.1);
                border: 1px solid rgba(99, 102, 241, 0.3);
                border-radius: 50px;
                color: var(--primary-blue);
                font-weight: 600;
            }
        </style>

        <div class="security-grid">
            <div class="security-card">
                <div class="security-icon">🔒</div>
                <h3 class="security-title content-en">Encryption</h3>
                <h3 class="security-title content-ar">التشفير</h3>
                <p class="security-desc content-en">
                    All data is encrypted in transit using TLS 1.3 and at rest using AES-256 encryption.
                    Your sensitive information is always protected.
                </p>
                <p class="security-desc content-ar">
                    يتم تشفير جميع البيانات أثناء النقل باستخدام TLS 1.3 وفي حالة السكون باستخدام تشفير AES-256.
                    معلوماتك الحساسة محمية دائماً.
                </p>
            </div>

            <div class="security-card">
                <div class="security-icon">🛡️</div>
                <h3 class="security-title content-en">Authentication</h3>
                <h3 class="security-title content-ar">المصادقة</h3>
                <p class="security-desc content-en">
                    Multi-factor authentication (MFA) and OAuth 2.0 secure authentication protocols protect
                    your account from unauthorized access.
                </p>
                <p class="security-desc content-ar">
                    المصادقة متعددة العوامل (MFA) وبروتوكولات المصادقة الآمنة OAuth 2.0 تحمي
                    حسابك من الوصول غير المصرح به.
                </p>
            </div>

            <div class="security-card">
                <div class="security-icon">🔍</div>
                <h3 class="security-title content-en">Monitoring</h3>
                <h3 class="security-title content-ar">المراقبة</h3>
                <p class="security-desc content-en">
                    24/7 security monitoring and automated threat detection systems protect against
                    malicious activities and potential security breaches.
                </p>
                <p class="security-desc content-ar">
                    مراقبة الأمان على مدار الساعة وأنظمة الكشف الآلي عن التهديدات تحمي من
                    الأنشطة الضارة والانتهاكات الأمنية المحتملة.
                </p>
            </div>

            <div class="security-card">
                <div class="security-icon">💾</div>
                <h3 class="security-title content-en">Backups</h3>
                <h3 class="security-title content-ar">النسخ الاحتياطية</h3>
                <p class="security-desc content-en">
                    Automated daily backups with point-in-time recovery ensure your data is never lost
                    and can be restored if needed.
                </p>
                <p class="security-desc content-ar">
                    النسخ الاحتياطية اليومية التلقائية مع استرداد نقطة زمنية تضمن عدم فقدان بياناتك أبداً
                    ويمكن استعادتها إذا لزم الأمر.
                </p>
            </div>

            <div class="security-card">
                <div class="security-icon">🌐</div>
                <h3 class="security-title content-en">Infrastructure</h3>
                <h3 class="security-title content-ar">البنية التحتية</h3>
                <p class="security-desc content-en">
                    Hosted on enterprise-grade cloud infrastructure with 99.9% uptime SLA and
                    distributed globally for optimal performance.
                </p>
                <p class="security-desc content-ar">
                    مستضاف على بنية تحتية سحابية على مستوى المؤسسات مع SLA لوقت التشغيل بنسبة 99.9%
                    وموزع عالمياً للأداء الأمثل.
                </p>
            </div>

            <div class="security-card">
                <div class="security-icon">👥</div>
                <h3 class="security-title content-en">Access Control</h3>
                <h3 class="security-title content-ar">التحكم في الوصول</h3>
                <p class="security-desc content-en">
                    Role-based access control (RBAC) and granular permissions ensure team members
                    only access what they need.
                </p>
                <p class="security-desc content-ar">
                    التحكم في الوصول القائم على الأدوار (RBAC) والأذونات الدقيقة تضمن وصول أعضاء الفريق
                    فقط إلى ما يحتاجون إليه.
                </p>
            </div>
        </div>

        <div class="security-details">
            <div class="security-section">
                <h2 class="content-en">Our Security Practices</h2>
                <h2 class="content-ar">ممارساتنا الأمنية</h2>
                <p class="content-en">
                    At Media Pro, we implement industry-leading security practices to protect your data:
                </p>
                <p class="content-ar">
                    في ميديا برو، نطبق ممارسات أمنية رائدة في الصناعة لحماية بياناتك:
                </p>
                <ul class="content-en">
                    <li>Regular security audits and penetration testing</li>
                    <li>SOC 2 Type II compliance</li>
                    <li>GDPR and CCPA compliant data handling</li>
                    <li>Secure development lifecycle (SDLC)</li>
                    <li>Employee security training and background checks</li>
                    <li>Incident response and disaster recovery plans</li>
                </ul>
                <ul class="content-ar">
                    <li>عمليات تدقيق أمنية منتظمة واختبار الاختراق</li>
                    <li>الامتثال لـ SOC 2 Type II</li>
                    <li>التعامل مع البيانات المتوافق مع GDPR و CCPA</li>
                    <li>دورة حياة التطوير الآمن (SDLC)</li>
                    <li>التدريب الأمني للموظفين والفحوصات الخلفية</li>
                    <li>خطط الاستجابة للحوادث والتعافي من الكوارث</li>
                </ul>
            </div>

            <div class="security-section">
                <h2 class="content-en">Report a Security Issue</h2>
                <h2 class="content-ar">الإبلاغ عن مشكلة أمنية</h2>
                <p class="content-en">
                    If you discover a security vulnerability, please report it to our security team immediately
                    at: security@mediapro.com
                </p>
                <p class="content-ar">
                    إذا اكتشفت ثغرة أمنية، يرجى الإبلاغ عنها إلى فريق الأمان لدينا فوراً
                    على: security@mediapro.com
                </p>
                <p class="content-en">
                    We take all security reports seriously and will respond within 24 hours. We appreciate
                    responsible disclosure and may offer rewards for valid security findings.
                </p>
                <p class="content-ar">
                    نأخذ جميع التقارير الأمنية على محمل الجد وسنرد في غضون 24 ساعة. نقدر
                    الإفصاح المسؤول وقد نقدم مكافآت على النتائج الأمنية الصحيحة.
                </p>
            </div>

            <div class="compliance-badges">
                <span class="badge">SOC 2 Type II</span>
                <span class="badge">GDPR Compliant</span>
                <span class="badge">ISO 27001</span>
                <span class="badge">CCPA Compliant</span>
            </div>
        </div>
    </div>
</section>
@endsection
