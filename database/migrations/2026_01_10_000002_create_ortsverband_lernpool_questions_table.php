<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ortsverband_lernpool_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lernpool_id')->constrained('ortsverband_lernpools')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('lernabschnitt')->nullable();
            $table->integer('nummer')->nullable();
            $table->text('frage');
            $table->string('antwort_a');
            $table->string('antwort_b');
            $table->string('antwort_c');
            $table->string('loesung'); // z.B. "A,C"
            $table->timestamps();
            
            $table->index(['lernpool_id', 'lernabschnitt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortsverband_lernpool_questions');
    }
};
