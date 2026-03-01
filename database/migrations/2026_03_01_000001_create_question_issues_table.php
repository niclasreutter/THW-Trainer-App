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
        Schema::create('question_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('report_count')->default(1);
            $table->text('latest_message')->nullable();
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->enum('status', ['open', 'in_review', 'resolved', 'rejected'])->default('open');
            $table->timestamps();

            $table->index(['question_id']);
            $table->index(['status']);
            $table->unique('question_id');
        });

        Schema::create('question_issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_issue_id')->constrained('question_issues')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['question_issue_id', 'created_at'], 'q_issue_reports_issue_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_issue_reports');
        Schema::dropIfExists('question_issues');
    }
};
