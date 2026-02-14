<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'reply_to_id',
        'content',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'self_destruct_at',
        'scheduled_at',
        'is_deleted',
    ];

    protected $casts = [
        'self_destruct_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    // Relasi: chat
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    // Relasi: pengirim
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relasi: reply ke pesan
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    // Relasi: replies
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    // Relasi: reactions
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'message_id');
    }

    // Cek apakah pesan sudah expired (self-destruct)
    public function isExpired(): bool
    {
        return $this->self_destruct_at && $this->self_destruct_at->isPast();
    }

    // Cek apakah pesan scheduled dan belum terkirim
    public function isScheduled(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    // Cek apakah pesan sudah seharusnya terkirim
    public function shouldBeSent(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast();
    }

    // Get file URL
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return asset('storage/messages/' . $this->file_path);
        }
        return null;
    }

    // Format ukuran file
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '';

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = $this->file_size;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
