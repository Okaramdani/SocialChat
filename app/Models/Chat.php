<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'type',
        'created_by',
        'is_pinned',
        'label',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    // Relasi: user yang membuat chat
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi: peserta chat
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    // Relasi: pesan dalam chat
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    // Relasi: pesan terakhir
    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id')->latest()->limit(1);
    }

    // Get nama chat (jika grup atau nama lawan chat)
    public function getDisplayName(User $currentUser): string
    {
        if ($this->type === 'group') {
            return $this->name ?? 'Group Chat';
        }

        $participant = $this->participants()
            ->where('user_id', '!=', $currentUser->id)
            ->first();

        return $participant?->name ?? 'Unknown';
    }

    // Get avatar chat
    public function getDisplayAvatar(User $currentUser): ?string
    {
        if ($this->type === 'group') {
            return $this->avatar;
        }

        $participant = $this->participants()
            ->where('user_id', '!=', $currentUser->id)
            ->first();

        return $participant?->avatar;
    }
}
