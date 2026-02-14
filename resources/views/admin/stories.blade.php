{{-- Admin Stories View - Daftar semua story untuk moderasi --}}

@extends('layouts.admin')

@section('title', 'Stories - Admin')
@section('header', 'Story Management')

@section('content')
<div class="space-y-4">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500">Total Stories</p>
            <p class="text-2xl font-bold">{{\App\Models\Story::count()}}</p>
        </div>
        <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500">Active Stories</p>
            <p class="text-2xl font-bold">{{\App\Models\Story::where('created_at', '>', now()->subHours(24))->where('is_deleted', false)->count()}}</p>
        </div>
        <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500">Deleted</p>
            <p class="text-2xl font-bold">{{\App\Models\Story::where('is_deleted', true)->count()}}</p>
        </div>
    </div>
    
    <!-- Stories Grid -->
    <div class="bg-white dark:bg-dark-100 rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold">All Stories</h3>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 p-4">
            @php
                $stories = \App\Models\Story::with('user')
                    ->where('is_deleted', false)
                    ->latest()
                    ->limit(30)
                    ->get();
            @endphp
            @forelse($stories as $story)
            <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100">
                @if($story->type === 'image')
                    <img src="{{ asset('storage/stories/' . $story->file_path) }}" class="w-full h-full object-cover">
                @else
                    <video src="{{ asset('storage/stories/' . $story->file_path) }}" class="w-full h-full object-cover"></video>
                @endif
                
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <div class="text-white text-center">
                        <p class="text-xs font-medium">{{ $story->user->name }}</p>
                        <p class="text-xs opacity-70">{{ $story->created_at->diffForHumans() }}</p>
                        <form action="{{ route('admin.story.delete', $story) }}" method="POST" class="mt-2">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 bg-red-500 rounded-full text-xs hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8">
                <i class="fas fa-circle-notch text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500">No stories</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
