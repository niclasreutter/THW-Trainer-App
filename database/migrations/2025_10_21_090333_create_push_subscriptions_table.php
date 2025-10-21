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
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('endpoint', 500);
            $table->string('public_key');
            $table->string('auth_token');
            $table->string('content_encoding')->default('aesgcm');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            // Ein User kann mehrere Subscriptions haben (verschiedene Geräte)
            $table->unique(['user_id', 'endpoint']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
