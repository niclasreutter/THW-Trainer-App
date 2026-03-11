<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ortsverband_id')->nullable()->constrained('ortsverbände')->cascadeOnDelete();
            $table->enum('scope', ['global', 'ortsverband']);
            $table->enum('question_source', ['all', 'sections', 'lernpool']);
            $table->json('question_sections')->nullable();
            $table->foreignId('lernpool_id')->nullable()->constrained('ortsverband_lernpools')->nullOnDelete();
            $table->enum('schedule_type', ['once', 'recurring']);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->json('recurrence_days')->nullable();
            $table->time('recurrence_start_time')->nullable();
            $table->time('recurrence_end_time')->nullable();
            $table->date('recurrence_valid_from')->nullable();
            $table->date('recurrence_valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['scope', 'is_active']);
            $table->index('ortsverband_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_sessions');
    }
};
