<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_question_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_question_id')->constrained('extra_questions')->onDelete('cascade');
            $table->integer('report_count')->default(1);
            $table->text('latest_message')->nullable();
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->enum('status', ['open', 'in_review', 'resolved', 'rejected'])->default('open');
            $table->timestamps();

            $table->index('status');
            $table->unique('extra_question_id');
        });

        Schema::create('extra_question_issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_question_issue_id')->constrained('extra_question_issues')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['extra_question_issue_id', 'created_at'], 'eq_issue_reports_issue_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_question_issue_reports');
        Schema::dropIfExists('extra_question_issues');
    }
};
