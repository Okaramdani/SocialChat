<?php
/**
 * Dashboard View - Main Chat Interface
 * 3-column layout: Sidebar (chats), Conversation, Profile/Media
 * Responsive: Bottom navigation on mobile
 */
?>

@extends('layouts.app')

@section('title', 'Dashboard - Social Chat')

@section('content')
<div class="h-screen flex flex-col md:flex-row overflow-hidden"
     x-data="chatApp()"
     x-init="init()">
    
    <!-- Dark Mode Toggle (Fixed) -->
    <button @click="toggleDarkMode()" 
            class="fixed top-3 right-3 p-2 rounded-full bg-gray-200 dark:bg-dark-100 hover:bg-gray-300 dark:hover:bg-dark-300 transition-all z-50 shadow-md">
        <i class="fas" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon text-gray-600'"></i>
    </button>

    <!-- ==================== LEFT SIDEBAR ==================== -->
    <!-- Chat List, Search, Labels -->
    <aside class="w-full md:w-80 lg:w-96 bg-white dark:bg-dark-100 border-r border-gray-200 dark:border-dark-300 flex flex-col"
           :class="{'hidden md:flex': activeView !== 'chat'}">
        
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-dark-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <!-- Avatar -->
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.auth()->user()->name.'&background=0ea5e9&color=fff' }}"
                             class="w-10 h-10 rounded-full object-cover ring-2 ring-primary-500">
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-dark-100 rounded-full"
                              :class="{'hidden': !isOnline}"></span>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="isOnline ? 'Online' : 'Offline'"></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- New Chat -->
                    <button @click="showNewChatModal = true"
                            class="p-2 rounded-full bg-gray-100 dark:bg-dark-300 hover:bg-gray-200 dark:hover:bg-dark-300 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-plus"></i>
                    </button>
                    <!-- Menu -->
                    <div class="relative" x-data="{open: false}">
                        <button @click="open = !open" class="p-2 rounded-full bg-gray-100 dark:bg-dark-300 hover:bg-gray-200 dark:hover:bg-dark-300 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-100 rounded-xl shadow-lg border border-gray-200 dark:border-dark-300 py-1 z-50">
                            <a href="#" @click="activeView = 'profile'" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-user mr-2"></i>Profil
                            </a>
                            <a href="#" @click="activeView = 'story'" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-history mr-2"></i>Story
                            </a>
                            <a href="#" @click="activeView = 'timeline'" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-images mr-2"></i>Timeline
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-cog mr-2"></i>Admin
                            </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-dark-300">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari chat..." 
                       x-model="searchQuery"
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-dark-300 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
            </div>

            <!-- Labels Filter -->
            <div class="flex space-x-2 mt-3 overflow-x-auto pb-1">
                <button @click="selectedLabel = null" 
                        class="px-3 py-1 text-xs rounded-full whitespace-nowrap transition-all"
                        :class="selectedLabel === null ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-dark-300 text-gray-600 dark:text-gray-300'">
                    Semua
                </button>
                <button @click="selectedLabel = 'pinned'" 
                        class="px-3 py-1 text-xs rounded-full whitespace-nowrap transition-all"
                        :class="selectedLabel === 'pinned' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-dark-300 text-gray-600 dark:text-gray-300'">
                    <i class="fas fa-thumbtack mr-1"></i>Tersemat
                </button>
                <button @click="selectedLabel = 'group'" 
                        class="px-3 py-1 text-xs rounded-full whitespace-nowrap transition-all"
                        :class="selectedLabel === 'group' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-dark-300 text-gray-600 dark:text-gray-300'">
                    <i class="fas fa-users mr-1"></i>Grup
                </button>
            </div>
        </div>

        <!-- Chat List -->
        <div class="flex-1 overflow-y-auto">
            <!-- Loading Skeleton -->
            <template x-if="loading">
                <div class="p-4 space-y-3">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="flex items-center space-x-3 animate-pulse">
                        <div class="w-12 h-12 bg-gray-200 dark:bg-dark-300 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-200 dark:bg-dark-300 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-200 dark:bg-dark-300 rounded w-1/2"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </template>

            <!-- Chat Items -->
            <template x-for="chat in filteredChats" :key="chat.id">
                <div @click="selectChat(chat)"
                     class="flex items-center p-4 hover:bg-gray-100 dark:hover:bg-dark-300 cursor-pointer transition-all border-l-4"
                     :class="selectedChatId === chat.id ? 'bg-gray-100 dark:bg-dark-300 border-primary-500' : 'border-transparent'">
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <img :src="chat.avatar || `https://ui-avatars.com/api/?name=${chat.name}&background=0ea5e9&color=fff`"
                             class="w-12 h-12 rounded-full object-cover">
                        <template x-if="chat.type === 'group'">
                            <span class="absolute -bottom-1 -right-1 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-white text-xs"></i>
                            </span>
                        </template>
                        <template x-if="chat.is_online">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-dark-100 rounded-full"></span>
                        </template>
                    </div>

                    <!-- Info -->
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-900 dark:text-white truncate" x-text="chat.name"></h3>
                            <div class="flex items-center space-x-1">
                                <span class="text-xs text-gray-400" x-text="formatTime(chat.updated_at)"></span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="chat.last_message?.content || 'Belum ada pesan'"></p>
                            <template x-if="chat.is_pinned">
                                <i class="fas fa-thumbtack text-primary-500 text-xs"></i>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && filteredChats.length === 0">
                <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                    <i class="fas fa-comments text-4xl mb-3"></i>
                    <p>Tidak ada chat</p>
                </div>
            </template>
        </div>
    </aside>

    <!-- ==================== CENTER CONVERSATION ==================== -->
    <main class="flex-1 flex flex-col bg-gray-50 dark:bg-dark-200"
          :class="{'hidden md:flex': activeView !== 'chat'}">
        
        <!-- No Chat Selected -->
        <template x-if="!selectedChat">
            <div class="flex-1 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-purple-600 rounded-3xl flex items-center justify-center shadow-lg mb-4">
                    <i class="fas fa-comments text-4xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Social Chat</h2>
                <p class="text-gray-500 dark:text-gray-400">Pilih chat untuk memulai percakapan</p>
            </div>
        </template>

        <!-- Chat View -->
        <template x-if="selectedChat">
            <!-- Chat Header -->
            <div class="p-4 bg-white dark:bg-dark-100 border-b border-gray-200 dark:border-dark-300 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <button @click="activeView = 'chat'; selectedChatId = null" class="md:hidden p-2 -ml-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="relative">
                        <img :src="selectedChat.avatar || `https://ui-avatars.com/api/?name=${selectedChat.name}&background=0ea5e9&color=fff`"
                             class="w-10 h-10 rounded-full object-cover">
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-dark-100 rounded-full"
                              :class="{'hidden': !selectedChat.is_online}"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white" x-text="selectedChat.name"></h3>
                        <p class="text-xs text-gray-500" x-text="selectedChat.is_online ? 'Online' : 'Offline'"></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="startCall('voice')" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-dark-300 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button @click="startCall('video')" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-dark-300 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-video"></i>
                    </button>
                    <button @click="showChatInfo = !showChatInfo" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-dark-300 text-primary-500" title="Details">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="messagesContainer">
                <template x-for="message in messages" :key="message.id">
                    <div class="flex" :class="message.sender_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[70%] group" :class="message.sender_id === {{ auth()->id() }} ? 'order-2' : 'order-1'">
                            <!-- Message Bubble -->
                            <div class="px-4 py-2 rounded-2xl shadow-sm"
                                 :class="message.sender_id === {{ auth()->id() }} 
                                    ? 'bg-primary-500 text-white rounded-br-md' 
                                    : 'bg-white dark:bg-dark-100 text-gray-900 dark:text-white rounded-bl-md'">
                                
                                <!-- Reply -->
                                <template x-if="message.reply_to">
                                    <div class="mb-2 pb-2 border-b border-gray-200 dark:border-gray-700"
                                         :class="message.sender_id === {{ auth()->id() }} ? 'border-white/20' : ''">
                                        <p class="text-xs opacity-70" x-text="message.reply_to.sender?.name"></p>
                                        <p class="text-sm truncate" x-text="message.reply_to.content"></p>
                                    </div>
                                </template>

                                <!-- Text Content -->
                                <p x-show="message.content" x-text="message.content"></p>

                                <!-- File/Media -->
                                <template x-if="message.file_path">
                                    <div class="mt-2">
                                        <template x-if="message.type === 'image'">
                                            <img :src="'/storage/messages/' + message.file_path" 
                                                 class="rounded-lg max-w-full cursor-pointer" @click="openImage(message.file_path)">
                                        </template>
                                        <template x-if="message.type === 'video'">
                                            <video :src="'/storage/messages/' + message.file_path" 
                                                   controls class="rounded-lg max-w-full"></video>
                                        </template>
                                        <template x-if="message.type === 'voice'">
                                            <div class="flex items-center gap-2 p-2 rounded-lg" 
                                                 :class="message.sender_id === {{ auth()->id() }} ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-700'">
                                                <audio :src="'/storage/messages/' + message.file_path" controls class="h-8"></audio>
                                            </div>
                                        </template>
                                        <template x-if="message.type === 'file'">
                                            <div class="flex items-center space-x-2 p-2 bg-black/10 rounded-lg">
                                                <i class="fas fa-file"></i>
                                                <span class="text-sm" x-text="message.file_name"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                
                                <!-- Location -->
                                <template x-if="message.type === 'location'">
                                    <a :href="'https://www.google.com/maps?q=' + message.content" target="_blank"
                                       class="flex items-center gap-2 p-2 rounded-lg bg-blue-500 hover:bg-blue-600 mt-2">
                                        <i class="fas fa-map-marker-alt text-white"></i>
                                        <span class="text-sm text-white">Buka di Google Maps</span>
                                    </a>
                                </template>

                                <!-- Time & Status -->
                                <div class="flex items-center justify-end mt-1 space-x-1"
                                     :class="message.sender_id === {{ auth()->id() }} ? 'text-white/70' : 'text-gray-400'">
                                    <span class="text-xs" x-text="formatTime(message.created_at)"></span>
                                    <template x-if="message.sender_id === {{ auth()->id() }}">
                                        <i class="fas fa-check-double text-xs"></i>
                                    </template>
                                </div>
                            </div>

                            <!-- Reactions -->
                            <template x-if="message.reactions && message.reactions.length > 0">
                                <div class="flex flex-wrap gap-1 mt-1" 
                                     :class="message.sender_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                                    <template x-for="reaction in message.reactions" :key="reaction.id">
                                        <span class="text-xs px-1.5 py-0.5 bg-gray-200 dark:bg-dark-300 rounded-full" 
                                              x-text="reaction.emoji"></span>
                                    </template>
                                </div>
                            </template>

                            <!-- Actions (hanya untuk pesan sendiri) -->
                            <div class="flex items-center space-x-1 mt-1">
                                <button @click="replyTo = message" class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-dark-300 text-gray-500" title="Reply">
                                    <i class="fas fa-reply text-xs"></i>
                                </button>
                                <button @click="if(message.sender_id === {{ auth()->id() }}) editMessage(message)" :class="message.sender_id === {{ auth()->id() }} ? 'text-blue-500' : 'text-gray-300 cursor-not-allowed'" class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-dark-300" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button @click="if(message.sender_id === {{ auth()->id() }}) deleteMessage(message)" :class="message.sender_id === {{ auth()->id() }} ? 'text-red-500' : 'text-gray-300 cursor-not-allowed'" class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-dark-300" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Typing Indicator -->
                <template x-if="typingUsers.length > 0">
                    <div class="flex items-center space-x-2">
                        <div class="px-4 py-2 bg-gray-200 dark:bg-dark-300 rounded-2xl rounded-bl-md">
                            <div class="flex space-x-1">
                                <span class="w-2 h-2 bg-gray-500 rounded-full typing-dot"></span>
                                <span class="w-2 h-2 bg-gray-500 rounded-full typing-dot"></span>
                                <span class="w-2 h-2 bg-gray-500 rounded-full typing-dot"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white dark:bg-dark-100 border-t border-gray-200 dark:border-dark-300">
                <!-- Reply Preview -->
                <template x-if="replyTo">
                    <div class="mb-2 flex items-center justify-between p-2 bg-gray-100 dark:bg-dark-300 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-reply text-primary-500"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300" x-text="'Reply to ' + (replyTo.sender?.name || 'message')"></span>
                        </div>
                        <button @click="replyTo = null" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>

                <div class="flex items-end space-x-2">
                    <!-- Attachment Button -->
                    <div class="relative" x-data="{open: false}">
                        <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-dark-300 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute bottom-full left-0 mb-2 bg-white dark:bg-dark-100 rounded-xl shadow-lg border border-gray-200 dark:border-dark-300 py-2 z-50">
                            <button @click="open = false; $refs.fileInput.click()" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-image mr-2"></i>Gambar
                            </button>
                            <button @click="open = false; $refs.fileInput.click()" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-video mr-2"></i>Video
                            </button>
                            <button @click="open = false; $refs.fileInput.click()" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-file mr-2"></i>File
                            </button>
                            <button @click="open = false; $refs.voiceInput.click()" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-microphone mr-2"></i>Voice
                            </button>
                            <button @click="open = false; sendLocation()" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-dark-300">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Lokasi
                            </button>
                        </div>
                        <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                        <input type="file" x-ref="voiceInput" @change="handleFileUpload($event)" class="hidden" accept="audio/*">
                    </div>

                    <!-- Location Button (Direct) -->
                    <button @click="sendLocation()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-dark-300 text-gray-600 dark:text-gray-300" title="Kirim Lokasi">
                        <i class="fas fa-map-marker-alt"></i>
                    </button>

                    <!-- Message Input -->
                    <div class="flex-1 relative flex items-center gap-2">
                        <button @click="toggleVoiceRecording()" 
                                :class="isRecording ? 'bg-red-500 text-white animate-pulse' : ''"
                                class="p-2 rounded-xl hover:bg-gray-200 dark:hover:bg-dark-300 text-gray-500" title="Voice">
                            <i class="fas" :class="isRecording ? 'fa-stop' : 'fa-microphone'"></i>
                        </button>
                        <textarea x-model="newMessage" 
                                  @keydown.enter.exact.prevent="sendMessage()"
                                  placeholder="Ketik pesan..."
                                  rows="1"
                                  class="flex-1 px-4 py-2 bg-gray-100 dark:bg-dark-300 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>

                    <!-- Send Button -->
                    <button @click="sendMessage()" 
                            :disabled="!newMessage.trim()"
                            class="p-3 rounded-xl bg-primary-500 text-white hover:bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                
                <!-- Voice Recording Indicator -->
                <div x-show="isRecording" class="mt-2 p-2 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-red-500" x-text="recordingTime"></span>
                    <span class="text-xs text-gray-500">Merekam...</span>
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between mt-2 text-xs text-gray-400">
                    <div class="flex items-center space-x-3">
                        <button @click="showScheduleModal = true" class="hover:text-primary-500">
                            <i class="fas fa-clock mr-1"></i>Jadwalkan
                        </button>
                        <button @click="showSelfDestructModal = true" class="hover:text-primary-500">
                            <i class="fas fa-stopwatch mr-1"></i>Self-destruct
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </main>

    <!-- ==================== RIGHT PANEL ==================== -->
    <!-- Profile / Media / Info -->
    <aside class="w-full md:w-72 lg:w-80 bg-white dark:bg-dark-100 border-l border-gray-200 dark:border-dark-300 flex flex-col"
           :class="{'hidden md:flex': !showChatInfo || !selectedChat}">
        
        <template x-if="selectedChat">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 dark:border-dark-300 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Info</h3>
                    <button @click="showChatInfo = false" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-dark-300">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>

                <!-- Profile -->
                <div class="p-4 text-center">
                    <img :src="selectedChat.avatar || `https://ui-avatars.com/api/?name=${selectedChat.name}&background=0ea5e9&color=fff`"
                         class="w-24 h-24 rounded-full object-cover mx-auto mb-3 ring-4 ring-primary-500/20">
                    <h2 class="font-bold text-lg text-gray-900 dark:text-white" x-text="selectedChat.name"></h2>
                    <p class="text-sm text-gray-500" x-text="selectedChat.is_online ? 'Online' : 'Offline'"></p>

                    <!-- Actions -->
                    <div class="flex justify-center space-x-3 mt-4">
                        <button @click="startCall('voice')" class="p-3 rounded-full bg-gray-100 dark:bg-dark-300 hover:bg-gray-200 dark:hover:bg-dark-300">
                            <i class="fas fa-phone text-primary-500"></i>
                        </button>
                        <button @click="startCall('video')" class="p-3 rounded-full bg-gray-100 dark:bg-dark-300 hover:bg-gray-200 dark:hover:bg-dark-300">
                            <i class="fas fa-video text-primary-500"></i>
                        </button>
                    </div>
                </div>

                <!-- Media Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Media</h4>
                    <div class="grid grid-cols-3 gap-1">
                        <template x-for="media in chatMedia" :key="media.id">
                            <img :src="'/storage/messages/' + media.file_path" 
                                 class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-80">
                        </template>
                    </div>
                    
                    <!-- Empty Media State -->
                    <template x-if="chatMedia.length === 0">
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-images text-3xl mb-2"></i>
                            <p class="text-sm">Belum ada media</p>
                        </div>
                    </template>
                </div>

                <!-- Details -->
                <div class="p-4 border-t border-gray-200 dark:border-dark-300 space-y-2">
                    <p class="text-xs text-gray-400 px-2 mb-1">Details</p>
                    
                    <button @click="togglePin()" 
                            class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-300">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-thumbtack mr-2"></i>Pin Chat
                        </span>
                        <i class="fas text-primary-500" :class="selectedChat.is_pinned ? 'fa-check' : ''"></i>
                    </button>
                    
                    <button @click="showLabelModal = true" 
                            class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-300">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-tag mr-2"></i>Add Label
                        </span>
                        <span x-text="selectedChat.label || 'None'" class="text-xs text-gray-400"></span>
                    </button>
                    
                    <!-- Group Actions -->
                    <template x-if="selectedChat.type === 'group'">
                        <div class="space-y-1">
                            <button @click="leaveGroup(selectedChat)" 
                                    class="w-full flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-300 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-sign-out-alt mr-2"></i>Keluar Grup
                            </button>
                            <button @click="deleteGroup(selectedChat)" 
                                    class="w-full flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-300 text-red-500">
                                <i class="fas fa-trash mr-2"></i>Hapus Grup
                            </button>
                        </div>
                    </template>
                    
                    <!-- Private Actions -->
                    <template x-if="selectedChat.type === 'private'">
                        <div class="space-y-1">
                            <button @click="deleteChat(selectedChat)" 
                                    class="w-full flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-300 text-red-500">
                                <i class="fas fa-trash mr-2"></i>Hapus Chat
                            </button>
                            <button @click="removeContact(selectedChat)" 
                                    class="w-full flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-300 text-red-500">
                                <i class="fas fa-user-minus mr-2"></i>Hapus Kontak
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </aside>

    <!-- ==================== MOBILE BOTTOM NAV ==================== -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-dark-100 border-t border-gray-200 dark:border-dark-300 flex justify-around py-2 px-4 z-40">
        <a href="#" @click="activeView = 'chat'" 
           class="flex flex-col items-center p-2 rounded-lg transition-all"
           :class="activeView === 'chat' ? 'text-primary-500' : 'text-gray-400'">
            <i class="fas fa-comment-alt text-xl"></i>
            <span class="text-xs mt-1">Chat</span>
        </a>
        <a href="#" @click="activeView = 'story'" 
           class="flex flex-col items-center p-2 rounded-lg transition-all"
           :class="activeView === 'story' ? 'text-primary-500' : 'text-gray-400'">
            <i class="fas fa-history text-xl"></i>
            <span class="text-xs mt-1">Story</span>
        </a>
        <a href="#" @click="activeView = 'timeline'" 
           class="flex flex-col items-center p-2 rounded-lg transition-all"
           :class="activeView === 'timeline' ? 'text-primary-500' : 'text-gray-400'">
            <i class="fas fa-images text-xl"></i>
            <span class="text-xs mt-1">Timeline</span>
        </a>
        <a href="#" @click="activeView = 'profile'" 
           class="flex flex-col items-center p-2 rounded-lg transition-all"
           :class="activeView === 'profile' ? 'text-primary-500' : 'text-gray-400'">
            <i class="fas fa-user text-xl"></i>
            <span class="text-xs mt-1">Profil</span>
        </a>
    </nav>

    <!-- Floating Action Button (Mobile) -->
    <button @click="showNewChatModal = true"
            class="md:hidden fixed bottom-16 right-4 w-14 h-14 bg-primary-500 rounded-full shadow-lg flex items-center justify-center text-white z-40">
        <i class="fas fa-plus text-xl"></i>
    </button>

</div>

<script>
function chatApp() {
    return {
        darkMode: localStorage.getItem('darkMode') === 'true',
        loading: true,
        chats: [],
        messages: [],
        selectedChatId: null,
        selectedChat: null,
        newMessage: '',
        replyTo: null,
        searchQuery: '',
        selectedLabel: null,
        activeView: 'chat',
        showChatInfo: false,
        showNewChatModal: false,
        showScheduleModal: false,
        showSelfDestructModal: false,
        showLabelModal: false,
        typingUsers: [],
        isOnline: true,
        chatMedia: [],
        
        // Voice Recording
        isRecording: false,
        recordingTime: '0:00',
        mediaRecorder: null,
        audioChunks: [],
        recordingInterval: null,
        recordingSeconds: 0,

        init() {
            this.loadChats();
            this.setupEcho();
        },

        // Voice Recording Functions
        async toggleVoiceRecording() {
            if (this.isRecording) {
                this.stopVoiceRecording();
            } else {
                await this.startVoiceRecording();
            }
        },
        
        async startVoiceRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];
                this.recordingSeconds = 0;
                this.isRecording = true;
                
                this.mediaRecorder.ondataavailable = event => {
                    this.audioChunks.push(event.data);
                };
                
                this.mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const formData = new FormData();
                    formData.append('file', audioBlob, 'voice.webm');
                    formData.append('type', 'voice');
                    
                    this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                    
                    fetch(`/api/chats/${this.selectedChatId}/messages/media`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    }).then(() => {
                        this.loadMessages(this.selectedChatId);
                    }).catch(err => {
                        console.error('Error sending voice:', err);
                        alert('Gagal mengirim voice note');
                    });
                };
                
                this.mediaRecorder.start();
                
                this.recordingInterval = setInterval(() => {
                    this.recordingSeconds++;
                    const minutes = Math.floor(this.recordingSeconds / 60);
                    const seconds = this.recordingSeconds % 60;
                    this.recordingTime = `${minutes}:${String(seconds).padStart(2, '0')}`;
                }, 1000);
                
            } catch (err) {
                console.error('Error accessing microphone:', err);
                alert('Tidak dapat akses microphone');
            }
        },
        
        stopVoiceRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                this.mediaRecorder.stop();
                this.isRecording = false;
                clearInterval(this.recordingInterval);
                this.recordingTime = '0:00';
            }
        },

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        get filteredChats() {
            let filtered = this.chats;
            
            if (this.searchQuery) {
                filtered = filtered.filter(c => c.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
            }
            
            if (this.selectedLabel === 'pinned') {
                filtered = filtered.filter(c => c.is_pinned);
            } else if (this.selectedLabel === 'group') {
                filtered = filtered.filter(c => c.type === 'group');
            }
            
            return filtered;
        },

        loadChats() {
            fetch('/api/chats', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.chats = data;
                this.loading = false;
            })
            .catch(err => {
                console.error(err);
                this.loading = false;
            });
        },

        selectChat(chat) {
            this.selectedChatId = chat.id;
            this.selectedChat = chat;
            this.activeView = 'chat';
            this.loadMessages(chat.id);
        },

        loadMessages(chatId) {
            fetch(`/api/chats/${chatId}/messages`, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.messages = data.data || data;
                this.scrollToBottom();
            });
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;

            const data = {
                content: this.newMessage,
                reply_to_id: this.replyTo?.id || null
            };

            fetch(`/api/chats/${this.selectedChatId}/messages`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(message => {
                this.messages.push(message);
                this.newMessage = '';
                this.replyTo = null;
                this.scrollToBottom();
            });
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', file.type.startsWith('image') ? 'image' : file.type.startsWith('video') ? 'video' : 'file');

            fetch(`/api/chats/${this.selectedChatId}/messages/media`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(message => {
                this.messages.push(message);
                this.scrollToBottom();
            });
        },

        startCall(type) {
            window.location.href = `/call/${this.selectedChatId}?type=${type}`;
        },

        togglePin() {
            fetch(`/api/chats/${this.selectedChatId}/pin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.selectedChat.is_pinned = data.is_pinned;
            });
        },

        editMessage(message) {
            const newContent = prompt('Edit pesan:', message.content);
            if (newContent === null || newContent === message.content) return;

            fetch(`/api/messages/${message.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: newContent })
            })
            .then(res => res.json())
            .then(data => {
                const idx = this.messages.findIndex(m => m.id === message.id);
                if (idx !== -1) {
                    this.messages[idx].content = data.message.content;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal edit pesan');
            });
        },

        sendLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation tidak didukung browser ini');
                return;
            }
            
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                fetch(`/api/chats/${this.selectedChatId}/messages`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ content: `${lat},${lng}`, type: 'location' })
                }).then(() => {
                    this.loadMessages(this.selectedChatId);
                }).catch(err => {
                    console.error(err);
                    alert('Gagal mengirim lokasi');
                });
            }, (error) => {
                alert('Tidak bisa dapat lokasi: ' + error.message);
            });
        },
                    body: JSON.stringify({ content: `${lat},${lng}`, type: 'location' })
                }).then(() => {
                    this.loadMessages(this.selectedChatId);
                }).catch(err => {
                    console.error(err);
                    alert('Gagal mengirim lokasi');
                });
            }, (error) => {
                alert('Tidak bisa dapat lokasi: ' + error.message);
            });
        },

        deleteMessage(message) {
            if (!confirm('Hapus pesan ini?')) return;

            fetch(`/api/messages/${message.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                this.messages = this.messages.filter(m => m.id !== message.id);
            });
        },

        deleteGroup(chat) {
            if (!confirm('Hapus grup ini? Semua pesan akan dihapus.')) return;

            fetch(`/api/chats/${chat.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                this.chats = this.chats.filter(c => c.id !== chat.id);
                if (this.selectedChatId === chat.id) {
                    this.selectedChatId = null;
                    this.selectedChat = null;
                    this.messages = [];
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghapus grup');
            });
        },

        leaveGroup(chat) {
            if (!confirm('Keluar dari grup ini?')) return;

            fetch(`/api/chats/${chat.id}/leave`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                this.chats = this.chats.filter(c => c.id !== chat.id);
                if (this.selectedChatId === chat.id) {
                    this.selectedChatId = null;
                    this.selectedChat = null;
                    this.messages = [];
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal keluar dari grup');
            });
        },

        deleteChat(chat) {
            if (!confirm('Hapus chat ini? Semua pesan akan dihapus.')) return;

            fetch(`/api/chats/${chat.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                this.chats = this.chats.filter(c => c.id !== chat.id);
                if (this.selectedChatId === chat.id) {
                    this.selectedChatId = null;
                    this.selectedChat = null;
                    this.messages = [];
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghapus chat');
            });
        },

        removeContact(chat) {
            if (!confirm('Hapus kontak ini? Chat juga akan dihapus.')) return;

            fetch(`/api/chats/${chat.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                this.chats = this.chats.filter(c => c.id !== chat.id);
                if (this.selectedChatId === chat.id) {
                    this.selectedChatId = null;
                    this.selectedChat = null;
                    this.messages = [];
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghapus kontak');
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        formatTime(date) {
            const d = new Date(date);
            const now = new Date();
            const diff = now - d;
            
            if (diff < 60000) return 'Baru';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm';
            if (diff < 86400000) return d.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            return d.toLocaleDateString('id-ID', {day: 'numeric', month: 'short'});
        },

        openImage(path) {
            window.open('/storage/messages/' + path, '_blank');
        },

        setupEcho() {
            if (typeof Echo === 'undefined') return;
            
            window.Echo.private('chat.' + this.selectedChatId)
                .listen('MessageSent', (e) => {
                    if (e.message.chat_id === this.selectedChatId) {
                        this.messages.push(e.message);
                        this.scrollToBottom();
                    }
                });
        }
    };
}
</script>
@endsection
