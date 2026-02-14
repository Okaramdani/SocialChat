<?php

namespace App\Notifications;

use App\Models\CallLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class IncomingCallNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $callLog;

    public function __construct(CallLog $callLog)
    {
        $this->callLog = $callLog;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'call',
            'call_id' => $this->callLog->id,
            'caller_id' => $this->callLog->caller_id,
            'caller_name' => $this->callLog->caller->name,
            'caller_avatar' => $this->callLog->caller->avatar_url,
            'call_type' => $this->callLog->type,
            'created_at' => $this->callLog->created_at->toIso8601String(),
        ];
    }

    public function broadcastOn(): array
    {
        return ['notifications.' . $this->callLog->receiver_id];
    }
}
