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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_seed')->nullable()->after('name');
        });

        // Set default seed for existing users: id + name
        \App\Models\User::query()->each(function ($user) {
            $user->update(['avatar_seed' => $user->id . $user->name]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_seed');
        });
    }
};
