<?php

namespace App\Notifications;

use App\Models\Story;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewStoryNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'story',
            'story_id' => $this->story->id,
            'user_id' => $this->story->user_id,
            'user_name' => $this->story->user->name,
            'user_avatar' => $this->story->user->avatar_url,
            'created_at' => $this->story->created_at->toIso8601String(),
        ];
    }

    public function broadcastOn(): array
    {
        return ['notifications.all'];
    }
}
