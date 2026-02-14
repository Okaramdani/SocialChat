{{-- 
    Admin Layout
    Layout untuk halaman admin panel
    Includes: sidebar admin, stats header, content area
    Theme: Purple/Violet - berbeda dari user (blue/primary)
--}}

<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    sidebarOpen: false,
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" x-init="
    if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        darkMode = true;
        document.documentElement.classList.add('dark');
    }
" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Social Chat')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e' },
                        admin: { 50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9', 800: '#5b21b6', 900: '#4c1d95', 950: '#2e1065' },
                        purple: { 50: '#faf5ff', 100: '#f3e8ff', 200: '#e9d5ff', 300: '#d8b4fe', 400: '#c084fc', 500: '#a855f7', 600: '#9333ea', 700: '#7e22ce', 800: '#6b21a8', 900: '#581c87', 950: '#3b0764' },
                        danger: { 50: '#fef2f2', 100: '#fee2e2', 500: '#ef4444', 600: '#dc2626' },
                        success: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a' },
                        warning: { 50: '#fffbeb', 100: '#fef3c7', 500: '#f59e0b', 600: '#d97706' },
                        dark: { 100: '#1e293b', 200: '#0f172a', 300: '#020617' }
                    }
                }
            }
        }
    </script>
    
    <style>
        * { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        body { font-family: 'Inter', sans-serif; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        
        .admin-sidebar { @apply w-64 bg-white dark:bg-dark-100 border-r border-gray-200 dark:border-gray-700; }
    </style>
    
    @stack('styles')
</head>

<body class="bg-gradient-to-br from-admin-50 to-purple-100 dark:from-slate-900 dark:to-purple-950 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex">
        <!-- Admin Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
               class="admin-sidebar fixed lg:static inset-y-0 left-0 z-50 transform transition-transform duration-300 flex flex-col bg-gradient-to-b from-admin-900 to-purple-950 dark:from-admin-950 dark:to-black border-r border-admin-800 dark:border-admin-800">
            <!-- Logo -->
            <div class="h-14 flex items-center justify-between px-4 border-b border-admin-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-admin-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-admin-500/30">
                        <i class="fas fa-shield-halved text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-lg text-white">Admin Panel</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-2">
                    <i class="fas fa-times text-white/60"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4 overflow-y-auto">
                <!-- Menu Section -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-semibold text-admin-400 uppercase tracking-wider mb-2">Menu</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="nav-item-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-admin-800">
                                <i class="fas fa-chart-line text-admin-400"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ route('admin.users') }}" class="nav-item-admin {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-admin-800">
                                <i class="fas fa-users text-admin-400"></i>
                            </div>
                            <span>Kelola Users</span>
                        </a>
                        
                        <a href="{{ route('admin.calls') }}" class="nav-item-admin {{ request()->routeIs('admin.calls') ? 'active' : '' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-admin-800">
                                <i class="fas fa-phone text-admin-400"></i>
                            </div>
                            <span>Riwayat Panggilan</span>
                        </a>
                    </div>
                </div>
                
                <!-- System Section -->
                <div class="mb-6">
                    <p class="px-3 text-xs font-semibold text-admin-400 uppercase tracking-wider mb-2">Sistem</p>
                    <div class="space-y-1">
                        <div class="px-3 py-2.5 rounded-xl bg-admin-800/50 text-admin-300 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Admin hanya bisa memantau, tidak bisa menggunakan fitur user
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-item-admin w-full text-left text-red-400 hover:text-red-300">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/20">
                                    <i class="fas fa-sign-out-alt text-red-400"></i>
                                </div>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
            
            <!-- Admin Info -->
            <div class="p-4 border-t border-admin-800">
                <div class="flex items-center gap-3">
                    <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" 
                         class="w-10 h-10 rounded-lg object-cover border-2 border-admin-500">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-admin-300 font-medium">Administrator</p>
                    </div>
                    <button @click="toggleDarkMode()" class="p-2 rounded-lg hover:bg-admin-800">
                        <i :class="darkMode ? 'fas fa-sun text-yellow-400' : 'fas fa-moon text-admin-300'" class="text-sm"></i>
                    </button>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Header -->
            <header class="h-14 bg-gradient-to-r from-admin-800 to-purple-800 dark:from-admin-900 dark:to-admin-950 flex items-center justify-between px-4 sticky top-0 z-40 shadow-lg shadow-admin-500/20">
    <!-- Left: Logo & Brand -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-admin-700/50 transition-colors">
            <i class="fas fa-bars text-white/80"></i>
        </button>
        
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-br from-admin-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-admin-500/30">
                <i class="fas fa-shield-halved text-white text-sm"></i>
            </div>
            <div class="hidden sm:block">
                <span class="font-bold text-white text-lg tracking-wide">Admin Panel</span>
                <p class="text-xs text-admin-200">Social Chat Management</p>
            </div>
        </a>
    </div>
    
    <!-- Right: Admin Actions -->
    <div class="flex items-center gap-2">
        <!-- Online Users -->
        <div class="flex items-center gap-2 px-3 py-1.5 bg-admin-700/50 rounded-full border border-admin-600">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></span>
            <span class="text-sm text-white/90 font-medium" id="onlineCount">Loading...</span>
        </div>
        
        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode()" class="p-2.5 rounded-lg hover:bg-admin-700/50 transition-colors">
            <i :class="darkMode ? 'fas fa-sun text-yellow-400' : 'fas fa-moon text-white/70'" class="text-lg"></i>
        </button>
        
        <!-- Notifications -->
        <div x-data="{ notificationsOpen: false }" class="relative">
            <button @click="notificationsOpen = !notificationsOpen" class="p-2.5 rounded-lg hover:bg-admin-700/50 transition-colors relative">
                <i class="fas fa-bell text-white/80 text-lg"></i>
            </button>
        </div>
        
        <!-- Profile Dropdown -->
        <div x-data="{ profileOpen: false }" class="relative">
            <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-admin-700/50 transition-colors">
                <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" 
                     class="w-8 h-8 rounded-lg object-cover border-2 border-admin-400">
                <i class="fas fa-chevron-down text-xs text-white/50"></i>
            </button>
            
            <div x-show="profileOpen" x-cloak @click.away="profileOpen = false"
                 class="absolute right-0 mt-2 w-56 bg-admin-800 rounded-xl shadow-xl border border-admin-700 py-1 z-50">
                <div class="px-4 py-3 border-b border-admin-700">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-admin-300">Administrator</p>
                </div>
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-admin-700/50">
                    <i class="fas fa-arrow-left w-4"></i> Kembali ke App
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-admin-700/50 w-full">
                        <i class="fas fa-sign-out-alt w-4"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
            
            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-6 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>
    
    <!-- Styles for nav items -->
    <style>
        .nav-item-admin {
            @apply flex items-center gap-3 px-3 py-2.5 rounded-xl text-admin-200 hover:bg-admin-800/60 hover:text-white transition-all duration-200;
        }
        .nav-item-admin.active {
            @apply bg-gradient-to-r from-admin-500 to-purple-600 text-white shadow-lg shadow-admin-500/25;
        }
        .nav-item-admin.active div {
            @apply bg-white/20;
        }
    </style>
    
    <script>
        // Fetch online users count
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/admin/online-users')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('onlineCount').textContent = data.length + ' online';
                })
                .catch(() => {});
        });
    </script>
    
    @stack('scripts')
</body>
</html>
