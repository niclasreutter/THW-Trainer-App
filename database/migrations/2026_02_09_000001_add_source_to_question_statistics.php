<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('question_statistics', function (Blueprint $table) {
            $table->string('source', 20)->nullable()->after('is_correct')
                ->comment('exam or practice');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('question_statistics', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }
};
