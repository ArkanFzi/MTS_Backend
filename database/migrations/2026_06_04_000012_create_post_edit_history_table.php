<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_edit_history', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->foreignUuid('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignUuid('edited_by')->constrained('users')->onDelete('cascade');
            $table->text('body_before')->nullable();
            $table->text('body_after')->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamp('edited_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_edit_history');
    }
};