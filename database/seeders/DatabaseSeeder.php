<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Story;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat admin default
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@socialchat.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => 'Administrator Social Chat',
            'is_online' => true,
        ]);

        // Buat user contoh
        $user1 = User::create([
            'name' => 'User Test',
            'email' => 'Okaramdani4@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'System testing account for Social Chat platform. Developed by Oka Ramdani.',
            'mood' => 'Happy',
            'is_online' => true,
        ]);

        // Buat user contoh lainnya
        $user2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Hai, saya Budi!',
            'mood' => 'Excited',
            'is_online' => true,
        ]);

        $user3 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Welcome to my profile!',
            'mood' => 'Happy',
            'is_online' => false,
        ]);

        $user4 = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Just another user',
            'mood' => 'Cool',
            'is_online' => true,
        ]);

        // Buat chat private antara user1 dan user2
        $chat1 = Chat::create([
            'type' => 'private',
            'created_by' => $user1->id,
        ]);
        $chat1->participants()->attach([$user1->id, $user2->id]);

        // Buat chat private antara user1 dan user3
        $chat2 = Chat::create([
            'type' => 'private',
            'created_by' => $user1->id,
        ]);
        $chat2->participants()->attach([$user1->id, $user3->id]);

        // Buat grup chat
        $groupChat = Chat::create([
            'name' => 'Social Chat Group',
            'type' => 'group',
            'created_by' => $user1->id,
        ]);
        $groupChat->participants()->attach([$user1->id, $user2->id, $user3->id, $user4->id]);

        // Buat pesan di chat1
        Message::create([
            'chat_id' => $chat1->id,
            'sender_id' => $user2->id,
            'content' => 'Halo! Apa kabar?',
            'type' => 'text',
        ]);

        Message::create([
            'chat_id' => $chat1->id,
            'sender_id' => $user1->id,
            'content' => 'Hi Budi! Kabar baik, kamu bagaimana?',
            'type' => 'text',
        ]);

        Message::create([
            'chat_id' => $chat1->id,
            'sender_id' => $user2->id,
            'content' => 'Baik juga! Selamat datang di Social Chat!',
            'type' => 'text',
        ]);

        // Buat pesan di group
        Message::create([
            'chat_id' => $groupChat->id,
            'sender_id' => $user2->id,
            'content' => 'Halo semua!',
            'type' => 'text',
        ]);

        Message::create([
            'chat_id' => $groupChat->id,
            'sender_id' => $user3->id,
            'content' => 'Hai Budi!',
            'type' => 'text',
        ]);

        Message::create([
            'chat_id' => $groupChat->id,
            'sender_id' => $user4->id,
            'content' => 'Welcome everyone!',
            'type' => 'text',
        ]);
    }
}
