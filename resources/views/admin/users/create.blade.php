@extends('layouts.admin')

@section('title', __('Create User'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .create-user-page {
        padding: 2rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        {{ app()->getLocale() === 'ar' ? 'left: -20%;' : 'right: -20%;' }}
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(20px); }
    }

    .page-header-content {
        position: relative;
        z-index: 1;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .breadcrumb a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: white;
    }

    /* Form Container */
    .form-container {
        background: #1e293b;
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    /* Form Group */
    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .form-label .required {
        color: #f87171;
        margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 0.25rem;
    }

    .form-help {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: block;
    }

    /* Form Inputs */
    .form-input {
        width: 100%;
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #e2e8f0;
        padding: 0.875rem 1.125rem;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: #0f172a;
    }

    .form-input::placeholder {
        color: #64748b;
    }

    /* Error Messages */
    .form-error {
        color: #f87171;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    /* Card Sections */
    .form-section {
        background: #0f172a;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .section-title {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 24px;
        height: 24px;
        color: #818cf8;
    }

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 0.5rem;
        height: 4px;
        background: rgba(148, 163, 184, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: all 0.3s ease;
    }

    .password-strength-bar.weak {
        width: 33%;
        background: #ef4444;
    }

    .password-strength-bar.medium {
        width: 66%;
        background: #f59e0b;
    }

    .password-strength-bar.strong {
        width: 100%;
        background: #10b981;
    }

    .password-strength-text {
        color: #94a3b8;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Buttons */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: {{ app()->getLocale() === 'ar' ? 'flex-start' : 'flex-end' }};
        flex-wrap: wrap;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-cancel {
        background: rgba(148, 163, 184, 0.1);
        color: #cbd5e1;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .btn-cancel:hover {
        background: rgba(148, 163, 184, 0.2);
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        color: white;
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(99, 102, 241, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .create-user-page {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .form-container {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="create-user-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb">
                <a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>{{ __('Create New User') }}</span>
            </div>
            <h1 class="page-title">{{ __('Create New User') }}</h1>
            <p class="page-subtitle">{{ __('Add a new user to your social media management platform') }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ __('Personal Information') }}
                </h2>

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        {{ __('Full Name') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-input"
                        placeholder="{{ __('John Doe') }}"
                        required
                    >
                    <span class="form-help">{{ __('Enter the user\'s full name') }}</span>
                    @error('name')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        {{ __('Email Address') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-input"
                        placeholder="{{ __('john@example.com') }}"
                        required
                    >
                    <span class="form-help">{{ __('This will be used for login and notifications') }}</span>
                    @error('email')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Security Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    {{ __('Security') }}
                </h2>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        {{ __('Password') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="{{ __('Minimum 8 characters') }}"
                        required
                    >
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <span class="password-strength-text" id="strengthText">{{ __('Password strength will appear here') }}</span>
                    <span class="form-help">{{ __('Use a mix of letters, numbers, and symbols for better security') }}</span>
                    @error('password')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        {{ __('Confirm Password') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="{{ __('Re-enter password') }}"
                        required
                    >
                    <span class="form-help">{{ __('Please re-enter the password to confirm') }}</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('Create User') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        strengthBar.className = 'password-strength-bar';

        if (strength === 0 || strength === 1) {
            strengthBar.classList.add('weak');
            strengthText.textContent = '{{ __("Weak password") }}';
            strengthText.style.color = '#ef4444';
        } else if (strength === 2 || strength === 3) {
            strengthBar.classList.add('medium');
            strengthText.textContent = '{{ __("Medium password") }}';
            strengthText.style.color = '#f59e0b';
        } else {
            strengthBar.classList.add('strong');
            strengthText.textContent = '{{ __("Strong password") }}';
            strengthText.style.color = '#10b981';
        }
    });
</script>
@endsection
