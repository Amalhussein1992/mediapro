<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    /**
     * إنشاء عملية دفع جديدة
     */
    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|exists:subscriptions,id',
                'email' => 'required|email',
                'name' => 'required|string',
                'phone' => 'required|string',
            ]);

            $subscription = Subscription::findOrFail($request->subscription_id);

            // إنشاء رابط الدفع
            $result = $this->paymobService->createPaymentUrl(
                $subscription->id,
                $subscription->price,
                $request->email,
                $request->name,
                $request->phone
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في إنشاء رابط الدفع: ' . ($result['error'] ?? 'خطأ غير معروف'),
                ], 500);
            }

            // حفظ سجل الدفع في قاعدة البيانات
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'subscription_id' => $subscription->id,
                'amount' => $subscription->price,
                'currency' => 'EGP',
                'payment_method' => 'paymob',
                'gateway' => 'paymob',
                'status' => 'pending',
                'gateway_transaction_id' => $result['order_id'],
                'gateway_response' => [
                    'order_id' => $result['order_id'],
                    'payment_token' => $result['payment_token'],
                ],
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Initiation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء عملية الدفع',
            ], 500);
        }
    }

    /**
     * معالجة callback من Paymob (عند نجاح/فشل الدفع)
     */
    public function handleCallback(Request $request)
    {
        try {
            $success = $request->query('success') === 'true';
            $orderId = $request->query('order');

            if ($success) {
                return redirect('/payment/success?order=' . $orderId);
            } else {
                return redirect('/payment/failed?order=' . $orderId);
            }

        } catch (\Exception $e) {
            Log::error('Payment Callback Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/payment/failed');
        }
    }

    /**
     * معالجة Webhook من Paymob
     */
    public function handleWebhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Paymob Webhook Received', ['data' => $data]);

            // التحقق من صحة HMAC
            if (!$this->paymobService->verifyHmac($data)) {
                Log::warning('Invalid HMAC from Paymob', ['data' => $data]);
                return response()->json(['message' => 'Invalid HMAC'], 403);
            }

            $orderId = $data['order']['id'] ?? null;
            $success = $data['success'] ?? false;
            $transactionId = $data['id'] ?? null;

            if (!$orderId) {
                return response()->json(['message' => 'Order ID not found'], 400);
            }

            // البحث عن الدفع
            $payment = Payment::where('gateway_transaction_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found for order', ['order_id' => $orderId]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // تحديث حالة الدفع
            DB::beginTransaction();

            try {
                $payment->update([
                    'status' => $success ? 'completed' : 'failed',
                    'gateway_transaction_id' => $transactionId,
                    'gateway_response' => array_merge(
                        $payment->gateway_response ?? [],
                        ['webhook_data' => $data]
                    ),
                    'paid_at' => $success ? now() : null,
                ]);

                // إذا نجح الدفع، تحديث اشتراك المستخدم
                if ($success && $payment->user_id) {
                    $user = User::find($payment->user_id);
                    if ($user) {
                        $user->update([
                            'subscription_id' => $payment->subscription_id,
                            'subscription_status' => 'active',
                            'subscription_started_at' => now(),
                            'subscription_ends_at' => now()->addMonth(),
                        ]);
                    }
                }

                DB::commit();

                return response()->json(['message' => 'Webhook processed successfully']);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Payment Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Error processing webhook'], 500);
        }
    }

    /**
     * صفحة نجاح الدفع
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order');

        return view('payment.success', [
            'order_id' => $orderId,
        ]);
    }

    /**
     * صفحة فشل الدفع
     */
    public function failed(Request $request)
    {
        $orderId = $request->query('order');

        return view('payment.failed', [
            'order_id' => $orderId,
        ]);
    }
}
