<?php

namespace App\Console\Commands;

use App\Models\Story;
use Illuminate\Console\Command;

class DeleteExpiredStories extends Command
{
    protected $signature = 'stories:delete-expired';
    protected $description = 'Hapus story yang sudah expired (24 jam)';

    public function handle(): int
    {
        $count = Story::where('expires_at', '<=', now())
            ->where('is_deleted', false)
            ->update(['is_deleted' => true]);

        $this->info("Berhasil hapus {$count} story expired");
        return Command::SUCCESS;
    }
}
