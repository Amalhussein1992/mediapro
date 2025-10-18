<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure rate limiters for different API endpoints.
    | The format is 'max_attempts:decay_minutes'
    |
    */

    'api' => [
        // General API endpoints
        'default' => env('RATE_LIMIT_DEFAULT', '60:1'), // 60 requests per minute

        // Authentication endpoints (more restrictive)
        'auth' => env('RATE_LIMIT_AUTH', '5:1'), // 5 login attempts per minute
        'register' => env('RATE_LIMIT_REGISTER', '3:60'), // 3 registrations per hour
        'password_reset' => env('RATE_LIMIT_PASSWORD_RESET', '3:60'), // 3 password resets per hour

        // Post management
        'posts' => env('RATE_LIMIT_POSTS', '100:1'), // 100 requests per minute
        'post_create' => env('RATE_LIMIT_POST_CREATE', '30:1'), // 30 posts per minute
        'post_delete' => env('RATE_LIMIT_POST_DELETE', '20:1'), // 20 deletions per minute

        // AI features (more restrictive due to cost)
        'ai_generation' => env('RATE_LIMIT_AI', '10:1'), // 10 AI requests per minute
        'ai_image' => env('RATE_LIMIT_AI_IMAGE', '5:1'), // 5 image generations per minute
        'ai_video' => env('RATE_LIMIT_AI_VIDEO', '3:5'), // 3 video generations per 5 minutes

        // Social accounts
        'social_oauth' => env('RATE_LIMIT_OAUTH', '10:5'), // 10 OAuth attempts per 5 minutes
        'social_posts' => env('RATE_LIMIT_SOCIAL_POSTS', '50:1'), // 50 social posts per minute

        // Analytics
        'analytics' => env('RATE_LIMIT_ANALYTICS', '100:1'), // 100 analytics requests per minute

        // Upload endpoints
        'upload' => env('RATE_LIMIT_UPLOAD', '20:1'), // 20 uploads per minute

        // Admin endpoints (less restrictive for admins)
        'admin' => env('RATE_LIMIT_ADMIN', '200:1'), // 200 requests per minute

        // Search endpoints
        'search' => env('RATE_LIMIT_SEARCH', '30:1'), // 30 searches per minute

        // Export endpoints
        'export' => env('RATE_LIMIT_EXPORT', '5:60'), // 5 exports per hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limit By User or IP
    |--------------------------------------------------------------------------
    |
    | Determine whether to rate limit by authenticated user or by IP address
    |
    */

    'by_user' => true, // If false, will use IP address

    /*
    |--------------------------------------------------------------------------
    | Custom Response
    |--------------------------------------------------------------------------
    |
    | Customize the response returned when rate limit is exceeded
    |
    */

    'response' => [
        'message' => 'Too many requests. Please slow down.',
        'status_code' => 429,
        'include_retry_after' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Whitelist
    |--------------------------------------------------------------------------
    |
    | IP addresses or user IDs that should bypass rate limiting
    |
    */

    'whitelist' => [
        'ips' => [
            // '127.0.0.1',
        ],
        'users' => [
            // Admin user IDs
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    |
    | Add rate limit information to response headers
    |
    */

    'headers' => [
        'limit' => 'X-RateLimit-Limit',
        'remaining' => 'X-RateLimit-Remaining',
        'retry_after' => 'Retry-After',
        'reset' => 'X-RateLimit-Reset',
    ],

];
