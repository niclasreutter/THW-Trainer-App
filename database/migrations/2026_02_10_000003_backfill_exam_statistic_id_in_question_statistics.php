<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('app:backfill-exam-links');
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('question_statistics')
            ->whereNotNull('exam_statistic_id')
            ->update(['exam_statistic_id' => null]);
    }
};
