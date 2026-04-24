<?php

namespace Database\Seeders;

use App\Models\ExamStatistic;
use App\Models\Ortsverband;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Models\UserTrainingProgress;
use App\Services\TrainingProgressService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AusbildungsfortschrittDemoSeeder extends Seeder
{
    private const EXAM_SIZE = 40;
    private const EXAM_PASS_THRESHOLD = 32;

    public function run(): void
    {
        $ausbilder = User::updateOrCreate(
            ['email' => 'ausbilder@example.com'],
            [
                'name' => 'Anja Ausbilder',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'useroll' => 'user',
            ],
        );

        $ortsverband = Ortsverband::firstOrCreate(
            ['created_by' => $ausbilder->id],
            [
                'name' => 'OV Demo-Stadt',
                'description' => 'Demo-Ortsverband für Ausbildungsfortschritt',
            ],
        );

        DB::table('ortsverband_members')->updateOrInsert(
            ['ortsverband_id' => $ortsverband->id, 'user_id' => $ausbilder->id],
            ['role' => 'ausbildungsbeauftragter', 'joined_at' => now(), 'updated_at' => now(), 'created_at' => now()],
        );

        $allItemKeys = TrainingProgressService::validItemKeys();
        $questionIds = Question::orderBy('id')->pluck('id')->all();
        $totalQuestions = count($questionIds);

        $members = [
            [
                'email' => 'vollstaendig@example.com',
                'name' => 'Max Vollständig',
                'training_keys' => $allItemKeys,
                'mastered_ratio' => 1.0,
                'partial_ratio' => 0.0,
                'passed_exams' => 10,
                'failed_exams' => 0,
            ],
            [
                'email' => 'neuling@example.com',
                'name' => 'Lukas Neuling',
                'training_keys' => [],
                'mastered_ratio' => 0.0,
                'partial_ratio' => 0.0,
                'passed_exams' => 0,
                'failed_exams' => 0,
            ],
            [
                'email' => 'fortgeschritten@example.com',
                'name' => 'Anna Fortgeschritten',
                'training_keys' => array_slice($allItemKeys, 0, (int) round(count($allItemKeys) * 0.7)),
                'mastered_ratio' => 0.7,
                'partial_ratio' => 0.15,
                'passed_exams' => 5,
                'failed_exams' => 1,
            ],
            [
                'email' => 'anfaenger@example.com',
                'name' => 'Ben Anfänger',
                'training_keys' => array_slice($allItemKeys, 0, (int) round(count($allItemKeys) * 0.3)),
                'mastered_ratio' => 0.3,
                'partial_ratio' => 0.2,
                'passed_exams' => 1,
                'failed_exams' => 2,
            ],
        ];

        foreach ($members as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'useroll' => 'user',
                ],
            );

            DB::table('ortsverband_members')->updateOrInsert(
                ['ortsverband_id' => $ortsverband->id, 'user_id' => $user->id],
                ['role' => 'member', 'joined_at' => now(), 'updated_at' => now(), 'created_at' => now()],
            );

            $this->seedTrainingProgress($user, $data['training_keys']);
            $this->seedQuestionAndExamProgress($user, $questionIds, $totalQuestions, $data);
        }
    }

    private function seedTrainingProgress(User $user, array $keys): void
    {
        UserTrainingProgress::where('user_id', $user->id)->delete();

        if (empty($keys)) {
            return;
        }

        $now = now();
        $rows = array_map(fn ($key) => [
            'user_id' => $user->id,
            'item_key' => $key,
            'completed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], $keys);

        UserTrainingProgress::insert($rows);
    }

    private function seedQuestionAndExamProgress(User $user, array $questionIds, int $totalQuestions, array $data): void
    {
        UserQuestionProgress::where('user_id', $user->id)->delete();
        QuestionStatistic::where('user_id', $user->id)->delete();
        ExamStatistic::where('user_id', $user->id)->delete();

        $user->exam_passed_count = 0;
        $user->solved_questions = [];
        $user->exam_failed_questions = [];
        $user->save();

        if ($totalQuestions === 0) {
            return;
        }

        $masteredCount = (int) round($totalQuestions * $data['mastered_ratio']);
        $partialCount = (int) round($totalQuestions * $data['partial_ratio']);
        $partialCount = min($partialCount, $totalQuestions - $masteredCount);

        $masteredIds = array_slice($questionIds, 0, $masteredCount);
        $partialIds = array_slice($questionIds, $masteredCount, $partialCount);

        $now = now();
        $progressRows = [];

        foreach ($masteredIds as $qid) {
            $progressRows[] = [
                'user_id' => $user->id,
                'question_id' => $qid,
                'consecutive_correct' => UserQuestionProgress::MASTERY_THRESHOLD,
                'last_answered_at' => $now,
                'next_review_at' => $now->copy()->addDays(7),
                'review_interval' => 7,
                'easiness_factor' => 2.5,
                'repetition_count' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($partialIds as $i => $qid) {
            $level = ($i % 2) + 1;
            $progressRows[] = [
                'user_id' => $user->id,
                'question_id' => $qid,
                'consecutive_correct' => $level,
                'last_answered_at' => $now,
                'next_review_at' => $now->copy()->addDays($level),
                'review_interval' => $level,
                'easiness_factor' => 2.5,
                'repetition_count' => $level,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($progressRows)) {
            foreach (array_chunk($progressRows, 200) as $chunk) {
                UserQuestionProgress::insert($chunk);
            }
        }

        $user->solved_questions = $masteredIds;
        $user->save();

        $this->seedExams($user, $questionIds, $data['passed_exams'], $data['failed_exams']);

        $practicePool = array_merge($masteredIds, $partialIds);
        if (!empty($practicePool)) {
            $this->seedPracticeStatistics($user, $practicePool, $data['mastered_ratio']);
        }
    }

    private function seedExams(User $user, array $questionIds, int $passedCount, int $failedCount): void
    {
        $totalExams = $passedCount + $failedCount;
        if ($totalExams === 0) {
            return;
        }

        $baseTime = Carbon::now()->subDays($totalExams);
        $sequence = array_merge(
            array_fill(0, $failedCount, false),
            array_fill(0, $passedCount, true),
        );

        $lastFailedIds = [];

        foreach ($sequence as $i => $isPassed) {
            $examTime = $baseTime->copy()->addDays($i)->addHours(18);

            $ids = $questionIds;
            shuffle($ids);
            $examIds = array_slice($ids, 0, min(self::EXAM_SIZE, count($ids)));

            if ($isPassed) {
                $correctCount = rand(self::EXAM_PASS_THRESHOLD, count($examIds));
            } else {
                $correctCount = rand(15, self::EXAM_PASS_THRESHOLD - 1);
            }

            $exam = ExamStatistic::create([
                'user_id' => $user->id,
                'is_passed' => $isPassed,
                'correct_answers' => $correctCount,
            ]);
            $exam->created_at = $examTime;
            $exam->updated_at = $examTime;
            $exam->save();

            $correctSet = array_flip(array_slice($examIds, 0, $correctCount));
            $failedInExam = [];
            $statRows = [];
            foreach ($examIds as $qid) {
                $correct = isset($correctSet[$qid]);
                if (!$correct) {
                    $failedInExam[] = $qid;
                }
                $statRows[] = [
                    'question_id' => $qid,
                    'user_id' => $user->id,
                    'is_correct' => $correct,
                    'source' => 'exam',
                    'exam_statistic_id' => $exam->id,
                    'created_at' => $examTime,
                    'updated_at' => $examTime,
                ];
            }
            QuestionStatistic::insert($statRows);

            if (!$isPassed) {
                $lastFailedIds = $failedInExam;
            }
        }

        $trailingPassedStreak = 0;
        foreach (array_reverse($sequence) as $isPassed) {
            if ($isPassed) {
                $trailingPassedStreak++;
            } else {
                break;
            }
        }

        $user->exam_passed_count = $trailingPassedStreak;
        $user->exam_failed_questions = $trailingPassedStreak === count($sequence) ? [] : $lastFailedIds;
        $user->save();
    }

    private function seedPracticeStatistics(User $user, array $questionIds, float $masteredRatio): void
    {
        $sampleSize = min(count($questionIds), 50);
        $sample = array_slice($questionIds, 0, $sampleSize);

        $correctProbability = max(0.5, min(0.95, 0.5 + $masteredRatio * 0.45));
        $rows = [];
        $start = Carbon::now()->subDays(30);

        foreach ($sample as $i => $qid) {
            $isCorrect = (mt_rand(1, 100) / 100) <= $correctProbability;
            $ts = $start->copy()->addHours($i * 4);
            $rows[] = [
                'question_id' => $qid,
                'user_id' => $user->id,
                'is_correct' => $isCorrect,
                'source' => 'practice',
                'exam_statistic_id' => null,
                'created_at' => $ts,
                'updated_at' => $ts,
            ];
        }

        if (!empty($rows)) {
            foreach (array_chunk($rows, 200) as $chunk) {
                QuestionStatistic::insert($chunk);
            }
        }
    }
}
