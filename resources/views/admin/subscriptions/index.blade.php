@extends('layouts.admin')

@section('title', __('admin.subscriptions'))
@section('header', __('admin.subscriptions'))

@section('content')
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.subscriptions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">{{ __('admin.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>{{ __('admin.canceled') }}</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('admin.expired') }}</option>
                    <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>{{ __('admin.trial') }}</option>
                    <option value="past_due" {{ request('status') == 'past_due' ? 'selected' : '' }}>{{ __('admin.past_due') }}</option>
                </select>
            </div>

            <div>
                <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.plan') }}</label>
                <select name="plan_id" id="plan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">{{ __('admin.all_plans') }}</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.search_user') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('admin.name_or_email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    {{ __('admin.filter') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.user') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.plan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.started') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.ends') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscriptions as $subscription)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $subscription->subscriptionPlan->name }}</div>
                        <div class="text-sm text-gray-500">${{ number_format($subscription->subscriptionPlan->price, 2) }}/{{ $subscription->subscriptionPlan->billing_cycle }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($subscription->status == 'active')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('admin.active') }}
                            </span>
                        @elseif($subscription->status == 'canceled')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ __('admin.canceled') }}
                            </span>
                        @elseif($subscription->status == 'expired')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ __('admin.expired') }}
                            </span>
                        @elseif($subscription->status == 'trial')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ __('admin.trial') }}
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ __('admin.past_due') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscription->starts_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : __('admin.na') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-primary hover:text-blue-900">{{ __('admin.view') }}</a>
                        @if($subscription->status == 'active')
                            <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_cancel_subscription') }}');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.cancel') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        {{ __('admin.no_subscriptions_found') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
