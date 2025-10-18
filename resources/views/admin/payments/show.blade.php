@extends('layouts.admin')

@section('title', 'Payment Details')
@section('header', 'Payment Details')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.payments.index') }}" class="text-primary hover:text-blue-900">
            &larr; Back to Payments
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Payment #{{ $payment->id }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $payment->created_at->format('F d, Y \a\t H:i') }}</p>
            </div>
            <div>
                @if($payment->status == 'completed')
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                        Completed
                    </span>
                @elseif($payment->status == 'pending')
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pending
                    </span>
                @elseif($payment->status == 'failed')
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                        Failed
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                        Refunded
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gradient-to-r from-primary to-secondary rounded-lg p-6 text-white">
                <p class="text-sm opacity-90 mb-2">Total Amount</p>
                <p class="text-4xl font-bold">${{ number_format($payment->amount, 2) }}</p>
                <p class="text-sm opacity-90 mt-2">{{ $payment->currency }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-2">Transaction ID</p>
                <p class="text-lg font-mono font-bold text-gray-900">{{ $payment->transaction_id }}</p>
                <p class="text-sm text-gray-600 mt-4 mb-2">Payment Method</p>
                <p class="text-lg font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">User Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ $payment->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">User ID</p>
                    <p class="text-sm font-medium text-gray-900">#{{ $payment->user->id }}</p>
                </div>
                @if($payment->paid_at)
                <div>
                    <p class="text-sm text-gray-600">Paid At</p>
                    <p class="text-sm font-medium text-gray-900">{{ $payment->paid_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($payment->subscription)
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Subscription Information</h4>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Plan</p>
                        <p class="text-sm font-medium text-gray-900">{{ $payment->subscription->subscriptionPlan->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Subscription Status</p>
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($payment->subscription->status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Billing Cycle</p>
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($payment->subscription->subscriptionPlan->billing_cycle) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.subscriptions.show', $payment->subscription) }}" class="text-primary hover:text-blue-900 text-sm font-medium">
                        View Subscription &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if($payment->metadata)
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h4>
            <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif>
    </div>
</div>
@endsection
