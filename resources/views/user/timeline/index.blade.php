<?php
/**
 * Timeline View
 * Media timeline dari semua user
 */
?>

@extends('layouts.user')

@section('title', 'Timeline - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold">Timeline</h2>
        </div>
        
        <div class="grid grid-cols-3 gap-1 p-1">
            @forelse($media as $item)
                <a href="{{ asset('storage/messages/' . $item->file_path) }}" target="_blank" 
                   class="aspect-square bg-gray-100 dark:bg-gray-800 overflow-hidden relative">
                    @if($item->type === 'image')
                        <img src="{{ asset('storage/messages/' . $item->file_path) }}" class="w-full h-full object-cover hover:scale-110 transition-transform">
                    @elseif($item->type === 'video')
                        <video src="{{ asset('storage/messages/' . $item->file_path) }}" class="w-full h-full object-cover"></video>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-play text-white text-2xl drop-shadow-lg"></i>
                        </div>
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-file text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-clock text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Belum ada media di timeline</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
