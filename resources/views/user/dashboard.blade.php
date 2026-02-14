<?php
/**
 * User Dashboard View
 * Halaman utama setelah login - list semua chat
 */
?>

@extends('layouts.user')

@section('title', 'Dashboard - Social Chat')

@section('content')
{{-- Stats Cards --}}
<div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                <i class="fas fa-comments text-primary-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $totalChats ?? 0 }}</p>
                <p class="text-xs text-gray-500">Chats</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                <i class="fas fa-circle-notch text-accent-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $totalStories ?? 0 }}</p>
                <p class="text-xs text-gray-500">Stories</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <i class="fas fa-phone text-green-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $totalCalls ?? 0 }}</p>
                <p class="text-xs text-gray-500">Calls</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <i class="fas fa-trophy text-yellow-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold">#{{ $leaderboardPosition ?? '-' }}</p>
                <p class="text-xs text-gray-500">Rank</p>
            </div>
        </div>
    </div>
</div>

<div x-data="{ 
    showNewChat: false, 
    showCreateGroup: false, 
    searchUser: '',
    users: {{ \App\Models\User::where('id', '!=', auth()->id())->get(['id', 'name', 'avatar', 'is_online'])->toJson() }}
}" 
     x-init="
        window.showCreateGroupModal = () => { showCreateGroup = true; };
     ">
    <div class="flex h-[calc(100vh-4rem)]">
    <!-- Chat List -->
    <div class="w-full md:w-80 lg:w-96 bg-white dark:bg-dark-100 border-r border-gray-200 dark:border-gray-700 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xl font-bold">Chats</h2>
                <button onclick="openCreateGroupModal()" 
                        class="p-2 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-colors">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            
            <!-- New Chat Button -->
            <button @click="showNewChat = true" 
                    class="w-full flex items-center gap-3 p-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-comment-dots text-gray-400"></i>
                <span class="text-gray-500 dark:text-gray-400">New conversation...</span>
            </button>
        </div>
        
        <!-- Chat List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($chats as $chat)
                @include('components.chat-item', ['chat' => $chat, 'user' => auth()->user()])
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500">Belum ada chat</p>
                    <button @click="showNewChat = true" class="mt-3 text-primary-600 hover:underline">
                        Mulai percakapan
                    </button>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Welcome Panel (jika belum pilih chat) -->
    <div class="hidden md:flex flex-1 items-center justify-center bg-gray-50 dark:bg-dark-200">
        <div class="text-center">
            <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-primary-500 to-accent-500 rounded-3xl flex items-center justify-center shadow-xl">
                <i class="fas fa-comments text-4xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Social Chat</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Connect Smarter</p>
            <p class="text-sm text-gray-400">Pilih chat untuk mulai percakapan</p>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
    <div x-show="showNewChat" x-cloak
         @click="showNewChat = false"
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        
        <div x-show="showNewChat"
             @click.stop
             class="bg-white dark:bg-dark-100 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">New Conversation</h3>
                    <button @click="showNewChat = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <input type="text" x-model="searchUser" placeholder="Search users..." 
                   class="w-full mt-3 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div class="max-h-80 overflow-y-auto p-2">
                <template x-for="user in users.filter(u => searchUser === '' || u.name.toLowerCase().includes(searchUser.toLowerCase()))" :key="user.id">
                    <button @click="startChat(user.id)" 
                            class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <img :src="user.avatar_url" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1 text-left">
                            <p class="font-medium" x-text="user.name"></p>
                            <p class="text-xs text-gray-500" x-text="user.is_online ? 'Online' : 'Offline'"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div x-show="showCreateGroup" x-cloak
     @click="showCreateGroup = false"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    
    <div x-show="showCreateGroup"
         @click.stop
         class="bg-white dark:bg-dark-100 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">Create Group</h3>
                <button @click="showCreateGroup = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ url('/api/chats/group') }}" method="POST" @submit.prevent="submitGroup($event)">
            @csrf
            <div class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Group Name</label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500"
                           placeholder="Enter group name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Participants</label>
                    @php
                        $allUsers = \App\Models\User::where('id', '!=', auth()->id())->get();
                    @endphp
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        @foreach($allUsers as $user)
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="participants[]" value="{{ $user->id }}" class="rounded text-primary-500">
                                <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                                <span>{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <button type="submit" class="w-full py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                    Create Group
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
function startChat(userId) {
    fetch('/api/chats', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to start chat');
        return res.json();
    })
    .then(data => {
        if (data.chat) {
            window.location.href = '/chat/' + data.chat.id;
        }
    })
    .catch(err => {
        alert('Gagal mulai chat: ' + err.message);
    });
}

function submitGroup(e) {
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    
    fetch('/api/chats/group', {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => { throw new Error(err.message || 'Failed to create group'); });
        }
        return res.json();
    })
    .then(data => {
        if (data.chat) {
            window.location.href = '/chat/' + data.chat.id;
        }
    })
    .catch(err => {
        alert('Gagal buat grup: ' + err.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Create Group';
    });
}
</script>
@endsection
