<?php
/**
 * Reaction Popup Component
 * Popup untuk memilih emoji reaction
 */
?>

<div x-show="showReactionPopup" x-cloak
     @click.away="showReactionPopup = false"
     class="absolute bg-white dark:bg-gray-800 rounded-full shadow-lg p-2 flex gap-1 z-50"
     :style="reactionPosition">
    @foreach(['👍', '❤️', '😂', '😮', '😢', '😡', '🔥', '👏'] as $emoji)
        <button @click="addReaction('{{ $emoji }}')"
                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full text-lg">
            {{ $emoji }}
        </button>
    @endforeach
</div>
