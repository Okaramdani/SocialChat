<?php
/**
 * Message Item Component
 * Tampilan pesan dalam chat
 * Props: message (Message model)
 */
?>

@props(['message'])

@php
    $isOwn = auth()->id() === $message->sender_id;
@endphp

<div class="message-wrapper flex gap-2 mb-3 {{ $isOwn ? 'flex-row-reverse' : '' }}" 
     data-message-id="{{ $message->id }}">
    
    <!-- Avatar (kalo bukan pesan sendiri) -->
    @if(!$isOwn)
        <img src="{{ $message->sender->avatar_url ?? asset('images/default-avatar.png') }}" 
             class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-auto">
    @endif
    
    <!-- Message Bubble -->
    <div class="message-bubble max-w-[75%] {{ $isOwn ? 'bg-primary-500 text-white' : 'bg-white dark:bg-gray-700' }} 
                rounded-2xl px-4 py-2.5 shadow-sm relative group
                {{ $isOwn ? 'rounded-br-md' : 'rounded-bl-md' }}">
        
        <!-- Reply Context -->
        @if($message->replyTo)
            <div class="text-xs opacity-70 mb-1 pb-1 border-b border-current/10">
                <i class="fas fa-reply mr-1"></i>
                Replying to {{ $message->replyTo->sender->name ?? 'message' }}
            </div>
        @endif
        
        <!-- Content -->
        @if($message->type === 'text')
            <p class="text-sm whitespace-pre-wrap break-words">{{ $message->content }}</p>
            
        @elseif($message->type === 'image')
            <div class="space-y-2">
                @if($message->content)
                    <p class="text-sm">{{ $message->content }}</p>
                @endif
                <img src="{{ $message->file_url }}" 
                     class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                     onclick="window.open(this.src)">
            </div>
            
        @elseif($message->type === 'video')
            <div class="space-y-2">
                @if($message->content)
                    <p class="text-sm">{{ $message->content }}</p>
                @endif
                <video controls class="max-w-full rounded-lg">
                    <source src="{{ $message->file_url }}">
                </video>
            </div>
            
        @elseif($message->type === 'file')
            @php
                $ext = strtolower(pathinfo($message->file_name, PATHINFO_EXTENSION));
                $icon = 'fa-file';
                $color = 'text-gray-500';
                
                if (in_array($ext, ['pdf'])) { $icon = 'fa-file-pdf'; $color = 'text-red-500'; }
                elseif (in_array($ext, ['doc','docx'])) { $icon = 'fa-file-word'; $color = 'text-blue-500'; }
                elseif (in_array($ext, ['xls','xlsx'])) { $icon = 'fa-file-excel'; $color = 'text-green-500'; }
                elseif (in_array($ext, ['ppt','pptx'])) { $icon = 'fa-file-powerpoint'; $color = 'text-orange-500'; }
                elseif (in_array($ext, ['zip','rar','7z'])) { $icon = 'fa-file-archive'; $color = 'text-yellow-500'; }
                elseif (in_array($ext, ['txt'])) { $icon = 'fa-file-alt'; $color = 'text-gray-500'; }
                elseif (in_array($ext, ['gif','svg','webp'])) { $icon = 'fa-image'; $color = 'text-purple-500'; }
            @endphp
            <a href="{{ $message->file_url }}" download 
               class="flex items-center gap-3 p-2 rounded-lg bg-black/10 hover:bg-black/20 transition-colors">
                <i class="fas {{ $icon }} {{ $color }} text-xl"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $message->file_name }}</p>
                    <p class="text-xs opacity-70">{{ $message->formatted_file_size }}</p>
                </div>
                <i class="fas fa-download"></i>
            </a>
            
        @elseif($message->type === 'voice')
            <div class="flex items-center gap-3 min-w-[200px]" x-data="{ playing: false, duration: 0, currentTime: 0 }">
                <button @click="playing = !playing; if(playing) { $refs.audio.play(); } else { $refs.audio.pause(); }" 
                        class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center hover:bg-green-600 transition-colors shadow-lg">
                    <i class="fas text-white text-lg" :class="playing ? 'fa-pause' : 'fa-play'"></i>
                </button>
                <audio x-ref="audio" @loadedmetadata="duration = $refs.audio.duration" @timeupdate="currentTime = $refs.audio.currentTime" @ended="playing = false" class="hidden">
                    <source src="{{ $message->file_url }}">
                </audio>
                <div class="flex-1">
                    <div class="h-2 bg-white/30 rounded-full overflow-hidden">
                        <div class="h-full bg-green-400 rounded-full transition-all duration-300" 
                             :style="'width: ' + (duration ? (currentTime / duration * 100) : 0) + '%'"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <p class="text-xs opacity-70">{{ $message->formatted_file_size }}</p>
                        <p class="text-xs opacity-70" x-text="duration ? Math.floor(duration / 60) + ':' + String(Math.floor(duration % 60)).padStart(2, '0') : '0:00'"></p>
                    </div>
                </div>
            </div>
        @elseif($message->type === 'location')
            @php
                $coords = explode(',', $message->content);
                $lat = trim($coords[0] ?? '0');
                $lng = trim($coords[1] ?? '0');
            @endphp
            <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank"
               class="flex items-center gap-3 p-3 rounded-lg bg-blue-500 hover:bg-blue-600 transition-colors shadow-md">
                <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-white">Lokasi</p>
                    <p class="text-xs text-white/70">Buka di Google Maps</p>
                </div>
                <i class="fas fa-external-link-alt text-white"></i>
            </a>
        @endif
        
        <!-- Timestamp & Status -->
        <div class="flex items-center justify-end gap-1 mt-1 text-xs opacity-60">
            @if($message->self_destruct_at)
                <i class="fas fa-clock" title="Self-destruct"></i>
            @endif
            <span>{{ $message->created_at->format('H:i') }}</span>
            @if($isOwn)
                <i class="fas fa-check-double ml-1"></i>
            @endif
        </div>
        
        <!-- Reactions -->
        @if($message->reactions->count() > 0)
            <div class="absolute -bottom-2 {{ $isOwn ? 'left-0' : 'right-0' }}">
                <div class="flex gap-0.5">
                    @foreach($message->reactions->groupBy('emoji') as $emoji => $reactions)
                        <span class="px-1.5 py-0.5 bg-white dark:bg-gray-600 rounded-full text-xs shadow-sm">
                            {{ $emoji }} {{ $reactions->count() }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Action Menu (hanya untuk pengirim) -->
        @if($isOwn)
        <div class="absolute top-1/2 -translate-y-1/2 left-full ml-1 flex gap-0.5 z-10">
            <button @click="$dispatch('reply-message', { id: {{ $message->id }} })" 
                    class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 shadow flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-500">
                <i class="fas fa-reply text-xs"></i>
            </button>
            <button @click="$dispatch('edit-message', { id: {{ $message->id }} })"
                    class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 shadow flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-500 text-blue-500">
                <i class="fas fa-edit text-xs"></i>
            </button>
            <button @click="$dispatch('delete-message', { id: {{ $message->id }} })"
                    class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 shadow flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-500 text-red-500">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>
        @else
        <div class="absolute top-1/2 -translate-y-1/2 left-full ml-1 flex gap-0.5 z-10 opacity-50">
            <button class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 shadow flex items-center justify-center cursor-not-allowed">
                <i class="fas fa-reply text-xs"></i>
            </button>
            <button class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 shadow flex items-center justify-center cursor-not-allowed">
                <i class="fas fa-edit text-xs text-gray-300"></i>
            </button>
            <button class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 shadow flex items-center justify-center cursor-not-allowed">
                <i class="fas fa-trash text-xs text-gray-300"></i>
            </button>
        </div>
        @endif
    </div>
</div>
