<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('lehrgaenge', 'ziel_punkte')) {
            Schema::table('lehrgaenge', function (Blueprint $table) {
                $table->dropColumn('ziel_punkte');
            });
        }
    }

    public function down(): void
    {
        Schema::table('lehrgaenge', function (Blueprint $table) {
            $table->integer('ziel_punkte')->default(0)->comment('Ziel-Punkte zum Bestehen');
        });
    }
};
