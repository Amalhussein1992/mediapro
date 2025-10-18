@extends('layouts.admin')

@section('title', 'إنشاء خطة اشتراك جديدة')
@section('page-title', 'إنشاء خطة اشتراك جديدة')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-plus"></i>
                </div>
                إنشاء خطة اشتراك جديدة
            </h2>
            <p class="text-gray-600 mt-2 mr-15">قم بملء النموذج أدناه لإنشاء خطة اشتراك جديدة للمستخدمين</p>
        </div>

        <form action="{{ route('admin.subscription-plans.store') }}" method="POST">
            @csrf

            <!-- المعلومات الأساسية -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-blue-500">المعلومات الأساسية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tag ml-2 text-blue-600"></i>
                            اسم الخطة
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="مثال: الخطة الاحترافية"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-dollar-sign ml-2 text-green-600"></i>
                            السعر ({{ $currency['code'] }} - {{ $currency['name_ar'] }})
                        </label>
                        <div class="relative">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">{{ $currency['symbol'] }}</span>
                            <input type="number" name="price" id="price" value="{{ old('price', 0) }}" step="0.01" min="0" required
                                placeholder="0.00"
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            يمكنك تغيير العملة الافتراضية من صفحة <a href="{{ route('admin.settings.index') }}" class="text-blue-600 hover:underline">الإعدادات</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- الوصف -->
            <div class="mb-8">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-align-right ml-2 text-purple-600"></i>
                    الوصف
                </label>
                <textarea name="description" id="description" rows="3"
                    placeholder="اكتب وصفاً تفصيلياً للخطة يوضح مزاياها وفوائدها..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2 flex items-center">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- دورة الفوترة -->
            <div class="mb-8">
                <label for="billing_cycle" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-calendar-alt ml-2 text-indigo-600"></i>
                    دورة الفوترة
                </label>
                <select name="billing_cycle" id="billing_cycle" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>شهري</option>
                    <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>سنوي</option>
                </select>
                @error('billing_cycle')
                    <p class="text-red-500 text-sm mt-2 flex items-center">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- الحدود والقيود -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-green-500">الحدود والقيود</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="max_posts_per_month" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-file-alt ml-2 text-blue-600"></i>
                            الحد الأقصى للمنشورات/شهر
                        </label>
                        <input type="number" name="max_posts_per_month" id="max_posts_per_month" value="{{ old('max_posts_per_month') }}" min="0"
                            placeholder="0 = غير محدود"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            اتركه 0 أو فارغاً للاستخدام غير المحدود
                        </p>
                    </div>

                    <div>
                        <label for="max_social_accounts" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-share-alt ml-2 text-purple-600"></i>
                            الحد الأقصى للحسابات الاجتماعية
                        </label>
                        <input type="number" name="max_social_accounts" id="max_social_accounts" value="{{ old('max_social_accounts') }}" min="0"
                            placeholder="0 = غير محدود"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            اتركه 0 أو فارغاً للاستخدام غير المحدود
                        </p>
                    </div>

                    <div>
                        <label for="max_team_members" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-users ml-2 text-green-600"></i>
                            الحد الأقصى لأعضاء الفريق
                        </label>
                        <input type="number" name="max_team_members" id="max_team_members" value="{{ old('max_team_members') }}" min="0"
                            placeholder="0 = غير محدود"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            اتركه 0 أو فارغاً للاستخدام غير المحدود
                        </p>
                    </div>
                </div>
            </div>

            <!-- الميزات -->
            <div class="mb-8">
                <label for="features" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-list-ul ml-2 text-amber-600"></i>
                    الميزات (ميزة واحدة في كل سطر)
                </label>
                <textarea name="features" id="features" rows="6"
                    placeholder="تحليلات متقدمة&#10;دعم فني مخصص&#10;علامة تجارية مخصصة&#10;الوصول إلى API&#10;تقارير مفصلة"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent font-mono">{{ old('features') }}</textarea>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-lightbulb ml-1"></i>
                    اكتب ميزة واحدة في كل سطر
                </p>
            </div>

            <!-- الميزات الإضافية -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-purple-500">الميزات الإضافية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 bg-purple-50 rounded-lg border-2 border-purple-200 cursor-pointer hover:bg-purple-100 transition-colors">
                        <input type="checkbox" name="ai_features" value="1" {{ old('ai_features') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-robot ml-2 text-purple-600"></i>
                            ميزات الذكاء الاصطناعي
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-indigo-50 rounded-lg border-2 border-indigo-200 cursor-pointer hover:bg-indigo-100 transition-colors">
                        <input type="checkbox" name="analytics" value="1" {{ old('analytics') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-chart-line ml-2 text-indigo-600"></i>
                            التحليلات المتقدمة
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-amber-50 rounded-lg border-2 border-amber-200 cursor-pointer hover:bg-amber-100 transition-colors">
                        <input type="checkbox" name="priority_support" value="1" {{ old('priority_support') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-headset ml-2 text-amber-600"></i>
                            دعم فني مميز
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-cyan-50 rounded-lg border-2 border-cyan-200 cursor-pointer hover:bg-cyan-100 transition-colors">
                        <input type="checkbox" name="custom_branding" value="1" {{ old('custom_branding') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-palette ml-2 text-cyan-600"></i>
                            علامة تجارية مخصصة
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-green-50 rounded-lg border-2 border-green-200 cursor-pointer hover:bg-green-100 transition-colors">
                        <input type="checkbox" name="api_access" value="1" {{ old('api_access') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-code ml-2 text-green-600"></i>
                            الوصول إلى API
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-rose-50 rounded-lg border-2 border-rose-200 cursor-pointer hover:bg-rose-100 transition-colors">
                        <input type="checkbox" name="white_label" value="1" {{ old('white_label') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-tag ml-2 text-rose-600"></i>
                            White Label
                        </span>
                    </label>
                </div>
            </div>

            <!-- الحالة -->
            <div class="mb-8 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 border-2 border-blue-200">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="mr-3 text-base font-medium text-gray-800 flex items-center">
                        <i class="fas fa-eye ml-2 text-blue-600"></i>
                        مفعّل (مرئي للمستخدمين)
                    </span>
                </label>
                <p class="text-sm text-gray-600 mt-2 mr-8">
                    <i class="fas fa-info-circle ml-1"></i>
                    إذا كانت الخطة مفعّلة، ستكون مرئية للمستخدمين ويمكنهم الاشتراك بها
                </p>
            </div>

            <!-- الأزرار -->
            <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                <a href="{{ route('admin.subscription-plans.index') }}"
                   class="inline-flex items-center px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء الخطة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
