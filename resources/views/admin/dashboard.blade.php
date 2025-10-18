@extends('layouts.admin')

@section('title', __('admin.dashboard'))
@section('page-title', __('admin.main_dashboard'))

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">{{ __('admin.total_users') }}</p>
                <p class="text-4xl font-bold mt-2">1,234</p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-3xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">{{ __('admin.posts') }}</p>
                <p class="text-4xl font-bold mt-2">5,678</p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-3xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">{{ __('admin.active_subscriptions') }}</p>
                <p class="text-4xl font-bold mt-2">892</p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-credit-card text-3xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm">{{ __('admin.monthly_revenue') }}</p>
                <p class="text-4xl font-bold mt-2">$12.5K</p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('admin.recent_users') }}</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">A</div>
                    <div class="mr-3">
                        <p class="font-semibold">أحمد محمد</p>
                        <p class="text-sm text-gray-500">ahmed@example.com</p>
                    </div>
                </div>
                <span class="text-xs text-gray-500">منذ 5 دقائق</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('admin.recent_ad_requests') }}</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-semibold">طلب إعلان جديد</p>
                    <p class="text-sm text-gray-500">Facebook - $500</p>
                </div>
                <a href="/admin/ad-requests" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
