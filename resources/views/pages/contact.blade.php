@extends('layouts.public')

@section('title', 'Contact Us')

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="page-title content-en">Get In Touch</h1>
        <h1 class="page-title content-ar">تواصل معنا</h1>
        <p class="page-subtitle content-en">
            We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
        <p class="page-subtitle content-ar">
            يسعدنا سماع رأيك. أرسل لنا رسالة وسنرد في أقرب وقت ممكن.
        </p>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <style>
            .contact-container {
                max-width: 1100px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 50px;
            }

            .contact-info {
                background: var(--dark-card);
                padding: 50px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
            }

            .contact-info h2 {
                font-size: 2rem;
                margin-bottom: 30px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .info-item {
                display: flex;
                align-items: flex-start;
                gap: 20px;
                margin-bottom: 30px;
            }

            .info-icon {
                font-size: 2rem;
                flex-shrink: 0;
            }

            .info-content h3 {
                font-size: 1.2rem;
                color: var(--text-light);
                margin-bottom: 8px;
            }

            .info-content p {
                color: var(--text-gray);
                line-height: 1.6;
            }

            .contact-form {
                background: var(--dark-card);
                padding: 50px;
                border-radius: 20px;
                border: 1px solid rgba(99, 102, 241, 0.15);
            }

            .contact-form h2 {
                font-size: 2rem;
                margin-bottom: 30px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .form-group {
                margin-bottom: 25px;
            }

            .form-group label {
                display: block;
                color: var(--text-light);
                margin-bottom: 10px;
                font-weight: 600;
            }

            .form-group input,
            .form-group textarea {
                width: 100%;
                padding: 15px;
                background: var(--dark-bg);
                border: 1px solid rgba(99, 102, 241, 0.2);
                border-radius: 10px;
                color: var(--text-light);
                font-size: 1rem;
                font-family: inherit;
                transition: all 0.3s ease;
            }

            .form-group input:focus,
            .form-group textarea:focus {
                outline: none;
                border-color: var(--primary-blue);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            }

            .form-group textarea {
                min-height: 150px;
                resize: vertical;
            }

            .submit-button {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
                color: white;
                border: none;
                border-radius: 10px;
                font-weight: 700;
                font-size: 1.1rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .submit-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
            }

            @media (max-width: 968px) {
                .contact-container {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="contact-container">
            <div class="contact-info">
                <h2 class="content-en">Contact Information</h2>
                <h2 class="content-ar">معلومات الاتصال</h2>

                <div class="info-item">
                    <span class="info-icon">📧</span>
                    <div class="info-content">
                        <h3 class="content-en">Email</h3>
                        <h3 class="content-ar">البريد الإلكتروني</h3>
                        <p>support@mediapro.com</p>
                        <p>sales@mediapro.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-icon">💬</span>
                    <div class="info-content">
                        <h3 class="content-en">Live Chat</h3>
                        <h3 class="content-ar">الدردشة المباشرة</h3>
                        <p class="content-en">Available 24/7 for instant support</p>
                        <p class="content-ar">متاح على مدار الساعة طوال أيام الأسبوع للدعم الفوري</p>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-icon">🌍</span>
                    <div class="info-content">
                        <h3 class="content-en">Office</h3>
                        <h3 class="content-ar">المكتب</h3>
                        <p class="content-en">123 Innovation Street<br>Tech Valley, CA 94025<br>United States</p>
                        <p class="content-ar">123 شارع الابتكار<br>وادي التقنية، كاليفورنيا 94025<br>الولايات المتحدة</p>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-icon">⏰</span>
                    <div class="info-content">
                        <h3 class="content-en">Business Hours</h3>
                        <h3 class="content-ar">ساعات العمل</h3>
                        <p class="content-en">Monday - Friday: 9:00 AM - 6:00 PM PST<br>Weekend: Email support only</p>
                        <p class="content-ar">الإثنين - الجمعة: 9:00 صباحاً - 6:00 مساءً بتوقيت المحيط الهادئ<br>عطلة نهاية الأسبوع: دعم البريد الإلكتروني فقط</p>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h2 class="content-en">Send Us a Message</h2>
                <h2 class="content-ar">أرسل لنا رسالة</h2>

                <form action="#" method="POST">
                    <div class="form-group">
                        <label class="content-en">Name</label>
                        <label class="content-ar">الاسم</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-group">
                        <label class="content-en">Email</label>
                        <label class="content-ar">البريد الإلكتروني</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label class="content-en">Subject</label>
                        <label class="content-ar">الموضوع</label>
                        <input type="text" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label class="content-en">Message</label>
                        <label class="content-ar">الرسالة</label>
                        <textarea name="message" required></textarea>
                    </div>

                    <button type="submit" class="submit-button content-en">Send Message →</button>
                    <button type="submit" class="submit-button content-ar">إرسال الرسالة ←</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
