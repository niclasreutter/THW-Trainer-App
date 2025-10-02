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
        Schema::table('users', function (Blueprint $table) {
            // Prüfe ob email_consent_at bereits existiert
            if (!Schema::hasColumn('users', 'email_consent_at')) {
                $table->timestamp('email_consent_at')->nullable()->after('email_consent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_consent_at')) {
                $table->dropColumn('email_consent_at');
            }
        });
    }
};
