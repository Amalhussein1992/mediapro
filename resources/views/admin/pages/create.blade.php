@extends('layouts.admin')

@section('title', 'إضافة صفحة جديدة')
@section('page-title', 'إضافة صفحة جديدة')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-plus"></i>
                </div>
                إضافة صفحة جديدة
            </h2>
            <p class="text-gray-600 mt-2 mr-15">قم بملء النموذج أدناه لإضافة صفحة جديدة للموقع</p>
        </div>

        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf

            <!-- المعلومات الأساسية -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-blue-500">المعلومات الأساسية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-heading ml-2 text-blue-600"></i>
                            العنوان (English)
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            placeholder="e.g., Features"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-heading ml-2 text-purple-600"></i>
                            العنوان (عربي)
                        </label>
                        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar') }}"
                            placeholder="مثال: الميزات"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('title_ar') border-red-500 @enderror">
                        @error('title_ar')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- الرابط والأيقونة -->
            <div class="mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-link ml-2 text-green-600"></i>
                            الرابط (Slug)
                        </label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                            placeholder="e.g., features (اتركه فارغاً للتوليد التلقائي)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('slug') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            سيتم توليد الرابط تلقائياً من العنوان إذا تركته فارغاً
                        </p>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-icons ml-2 text-amber-600"></i>
                            الأيقونة (اختياري)
                        </label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                            placeholder="e.g., fa-star or fas fa-star"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-lightbulb ml-1"></i>
                            استخدم أيقونات Font Awesome (مثال: fas fa-star)
                        </p>
                    </div>
                </div>
            </div>

            <!-- المحتوى -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-purple-500">المحتوى</h3>
                <div class="space-y-6">
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-align-right ml-2 text-blue-600"></i>
                            المحتوى (English)
                        </label>
                        <textarea name="content" id="content" rows="8"
                            placeholder="Write the page content here..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="content_ar" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-align-right ml-2 text-purple-600"></i>
                            المحتوى (عربي)
                        </label>
                        <textarea name="content_ar" id="content_ar" rows="8"
                            placeholder="اكتب محتوى الصفحة هنا..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('content_ar') border-red-500 @enderror">{{ old('content_ar') }}</textarea>
                        @error('content_ar')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-green-500">تحسين محركات البحث (SEO)</h3>
                <div class="space-y-4">
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-search ml-2 text-green-600"></i>
                            وصف SEO (English)
                        </label>
                        <textarea name="meta_description" id="meta_description" rows="2"
                            placeholder="Meta description for search engines..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('meta_description') }}</textarea>
                    </div>

                    <div>
                        <label for="meta_description_ar" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-search ml-2 text-green-600"></i>
                            وصف SEO (عربي)
                        </label>
                        <textarea name="meta_description_ar" id="meta_description_ar" rows="2"
                            placeholder="وصف الصفحة لمحركات البحث..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('meta_description_ar') }}</textarea>
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tags ml-2 text-green-600"></i>
                            الكلمات المفتاحية (Keywords)
                        </label>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}"
                            placeholder="keyword1, keyword2, keyword3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            افصل الكلمات المفتاحية بفاصلة
                        </p>
                    </div>
                </div>
            </div>

            <!-- الإعدادات -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b-2 border-indigo-500">الإعدادات</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="section" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-layer-group ml-2 text-indigo-600"></i>
                            القسم
                        </label>
                        <select name="section" id="section" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="main" {{ old('section') == 'main' ? 'selected' : '' }}>الصفحات الرئيسية (Main)</option>
                            <option value="features" {{ old('section') == 'features' ? 'selected' : '' }}>الميزات (Features)</option>
                            <option value="company" {{ old('section') == 'company' ? 'selected' : '' }}>الشركة (Company)</option>
                            <option value="resources" {{ old('section') == 'resources' ? 'selected' : '' }}>الموارد (Resources)</option>
                            <option value="legal" {{ old('section') == 'legal' ? 'selected' : '' }}>القانونية (Legal)</option>
                        </select>
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-sort-numeric-down ml-2 text-indigo-600"></i>
                            الترتيب
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                            placeholder="0"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            الرقم الأصغر يظهر أولاً
                        </p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-toggle-on ml-2 text-indigo-600"></i>
                            الحالة
                        </label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور (Published)</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة (Draft)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- العرض -->
            <div class="mb-8 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 border-2 border-blue-200">
                <h4 class="text-base font-bold text-gray-700 mb-4">عرض الصفحة في:</h4>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="show_in_header" value="1" {{ old('show_in_header', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-bars ml-2 text-blue-600"></i>
                            القائمة العلوية (Header)
                        </span>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="mr-3 text-sm font-medium text-gray-800 flex items-center">
                            <i class="fas fa-shoe-prints ml-2 text-purple-600"></i>
                            التذييل (Footer)
                        </span>
                    </label>
                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                <a href="{{ route('admin.pages.index') }}"
                   class="inline-flex items-center px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة الصفحة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
