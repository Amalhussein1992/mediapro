@extends('layouts.admin')

@section('title', 'Subscription Details')
@section('header', 'Subscription Details')

@section('content')
<div class="container mx-auto max-w-5xl">
    <div class="mb-6">
        <a href="{{ route('admin.subscriptions.index') }}" class="text-primary hover:text-blue-900">
            &larr; Back to Subscriptions
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Subscription Info</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Status:</span>
                    <div class="mt-1">
                        @if($subscription->status == 'active')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @elseif($subscription->status == 'canceled')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Canceled
                            </span>
                        @elseif($subscription->status == 'expired')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Expired
                            </span>
                        @elseif($subscription->status == 'trial')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Trial
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Past Due
                            </span>
                        @endif
                    </div>
                </div>
                @if($subscription->starts_at)
                <div>
                    <span class="text-sm text-gray-600">Started:</span>
                    <p class="text-sm font-medium text-gray-900">{{ $subscription->starts_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
                @if($subscription->ends_at)
                <div>
                    <span class="text-sm text-gray-600">Ends:</span>
                    <p class="text-sm font-medium text-gray-900">{{ $subscription->ends_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
                @if($subscription->canceled_at)
                <div>
                    <span class="text-sm text-gray-600">Canceled:</span>
                    <p class="text-sm font-medium text-gray-900">{{ $subscription->canceled_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">User Info</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Name:</span>
                    <p class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Email:</span>
                    <p class="text-sm font-medium text-gray-900">{{ $subscription->user->email }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">User ID:</span>
                    <p class="text-sm font-medium text-gray-900">#{{ $subscription->user->id }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Plan Info</h3>
            @if($subscription->subscriptionPlan)
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Plan:</span>
                    <p class="text-sm font-medium text-gray-900">{{ $subscription->subscriptionPlan->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Price:</span>
                    <p class="text-sm font-medium text-gray-900">${{ number_format($subscription->subscriptionPlan->price, 2) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Billing:</span>
                    <p class="text-sm font-medium text-gray-900">{{ ucfirst($subscription->subscriptionPlan->billing_cycle) }}</p>
                </div>
            </div>
            @else
            <p class="text-sm text-gray-500">No plan information available</p>
            @endif
        </div>
    </div>

    @if($subscription->subscriptionPlan)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Plan Features</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-sm text-gray-600">Posts per Month:</span>
                <p class="text-sm font-medium text-gray-900">
                    {{ $subscription->subscriptionPlan->max_posts_per_month ?? 'Unlimited' }}
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Social Accounts:</span>
                <p class="text-sm font-medium text-gray-900">
                    {{ $subscription->subscriptionPlan->max_social_accounts ?? 'Unlimited' }}
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Team Members:</span>
                <p class="text-sm font-medium text-gray-900">
                    {{ $subscription->subscriptionPlan->max_team_members ?? 'Unlimited' }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscription->payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->transaction_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->status == 'completed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @elseif($payment->status == 'pending')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($payment->status == 'failed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Failed
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Refunded
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($subscription->status == 'active')
    <div class="mt-6 flex justify-end">
        <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                Cancel Subscription
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
