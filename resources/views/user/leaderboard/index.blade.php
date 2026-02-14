<?php
/**
 * Leaderboard View
 * Ranking user berdasarkan aktivitas
 */
?>

@extends('layouts.user')

@section('title', 'Leaderboard - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-500"></i> Leaderboard
            </h2>
        </div>
        
        <!-- Top 3 -->
        <div class="p-6 bg-gradient-to-b from-yellow-50 to-white dark:from-yellow-900/20 dark:to-dark-100">
            <div class="flex justify-center items-end gap-4">
                <!-- 2nd -->
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 mx-auto mb-2 ring-4 ring-gray-300 flex items-center justify-center">
                        <i class="fas fa-medal text-gray-500 text-2xl"></i>
                    </div>
                    <p class="font-medium text-sm">-</p>
                </div>
                
                <!-- 1st -->
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 mx-auto mb-2 ring-4 ring-yellow-400 flex items-center justify-center shadow-lg">
                        <i class="fas fa-crown text-white text-3xl"></i>
                    </div>
                    <p class="font-bold">Top User</p>
                </div>
                
                <!-- 3rd -->
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 mx-auto mb-2 ring-4 ring-orange-300 flex items-center justify-center">
                        <i class="fas fa-medal text-orange-400 text-2xl"></i>
                    </div>
                    <p class="font-medium text-sm">-</p>
                </div>
            </div>
        </div>
        
        <!-- Rankings List -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @php
                $users = \App\Models\User::select('id', 'name', 'avatar')
                    ->withCount('sentMessages')
                    ->orderByDesc('sent_messages_count')
                    ->limit(20)
                    ->get();
            @endphp
            
            @foreach($users as $index => $user)
                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <span class="w-8 text-center font-bold {{ $index < 3 ? 'text-yellow-500' : 'text-gray-400' }}">
                        #{{ $index + 1 }}
                    </span>
                    
                    <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover">
                    
                    <div class="flex-1">
                        <p class="font-medium {{ $user->id === auth()->id() ? 'text-primary-600' : '' }}">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="text-xs bg-primary-100 dark:bg-primary-900/30 px-2 py-0.5 rounded-full">You</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-semibold">{{ $user->sent_messages_count }}</p>
                        <p class="text-xs text-gray-500">messages</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
