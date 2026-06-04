<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->foreignUuid('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('parent_id')->nullable();
            $table->text('body');
            $table->integer('vote_score')->default(0);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('comments')
                  ->onDelete('cascade');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('accepted_answer_id', 'posts_accepted_answer_fk')
                  ->references('id')
                  ->on('comments')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('posts', 'accepted_answer_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropForeign('posts_accepted_answer_fk');
            });
        }
        
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });

        Schema::dropIfExists('comments');
    }
};