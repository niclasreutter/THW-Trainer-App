<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reduziert Ligen von 8 auf 5: Smaragd/Rubin/Saphir-User werden nach Platin verschoben.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereIn('league', ['smaragd', 'rubin', 'saphir'])
            ->update(['league' => 'platin']);
    }

    public function down(): void
    {
        // Nicht reversibel - User bleiben in Platin
    }
};
