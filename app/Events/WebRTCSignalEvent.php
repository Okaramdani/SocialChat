<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignalEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $fromUserId;
    public int $toUserId;
    public array $signal;

    public function __construct(int $fromUserId, int $toUserId, array $signal)
    {
        $this->fromUserId = $fromUserId;
        $this->toUserId = $toUserId;
        $this->signal = $signal;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->toUserId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'from_user_id' => $this->fromUserId,
            'signal' => $this->signal,
        ];
    }
}
