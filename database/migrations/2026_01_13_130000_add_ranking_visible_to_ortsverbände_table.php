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
        Schema::table('ortsverbände', function (Blueprint $table) {
            $table->boolean('ranking_visible')->default(false)->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ortsverbände', function (Blueprint $table) {
            $table->dropColumn('ranking_visible');
        });
    }
};
