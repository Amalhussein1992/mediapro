<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - Media Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }

        .sidebar-scrollbar::-webkit-scrollbar { width: 6px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .animate-slide-in { animation: slideIn 0.3s ease-out; }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenuOpen"
         @click="mobileMenuOpen = false"
         x-cloak
         class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Sidebar -->
    <aside
        x-show="sidebarOpen || mobileMenuOpen"
        @click.away="mobileMenuOpen = false"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 h-full w-72 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl z-50 overflow-y-auto sidebar-scrollbar">

        <!-- Logo Section -->
        <div class="p-6 border-b border-slate-700/50">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    @php
                        $appName = DB::table('app_settings')->where('key', 'app_name')->value('value') ?? config('app.name', 'Media Pro');
                        $logoExists = file_exists(public_path('storage/logo.png')) ||
                                     file_exists(public_path('storage/logo.jpg')) ||
                                     file_exists(public_path('storage/logo.svg'));
                    @endphp

                    @if($logoExists)
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('storage/logo.png') }}?v={{ time() }}"
                                 alt="{{ $appName }}"
                                 class="max-h-12 w-auto object-contain"
                                 onerror="this.src='{{ asset('storage/logo.jpg') }}'; this.onerror=function(){this.src='{{ asset('storage/logo.svg') }}'};">
                            <div>
                                <h1 class="text-lg font-bold text-white">{{ $appName }}</h1>
                                <p class="text-xs text-slate-400 mt-0.5">لوحة التحكم الإدارية</p>
                            </div>
                        </div>
                    @else
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                                {{ $appName }}
                            </h1>
                            <p class="text-xs text-slate-400 mt-1 font-medium">لوحة التحكم الإدارية</p>
                        </div>
                    @endif
                </div>
                <button @click="mobileMenuOpen = false" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-700/50">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- User Info -->
        <div class="p-4 mx-4 my-4 rounded-xl bg-gradient-to-r from-blue-600/20 to-purple-600/20 border border-blue-500/30">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@admin.com' }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="px-3 pb-4">
            <!-- Main Section -->
            <div class="mb-6">
                <p class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">القائمة الرئيسية</p>

                <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-blue-600 hover:to-purple-600 hover:text-white transition-all {{ request()->is('admin/dashboard') || request()->is('admin') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span class="font-medium">الرئيسية</span>
                </a>

                <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-blue-600 hover:to-purple-600 hover:text-white transition-all {{ request()->is('admin/users*') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="font-medium">المستخدمين</span>
                </a>

                <a href="/admin/posts" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-blue-600 hover:to-purple-600 hover:text-white transition-all {{ request()->is('admin/posts*') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span class="font-medium">المنشورات</span>
                </a>
            </div>

            <!-- Subscriptions Section -->
            <div class="mb-6">
                <p class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">الاشتراكات والمدفوعات</p>

                <a href="/admin/subscription-plans" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-emerald-600 hover:to-teal-600 hover:text-white transition-all {{ request()->is('admin/subscription-plans*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-box w-5 text-center"></i>
                    <span class="font-medium">خطط الاشتراك</span>
                </a>

                <a href="/admin/subscriptions" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-emerald-600 hover:to-teal-600 hover:text-white transition-all {{ request()->is('admin/subscriptions') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-credit-card w-5 text-center"></i>
                    <span class="font-medium">الاشتراكات</span>
                </a>

                <a href="/admin/payments" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-emerald-600 hover:to-teal-600 hover:text-white transition-all {{ request()->is('admin/payments*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-money-bill-wave w-5 text-center"></i>
                    <span class="font-medium">المدفوعات</span>
                </a>
            </div>

            <!-- Ads Section -->
            <div class="mb-6">
                <p class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">الإعلانات</p>

                <a href="/admin/ads-campaigns" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-orange-600 hover:to-red-600 hover:text-white transition-all {{ request()->is('admin/ads-campaigns*') ? 'bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-bullhorn w-5 text-center"></i>
                    <span class="font-medium">الحملات الإعلانية</span>
                </a>

                <a href="/admin/ad-requests" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-orange-600 hover:to-red-600 hover:text-white transition-all {{ request()->is('admin/ad-requests*') ? 'bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-ad w-5 text-center"></i>
                    <span class="font-medium">طلبات الإعلانات</span>
                    @php
                        $pendingCount = \App\Models\AdRequest::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                    <span class="mr-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-bold shadow-lg">
                        {{ $pendingCount }}
                    </span>
                    @endif
                </a>
            </div>

            <!-- Content Section -->
            <div class="mb-6">
                <p class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">المحتوى</p>

                <a href="/admin/pages" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-pink-600 hover:to-rose-600 hover:text-white transition-all {{ request()->is('admin/pages*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span class="font-medium">إدارة الصفحات</span>
                </a>

                <a href="/admin/brand-kits" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-pink-600 hover:to-rose-600 hover:text-white transition-all {{ request()->is('admin/brand-kits*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-palette w-5 text-center"></i>
                    <span class="font-medium">العلامة التجارية</span>
                </a>

                <a href="/admin/translations" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-pink-600 hover:to-rose-600 hover:text-white transition-all {{ request()->is('admin/translations*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-language w-5 text-center"></i>
                    <span class="font-medium">الترجمات</span>
                </a>
            </div>

            <!-- Settings Section -->
            <div class="mb-6">
                <p class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">النظام</p>

                <a href="/admin/settings" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-indigo-600 hover:to-purple-600 hover:text-white transition-all {{ request()->is('admin/settings*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="font-medium">الإعدادات</span>
                </a>

                <a href="/admin/analytics" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-slate-300 hover:bg-gradient-to-r hover:from-indigo-600 hover:to-purple-600 hover:text-white transition-all {{ request()->is('admin/analytics*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span class="font-medium">التحليلات</span>
                </a>
            </div>

            <!-- Logout -->
            <div class="px-4 pt-4 border-t border-slate-700/50">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-red-400 hover:bg-red-900/20 hover:text-red-300 transition-all">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span class="font-medium">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="transition-all duration-300" :class="sidebarOpen ? 'lg:mr-72' : 'mr-0'">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 glass-effect border-b border-gray-200/50 shadow-sm">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <!-- Sidebar Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <!-- Page Title -->
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                            @yield('page-title', 'لوحة التحكم')
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">مرحباً بك في لوحة التحكم</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                                <h3 class="font-semibold">الإشعارات</h3>
                                <p class="text-xs opacity-90">لديك 3 إشعارات جديدة</p>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors">
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-ad text-yellow-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">طلب إعلان جديد</p>
                                            <p class="text-xs text-gray-500 mt-1">منذ 5 دقائق</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">عرض جميع الإشعارات</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 transition-all">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-slate-600"></i>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             x-transition
                             class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="font-semibold text-gray-800">{{ auth()->user()->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->email ?? 'admin@admin.com' }}</p>
                            </div>
                            <div class="py-2">
                                <a href="/admin/profile" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-user w-4 text-center text-gray-400"></i>
                                    <span>الملف الشخصي</span>
                                </a>
                                <a href="/admin/settings" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-cog w-4 text-center text-gray-400"></i>
                                    <span>الإعدادات</span>
                                </a>
                            </div>
                            <div class="border-t border-gray-100">
                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                        <span>تسجيل الخروج</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 min-h-screen">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm animate-slide-in">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm animate-slide-in">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white/50 backdrop-blur-sm">
            <div class="px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-600">
                    <p class="flex items-center gap-2">
                        <span>©</span>
                        <span>2025</span>
                        <span class="font-semibold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Media Pro</span>
                        <span>•</span>
                        <span>جميع الحقوق محفوظة</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fas fa-code text-blue-600"></i>
                        <span>نسخة 1.0.0</span>
                    </p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
