<?php
/**
 * Story Index View
 * List semua story (24 jam terakhir)
 */
?>

@extends('layouts.user')

@section('title', 'Stories - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Add Story Button -->
    <div class="mb-4">
        <a href="{{ route('stories.create') }}" 
           class="flex items-center justify-center gap-2 p-4 bg-gradient-to-r from-primary-500 to-accent-500 text-white rounded-2xl hover:opacity-90 transition-opacity">
            <i class="fas fa-plus-circle"></i>
            <span>Add Story</span>
        </a>
    </div>
    
    <!-- My Story -->
    @php
        $myStories = auth()->user()->stories()
            ->where('is_deleted', false)
            ->where('expires_at', '>', now())
            ->get();
    @endphp
    @if($myStories->count() > 0)
        <div class="bg-white dark:bg-dark-100 rounded-2xl p-4 mb-4">
            <h3 class="font-semibold mb-3">My Story</h3>
            <div class="flex gap-2 overflow-x-auto">
                @foreach($myStories as $story)
                    <div class="flex-shrink-0 w-20 h-32 rounded-xl overflow-hidden bg-gray-100 relative">
                        @if($story->type === 'image')
                            <img src="{{ asset('storage/stories/' . $story->media_path) }}" class="w-full h-full object-cover">
                        @else
                            <video src="{{ asset('storage/stories/' . $story->media_path) }}" class="w-full h-full object-cover"></video>
                        @endif
                        <form action="{{ url('/api/stories/' . $story->id) }}" method="POST" class="absolute top-1 right-1">
                            @csrf @method('DELETE')
                            <button class="p-1 bg-black/50 rounded-full text-white text-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- All Stories -->
    <div class="bg-white dark:bg-dark-100 rounded-2xl p-4">
        <h3 class="font-semibold mb-3">All Stories</h3>
        
        <div class="space-y-3">
            @php
                $stories = \App\Models\Story::where('user_id', '!=', auth()->id())
                    ->where('expires_at', '>', now())
                    ->where('is_deleted', false)
                    ->with('user')
                    ->latest()
                    ->get()
                    ->groupBy('user_id');
            @endphp
            
            @forelse($stories as $userId => $userStories)
                @php $user = $userStories->first()->user; @endphp
                <a href="{{ url('/stories/user/' . $userId) }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800">
                    <div class="relative">
                        <img src="{{ $user->avatar_url }}" class="w-14 h-14 rounded-full object-cover ring-2 ring-primary-500">
                        @if($user->is_online)
                            <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $userStories->count() }} story • {{ $userStories->first()->created_at->shortRelativeDiffForHumans() }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-circle-notch text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">No stories yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
