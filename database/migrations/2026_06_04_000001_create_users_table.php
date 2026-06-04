<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mengaktifkan ekstensi UUID untuk PostgreSQL jika belum aktif
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('username', 100)->unique();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('avatar_url', 500)->nullable();
            $table->text('bio')->nullable();
            $table->integer('reputation_points')->default(0);
            $table->integer('level')->default(1);
            $table->boolean('is_banned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};