<?php
/**
 * Profile Index View
 * Halaman profil user
 */
?>

@extends('layouts.user')

@section('title', 'Profile - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <!-- Cover -->
        <div class="h-32 bg-gradient-to-r from-primary-500 to-accent-500"></div>
        
        <!-- Profile Info -->
        <div class="px-6 pb-6">
            <div class="flex items-end gap-4 -mt-12 mb-4">
                <img src="{{ auth()->user()->avatar_url }}" 
                     class="w-24 h-24 rounded-2xl object-cover border-4 border-white dark:border-dark-100">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-500">{{ auth()->user()->is_online ? 'Online' : 'Last seen ' . auth()->user()->last_seen?->shortRelativeDiffForHumans() }}</p>
                </div>
                <a href="{{ url('/profile/edit') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
            
            <!-- Bio -->
            <div class="mb-4">
                <p class="text-gray-600 dark:text-gray-300">{{ auth()->user()->bio ?? 'No bio yet' }}</p>
            </div>
            
            <!-- Mood -->
            @if(auth()->user()->mood)
            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-full text-sm">
                    {{ auth()->user()->mood }}
                </span>
            </div>
            @endif
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <p class="text-2xl font-bold">{{ auth()->user()->chats()->count() }}</p>
                    <p class="text-xs text-gray-500">Chats</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold">{{ auth()->user()->sentMessages()->count() }}</p>
                    <p class="text-xs text-gray-500">Messages</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold">{{ auth()->user()->stories()->count() }}</p>
                    <p class="text-xs text-gray-500">Stories</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Media -->
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden mt-4">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold">Recent Media</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-3 gap-2">
                @php
                    $media = auth()->user()->mediaTimelines()->latest()->limit(9)->get();
                @endphp
                @forelse($media as $m)
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                        @if($m->type === 'image')
                            <img src="{{ asset('storage/media/' . $m->file_path) }}" class="w-full h-full object-cover">
                        @else
                            <video src="{{ asset('storage/media/' . $m->file_path) }}" class="w-full h-full object-cover"></video>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-images text-3xl text-gray-300"></i>
                        <p class="text-sm text-gray-500 mt-2">No media yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
