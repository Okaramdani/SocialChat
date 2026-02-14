<?php
/**
 * Modal Component
 * Reusable modal dialog
 */
?>

@props(['id' => 'modal', 'title' => ''])

<div x-data="{ open: false }" 
     @open-modal{{ $id }}.window="open = true"
     @close-modal{{ $id }}.window="open = false">
    
    <!-- Backdrop -->
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click="open = false">
        
        <!-- Modal Content -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop
             class="bg-white dark:bg-dark-100 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden">
            
            <!-- Header -->
            @if($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
                <button @click="open = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            @if(isset($footer))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
