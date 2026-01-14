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
        $achievements = [
            // Erste Schritte
            [
                'key' => 'first_question',
                'title' => '🌟 Erste Schritte',
                'description' => 'Erste Frage beantwortet',
                'icon' => '🎯',
                'category' => 'questions',
                'requirement_value' => 1,
                'sort_order' => 1,
            ],

            // Streak Achievements
            [
                'key' => 'streak_3',
                'title' => '🔥 Feuer entfacht',
                'description' => '3 Tage in Folge gelernt',
                'icon' => '🔥',
                'category' => 'streak',
                'requirement_value' => 3,
                'sort_order' => 10,
            ],
            [
                'key' => 'streak_7',
                'title' => '🚀 Durchstarter',
                'description' => '7 Tage in Folge gelernt',
                'icon' => '🚀',
                'category' => 'streak',
                'requirement_value' => 7,
                'sort_order' => 11,
            ],
            [
                'key' => 'streak_30',
                'title' => '👑 Lernkönig',
                'description' => '30 Tage in Folge gelernt',
                'icon' => '👑',
                'category' => 'streak',
                'requirement_value' => 30,
                'sort_order' => 12,
            ],

            // Fragen Achievements
            [
                'key' => 'questions_50',
                'title' => '📚 Wissensdurst',
                'description' => '50 Fragen beantwortet',
                'icon' => '📚',
                'category' => 'questions',
                'requirement_value' => 50,
                'sort_order' => 20,
            ],
            [
                'key' => 'questions_100',
                'title' => '🧠 Denker',
                'description' => '100 Fragen beantwortet',
                'icon' => '🧠',
                'category' => 'questions',
                'requirement_value' => 100,
                'sort_order' => 21,
            ],
            [
                'key' => 'questions_500',
                'title' => '🎓 Experte',
                'description' => '500 Fragen beantwortet',
                'icon' => '🎓',
                'category' => 'questions',
                'requirement_value' => 500,
                'sort_order' => 22,
            ],

            // Prüfungs Achievements
            [
                'key' => 'exam_first',
                'title' => '🏆 Erste Prüfung',
                'description' => 'Erste Prüfung bestanden',
                'icon' => '🏆',
                'category' => 'exam',
                'requirement_value' => 1,
                'sort_order' => 30,
            ],
            [
                'key' => 'exam_perfect',
                'title' => '💎 Perfektionist',
                'description' => 'Prüfung mit 100% bestanden',
                'icon' => '💎',
                'category' => 'exam',
                'requirement_value' => 100,
                'sort_order' => 31,
            ],

            // Sonstige Achievements
            [
                'key' => 'speed_demon',
                'title' => '⚡ Blitzschnell',
                'description' => '20 Fragen an einem Tag',
                'icon' => '⚡',
                'category' => 'general',
                'requirement_value' => 20,
                'sort_order' => 40,
            ],
            [
                'key' => 'section_master',
                'title' => '🎯 Abschnittsmeister',
                'description' => 'Alle Fragen eines Abschnitts gelöst',
                'icon' => '🎯',
                'category' => 'general',
                'requirement_value' => null,
                'sort_order' => 41,
            ],

            // Level Achievements
            [
                'key' => 'level_5',
                'title' => '⭐ Aufsteiger',
                'description' => 'Level 5 erreicht',
                'icon' => '⭐',
                'category' => 'level',
                'requirement_value' => 5,
                'sort_order' => 50,
            ],
            [
                'key' => 'level_10',
                'title' => '🌟 Meister',
                'description' => 'Level 10 erreicht',
                'icon' => '🌟',
                'category' => 'level',
                'requirement_value' => 10,
                'sort_order' => 51,
            ],
            [
                'key' => 'level_15',
                'title' => '💫 Experte',
                'description' => 'Level 15 erreicht',
                'icon' => '💫',
                'category' => 'level',
                'requirement_value' => 15,
                'sort_order' => 52,
            ],
            [
                'key' => 'level_20',
                'title' => '🏅 Legende',
                'description' => 'Level 20 erreicht',
                'icon' => '🏅',
                'category' => 'level',
                'requirement_value' => 20,
                'sort_order' => 53,
            ],
        ];

        $now = now();
        foreach ($achievements as &$achievement) {
            $achievement['created_at'] = $now;
            $achievement['updated_at'] = $now;
        }

        DB::table('achievements')->insert($achievements);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('achievements')->truncate();
    }
};
