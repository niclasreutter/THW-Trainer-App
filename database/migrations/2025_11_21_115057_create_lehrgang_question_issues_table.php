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
        Schema::create('lehrgang_question_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lehrgang_question_id')->constrained('lehrgaenge_questions')->onDelete('cascade');
            $table->integer('report_count')->default(1); // Wie oft wurde diese Frage gemeldet
            $table->text('latest_message')->nullable(); // Letzte Nachricht des Nutzers
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Benutzer der zuletzt gemeldet hat
            $table->text('admin_notes')->nullable(); // Admin-Notizen/Bearbeitung
            $table->enum('status', ['open', 'in_review', 'resolved', 'rejected'])->default('open'); // Status der Meldung
            $table->timestamps();
            
            // Index für schnelle Abfragen
            $table->index(['lehrgang_question_id']);
            $table->index(['status']);
            $table->unique('lehrgang_question_id'); // Pro Frage maximal ein Eintrag
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lehrgang_question_issues');
    }
};
