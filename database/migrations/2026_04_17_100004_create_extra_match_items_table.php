<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_match_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_question_id')->constrained('extra_questions')->cascadeOnDelete();
            $table->string('text');
            $table->foreignId('correct_category_id')->constrained('extra_match_categories')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['extra_question_id', 'sort_order']);
            $table->index('correct_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_match_items');
    }
};
