@extends('layouts.admin')

@section('title', 'Edit Translation')
@section('header', 'Edit Translation')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Edit Translation</h3>
                    <p class="text-sm text-gray-500 mt-1">Key: {{ $translation->key }}</p>
                </div>
                <a href="{{ route('admin.translations.index') }}" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.translations.update', $translation) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Key Field -->
            <div>
                <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                    Translation Key <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="key"
                    id="key"
                    value="{{ old('key', $translation->key) }}"
                    placeholder="e.g., hero.title, footer.copyright"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('key') border-red-500 @enderror"
                >
                @error('key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Use dot notation for hierarchical keys (e.g., section.subsection.key)</p>
            </div>

            <!-- Group Field -->
            <div>
                <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                    Group <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="group"
                        id="group"
                        value="{{ old('group', $translation->group) }}"
                        list="groupsList"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('group') border-red-500 @enderror"
                    >
                    <datalist id="groupsList">
                        @foreach($groups as $group)
                            <option value="{{ $group }}">
                        @endforeach
                        <option value="general">
                        <option value="hero">
                        <option value="features">
                        <option value="stats">
                        <option value="footer">
                    </datalist>
                </div>
                @error('group')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Select existing group or create a new one</p>
            </div>

            <!-- English Value -->
            <div>
                <label for="value_en" class="block text-sm font-medium text-gray-700 mb-2">
                    English Value <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="value_en"
                    id="value_en"
                    rows="4"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('value_en') border-red-500 @enderror"
                >{{ old('value_en', $translation->value_en) }}</textarea>
                @error('value_en')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Arabic Value -->
            <div>
                <label for="value_ar" class="block text-sm font-medium text-gray-700 mb-2">
                    Arabic Value <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="value_ar"
                    id="value_ar"
                    rows="4"
                    dir="rtl"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('value_ar') border-red-500 @enderror"
                >{{ old('value_ar', $translation->value_ar) }}</textarea>
                @error('value_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Section -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Preview</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">English</p>
                        <div id="preview_en" class="text-sm text-gray-900 p-2 bg-white rounded border border-gray-200 min-h-[60px]"></div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Arabic</p>
                        <div id="preview_ar" class="text-sm text-gray-900 p-2 bg-white rounded border border-gray-200 min-h-[60px]" dir="rtl"></div>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Metadata</h4>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Created:</span> {{ $translation->created_at->format('M d, Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Last Updated:</span> {{ $translation->updated_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <form action="{{ route('admin.translations.destroy', $translation) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this translation?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Delete</span>
                    </button>
                </form>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.translations.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Update Translation</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live preview functionality
    const valueEnInput = document.getElementById('value_en');
    const valueArInput = document.getElementById('value_ar');
    const previewEn = document.getElementById('preview_en');
    const previewAr = document.getElementById('preview_ar');

    valueEnInput.addEventListener('input', function() {
        previewEn.textContent = this.value || 'English preview will appear here...';
    });

    valueArInput.addEventListener('input', function() {
        previewAr.textContent = this.value || 'سيظهر النص العربي هنا...';
    });

    // Initialize preview
    previewEn.textContent = valueEnInput.value || 'English preview will appear here...';
    previewAr.textContent = valueArInput.value || 'سيظهر النص العربي هنا...';
</script>
@endpush
