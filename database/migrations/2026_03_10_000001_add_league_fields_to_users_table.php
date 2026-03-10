<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('league')->default('bronze')->after('weekly_points');
            $table->timestamp('league_updated_at')->nullable()->after('league');
            $table->string('previous_league')->nullable()->after('league_updated_at');
            $table->string('league_change')->nullable()->after('previous_league');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['league', 'league_updated_at', 'previous_league', 'league_change']);
        });
    }
};
