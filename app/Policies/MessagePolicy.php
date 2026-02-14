<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Determine whether the user can view any messages.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the message.
     */
    public function view(User $user, Message $message): bool
    {
        return $user->isAdmin() || $message->sender_id === $user->id;
    }

    /**
     * Determine whether the user can create messages.
     */
    public function create(User $user): bool
    {
        return !$user->is_suspended;
    }

    /**
     * Determine whether the user can update the message.
     */
    public function update(User $user, Message $message): bool
    {
        return $user->isAdmin() || $message->sender_id === $user->id;
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function delete(User $user, Message $message): bool
    {
        return $user->isAdmin() || $message->sender_id === $user->id;
    }

    /**
     * Determine whether the user can react to the message.
     */
    public function react(User $user, Message $message): bool
    {
        return !$user->is_suspended;
    }

    /**
     * Determine whether user can delete any message (admin only).
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
