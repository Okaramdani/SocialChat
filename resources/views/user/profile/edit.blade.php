<?php
/**
 * Profile Edit View
 * Form edit profil user
 */
?>

@extends('layouts.user')

@section('title', 'Edit Profile - Social Chat')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-bold">Edit Profile</h2>
            <a href="{{ url('/profile') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                <i class="fas fa-times"></i>
            </a>
        </div>
        
        <form action="{{ url('/profile') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <!-- Avatar -->
            <div class="flex justify-center">
                <div class="relative">
                    <img src="{{ auth()->user()->avatar_url }}" id="avatarPreview"
                         class="w-24 h-24 rounded-2xl object-cover">
                    <label class="absolute bottom-0 right-0 p-2 bg-primary-500 text-white rounded-xl cursor-pointer">
                        <i class="fas fa-camera"></i>
                        <input type="file" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                    </label>
                    @if(auth()->user()->avatar)
                    <button type="button" onclick="deleteAvatar()" 
                            class="absolute top-0 right-0 p-1.5 bg-red-500 text-white rounded-full text-xs hover:bg-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </div>
            </div>
            
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium mb-2">Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required
                       class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500">
            </div>
            
            <!-- Bio -->
            <div>
                <label class="block text-sm font-medium mb-2">Bio</label>
                <textarea name="bio" rows="3" maxlength="150"
                          class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500"
                          placeholder="Tell about yourself...">{{ auth()->user()->bio }}</textarea>
            </div>
            
            <!-- Mood -->
            <div>
                <label class="block text-sm font-medium mb-2">Mood</label>
                <select name="mood" class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500">
                    <option value="">Select mood</option>
                    @foreach(['Happy', 'Excited', 'Calm', 'Focused', 'Creative', 'Tired', 'Sad', 'Angry'] as $mood)
                        <option value="{{ $mood }}" {{ auth()->user()->mood === $mood ? 'selected' : '' }}>{{ $mood }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Submit -->
            <button type="submit" class="w-full py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                Save Changes
            </button>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function deleteAvatar() {
    if (confirm('Hapus foto profil?')) {
        fetch('/profile/avatar', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
