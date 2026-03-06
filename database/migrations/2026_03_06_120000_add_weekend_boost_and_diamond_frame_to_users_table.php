<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('weekend_boost_until')->nullable()->after('double_xp_until');
            $table->string('profile_frame_type', 20)->nullable()->after('profile_frame_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['weekend_boost_until', 'profile_frame_type']);
        });
    }
};
