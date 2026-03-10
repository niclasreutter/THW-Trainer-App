<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lootboxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 20); // gold, silber, bronze
            $table->string('reward_type', 30)->nullable(); // xp, streak_freeze, double_xp
            $table->integer('reward_amount')->nullable();
            $table->boolean('opened')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lootboxes');
    }
};
