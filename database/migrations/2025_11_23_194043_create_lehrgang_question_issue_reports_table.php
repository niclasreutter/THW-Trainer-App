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
        Schema::create('lehrgang_question_issue_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lehrgang_question_issue_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('message')->nullable(); // Nachricht des Nutzers
            $table->timestamps();
            
            // Foreign Key mit kürzerem Namen
            $table->foreign('lehrgang_question_issue_id', 'lehrgang_issue_reports_issue_fk')
                  ->references('id')
                  ->on('lehrgang_question_issues')
                  ->onDelete('cascade');
            
            // Index für schnelle Abfragen mit kürzerem Namen
            $table->index(['lehrgang_question_issue_id', 'created_at'], 'issue_reports_issue_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lehrgang_question_issue_reports');
    }
};
