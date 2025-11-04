<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected $apiKey;
    protected $iframeId;
    protected $integrationId;
    protected $hmacSecret;
    protected $mode;
    protected $currency;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('paymob_api_key');
        $this->iframeId = Setting::get('paymob_iframe_id');
        $this->integrationId = Setting::get('paymob_integration_id');
        $this->hmacSecret = Setting::get('paymob_hmac_secret');
        $this->mode = Setting::get('paymob_mode', 'sandbox');
        $this->currency = Setting::get('paymob_currency', 'EGP');

        // تحديد رابط API حسب الوضع
        $this->baseUrl = $this->mode === 'live'
            ? 'https://accept.paymob.com/api'
            : 'https://accept.paymobsolutions.com/api';
    }

    /**
     * الحصول على Auth Token من Paymob
     */
    protected function getAuthToken()
    {
        try {
            $response = Http::post("{$this->baseUrl}/auth/tokens", [
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json()['token'];
            }

            Log::error('Paymob Auth Error', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob Auth Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * إنشاء طلب دفع
     */
    public function createOrder($amount, $items = [])
    {
        try {
            $authToken = $this->getAuthToken();

            if (!$authToken) {
                throw new \Exception('فشل في الحصول على Auth Token');
            }

            // تحويل المبلغ لـ cents (قروش)
            $amountCents = $amount * 100;

            $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
                'auth_token' => $authToken,
                'delivery_needed' => 'false',
                'amount_cents' => $amountCents,
                'currency' => $this->currency,
                'items' => $items,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Paymob Order Error', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob Order Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * الحصول على Payment Key
     */
    public function getPaymentKey($orderId, $amount, $billingData)
    {
        try {
            $authToken = $this->getAuthToken();

            if (!$authToken) {
                throw new \Exception('فشل في الحصول على Auth Token');
            }

            // تحويل المبلغ لـ cents (قروش)
            $amountCents = $amount * 100;

            $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
                'auth_token' => $authToken,
                'amount_cents' => $amountCents,
                'expiration' => 3600, // انتهاء الصلاحية بعد ساعة
                'order_id' => $orderId,
                'billing_data' => [
                    'apartment' => $billingData['apartment'] ?? 'NA',
                    'email' => $billingData['email'],
                    'floor' => $billingData['floor'] ?? 'NA',
                    'first_name' => $billingData['first_name'],
                    'street' => $billingData['street'] ?? 'NA',
                    'building' => $billingData['building'] ?? 'NA',
                    'phone_number' => $billingData['phone_number'],
                    'shipping_method' => 'NA',
                    'postal_code' => $billingData['postal_code'] ?? 'NA',
                    'city' => $billingData['city'] ?? 'NA',
                    'country' => $billingData['country'] ?? 'EG',
                    'last_name' => $billingData['last_name'] ?? $billingData['first_name'],
                    'state' => $billingData['state'] ?? 'NA',
                ],
                'currency' => $this->currency,
                'integration_id' => $this->integrationId,
            ]);

            if ($response->successful()) {
                return $response->json()['token'];
            }

            Log::error('Paymob Payment Key Error', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob Payment Key Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * إنشاء رابط الدفع الكامل
     */
    public function createPaymentUrl($subscriptionId, $amount, $userEmail, $userName, $userPhone)
    {
        try {
            // إنشاء الطلب
            $order = $this->createOrder($amount, [
                [
                    'name' => "اشتراك #{$subscriptionId}",
                    'amount_cents' => $amount * 100,
                    'description' => 'اشتراك في Social Media Manager',
                    'quantity' => 1,
                ]
            ]);

            if (!$order) {
                throw new \Exception('فشل في إنشاء الطلب');
            }

            // تجهيز بيانات الفوترة
            $billingData = [
                'email' => $userEmail,
                'first_name' => $userName,
                'phone_number' => $userPhone,
            ];

            // الحصول على Payment Key
            $paymentKey = $this->getPaymentKey($order['id'], $amount, $billingData);

            if (!$paymentKey) {
                throw new \Exception('فشل في الحصول على Payment Key');
            }

            // إنشاء رابط iframe
            $iframeUrl = "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";

            return [
                'success' => true,
                'payment_url' => $iframeUrl,
                'order_id' => $order['id'],
                'payment_token' => $paymentKey,
            ];

        } catch (\Exception $e) {
            Log::error('Paymob Create Payment URL Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * التحقق من صحة HMAC
     */
    public function verifyHmac($data)
    {
        try {
            $hmacData = [
                'amount_cents' => $data['amount_cents'] ?? '',
                'created_at' => $data['created_at'] ?? '',
                'currency' => $data['currency'] ?? '',
                'error_occured' => $data['error_occured'] ?? 'false',
                'has_parent_transaction' => $data['has_parent_transaction'] ?? 'false',
                'id' => $data['id'] ?? '',
                'integration_id' => $data['integration_id'] ?? '',
                'is_3d_secure' => $data['is_3d_secure'] ?? 'false',
                'is_auth' => $data['is_auth'] ?? 'false',
                'is_capture' => $data['is_capture'] ?? 'false',
                'is_refunded' => $data['is_refunded'] ?? 'false',
                'is_standalone_payment' => $data['is_standalone_payment'] ?? 'false',
                'is_voided' => $data['is_voided'] ?? 'false',
                'order' => $data['order']['id'] ?? '',
                'owner' => $data['owner'] ?? '',
                'pending' => $data['pending'] ?? 'false',
                'source_data_pan' => $data['source_data']['pan'] ?? '',
                'source_data_sub_type' => $data['source_data']['sub_type'] ?? '',
                'source_data_type' => $data['source_data']['type'] ?? '',
                'success' => $data['success'] ?? 'false',
            ];

            $concatenatedString = implode('', $hmacData);
            $hash = hash_hmac('sha512', $concatenatedString, $this->hmacSecret);

            return $hash === ($data['hmac'] ?? '');
        } catch (\Exception $e) {
            Log::error('Paymob HMAC Verification Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * التحقق من حالة الدفع
     */
    public function verifyPayment($transactionId)
    {
        try {
            $authToken = $this->getAuthToken();

            if (!$authToken) {
                return false;
            }

            $response = Http::get("{$this->baseUrl}/acceptance/transactions/{$transactionId}", [
                'token' => $authToken,
            ]);

            if ($response->successful()) {
                $transaction = $response->json();
                return $transaction['success'] === true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Paymob Verify Payment Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
