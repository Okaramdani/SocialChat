<?php
/**
 * Create Group View
 * Form untuk membuat grup chat
 */
?>

@extends('layouts.user')

@section('title', 'Create Group - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-bold">Create Group</h2>
            <a href="{{ route('dashboard') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                <i class="fas fa-times"></i>
            </a>
        </div>
        
        <form action="{{ url('/api/chats/group') }}" method="POST" class="p-6 space-y-4" id="createGroupForm">
            @csrf
            
            <!-- Group Name -->
            <div>
                <label class="block text-sm font-medium mb-2">Group Name</label>
                <input type="text" name="name" required
                       class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500"
                       placeholder="Enter group name">
            </div>
            
            <!-- Participants -->
            <div>
                <label class="block text-sm font-medium mb-2">Participants</label>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @php
                        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
                    @endphp
                    @foreach($users as $user)
                        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                            <input type="checkbox" name="participants[]" value="{{ $user->id }}" class="rounded text-primary-500">
                            <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->is_online ? 'Online' : 'Offline' }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <!-- Submit -->
            <button type="submit" class="w-full py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                Create Group
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('createGroupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/api/chats/group', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.chat) {
            window.location.href = '/chat/' + data.chat.id;
        }
    });
});
</script>
@endsection
