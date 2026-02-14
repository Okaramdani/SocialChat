<?php

namespace App\Events;

use App\Models\Story;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoryUploaded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Story $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('stories'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'story' => [
                'id' => $this->story->id,
                'user_id' => $this->story->user_id,
                'type' => $this->story->type,
                'expires_at' => $this->story->expires_at->toIso8601String(),
                'user' => [
                    'id' => $this->story->user->id,
                    'name' => $this->story->user->name,
                    'avatar' => $this->story->user->avatar,
                ],
            ],
        ];
    }
}
