<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\BrandKitController;
use App\Http\Controllers\Api\AiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Firebase OTP routes
    Route::post('otp/send', [AuthController::class, 'sendOTP']);
    Route::post('otp/verify', [AuthController::class, 'verifyOTP']);
    Route::post('otp/can-resend', [AuthController::class, 'canResendOTP']);
    Route::get('firebase/config', [AuthController::class, 'getFirebaseConfig']);

    // Public routes
    Route::get('subscription-plans', [SubscriptionController::class, 'plans']);
    Route::get('settings/public', [SettingController::class, 'public']);
    Route::get('languages', [LanguageController::class, 'index']);
    Route::get('translations/{languageCode}', [TranslationController::class, 'getByLanguage']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // User info & profile
        Route::get('user', function (Request $request) {
            return $request->user();
        });
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);

        // Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index']);
            Route::get('/{id}', [SubscriptionController::class, 'show']);
            Route::post('/', [SubscriptionController::class, 'store']);
            Route::put('/{id}', [SubscriptionController::class, 'update']);
            Route::delete('/{id}', [SubscriptionController::class, 'destroy']);
            Route::post('/{id}/cancel', [SubscriptionController::class, 'cancel']);
            Route::post('/{id}/renew', [SubscriptionController::class, 'renew']);
            Route::get('/user/current', [SubscriptionController::class, 'current']);
        });

        // Earnings
        Route::prefix('earnings')->group(function () {
            Route::get('/', [EarningController::class, 'index']);
            Route::get('/{id}', [EarningController::class, 'show']);
            Route::get('/stats/total', [EarningController::class, 'total']);
            Route::get('/stats/monthly', [EarningController::class, 'monthly']);
        });

        // Payments
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index']);
            Route::get('/{id}', [PaymentController::class, 'show']);
            Route::post('/stripe/create-payment-intent', [PaymentController::class, 'createStripePaymentIntent']);
            Route::post('/stripe/confirm', [PaymentController::class, 'confirmStripePayment']);
            Route::post('/paypal/create-order', [PaymentController::class, 'createPayPalOrder']);
            Route::post('/paypal/capture', [PaymentController::class, 'capturePayPalOrder']);
            Route::post('/{id}/refund', [PaymentController::class, 'refund']);
        });

        // Settings (admin only)
        Route::middleware('admin')->prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index']);
            Route::get('/{key}', [SettingController::class, 'show']);
            Route::post('/', [SettingController::class, 'store']);
            Route::put('/{key}', [SettingController::class, 'update']);
            Route::delete('/{key}', [SettingController::class, 'destroy']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
            Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
            Route::get('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'show']);
            Route::post('/{id}/mark-as-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-as-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
            Route::delete('/read/clear', [\App\Http\Controllers\Api\NotificationController::class, 'deleteRead']);
        });

        // API Keys Management
        Route::prefix('api-keys')->group(function () {
            Route::get('/', [ApiKeyController::class, 'index']);
            Route::post('/', [ApiKeyController::class, 'store']);
            Route::get('/{id}', [ApiKeyController::class, 'show']);
            Route::put('/{id}', [ApiKeyController::class, 'update']);
            Route::delete('/{id}', [ApiKeyController::class, 'destroy']);
            Route::get('/{id}/stats', [ApiKeyController::class, 'stats']);
            Route::post('/{id}/regenerate', [ApiKeyController::class, 'regenerate']);
        });

        // Brand Kits
        Route::prefix('brand-kits')->group(function () {
            Route::get('/', [BrandKitController::class, 'index']);
            Route::post('/', [BrandKitController::class, 'store']);
            Route::get('/default', [BrandKitController::class, 'getDefault']);
            Route::get('/{id}', [BrandKitController::class, 'show']);
            Route::post('/{id}', [BrandKitController::class, 'update']); // POST for file upload
            Route::delete('/{id}', [BrandKitController::class, 'destroy']);
            Route::post('/{id}/set-default', [BrandKitController::class, 'setDefault']);
        });

        // AI Features
        Route::prefix('ai')->group(function () {
            // Image Generation
            Route::post('/generate-image', [AiController::class, 'generateImage']);

            // Video Script Generation
            Route::post('/generate-video-script', [AiController::class, 'generateVideoScript']);

            // Audio Transcription
            Route::post('/transcribe-audio', [AiController::class, 'transcribeAudio']);

            // Social Media Content Generation
            Route::post('/generate-social-content', [AiController::class, 'generateSocialContent']);

            // History
            Route::get('/history', [AiController::class, 'getHistory']);
            Route::get('/history/{id}', [AiController::class, 'getGeneration']);
        });

        // Languages & Translations (admin only)
        Route::middleware('admin')->group(function () {
            Route::apiResource('languages', LanguageController::class);
            Route::post('languages/{id}/set-default', [LanguageController::class, 'setDefault']);

            Route::prefix('translations')->group(function () {
                Route::get('/', [TranslationController::class, 'index']);
                Route::post('/', [TranslationController::class, 'store']);
                Route::put('/{id}', [TranslationController::class, 'update']);
                Route::delete('/{id}', [TranslationController::class, 'destroy']);
                Route::post('/import', [TranslationController::class, 'import']);
                Route::get('/export/{languageCode}', [TranslationController::class, 'export']);
            });
        });
    });

    // Pages (Public)
    Route::prefix('pages')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PageController::class, 'index']);
        Route::get('/menu', [\App\Http\Controllers\Api\PageController::class, 'menu']);
        Route::get('/search', [\App\Http\Controllers\Api\PageController::class, 'search']);
        Route::get('/{slug}', [\App\Http\Controllers\Api\PageController::class, 'show']);
    });

    // Webhook routes (no authentication)
    Route::post('webhooks/stripe', [PaymentController::class, 'stripeWebhook']);
    Route::post('webhooks/paypal', [PaymentController::class, 'paypalWebhook']);
});
