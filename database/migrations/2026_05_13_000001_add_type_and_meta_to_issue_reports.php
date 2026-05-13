<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lehrgang_question_issue_reports', function (Blueprint $table) {
            $table->string('type', 20)->default('report')->after('user_id');
            $table->json('meta')->nullable()->after('message');
            $table->index('type', 'lehrgang_issue_reports_type_idx');
        });

        Schema::table('question_issue_reports', function (Blueprint $table) {
            $table->string('type', 20)->default('report')->after('user_id');
            $table->json('meta')->nullable()->after('message');
            $table->index('type', 'question_issue_reports_type_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lehrgang_question_issue_reports', function (Blueprint $table) {
            $table->dropIndex('lehrgang_issue_reports_type_idx');
            $table->dropColumn(['type', 'meta']);
        });

        Schema::table('question_issue_reports', function (Blueprint $table) {
            $table->dropIndex('question_issue_reports_type_idx');
            $table->dropColumn(['type', 'meta']);
        });
    }
};
