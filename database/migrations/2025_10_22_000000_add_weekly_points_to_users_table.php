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
            // Wöchentliche Punkte (wird jeden Montag zurückgesetzt)
            $table->integer('weekly_points')->default(0)->after('points');
            
            // Timestamp wann die Woche gestartet wurde
            $table->timestamp('weekly_reset_at')->nullable()->after('weekly_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['weekly_points', 'weekly_reset_at']);
        });
    }
};
