<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix: Migration 2026_03_05_000003 hat streak_freezes_available auf 0 gesetzt,
     * aber streak_freezes_used nicht zurückgesetzt. Dadurch können gekaufte Freezes
     * nicht angezeigt werden, weil used >= available bleibt.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereColumn('streak_freezes_used', '>', 'streak_freezes_available')
            ->update(['streak_freezes_used' => 0]);
    }

    public function down(): void
    {
        // Nicht rückgängig machbar
    }
};
