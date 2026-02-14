<?php
/**
 * Story View
 * View single user's stories
 */
?>

@extends('layouts.user')

@section('title', $user->name . "'s Stories")

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        @if($stories->count() > 0)
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h2 class="font-semibold">{{ $user->name }}</h2>
                        <p class="text-xs text-gray-500">{{ $stories->count() }} stories</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-2 p-4">
                @foreach($stories as $story)
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100">
                        @if($story->type === 'image')
                            <img src="{{ asset('storage/stories/' . $story->media_path) }}" class="w-full h-full object-cover">
                        @else
                            <video src="{{ asset('storage/stories/' . $story->media_path) }}" class="w-full h-full object-cover" controls></video>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <i class="fas fa-circle-notch text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No stories available</p>
                <a href="{{ route('stories.index') }}" class="mt-4 inline-block text-primary-600">Back to Stories</a>
            </div>
        @endif
    </div>
</div>
@endsection
