<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('target_id'); 
            $table->string('target_type', 20); 
            $table->integer('vote_type');   
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_id', 'target_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};