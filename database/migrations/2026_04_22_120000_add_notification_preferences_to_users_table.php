<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'push_consent')) {
                $table->boolean('push_consent')->default(false)->after('email_consent_at');
            }
            if (!Schema::hasColumn('users', 'push_consent_at')) {
                $table->timestamp('push_consent_at')->nullable()->after('push_consent');
            }

            if (!Schema::hasColumn('users', 'notify_daily_reminder_email')) {
                $table->boolean('notify_daily_reminder_email')->default(false)->after('push_consent_at');
            }
            if (!Schema::hasColumn('users', 'notify_daily_reminder_push')) {
                $table->boolean('notify_daily_reminder_push')->default(false)->after('notify_daily_reminder_email');
            }
            if (!Schema::hasColumn('users', 'notify_spaced_repetition_email')) {
                $table->boolean('notify_spaced_repetition_email')->default(false)->after('notify_daily_reminder_push');
            }
            if (!Schema::hasColumn('users', 'notify_spaced_repetition_push')) {
                $table->boolean('notify_spaced_repetition_push')->default(false)->after('notify_spaced_repetition_email');
            }
            if (!Schema::hasColumn('users', 'notify_league_updates_email')) {
                $table->boolean('notify_league_updates_email')->default(false)->after('notify_spaced_repetition_push');
            }
            if (!Schema::hasColumn('users', 'notify_league_updates_push')) {
                $table->boolean('notify_league_updates_push')->default(false)->after('notify_league_updates_email');
            }
        });

        // Wer bereits E-Mail-Zustimmung erteilt hat, bekommt alle Email-Sub-Preferences aktiviert
        \DB::table('users')
            ->where('email_consent', true)
            ->update([
                'notify_daily_reminder_email' => true,
                'notify_spaced_repetition_email' => true,
                'notify_league_updates_email' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'notify_league_updates_push',
                'notify_league_updates_email',
                'notify_spaced_repetition_push',
                'notify_spaced_repetition_email',
                'notify_daily_reminder_push',
                'notify_daily_reminder_email',
                'push_consent_at',
                'push_consent',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
