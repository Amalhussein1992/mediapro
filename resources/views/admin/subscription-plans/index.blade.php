@extends('layouts.admin')

@section('title', 'خطط الاشتراك')
@section('page-title', 'إدارة خطط الاشتراك')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">خطط الاشتراك</h2>
            <p class="text-gray-600">إدارة ومتابعة جميع خطط الاشتراك المتاحة</p>
        </div>
        <a href="{{ route('admin.subscription-plans.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-plus ml-2"></i>
            إنشاء خطة جديدة
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">إجمالي الخطط</p>
                    <p class="text-3xl font-bold">{{ $plans->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-box text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">الخطط النشطة</p>
                    <p class="text-3xl font-bold">{{ $plans->where('is_active', 1)->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm mb-1">الخطط غير النشطة</p>
                    <p class="text-3xl font-bold">{{ $plans->where('is_active', 0)->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-pause-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">المشتركون</p>
                    <p class="text-3xl font-bold">{{ $plans->sum('users_count') ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($plans as $plan)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
            <!-- Plan Header -->
            <div class="bg-gradient-to-br from-{{ $plan->is_active ? 'blue' : 'gray' }}-500 to-{{ $plan->is_active ? 'purple' : 'gray' }}-600 p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,0 L100,0 L100,100 Q50,80 0,100 Z" fill="white"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="text-xl font-bold">{{ $plan->name }}</h4>
                        @if($plan->is_active)
                            <span class="bg-green-400 text-white text-xs px-3 py-1 rounded-full font-medium shadow-md">
                                <i class="fas fa-check ml-1"></i>
                                نشط
                            </span>
                        @else
                            <span class="bg-gray-400 text-white text-xs px-3 py-1 rounded-full font-medium shadow-md">
                                <i class="fas fa-pause ml-1"></i>
                                غير نشط
                            </span>
                        @endif
                    </div>

                    <div class="mb-2">
                        <span class="text-4xl font-bold">{{ $currency['symbol'] }}{{ number_format($plan->price, 0) }}</span>
                        <span class="text-blue-100">
                            /{{ $plan->billing_cycle == 'monthly' ? 'شهري' : 'سنوي' }}
                        </span>
                    </div>

                    @if(isset($plan->users_count) && $plan->users_count > 0)
                    <div class="flex items-center text-sm text-blue-100">
                        <i class="fas fa-users ml-1"></i>
                        {{ $plan->users_count }} مشترك
                    </div>
                    @endif
                </div>
            </div>

            <!-- Plan Body -->
            <div class="p-6">
                @if($plan->description)
                <p class="text-gray-600 text-sm mb-4 min-h-[40px]">{{ Str::limit($plan->description, 80) }}</p>
                @else
                <p class="text-gray-400 text-sm mb-4 min-h-[40px] italic">لا يوجد وصف</p>
                @endif

                <!-- Features -->
                <div class="space-y-2 mb-6">
                    @if($plan->max_posts_per_month)
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-check text-green-600 text-xs"></i>
                            </div>
                            <span>{{ $plan->max_posts_per_month }} منشور شهرياً</span>
                        </div>
                    @else
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-infinity text-blue-600 text-xs"></i>
                            </div>
                            <span class="font-semibold">منشورات غير محدودة</span>
                        </div>
                    @endif

                    @if($plan->max_social_accounts)
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-check text-green-600 text-xs"></i>
                            </div>
                            <span>{{ $plan->max_social_accounts }} حساب اجتماعي</span>
                        </div>
                    @else
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-infinity text-blue-600 text-xs"></i>
                            </div>
                            <span class="font-semibold">حسابات غير محدودة</span>
                        </div>
                    @endif

                    @if($plan->max_team_members)
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-check text-green-600 text-xs"></i>
                            </div>
                            <span>{{ $plan->max_team_members }} عضو فريق</span>
                        </div>
                    @endif

                    @if($plan->ai_features)
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-purple-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-robot text-purple-600 text-xs"></i>
                            </div>
                            <span>ميزات الذكاء الاصطناعي</span>
                        </div>
                    @endif

                    @if($plan->analytics)
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-indigo-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-chart-line text-indigo-600 text-xs"></i>
                            </div>
                            <span>التحليلات المتقدمة</span>
                        </div>
                    @endif

                    @if($plan->priority_support)
                        <div class="flex items-center text-gray-700 text-sm">
                            <div class="w-5 h-5 bg-amber-100 rounded-full flex items-center justify-center ml-2">
                                <i class="fas fa-headset text-amber-600 text-xs"></i>
                            </div>
                            <span>دعم فني مميز</span>
                        </div>
                    @endif
                </div>

                <!-- Revenue Info -->
                @if(isset($plan->total_revenue) && $plan->total_revenue > 0)
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-3 mb-4 border border-green-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-green-700 font-medium">الإيرادات الإجمالية</span>
                        <span class="text-lg font-bold text-green-800">{{ $currency['symbol'] }}{{ number_format($plan->total_revenue, 2) }}</span>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.subscription-plans.edit', $plan->id) }}"
                       class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    <form action="{{ route('admin.subscription-plans.destroy', $plan->id) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟\n\nملاحظة: لا يمكن حذف الخطة إذا كان هناك مشتركون بها.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg">
                            <i class="fas fa-trash-alt ml-2"></i>
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-span-full">
            <div class="bg-white rounded-xl shadow-lg p-12 text-center border-2 border-dashed border-gray-300">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد خطط اشتراك</h3>
                <p class="text-gray-600 mb-6">ابدأ بإنشاء أول خطة اشتراك للمستخدمين</p>
                <a href="{{ route('admin.subscription-plans.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء خطة جديدة
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
