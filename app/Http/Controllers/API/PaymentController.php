<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the user's payment history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with('subscription.subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Store a newly created payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'payment_method' => 'required|in:credit_card,paypal,stripe,apple_pay,google_pay',
            'metadata' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Verify subscription belongs to user if provided
        if ($request->subscription_id) {
            $subscription = Subscription::where('id', $request->subscription_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found or does not belong to user'
                ], 404);
            }
        }

        // Generate unique transaction ID
        $transactionId = 'txn_' . Str::random(20);

        // Create payment
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $request->subscription_id,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'payment_method' => $request->payment_method,
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'metadata' => $request->metadata,
            'paid_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'data' => $payment->load('subscription.subscriptionPlan')
        ], 201);
    }

    /**
     * Display the specified payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $payment = Payment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('subscription.subscriptionPlan')
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }
}
