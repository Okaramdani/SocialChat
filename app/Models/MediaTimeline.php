<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_id',
        'media_path',
        'type',
        'file_name',
        'file_size',
    ];

    // Relasi: user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: message
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    // Get media URL
    public function getMediaUrlAttribute(): string
    {
        return asset('storage/media/' . $this->media_path);
    }
}
