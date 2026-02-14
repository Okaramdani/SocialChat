<?php
/**
 * Story Create View
 * Form upload story baru
 */
?>

@extends('layouts.user')

@section('title', 'Create Story - Social Chat')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-bold">New Story</h2>
            <a href="{{ route('stories.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                <i class="fas fa-times"></i>
            </a>
        </div>
        
        <form action="{{ url('/api/stories') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="storyForm">
            @csrf
            
            <!-- Hidden type field -->
            <input type="hidden" name="type" id="storyType" value="image">
            
            <!-- Preview -->
            <div class="aspect-[9/16] max-h-96 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center overflow-hidden">
                <video id="previewVideo" class="hidden w-full h-full object-contain"></video>
                <img id="preview" class="hidden w-full h-full object-contain">
                <div id="placeholder" class="text-center">
                    <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Click to upload</p>
                </div>
            </div>
            
            <input type="file" name="file" id="fileInput" accept="image/*,video/*" class="hidden" required
                   onchange="previewFile(this)">
            
            <button type="button" onclick="document.getElementById('fileInput').click()" 
                    class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-gray-500 hover:text-primary-500">
                <i class="fas fa-upload mr-2"></i> Select Media
            </button>
            
            <!-- Caption -->
            <div>
                <label class="block text-sm font-medium mb-2">Caption (optional)</label>
                <textarea name="caption" rows="2" class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500" placeholder="Write a caption..."></textarea>
            </div>
            
            <!-- Self Destruct Timer -->
            <div>
                <label class="block text-sm font-medium mb-2">Auto-delete after</label>
                <select name="expires_in" class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500">
                    <option value="24">24 hours</option>
                    <option value="12">12 hours</option>
                    <option value="6">6 hours</option>
                    <option value="1">1 hour</option>
                </select>
            </div>
            
            <button type="submit" class="w-full py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                Share Story
            </button>
        </form>
    </div>
</div>

<script>
function previewFile(input) {
    const preview = document.getElementById('preview');
    const previewVideo = document.getElementById('previewVideo');
    const placeholder = document.getElementById('placeholder');
    const storyType = document.getElementById('storyType');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileType = file.type;
        
        // Set the type based on file
        if (fileType.startsWith('video/')) {
            storyType.value = 'video';
            previewVideo.classList.remove('hidden');
            preview.classList.add('hidden');
            placeholder.classList.add('hidden');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewVideo.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            storyType.value = 'image';
            preview.classList.remove('hidden');
            previewVideo.classList.add('hidden');
            placeholder.classList.add('hidden');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}

document.getElementById('storyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sharing...';
    
    fetch('/api/stories', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    }).then(res => {
        if (!res.ok) throw new Error('Failed to share story');
        return res.json();
    }).then(() => {
        window.location.href = '{{ route('stories.index') }}';
    }).catch(err => {
        alert('Gagal share story: ' + err.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Share Story';
    });
});
</script>
@endsection
