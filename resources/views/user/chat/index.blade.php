<?php
/**
 * Chat Index View
 * List semua chat user
 */
?>

@extends('layouts.user')

@section('title', 'Chats - Social Chat')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold">All Chats</h2>
        </div>
        
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($chats as $chat)
                @include('components.chat-item', ['chat' => $chat, 'user' => auth()->user()])
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-comment-dots text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada percakapan</p>
                    <a href="{{ route('dashboard') }}" class="mt-3 inline-block px-4 py-2 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                        Mulai Chat
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
