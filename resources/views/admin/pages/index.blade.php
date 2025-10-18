@extends('layouts.admin')

@section('title', 'إدارة الصفحات')
@section('page-title', 'إدارة صفحات الموقع')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">إدارة الصفحات</h2>
            <p class="text-gray-600">إدارة جميع صفحات الموقع (المنتج، الميزات، الأسعار، من نحن، إلخ)</p>
        </div>
        <a href="{{ route('admin.pages.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-plus ml-2"></i>
            إضافة صفحة جديدة
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg flex items-center">
        <i class="fas fa-check-circle ml-3 text-xl"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle ml-3 text-xl"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">إجمالي الصفحات</p>
                    <p class="text-3xl font-bold">{{ $pages->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">الصفحات المنشورة</p>
                    <p class="text-3xl font-bold">{{ $pages->where('status', 'published')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm mb-1">المسودات</p>
                    <p class="text-3xl font-bold">{{ $pages->where('status', 'draft')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-edit text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">الأقسام</p>
                    <p class="text-3xl font-bold">{{ $pagesBySection->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pages by Section -->
    @foreach($pagesBySection as $sectionName => $sectionPages)
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100">
        <div class="flex items-center mb-4 pb-3 border-b-2 border-gray-200">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                <i class="fas fa-folder"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">
                @switch($sectionName)
                    @case('main') الصفحات الرئيسية @break
                    @case('features') الميزات @break
                    @case('company') الشركة @break
                    @case('resources') الموارد @break
                    @case('legal') القانونية @break
                    @default {{ $sectionName }}
                @endswitch
                <span class="text-sm text-gray-500 font-normal mr-2">({{ $sectionPages->count() }} صفحة)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">العنوان</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">الرابط (Slug)</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">الحالة</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">الترتيب</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">في الرأس</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">في التذييل</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sectionPages as $page)
                    <tr class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $page->id }}</td>
                        <td class="px-4 py-4">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $page->title }}</p>
                                @if($page->title_ar)
                                <p class="text-sm text-gray-500">{{ $page->title_ar }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $page->slug }}</code>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($page->status === 'published')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check ml-1"></i>
                                    منشور
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-edit ml-1"></i>
                                    مسودة
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">
                                {{ $page->order }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($page->show_in_header)
                                <i class="fas fa-check-circle text-green-600 text-lg"></i>
                            @else
                                <i class="fas fa-times-circle text-gray-400 text-lg"></i>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($page->show_in_footer)
                                <i class="fas fa-check-circle text-green-600 text-lg"></i>
                            @else
                                <i class="fas fa-times-circle text-gray-400 text-lg"></i>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pages.edit', $page->id) }}"
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الصفحة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm"
                                            title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($pages->count() === 0)
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border-2 border-dashed border-gray-300">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-file-alt text-5xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد صفحات</h3>
        <p class="text-gray-600 mb-6">ابدأ بإنشاء أول صفحة للموقع</p>
        <a href="{{ route('admin.pages.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus ml-2"></i>
            إضافة صفحة جديدة
        </a>
    </div>
    @endif
</div>
@endsection
