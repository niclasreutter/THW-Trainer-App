<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_match_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_question_id')->constrained('extra_questions')->cascadeOnDelete();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['extra_question_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_match_categories');
    }
};
