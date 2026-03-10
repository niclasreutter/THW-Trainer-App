<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_quests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('quest_date');
            $table->string('quest_type');
            $table->json('quest_params')->nullable();
            $table->string('quest_title');
            $table->integer('target_value');
            $table->integer('current_value')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('xp_reward');
            $table->tinyInteger('slot');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'quest_date', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quests');
    }
};
