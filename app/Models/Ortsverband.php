<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Ortsverband extends Model
{
    protected $table = 'ortsverbände';
    
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'logo',
        'ranking_visible'
    ];

    /**
     * Der Ersteller (Ausbildungsbeauftragter) des Ortsverbands
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alle Mitglieder des Ortsverbands
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ortsverband_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Alle Ausbildungsbeauftragten
     */
    public function ausbildungsbeauftragte(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'ausbildungsbeauftragter');
    }

    /**
     * Nur normale Mitglieder
     */
    public function regularMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'member');
    }

    /**
     * Alle Einladungen
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(OrtsverbandInvitation::class);
    }

    /**
     * Aktive Einladungen
     */
    public function activeInvitations(): HasMany
    {
        return $this->invitations()->where('is_active', true);
    }

    /**
     * Prüft ob ein User Ausbildungsbeauftragter ist
     */
    public function isAusbildungsbeauftragter(User $user): bool
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->wherePivot('role', 'ausbildungsbeauftragter')
                    ->exists();
    }

    /**
     * Prüft ob ein User Mitglied ist
     */
    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Hole Fortschrittsstatistiken aller Mitglieder
     */
    public function getMemberProgress()
    {
        $totalQuestions = \App\Models\Question::count();
        $trainingService = new \App\Services\TrainingProgressService();

        return $this->members->map(function($member) use ($totalQuestions, $trainingService) {
            // Theorie-Fortschritt
            $solvedQuestions = \App\Models\UserQuestionProgress::where('user_id', $member->id)
                              ->where('consecutive_correct', '>=', \App\Models\UserQuestionProgress::MASTERY_THRESHOLD)
                              ->count();

            // Prüfungs-Streak
            $allExams = \App\Models\ExamStatistic::where('user_id', $member->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $examStreak = 0;
            foreach ($allExams as $exam) {
                if ($exam->is_passed) {
                    $examStreak++;
                } else {
                    break;
                }
            }

            $trainingOverview = $trainingService->overview($member);

            return [
                'user' => $member,
                'theory_progress_percent' => $totalQuestions > 0 ? round(($solvedQuestions / $totalQuestions) * 100) : 0,
                'theory_progress_count' => $solvedQuestions,
                'theory_progress_total' => $totalQuestions,
                'training_progress_percent' => $trainingOverview['percent'],
                'training_progress_done' => $trainingOverview['done'],
                'training_progress_total' => $trainingOverview['total'],
                'exams_passed' => $examStreak,
                'streak' => $member->streak_days ?? 0,
                'level' => $member->level ?? 1,
                'points' => ($member->points ?? 0) + ($member->total_points_spent ?? 0),
                'last_activity' => $member->last_activity_date,
                'role' => $member->pivot->role
            ];
        })->sortByDesc('points')->values();
    }

    /**
     * Schwachstellen-Analyse für alle Mitglieder (nur normale Mitglieder, keine Ausbilder)
     */
    public function getWeaknesses()
    {
        // Nur normale Mitglieder (role = 'member'), keine Ausbilder
        $memberIds = $this->members()->wherePivot('role', 'member')->pluck('users.id');
        
        // Lernabschnitte mit niedriger Erfolgsquote
        $weakSections = DB::table('question_statistics')
            ->whereIn('user_id', $memberIds)
            ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
            ->select(
                'questions.lernabschnitt',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN question_statistics.is_correct = 1 THEN 1 ELSE 0 END) as correct')
            )
            ->groupBy('questions.lernabschnitt')
            ->get()
            ->map(function($section) {
                return [
                    'section' => $section->lernabschnitt,
                    'success_rate' => $section->total > 0 ? round(($section->correct / $section->total) * 100) : 0,
                    'total_attempts' => $section->total
                ];
            })
            ->sortBy('success_rate')
            ->take(5);
        
        // Häufigste Fehler
        $commonErrors = DB::table('question_statistics')
            ->whereIn('user_id', $memberIds)
            ->where('is_correct', false)
            ->select('question_id', DB::raw('COUNT(*) as error_count'))
            ->groupBy('question_id')
            ->orderByDesc('error_count')
            ->take(10)
            ->get()
            ->map(function($error) {
                $question = \App\Models\Question::find($error->question_id);
                return [
                    'question' => $question,
                    'error_count' => $error->error_count
                ];
            });
        
        return [
            'weak_sections' => $weakSections,
            'common_errors' => $commonErrors
        ];
    }

    /**
     * Themenabdeckung: pro Lernabschnitt der durchschnittliche Anteil der von
     * Mitgliedern (keine Ausbilder) jemals beantworteten Fragen. Aufsteigend
     * sortiert, damit die am wenigsten behandelten Themen oben stehen.
     */
    public function getTopicCoverage()
    {
        $memberIds = $this->members()->wherePivot('role', 'member')->pluck('users.id');
        $memberCount = $memberIds->count();

        if ($memberCount === 0) {
            return collect();
        }

        $totalPerSection = DB::table('questions')
            ->select('lernabschnitt', DB::raw('COUNT(*) as total'))
            ->groupBy('lernabschnitt')
            ->pluck('total', 'lernabschnitt');

        $attemptsPerUserSection = DB::table('question_statistics')
            ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
            ->whereIn('question_statistics.user_id', $memberIds)
            ->select(
                'questions.lernabschnitt',
                'question_statistics.user_id',
                DB::raw('COUNT(DISTINCT question_statistics.question_id) as attempted')
            )
            ->groupBy('questions.lernabschnitt', 'question_statistics.user_id')
            ->get();

        return $totalPerSection
            ->filter(fn ($total) => $total > 0)
            ->map(function ($total, $section) use ($attemptsPerUserSection, $memberCount) {
                $sectionAttempts = $attemptsPerUserSection->where('lernabschnitt', $section);
                $sumPercent = 0;
                foreach ($sectionAttempts as $row) {
                    $sumPercent += min(100, ($row->attempted / $total) * 100);
                }

                return [
                    'section' => (int) $section,
                    'avg_coverage_percent' => (int) round($sumPercent / $memberCount),
                    'members_touched' => $sectionAttempts->count(),
                    'total_members' => $memberCount,
                    'total_questions' => (int) $total,
                ];
            })
            ->sortBy('avg_coverage_percent')
            ->values();
    }

    /**
     * Pro Ausbildungsmodul: wie viele Mitglieder (keine Ausbilder) haben es
     * abgeschlossen. Ein Lernabschnitt gilt als abgeschlossen, wenn alle
     * Unterpunkte abgehakt sind; eine Zusatzausbildung, wenn ihr Key gesetzt ist.
     */
    public function getTrainingModuleCompletion(): array
    {
        $memberIds = $this->members()->wherePivot('role', 'member')->pluck('users.id');
        $memberCount = $memberIds->count();

        if ($memberCount === 0) {
            return [
                'members_total' => 0,
                'lernabschnitte' => collect(),
                'zusatz' => collect(),
            ];
        }

        $progressByUser = DB::table('user_training_progress')
            ->whereIn('user_id', $memberIds)
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->pluck('item_key')->flip()->all());

        $countCompletedFor = function (array $requiredKeys) use ($memberIds, $progressByUser): int {
            $count = 0;
            foreach ($memberIds as $uid) {
                $userKeys = $progressByUser->get($uid, []);
                $allDone = true;
                foreach ($requiredKeys as $key) {
                    if (!isset($userKeys[$key])) {
                        $allDone = false;
                        break;
                    }
                }
                if ($allDone) {
                    $count++;
                }
            }
            return $count;
        };

        $lernabschnitte = collect(\App\Services\TrainingProgressService::LERNABSCHNITTE)->map(function ($la) use ($countCompletedFor, $memberCount) {
            $requiredKeys = array_column($la['items'], 'key');
            $count = $countCompletedFor($requiredKeys);
            return [
                'key' => $la['key'],
                'nr' => $la['nr'],
                'title' => $la['title'],
                'completed_count' => $count,
                'total_members' => $memberCount,
                'percent' => (int) round(($count / $memberCount) * 100),
            ];
        });

        $zusatz = collect(\App\Services\TrainingProgressService::ZUSATZAUSBILDUNGEN)->map(function ($item) use ($countCompletedFor, $memberCount) {
            $count = $countCompletedFor([$item['key']]);
            return [
                'key' => $item['key'],
                'label' => $item['label'],
                'completed_count' => $count,
                'total_members' => $memberCount,
                'percent' => (int) round(($count / $memberCount) * 100),
            ];
        });

        return [
            'members_total' => $memberCount,
            'lernabschnitte' => $lernabschnitte,
            'zusatz' => $zusatz,
        ];
    }

    /**
     * Durchschnittsstatistiken (nur normale Mitglieder, keine Ausbilder)
     */
    public function getAverageStats()
    {
        // Nur normale Mitglieder (role = 'member'), keine Ausbilder
        $progress = $this->getMemberProgress()->filter(fn($m) => $m['role'] === 'member');
        $memberCount = $progress->count();
        
        if ($memberCount === 0) {
            return [
                'avg_theory' => 0,
                'avg_exams' => 0,
                'avg_streak' => 0,
                'total_members' => 0,
                'active_members' => 0
            ];
        }
        
        return [
            'avg_theory' => round($progress->avg('theory_progress_percent')),
            'avg_exams' => round($progress->avg('exams_passed'), 1),
            'avg_streak' => round($progress->avg('streak')),
            'total_members' => $memberCount,
            'active_members' => $progress->filter(function($m) {
                if (!$m['last_activity']) return false;
                $lastActivity = is_string($m['last_activity']) 
                    ? \Carbon\Carbon::parse($m['last_activity']) 
                    : $m['last_activity'];
                return $lastActivity->isAfter(now()->subDays(7));
            })->count()
        ];
    }

    /**
     * Alle Lernpools dieses Ortsverbands
     */
    public function lernpools(): HasMany
    {
        return $this->hasMany(OrtsverbandLernpool::class);
    }

    /**
     * Aktive Lernpools
     */
    public function activeLernpools(): HasMany
    {
        return $this->lernpools()->where('is_active', true);
    }
}

