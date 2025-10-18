@extends('layouts.admin')

@section('title', __('Settings'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
        background: #0f172a;
        color: #e2e8f0;
        overflow-x: hidden;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
    }

    @media (min-width: 768px) {
        .dashboard-container {
            padding: 2rem;
        }
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        position: relative;
        overflow: hidden;
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        {{ app()->getLocale() === 'ar' ? 'left: -20%;' : 'right: -20%;' }}
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 1.875rem;
        font-weight: 900;
        color: #f8fafc;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    @media (min-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
    }

    .hero-gradient-text {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 900;
    }

    .hero-subtitle {
        color: #94a3b8;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (min-width: 768px) {
        .hero-subtitle {
            font-size: 1.125rem;
        }
    }

    /* Cards */
    .settings-card {
        background: #1e293b;
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    @media (min-width: 768px) {
        .settings-card {
            padding: 2rem;
        }
    }

    .settings-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    @media (min-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }
    }

    .section-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-icon svg {
        width: 1.25rem;
        height: 1.25rem;
        color: white;
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #e2e8f0;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(148, 163, 184, 0.05);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 0.75rem;
        color: #f8fafc;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        background: rgba(99, 102, 241, 0.05);
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-input:disabled {
        background: rgba(148, 163, 184, 0.03);
        color: #64748b;
        cursor: not-allowed;
    }

    /* Toggle Switch */
    .toggle-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: rgba(148, 163, 184, 0.05);
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .toggle-container:hover {
        background: rgba(99, 102, 241, 0.05);
        border-color: rgba(99, 102, 241, 0.2);
    }

    .toggle-info h3 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 0.25rem;
    }

    .toggle-info p {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Custom Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 3.5rem;
        height: 1.75rem;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(148, 163, 184, 0.2);
        transition: 0.3s;
        border-radius: 1.75rem;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 1.25rem;
        width: 1.25rem;
        left: 0.25rem;
        bottom: 0.25rem;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(1.75rem);
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px -5px rgba(99, 102, 241, 0.6);
    }

    .btn-secondary {
        background: rgba(148, 163, 184, 0.1);
        color: #e2e8f0;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .btn-secondary:hover {
        background: rgba(148, 163, 184, 0.15);
        border-color: rgba(148, 163, 184, 0.3);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.active {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .status-badge.inactive {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .status-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background-color: currentColor;
    }

    .status-dot.pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Alert */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        display: none;
        align-items: center;
        gap: 0.75rem;
    }

    .alert.show {
        display: flex;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
    }

    .alert svg {
        width: 1.25rem;
        height: 1.25rem;
        flex-shrink: 0;
    }

    /* Grid Layout */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 1024px) {
        .main-grid {
            grid-template-columns: 2fr 1fr;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }

    .animate-in-1 { animation-delay: 0.1s; animation-fill-mode: both; }
    .animate-in-2 { animation-delay: 0.2s; animation-fill-mode: both; }
    .animate-in-3 { animation-delay: 0.3s; animation-fill-mode: both; }
    .animate-in-4 { animation-delay: 0.4s; animation-fill-mode: both; }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Hero Banner -->
    <div class="hero-banner animate-in animate-in-1">
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="hero-gradient-text">{{ app()->getLocale() === 'ar' ? '⚙️ إعدادات' : '⚙️ Settings' }}</span> {{ app()->getLocale() === 'ar' ? 'التطبيق' : '' }}
            </h1>
            <p class="hero-subtitle">{{ app()->getLocale() === 'ar' ? 'إدارة إعدادات النظام والتخصيصات' : 'Manage system settings and customizations' }}</p>
        </div>
    </div>

    <!-- Success Alert -->
    <div id="successAlert" class="alert alert-success">
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span id="successText"></span>
    </div>

    <!-- Main Grid -->
    <div class="main-grid">
        <!-- Settings Form -->
        <div>
            <form id="settingsForm" enctype="multipart/form-data">
                @csrf

                <!-- App Information Section -->
                <div class="settings-card animate-in animate-in-2">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'معلومات التطبيق' : 'Application Information' }}
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'اسم التطبيق' : 'Application Name' }}
                        </label>
                        <div style="display: flex; gap: 0.75rem; align-items: start;">
                            <input type="text" name="app_name" value="{{ $settings['app_name'] }}" class="form-input" placeholder="Media Pro" style="flex: 1;">
                            <button type="button" id="saveAppName" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; white-space: nowrap; transition: all 0.3s ease;">
                                <i class="fas fa-save" style="{{ app()->getLocale() === 'ar' ? 'margin-left: 0.5rem;' : 'margin-right: 0.5rem;' }}"></i>
                                {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                            </button>
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'اسم التطبيق سيظهر في جميع أنحاء لوحة التحكم' : 'Application name will appear throughout the admin panel' }}
                        </p>
                    </div>

                    <!-- App Logo Upload -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'شعار التطبيق (Logo)' : 'Application Logo' }}
                        </label>

                        @if(file_exists(public_path('storage/logo.png')) || file_exists(public_path('storage/logo.jpg')) || file_exists(public_path('storage/logo.svg')))
                        <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 0.75rem;">
                            <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 0.75rem;">
                                {{ app()->getLocale() === 'ar' ? 'الشعار الحالي:' : 'Current Logo:' }}
                            </p>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="{{ asset('storage/logo.png') }}?v={{ time() }}"
                                     alt="App Logo"
                                     style="max-width: 120px; max-height: 60px; object-fit: contain; background: white; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid rgba(148, 163, 184, 0.2);"
                                     onerror="this.src='{{ asset('storage/logo.jpg') }}'; this.onerror=function(){this.src='{{ asset('storage/logo.svg') }}'};">
                                <button type="button" onclick="document.getElementById('logo_file').click()"
                                        style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.875rem;">
                                    <i class="fas fa-sync-alt" style="margin-left: 0.5rem;"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تغيير الشعار' : 'Change Logo' }}
                                </button>
                            </div>
                        </div>
                        @endif

                        <div style="position: relative; border: 2px dashed rgba(148, 163, 184, 0.3); border-radius: 0.75rem; padding: 2rem; text-align: center; background: rgba(148, 163, 184, 0.05); cursor: pointer;"
                             onclick="document.getElementById('logo_file').click()"
                             id="logoDropZone">
                            <input type="file" name="app_logo" id="logo_file" accept="image/*" style="display: none;" onchange="handleLogoPreview(this)">
                            <div id="logoUploadContent">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #6366f1; margin-bottom: 1rem;"></i>
                                <p style="color: #f8fafc; font-size: 1rem; margin-bottom: 0.5rem;">
                                    {{ app()->getLocale() === 'ar' ? 'انقر للرفع أو اسحب الصورة هنا' : 'Click to upload or drag and drop' }}
                                </p>
                                <p style="color: #94a3b8; font-size: 0.875rem;">
                                    PNG, JPG, SVG {{ app()->getLocale() === 'ar' ? '(الحد الأقصى: 2MB)' : '(Max: 2MB)' }}
                                </p>
                            </div>
                            <div id="logoPreview" style="display: none;">
                                <img id="logoPreviewImg" src="" alt="Preview" style="max-width: 200px; max-height: 100px; object-fit: contain; margin-bottom: 1rem;">
                                <p style="color: #10b981; font-size: 0.875rem;">
                                    <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>
                                    <span id="logoFileName"></span>
                                </p>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar'
                                ? 'الشعار سيظهر في الجزء العلوي من لوحة التحكم. الحجم الموصى به: 200×60 بكسل'
                                : 'Logo will appear at the top of the admin panel. Recommended size: 200×60 pixels' }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'رابط التطبيق' : 'Application URL' }}
                        </label>
                        <input type="text" value="{{ $settings['app_url'] }}" readonly class="form-input" disabled>
                    </div>
                </div>

                <!-- Localization Section -->
                <div class="settings-card animate-in animate-in-3">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #8b5cf6, #a855f7);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'الإعدادات الإقليمية' : 'Localization Settings' }}
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'اللغة الافتراضية' : 'Default Language' }}
                        </label>
                        <div style="display: flex; gap: 0.75rem; align-items: start;">
                            <select name="default_language" class="form-input" style="flex: 1;">
                                <option value="ar" {{ $settings['default_language'] === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                <option value="en" {{ $settings['default_language'] === 'en' ? 'selected' : '' }}>English</option>
                            </select>
                            <button type="button" id="saveLanguage" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; white-space: nowrap; transition: all 0.3s ease;">
                                <i class="fas fa-save" style="{{ app()->getLocale() === 'ar' ? 'margin-left: 0.5rem;' : 'margin-right: 0.5rem;' }}"></i>
                                {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'المنطقة الزمنية' : 'Timezone' }}
                        </label>
                        <select name="timezone" class="form-input">
                            <option value="Asia/Dubai" {{ $settings['timezone'] === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (UAE)</option>
                            <option value="Asia/Riyadh" {{ $settings['timezone'] === 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh (KSA)</option>
                            <option value="Africa/Cairo" {{ $settings['timezone'] === 'Africa/Cairo' ? 'selected' : '' }}>Africa/Cairo (Egypt)</option>
                            <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'العملة الافتراضية' : 'Default Currency' }}
                        </label>
                        <div style="display: flex; gap: 0.75rem; align-items: start;">
                            <select name="currency" class="form-input" style="flex: 1;">
                                @foreach($currencies as $code => $currency)
                                    <option value="{{ $code }}" {{ $settings['currency'] === $code ? 'selected' : '' }}>
                                        {{ $code }} ({{ $currency['symbol'] }} - {{ app()->getLocale() === 'ar' ? $currency['name_ar'] : $currency['name'] }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="saveCurrency" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; white-space: nowrap; transition: all 0.3s ease;">
                                <i class="fas fa-save" style="{{ app()->getLocale() === 'ar' ? 'margin-left: 0.5rem;' : 'margin-right: 0.5rem;' }}"></i>
                                {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                            </button>
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'العملة المستخدمة في خطط الاشتراك والمدفوعات' : 'Currency used for subscription plans and payments' }}
                        </p>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="settings-card animate-in animate-in-4">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'إعدادات الإشعارات' : 'Notification Settings' }}
                    </div>

                    <div class="toggle-container">
                        <div class="toggle-info">
                            <h3>{{ app()->getLocale() === 'ar' ? 'إشعارات البريد الإلكتروني' : 'Email Notifications' }}</h3>
                            <p>{{ app()->getLocale() === 'ar' ? 'إرسال إشعارات عبر البريد' : 'Send notifications via email' }}</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="toggle-container">
                        <div class="toggle-info">
                            <h3>{{ app()->getLocale() === 'ar' ? 'الإشعارات الفورية' : 'Push Notifications' }}</h3>
                            <p>{{ app()->getLocale() === 'ar' ? 'إشعارات فورية للتطبيق' : 'Send push notifications to app' }}</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="push_notifications" {{ $settings['push_notifications'] ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- AI Providers Section -->
                <div class="settings-card animate-in animate-in-4">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'إعدادات أدوات الذكاء الاصطناعي' : 'AI Tools Configuration' }}
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'OpenAI API Key' : 'OpenAI API Key' }}
                        </label>
                        <input type="password" name="openai_api_key" value="{{ env('OPENAI_API_KEY') }}" class="form-input" placeholder="sk-...">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'استخدم لتوليد النصوص والمحتوى والصور' : 'Used for text generation, content, and images' }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'OpenAI Model' : 'OpenAI Model' }}
                        </label>
                        <select name="openai_model" class="form-input">
                            <option value="gpt-4o-mini" {{ env('OPENAI_MODEL', 'gpt-4o-mini') === 'gpt-4o-mini' ? 'selected' : '' }}>GPT-4o Mini</option>
                            <option value="gpt-4" {{ env('OPENAI_MODEL') === 'gpt-4' ? 'selected' : '' }}>GPT-4</option>
                            <option value="gpt-4-turbo-preview" {{ env('OPENAI_MODEL') === 'gpt-4-turbo-preview' ? 'selected' : '' }}>GPT-4 Turbo</option>
                            <option value="gpt-3.5-turbo" {{ env('OPENAI_MODEL') === 'gpt-3.5-turbo' ? 'selected' : '' }}>GPT-3.5 Turbo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'الحد الأقصى للرموز (Tokens)' : 'Max Tokens' }}
                        </label>
                        <input type="number" name="openai_max_tokens" value="{{ env('OPENAI_MAX_TOKENS', 2000) }}" class="form-input" placeholder="2000" min="100" max="8000">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Gemini API Key' : 'Gemini API Key' }}
                        </label>
                        <input type="password" name="gemini_api_key" value="{{ env('GEMINI_API_KEY') }}" class="form-input" placeholder="AIza...">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'مفتاح Google Gemini API' : 'Google Gemini API Key' }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Gemini Model' : 'Gemini Model' }}
                        </label>
                        <select name="gemini_model" class="form-input">
                            <option value="gemini-1.5-flash" {{ env('GEMINI_MODEL', 'gemini-1.5-flash') === 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash</option>
                            <option value="gemini-1.5-pro" {{ env('GEMINI_MODEL') === 'gemini-1.5-pro' ? 'selected' : '' }}>Gemini 1.5 Pro</option>
                            <option value="gemini-pro" {{ env('GEMINI_MODEL') === 'gemini-pro' ? 'selected' : '' }}>Gemini Pro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Claude API Key' : 'Claude API Key' }}
                        </label>
                        <input type="password" name="claude_api_key" value="{{ env('CLAUDE_API_KEY') }}" class="form-input" placeholder="sk-ant-...">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'مفتاح Anthropic Claude API' : 'Anthropic Claude API Key' }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Claude Model' : 'Claude Model' }}
                        </label>
                        <select name="claude_model" class="form-input">
                            <option value="claude-3-5-sonnet-20241022" {{ env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022') === 'claude-3-5-sonnet-20241022' ? 'selected' : '' }}>Claude 3.5 Sonnet</option>
                            <option value="claude-3-opus-20240229" {{ env('CLAUDE_MODEL') === 'claude-3-opus-20240229' ? 'selected' : '' }}>Claude 3 Opus</option>
                            <option value="claude-3-sonnet-20240229" {{ env('CLAUDE_MODEL') === 'claude-3-sonnet-20240229' ? 'selected' : '' }}>Claude 3 Sonnet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Claude Max Tokens' : 'Claude Max Tokens' }}
                        </label>
                        <input type="number" name="claude_max_tokens" value="{{ env('CLAUDE_MAX_TOKENS', 4096) }}" class="form-input" placeholder="4096" min="100" max="8000">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'مزود الذكاء الاصطناعي الافتراضي' : 'Default AI Provider' }}
                        </label>
                        <select name="default_ai_provider" class="form-input">
                            <option value="openai" {{ env('DEFAULT_AI_PROVIDER', 'openai') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                            <option value="gemini" {{ env('DEFAULT_AI_PROVIDER') === 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                            <option value="claude" {{ env('DEFAULT_AI_PROVIDER') === 'claude' ? 'selected' : '' }}>Anthropic Claude</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Whisper API Key (تحويل الصوت إلى نص)' : 'Whisper API Key (Voice to Text)' }}
                        </label>
                        <input type="password" name="whisper_api_key" value="{{ env('WHISPER_API_KEY') }}" class="form-input" placeholder="sk-...">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'يمكن استخدام نفس مفتاح OpenAI' : 'Can use same OpenAI API key' }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ app()->getLocale() === 'ar' ? 'Google Cloud Project ID' : 'Google Cloud Project ID' }}
                        </label>
                        <input type="text" name="google_cloud_project_id" value="{{ env('GOOGLE_CLOUD_PROJECT_ID') }}" class="form-input" placeholder="my-project-123">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'مطلوب لخدمات Google Cloud' : 'Required for Google Cloud services' }}
                        </p>
                    </div>

                    <div class="toggle-container">
                        <div class="toggle-info">
                            <h3>{{ app()->getLocale() === 'ar' ? 'تفعيل ميزات AI' : 'Enable AI Features' }}</h3>
                            <p>{{ app()->getLocale() === 'ar' ? 'تفعيل جميع ميزات الذكاء الاصطناعي في التطبيق' : 'Enable all AI features in the application' }}</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="ai_features_enabled" {{ env('AI_FEATURES_ENABLED', true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" id="saveAiSettings" style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-save" style="{{ app()->getLocale() === 'ar' ? 'margin-left: 0.5rem;' : 'margin-right: 0.5rem;' }}"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات AI' : 'Save AI Settings' }}
                        </button>
                    </div>
                </div>

                <!-- Social Media OAuth Section -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #3b82f6, #06b6d4);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'إعدادات OAuth للمنصات الاجتماعية' : 'Social Media OAuth Settings' }}
                    </div>

                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1.5rem;">
                        {{ app()->getLocale() === 'ar'
                            ? 'قم بإعداد OAuth للربط مع منصات التواصل الاجتماعي. احصل على المفاتيح من developer portals الخاصة بكل منصة.'
                            : 'Configure OAuth for social media platform integration. Get credentials from respective developer portals.' }}
                    </p>

                    <!-- Facebook OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-facebook" style="color: #1877f2; margin-left: 0.5rem;"></i>
                        Facebook
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="facebook_client_id" value="{{ env('FACEBOOK_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="facebook_client_secret" value="{{ env('FACEBOOK_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- Instagram OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-instagram" style="color: #e4405f; margin-left: 0.5rem;"></i>
                        Instagram
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="instagram_client_id" value="{{ env('INSTAGRAM_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="instagram_client_secret" value="{{ env('INSTAGRAM_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- Twitter OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-twitter" style="color: #1da1f2; margin-left: 0.5rem;"></i>
                        Twitter / X
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="twitter_client_id" value="{{ env('TWITTER_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="twitter_client_secret" value="{{ env('TWITTER_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- LinkedIn OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-linkedin" style="color: #0077b5; margin-left: 0.5rem;"></i>
                        LinkedIn
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="linkedin_client_id" value="{{ env('LINKEDIN_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="linkedin_client_secret" value="{{ env('LINKEDIN_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- TikTok OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-tiktok" style="color: #000000; margin-left: 0.5rem;"></i>
                        TikTok
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="tiktok_client_id" value="{{ env('TIKTOK_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="tiktok_client_secret" value="{{ env('TIKTOK_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- YouTube OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-youtube" style="color: #ff0000; margin-left: 0.5rem;"></i>
                        YouTube
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="youtube_client_id" value="{{ env('YOUTUBE_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="youtube_client_secret" value="{{ env('YOUTUBE_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- Pinterest OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-pinterest" style="color: #bd081c; margin-left: 0.5rem;"></i>
                        Pinterest
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="pinterest_client_id" value="{{ env('PINTEREST_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="pinterest_client_secret" value="{{ env('PINTEREST_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- Snapchat OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-snapchat" style="color: #fffc00; margin-left: 0.5rem;"></i>
                        Snapchat
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف التطبيق (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="snapchat_client_id" value="{{ env('SNAPCHAT_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر التطبيق (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="snapchat_client_secret" value="{{ env('SNAPCHAT_CLIENT_SECRET') }}" class="form-input">
                    </div>
                </div>

                <!-- User Authentication OAuth Section -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول عبر Google & Apple' : 'User Login OAuth (Google & Apple)' }}
                    </div>

                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1.5rem;">
                        {{ app()->getLocale() === 'ar'
                            ? 'إعدادات تسجيل الدخول للمستخدمين باستخدام Google أو Apple'
                            : 'User authentication settings using Google or Apple login' }}
                    </p>

                    <!-- Google Login OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-google" style="color: #ea4335; margin-left: 0.5rem;"></i>
                        Google Login
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف العميل (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="google_client_id" value="{{ env('GOOGLE_CLIENT_ID') }}" class="form-input" placeholder="your_google_client_id_here">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'سر العميل (Client Secret)' : 'Client Secret' }}</label>
                        <input type="password" name="google_client_secret" value="{{ env('GOOGLE_CLIENT_SECRET') }}" class="form-input">
                    </div>

                    <!-- Apple Login OAuth -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-apple" style="color: #000000; margin-left: 0.5rem;"></i>
                        Apple Login
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف العميل (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="apple_client_id" value="{{ env('APPLE_CLIENT_ID') }}" class="form-input" placeholder="your_apple_client_id_here">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف الفريق (Team ID)' : 'Team ID' }}</label>
                        <input type="text" name="apple_team_id" value="{{ env('APPLE_TEAM_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف المفتاح (Key ID)' : 'Key ID' }}</label>
                        <input type="text" name="apple_key_id" value="{{ env('APPLE_KEY_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'المفتاح الخاص (Private Key)' : 'Private Key' }}</label>
                        <textarea name="apple_private_key" rows="4" class="form-input" placeholder="-----BEGIN PRIVATE KEY-----">{{ env('APPLE_PRIVATE_KEY') }}</textarea>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'المفتاح الخاص من Apple Developer Console' : 'Private key from Apple Developer Console' }}
                        </p>
                    </div>
                </div>

                <!-- Payment Services Section -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'خدمات الدفع الإلكتروني' : 'Payment Services' }}
                    </div>

                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1.5rem;">
                        {{ app()->getLocale() === 'ar'
                            ? 'إعدادات بوابات الدفع (Stripe و PayPal)'
                            : 'Payment gateway configuration (Stripe & PayPal)' }}
                    </p>

                    <!-- Stripe -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-stripe" style="color: #635bff; margin-left: 0.5rem;"></i>
                        Stripe
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مفتاح Stripe العام (Publishable Key)' : 'Stripe Publishable Key' }}</label>
                        <input type="text" name="stripe_key" value="{{ env('STRIPE_KEY') }}" class="form-input" placeholder="pk_test_...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مفتاح Stripe السري (Secret Key)' : 'Stripe Secret Key' }}</label>
                        <input type="password" name="stripe_secret" value="{{ env('STRIPE_SECRET') }}" class="form-input" placeholder="sk_test_...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مفتاح Webhook السري' : 'Webhook Secret' }}</label>
                        <input type="password" name="stripe_webhook_secret" value="{{ env('STRIPE_WEBHOOK_SECRET') }}" class="form-input" placeholder="whsec_...">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'لتأمين استقبال إشعارات Stripe' : 'For securing Stripe webhook notifications' }}
                        </p>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="button" id="saveStripeSettings" style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-save" style="{{ app()->getLocale() === 'ar' ? 'margin-left: 0.5rem;' : 'margin-right: 0.5rem;' }}"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات Stripe' : 'Save Stripe Settings' }}
                        </button>
                    </div>

                    <!-- PayPal -->
                    <h4 style="color: #f8fafc; font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.2);">
                        <i class="fab fa-paypal" style="color: #0070ba; margin-left: 0.5rem;"></i>
                        PayPal
                    </h4>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'وضع PayPal' : 'PayPal Mode' }}</label>
                        <select name="paypal_mode" class="form-input">
                            <option value="sandbox" {{ env('PAYPAL_MODE', 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox ({{ app()->getLocale() === 'ar' ? 'تجريبي' : 'Testing' }})</option>
                            <option value="live" {{ env('PAYPAL_MODE') === 'live' ? 'selected' : '' }}>Live ({{ app()->getLocale() === 'ar' ? 'إنتاج' : 'Production' }})</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف العميل (Client ID)' : 'Client ID' }}</label>
                        <input type="text" name="paypal_client_id" value="{{ env('PAYPAL_CLIENT_ID') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'السر (Secret)' : 'Secret' }}</label>
                        <input type="password" name="paypal_secret" value="{{ env('PAYPAL_SECRET') }}" class="form-input">
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="button" id="savePaypalSettings" style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-save" style="{{ app()->getLocale() === 'ar' ? 'margin-left: 0.5rem;' : 'margin-right: 0.5rem;' }}"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات PayPal' : 'Save PayPal Settings' }}
                        </button>
                    </div>
                </div>

                <!-- AWS Services Section -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'خدمات Amazon AWS (تخزين الملفات)' : 'Amazon AWS Services (File Storage)' }}
                    </div>

                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1.5rem;">
                        {{ app()->getLocale() === 'ar'
                            ? 'إعدادات AWS S3 لتخزين الصور والفيديوهات والملفات'
                            : 'AWS S3 configuration for storing images, videos, and files' }}
                    </p>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'معرّف الوصول (Access Key ID)' : 'Access Key ID' }}</label>
                        <input type="text" name="aws_access_key_id" value="{{ env('AWS_ACCESS_KEY_ID') }}" class="form-input" placeholder="AKIAIOSFODNN7EXAMPLE">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مفتاح الوصول السري (Secret Access Key)' : 'Secret Access Key' }}</label>
                        <input type="password" name="aws_secret_access_key" value="{{ env('AWS_SECRET_ACCESS_KEY') }}" class="form-input" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'المنطقة (Region)' : 'Region' }}</label>
                        <select name="aws_default_region" class="form-input">
                            <option value="us-east-1" {{ env('AWS_DEFAULT_REGION', 'us-east-1') === 'us-east-1' ? 'selected' : '' }}>US East (N. Virginia)</option>
                            <option value="us-west-2" {{ env('AWS_DEFAULT_REGION') === 'us-west-2' ? 'selected' : '' }}>US West (Oregon)</option>
                            <option value="eu-west-1" {{ env('AWS_DEFAULT_REGION') === 'eu-west-1' ? 'selected' : '' }}>Europe (Ireland)</option>
                            <option value="ap-southeast-1" {{ env('AWS_DEFAULT_REGION') === 'ap-southeast-1' ? 'selected' : '' }}>Asia Pacific (Singapore)</option>
                            <option value="me-south-1" {{ env('AWS_DEFAULT_REGION') === 'me-south-1' ? 'selected' : '' }}>Middle East (Bahrain)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الحاوية (Bucket Name)' : 'Bucket Name' }}</label>
                        <input type="text" name="aws_bucket" value="{{ env('AWS_BUCKET') }}" class="form-input" placeholder="my-app-bucket">
                    </div>
                </div>

                <!-- Database Configuration Section -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'إعدادات قاعدة البيانات' : 'Database Configuration' }}
                    </div>

                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1rem; padding: 1rem; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: 0.75rem;">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 1.125rem; height: 1.125rem; display: inline-block; vertical-align: middle; margin-left: 0.5rem; color: #fbbf24;">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'تحذير: تغيير إعدادات قاعدة البيانات قد يسبب مشاكل في الاتصال' : 'Warning: Changing database settings may cause connection issues' }}
                    </p>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نوع قاعدة البيانات' : 'Database Connection' }}</label>
                        <select name="db_connection" class="form-input">
                            <option value="mysql" {{ env('DB_CONNECTION', 'mysql') === 'mysql' ? 'selected' : '' }}>MySQL</option>
                            <option value="pgsql" {{ env('DB_CONNECTION') === 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
                            <option value="sqlite" {{ env('DB_CONNECTION') === 'sqlite' ? 'selected' : '' }}>SQLite</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عنوان الخادم (Host)' : 'Database Host' }}</label>
                        <input type="text" name="db_host" value="{{ env('DB_HOST', '127.0.0.1') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'المنفذ (Port)' : 'Port' }}</label>
                        <input type="number" name="db_port" value="{{ env('DB_PORT', 3306) }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم قاعدة البيانات' : 'Database Name' }}</label>
                        <input type="text" name="db_database" value="{{ env('DB_DATABASE') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم المستخدم' : 'Username' }}</label>
                        <input type="text" name="db_username" value="{{ env('DB_USERNAME') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                        <input type="password" name="db_password" value="{{ env('DB_PASSWORD') }}" class="form-input">
                    </div>
                </div>

                <!-- Mail Configuration Section -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'إعدادات البريد الإلكتروني' : 'Mail Configuration' }}
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نظام إرسال البريد' : 'Mail Driver' }}</label>
                        <select name="mail_mailer" class="form-input">
                            <option value="smtp" {{ env('MAIL_MAILER', 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="log" {{ env('MAIL_MAILER') === 'log' ? 'selected' : '' }}>Log ({{ app()->getLocale() === 'ar' ? 'للاختبار' : 'Testing' }})</option>
                            <option value="sendmail" {{ env('MAIL_MAILER') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ env('MAIL_MAILER') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'خادم البريد (Host)' : 'Mail Host' }}</label>
                        <input type="text" name="mail_host" value="{{ env('MAIL_HOST', '127.0.0.1') }}" class="form-input" placeholder="smtp.gmail.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'المنفذ (Port)' : 'Port' }}</label>
                        <input type="number" name="mail_port" value="{{ env('MAIL_PORT', 2525) }}" class="form-input" placeholder="587">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم المستخدم' : 'Username' }}</label>
                        <input type="text" name="mail_username" value="{{ env('MAIL_USERNAME') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                        <input type="password" name="mail_password" value="{{ env('MAIL_PASSWORD') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عنوان البريد المرسل' : 'From Address' }}</label>
                        <input type="email" name="mail_from_address" value="{{ env('MAIL_FROM_ADDRESS', 'hello@example.com') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم المرسل' : 'From Name' }}</label>
                        <input type="text" name="mail_from_name" value="{{ env('MAIL_FROM_NAME') }}" class="form-input" placeholder="Media Pro">
                    </div>
                </div>

                <!-- Environment Variables Editor -->
                <div class="settings-card animate-in animate-in-5">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'متغيرات البيئة (.env)' : 'Environment Variables (.env)' }}
                    </div>

                    <p style="font-size: 0.8125rem; color: #94a3b8; margin-bottom: 1.5rem; padding: 1rem; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: 0.75rem;">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 1.125rem; height: 1.125rem; display: inline-block; vertical-align: middle; margin-left: 0.5rem; color: #fbbf24;">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'تحذير: تأكد من صحة القيم قبل الحفظ - قد يتطلب إعادة تشغيل الخادم' : 'Warning: Verify values before saving - server restart may be required' }}
                    </p>

                    <div id="envVariables" style="display: grid; gap: 1rem;">
                        @php
                            $envLines = explode("\n", $envContent ?? '');
                            $envVars = [];
                            foreach ($envLines as $line) {
                                $line = trim($line);
                                if (empty($line) || strpos($line, '#') === 0) continue;
                                if (strpos($line, '=') !== false) {
                                    list($key, $value) = explode('=', $line, 2);
                                    $envVars[$key] = trim($value, '"\'');
                                }
                            }
                        @endphp

                        @foreach([
                            'APP_NAME' => ['label' => 'Application Name', 'labelAr' => 'اسم التطبيق', 'type' => 'text'],
                            'APP_ENV' => ['label' => 'Environment', 'labelAr' => 'البيئة', 'type' => 'select', 'options' => ['local', 'production', 'staging']],
                            'APP_DEBUG' => ['label' => 'Debug Mode', 'labelAr' => 'وضع التطوير', 'type' => 'select', 'options' => ['true', 'false']],
                            'APP_URL' => ['label' => 'Application URL', 'labelAr' => 'رابط التطبيق', 'type' => 'url'],
                            'DB_CONNECTION' => ['label' => 'Database Connection', 'labelAr' => 'نوع قاعدة البيانات', 'type' => 'text'],
                            'DB_HOST' => ['label' => 'Database Host', 'labelAr' => 'عنوان قاعدة البيانات', 'type' => 'text'],
                            'DB_PORT' => ['label' => 'Database Port', 'labelAr' => 'منفذ قاعدة البيانات', 'type' => 'number'],
                            'DB_DATABASE' => ['label' => 'Database Name', 'labelAr' => 'اسم قاعدة البيانات', 'type' => 'text'],
                            'DB_USERNAME' => ['label' => 'Database Username', 'labelAr' => 'اسم مستخدم قاعدة البيانات', 'type' => 'text'],
                            'DB_PASSWORD' => ['label' => 'Database Password', 'labelAr' => 'كلمة مرور قاعدة البيانات', 'type' => 'password'],
                            'MAIL_MAILER' => ['label' => 'Mail Driver', 'labelAr' => 'نظام البريد', 'type' => 'select', 'options' => ['smtp', 'sendmail', 'mailgun', 'ses']],
                            'MAIL_HOST' => ['label' => 'Mail Host', 'labelAr' => 'عنوان خادم البريد', 'type' => 'text'],
                            'MAIL_PORT' => ['label' => 'Mail Port', 'labelAr' => 'منفذ البريد', 'type' => 'number'],
                            'MAIL_USERNAME' => ['label' => 'Mail Username', 'labelAr' => 'اسم مستخدم البريد', 'type' => 'text'],
                            'MAIL_PASSWORD' => ['label' => 'Mail Password', 'labelAr' => 'كلمة مرور البريد', 'type' => 'password'],
                            'MAIL_FROM_ADDRESS' => ['label' => 'Mail From Address', 'labelAr' => 'البريد المرسل', 'type' => 'email'],
                        ] as $key => $config)
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.75rem;">
                                {{ app()->getLocale() === 'ar' ? $config['labelAr'] : $config['label'] }}
                                <span style="color: #64748b; font-weight: 400; margin-right: 0.5rem;">({{ $key }})</span>
                            </label>
                            @if($config['type'] === 'select')
                                <select name="env[{{ $key }}]" class="form-input env-var-input" data-key="{{ $key }}">
                                    @foreach($config['options'] as $option)
                                        <option value="{{ $option }}" {{ ($envVars[$key] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="{{ $config['type'] }}"
                                    name="env[{{ $key }}]"
                                    value="{{ $envVars[$key] ?? '' }}"
                                    class="form-input env-var-input"
                                    data-key="{{ $key }}"
                                    placeholder="{{ app()->getLocale() === 'ar' ? $config['labelAr'] : $config['label'] }}"
                                >
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(148, 163, 184, 0.1);">
                        <button type="button" id="saveEnvBtn" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}
                        </button>
                        <button type="button" id="reloadEnvBtn" class="btn btn-secondary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ app()->getLocale() === 'ar' ? 'إعادة تعيين' : 'Reset' }}
                        </button>
                    </div>
                </div>

                <!-- Save Info Notice -->
                <div style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 1.25rem; height: 1.25rem; color: #6366f1; flex-shrink: 0; margin-top: 0.125rem;">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div style="flex: 1;">
                            <p style="color: #c7d2fe; font-size: 0.875rem; line-height: 1.5;">
                                {{ app()->getLocale() === 'ar'
                                    ? 'عند حفظ التغييرات، سيتم تحديث الإعدادات في قاعدة البيانات ومفاتيح API في ملف التطبيق (.env). سيتم إنشاء نسخة احتياطية تلقائياً.'
                                    : 'When saving changes, settings will be updated in the database and API keys in the application file (.env). A backup will be created automatically.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'حفظ جميع التغييرات' : 'Save All Changes' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- System Status -->
            <div class="settings-card animate-in animate-in-2">
                <div class="section-title">
                    <div class="section-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    {{ app()->getLocale() === 'ar' ? 'حالة النظام' : 'System Status' }}
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-between; padding: 0.875rem 1rem; background: rgba(148, 163, 184, 0.05); border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 0.75rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: #cbd5e1;">OpenAI API</span>
                        @if($settings['openai_enabled'])
                            <span class="status-badge active">
                                <span class="status-dot pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'مفعّل' : 'Active' }}
                            </span>
                        @else
                            <span class="status-badge inactive">
                                <span class="status-dot"></span>
                                {{ app()->getLocale() === 'ar' ? 'معطّل' : 'Inactive' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-between; padding: 0.875rem 1rem; background: rgba(148, 163, 184, 0.05); border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 0.75rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: #cbd5e1;">{{ app()->getLocale() === 'ar' ? 'وضع الصيانة' : 'Maintenance' }}</span>
                        @if($settings['maintenance_mode'])
                            <span class="status-badge" style="background: rgba(251, 191, 36, 0.15); color: #fbbf24;">
                                <span class="status-dot pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'مفعّل' : 'ON' }}
                            </span>
                        @else
                            <span class="status-badge active">
                                <span class="status-dot"></span>
                                {{ app()->getLocale() === 'ar' ? 'معطّل' : 'OFF' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="settings-card animate-in animate-in-3">
                <div class="section-title">
                    <div class="section-icon" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    {{ app()->getLocale() === 'ar' ? 'إجراءات سريعة' : 'Quick Actions' }}
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button id="clearCacheBtn" class="btn btn-secondary" style="width: 100%;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'مسح الذاكرة المؤقتة' : 'Clear Cache' }}
                    </button>

                    <button id="toggleMaintenanceBtn" class="btn btn-secondary" style="width: 100%;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'تبديل وضع الصيانة' : 'Toggle Maintenance' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Settings Form Submit
document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    // Handle checkboxes
    if (!formData.has('email_notifications')) formData.append('email_notifications', 'false');
    if (!formData.has('push_notifications')) formData.append('push_notifications', 'false');
    if (!formData.has('ai_features_enabled')) formData.append('ai_features_enabled', 'false');

    try {
        const response = await fetch('/admin/settings', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();
        if (result.success) {
            showSuccessMessage(result.message);
            // Reload page after 2 seconds to show new logo
            setTimeout(() => location.reload(), 2000);
        } else {
            alert('{{ app()->getLocale() === "ar" ? "خطأ: " : "Error: " }}' + (result.message || '{{ app()->getLocale() === "ar" ? "فشل الحفظ" : "Failed to save" }}'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('{{ app()->getLocale() === "ar" ? "حدث خطأ أثناء الحفظ" : "An error occurred while saving" }}');
    }
});

// ============================================================================
// Individual Setting Save Function (AJAX)
// ============================================================================
async function saveSingleSetting(settingKey, settingValue, settingGroup = 'general', buttonElement = null) {
    // Show loading state on button if provided
    let originalButtonContent = '';
    if (buttonElement) {
        originalButtonContent = buttonElement.innerHTML;
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' +
            ({{ app()->getLocale() === 'ar' ? '"جاري الحفظ..."' : '"Saving..."' }});
    }

    try {
        const response = await fetch('/admin/settings/update-single', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                key: settingKey,
                value: settingValue,
                group: settingGroup
            })
        });

        const result = await response.json();

        if (result.success) {
            showSuccessMessage(result.message);

            // Reset button state with success indicator
            if (buttonElement) {
                buttonElement.innerHTML = '<i class="fas fa-check"></i> ' +
                    ({{ app()->getLocale() === 'ar' ? '"تم الحفظ"' : '"Saved"' }});
                setTimeout(() => {
                    buttonElement.innerHTML = originalButtonContent;
                    buttonElement.disabled = false;
                }, 2000);
            }
        } else {
            alert('{{ app()->getLocale() === "ar" ? "خطأ: " : "Error: " }}' + (result.message || '{{ app()->getLocale() === "ar" ? "فشل الحفظ" : "Failed to save" }}'));

            // Reset button state
            if (buttonElement) {
                buttonElement.innerHTML = originalButtonContent;
                buttonElement.disabled = false;
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('{{ app()->getLocale() === "ar" ? "حدث خطأ أثناء الحفظ" : "An error occurred while saving" }}');

        // Reset button state
        if (buttonElement) {
            buttonElement.innerHTML = originalButtonContent;
            buttonElement.disabled = false;
        }
    }
}

// ============================================================================
// Individual Save Buttons Event Handlers
// ============================================================================

// App Name Save
if (document.getElementById('saveAppName')) {
    document.getElementById('saveAppName').addEventListener('click', function() {
        const value = document.querySelector('input[name="app_name"]').value;
        saveSingleSetting('app_name', value, 'general', this);
    });
}

// Default Language Save
if (document.getElementById('saveLanguage')) {
    document.getElementById('saveLanguage').addEventListener('click', function() {
        const value = document.querySelector('select[name="default_language"]').value;
        saveSingleSetting('default_language', value, 'localization', this);
    });
}

// Currency Save
if (document.getElementById('saveCurrency')) {
    document.getElementById('saveCurrency').addEventListener('click', function() {
        const value = document.querySelector('select[name="currency"]').value;
        saveSingleSetting('currency', value, 'localization', this);
    });
}

// Email Notifications Save
if (document.getElementById('saveEmailNotif')) {
    document.getElementById('saveEmailNotif').addEventListener('click', function() {
        const value = document.querySelector('input[name="enable_email_notifications"]').checked;
        saveSingleSetting('enable_email_notifications', value, 'notifications', this);
    });
}

// Push Notifications Save
if (document.getElementById('savePushNotif')) {
    document.getElementById('savePushNotif').addEventListener('click', function() {
        const value = document.querySelector('input[name="enable_push_notifications"]').checked;
        saveSingleSetting('enable_push_notifications', value, 'notifications', this);
    });
}

// SMS Notifications Save
if (document.getElementById('saveSmsNotif')) {
    document.getElementById('saveSmsNotif').addEventListener('click', function() {
        const value = document.querySelector('input[name="enable_sms_notifications"]').checked;
        saveSingleSetting('enable_sms_notifications', value, 'notifications', this);
    });
}

// SMTP Settings Save (All at once for this group)
if (document.getElementById('saveSmtpSettings')) {
    document.getElementById('saveSmtpSettings').addEventListener('click', async function() {
        const settings = {
            smtp_host: document.querySelector('input[name="smtp_host"]')?.value || '',
            smtp_port: document.querySelector('input[name="smtp_port"]')?.value || '',
            smtp_username: document.querySelector('input[name="smtp_username"]')?.value || '',
            smtp_password: document.querySelector('input[name="smtp_password"]')?.value || '',
            smtp_encryption: document.querySelector('select[name="smtp_encryption"]')?.value || '',
            smtp_from_address: document.querySelector('input[name="smtp_from_address"]')?.value || '',
            smtp_from_name: document.querySelector('input[name="smtp_from_name"]')?.value || ''
        };

        const originalContent = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + ({{ app()->getLocale() === 'ar' ? '"جاري الحفظ..."' : '"Saving..."' }});

        try {
            for (const [key, value] of Object.entries(settings)) {
                await saveSingleSetting(key, value, 'email', null);
            }

            showSuccessMessage({{ app()->getLocale() === 'ar' ? '"تم حفظ إعدادات SMTP بنجاح"' : '"SMTP settings saved successfully"' }});
            this.innerHTML = '<i class="fas fa-check"></i> ' + ({{ app()->getLocale() === 'ar' ? '"تم الحفظ"' : '"Saved"' }});

            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        } catch (error) {
            this.innerHTML = originalContent;
            this.disabled = false;
        }
    });
}

// Stripe Settings Save
if (document.getElementById('saveStripeSettings')) {
    document.getElementById('saveStripeSettings').addEventListener('click', async function() {
        const settings = {
            stripe_publishable_key: document.querySelector('input[name="stripe_publishable_key"]')?.value || '',
            stripe_secret_key: document.querySelector('input[name="stripe_secret_key"]')?.value || '',
            stripe_webhook_secret: document.querySelector('input[name="stripe_webhook_secret"]')?.value || ''
        };

        const originalContent = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + ({{ app()->getLocale() === 'ar' ? '"جاري الحفظ..."' : '"Saving..."' }});

        try {
            for (const [key, value] of Object.entries(settings)) {
                await saveSingleSetting(key, value, 'payments', null);
            }

            showSuccessMessage({{ app()->getLocale() === 'ar' ? '"تم حفظ إعدادات Stripe بنجاح"' : '"Stripe settings saved successfully"' }});
            this.innerHTML = '<i class="fas fa-check"></i> ' + ({{ app()->getLocale() === 'ar' ? '"تم الحفظ"' : '"Saved"' }});

            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        } catch (error) {
            this.innerHTML = originalContent;
            this.disabled = false;
        }
    });
}

// PayPal Settings Save
if (document.getElementById('savePaypalSettings')) {
    document.getElementById('savePaypalSettings').addEventListener('click', async function() {
        const settings = {
            paypal_mode: document.querySelector('select[name="paypal_mode"]')?.value || '',
            paypal_client_id: document.querySelector('input[name="paypal_client_id"]')?.value || '',
            paypal_secret: document.querySelector('input[name="paypal_secret"]')?.value || ''
        };

        const originalContent = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + ({{ app()->getLocale() === 'ar' ? '"جاري الحفظ..."' : '"Saving..."' }});

        try {
            for (const [key, value] of Object.entries(settings)) {
                await saveSingleSetting(key, value, 'payments', null);
            }

            showSuccessMessage({{ app()->getLocale() === 'ar' ? '"تم حفظ إعدادات PayPal بنجاح"' : '"PayPal settings saved successfully"' }});
            this.innerHTML = '<i class="fas fa-check"></i> ' + ({{ app()->getLocale() === 'ar' ? '"تم الحفظ"' : '"Saved"' }});

            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        } catch (error) {
            this.innerHTML = originalContent;
            this.disabled = false;
        }
    });
}

// AI Settings Save
if (document.getElementById('saveAiSettings')) {
    document.getElementById('saveAiSettings').addEventListener('click', async function() {
        const settings = {
            openai_api_key: document.querySelector('input[name="openai_api_key"]')?.value || '',
            gemini_api_key: document.querySelector('input[name="gemini_api_key"]')?.value || '',
            claude_api_key: document.querySelector('input[name="claude_api_key"]')?.value || '',
            default_ai_provider: document.querySelector('select[name="default_ai_provider"]')?.value || ''
        };

        const originalContent = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + ({{ app()->getLocale() === 'ar' ? '"جاري الحفظ..."' : '"Saving..."' }});

        try {
            for (const [key, value] of Object.entries(settings)) {
                await saveSingleSetting(key, value, 'ai', null);
            }

            showSuccessMessage({{ app()->getLocale() === 'ar' ? '"تم حفظ إعدادات AI بنجاح"' : '"AI settings saved successfully"' }});
            this.innerHTML = '<i class="fas fa-check"></i> ' + ({{ app()->getLocale() === 'ar' ? '"تم الحفظ"' : '"Saved"' }});

            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        } catch (error) {
            this.innerHTML = originalContent;
            this.disabled = false;
        }
    });
}

// Clear Cache
document.getElementById('clearCacheBtn').addEventListener('click', async function() {
    if (!confirm('{{ app()->getLocale() === "ar" ? "هل أنت متأكد من مسح الذاكرة المؤقتة؟" : "Are you sure you want to clear the cache?" }}')) return;

    try {
        const response = await fetch('/admin/settings/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (result.success) showSuccessMessage(result.message);
    } catch (error) {
        console.error('Error:', error);
    }
});

// Toggle Maintenance
document.getElementById('toggleMaintenanceBtn').addEventListener('click', async function() {
    const isEnabled = {{ $settings['maintenance_mode'] ? 'true' : 'false' }};
    if (!confirm('{{ app()->getLocale() === "ar" ? "هل أنت متأكد من تبديل وضع الصيانة؟" : "Are you sure you want to toggle maintenance mode?" }}')) return;

    try {
        const response = await fetch('/admin/settings/toggle-maintenance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ enabled: !isEnabled })
        });

        const result = await response.json();
        if (result.success) {
            showSuccessMessage(result.message);
            setTimeout(() => location.reload(), 1500);
        }
    } catch (error) {
        console.error('Error:', error);
    }
});

// Show Success Message
function showSuccessMessage(message) {
    const alert = document.getElementById('successAlert');
    const text = document.getElementById('successText');
    text.textContent = message;
    alert.classList.add('show');
    setTimeout(() => alert.classList.remove('show'), 5000);
}

// Save .env File
document.getElementById('saveEnvBtn').addEventListener('click', async function() {
    if (!confirm('{{ app()->getLocale() === "ar" ? "هل أنت متأكد من حفظ تغييرات ملف .env؟ قد يتطلب إعادة تشغيل الخادم." : "Are you sure you want to save .env changes? Server restart may be required." }}')) {
        return;
    }

    // Collect all env variables from inputs
    const envInputs = document.querySelectorAll('.env-var-input');
    const envVars = {};

    envInputs.forEach(input => {
        const key = input.getAttribute('data-key');
        let value = input.value;

        // Wrap values with spaces in quotes
        if (value.includes(' ') || value.includes('=')) {
            value = '"' + value + '"';
        }

        envVars[key] = value;
    });

    // Build env content string
    let envContent = '';
    for (const [key, value] of Object.entries(envVars)) {
        envContent += key + '=' + value + '\n';
    }

    try {
        const response = await fetch('/admin/settings/update-env', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ env_content: envContent })
        });

        const result = await response.json();
        if (result.success) {
            showSuccessMessage(result.message);
        } else {
            alert('{{ app()->getLocale() === "ar" ? "خطأ: " : "Error: " }}' + (result.message || '{{ app()->getLocale() === "ar" ? "فشل الحفظ" : "Failed to save" }}'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('{{ app()->getLocale() === "ar" ? "حدث خطأ أثناء حفظ الملف" : "An error occurred while saving the file" }}');
    }
});

// Reload .env Content
document.getElementById('reloadEnvBtn').addEventListener('click', async function() {
    if (!confirm('{{ app()->getLocale() === "ar" ? "إعادة تعيين القيم للأصل؟ سيتم فقدان التعديلات غير المحفوظة." : "Reset values to original? Unsaved changes will be lost." }}')) {
        return;
    }

    // Reload the page to reset values
    window.location.reload();
});

// Logo Preview Handler
function handleLogoPreview(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('{{ app()->getLocale() === "ar" ? "حجم الملف كبير جداً. الحد الأقصى 2 ميجابايت" : "File size is too large. Maximum 2MB" }}');
            input.value = '';
            return;
        }

        // Check file type
        if (!file.type.match('image.*')) {
            alert('{{ app()->getLocale() === "ar" ? "يرجى اختيار ملف صورة" : "Please select an image file" }}');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreviewImg').src = e.target.result;
            document.getElementById('logoFileName').textContent = file.name;
            document.getElementById('logoUploadContent').style.display = 'none';
            document.getElementById('logoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Drag and Drop for Logo
const logoDropZone = document.getElementById('logoDropZone');

logoDropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.style.borderColor = '#6366f1';
    this.style.background = 'rgba(99, 102, 241, 0.1)';
});

logoDropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.style.borderColor = 'rgba(148, 163, 184, 0.3)';
    this.style.background = 'rgba(148, 163, 184, 0.05)';
});

logoDropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.style.borderColor = 'rgba(148, 163, 184, 0.3)';
    this.style.background = 'rgba(148, 163, 184, 0.05)';

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('logo_file').files = files;
        handleLogoPreview(document.getElementById('logo_file'));
    }
});
</script>
@endsection