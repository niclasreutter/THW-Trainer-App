<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_question_progress', function (Blueprint $table) {
            $table->timestamp('next_review_at')->nullable()->after('last_answered_at');
            $table->integer('review_interval')->default(0)->after('next_review_at'); // in Tagen
            $table->decimal('easiness_factor', 3, 1)->default(2.5)->after('review_interval'); // SM-2 EF
            $table->integer('repetition_count')->default(0)->after('easiness_factor');

            $table->index('next_review_at');
            $table->index(['user_id', 'next_review_at']);
        });
    }

    public function down(): void
    {
        Schema::table('user_question_progress', function (Blueprint $table) {
            $table->dropIndex(['next_review_at']);
            $table->dropIndex(['user_id', 'next_review_at']);
            $table->dropColumn(['next_review_at', 'review_interval', 'easiness_factor', 'repetition_count']);
        });
    }
};
