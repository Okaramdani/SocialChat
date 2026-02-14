{{-- 
    Admin Dashboard View
    Halaman dashboard admin dengan statistik dan monitoring
    Theme: Purple/Violet - berbeda dari user (blue)
--}}

@extends('layouts.admin')

@section('title', 'Dashboard Admin - Social Chat')

@section('content')
<!-- Welcome Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
    <p class="text-gray-500 dark:text-gray-400">Selamat datang di panel administrasi Social Chat</p>
</div>

<!-- Stats Cards with Gradients -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="relative overflow-hidden bg-gradient-to-br from-admin-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg shadow-admin-500/25">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-2 -right-2 w-16 h-16 bg-white/5 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <span class="text-4xl font-bold">{{ $stats['total_users'] }}</span>
            </div>
            <p class="text-white/80 font-medium">Total Users</p>
            <p class="text-white/60 text-sm mt-1">Semua pengguna terdaftar</p>
        </div>
    </div>

    <!-- Online Users -->
    <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg shadow-green-500/25">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-2 -right-2 w-16 h-16 bg-white/5 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-circle text-xl"></i>
                </div>
                <span class="text-4xl font-bold">{{ $stats['online_users'] }}</span>
            </div>
            <p class="text-white/80 font-medium">Online Sekarang</p>
            <p class="text-white/60 text-sm mt-1">Pengguna aktif saat ini</p>
        </div>
    </div>

    <!-- Total Messages -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/25">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-2 -right-2 w-16 h-16 bg-white/5 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <span class="text-4xl font-bold">{{ $stats['total_messages'] }}</span>
            </div>
            <p class="text-white/80 font-medium">Total Pesan</p>
            <p class="text-white/60 text-sm mt-1">Pesan yang dikirim</p>
        </div>
    </div>

    <!-- Suspended Users -->
    <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg shadow-red-500/25">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-2 -right-2 w-16 h-16 bg-white/5 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-ban text-xl"></i>
                </div>
                <span class="text-4xl font-bold">{{ $stats['suspended_users'] }}</span>
            </div>
            <p class="text-white/80 font-medium">Suspended</p>
            <p class="text-white/60 text-sm mt-1">Akun yang diblokir</p>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Users -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Quick Actions -->
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm border border-admin-100 dark:border-admin-800 p-6">
        <h3 class="font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-bolt text-admin-500 mr-2"></i>Aksi Cepat
        </h3>
        <div class="space-y-3">
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl bg-admin-50 dark:bg-admin-900/30 hover:bg-admin-100 dark:hover:bg-admin-900/50 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-admin-500 text-white flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900 dark:text-white group-hover:text-admin-600">Kelola Users</p>
                    <p class="text-sm text-gray-500">Lihat & manage pengguna</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-admin-500"></i>
            </a>
            
            <a href="{{ route('admin.calls') }}" class="flex items-center gap-3 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-green-500 text-white flex items-center justify-center">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900 dark:text-white group-hover:text-green-600">Riwayat Panggilan</p>
                    <p class="text-sm text-gray-500">Lihat log panggilan</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-500"></i>
            </a>
            
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                    <i class="fas fa-home"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600">Buka Aplikasi</p>
                    <p class="text-sm text-gray-500">Ke halaman utama</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
            </a>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="lg:col-span-2 bg-white dark:bg-dark-100 rounded-2xl shadow-sm border border-admin-100 dark:border-admin-800 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 dark:text-white">
                <i class="fas fa-user-plus text-admin-500 mr-2"></i>User Terbaru
            </h3>
            <a href="{{ route('admin.users') }}" class="text-sm text-admin-500 hover:text-admin-600 font-medium">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentUsers as $user)
            <div class="p-4 hover:bg-admin-50 dark:hover:bg-admin-900/20 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img src="{{ $user->avatar ? asset('storage/avatars/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.$user->name.'&background=8b5cf6&color=fff' }}" 
                                 class="w-10 h-10 rounded-full object-cover">
                            @if($user->is_online)
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($user->role === 'admin')
                        <span class="px-2 py-1 text-xs font-medium bg-admin-100 text-admin-600 dark:bg-admin-800 dark:text-admin-300 rounded-full">Admin</span>
                        @else
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-full">User</span>
                        @endif
                        <div class="flex gap-1">
                            <a href="{{ route('admin.user.detail', $user) }}" class="p-2 text-admin-500 hover:bg-admin-100 dark:hover:bg-admin-800 rounded-lg transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$user->isAdmin())
                            <form action="{{ route('admin.user.suspend', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Suspend" onclick="return confirm('Suspend user ini?')">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                <p>Belum ada user</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- System Info -->
<div class="mt-6 bg-gradient-to-r from-admin-800 to-purple-800 rounded-2xl p-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h4 class="font-bold text-lg">Social Chat Admin Panel</h4>
            <p class="text-white/70 text-sm">Versi 1.0.0 • Laravel {{ Illuminate\Support\Facades\App::version() }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-white/60">Waktu Server</p>
            <p class="font-mono text-lg">{{ now()->format('H:i:s') }}</p>
        </div>
    </div>
</div>
@endsection
