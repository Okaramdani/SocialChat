<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use App\Models\Chat;
use App\Models\Story;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    // User hanya bisa hapus pesan miliknya
    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id || $user->isAdmin();
    }

    // User hanya bisa edit pesan miliknya (belum diimplementasi full)
    public function update(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id;
    }
}

class ChatPolicy
{
    use HandlesAuthorization;

    // User harus jadi peserta chat
    public function view(User $user, Chat $chat): bool
    {
        return $chat->participants()->where('user_id', $user->id)->exists() || $user->isAdmin();
    }

    // User harus jadi peserta chat untuk mengirim pesan
    public function sendMessage(User $user, Chat $chat): bool
    {
        return $chat->participants()->where('user_id', $user->id)->exists();
    }
}

class StoryPolicy
{
    use HandlesAuthorization;

    // User hanya bisa hapus story miliknya
    public function delete(User $user, Story $story): bool
    {
        return $user->id === $story->user_id || $user->isAdmin();
    }

    // Semua user bisa lihat story
    public function view(User $user, Story $story): bool
    {
        return !$story->is_deleted && !$story->isExpired();
    }
}

class UserPolicy
{
    use HandlesAuthorization;

    // Admin bisa lakukan apapun
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }

    // User bisa update profilnya sendiri
    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->id === $targetUser->id || $authUser->isAdmin();
    }
}
