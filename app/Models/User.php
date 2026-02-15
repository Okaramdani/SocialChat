<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasDatabaseNotifications;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'mood',
        'role',
        'is_online',
        'last_seen',
        'is_suspended',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen' => 'datetime',
        'is_online' => 'boolean',
        'is_suspended' => 'boolean',
    ];

    protected $appends = ['avatar_url'];

    // Relasi: pesan yang dikirim user
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Relasi: chat yang diikuti user
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    // Relasi: chat yang dibuat user
    public function createdChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    // Relasi: story user
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'user_id');
    }

    // Relasi: media timeline user
    public function mediaTimelines(): HasMany
    {
        return $this->hasMany(MediaTimeline::class, 'user_id');
    }

    // Relasi: call logs sebagai caller
    public function outgoingCalls(): HasMany
    {
        return $this->hasMany(CallLog::class, 'caller_id');
    }

    // Relasi: call logs sebagai receiver
    public function incomingCalls(): HasMany
    {
        return $this->hasMany(CallLog::class, 'receiver_id');
    }

    // Relasi: reactions user
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'user_id');
    }

    // Cek apakah user adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cek apakah user di-suspend
    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    // Get avatar URL
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            $version = $this->updated_at ? $this->updated_at->timestamp : time();
            return asset('assets/avatars/' . $this->avatar) . '?v=' . $version;
        }
        return asset('images/default-avatar.png');
    }

    // Update status online
    public function setOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen' => now(),
        ]);
    }

    public function setOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen' => now(),
        ]);
    }
}
