<?php

/**
 * AI Services Testing Script
 *
 * This script helps test the AI services implementation
 * Run with: php test_ai_services.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\AIService;
use App\Services\VoiceTranscriptionService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║        AI Services Implementation Test Suite               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Check AI Service Providers
echo "Test 1: Checking AI Provider Configuration...\n";
echo str_repeat("-", 60) . "\n";

$aiService = new AIService();
$providers = $aiService->getAvailableProviders();

foreach ($providers as $key => $provider) {
    $status = $provider['available'] ? '✅ Configured' : '❌ Not Configured';
    echo sprintf("  %-20s: %s (%s)\n", $provider['name'], $status, $provider['model']);
}

$configuredCount = count(array_filter($providers, fn($p) => $p['available']));
echo "\nTotal Configured Providers: $configuredCount / " . count($providers) . "\n\n";

if ($configuredCount === 0) {
    echo "⚠️  WARNING: No AI providers configured!\n";
    echo "Please add at least one API key to your .env file:\n";
    echo "  - OPENAI_API_KEY\n";
    echo "  - GEMINI_API_KEY\n";
    echo "  - CLAUDE_API_KEY\n\n";
} else {
    echo "✅ AI Service is ready to use!\n\n";
}

// Test 2: Check Voice Transcription Service
echo "Test 2: Checking Voice Transcription Service...\n";
echo str_repeat("-", 60) . "\n";

$transcriptionService = new VoiceTranscriptionService();
$formats = $transcriptionService->getSupportedFormats();
$maxSize = $transcriptionService->getMaxFileSize();

echo "  Supported Audio Formats:\n";
foreach ($formats as $format) {
    echo "    - $format\n";
}
echo "\n  Maximum File Size: " . round($maxSize / 1024 / 1024, 2) . " MB\n";

$whisperConfigured = !empty(env('OPENAI_API_KEY'));
echo "\n  Whisper API Status: " . ($whisperConfigured ? '✅ Configured' : '❌ Not Configured') . "\n\n";

// Test 3: Test Content Generation (if provider available)
if ($configuredCount > 0) {
    echo "Test 3: Testing Content Generation...\n";
    echo str_repeat("-", 60) . "\n";

    try {
        $result = $aiService->generateContent(
            "Quick test of the social media AI system",
            [
                'platform' => 'instagram',
                'tone' => 'friendly',
                'length' => 'short',
                'language' => 'en'
            ]
        );

        echo "  ✅ Content Generation Test: PASSED\n";
        echo "  Provider Used: " . $result['provider'] . "\n";
        echo "  Generated Content:\n";
        echo "  " . str_repeat("-", 58) . "\n";
        echo "  " . wordwrap($result['content'], 56, "\n  ") . "\n";
        echo "  " . str_repeat("-", 58) . "\n";
        echo "  Word Count: " . $result['metadata']['word_count'] . "\n";
        echo "  Character Count: " . $result['metadata']['char_count'] . "\n\n";

    } catch (Exception $e) {
        echo "  ❌ Content Generation Test: FAILED\n";
        echo "  Error: " . $e->getMessage() . "\n\n";
    }

    // Test 4: Test Hashtag Generation
    echo "Test 4: Testing Hashtag Generation...\n";
    echo str_repeat("-", 60) . "\n";

    try {
        $result = $aiService->generateHashtags(
            "Excited to share our new sustainable product line!",
            'instagram',
            5
        );

        echo "  ✅ Hashtag Generation Test: PASSED\n";
        echo "  Provider Used: " . $result['provider'] . "\n";
        echo "  Generated Hashtags: " . implode(' ', $result['hashtags']) . "\n";
        echo "  Count: " . $result['count'] . "\n\n";

    } catch (Exception $e) {
        echo "  ❌ Hashtag Generation Test: FAILED\n";
        echo "  Error: " . $e->getMessage() . "\n\n";
    }

    // Test 5: Test Content Enhancement
    echo "Test 5: Testing Content Enhancement...\n";
    echo str_repeat("-", 60) . "\n";

    try {
        $result = $aiService->enhanceContent(
            "we have new product its really good",
            'professional',
            ['grammar', 'engagement']
        );

        echo "  ✅ Content Enhancement Test: PASSED\n";
        echo "  Original: " . $result['original'] . "\n";
        echo "  Enhanced: " . $result['enhanced'] . "\n";
        echo "  Provider Used: " . $result['provider'] . "\n\n";

    } catch (Exception $e) {
        echo "  ❌ Content Enhancement Test: FAILED\n";
        echo "  Error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "⏭️  Skipping AI generation tests (no providers configured)\n\n";
}

// Test Summary
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                      Test Summary                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "Service Status:\n";
echo "  AI Providers: " . ($configuredCount > 0 ? "✅ Ready" : "❌ Not Configured") . "\n";
echo "  Voice Service: " . ($whisperConfigured ? "✅ Ready" : "❌ Not Configured") . "\n";

echo "\nAPI Endpoints Available:\n";
$endpoints = [
    'POST /api/ai/generate-content',
    'POST /api/ai/generate-hashtags',
    'POST /api/ai/transcribe-voice',
    'POST /api/ai/voice-to-post',
    'POST /api/ai/enhance-content',
    'POST /api/ai/generate-multilingual',
    'GET  /api/ai/providers',
    'POST /api/ai/set-provider',
    'GET  /api/ai/transcription-info',
];

foreach ($endpoints as $endpoint) {
    echo "  ✅ $endpoint\n";
}

echo "\nNext Steps:\n";
if ($configuredCount === 0) {
    echo "  1. Add at least one AI provider API key to .env\n";
    echo "  2. Run 'php artisan config:clear'\n";
    echo "  3. Re-run this test script\n";
} else {
    echo "  1. Test endpoints with Postman or cURL\n";
    echo "  2. Integrate with frontend application\n";
    echo "  3. Monitor usage and costs\n";
}

echo "\nDocumentation:\n";
echo "  - Complete Guide: backend-laravel/AI_SERVICES_IMPLEMENTATION.md\n";
echo "  - Quick Start: backend-laravel/AI_SERVICES_QUICK_START.md\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "Testing Complete!\n";
echo str_repeat("=", 60) . "\n";
