<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->after('loesung');
            $table->enum('status', ['draft', 'review', 'published'])->default('published')->after('difficulty');
            $table->text('loesungsweg')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'status', 'loesungsweg']);
        });
    }
};
