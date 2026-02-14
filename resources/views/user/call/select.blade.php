<?php
/**
 * Call Select View
 * Pilih chat atau user untuk memulai panggilan
 */
?>

@extends('layouts.user')

@section('title', 'Start Call - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold">Start Call</h2>
            <p class="text-sm text-gray-500">Pilih percakapan untuk memulai panggilan</p>
        </div>

        <div class="p-4">
            @forelse($chats as $chat)
                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 mb-2">
                    <div class="flex items-center gap-3">
                        @if($chat->type === 'group')
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white">
                                <i class="fas fa-users"></i>
                            </div>
                        @else
                            @php
                                $otherUser = $chat->participants()->where('user_id', '!=', auth()->id())->first();
                            @endphp
                            <img src="{{ $otherUser?->avatar_url }}" class="w-12 h-12 rounded-xl object-cover">
                        @endif
                        <div>
                            <p class="font-medium">{{ $chat->getDisplayName(auth()->user()) }}</p>
                            <p class="text-xs text-gray-500">{{ $chat->type === 'group' ? $chat->participants->count() . ' participants' : ($otherUser?->is_online ? 'Online' : 'Offline') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('call.voice', $chat) }}" class="p-3 rounded-xl bg-green-500 text-white hover:bg-green-600">
                            <i class="fas fa-phone"></i>
                        </a>
                        <a href="{{ route('call.video', $chat) }}" class="p-3 rounded-xl bg-primary-500 text-white hover:bg-primary-600">
                            <i class="fas fa-video"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-phone text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada percakapan</p>
                    <a href="{{ route('dashboard') }}" class="mt-3 inline-block px-4 py-2 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                        Mulai percakapan
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
