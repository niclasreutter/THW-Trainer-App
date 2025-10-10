<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_passed');
            $table->integer('correct_answers');
            $table->timestamps();

            // Index für bessere Performance bei Statistik-Abfragen
            $table->index(['user_id', 'is_passed']);
            $table->index('created_at');
            $table->index('is_passed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_statistics');
    }
};
