<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('question_statistics', function (Blueprint $table) {
            $table->foreignId('exam_statistic_id')->nullable()->after('source')
                ->constrained('exam_statistics')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('question_statistics', function (Blueprint $table) {
            $table->dropForeign(['exam_statistic_id']);
            $table->dropColumn('exam_statistic_id');
        });
    }
};
