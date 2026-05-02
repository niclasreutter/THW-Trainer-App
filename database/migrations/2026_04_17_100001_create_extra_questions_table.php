<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_questions', function (Blueprint $table) {
            $table->id();
            $table->enum('typ', ['matching', 'image_name', 'image_select']);
            $table->string('lernabschnitt');
            $table->text('frage');
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index('typ');
            $table->index('lernabschnitt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_questions');
    }
};
