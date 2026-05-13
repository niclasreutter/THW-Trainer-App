<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lehrgang_question_issues', function (Blueprint $table) {
            $table->foreignId('assignee_id')->nullable()->after('reported_by_user_id')
                ->constrained('users')->nullOnDelete();
            $table->index('assignee_id', 'lehrgang_issues_assignee_idx');
        });

        Schema::table('question_issues', function (Blueprint $table) {
            $table->foreignId('assignee_id')->nullable()->after('reported_by_user_id')
                ->constrained('users')->nullOnDelete();
            $table->index('assignee_id', 'question_issues_assignee_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lehrgang_question_issues', function (Blueprint $table) {
            $table->dropIndex('lehrgang_issues_assignee_idx');
            $table->dropForeign(['assignee_id']);
            $table->dropColumn('assignee_id');
        });

        Schema::table('question_issues', function (Blueprint $table) {
            $table->dropIndex('question_issues_assignee_idx');
            $table->dropForeign(['assignee_id']);
            $table->dropColumn('assignee_id');
        });
    }
};
