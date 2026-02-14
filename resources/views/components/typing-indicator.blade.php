<?php
/**
 * Typing Indicator Component
 * Animasi typing indicator untuk chat
 */
?>

<div class="typing-indicator flex gap-2 mb-3" style="display: none;" id="typingIndicator">
    <img src="{{ asset('images/default-avatar.png') }}" class="w-8 h-8 rounded-full">
    <div class="bg-white dark:bg-gray-700 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm">
        <div class="flex gap-1">
            <span class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></span>
            <span class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></span>
            <span class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></span>
        </div>
    </div>
</div>

<style>
    .typing-dot {
        animation: bounce 1.4s infinite ease-in-out both;
    }
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-dot:nth-child(3) { animation-delay: 0s; }
    
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0.6); }
        40% { transform: scale(1); }
    }
</style>
