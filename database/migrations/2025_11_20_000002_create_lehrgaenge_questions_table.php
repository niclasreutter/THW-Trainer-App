<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lehrgaenge_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lehrgang_id')->constrained('lehrgaenge')->onDelete('cascade');
            $table->string('lernabschnitt');
            $table->integer('nummer');
            $table->text('frage');
            $table->string('antwort_a');
            $table->string('antwort_b');
            $table->string('antwort_c');
            $table->string('loesung'); // z.B. "A,C"
            $table->timestamps();
            
            $table->index(['lehrgang_id', 'lernabschnitt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lehrgaenge_questions');
    }
};
