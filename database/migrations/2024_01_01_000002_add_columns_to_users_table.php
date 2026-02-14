<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('mood')->nullable()->after('bio');
            $table->enum('role', ['admin', 'user'])->default('user')->after('mood');
            $table->boolean('is_online')->default(false)->after('role');
            $table->timestamp('last_seen')->nullable()->after('is_online');
            $table->boolean('is_suspended')->default(false)->after('last_seen');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'mood', 'role', 'is_online', 'last_seen', 'is_suspended']);
        });
    }
};
