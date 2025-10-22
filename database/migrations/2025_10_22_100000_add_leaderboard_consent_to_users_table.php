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
            // Leaderboard Consent (separat von E-Mail Consent)
            $table->boolean('leaderboard_consent')->default(false)->after('email_consent_at');
            $table->timestamp('leaderboard_consent_at')->nullable()->after('leaderboard_consent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['leaderboard_consent', 'leaderboard_consent_at']);
        });
    }
};
