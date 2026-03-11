<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_session_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_session_id')->constrained('learning_sessions')->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', ['scheduled', 'active', 'completed'])->default('scheduled');
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
            $table->index('learning_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_session_instances');
    }
};
