@extends('layouts.admin')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Profile Information -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-user-edit"></i>
                </div>
                المعلومات الشخصية
            </h2>

            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-user ml-2 text-blue-600"></i>
                            الاسم
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope ml-2 text-blue-600"></i>
                            البريد الإلكتروني
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save ml-2"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white ml-3">
                    <i class="fas fa-lock"></i>
                </div>
                تغيير كلمة المرور
            </h2>

            <form action="{{ route('admin.profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-key ml-2 text-amber-600"></i>
                            كلمة المرور الحالية
                        </label>
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-lock ml-2 text-amber-600"></i>
                            كلمة المرور الجديدة
                        </label>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-lock ml-2 text-amber-600"></i>
                            تأكيد كلمة المرور
                        </label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-medium rounded-lg hover:from-amber-600 hover:to-orange-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-key ml-2"></i>
                            تحديث كلمة المرور
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- User Avatar Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="text-center">
                <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-5xl font-bold mx-auto mb-4 shadow-lg">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $user->email }}</p>

                @if($user->role)
                <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-full">
                    <i class="fas fa-crown ml-2 text-purple-600"></i>
                    <span class="text-sm font-medium text-purple-800">{{ ucfirst($user->role) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Account Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800">
                <i class="fas fa-info-circle ml-2 text-blue-600"></i>
                معلومات الحساب
            </h3>
            <div class="space-y-4">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                    <label class="text-sm text-blue-700 font-medium flex items-center mb-2">
                        <i class="fas fa-calendar-plus ml-2"></i>
                        تاريخ التسجيل
                    </label>
                    <p class="font-semibold text-blue-900">{{ $user->created_at->format('Y-m-d') }}</p>
                    <p class="text-xs text-blue-600 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                    <label class="text-sm text-green-700 font-medium flex items-center mb-2">
                        <i class="fas fa-clock ml-2"></i>
                        آخر تحديث
                    </label>
                    <p class="font-semibold text-green-900">{{ $user->updated_at->format('Y-m-d') }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
                </div>

                @if($user->email_verified_at)
                <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg p-4 border border-teal-200">
                    <label class="text-sm text-teal-700 font-medium flex items-center mb-2">
                        <i class="fas fa-check-circle ml-2"></i>
                        البريد مُفعّل
                    </label>
                    <p class="text-xs text-teal-600">{{ $user->email_verified_at->format('Y-m-d') }}</p>
                </div>
                @else
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-lg p-4 border border-yellow-200">
                    <label class="text-sm text-yellow-700 font-medium flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        البريد غير مُفعّل
                    </label>
                    <button class="text-xs text-yellow-600 hover:text-yellow-800 underline">
                        إرسال رابط التفعيل
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
