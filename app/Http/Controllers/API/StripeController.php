<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Subscription as StripeSubscription;
use Stripe\Invoice;
use Stripe\Webhook;
use Carbon\Carbon;

class StripeController extends Controller
{
    public function __construct()
    {
        // Set Stripe API Key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Get Stripe configuration (publishable key)
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'publishable_key' => config('services.stripe.key'),
        ]);
    }

    /**
     * Create Payment Intent for one-time payment
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'metadata' => 'sometimes|array',
        ]);

        $user = Auth::user();

        // Log attempt
        $this->logPaymentAttempt($user->id, [
            'provider' => 'stripe',
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'pending'
        ]);

        try {
            // Create or get Stripe customer
            $customerId = $this->getOrCreateCustomer($user);

            // Create Payment Intent
            $intent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => strtolower($request->currency),
                'customer' => $customerId,
                'metadata' => array_merge([
                    'user_id' => $user->id,
                    'platform' => 'mobile_app',
                ], $request->metadata ?? []),
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent Error: ' . $e->getMessage());

            $this->logPaymentAttempt($user->id, [
                'provider' => 'stripe',
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Subscription
     */
    public function createSubscription(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'price_id' => 'required|string',
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        try {
            // Create or get Stripe customer
            $customerId = $this->getOrCreateCustomer($user);

            // Cancel any existing active subscriptions
            $this->cancelExistingSubscriptions($user->id);

            // Create subscription
            $subscription = StripeSubscription::create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $request->price_id],
                ],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription'
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Save subscription to database
            $dbSubscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'payment_provider' => 'stripe',
                'provider_subscription_id' => $subscription->id,
                'provider_customer_id' => $customerId,
                'status' => $subscription->status === 'active' ? 'active' : 'pending',
                'starts_at' => Carbon::createFromTimestamp($subscription->current_period_start),
                'ends_at' => Carbon::createFromTimestamp($subscription->current_period_end),
                'auto_renew' => true,
            ]);

            // Update user's subscription
            $user->update([
                'current_subscription_plan_id' => $plan->id,
                'subscription_status' => 'active',
            ]);

            // Log successful attempt
            $this->logPaymentAttempt($user->id, [
                'provider' => 'stripe',
                'subscription_plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => $plan->currency ?? 'USD',
                'status' => 'succeeded'
            ]);

            return response()->json([
                'success' => true,
                'subscription' => $dbSubscription,
                'client_secret' => $subscription->latest_invoice->payment_intent->client_secret ?? null,
                'payment_intent_id' => $subscription->latest_invoice->payment_intent->id ?? null,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Stripe Subscription Error: ' . $e->getMessage());

            $this->logPaymentAttempt($user->id, [
                'provider' => 'stripe',
                'subscription_plan_id' => $plan->id,
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add Payment Method
     */
    public function addPaymentMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $customerId = $this->getOrCreateCustomer($user);

            // Attach payment method to customer
            $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);
            $paymentMethod->attach(['customer' => $customerId]);

            // Set as default if it's the first one
            $existingMethods = PaymentMethod::all([
                'customer' => $customerId,
                'type' => 'card',
            ]);

            if (count($existingMethods->data) === 1) {
                Customer::update($customerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'payment_method' => $paymentMethod,
            ]);

        } catch (\Exception $e) {
            Log::error('Add Payment Method Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Payment Methods
     */
    public function getPaymentMethods(): JsonResponse
    {
        $user = Auth::user();

        try {
            if (!$user->stripe_customer_id) {
                return response()->json([
                    'success' => true,
                    'payment_methods' => [],
                ]);
            }

            $paymentMethods = PaymentMethod::all([
                'customer' => $user->stripe_customer_id,
                'type' => 'card',
            ]);

            return response()->json([
                'success' => true,
                'payment_methods' => $paymentMethods->data,
            ]);

        } catch (\Exception $e) {
            Log::error('Get Payment Methods Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set Default Payment Method
     */
    public function setDefaultPaymentMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            if (!$user->stripe_customer_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'No Stripe customer found',
                ], 404);
            }

            Customer::update($user->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated',
            ]);

        } catch (\Exception $e) {
            Log::error('Set Default Payment Method Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete Payment Method
     */
    public function deletePaymentMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        try {
            $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);
            $paymentMethod->detach();

            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted',
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Payment Method Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Subscription
     */
    public function updateSubscription(Request $request): JsonResponse
    {
        $request->validate([
            'subscription_id' => 'required|string',
            'new_price_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $subscription = StripeSubscription::retrieve($request->subscription_id);

            // Update subscription
            StripeSubscription::update($request->subscription_id, [
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'price' => $request->new_price_id,
                    ]
                ],
                'proration_behavior' => 'create_prorations',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Update Subscription Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel Subscription
     */
    public function cancelSubscription(Request $request): JsonResponse
    {
        $request->validate([
            'subscription_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            // Cancel on Stripe
            $subscription = StripeSubscription::retrieve($request->subscription_id);
            $subscription->cancel();

            // Update in database
            Subscription::where('user_id', $user->id)
                ->where('provider_subscription_id', $request->subscription_id)
                ->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                    'auto_renew' => false,
                ]);

            $user->update([
                'subscription_status' => 'canceled',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel Subscription Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Subscription Details
     */
    public function getSubscription(string $subscriptionId): JsonResponse
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);

            return response()->json([
                'success' => true,
                'subscription' => $subscription,
            ]);

        } catch (\Exception $e) {
            Log::error('Get Subscription Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Invoices
     */
    public function getInvoices(Request $request): JsonResponse
    {
        $user = Auth::user();

        try {
            if (!$user->stripe_customer_id) {
                return response()->json([
                    'success' => true,
                    'invoices' => [],
                ]);
            }

            $limit = $request->input('limit', 10);

            $invoices = Invoice::all([
                'customer' => $user->stripe_customer_id,
                'limit' => $limit,
            ]);

            return response()->json([
                'success' => true,
                'invoices' => $invoices->data,
            ]);

        } catch (\Exception $e) {
            Log::error('Get Invoices Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook Handler
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailure($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdate($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionCancelled($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['received' => true]);
    }

    // Helper Methods

    private function getOrCreateCustomer(User $user): string
    {
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    private function cancelExistingSubscriptions(int $userId): void
    {
        Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('payment_provider', 'stripe')
            ->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
    }

    private function handlePaymentSuccess($paymentIntent): void
    {
        $userId = $paymentIntent->metadata->user_id ?? null;

        if ($userId) {
            Payment::create([
                'user_id' => $userId,
                'payment_provider' => 'stripe',
                'provider_payment_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => strtoupper($paymentIntent->currency),
                'status' => 'completed',
                'paid_at' => now(),
            ]);
        }
    }

    private function handlePaymentFailure($paymentIntent): void
    {
        $userId = $paymentIntent->metadata->user_id ?? null;

        if ($userId) {
            $this->logPaymentAttempt($userId, [
                'provider' => 'stripe',
                'status' => 'failed',
                'error_message' => $paymentIntent->last_payment_error->message ?? 'Unknown error'
            ]);
        }
    }

    private function handleSubscriptionUpdate($subscription): void
    {
        $customerId = $subscription->customer;
        $user = User::where('stripe_customer_id', $customerId)->first();

        if ($user) {
            Subscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider_subscription_id' => $subscription->id,
                ],
                [
                    'payment_provider' => 'stripe',
                    'provider_customer_id' => $customerId,
                    'status' => $subscription->status,
                    'starts_at' => Carbon::createFromTimestamp($subscription->current_period_start),
                    'ends_at' => Carbon::createFromTimestamp($subscription->current_period_end),
                    'auto_renew' => !$subscription->cancel_at_period_end,
                ]
            );

            $user->update(['subscription_status' => $subscription->status]);
        }
    }

    private function handleSubscriptionCancelled($subscription): void
    {
        $customerId = $subscription->customer;
        $user = User::where('stripe_customer_id', $customerId)->first();

        if ($user) {
            Subscription::where('user_id', $user->id)
                ->where('provider_subscription_id', $subscription->id)
                ->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                ]);

            $user->update(['subscription_status' => 'canceled']);
        }
    }

    private function handleInvoicePaymentSucceeded($invoice): void
    {
        $customerId = $invoice->customer;
        $user = User::where('stripe_customer_id', $customerId)->first();

        if ($user) {
            Payment::create([
                'user_id' => $user->id,
                'payment_provider' => 'stripe',
                'provider_payment_id' => $invoice->payment_intent,
                'amount' => $invoice->amount_paid / 100,
                'currency' => strtoupper($invoice->currency),
                'status' => 'completed',
                'paid_at' => Carbon::createFromTimestamp($invoice->status_transitions->paid_at),
            ]);
        }
    }

    private function handleInvoicePaymentFailed($invoice): void
    {
        $customerId = $invoice->customer;
        $user = User::where('stripe_customer_id', $customerId)->first();

        if ($user) {
            $this->logPaymentAttempt($user->id, [
                'provider' => 'stripe',
                'amount' => $invoice->amount_due / 100,
                'currency' => strtoupper($invoice->currency),
                'status' => 'failed',
                'error_message' => 'Invoice payment failed'
            ]);
        }
    }

    private function logPaymentAttempt(int $userId, array $data): void
    {
        PaymentAttempt::create(array_merge([
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $data));
    }
}
