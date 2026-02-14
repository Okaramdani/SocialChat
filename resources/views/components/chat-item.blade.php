<?php
/**
 * Chat Item Component
 * Tampilan item chat dalam list
 * Props: chat (Chat model), user (current user)
 */
?>

@props(['chat', 'user'])

<a href="{{ route('chat.show', $chat) }}" 
   class="chat-item flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 group"
   :class="{ 'bg-primary-50 dark:bg-primary-900/20': {{ request()->route('chat')?->id === $chat->id ? 'true' : 'false' }} }">
    
    <!-- Avatar -->
    <div class="relative flex-shrink-0">
        @if($chat->type === 'group')
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-semibold">
                <i class="fas fa-users"></i>
            </div>
        @else
            @php
                $otherUser = $chat->participants()->where('user_id', '!=', $user->id)->first();
            @endphp
            <img src="{{ $otherUser?->avatar_url ?? asset('images/default-avatar.png') }}" 
                 class="w-12 h-12 rounded-xl object-cover">
            @if($otherUser?->is_online)
                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
            @endif
        @endif
    </div>
    
    <!-- Content -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
            <h3 class="font-medium truncate {{ $chat->is_pinned ? 'text-primary-600' : '' }}">
                @if($chat->is_pinned)
                    <i class="fas fa-thumbtack text-xs mr-1"></i>
                @endif
                {{ $chat->getDisplayName($user) }}
            </h3>
            <span class="text-xs text-gray-400">
                {{ $chat->latestMessage->first()?->created_at->shortRelativeDiffForHumans() }}
            </span>
        </div>
        
        <div class="flex items-center justify-between mt-0.5">
            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                @if($chat->latestMessage->first())
                    @if($chat->latestMessage->first()->type === 'image')
                        <i class="fas fa-image mr-1"></i> Photo
                    @elseif($chat->latestMessage->first()->type === 'video')
                        <i class="fas fa-video mr-1"></i> Video
                    @elseif($chat->latestMessage->first()->type === 'file')
                        <i class="fas fa-file mr-1"></i> File
                    @elseif($chat->latestMessage->first()->type === 'voice')
                        <i class="fas fa-microphone mr-1"></i> Voice
                    @else
                        {{ $chat->latestMessage->first()->content }}
                    @endif
                @else
                    <span class="italic">Belum ada pesan</span>
                @endif
            </span>
            
            @if($chat->label)
                <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded-full">{{ $chat->label }}</span>
            @endif
        </div>
    </div>
</a>

<style>
    .chat-item:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
</style>
