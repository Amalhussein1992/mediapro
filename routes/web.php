<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AdminPostController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\AdminSocialAccountController;
use App\Http\Controllers\Web\AdminTranslationController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\LanguageController;

// Language Switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Temporary: Clear cache
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');

    return '<h1>✅ Cache Cleared!</h1><p>All caches have been cleared.</p><p><a href="/">Go to Home Page</a></p><p>Version: 2025-01-19 22:00</p>';
});

// Temporary: Seed pages directly via web route
Route::get('/run-seeder', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'PagesSeeder']);

        return '<h1>Success!</h1><p>Pages seeded successfully!</p><p>Now test: <a href="/features">Features</a>, <a href="/pricing">Pricing</a>, <a href="/about">About</a></p><p><strong>Delete this route after use!</strong></p>';
    } catch (\Exception $e) {
        return '<h1>Error</h1><p>' . $e->getMessage() . '</p>';
    }
});

// Settings Dashboard (No auth required for testing)
Route::get('/settings-dashboard', function () {
    return view('settings-dashboard');
})->name('settings.dashboard');

// Public Pages - Dynamic from Database
Route::get('/features', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'features')->name('features');
Route::get('/pricing', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'pricing')->name('pricing');
Route::get('/api', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'api')->name('api');
Route::get('/about', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'about')->name('about');
Route::get('/blog', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'blog')->name('blog');
Route::get('/careers', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'careers')->name('careers');
Route::get('/help', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'help')->name('help');
Route::get('/community', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'community')->name('community');
Route::get('/contact', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'contact')->name('contact');
Route::get('/privacy', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'privacy')->name('privacy');
Route::get('/terms', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'terms')->name('terms');
Route::get('/security', [App\Http\Controllers\Web\PageController::class, 'show'])->defaults('slug', 'security')->name('security');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Logout (requires authentication)
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Posts
        Route::resource('posts', AdminPostController::class);

        // Users
        Route::resource('users', AdminUserController::class);

        // Social Accounts
        Route::resource('social-accounts', AdminSocialAccountController::class);

        // Subscription Plans
        Route::resource('subscription-plans', App\Http\Controllers\Admin\SubscriptionPlanController::class);
        Route::post('subscription-plans/{id}/toggle-active', [App\Http\Controllers\Admin\SubscriptionPlanController::class, 'toggleActive'])->name('subscription-plans.toggle-active');

        // Subscriptions
        Route::get('subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/{id}', [App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('subscriptions/{id}/update-status', [App\Http\Controllers\Admin\SubscriptionController::class, 'updateStatus'])->name('subscriptions.update-status');
        Route::post('subscriptions/{id}/change-plan', [App\Http\Controllers\Admin\SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
        Route::post('subscriptions/{id}/cancel', [App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('subscriptions/{id}/reactivate', [App\Http\Controllers\Admin\SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');
        Route::get('subscriptions/export', [App\Http\Controllers\Admin\SubscriptionController::class, 'export'])->name('subscriptions.export');

        // Payments
        Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/create', [App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{id}/update-status', [App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.update-status');
        Route::post('payments/{id}/refund', [App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('payments.refund');
        Route::get('payments-export', [App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');
        Route::get('payments/stats/revenue', [App\Http\Controllers\Admin\PaymentController::class, 'getRevenueStats'])->name('payments.revenue-stats');

        // Ad Requests Management
        Route::get('ad-requests', [App\Http\Controllers\Admin\AdRequestController::class, 'index'])->name('ad-requests.index');
        Route::get('ad-requests/{id}', [App\Http\Controllers\Admin\AdRequestController::class, 'show'])->name('ad-requests.show');
        Route::post('ad-requests/{id}/update-status', [App\Http\Controllers\Admin\AdRequestController::class, 'updateStatus'])->name('ad-requests.update-status');
        Route::post('ad-requests/{id}/update-metrics', [App\Http\Controllers\Admin\AdRequestController::class, 'updateMetrics'])->name('ad-requests.update-metrics');
        Route::delete('ad-requests/{id}', [App\Http\Controllers\Admin\AdRequestController::class, 'destroy'])->name('ad-requests.destroy');
        Route::post('ad-requests/bulk-approve', [App\Http\Controllers\Admin\AdRequestController::class, 'bulkApprove'])->name('ad-requests.bulk-approve');
        Route::get('ad-requests-export', [App\Http\Controllers\Admin\AdRequestController::class, 'export'])->name('ad-requests.export');

        // Analytics
        Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

        // Brand Kits
        Route::resource('brand-kits', App\Http\Controllers\Admin\BrandKitController::class);
        Route::post('brand-kits/{id}/set-default', [App\Http\Controllers\Admin\BrandKitController::class, 'setDefault'])->name('brand-kits.set-default');

        // Ads Campaigns
        Route::resource('ads-campaigns', App\Http\Controllers\Web\AdminAdsCampaignController::class);

        // Settings (New Admin Settings Controller)
        Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/update-single', [App\Http\Controllers\Admin\SettingsController::class, 'updateSingle'])->name('settings.update-single');
        Route::post('settings/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('settings/toggle-maintenance', [App\Http\Controllers\Admin\SettingsController::class, 'toggleMaintenance'])->name('settings.toggle-maintenance');
        Route::post('settings/update-env', [App\Http\Controllers\Admin\SettingsController::class, 'updateEnv'])->name('settings.update-env');
        Route::get('settings/get-env', [App\Http\Controllers\Admin\SettingsController::class, 'getEnv'])->name('settings.get-env');

        // Landing Page Management
        Route::get('settings/landing-page', [SettingsController::class, 'landingPage'])->name('settings.landing-page');
        Route::post('settings/landing-page', [SettingsController::class, 'updateLandingPage'])->name('settings.landing-page.update');

        // Pages Management
        Route::get('settings/pages', [SettingsController::class, 'pages'])->name('settings.pages');
        Route::get('settings/pages/{page}', [SettingsController::class, 'editPage'])->name('settings.pages.edit');
        Route::post('settings/pages/{page}', [SettingsController::class, 'updatePage'])->name('settings.pages.update');

        // Mobile App Management
        Route::get('settings/mobile-app', [SettingsController::class, 'mobileApp'])->name('settings.mobile-app');
        Route::post('settings/mobile-app', [SettingsController::class, 'updateMobileApp'])->name('settings.mobile-app.update');

        // Translations
        Route::resource('translations', AdminTranslationController::class);
        Route::get('translations/export', [AdminTranslationController::class, 'export'])->name('translations.export');
        Route::post('translations/import', [AdminTranslationController::class, 'import'])->name('translations.import');

        // Profile
        Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');

        // Pages Management
        Route::resource('pages', App\Http\Controllers\Admin\PageController::class);
    });
});
