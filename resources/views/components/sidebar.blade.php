{{-- 
    Sidebar Component - Icon Only with More Spacing
--}}

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
       class="fixed lg:static inset-y-0 left-0 z-50 w-16 bg-white dark:bg-dark-100 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 flex flex-col">
    
    <!-- Logo -->
    <div class="h-14 flex items-center justify-center border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-600 to-primary-700">
        <a href="{{ route('dashboard') }}" class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-paper-plane text-white text-sm"></i>
        </a>
    </div>
    
    <!-- Navigation Icons - Vertical with more spacing -->
    <nav class="flex-1 py-4 flex flex-col items-center gap-3 overflow-y-auto">
        
        <!-- Main Menu -->
        <a href="{{ route('dashboard') }}" class="nav-icon {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Home">
            <i class="fas fa-home"></i>
        </a>
        
        <a href="{{ url('/chat') }}" class="nav-icon {{ request()->routeIs('chat*') ? 'active' : '' }}" title="Chats">
            <i class="fas fa-comment-dots"></i>
        </a>
        
        <a href="{{ url('/stories') }}" class="nav-icon {{ request()->routeIs('stories*') ? 'active' : '' }}" title="Stories">
            <i class="fas fa-circle-notch"></i>
        </a>
        
        <a href="{{ url('/timeline') }}" class="nav-icon {{ request()->routeIs('timeline*') ? 'active' : '' }}" title="Timeline">
            <i class="fas fa-clock"></i>
        </a>
        
        <a href="{{ url('/leaderboard') }}" class="nav-icon {{ request()->routeIs('leaderboard*') ? 'active' : '' }}" title="Leaderboard">
            <i class="fas fa-trophy"></i>
        </a>
        
        <a href="{{ url('/chatbot') }}" class="nav-icon {{ request()->routeIs('chatbot*') ? 'active' : '' }}" title="AI Chatbot">
            <i class="fas fa-robot"></i>
        </a>
        
        <!-- Spacer -->
        <div class="flex-1"></div>
        
        <!-- Actions -->
        <a href="{{ route('stories.create') }}" class="nav-icon" title="Add Story">
            <i class="fas fa-plus-square"></i>
        </a>
        
        <a href="{{ route('call.select') }}" class="nav-icon" title="Start Call">
            <i class="fas fa-phone"></i>
        </a>
        
        <button onclick="openCreateGroupModal()" class="nav-icon" title="New Group">
            <i class="fas fa-plus-circle"></i>
        </button>
    </nav>
    
    <!-- Profile -->
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex justify-center">
        <a href="{{ url('/profile') }}" class="nav-icon" title="Profile">
            <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" 
                 class="w-7 h-7 rounded-full object-cover">
        </a>
    </div>
</aside>

<style>
    .nav-icon {
        @apply w-10 h-10 flex items-center justify-center rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200;
    }
    .nav-icon.active {
        @apply bg-primary-100 dark:bg-primary-900/50 text-primary-500;
    }
</style>
