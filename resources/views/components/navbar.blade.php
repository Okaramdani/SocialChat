{{-- 
    Navbar Component
--}}

<header class="h-14 bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 flex items-center justify-between px-4 sticky top-0 z-40 shadow-lg">
    <!-- Left -->
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-full hover:bg-primary-500/30 transition-colors">
            <i class="fas fa-bars text-white"></i>
        </button>
        
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-comments text-white text-sm"></i>
            </div>
            <span class="font-bold text-white text-lg hidden sm:block">Social Chat</span>
        </a>
    </div>
    
    <!-- Right -->
    <div class="flex items-center gap-1">
        <!-- Stories -->
        <a href="{{ url('/stories') }}" class="p-2.5 rounded-full hover:bg-primary-500/30 transition-colors" title="Stories">
            <i class="fas fa-circle-notch text-white text-lg"></i>
        </a>
        
        <!-- New Chat -->
        <button onclick="window.dispatchEvent(new CustomEvent('open-new-chat'))" class="p-2.5 rounded-full hover:bg-primary-500/30 transition-colors" title="New Chat">
            <i class="fas fa-comment-medical text-white text-lg"></i>
        </button>
        
        <!-- Menu -->
        <div x-data="{ menuOpen: false }" class="relative">
            <button @click="menuOpen = !menuOpen" class="p-2.5 rounded-full hover:bg-primary-500/30 transition-colors">
                <i class="fas fa-ellipsis-vertical text-white text-lg"></i>
            </button>
            
            <div x-show="menuOpen" x-cloak @click.away="menuOpen = false"
                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-dark-100 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1 z-50">
                <a href="{{ url('/timeline') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-clock w-4 text-gray-500"></i>
                    <span>Timeline</span>
                </a>
                <a href="{{ url('/leaderboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-trophy w-4 text-yellow-500"></i>
                    <span>Leaderboard</span>
                </a>
                <a href="{{ url('/chatbot') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-robot w-4 text-primary-500"></i>
                    <span>AI Chatbot</span>
                </a>
                <hr class="my-1 border-gray-200 dark:border-gray-700">
                <a href="{{ url('/settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-cog w-4 text-gray-500"></i>
                    <span>Settings</span>
                </a>
                <a href="{{ url('/profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-user w-4 text-gray-500"></i>
                    <span>Profile</span>
                </a>
                <hr class="my-1 border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-gray-700 w-full">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
