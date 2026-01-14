<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hole alle Achievements aus der DB
        $achievements = DB::table('achievements')->get()->keyBy('key');

        // Hole alle User mit Achievements
        $users = DB::table('users')
            ->whereNotNull('achievements')
            ->get();

        $now = now();
        foreach ($users as $user) {
            // Parse JSON achievements
            $userAchievements = json_decode($user->achievements, true);

            if (!is_array($userAchievements) || empty($userAchievements)) {
                continue;
            }

            foreach ($userAchievements as $achievementKey) {
                if (!isset($achievements[$achievementKey])) {
                    \Log::warning("Achievement key '{$achievementKey}' not found in database for user {$user->id}");
                    continue;
                }

                $achievement = $achievements[$achievementKey];

                // Insert in user_achievements Tabelle
                try {
                    DB::table('user_achievements')->insert([
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                        'unlocked_at' => $now, // Wir wissen nicht wann es freigeschaltet wurde, also nehmen wir jetzt
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } catch (\Exception $e) {
                    // Ignoriere Duplikate
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        \Log::error("Failed to migrate achievement {$achievementKey} for user {$user->id}: " . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user_achievements')->truncate();
    }
};
