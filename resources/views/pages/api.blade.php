@extends('layouts.public')

@section('title', 'API Documentation')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">API Documentation</h1>
        <h1 class="page-title content-ar">واجهة برمجية</h1>
        <p class="page-subtitle content-en">
            Integrate Media Pro with your applications using our powerful REST API
        </p>
        <p class="page-subtitle content-ar">
            ادمج ميديا برو مع تطبيقاتك باستخدام واجهة برمجة تطبيقات REST القوية الخاصة بنا
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .api-section {
                background: var(--dark-card);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
                margin-bottom: 30px;
            }

            .api-section h2 {
                font-size: 2rem;
                margin-bottom: 20px;
                color: var(--text-light);
            }

            .api-section p {
                color: var(--text-gray);
                line-height: 1.8;
                margin-bottom: 15px;
            }

            .api-features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-top: 30px;
            }

            .api-feature {
                padding: 25px;
                background: rgba(99, 102, 241, 0.05);
                border-radius: 12px;
                border: 1px solid rgba(99, 102, 241, 0.1);
            }

            .api-feature h4 {
                color: var(--primary-blue);
                margin-bottom: 10px;
                font-size: 1.2rem;
            }

            .code-block {
                background: #0d1117;
                padding: 20px;
                border-radius: 12px;
                margin: 20px 0;
                overflow-x: auto;
                border: 1px solid rgba(99, 102, 241, 0.2);
            }

            .code-block code {
                color: #58a6ff;
                font-family: 'Courier New', monospace;
                font-size: 0.9rem;
            }
        </style>

        <div class="api-section">
            <h2 class="content-en">Getting Started</h2>
            <h2 class="content-ar">البدء</h2>
            <p class="content-en">
                The Media Pro API allows you to programmatically access all features of our platform.
                Our RESTful API uses standard HTTP methods and returns JSON responses.
            </p>
            <p class="content-ar">
                تتيح لك واجهة برمجة تطبيقات ميديا برو الوصول برمجياً إلى جميع ميزات منصتنا.
                تستخدم واجهة برمجة التطبيقات RESTful الخاصة بنا طرق HTTP القياسية وتعيد استجابات JSON.
            </p>

            <div class="code-block">
                <code>
curl -X GET https://api.mediapro.com/v1/posts \<br>
  -H "Authorization: Bearer YOUR_API_KEY" \<br>
  -H "Content-Type: application/json"
                </code>
            </div>
        </div>

        <div class="api-section">
            <h2 class="content-en">Key Features</h2>
            <h2 class="content-ar">الميزات الرئيسية</h2>

            <div class="api-features">
                <div class="api-feature">
                    <h4 class="content-en">📝 Post Management</h4>
                    <h4 class="content-ar">📝 إدارة المنشورات</h4>
                    <p class="content-en">Create, schedule, update, and delete posts across all platforms</p>
                    <p class="content-ar">إنشاء وجدولة وتحديث وحذف المنشورات عبر جميع المنصات</p>
                </div>

                <div class="api-feature">
                    <h4 class="content-en">📊 Analytics</h4>
                    <h4 class="content-ar">📊 التحليلات</h4>
                    <p class="content-en">Access detailed analytics and performance metrics</p>
                    <p class="content-ar">الوصول إلى التحليلات التفصيلية ومقاييس الأداء</p>
                </div>

                <div class="api-feature">
                    <h4 class="content-en">🔗 Account Management</h4>
                    <h4 class="content-ar">🔗 إدارة الحسابات</h4>
                    <p class="content-en">Connect and manage multiple social media accounts</p>
                    <p class="content-ar">ربط وإدارة حسابات وسائل التواصل الاجتماعي المتعددة</p>
                </div>

                <div class="api-feature">
                    <h4 class="content-en">🤖 AI Integration</h4>
                    <h4 class="content-ar">🤖 تكامل الذكاء الاصطناعي</h4>
                    <p class="content-en">Leverage AI-powered content generation and optimization</p>
                    <p class="content-ar">الاستفادة من إنشاء المحتوى وتحسينه بالذكاء الاصطناعي</p>
                </div>
            </div>
        </div>

        <div class="api-section">
            <h2 class="content-en">Authentication</h2>
            <h2 class="content-ar">المصادقة</h2>
            <p class="content-en">
                All API requests require authentication using an API key. You can generate your API key from your account settings.
                Include your API key in the Authorization header of each request.
            </p>
            <p class="content-ar">
                تتطلب جميع طلبات API المصادقة باستخدام مفتاح API. يمكنك إنشاء مفتاح API الخاص بك من إعدادات حسابك.
                قم بتضمين مفتاح API الخاص بك في رأس التفويض لكل طلب.
            </p>
        </div>

        <div class="api-section">
            <h2 class="content-en">Rate Limits</h2>
            <h2 class="content-ar">حدود المعدل</h2>
            <p class="content-en">
                API rate limits vary by plan:
            </p>
            <p class="content-ar">
                تختلف حدود معدل API حسب الخطة:
            </p>
            <ul style="color: var(--text-gray); padding-left: 20px; margin-top: 15px;">
                <li class="content-en" style="margin-bottom: 10px;">Starter: 1,000 requests/hour</li>
                <li class="content-en" style="margin-bottom: 10px;">Professional: 10,000 requests/hour</li>
                <li class="content-en" style="margin-bottom: 10px;">Enterprise: Custom limits</li>

                <li class="content-ar" style="margin-bottom: 10px;">المبتدئ: 1,000 طلب/ساعة</li>
                <li class="content-ar" style="margin-bottom: 10px;">المحترف: 10,000 طلب/ساعة</li>
                <li class="content-ar" style="margin-bottom: 10px;">المؤسسات: حدود مخصصة</li>
            </ul>
        </div>
    </div>
</section>
@endsection
