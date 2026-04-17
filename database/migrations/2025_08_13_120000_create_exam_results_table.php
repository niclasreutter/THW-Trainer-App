<?php

use Illuminate\Database\Migrations\Migration;

// Legacy-Stub: Die ursprüngliche Migration war leer (0 Byte committet).
// RefreshDatabase in Tests schlug dadurch fehl ("Class CreateExamResultsTable not found").
// Wir liefern hier eine No-Op-Migration, damit der Migrator nichts bricht.
return new class extends Migration
{
    public function up(): void
    {
        // Intentionally empty — table is created elsewhere or not needed.
    }

    public function down(): void
    {
        // No-op.
    }
};
