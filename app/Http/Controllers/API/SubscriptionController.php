<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the user's subscription history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $subscriptions = Subscription::where('user_id', $request->user()->id)
            ->with('subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $subscriptions
        ]);
    }

    /**
     * Get the current active subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        $subscription = Subscription::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with('subscriptionPlan')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription
        ]);
    }

    /**
     * Subscribe to a plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:credit_card,paypal,stripe,apple_pay,google_pay',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $plan = SubscriptionPlan::find($request->subscription_plan_id);

        // Cancel existing active subscriptions
        Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update([
                'status' => 'canceled',
                'canceled_at' => Carbon::now()
            ]);

        // Create new subscription
        $startsAt = Carbon::now();
        $endsAt = $plan->billing_cycle === 'monthly'
            ? $startsAt->copy()->addMonth()
            : $startsAt->copy()->addYear();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        // Update user's current subscription
        $user->update([
            'current_subscription_plan_id' => $plan->id,
            'subscription_status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to ' . $plan->name . ' plan',
            'data' => $subscription->load('subscriptionPlan')
        ], 201);
    }

    /**
     * Cancel the current subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request)
    {
        $subscription = Subscription::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription to cancel'
            ], 404);
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => Carbon::now()
        ]);

        $request->user()->update([
            'subscription_status' => 'canceled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription canceled successfully'
        ]);
    }

    /**
     * Renew an expired subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_method' => 'required|in:credit_card,paypal,stripe,apple_pay,google_pay',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $oldSubscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$oldSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found'
            ], 404);
        }

        $plan = $oldSubscription->subscriptionPlan;

        // Create new subscription
        $startsAt = Carbon::now();
        $endsAt = $plan->billing_cycle === 'monthly'
            ? $startsAt->copy()->addMonth()
            : $startsAt->copy()->addYear();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        // Update user's current subscription
        $user->update([
            'current_subscription_plan_id' => $plan->id,
            'subscription_status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription renewed successfully',
            'data' => $subscription->load('subscriptionPlan')
        ]);
    }
}
