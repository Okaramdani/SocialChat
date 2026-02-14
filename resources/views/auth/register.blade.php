<?php
/**
 * Register View
 * Form registrasi user baru
 */
?>

@extends('layouts.app')

@section('title', 'Register - Social Chat')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
     x-data="{ 
         darkMode: localStorage.getItem('darkMode') === 'true',
         toggleDarkMode() {
             this.darkMode = !this.darkMode;
             localStorage.setItem('darkMode', this.darkMode);
             if (this.darkMode) document.documentElement.classList.add('dark');
             else document.documentElement.classList.remove('dark');
         }
     }"
     :class="{ 'dark': darkMode }">
    
    <!-- Dark Mode Toggle -->
    <button @click="toggleDarkMode()" 
            class="fixed top-4 right-4 p-2 rounded-full bg-gray-200 dark:bg-dark-100 hover:bg-gray-300 dark:hover:bg-dark-300 transition-all z-50">
        <i class="fas" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon text-gray-600'"></i>
    </button>

    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-primary-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-comments text-2xl text-white"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Buat Akun
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Bergabung dengan Social Chat
            </p>
        </div>

        <!-- Form -->
        <form class="mt-8 space-y-4" action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" id="name" required
                               class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-dark-100 rounded-xl bg-white dark:bg-dark-100 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="John Doe">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" required
                               class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-dark-100 rounded-xl bg-white dark:bg-dark-100 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="email@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-dark-100 rounded-xl bg-white dark:bg-dark-100 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-dark-100 rounded-xl bg-white dark:bg-dark-100 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-primary-500 to-purple-600 hover:from-primary-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all shadow-lg hover:shadow-xl">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-user-plus"></i>
                </span>
                Daftar
            </button>
        </form>

        <!-- Login Link -->
        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                Login
            </a>
        </p>
    </div>
</div>
@endsection
