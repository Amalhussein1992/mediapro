@extends('layouts.admin')

@section('title', 'تعديل خطة الاشتراك')
@section('page-title', 'تعديل خطة الاشتراك')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-edit"></i>
                </div>
                تعديل: {{ $subscriptionPlan->name }}
            </h2>
        </div>

        <form action="{{ route('admin.subscription-plans.update', $subscriptionPlan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- المعلومات الأساسية -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-blue-500">المعلومات الأساسية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tag ml-2 text-blue-600"></i>
                            اسم الخطة
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $subscriptionPlan->name) }}" required
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
                            <input type="number" name="price" id="price" value="{{ old('price', $subscriptionPlan->price) }}" step="0.01" min="0" required
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
                    placeholder="اكتب وصفاً تفصيلياً للخطة..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $subscriptionPlan->description) }}</textarea>
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
                    <option value="monthly" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) == 'monthly' ? 'selected' : '' }}>شهري</option>
                    <option value="yearly" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) == 'yearly' ? 'selected' : '' }}>سنوي</option>
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
                        <input type="number" name="max_posts_per_month" id="max_posts_per_month" value="{{ old('max_posts_per_month', $subscriptionPlan->max_posts_per_month) }}" min="0"
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
                        <input type="number" name="max_social_accounts" id="max_social_accounts" value="{{ old('max_social_accounts', $subscriptionPlan->max_social_accounts) }}" min="0"
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
                        <input type="number" name="max_team_members" id="max_team_members" value="{{ old('max_team_members', $subscriptionPlan->max_team_members) }}" min="0"
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent font-mono">{{ old('features', !empty($subscriptionPlan->features) ? (is_array(json_decode($subscriptionPlan->features)) ? implode("\n", json_decode($subscriptionPlan->features)) : $subscriptionPlan->features) : '') }}</textarea>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-lightbulb ml-1"></i>
                    اكتب ميزة واحدة في كل سطر
                </p>
            </div>

            <!-- الحالة -->
            <div class="mb-8 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 border-2 border-blue-200">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscriptionPlan->is_active) ? 'checked' : '' }}
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
                    <i class="fas fa-save ml-2"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
