<?php

/**
 * Production CORS Configuration
 * This file should be used for production environment
 * with stricter security settings
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration - PRODUCTION
    |--------------------------------------------------------------------------
    |
    | Stricter CORS configuration for production environment.
    | Only allow requests from your actual domains.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Only allow specific HTTP methods
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // IMPORTANT: Replace with your actual production domains
    'allowed_origins' => [
        'https://yourdomain.com',
        'https://www.yourdomain.com',
        'https://app.yourdomain.com',
        // Add Expo published app domain if using OTA updates
        'https://expo.dev',
        // Add your CDN domain if applicable
        'https://cdn.yourdomain.com',
    ],

    'allowed_origins_patterns' => [
        // Allow all subdomains of your domain
        '/^https:\/\/([a-z0-9-]+\.)?yourdomain\.com$/',
    ],

    // Only allow necessary headers
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
        'X-CSRF-Token',
    ],

    // Expose rate limit headers
    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
        'Retry-After',
    ],

    // Cache preflight requests for 24 hours
    'max_age' => 86400,

    // Allow credentials (cookies, authorization headers)
    'supports_credentials' => true,

];
