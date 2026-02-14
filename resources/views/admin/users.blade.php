{{-- Admin Users View - Daftar semua user dengan fitur suspend/delete --}}

@extends('layouts.admin')

@section('title', 'Kelola Users - Admin')
@section('header', 'Kelola Users')

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Users</h1>
        <p class="text-gray-500 dark:text-gray-400">Kelola semua pengguna aplikasi</p>
    </div>
    <div class="flex gap-2">
        <span class="px-3 py-1.5 bg-admin-100 dark:bg-admin-800 text-admin-600 dark:text-admin-300 rounded-lg text-sm font-medium">
            <i class="fas fa-users mr-1"></i> {{ $users->total() }} Users
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-admin-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Total Users</p>
                <p class="text-2xl font-bold">{{ $users->total() }}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Online</p>
                <p class="text-2xl font-bold">{{\App\Models\User::where('is_online', true)->count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-red-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Suspended</p>
                <p class="text-2xl font-bold">{{\App\Models\User::where('is_suspended', true)->count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Admins</p>
                <p class="text-2xl font-bold">{{\App\Models\User::where('role', 'admin')->count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/ items-center justify-center20 rounded-lg flex">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white dark:bg-dark-100 rounded-xl shadow-sm border border-admin-100 dark:border-admin-800 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-list text-admin-500 mr-2"></i>Daftar Users
            </h3>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-admin-50 dark:bg-admin-900/30">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Bergabung</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($users as $user)
                <tr class="hover:bg-admin-50 dark:hover:bg-admin-900/20 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover">
                                @if($user->is_online && !$user->is_suspended)
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($user->is_suspended)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">Suspended</span>
                        @elseif($user->is_online)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">Online</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Offline</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($user->role === 'admin')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-admin-100 text-admin-600 dark:bg-admin-800 dark:text-admin-300">
                                <i class="fas fa-shield-alt mr-1"></i>Admin
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">User</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1">
                            <a href="{{ route('admin.user.detail', $user) }}" class="p-2 text-admin-500 hover:bg-admin-100 dark:hover:bg-admin-800 rounded-lg transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($user->role !== 'admin')
                                @if($user->is_suspended)
                                    <form action="{{ route('admin.user.unsuspend', $user) }}" method="POST">
                                        @csrf
                                        <button class="p-2 text-green-500 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors" title="Aktifkan">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.user.suspend', $user) }}" method="POST">
                                        @csrf
                                        <button class="p-2 text-orange-500 hover:bg-orange-100 dark:hover:bg-orange-900/30 rounded-lg transition-colors" title="Suspend" onclick="return confirm('Suspend user ini?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.user.delete', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        {{ $users->links() }}
    </div>
</div>
@endsection
