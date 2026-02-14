<?php
/**
 * Dark Mode Toggle Component
 * Tombol toggle untuk dark mode
 */
?>

<button x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
        @click="darkMode = !darkMode; 
                localStorage.setItem('darkMode', darkMode);
                if (darkMode) { document.documentElement.classList.add('dark'); } 
                else { document.documentElement.classList.remove('dark'); }"
        x-init="if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) { 
                    darkMode = true; 
                    document.documentElement.classList.add('dark'); 
                }"
        class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        :class="{ 'bg-primary-100 dark:bg-primary-900/30': darkMode }">
    <i class="fas fa-moon text-gray-600 dark:text-gray-300" x-show="!darkMode"></i>
    <i class="fas fa-sun text-yellow-400" x-show="darkMode" x-cloak></i>
</button>
