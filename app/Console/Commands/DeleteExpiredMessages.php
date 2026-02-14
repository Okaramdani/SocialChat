<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Console\Command;

class DeleteExpiredMessages extends Command
{
    protected $signature = 'messages:delete-expired';
    protected $description = 'Hapus pesan yang sudah expired (self-destruct)';

    public function handle(): int
    {
        $count = Message::whereNotNull('self_destruct_at')
            ->where('self_destruct_at', '<=', now())
            ->where('is_deleted', false)
            ->update([
                'is_deleted' => true,
                'content' => null,
                'file_path' => null,
            ]);

        $this->info("Berhasil hapus {$count} pesan expired");
        return Command::SUCCESS;
    }
}
