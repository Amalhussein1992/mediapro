<?php

/**
 * Automated API Testing Script
 *
 * This script tests all critical API endpoints to ensure the app is ready for production
 *
 * Usage: php tests/automated_api_test.php
 */

class APITester
{
    private $baseUrl = 'http://localhost:8000/api';
    private $token = null;
    private $testResults = [];
    private $passedTests = 0;
    private $failedTests = 0;

    public function __construct()
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║     🚀 Social Media Manager - Automated API Tests 🚀        ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
    }

    /**
     * Run all tests
     */
    public function runAllTests()
    {
        $this->printSection("1️⃣  AUTHENTICATION TESTS");
        $this->testRegister();
        $this->testLogin();
        $this->testGetUser();

        $this->printSection("2️⃣  PUBLIC ENDPOINTS TESTS");
        $this->testGetConfig();
        $this->testGetSubscriptionPlans();
        $this->testGetTranslations();

        $this->printSection("3️⃣  POSTS MANAGEMENT TESTS");
        $this->testCreatePost();
        $this->testGetPosts();

        $this->printSection("4️⃣  SOCIAL ACCOUNTS TESTS");
        $this->testGetSocialAccounts();

        $this->printSection("5️⃣  SUBSCRIPTIONS TESTS");
        $this->testGetCurrentSubscription();

        $this->printSection("6️⃣  ANALYTICS TESTS");
        $this->testGetDashboardAnalytics();

        $this->printSection("7️⃣  AI FEATURES TESTS");
        $this->testAIGenerateContent();
        $this->testGetAIProviders();

        $this->printSection("8️⃣  NOTIFICATIONS TESTS");
        $this->testGetNotifications();
        $this->testGetUnreadCount();

        $this->printSection("9️⃣  BRAND KITS TESTS");
        $this->testGetBrandKits();

        $this->printSection("🔟 SECURITY TESTS");
        $this->testUnauthorizedAccess();
        $this->testInvalidToken();

        // Print final results
        $this->printFinalResults();
    }

    /**
     * Test user registration
     */
    private function testRegister()
    {
        $data = [
            'name' => 'Test User ' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->makeRequest('POST', '/auth/register', $data);

        if ($response && isset($response['token'])) {
            $this->recordTest('Register User', true, 'User registered successfully');
            $this->token = $response['token'];
        } else {
            $this->recordTest('Register User', false, 'Failed to register user');
        }
    }

    /**
     * Test user login
     */
    private function testLogin()
    {
        // Create a test user first if token is not available
        if (!$this->token) {
            $this->testRegister();
        }

        $data = [
            'email' => 'admin@example.com',
            'password' => 'password'
        ];

        $response = $this->makeRequest('POST', '/auth/login', $data);

        if ($response && isset($response['token'])) {
            $this->recordTest('Login User', true, 'Login successful');
            $this->token = $response['token'];
        } else {
            $this->recordTest('Login User', false, 'Login failed - Admin user may not exist');
        }
    }

    /**
     * Test get authenticated user
     */
    private function testGetUser()
    {
        $response = $this->makeRequest('GET', '/auth/user', [], true);

        if ($response && isset($response['id'])) {
            $this->recordTest('Get Authenticated User', true, 'User data retrieved');
        } else {
            $this->recordTest('Get Authenticated User', false, 'Failed to get user data');
        }
    }

    /**
     * Test get app config
     */
    private function testGetConfig()
    {
        $response = $this->makeRequest('GET', '/config');

        if ($response && is_array($response)) {
            $this->recordTest('Get App Config', true, 'Config retrieved successfully');
        } else {
            $this->recordTest('Get App Config', false, 'Failed to get config');
        }
    }

    /**
     * Test get subscription plans
     */
    private function testGetSubscriptionPlans()
    {
        $response = $this->makeRequest('GET', '/subscription-plans');

        if ($response && is_array($response)) {
            $plansCount = isset($response['data']) ? count($response['data']) : count($response);
            $this->recordTest('Get Subscription Plans', true, "Retrieved {$plansCount} plans");
        } else {
            $this->recordTest('Get Subscription Plans', false, 'Failed to get plans');
        }
    }

    /**
     * Test get translations
     */
    private function testGetTranslations()
    {
        $response = $this->makeRequest('GET', '/translations/en');

        if ($response && is_array($response)) {
            $this->recordTest('Get Translations (EN)', true, 'Translations retrieved');
        } else {
            $this->recordTest('Get Translations (EN)', false, 'Failed to get translations');
        }

        $response = $this->makeRequest('GET', '/translations/ar');

        if ($response && is_array($response)) {
            $this->recordTest('Get Translations (AR)', true, 'Translations retrieved');
        } else {
            $this->recordTest('Get Translations (AR)', false, 'Failed to get translations');
        }
    }

    /**
     * Test create post
     */
    private function testCreatePost()
    {
        if (!$this->token) {
            $this->recordTest('Create Post', false, 'No authentication token');
            return;
        }

        $data = [
            'content' => 'Test post created by automated testing script ' . date('Y-m-d H:i:s'),
            'platforms' => json_encode(['facebook', 'twitter']),
            'status' => 'draft'
        ];

        $response = $this->makeRequest('POST', '/posts', $data, true);

        if ($response && isset($response['id'])) {
            $this->recordTest('Create Post', true, 'Post created successfully');
        } else {
            $this->recordTest('Create Post', false, 'Failed to create post');
        }
    }

    /**
     * Test get posts
     */
    private function testGetPosts()
    {
        $response = $this->makeRequest('GET', '/posts', [], true);

        if ($response) {
            $postsCount = isset($response['data']) ? count($response['data']) : 0;
            $this->recordTest('Get Posts', true, "Retrieved posts (count: {$postsCount})");
        } else {
            $this->recordTest('Get Posts', false, 'Failed to get posts');
        }
    }

    /**
     * Test get social accounts
     */
    private function testGetSocialAccounts()
    {
        $response = $this->makeRequest('GET', '/social-accounts', [], true);

        if ($response !== false) {
            $accountsCount = isset($response['data']) ? count($response['data']) : 0;
            $this->recordTest('Get Social Accounts', true, "Retrieved {$accountsCount} accounts");
        } else {
            $this->recordTest('Get Social Accounts', false, 'Failed to get accounts');
        }
    }

    /**
     * Test get current subscription
     */
    private function testGetCurrentSubscription()
    {
        $response = $this->makeRequest('GET', '/subscriptions/current', [], true);

        if ($response !== false) {
            $this->recordTest('Get Current Subscription', true, 'Subscription info retrieved');
        } else {
            $this->recordTest('Get Current Subscription', false, 'Failed to get subscription');
        }
    }

    /**
     * Test get dashboard analytics
     */
    private function testGetDashboardAnalytics()
    {
        $response = $this->makeRequest('GET', '/analytics/dashboard', [], true);

        if ($response && is_array($response)) {
            $this->recordTest('Get Dashboard Analytics', true, 'Analytics retrieved');
        } else {
            $this->recordTest('Get Dashboard Analytics', false, 'Failed to get analytics');
        }
    }

    /**
     * Test AI generate content
     */
    private function testAIGenerateContent()
    {
        $data = [
            'topic' => 'social media marketing tips',
            'platform' => 'instagram',
            'tone' => 'professional'
        ];

        $response = $this->makeRequest('POST', '/ai/generate-content', $data, true);

        if ($response && isset($response['content'])) {
            $this->recordTest('AI Generate Content', true, 'Content generated successfully');
        } else {
            $this->recordTest('AI Generate Content', false, 'AI content generation failed (may need API key)');
        }
    }

    /**
     * Test get AI providers
     */
    private function testGetAIProviders()
    {
        $response = $this->makeRequest('GET', '/ai/providers', [], true);

        if ($response && is_array($response)) {
            $this->recordTest('Get AI Providers', true, 'AI providers retrieved');
        } else {
            $this->recordTest('Get AI Providers', false, 'Failed to get AI providers');
        }
    }

    /**
     * Test get notifications
     */
    private function testGetNotifications()
    {
        $response = $this->makeRequest('GET', '/notifications', [], true);

        if ($response !== false) {
            $notifCount = isset($response['data']) ? count($response['data']) : 0;
            $this->recordTest('Get Notifications', true, "Retrieved {$notifCount} notifications");
        } else {
            $this->recordTest('Get Notifications', false, 'Failed to get notifications');
        }
    }

    /**
     * Test get unread notifications count
     */
    private function testGetUnreadCount()
    {
        $response = $this->makeRequest('GET', '/notifications/unread-count', [], true);

        if ($response !== false) {
            $count = $response['count'] ?? 0;
            $this->recordTest('Get Unread Count', true, "Unread count: {$count}");
        } else {
            $this->recordTest('Get Unread Count', false, 'Failed to get unread count');
        }
    }

    /**
     * Test get brand kits
     */
    private function testGetBrandKits()
    {
        $response = $this->makeRequest('GET', '/brand-kits', [], true);

        if ($response !== false) {
            $kitsCount = isset($response['data']) ? count($response['data']) : 0;
            $this->recordTest('Get Brand Kits', true, "Retrieved {$kitsCount} brand kits");
        } else {
            $this->recordTest('Get Brand Kits', false, 'Failed to get brand kits');
        }
    }

    /**
     * Test unauthorized access
     */
    private function testUnauthorizedAccess()
    {
        // Try to access protected endpoint without token
        $ch = curl_init($this->baseUrl . '/posts');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 401) {
            $this->recordTest('Unauthorized Access Protection', true, 'Returns 401 as expected');
        } else {
            $this->recordTest('Unauthorized Access Protection', false, "Expected 401, got {$httpCode}");
        }
    }

    /**
     * Test invalid token
     */
    private function testInvalidToken()
    {
        $ch = curl_init($this->baseUrl . '/posts');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer invalid_token_12345'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 401) {
            $this->recordTest('Invalid Token Protection', true, 'Rejects invalid token');
        } else {
            $this->recordTest('Invalid Token Protection', false, "Expected 401, got {$httpCode}");
        }
    }

    /**
     * Make HTTP request
     */
    private function makeRequest($method, $endpoint, $data = [], $requiresAuth = false)
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        $headers = ['Accept: application/json'];

        if ($requiresAuth && $this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        return false;
    }

    /**
     * Record test result
     */
    private function recordTest($testName, $passed, $message = '')
    {
        $this->testResults[] = [
            'name' => $testName,
            'passed' => $passed,
            'message' => $message
        ];

        if ($passed) {
            $this->passedTests++;
            echo "  ✅ {$testName}: {$message}\n";
        } else {
            $this->failedTests++;
            echo "  ❌ {$testName}: {$message}\n";
        }
    }

    /**
     * Print section header
     */
    private function printSection($title)
    {
        echo "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  {$title}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }

    /**
     * Print final results
     */
    private function printFinalResults()
    {
        $totalTests = $this->passedTests + $this->failedTests;
        $passPercentage = $totalTests > 0 ? round(($this->passedTests / $totalTests) * 100, 2) : 0;

        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    📊 TEST RESULTS SUMMARY                   ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "  Total Tests:    {$totalTests}\n";
        echo "  ✅ Passed:      {$this->passedTests}\n";
        echo "  ❌ Failed:      {$this->failedTests}\n";
        echo "  📈 Pass Rate:   {$passPercentage}%\n";
        echo "\n";

        if ($passPercentage >= 90) {
            echo "  🎉 EXCELLENT! Your app is ready for production!\n";
        } elseif ($passPercentage >= 70) {
            echo "  👍 GOOD! Some minor issues need fixing.\n";
        } elseif ($passPercentage >= 50) {
            echo "  ⚠️  WARNING! Several issues detected.\n";
        } else {
            echo "  🚨 CRITICAL! Major issues need immediate attention.\n";
        }

        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                   🎯 READINESS ASSESSMENT                    ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "  App Readiness: {$passPercentage}%\n";
        echo "\n";

        if ($this->failedTests > 0) {
            echo "  ⚠️  Failed Tests:\n";
            echo "  ────────────────\n";
            foreach ($this->testResults as $result) {
                if (!$result['passed']) {
                    echo "  • {$result['name']}: {$result['message']}\n";
                }
            }
            echo "\n";
        }

        echo "  📝 Next Steps:\n";
        echo "  ─────────────\n";
        echo "  1. Review failed tests above\n";
        echo "  2. Ensure MySQL is running\n";
        echo "  3. Configure all API keys in .env\n";
        echo "  4. Run Laravel tests: php artisan test\n";
        echo "  5. Test on real devices (iOS & Android)\n";
        echo "\n";
        echo "  For detailed test checklist, see: MOBILE_APP_TEST_SUITE.md\n";
        echo "\n";
    }
}

// Run the tests
try {
    $tester = new APITester();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "❌ Error running tests: " . $e->getMessage() . "\n";
    exit(1);
}
