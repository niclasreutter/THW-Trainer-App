<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('best_combo')->default(0)->after('league_change');
            $table->timestamp('last_combo_bonus_at')->nullable()->after('best_combo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['best_combo', 'last_combo_bonus_at']);
        });
    }
};
