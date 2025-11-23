<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lehrgaenge_lernabschnitte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lehrgang_id')->constrained('lehrgaenge')->onDelete('cascade');
            $table->integer('lernabschnitt_nr');
            $table->string('lernabschnitt');
            $table->timestamps();
            
            $table->unique(['lehrgang_id', 'lernabschnitt_nr']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lehrgaenge_lernabschnitte');
    }
};
