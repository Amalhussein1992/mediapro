<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SocialAccountController;
use App\Http\Controllers\API\SubscriptionPlanController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\AnalyticsController;
use App\Http\Controllers\API\AIContentController;
use App\Http\Controllers\API\AIController;
use App\Http\Controllers\API\BrandKitController;
use App\Http\Controllers\API\AdsCampaignController;
use App\Http\Controllers\API\AdsAnalyticsController;
use App\Http\Controllers\API\AIMediaController;
use App\Http\Controllers\API\AppSettingController;
use App\Http\Controllers\API\OAuthController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\EnhancedAdminController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\AdminNotificationController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\API\SocialMediaController;
use App\Http\Controllers\API\SocialLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Social Login Routes (Google & Apple)
Route::prefix('auth')->group(function () {
    // Google OAuth
    Route::get('/google', [SocialLoginController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
    Route::post('/google/token', [SocialLoginController::class, 'loginWithGoogleToken']);

    // Apple OAuth
    Route::get('/apple', [SocialLoginController::class, 'redirectToApple']);
    Route::get('/apple/callback', [SocialLoginController::class, 'handleAppleCallback']);
    Route::post('/apple/token', [SocialLoginController::class, 'loginWithAppleToken']);
});

// Public app configuration (no auth required)
Route::get('/config', [AppSettingController::class, 'config']);
Route::get('/settings/theme', [AppSettingController::class, 'theme']);
Route::get('/settings/branding', [AppSettingController::class, 'branding']);
Route::get('/settings/public', [SettingsController::class, 'getPublicSettings']);

// Public subscription plans
Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);
Route::get('/subscription-plans/{id}', [SubscriptionPlanController::class, 'show']);

// Public translations (no auth required for reading)
Route::get('/translations/{locale}', [TranslationController::class, 'getByLocale'])->where('locale', 'en|ar');
Route::get('/translations/export/{locale}', [TranslationController::class, 'export'])->where('locale', 'en|ar');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Posts routes
    Route::apiResource('posts', PostController::class);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish']);

    // Users routes
    Route::apiResource('users', UserController::class);
    Route::get('/user/subscription-info', [UserController::class, 'getSubscriptionInfo']);

    // Social Accounts routes
    Route::apiResource('social-accounts', SocialAccountController::class);
    Route::post('/social-accounts/{id}/refresh', [SocialAccountController::class, 'refresh']);

    // OAuth routes for social media platforms
    Route::prefix('oauth')->group(function () {
        Route::get('/{platform}/auth-url', [OAuthController::class, 'getAuthUrl']);
        Route::get('/{platform}/callback', [OAuthController::class, 'handleCallback']);
    });

    // Subscription routes
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get('/current', [SubscriptionController::class, 'current']);
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('/renew', [SubscriptionController::class, 'renew']);
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/{id}', [PaymentController::class, 'show']);
    });

    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('/posts/{postId}', [AnalyticsController::class, 'postAnalytics']);
        Route::get('/accounts/{accountId}', [AnalyticsController::class, 'accountAnalytics']);
        Route::get('/trends', [AnalyticsController::class, 'engagementTrends']);
        Route::get('/best-times', [AnalyticsController::class, 'bestPostingTimes']);
    });

    // AI Content Generation routes (Legacy - kept for backward compatibility)
    Route::prefix('ai')->group(function () {
        // Legacy routes
        Route::post('/generate-caption', [AIContentController::class, 'generateCaption']);
        Route::post('/generate-hashtags', [AIContentController::class, 'generateHashtags']);
        Route::post('/improve-content', [AIContentController::class, 'improveContent']);
        Route::post('/generate-ideas', [AIContentController::class, 'generateIdeas']);

        // New comprehensive AI routes
        Route::post('/generate-content', [AIController::class, 'generateContent']);
        Route::post('/enhance-content', [AIController::class, 'enhanceContent']);
        Route::post('/generate-multilingual', [AIController::class, 'generateMultilingual']);

        // Voice transcription routes
        Route::post('/transcribe-voice', [AIController::class, 'transcribeVoice']);
        Route::post('/voice-to-post', [AIController::class, 'voiceToPost']);
        Route::get('/transcription-info', [AIController::class, 'getTranscriptionInfo']);

        // AI provider management
        Route::get('/providers', [AIController::class, 'getProviders']);
        Route::post('/set-provider', [AIController::class, 'setProvider']);
    });

    // AI Media Generation routes
    Route::prefix('ai-media')->group(function () {
        Route::post('/generate-post-with-image', [AIMediaController::class, 'generatePostWithImage']);
        Route::post('/generate-image', [AIMediaController::class, 'generateImage']);
        Route::post('/generate-video-script', [AIMediaController::class, 'generateVideoScript']);
        Route::post('/generate-image-variations', [AIMediaController::class, 'generateImageVariations']);
    });

    // Brand Kit routes
    Route::post('/brand-kits/generate-with-ai', [BrandKitController::class, 'generateWithAI']);
    Route::get('/brand-kits/default/get', [BrandKitController::class, 'getDefault']);
    Route::post('/brand-kits/{id}/set-default', [BrandKitController::class, 'setDefault']);
    Route::apiResource('brand-kits', BrandKitController::class);

    // Social Media Integration routes (Ayrshare)
    Route::prefix('social-media')->group(function () {
        // OAuth & Account Management
        Route::get('/auth-url', [SocialMediaController::class, 'getAuthUrl']);
        Route::get('/accounts', [SocialMediaController::class, 'getConnectedAccounts']);
        Route::post('/connect', [SocialMediaController::class, 'connectAccount']);
        Route::delete('/disconnect/{platform}', [SocialMediaController::class, 'disconnectAccount']);

        // Posting
        Route::post('/post', [SocialMediaController::class, 'createPost']);
        Route::delete('/post/{postId}', [SocialMediaController::class, 'deletePost']);
        Route::get('/posts', [SocialMediaController::class, 'getPostHistory']);

        // Analytics
        Route::get('/analytics', [SocialMediaController::class, 'getAnalytics']);
        Route::get('/analytics/{platform}', [SocialMediaController::class, 'getAccountAnalytics']);

        // Media
        Route::post('/media/upload', [SocialMediaController::class, 'uploadMedia']);

        // Status
        Route::get('/status', [SocialMediaController::class, 'getStatus']);
    });

    // Ads Campaign routes
    Route::prefix('ads')->group(function () {
        // Dashboard & Metrics
        Route::get('/dashboard/metrics', [AdsAnalyticsController::class, 'getDashboardMetrics']);

        // Campaign Management
        Route::apiResource('campaigns', AdsCampaignController::class);
        Route::post('/campaigns/build-with-ai', [AdsCampaignController::class, 'buildWithAI']);
        Route::post('/campaigns/{campaign}/publish', [AdsCampaignController::class, 'publish']);
        Route::post('/campaigns/{campaign}/pause', [AdsCampaignController::class, 'pause']);
        Route::post('/campaigns/{campaign}/resume', [AdsCampaignController::class, 'resume']);

        // Campaign Analytics
        Route::get('/campaigns/{campaign}/analytics', [AdsAnalyticsController::class, 'campaignAnalytics']);
        Route::get('/campaigns/{campaign}/insights', [AdsAnalyticsController::class, 'campaignInsights']);
    });

    // Ad Requests routes (Paid Ads Management)
    Route::prefix('ad-requests')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\AdRequestController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\API\AdRequestController::class, 'store']);
        Route::get('/statistics', [\App\Http\Controllers\API\AdRequestController::class, 'statistics']);
        Route::get('/{id}', [\App\Http\Controllers\API\AdRequestController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\API\AdRequestController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\API\AdRequestController::class, 'destroy']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::post('/clear-read', [NotificationController::class, 'clearRead']);

        // Push notification routes
        Route::post('/register-push-token', [NotificationController::class, 'registerPushToken']);
        Route::post('/remove-push-token', [NotificationController::class, 'removePushToken']);
        Route::post('/test', [NotificationController::class, 'sendTestNotification']);
    });

    // Translation Management routes (Admin/Moderator access)
    Route::prefix('translations')->middleware('role:admin,moderator')->group(function () {
        Route::get('/', [TranslationController::class, 'index']);
        Route::get('/stats', [TranslationController::class, 'stats']);
        Route::post('/', [TranslationController::class, 'store']);
        Route::put('/{id}', [TranslationController::class, 'update']);
        Route::delete('/{id}', [TranslationController::class, 'destroy']);
        Route::post('/sync', [TranslationController::class, 'sync']);
    });

    // Admin routes (basic - for backward compatibility)
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);

        // User Management
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/users/{id}', [AdminController::class, 'getUserDetails']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

        // Post Management
        Route::get('/posts', [AdminController::class, 'getAllPosts']);
        Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);

        // System
        Route::get('/logs', [AdminController::class, 'getSystemLogs']);
        Route::post('/cache/clear', [AdminController::class, 'clearCache']);
    });

    // Enhanced Admin routes (with role-based access control)
    Route::prefix('admin/v2')->middleware('role:admin,moderator')->group(function () {
        // Enhanced Dashboard
        Route::get('/dashboard/enhanced-stats', [EnhancedAdminController::class, 'getEnhancedDashboardStats']);

        // Content Moderation
        Route::get('/moderation/queue', [EnhancedAdminController::class, 'getModerationQueue']);
        Route::post('/moderation/posts/{id}', [EnhancedAdminController::class, 'moderatePost']);

        // Bulk Operations
        Route::post('/bulk/users', [EnhancedAdminController::class, 'bulkUserOperations']);
        Route::post('/bulk/posts', [EnhancedAdminController::class, 'bulkPostOperations']);

        // API Usage Analytics
        Route::get('/analytics/api-usage', [EnhancedAdminController::class, 'getApiUsageAnalytics']);

        // Audit Logs
        Route::get('/audit-logs', [EnhancedAdminController::class, 'getAuditLogs']);

        // Subscription Management
        Route::get('/subscriptions/management', [EnhancedAdminController::class, 'getSubscriptionManagement']);

        // Reports
        Route::post('/reports/generate', [EnhancedAdminController::class, 'generateReport']);

        // App Settings Management (Admin Only)
        Route::prefix('settings')->middleware('role:admin')->group(function () {
            Route::get('/', [SettingsController::class, 'index']);
            Route::get('/group/{group}', [SettingsController::class, 'getByGroup']);
            Route::get('/{key}', [SettingsController::class, 'show']);
            Route::post('/', [SettingsController::class, 'store']);
            Route::put('/bulk', [SettingsController::class, 'bulkUpdate']);
            Route::delete('/{key}', [SettingsController::class, 'destroy']);
            Route::post('/initialize', [SettingsController::class, 'initializeDefaults']);
        });

        // Admin Notification Management (Admin Only)
        Route::prefix('notifications')->middleware('role:admin')->group(function () {
            Route::get('/stats', [AdminNotificationController::class, 'getStats']);
            Route::get('/all', [AdminNotificationController::class, 'getAllNotifications']);
            Route::get('/templates', [AdminNotificationController::class, 'getTemplates']);
            Route::post('/send-to-user', [AdminNotificationController::class, 'sendToUser']);
            Route::post('/send-to-multiple', [AdminNotificationController::class, 'sendToMultipleUsers']);
            Route::post('/send-to-all', [AdminNotificationController::class, 'sendToAllUsers']);
            Route::delete('/{id}', [AdminNotificationController::class, 'deleteNotification']);
        });
    });
});

// Test endpoint for deployment verification
Route::get('/test-deployment', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Auto deployment is working perfectly!',
        'deployed_at' => now()->format('Y-m-d H:i:s'),
        'server' => 'Plesk',
        'laravel_version' => app()->version(),
        'environment' => app()->environment(),
    ]);
});
