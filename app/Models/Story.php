<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'media_path',
        'type',
        'caption',
        'expires_at',
        'is_deleted',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    // Relasi: user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: views
    public function views(): HasMany
    {
        return $this->hasMany(StoryView::class, 'story_id');
    }

    // Cek apakah story sudah expired
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // Get media URL
    public function getMediaUrlAttribute(): string
    {
        return asset('storage/stories/' . $this->media_path);
    }
}
