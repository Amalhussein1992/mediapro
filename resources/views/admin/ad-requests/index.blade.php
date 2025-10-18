@extends('layouts.admin')

@section('title', 'طلبات الإعلانات')
@section('page-title', 'إدارة طلبات الإعلانات')

@section('content')
<!-- Stats Cards with Modern Gradients and Icons -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <!-- Total -->
    <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-200 mb-1">إجمالي الطلبات</p>
                <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-rectangle-ad text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-yellow-50 mb-1">قيد الانتظار</p>
                <p class="text-3xl font-bold">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-clock text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- In Review -->
    <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-blue-50 mb-1">قيد المراجعة</p>
                <p class="text-3xl font-bold">{{ $stats['in_review'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-search text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Approved -->
    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-green-50 mb-1">مُوافق عليها</p>
                <p class="text-3xl font-bold">{{ $stats['approved'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Running -->
    <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-purple-50 mb-1">قيد التشغيل</p>
                <p class="text-3xl font-bold">{{ $stats['running'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-play-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-red-50 mb-1">مرفوضة</p>
                <p class="text-3xl font-bold">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-times-circle text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Budget Card -->
<div class="bg-gradient-to-br from-orange-500 via-pink-500 to-purple-600 rounded-xl shadow-2xl p-8 mb-8 text-white transform hover:shadow-3xl transition-all duration-300">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-lg text-white/90 mb-2 flex items-center">
                <i class="fas fa-dollar-sign ml-2"></i>
                إجمالي الميزانية
            </p>
            <p class="text-5xl font-bold">${{ number_format($stats['total_budget'], 2) }}</p>
            <p class="text-sm text-white/70 mt-2">جميع الحملات الإعلانية</p>
        </div>
        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
            <i class="fas fa-chart-line text-4xl"></i>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6" x-data="{ showFilters: false }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center">
            <i class="fas fa-filter ml-2 text-blue-600"></i>
            تصفية النتائج
        </h3>
        <button @click="showFilters = !showFilters" class="text-blue-600 hover:text-blue-800">
            <i class="fas" :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </button>
    </div>
    <div x-show="showFilters" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
                <option value="">الكل</option>
                <option value="pending">قيد الانتظار</option>
                <option value="in_review">قيد المراجعة</option>
                <option value="approved">مُوافق عليها</option>
                <option value="running">قيد التشغيل</option>
                <option value="rejected">مرفوضة</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المنصة</label>
            <select class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
                <option value="">الكل</option>
                <option value="facebook">Facebook</option>
                <option value="instagram">Instagram</option>
                <option value="twitter">Twitter</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
            <input type="date" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
            <input type="date" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($adRequests->count() > 0)
    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">النوع</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">المنصة</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">الميزانية</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">التاريخ</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($adRequests as $request)
                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-gray-900">#{{ $request->id }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold ml-3">
                                {{ substr($request->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700">{{ $request->ad_type }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                            <i class="fab fa-{{ strtolower($request->platform) }} ml-1"></i>
                            {{ $request->platform }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-lg font-bold text-green-600">${{ number_format($request->budget, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->status === 'pending')
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock ml-1"></i> قيد الانتظار
                            </span>
                        @elseif($request->status === 'in_review')
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-search ml-1"></i> قيد المراجعة
                            </span>
                        @elseif($request->status === 'approved')
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle ml-1"></i> مُوافق عليها
                            </span>
                        @elseif($request->status === 'running')
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-play-circle ml-1"></i> قيد التشغيل
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle ml-1"></i> مرفوضة
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <i class="fas fa-calendar ml-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="/admin/ad-requests/{{ $request->id }}"
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-eye ml-2"></i>
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4 p-4">
        @foreach($adRequests as $request)
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md p-4 border border-gray-200 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-gray-900">#{{ $request->id }}</span>
                @if($request->status === 'pending')
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">قيد الانتظار</span>
                @elseif($request->status === 'approved')
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">مُوافق عليها</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $request->status }}</span>
                @endif
            </div>
            <div class="space-y-2 mb-3">
                <p class="text-sm"><span class="font-semibold text-gray-700">المستخدم:</span> {{ $request->user->name ?? 'N/A' }}</p>
                <p class="text-sm"><span class="font-semibold text-gray-700">النوع:</span> {{ $request->ad_type }}</p>
                <p class="text-sm"><span class="font-semibold text-gray-700">المنصة:</span> {{ $request->platform }}</p>
                <p class="text-sm"><span class="font-semibold text-gray-700">الميزانية:</span> <span class="text-green-600 font-bold">${{ number_format($request->budget, 2) }}</span></p>
                <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
            </div>
            <a href="/admin/ad-requests/{{ $request->id }}"
               class="block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                <i class="fas fa-eye ml-2"></i>
                عرض التفاصيل
            </a>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $adRequests->links() }}
    </div>
    @else
    <div class="p-16 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-inbox text-4xl text-gray-400"></i>
        </div>
        <p class="text-xl font-semibold text-gray-600 mb-2">لا توجد طلبات إعلانات</p>
        <p class="text-sm text-gray-500">سيتم عرض طلبات الإعلانات هنا عند إضافتها</p>
    </div>
    @endif
</div>
@endsection
