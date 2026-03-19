<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('avatar_seed', 'avatar_path');
        });

        // Convert existing seeds to full avatar paths
        \App\Models\User::query()->each(function ($user) {
            $seed = urlencode($user->id . str_replace(' ', '', $user->name));
            $user->update([
                'avatar_path' => 'avataaars-neutral/svg?radius=50&seed=' . $seed . '&eyes=default,happy&mouth=default,smile',
            ]);
        });
    }

    public function down(): void
    {
        // Convert paths back to seeds
        \App\Models\User::query()->each(function ($user) {
            $user->update([
                'avatar_path' => $user->id . $user->name,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('avatar_path', 'avatar_seed');
        });
    }
};
