<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ortsverband_lernpools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ortsverband_id')->constrained('ortsverbände')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['ortsverband_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortsverband_lernpools');
    }
};
