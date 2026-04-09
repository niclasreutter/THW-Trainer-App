<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating_overall');
            $table->tinyInteger('rating_usability');
            $table->tinyInteger('rating_design');
            $table->string('found_via');
            $table->text('feedback_general')->nullable();
            $table->text('feedback_wishes')->nullable();
            $table->text('feedback_changes')->nullable();
            $table->string('hermine_interest');
            $table->string('publish_mode');
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_given_at')->nullable();
            $table->timestamps();

            $table->unique(['survey_id', 'user_id']);
            $table->index('survey_id');
            $table->index('user_id');
            $table->index('publish_mode');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('surveys');
    }
};
