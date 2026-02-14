<?php
/**
 * Loader Component
 * Loading spinner component
 */
?>

@props(['size' => 'md', 'color' => 'primary'])

@php
    $sizes = [
        'sm' => 'w-4 h-4 border-2',
        'md' => 'w-8 h-8 border-2',
        'lg' => 'w-12 h-12 border-3',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="loader-container flex items-center justify-center">
    <div class="{{ $sizeClass }} border-{{ $color }}-200 border-t-{{ $color }}-500 rounded-full animate-spin"></div>
</div>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
