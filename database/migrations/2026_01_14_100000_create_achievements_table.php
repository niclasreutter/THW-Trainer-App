<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // z.B. 'first_question', 'streak_3'
            $table->string('title'); // z.B. '🌟 Erste Schritte'
            $table->text('description'); // z.B. 'Erste Frage beantwortet'
            $table->string('icon')->nullable(); // z.B. '🎯'
            $table->string('category')->default('general'); // general, streak, questions, exam, level
            $table->integer('requirement_value')->nullable(); // z.B. 50 für questions_50
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
