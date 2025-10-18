<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminSubscriptionController extends Controller
{
    /**
     * Display a listing of the subscriptions.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'subscriptionPlan']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->has('plan_id') && $request->plan_id != '') {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        // Search by user
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        $plans = \App\Models\SubscriptionPlan::all();

        return view('admin.subscriptions.index', compact('subscriptions', 'plans'));
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'subscriptionPlan', 'payments']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel a user's subscription.
     */
    public function cancel(Subscription $subscription)
    {
        if ($subscription->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Only active subscriptions can be canceled.');
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => Carbon::now()
        ]);

        $subscription->user->update([
            'subscription_status' => 'canceled'
        ]);

        return redirect()->back()
            ->with('success', 'Subscription canceled successfully.');
    }
}
