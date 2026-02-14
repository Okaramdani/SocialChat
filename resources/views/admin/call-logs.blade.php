{{-- Admin Call Logs View - Daftar semua riwayat panggilan --}}

@extends('layouts.admin')

@section('title', 'Riwayat Panggilan - Admin')
@section('header', 'Riwayat Panggilan')

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Panggilan</h1>
        <p class="text-gray-500 dark:text-gray-400">Monitor semua panggilan video & voice</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-admin-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Total Panggilan</p>
                <p class="text-2xl font-bold">{{\App\Models\CallLog::count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-phone"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Video Call</p>
                <p class="text-2xl font-bold">{{\App\Models\CallLog::where('type', 'video')->count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-video"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Voice Call</p>
                <p class="text-2xl font-bold">{{\App\Models\CallLog::where('type', 'voice')->count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-microphone"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-red-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Missed</p>
                <p class="text-2xl font-bold">{{\App\Models\CallLog::where('status', 'missed')->count()}}</p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-phone-slash"></i>
            </div>
        </div>
    </div>
</div>

<!-- Calls Table -->
<div class="bg-white dark:bg-dark-100 rounded-xl shadow-sm border border-admin-100 dark:border-admin-800 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-list text-admin-500 mr-2"></i>Daftar Panggilan
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-admin-50 dark:bg-admin-900/30">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Pemanggil</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Penerima</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Durasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-admin-600 dark:text-admin-400 uppercase">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($calls as $call)
                <tr class="hover:bg-admin-50 dark:hover:bg-admin-900/20 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $call->caller->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $call->caller->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $call->receiver->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $call->receiver->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($call->type === 'video') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                            @else bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 @endif">
                            <i class="fas fa-{{ $call->type === 'video' ? 'video' : 'microphone' }} mr-1"></i>
                            {{ ucfirst($call->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($call->status === 'answered') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
                            @elseif($call->status === 'missed') bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
                            @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 @endif">
                            @if($call->status === 'answered')<i class="fas fa-check mr-1"></i>
                            @elseif($call->status === 'missed')<i class="fas fa-times mr-1"></i>
                            @else<i class="fas fa-clock mr-1"></i>@endif
                            {{ ucfirst($call->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $call->duration ? gmdate('i:s', $call->duration) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $call->created_at->format('d M, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-phone-slash text-4xl text-gray-300 mb-3"></i>
                            <p class="font-medium">Belum ada riwayat panggilan</p>
                            <p class="text-sm text-gray-400">Panggilan akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($calls->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        {{ $calls->links() }}
    </div>
    @endif
</div>
@endsection
