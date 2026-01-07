<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ortsverband_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ortsverband_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['ausbildungsbeauftragter', 'member'])->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->foreign('ortsverband_id')->references('id')->on('ortsverbände')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['ortsverband_id', 'user_id'], 'unique_membership');
            $table->index('user_id');
            $table->index('ortsverband_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortsverband_members');
    }
};
