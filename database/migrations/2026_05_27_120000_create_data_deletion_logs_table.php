<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_deletion_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('deletion_type', 50)->default('user_account');
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamps();

            $table->index('deleted_at');
            $table->index('deletion_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_deletion_logs');
    }
};
