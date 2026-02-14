<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Console\Command;

class SendScheduledMessages extends Command
{
    protected $signature = 'messages:send-scheduled';
    protected $description = 'Kirim pesan yang terjadwal';

    public function handle(): int
    {
        $messages = Message::whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->where('is_deleted', false)
            ->get();

        foreach ($messages as $message) {
            $message->update(['scheduled_at' => null]);
            event(new MessageSent($message));
        }

        $this->info("Berhasil kirim {$messages->count()} pesan terjadwal");
        return Command::SUCCESS;
    }
}
