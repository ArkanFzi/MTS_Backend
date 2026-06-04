<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_tags', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->foreignUuid('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignUuid('tag_id')->constrained('tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tags');
    }
};