<?php

namespace App\Events;

use App\Models\CallLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CallLog $callLog;

    public function __construct(CallLog $callLog)
    {
        $this->callLog = $callLog;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->callLog->receiver_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'call' => [
                'id' => $this->callLog->id,
                'type' => $this->callLog->type,
                'status' => $this->callLog->status,
                'caller' => [
                    'id' => $this->callLog->caller->id,
                    'name' => $this->callLog->caller->name,
                    'avatar' => $this->callLog->caller->avatar,
                ],
            ],
        ];
    }
}
