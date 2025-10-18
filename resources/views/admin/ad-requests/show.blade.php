@extends('layouts.admin')

@section('title', 'تفاصيل طلب الإعلان #' . $adRequest->id)
@section('page-title')
    <div class="flex items-center justify-between">
        <span>تفاصيل طلب الإعلان #{{ $adRequest->id }}</span>
        <a href="/admin/ad-requests"
           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-slate-600 to-slate-700 text-white text-sm font-medium rounded-lg hover:from-slate-700 hover:to-slate-800 transition-all duration-200 shadow-md hover:shadow-lg">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                المعلومات الأساسية
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <label class="text-sm text-blue-700 font-medium flex items-center mb-2">
                        <i class="fas fa-tag ml-2"></i>
                        نوع الإعلان
                    </label>
                    <p class="font-bold text-lg text-blue-900">{{ $adRequest->ad_type }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <label class="text-sm text-purple-700 font-medium flex items-center mb-2">
                        <i class="fas fa-share-alt ml-2"></i>
                        المنصة
                    </label>
                    <p class="font-bold text-lg text-purple-900">
                        <i class="fab fa-{{ strtolower($adRequest->platform) }} ml-1"></i>
                        {{ $adRequest->platform }}
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <label class="text-sm text-green-700 font-medium flex items-center mb-2">
                        <i class="fas fa-dollar-sign ml-2"></i>
                        الميزانية
                    </label>
                    <p class="font-bold text-2xl text-green-900">${{ number_format($adRequest->budget, 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                    <label class="text-sm text-orange-700 font-medium flex items-center mb-2">
                        <i class="fas fa-clock ml-2"></i>
                        المدة
                    </label>
                    <p class="font-bold text-lg text-orange-900">{{ $adRequest->duration_days }} يوم</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 border border-indigo-200">
                    <label class="text-sm text-indigo-700 font-medium flex items-center mb-2">
                        <i class="fas fa-calendar-plus ml-2"></i>
                        تاريخ البدء
                    </label>
                    <p class="font-bold text-lg text-indigo-900">{{ $adRequest->start_date ? \Carbon\Carbon::parse($adRequest->start_date)->format('Y-m-d') : 'غير محدد' }}</p>
                </div>
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 border border-pink-200">
                    <label class="text-sm text-pink-700 font-medium flex items-center mb-2">
                        <i class="fas fa-calendar-check ml-2"></i>
                        تاريخ الانتهاء
                    </label>
                    <p class="font-bold text-lg text-pink-900">{{ $adRequest->end_date ? \Carbon\Carbon::parse($adRequest->end_date)->format('Y-m-d') : 'غير محدد' }}</p>
                </div>
            </div>
        </div>

        <!-- Target Audience -->
        @if($adRequest->target_audience)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-users"></i>
                </div>
                الجمهور المستهدف
            </h2>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-lg border border-gray-200">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ json_encode(json_decode($adRequest->target_audience), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        <!-- Objectives -->
        @if($adRequest->objectives)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-bullseye"></i>
                </div>
                الأهداف
            </h2>
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 rounded-lg border border-amber-200">
                <p class="text-gray-800 leading-relaxed">{{ $adRequest->objectives }}</p>
            </div>
        </div>
        @endif

        <!-- Content -->
        @if($adRequest->content)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-file-alt"></i>
                </div>
                المحتوى
            </h2>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-lg border border-gray-200">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ json_encode(json_decode($adRequest->content), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        <!-- Metrics -->
        @if($adRequest->metrics)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-chart-bar"></i>
                </div>
                المقاييس والإحصائيات
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $metrics = json_decode($adRequest->metrics, true);
                @endphp
                @if($metrics)
                    @foreach($metrics as $key => $value)
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 p-6 rounded-xl text-center text-white transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <div class="text-4xl font-bold mb-2">{{ $value }}</div>
                        <div class="text-sm text-blue-100 uppercase tracking-wider">{{ $key }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                حالة الطلب
            </h2>
            @php
                $statusGradients = [
                    'pending' => 'from-yellow-400 to-yellow-600',
                    'in_review' => 'from-blue-400 to-blue-600',
                    'approved' => 'from-green-400 to-green-600',
                    'running' => 'from-purple-400 to-purple-600',
                    'completed' => 'from-gray-400 to-gray-600',
                    'rejected' => 'from-red-400 to-red-600',
                ];
                $statusIcons = [
                    'pending' => 'fa-clock',
                    'in_review' => 'fa-search',
                    'approved' => 'fa-check-circle',
                    'running' => 'fa-play-circle',
                    'completed' => 'fa-check-double',
                    'rejected' => 'fa-times-circle',
                ];
                $statusLabels = [
                    'pending' => 'قيد الانتظار',
                    'in_review' => 'قيد المراجعة',
                    'approved' => 'مُوافق عليها',
                    'running' => 'قيد التشغيل',
                    'completed' => 'مكتملة',
                    'rejected' => 'مرفوضة',
                ];
            @endphp
            <div class="bg-gradient-to-br {{ $statusGradients[$adRequest->status] ?? 'from-gray-400 to-gray-600' }} rounded-xl p-6 text-white text-center shadow-lg">
                <i class="fas {{ $statusIcons[$adRequest->status] ?? 'fa-circle' }} text-4xl mb-3"></i>
                <div class="text-2xl font-bold">{{ $statusLabels[$adRequest->status] ?? $adRequest->status }}</div>
            </div>

            <!-- Status Actions -->
            <div class="mt-6 space-y-3">
                @if($adRequest->status === 'pending')
                <form method="POST" action="/admin/ad-requests/{{ $adRequest->id }}/update-status">
                    @csrf
                    <input type="hidden" name="status" value="in_review">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-search ml-2"></i>
                        بدء المراجعة
                    </button>
                </form>
                @endif

                @if($adRequest->status === 'in_review')
                <form method="POST" action="/admin/ad-requests/{{ $adRequest->id }}/update-status">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 rounded-lg hover:from-green-600 hover:to-green-700 font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-check-circle ml-2"></i>
                        موافقة
                    </button>
                </form>
                <form method="POST" action="/admin/ad-requests/{{ $adRequest->id }}/update-status">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 rounded-lg hover:from-red-600 hover:to-red-700 font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-times-circle ml-2"></i>
                        رفض
                    </button>
                </form>
                @endif

                @if($adRequest->status === 'approved')
                <form method="POST" action="/admin/ad-requests/{{ $adRequest->id }}/update-status">
                    @csrf
                    <input type="hidden" name="status" value="running">
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-play-circle ml-2"></i>
                        بدء التشغيل
                    </button>
                </form>
                @endif

                @if($adRequest->status === 'running')
                <form method="POST" action="/admin/ad-requests/{{ $adRequest->id }}/update-status">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="w-full bg-gradient-to-r from-gray-600 to-gray-700 text-white py-3 rounded-lg hover:from-gray-700 hover:to-gray-800 font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-check-double ml-2"></i>
                        إنهاء الحملة
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-user"></i>
                </div>
                معلومات المستخدم
            </h2>
            <div class="space-y-4">
                <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg p-4 border border-pink-200">
                    <label class="text-sm text-pink-700 font-medium flex items-center mb-2">
                        <i class="fas fa-user-tag ml-2"></i>
                        الاسم
                    </label>
                    <p class="font-bold text-lg text-pink-900">{{ $adRequest->user->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                    <label class="text-sm text-blue-700 font-medium flex items-center mb-2">
                        <i class="fas fa-envelope ml-2"></i>
                        البريد الإلكتروني
                    </label>
                    <p class="font-semibold text-sm text-blue-900 break-all">{{ $adRequest->user->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                التواريخ
            </h2>
            <div class="space-y-4">
                <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg p-4 border border-teal-200">
                    <label class="text-sm text-teal-700 font-medium flex items-center mb-2">
                        <i class="fas fa-calendar-plus ml-2"></i>
                        تاريخ الإنشاء
                    </label>
                    <p class="font-semibold text-teal-900">{{ $adRequest->created_at->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-teal-600 mt-1">{{ $adRequest->created_at->diffForHumans() }}</p>
                </div>
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-lg p-4 border border-cyan-200">
                    <label class="text-sm text-cyan-700 font-medium flex items-center mb-2">
                        <i class="fas fa-calendar-check ml-2"></i>
                        آخر تحديث
                    </label>
                    <p class="font-semibold text-cyan-900">{{ $adRequest->updated_at->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-cyan-600 mt-1">{{ $adRequest->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Delete -->
        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-lg p-6 border-2 border-red-200">
            <h2 class="text-xl font-bold mb-4 text-red-600 flex items-center">
                <i class="fas fa-exclamation-triangle ml-2"></i>
                منطقة الخطر
            </h2>
            <p class="text-sm text-red-600 mb-4">سيتم حذف الطلب نهائياً ولا يمكن استرجاعه</p>
            <form method="POST" action="/admin/ad-requests/{{ $adRequest->id }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟ هذا الإجراء لا يمكن التراجع عنه.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-rose-600 text-white py-3 rounded-lg hover:from-red-700 hover:to-rose-700 font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-trash-alt ml-2"></i>
                    حذف الطلب نهائياً
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
