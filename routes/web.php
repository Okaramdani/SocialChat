<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect old /story routes to /stories
Route::redirect('/story', '/stories');
Route::redirect('/story/create', '/stories/create');

// ==================== SPLASH & AUTH ====================
// Splash screen - tampilan awal sebelum redirect
Route::get('/', function () {
    return view('splash');
})->name('splash');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==================== MAIN APP ====================
// User routes - Admin tidak bisa akses
Route::middleware(['auth', 'prevent.admin'])->group(function () {
    // Dashboard - Main chat interface with stats
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        $chats = $user->chats()
            ->with(['participants', 'latestMessage.sender'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Stats
        $totalChats = $user->chats()->count();
        $totalStories = $user->stories()->count();
        $totalCalls = $user->outgoingCalls()->count() + $user->incomingCalls()->count();
        
        // Leaderboard position
        $leaderboardQuery = \App\Models\User::selectRaw('users.id, users.name, users.email, users.avatar, users.bio, users.mood, users.role, users.is_online, users.last_seen, users.is_suspended, users.created_at, users.updated_at, COUNT(messages.id) as message_count')
            ->leftJoin('messages', 'users.id', '=', 'messages.sender_id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.avatar', 'users.bio', 'users.mood', 'users.role', 'users.is_online', 'users.last_seen', 'users.is_suspended', 'users.created_at', 'users.updated_at')
            ->orderByDesc('message_count')
            ->get();
        
        $leaderboardPosition = $leaderboardQuery->search(fn($u) => $u->id === $user->id) + 1;
        $messageCount = $user->sentMessages()->count();
        
        return view('user.dashboard', compact('chats', 'totalChats', 'totalStories', 'totalCalls', 'leaderboardPosition', 'messageCount'));
    })->name('dashboard');

    // Chat routes
    Route::get('/chat', function () {
        $chats = auth()->user()->chats()
            ->with(['participants', 'latestMessage.sender'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('user.chat.index', compact('chats'));
    })->name('chat.index');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/create/group', function () {
        return view('user.chat.create-group');
    });

    // Stories routes
    Route::get('/stories', function () {
        $stories = \App\Models\Story::where('user_id', auth()->id())
            ->orWhereIn('user_id', \App\Models\User::whereHas('chats.participants', function($q) {
                $q->where('user_id', auth()->id());
            })->pluck('id'))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.story.index', compact('stories'));
    })->name('stories.index');
    Route::get('/stories/create', function () {
        return view('user.story.create');
    })->name('stories.create');
    Route::get('/stories/user/{user}', function (\App\Models\User $user) {
        $stories = $user->stories()
            ->where('expires_at', '>', now())
            ->where('is_deleted', false)
            ->orderBy('created_at', 'asc')
            ->get();
        return view('user.story.view', compact('user', 'stories'));
    });

    // Profile routes
    Route::get('/profile', function () {
        $user = auth()->user();
        $stats = [
            'total_chats' => $user->chats()->count(),
            'total_messages' => $user->sentMessages()->count(),
            'total_stories' => $user->stories()->count(),
        ];
        return view('user.profile.index', compact('user', 'stats'));
    });
    Route::get('/profile/edit', function () {
        return view('user.profile.edit');
    });
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/profile/avatar', [AuthController::class, 'deleteAvatar']);

    // Timeline
    Route::get('/timeline', function () {
        $media = \App\Models\Message::whereNotNull('file_path')
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        return view('user.timeline.index', compact('media'));
    });

    // Leaderboard
    Route::get('/leaderboard', function () {
        $leaderboard = \App\Models\User::withCount('sentMessages')
            ->orderByDesc('sent_messages_count')
            ->limit(20)
            ->get();
        return view('user.leaderboard.index', compact('leaderboard'));
    });

    // AI Chatbot
    Route::get('/chatbot', function () {
        return view('user.chatbot.index');
    })->name('chatbot');

    // Media panel
    Route::get('/media', function () {
        return view('user.chatpanel');
    });

    // Start Call
    Route::get('/call', function () {
        $chats = auth()->user()->chats()->with('participants')->get();
        return view('user.call.select', compact('chats'));
    })->name('call.select');
});

// Settings redirect
Route::redirect('/settings', '/profile');

// Start Call - pilih chat untuk call
Route::get('/call', function () {
    $chats = auth()->user()->chats()->with('participants')->get();
    return view('user.call.select', compact('chats'));
})->name('call.select')->middleware('auth');

// ==================== ADMIN ROUTES ====================
// Semua route admin dilindungi middleware role:admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'userDetail'])->name('user.detail');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('user.suspend');
    Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser'])->name('user.unsuspend');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('user.delete');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('user.update');
    
    // Messages management
    Route::delete('/messages/{message}', [AdminController::class, 'deleteMessage'])->name('message.delete');
    
    // Stories management
    Route::delete('/stories/{story}', [AdminController::class, 'deleteStory'])->name('story.delete');
    
    // Call logs
    Route::get('/calls', [AdminController::class, 'callLogs'])->name('calls');
    
    // Online users (real-time)
    Route::get('/online-users', [AdminController::class, 'onlineUsers'])->name('online.users');
    
    // User stats
    Route::get('/users/{user}/stats', [AdminController::class, 'userStats'])->name('user.stats');
});

// ==================== API ROUTES ====================
// Routes untuk API (bisa digunakan mobile app / AJAX)
Route::prefix('api')->middleware(['web', 'auth'])->group(function () {
    
    // Users
    Route::get('/users/search', [AuthController::class, 'search']);
    
    // Notifications
    Route::get('/notifications', function () {
        $user = auth()->user();
        $notifications = $user->notifications()->latest()->take(20)->get();
        $unread_count = $user->unreadNotifications()->count();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unread_count,
        ]);
    });
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    });
    Route::post('/notifications/{id}/mark-read', function ($id) {
        auth()->user()->notifications()->findOrFail($id)->markAsRead();
        return response()->json(['success' => true]);
    });
    
    // Chats
    Route::get('/chats', [ChatController::class, 'listApi']);
    Route::post('/chats', [ChatController::class, 'createPrivate']);
    Route::post('/chats/group', [ChatController::class, 'createGroup']);
    Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);
    Route::post('/chats/{chat}/messages/media', [MessageController::class, 'storeMedia']);
    Route::post('/chats/{chat}/pin', [ChatController::class, 'pin']);
    Route::put('/chats/{chat}/label', [ChatController::class, 'updateLabel']);
    Route::post('/chats/{chat}/read', [MessageController::class, 'markAsRead']);
    Route::post('/chats/{chat}/leave', [ChatController::class, 'leave']);
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);
    
    // Messages
    Route::post('/messages/{message}/react', [MessageController::class, 'react']);
    Route::delete('/messages/{message}/react', [MessageController::class, 'removeReaction']);
    Route::put('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
    
    // Stories
    Route::get('/stories', [StoryController::class, 'index']);
    Route::get('/stories/user/{user}', [StoryController::class, 'userStories']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::delete('/stories/{story}', [StoryController::class, 'destroy']);
    
    // Media
    Route::get('/media', [MediaController::class, 'timeline']);
    Route::post('/media', [MediaController::class, 'upload']);
    Route::get('/media/{type}', [MediaController::class, 'byType']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    
    // Calls
    Route::post('/call/initiate', [CallController::class, 'initiate']);
    Route::put('/call/{callLog}/status', [CallController::class, 'updateStatus']);
    Route::get('/calls', [CallController::class, 'index']);
    Route::post('/call/signaling', [CallController::class, 'signaling']);
});

// ==================== CALLS ====================
// Video call page
Route::get('/call/{chat}/video', function ($chatId, Request $request) {
    $chat = \App\Models\Chat::findOrFail($chatId);
    $otherUser = $chat->participants()->where('user_id', '!=', auth()->id())->first();
    $callLog = $request->call_id ? \App\Models\CallLog::find($request->call_id) : null;
    return view('user.call.video', ['chatId' => $chatId, 'type' => 'video', 'otherUser' => $otherUser, 'callLog' => $callLog]);
})->name('call.video')->middleware('auth');

// Voice call page
Route::get('/call/{chat}/voice', function ($chatId, Request $request) {
    $chat = \App\Models\Chat::findOrFail($chatId);
    $otherUser = $chat->participants()->where('user_id', '!=', auth()->id())->first();
    $callLog = $request->call_id ? \App\Models\CallLog::find($request->call_id) : null;
    return view('user.call.voice', ['chatId' => $chatId, 'type' => 'voice', 'otherUser' => $otherUser, 'callLog' => $callLog]);
})->name('call.voice')->middleware('auth');

// Legacy call route
Route::get('/call/{chat}', function ($chatId, Request $request) {
    $chat = \App\Models\Chat::findOrFail($chatId);
    $otherUser = $chat->participants()->where('user_id', '!=', auth()->id())->first();
    $callLog = $request->call_id ? \App\Models\CallLog::find($request->call_id) : null;
    $type = $request->type ?? 'video';
    return view('user.call.video', ['chatId' => $chatId, 'type' => $type, 'otherUser' => $otherUser, 'callLog' => $callLog]);
})->name('call')->middleware('auth');

require __DIR__.'/auth.php';
