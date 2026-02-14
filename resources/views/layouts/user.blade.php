{{-- 
    User Layout
    Layout utama untuk halaman user (dashboard, chat, profile, dll)
    Includes: navbar, sidebar, main content area
--}}

<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    sidebarOpen: false,
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" x-init="
    if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        darkMode = true;
        document.documentElement.classList.add('dark');
    }
    // Listen for custom events from sidebar
    window.addEventListener('open-create-group', () => {
        console.log('open-create-group received in layout');
    });
    window.addEventListener('open-new-chat', () => {
        console.log('open-new-chat received in layout');
    });
" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Social Chat')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('layout', () => ({
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: false,
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }));
        });
        
        // Global event bus for cross-component communication
        window.openCreateGroup = function() {
            window.dispatchEvent(new CustomEvent('open-create-group'));
        };
        window.openNewChat = function() {
            window.dispatchEvent(new CustomEvent('open-new-chat'));
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e' },
                        accent: { 50: '#fdf4ff', 100: '#fae8ff', 500: '#d946ef', 600: '#c026d3' },
                        dark: { 100: '#1e293b', 200: '#0f172a', 300: '#020617' }
                    },
                    animation: {
                        'slide-in': 'slideIn 0.3s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'pulse-once': 'pulse 2s ease-in-out 1',
                    },
                    keyframes: {
                        slideIn: { '0%': { transform: 'translateX(-100%)' }, '100%': { transform: 'translateX(0)' } },
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } }
                    }
                }
            }
        }
    </script>
    
    <style>
        * { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        body { font-family: 'Inter', sans-serif; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        
        .chat-container::-webkit-scrollbar { width: 4px; }
        .message-bubble { @apply rounded-xl shadow-sm; }
        
        .typing-dot { animation: bounce 1.4s infinite ease-in-out both; }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        .typing-dot:nth-child(3) { animation-delay: 0s; }
        
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        
        .online-indicator { @apply w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800; }
        .offline-indicator { @apply w-3 h-3 bg-gray-400 rounded-full border-2 border-white dark:border-gray-800; }
    </style>
    
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-dark-200 text-gray-900 dark:text-gray-100">
    @auth
    <script>
        // Laravel Echo configuration
        window.Echo = window.Echo || {};
        
        // Use Pusher for broadcasting (for production, configure your Pusher credentials)
        // For local development without WebSocket server, messages will be fetched via polling
        if (typeof Pusher !== 'undefined') {
            var pusher = new Pusher('demo-key', {
                cluster: 'mt1',
                broadcaster: 'pusher',
                forceTLS: false
            });
            
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: 'demo-key',
                cluster: 'mt1',
                forceTLS: false
            });
        } else {
            // Fallback: Simple polling for real-time updates
            window.Echo = {
                channel: function(name) {
                    return {
                        listen: function(event, callback) {
                            console.log('Listening to ' + event + ' on ' + name);
                            // Store callbacks for manual triggering
                            if (!window.echoCallbacks) window.echoCallbacks = {};
                            if (!window.echoCallbacks[name]) window.echoCallbacks[name] = {};
                            window.echoCallbacks[name][event] = callback;
                        },
                        notification: function(callback) {
                            console.log('Notification listen on ' + name);
                        }
                    };
                },
                private: function(name) {
                    return this.channel(name);
                }
            };
        }
        
        // Function to manually trigger events (for demo/testing)
        window.triggerEchoEvent = function(channel, event, data) {
            if (window.echoCallbacks && window.echoCallbacks[channel] && window.echoCallbacks[channel][event]) {
                window.echoCallbacks[channel][event](data);
            }
        };
        
        // Global search function
        window.searchUsers = function(query) {
            const searchInput = document.querySelector('input[placeholder="Cari user..."]');
            if (!searchInput) return;
            
            const searchQuery = searchInput.value;
            if (searchQuery.length < 2) {
                const dropdown = searchInput.parentElement.querySelector('[x-show="searchResults.length > 0"]');
                if (dropdown) dropdown.style.display = 'none';
                return;
            }
            
            fetch('/api/users/search?q=' + encodeURIComponent(searchQuery), {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                const container = searchInput.closest('.relative');
                let resultsContainer = container.querySelector('.search-results');
                if (!resultsContainer) {
                    resultsContainer = document.createElement('div');
                    resultsContainer.className = 'search-results absolute top-full left-0 right-0 mt-2 bg-white dark:bg-dark-100 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 max-h-80 overflow-y-auto z-50';
                    container.appendChild(resultsContainer);
                }
                
                if (data.users && data.users.length > 0) {
                    resultsContainer.innerHTML = data.users.map(user => `
                        <a href="/chat?user=${user.id}" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700">
                            <img src="${user.avatar_url || '/images/default-avatar.png'}" class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1">
                                <p class="font-medium">${user.name}</p>
                                <p class="text-xs text-gray-500">${user.is_online ? 'Online' : 'Offline'}</p>
                            </div>
                        </a>
                    `).join('');
                    resultsContainer.style.display = 'block';
                } else {
                    resultsContainer.innerHTML = '<p class="p-3 text-gray-500 text-sm">User tidak ditemukan</p>';
                    resultsContainer.style.display = 'block';
                }
            })
            .catch(err => console.error('Search error:', err));
        };
    </script>
    
    <script>
        // Make functions globally available
        window.openCreateGroupModal = function() {
            document.getElementById('createGroupModal')?.classList.remove('hidden');
            document.getElementById('createGroupModal')?.classList.add('flex');
        };
        window.closeCreateGroupModal = function() {
            document.getElementById('createGroupModal')?.classList.add('hidden');
            document.getElementById('createGroupModal')?.classList.remove('flex');
        };
    </script>
    @endauth
    
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-[calc(100vh-3.5rem)] lg:min-h-screen">
            <!-- Navbar (WhatsApp Style) -->
            @include('components.navbar')
            
            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Create Group Modal (Global) -->
    @auth
    <div id="createGroupModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">
        <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Create Group</h3>
                    <button onclick="closeCreateGroupModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form action="{{ url('/api/chats/group') }}" method="POST" onsubmit="submitGroupForm(event)">
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
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @php
                                $allUsers = \App\Models\User::where('id', '!=', auth()->id())->get();
                            @endphp
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
    
    <script>
        function submitGroupForm(e) {
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
                    closeCreateGroupModal();
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
    @endauth
    
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>
    
    @stack('scripts')
</body>
</html>
