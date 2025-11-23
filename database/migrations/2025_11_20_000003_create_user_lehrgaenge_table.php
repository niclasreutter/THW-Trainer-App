<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_lehrgaenge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lehrgang_id')->constrained('lehrgaenge')->onDelete('cascade');
            $table->integer('punkte')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'lehrgang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_lehrgaenge');
    }
};
