<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// User presence channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private chat channel
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Cek apakah user adalah peserta chat
    return \App\Models\Chat::where('id', $chatId)
        ->whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists() || $user->isAdmin();
});

// User notification channel
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Public stories channel
Broadcast::channel('stories', function ($user) {
    return true;
});

// Call signaling channel
Broadcast::channel('call.{chatId}', function ($user, $chatId) {
    return \App\Models\Chat::where('id', $chatId)
        ->whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists() || $user->isAdmin();
});
