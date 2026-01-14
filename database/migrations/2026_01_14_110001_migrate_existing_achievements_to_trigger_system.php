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
        // Mapping von achievement keys zu trigger types
        $triggerMappings = [
            // Question Achievements
            'first_question' => [
                'trigger_type' => 'question_count',
                'trigger_config' => json_encode(['value' => 1]),
            ],
            'questions_50' => [
                'trigger_type' => 'question_count',
                'trigger_config' => json_encode(['value' => 50]),
            ],
            'questions_100' => [
                'trigger_type' => 'question_count',
                'trigger_config' => json_encode(['value' => 100]),
            ],
            'questions_500' => [
                'trigger_type' => 'question_count',
                'trigger_config' => json_encode(['value' => 500]),
            ],

            // Streak Achievements
            'streak_3' => [
                'trigger_type' => 'streak_days',
                'trigger_config' => json_encode(['value' => 3]),
            ],
            'streak_7' => [
                'trigger_type' => 'streak_days',
                'trigger_config' => json_encode(['value' => 7]),
            ],
            'streak_30' => [
                'trigger_type' => 'streak_days',
                'trigger_config' => json_encode(['value' => 30]),
            ],

            // Exam Achievements
            'exam_first' => [
                'trigger_type' => 'exam_passed_count',
                'trigger_config' => json_encode(['value' => 1]),
            ],
            'exam_perfect' => [
                'trigger_type' => 'exam_perfect',
                'trigger_config' => json_encode([]),
            ],

            // Daily Achievement
            'speed_demon' => [
                'trigger_type' => 'daily_questions',
                'trigger_config' => json_encode(['value' => 20]),
            ],

            // Section Achievement
            'section_master' => [
                'trigger_type' => 'section_complete',
                'trigger_config' => json_encode(['any_section' => true]),
            ],

            // Level Achievements
            'level_5' => [
                'trigger_type' => 'level_reached',
                'trigger_config' => json_encode(['value' => 5]),
            ],
            'level_10' => [
                'trigger_type' => 'level_reached',
                'trigger_config' => json_encode(['value' => 10]),
            ],
            'level_15' => [
                'trigger_type' => 'level_reached',
                'trigger_config' => json_encode(['value' => 15]),
            ],
            'level_20' => [
                'trigger_type' => 'level_reached',
                'trigger_config' => json_encode(['value' => 20]),
            ],
        ];

        foreach ($triggerMappings as $key => $config) {
            DB::table('achievements')
                ->where('key', $key)
                ->update([
                    'trigger_type' => $config['trigger_type'],
                    'trigger_config' => $config['trigger_config'],
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Setze alle trigger_types zurück auf default
        DB::table('achievements')->update([
            'trigger_type' => 'question_count',
            'trigger_config' => null,
        ]);
    }
};
