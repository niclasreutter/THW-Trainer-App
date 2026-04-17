<?php

namespace App\Services;

use App\Models\ExtraQuestion;
use App\Models\User;
use App\Models\UserExtraQuestionProgress;
use Illuminate\Support\Carbon;

class ExtraQuestionService
{
    /**
     * Baut eine Queue von Extra-Fragen-IDs für einen User.
     *
     * Priorisierung (analog zu Official-Practice):
     *   1. Due reviews (next_review_at <= now)
     *   2. Unsolved (kein Progress vorhanden)
     *   3. In-Progress (consecutive_correct < MASTERY_THRESHOLD)
     *   4. Random fill (gemeisterte Fragen)
     *
     * @param string      $mode          'extras_only' | 'mixed' (für spätere Tasks)
     * @param string|null $lernabschnitt Optional: auf einen Abschnitt beschränken
     * @param int         $limit         Max. Anzahl IDs in Queue
     * @return array<int>                Array of extra_question ids
     */
    public function buildQueue(
        User $user,
        string $mode = 'extras_only',
        ?string $lernabschnitt = null,
        int $limit = 40
    ): array {
        if (! $user->extras_enabled) {
            return [];
        }

        $baseQuery = ExtraQuestion::query();
        if ($lernabschnitt) {
            $baseQuery->where('lernabschnitt', $lernabschnitt);
        }

        $dueIds = $this->getDueQuestionIds($user, (clone $baseQuery));
        $unsolvedIds = $this->getUnsolvedQuestionIds($user, (clone $baseQuery));
        $inProgressIds = $this->getInProgressQuestionIds($user, (clone $baseQuery));

        $queue = collect($dueIds)
            ->merge($unsolvedIds)
            ->merge($inProgressIds)
            ->unique()
            ->take($limit)
            ->values()
            ->toArray();

        // Falls nicht voll: mit zufälligen (gemeisterten) auffüllen
        if (count($queue) < $limit) {
            $fill = (clone $baseQuery)
                ->whereNotIn('id', $queue)
                ->inRandomOrder()
                ->limit($limit - count($queue))
                ->pluck('id')
                ->toArray();
            $queue = array_merge($queue, $fill);
        }

        return array_map('intval', $queue);
    }

    /**
     * Aktualisiert den Progress nach einer Beantwortung (vereinfachte SM-2 Variante).
     *
     * Analog zu UserQuestionProgress + SpacedRepetitionService, aber ohne den
     * globalen-Difficulty-Lookup (QuestionStatistic existiert nur für Official).
     */
    public function updateProgress(User $user, ExtraQuestion $question, bool $wasCorrect): UserExtraQuestionProgress
    {
        $progress = UserExtraQuestionProgress::firstOrNew([
            'user_id' => $user->id,
            'extra_question_id' => $question->id,
        ]);

        $ef = (float) ($progress->easiness_factor ?? 2.5);
        $interval = (int) ($progress->review_interval ?? 0);
        $repetition = (int) ($progress->repetition_count ?? 0);
        $consecutive = (int) ($progress->consecutive_correct ?? 0);

        if ($wasCorrect) {
            $consecutive++;
            // SM-2: quality 4 (korrekt) oder 5 (gemeistert)
            $quality = $consecutive >= UserExtraQuestionProgress::MASTERY_THRESHOLD ? 5 : 4;

            if ($repetition === 0) {
                $interval = 1;
            } elseif ($repetition === 1) {
                $interval = 3;
            } else {
                $interval = (int) round($interval * $ef);
            }
            $repetition++;
        } else {
            $consecutive = 0;
            $quality = 1;
            $repetition = 0;
            // Bei Fehler: Intervall graduell erhöhen, Cap 14 Tage (kein globaler Schwierigkeitslookup)
            $interval = min(max(1, $interval + 1), 14);
        }

        // EF-Update (SM-2 Formel)
        $ef = $ef + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        $ef = max(1.3, $ef);

        // Cap Intervall bei 90 Tagen
        $interval = min($interval, 90);

        $progress->consecutive_correct = $consecutive;
        $progress->easiness_factor = round($ef, 2);
        $progress->review_interval = $interval;
        $progress->repetition_count = $repetition;
        $progress->last_answered_at = now();

        // Gemeisterte Fragen verlassen das SR-System
        if ($consecutive >= UserExtraQuestionProgress::MASTERY_THRESHOLD) {
            $progress->next_review_at = null;
        } else {
            $progress->next_review_at = Carbon::today()->addDays($interval);
        }

        $progress->save();

        return $progress;
    }

    /**
     * Gibt die nächste Frage aus einer Queue zurück (Queue wird per Referenz mutiert)
     * oder null, wenn leer bzw. keine gültige Frage mehr übrig ist.
     *
     * @param array<int> $queue
     */
    public function nextFromQueue(array &$queue): ?ExtraQuestion
    {
        while (! empty($queue)) {
            $id = array_shift($queue);
            $q = ExtraQuestion::with([
                'options',
                'matchCategories',
                'matchItems.correctCategory',
            ])->find($id);

            if ($q) {
                return $q;
            }
        }

        return null;
    }

    /**
     * Merged zwei Queues (official + extras) im Verhältnis 4:1.
     * Jede 5. Position ist ein Extra (falls vorhanden).
     *
     * @param array<int> $officialIds  IDs aus questions-Tabelle
     * @param array<int> $extraIds     IDs aus extra_questions-Tabelle
     * @return array<array{type: string, id: int}>
     */
    public function mergeQueues(array $officialIds, array $extraIds): array
    {
        $result = [];
        $extraIdx = 0;

        foreach ($officialIds as $i => $oid) {
            $result[] = ['type' => 'official', 'id' => (int) $oid];
            if ((($i + 1) % 4) === 0 && isset($extraIds[$extraIdx])) {
                $result[] = ['type' => 'extra', 'id' => (int) $extraIds[$extraIdx]];
                $extraIdx++;
            }
        }

        // Verbleibende Extras hinten anhängen
        while ($extraIdx < count($extraIds)) {
            $result[] = ['type' => 'extra', 'id' => (int) $extraIds[$extraIdx]];
            $extraIdx++;
        }

        return $result;
    }

    // --- Private Helpers ---

    private function getDueQuestionIds(User $user, $query): array
    {
        return $query->whereHas('userProgress', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereNotNull('next_review_at')
              ->where('next_review_at', '<=', now());
        })->pluck('id')->toArray();
    }

    private function getUnsolvedQuestionIds(User $user, $query): array
    {
        return $query->whereDoesntHave('userProgress', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->pluck('id')->toArray();
    }

    private function getInProgressQuestionIds(User $user, $query): array
    {
        return $query->whereHas('userProgress', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('consecutive_correct', '<', UserExtraQuestionProgress::MASTERY_THRESHOLD)
              ->where('consecutive_correct', '>', 0);
        })->pluck('id')->toArray();
    }
}
