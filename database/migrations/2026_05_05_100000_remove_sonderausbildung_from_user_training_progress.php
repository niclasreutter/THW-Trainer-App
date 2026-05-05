<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('user_training_progress')
            ->where('item_key', 'zusatz.sonder')
            ->delete();
    }

    public function down(): void
    {
        // No-op: deleted progress entries cannot be restored.
    }
};
