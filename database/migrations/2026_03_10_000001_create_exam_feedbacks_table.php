<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->boolean('passed')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('publish_mode', ['anonymous', 'named'])->default('anonymous');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('exam_goodluck_sent_at')->nullable()->after('exam_date');
            $table->timestamp('exam_feedback_request_sent_at')->nullable()->after('exam_goodluck_sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_feedbacks');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['exam_goodluck_sent_at', 'exam_feedback_request_sent_at']);
        });
    }
};
