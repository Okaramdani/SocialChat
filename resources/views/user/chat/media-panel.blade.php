<?php
/**
 * Media Panel View
 * Panel untuk melihat media dalam chat
 */
?>

@extends('layouts.user')

@section('title', 'Media - Social Chat')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold">Media & Files</h2>
        </div>
        
        <div class="p-4">
            <!-- Tabs -->
            <div class="flex gap-2 mb-4">
                <button class="px-4 py-2 rounded-xl bg-primary-500 text-white">Photos</button>
                <button class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800">Videos</button>
                <button class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800">Files</button>
            </div>
            
            <!-- Grid -->
            <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                @php
                    $media = \App\Models\Message::whereHas('chat', function($q) {
                        $q->whereHas('participants', function($q2) {
                            $q2->where('user_id', auth()->id());
                        });
                    })->whereIn('type', ['image', 'video', 'file'])->where('is_deleted', false)->latest()->limit(20)->get();
                @endphp
                
                @forelse($media as $msg)
                    @if($msg->type === 'image')
                        <a href="{{ $msg->file_url }}" target="_blank" class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $msg->file_url }}" class="w-full h-full object-cover hover:scale-110 transition-transform">
                        </a>
                    @elseif($msg->type === 'video')
                        <a href="{{ $msg->file_url }}" target="_blank" class="aspect-square rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-play-circle text-3xl text-gray-400"></i>
                        </a>
                    @else
                        <a href="{{ $msg->file_url }}" download class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800">
                            <i class="fas fa-file text-2xl"></i>
                            <p class="text-xs mt-1 truncate">{{ $msg->file_name }}</p>
                        </a>
                    @endif
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-images text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">No media yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
