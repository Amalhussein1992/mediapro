@extends('layouts.admin')

@section('title', __('admin.settings'))
@section('header', __('admin.app_settings'))

@section('content')
<div class="max-w-6xl">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button
                    onclick="showTab('general')"
                    id="tab-general"
                    class="tab-button active px-6 py-4 text-sm font-semibold border-b-2 border-primary text-primary"
                >
                    {{ __('admin.general_settings') }}
                </button>
                <button
                    onclick="showTab('email')"
                    id="tab-email"
                    class="tab-button px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300"
                >
                    {{ __('admin.mail_settings') }}
                </button>
                <button
                    onclick="showTab('api')"
                    id="tab-api"
                    class="tab-button px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300"
                >
                    {{ __('admin.api_keys') }}
                </button>
                <button
                    onclick="showTab('preferences')"
                    id="tab-preferences"
                    class="tab-button px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300"
                >
                    {{ __('admin.preferences') }}
                </button>
                <button
                    onclick="showTab('payments')"
                    id="tab-payments"
                    class="tab-button px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300"
                >
                    {{ __('admin.payment_methods') }}
                </button>
            </nav>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
            @csrf

            <!-- General Settings Tab -->
            <div id="content-general" class="tab-content">
                <h3 class="text-xl font-bold text-gray-800 mb-4">General Settings</h3>

                <div class="mb-4">
                    <label for="app_name" class="block text-gray-700 text-sm font-semibold mb-2">
                        Application Name
                    </label>
                    <input
                        type="text"
                        id="app_name"
                        name="app_name"
                        value="{{ old('app_name', $settings['app_name']) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        placeholder="Social Media Manager"
                    >
                    @error('app_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="app_url" class="block text-gray-700 text-sm font-semibold mb-2">
                        Application URL
                    </label>
                    <input
                        type="url"
                        id="app_url"
                        name="app_url"
                        value="{{ old('app_url', $settings['app_url']) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        placeholder="https://example.com"
                    >
                    @error('app_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 mb-1">Note</h4>
                            <p class="text-sm text-blue-800">To persist these changes permanently, update your .env file or consider using a database table for settings.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Configuration Tab -->
            <div id="content-email" class="tab-content hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Email Configuration</h3>

                <div class="mb-4">
                    <label for="mail_from_address" class="block text-gray-700 text-sm font-semibold mb-2">
                        From Email Address
                    </label>
                    <input
                        type="email"
                        id="mail_from_address"
                        name="mail_from_address"
                        value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        placeholder="noreply@example.com"
                    >
                    @error('mail_from_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="mail_from_name" class="block text-gray-700 text-sm font-semibold mb-2">
                        From Name
                    </label>
                    <input
                        type="text"
                        id="mail_from_name"
                        name="mail_from_name"
                        value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        placeholder="Social Media Manager"
                    >
                    @error('mail_from_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-900 mb-1">Important</h4>
                            <p class="text-sm text-yellow-800">Update your MAIL_* environment variables in the .env file for email settings to take effect.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Keys Tab -->
            <div id="content-api" class="tab-content hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Social Media API Keys</h3>

                <!-- Facebook -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Facebook</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="facebook_api_key" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Key / App ID
                            </label>
                            <input
                                type="text"
                                id="facebook_api_key"
                                name="facebook_api_key"
                                value="{{ old('facebook_api_key') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your Facebook App ID"
                            >
                        </div>
                        <div>
                            <label for="facebook_api_secret" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Secret
                            </label>
                            <input
                                type="password"
                                id="facebook_api_secret"
                                name="facebook_api_secret"
                                value="{{ old('facebook_api_secret') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your Facebook App Secret"
                            >
                        </div>
                    </div>
                </div>

                <!-- Twitter -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Twitter</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="twitter_api_key" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Key
                            </label>
                            <input
                                type="text"
                                id="twitter_api_key"
                                name="twitter_api_key"
                                value="{{ old('twitter_api_key') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your Twitter API Key"
                            >
                        </div>
                        <div>
                            <label for="twitter_api_secret" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Secret
                            </label>
                            <input
                                type="password"
                                id="twitter_api_secret"
                                name="twitter_api_secret"
                                value="{{ old('twitter_api_secret') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your Twitter API Secret"
                            >
                        </div>
                    </div>
                </div>

                <!-- Instagram -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Instagram</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="instagram_api_key" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Key
                            </label>
                            <input
                                type="text"
                                id="instagram_api_key"
                                name="instagram_api_key"
                                value="{{ old('instagram_api_key') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your Instagram API Key"
                            >
                        </div>
                        <div>
                            <label for="instagram_api_secret" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Secret
                            </label>
                            <input
                                type="password"
                                id="instagram_api_secret"
                                name="instagram_api_secret"
                                value="{{ old('instagram_api_secret') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your Instagram API Secret"
                            >
                        </div>
                    </div>
                </div>

                <!-- LinkedIn -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">LinkedIn</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="linkedin_api_key" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Key
                            </label>
                            <input
                                type="text"
                                id="linkedin_api_key"
                                name="linkedin_api_key"
                                value="{{ old('linkedin_api_key') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your LinkedIn API Key"
                            >
                        </div>
                        <div>
                            <label for="linkedin_api_secret" class="block text-gray-700 text-sm font-semibold mb-2">
                                API Secret
                            </label>
                            <input
                                type="password"
                                id="linkedin_api_secret"
                                name="linkedin_api_secret"
                                value="{{ old('linkedin_api_secret') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                placeholder="Your LinkedIn API Secret"
                            >
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-red-900 mb-1">Security Warning</h4>
                            <p class="text-sm text-red-800">Never commit API keys to version control. Store them in your .env file and keep them secure.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Tab -->
            <div id="content-payments" class="tab-content hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('admin.payment_gateway_settings') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('admin.configure_payment_gateways_desc') }}</p>

                <div class="space-y-6">
                    <!-- Stripe Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-credit-card text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800">Stripe</h4>
                                <p class="text-sm text-gray-500">{{ __('admin.stripe_payment_gateway') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.stripe_publishable_key') }}</label>
                                <input
                                    type="text"
                                    name="stripe_key"
                                    placeholder="pk_live_..."
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.stripe_secret_key') }}</label>
                                <input
                                    type="password"
                                    name="stripe_secret"
                                    placeholder="sk_live_..."
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.stripe_webhook_secret') }}</label>
                                <input
                                    type="password"
                                    name="stripe_webhook_secret"
                                    placeholder="whsec_..."
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- PayPal Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-400 rounded-lg flex items-center justify-center mr-4">
                                <i class="fab fa-paypal text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800">PayPal</h4>
                                <p class="text-sm text-gray-500">{{ __('admin.paypal_payment_gateway') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.paypal_mode') }}</label>
                                <select
                                    name="paypal_mode"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                                    <option value="sandbox">Sandbox ({{ __('admin.testing') }})</option>
                                    <option value="live">Live ({{ __('admin.production') }})</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.paypal_client_id') }}</label>
                                <input
                                    type="text"
                                    name="paypal_client_id"
                                    placeholder="AYSq3RD..."
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.paypal_secret') }}</label>
                                <input
                                    type="password"
                                    name="paypal_secret"
                                    placeholder="EO422dn..."
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Google Pay Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-yellow-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="fab fa-google text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800">Google Pay</h4>
                                <p class="text-sm text-gray-500">{{ __('admin.google_pay_gateway') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.google_merchant_id') }}</label>
                                <input
                                    type="text"
                                    name="google_pay_merchant_id"
                                    placeholder="BCR2DN..."
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.google_merchant_name') }}</label>
                                <input
                                    type="text"
                                    name="google_pay_merchant_name"
                                    placeholder="Your Business Name"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.google_gateway_id') }}</label>
                                <input
                                    type="text"
                                    name="google_pay_gateway_id"
                                    placeholder="exampleGatewayMerchantId"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-semibold mb-1">{{ __('admin.google_pay_info') }}</p>
                                        <p>{{ __('admin.google_pay_info_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Apple Pay Settings -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-gray-800 to-gray-600 rounded-lg flex items-center justify-center mr-4">
                                <i class="fab fa-apple text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800">Apple Pay</h4>
                                <p class="text-sm text-gray-500">{{ __('admin.apple_pay_gateway') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.apple_merchant_id') }}</label>
                                <input
                                    type="text"
                                    name="apple_pay_merchant_id"
                                    placeholder="merchant.com.yourcompany.app"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.apple_merchant_name') }}</label>
                                <input
                                    type="text"
                                    name="apple_pay_merchant_name"
                                    placeholder="Your Business Name"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.apple_merchant_domain') }}</label>
                                <input
                                    type="text"
                                    name="apple_pay_merchant_domain"
                                    placeholder="yourdomain.com"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.apple_certificate') }}</label>
                                <textarea
                                    name="apple_pay_certificate"
                                    rows="4"
                                    placeholder="-----BEGIN CERTIFICATE-----
MIIDXTCCAkWgAwIBAgIJAKL...
-----END CERTIFICATE-----"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-mono text-sm"
                                ></textarea>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                                    <div class="text-sm text-yellow-700">
                                        <p class="font-semibold mb-1">{{ __('admin.apple_pay_info') }}</p>
                                        <p>{{ __('admin.apple_pay_info_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div id="content-preferences" class="tab-content hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Application Preferences</h3>

                <div class="space-y-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="enable_notifications"
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700 font-semibold">Enable Email Notifications</span>
                    </label>

                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="auto_publish"
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700 font-semibold">Auto-publish Scheduled Posts</span>
                    </label>

                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="analytics_tracking"
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700 font-semibold">Enable Analytics Tracking</span>
                    </label>

                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="maintenance_mode"
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700 font-semibold">Maintenance Mode</span>
                    </label>
                </div>

                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Timezone</h4>
                    <select class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <option>UTC</option>
                        <option>America/New_York</option>
                        <option>America/Chicago</option>
                        <option>America/Los_Angeles</option>
                        <option>Europe/London</option>
                        <option>Europe/Paris</option>
                        <option>Asia/Tokyo</option>
                        <option>Australia/Sydney</option>
                    </select>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end">
                <button
                    type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all font-semibold"
                >
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-primary', 'text-primary');
            button.classList.add('border-transparent', 'text-gray-600');
        });

        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');

        // Add active class to selected tab
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('active', 'border-primary', 'text-primary');
        activeTab.classList.remove('border-transparent', 'text-gray-600');
    }
</script>
@endpush
@endsection
