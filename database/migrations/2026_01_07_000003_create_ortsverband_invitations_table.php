<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ortsverband_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ortsverband_id');
            $table->string('code', 32)->unique();
            $table->unsignedBigInteger('created_by');
            $table->integer('max_uses')->nullable();
            $table->integer('current_uses')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('ortsverband_id')->references('id')->on('ortsverbände')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('code');
            $table->index('ortsverband_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortsverband_invitations');
    }
};
