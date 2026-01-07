<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ortsverband_invitation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invitation_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('used_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();

            $table->foreign('invitation_id')->references('id')->on('ortsverband_invitations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortsverband_invitation_logs');
    }
};
