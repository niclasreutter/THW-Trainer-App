<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_lehrgang_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lehrgang_question_id')->constrained('lehrgaenge_questions')->onDelete('cascade');
            $table->integer('consecutive_correct')->default(0)->comment('Wie oft hintereinander richtig beantwortet');
            $table->boolean('solved')->default(false);
            $table->boolean('failed')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'lehrgang_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_lehrgang_progress');
    }
};
