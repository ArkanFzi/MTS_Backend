<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->foreignUuid('reporter_id')->constrained('users')->onDelete('cascade');
            $table->uuid('target_id');
            $table->string('target_type', 20);
            $table->string('reason', 100);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};