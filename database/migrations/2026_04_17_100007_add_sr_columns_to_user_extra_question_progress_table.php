<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_extra_question_progress', function (Blueprint $table) {
            $table->unsignedInteger('review_interval')->default(1)->after('next_review_at');
            $table->decimal('easiness_factor', 4, 2)->default(2.50)->after('review_interval');
            $table->unsignedInteger('repetition_count')->default(0)->after('easiness_factor');
        });
    }

    public function down(): void
    {
        Schema::table('user_extra_question_progress', function (Blueprint $table) {
            $table->dropColumn(['review_interval', 'easiness_factor', 'repetition_count']);
        });
    }
};
