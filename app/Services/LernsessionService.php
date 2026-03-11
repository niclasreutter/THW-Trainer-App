<?php

namespace App\Services;

use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Models\LearningSessionParticipant;
use App\Models\Lootbox;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LernsessionService
{
    // Session erstellen
    public function createSession(array $data, User $creator): LearningSession
    {
        $session = LearningSession::create(array_merge($data, [
            'created_by' => $creator->id,
        ]));

        // Für Einmal-Sessions direkt eine Instanz erstellen
        if ($session->schedule_type === 'once') {
            $status = $session->starts_at && $session->starts_at->lte(now()) ? 'active' : 'scheduled';
            LearningSessionInstance::create([
                'learning_session_id' => $session->id,
                'starts_at' => $session->starts_at,
                'ends_at' => $session->ends_at,
                'status' => $status,
            ]);
        } else {
            // Für wiederkehrende Sessions Instanzen generieren
            $this->generateInstances($session);
        }

        return $session;
    }

    // Session aktualisieren
    public function updateSession(LearningSession $session, array $data): LearningSession
    {
        $session->update($data);

        // Zukünftige scheduled Instanzen entfernen und neu generieren
        $session->instances()
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->delete();

        if ($session->schedule_type === 'once') {
            // Prüfe ob eine Instanz existiert
            $existing = $session->instances()->first();
            if (!$existing) {
                LearningSessionInstance::create([
                    'learning_session_id' => $session->id,
                    'starts_at' => $session->starts_at,
                    'ends_at' => $session->ends_at,
                    'status' => 'scheduled',
                ]);
            }
        } else {
            $this->generateInstances($session);
        }

        return $session;
    }

    // Session löschen
    public function deleteSession(LearningSession $session): void
    {
        $session->delete();
    }

    // Instanzen für wiederkehrende Sessions generieren (nächste 14 Tage)
    public function generateInstances(LearningSession $session, int $daysAhead = 14): void
    {
        if ($session->schedule_type !== 'recurring' || !$session->recurrence_days) {
            return;
        }

        $start = Carbon::today();
        if ($session->recurrence_valid_from && $session->recurrence_valid_from->isFuture()) {
            $start = $session->recurrence_valid_from->copy();
        }

        $end = $start->copy()->addDays($daysAhead);
        if ($session->recurrence_valid_until && $session->recurrence_valid_until->lt($end)) {
            $end = $session->recurrence_valid_until->copy();
        }

        $current = $start->copy();
        while ($current->lte($end)) {
            // ISO Wochentag: 1=Montag, 7=Sonntag
            if (in_array($current->dayOfWeekIso, $session->recurrence_days)) {
                $instanceStart = $current->copy()->setTimeFromTimeString($session->recurrence_start_time);
                $instanceEnd = $current->copy()->setTimeFromTimeString($session->recurrence_end_time);

                // Nur zukünftige Instanzen, und nur wenn nicht bereits vorhanden
                if ($instanceEnd->isFuture()) {
                    $exists = $session->instances()
                        ->where('starts_at', $instanceStart)
                        ->exists();

                    if (!$exists) {
                        LearningSessionInstance::create([
                            'learning_session_id' => $session->id,
                            'starts_at' => $instanceStart,
                            'ends_at' => $instanceEnd,
                            'status' => $instanceStart->isPast() ? 'active' : 'scheduled',
                        ]);
                    }
                }
            }
            $current->addDay();
        }
    }

    // Instanz aktivieren
    public function activateInstance(LearningSessionInstance $instance): void
    {
        $instance->update(['status' => 'active']);
    }

    // Instanz abschließen: Ranking finalisieren, Winner bestimmen, Lootbox vergeben
    public function completeInstance(LearningSessionInstance $instance): array
    {
        $participants = $instance->participants()
            ->orderByDesc('rank_score')
            ->get();

        // Finale Ränge setzen
        $rank = 1;
        foreach ($participants as $participant) {
            $participant->update(['final_rank' => $rank]);
            $rank++;
        }

        // Winner bestimmen und Lootbox vergeben
        $winner = null;
        if ($participants->isNotEmpty()) {
            $topParticipant = $participants->first();
            if ($topParticipant->questions_answered > 0) {
                $winner = $topParticipant->user;
                $instance->update([
                    'status' => 'completed',
                    'winner_user_id' => $winner->id,
                ]);

                $this->awardWinnerLootbox($instance, $winner);
            } else {
                $instance->update(['status' => 'completed']);
            }
        } else {
            $instance->update(['status' => 'completed']);
        }

        // Notifications an alle Teilnehmer
        $sessionTitle = $instance->learningSession->title;
        foreach ($participants as $participant) {
            if ($participant->questions_answered > 0) {
                $isWinner = $winner && $participant->user_id === $winner->id;
                Notification::create([
                    'user_id' => $participant->user_id,
                    'type' => 'lernsession',
                    'title' => $isWinner ? 'Lernsession gewonnen!' : 'Lernsession beendet',
                    'message' => $isWinner
                        ? "Du hast die Lernsession \"{$sessionTitle}\" gewonnen! Platz {$participant->final_rank} mit {$participant->xp_earned} XP."
                        : "Lernsession \"{$sessionTitle}\" beendet. Dein Platz: {$participant->final_rank} mit {$participant->xp_earned} XP.",
                    'data' => json_encode([
                        'instance_id' => $instance->id,
                        'rank' => $participant->final_rank,
                        'xp' => $participant->xp_earned,
                    ]),
                ]);
            }
        }

        // Cache invalidieren
        foreach ($participants as $participant) {
            Cache::forget("lernsession_active_{$participant->user_id}");
        }

        return [
            'winner' => $winner,
            'participants_count' => $participants->count(),
        ];
    }

    // Lootbox für Gewinner vergeben
    private function awardWinnerLootbox(LearningSessionInstance $instance, User $winner): void
    {
        Lootbox::create([
            'user_id' => $winner->id,
            'type' => 'gold',
            'reward_type' => null,
            'reward_amount' => null,
            'opened' => false,
        ]);

        Notification::create([
            'user_id' => $winner->id,
            'type' => 'league_lootbox',
            'title' => 'Gold-Lootbox erhalten!',
            'message' => "Du hast eine Gold-Lootbox als Belohnung für den Sieg in \"{$instance->learningSession->title}\" erhalten!",
            'data' => null,
        ]);
    }

    // Session beitreten
    public function joinSession(LearningSessionInstance $instance, User $user, bool $rankingConsent = false): LearningSessionParticipant
    {
        $participant = LearningSessionParticipant::firstOrCreate(
            [
                'instance_id' => $instance->id,
                'user_id' => $user->id,
            ],
            [
                'joined_at' => now(),
                'ranking_consent' => $rankingConsent,
            ]
        );

        // Cache invalidieren damit der Boost sofort greift
        Cache::forget("lernsession_active_{$user->id}");

        return $participant;
    }

    // Antwort aufzeichnen und Ranking aktualisieren
    public function recordAnswer(LearningSessionParticipant $participant, bool $isCorrect, int $answerTimeMs, int $xpAwarded = 0): void
    {
        $newAnswered = $participant->questions_answered + 1;
        $newCorrect = $participant->questions_correct + ($isCorrect ? 1 : 0);
        $newXp = $participant->xp_earned + $xpAwarded;
        $newAccuracy = $newAnswered > 0 ? round(($newCorrect / $newAnswered) * 100, 2) : 0;

        // Gleitender Durchschnitt der Antwortzeit
        $newAvgTime = $participant->avg_answer_time_ms;
        if ($answerTimeMs > 0) {
            if ($newAvgTime === null) {
                $newAvgTime = $answerTimeMs;
            } else {
                $newAvgTime = (int) round(
                    (($participant->avg_answer_time_ms * $participant->questions_answered) + $answerTimeMs) / $newAnswered
                );
            }
        }

        $participant->update([
            'questions_answered' => $newAnswered,
            'questions_correct' => $newCorrect,
            'xp_earned' => $newXp,
            'accuracy_percent' => $newAccuracy,
            'avg_answer_time_ms' => $newAvgTime,
        ]);

        // Rank Score neu berechnen
        $participant->update([
            'rank_score' => $participant->calculateRankScore(),
        ]);
    }

    // Prüft ob ein User in einer aktiven Session ist (mit Cache)
    public function isUserInActiveSession(User $user): ?LearningSessionParticipant
    {
        $cacheKey = "lernsession_active_{$user->id}";

        return Cache::remember($cacheKey, 60, function () use ($user) {
            return LearningSessionParticipant::whereHas('instance', function ($q) {
                $q->where('status', 'active')
                  ->where('starts_at', '<=', now())
                  ->where('ends_at', '>=', now());
            })->where('user_id', $user->id)->first();
        });
    }

    // Aktive Sessions für User abrufen
    public function getActiveSessionsForUser(User $user): Collection
    {
        return LearningSessionInstance::where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereHas('learningSession', function ($q) use ($user) {
                $q->forUser($user);
            })
            ->with('learningSession')
            ->get();
    }

    // Bevorstehende Sessions für User abrufen
    public function getUpcomingSessionsForUser(User $user): Collection
    {
        return LearningSessionInstance::where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->whereHas('learningSession', function ($q) use ($user) {
                $q->forUser($user);
            })
            ->with('learningSession')
            ->orderBy('starts_at')
            ->limit(10)
            ->get();
    }

    // Live Ranking-Daten für Polling
    public function getLiveRankingData(LearningSessionInstance $instance, User $currentUser): array
    {
        $participants = $instance->participants()
            ->with('user')
            ->orderByDesc('rank_score')
            ->get();

        $ranking = [];
        $rank = 1;
        foreach ($participants as $participant) {
            $isCurrentUser = $participant->user_id === $currentUser->id;

            $ranking[] = [
                'rank' => $rank,
                'user_name' => $participant->ranking_consent
                    ? $participant->user->name
                    : ($isCurrentUser ? $participant->user->name : 'Anonymer Teilnehmer'),
                'xp_earned' => $participant->xp_earned,
                'accuracy' => (float) $participant->accuracy_percent,
                'questions' => $participant->questions_answered,
                'is_current_user' => $isCurrentUser,
            ];
            $rank++;
        }

        // Eigene Stats
        $myParticipant = $participants->firstWhere('user_id', $currentUser->id);
        $myStats = $myParticipant ? [
            'rank' => collect($ranking)->firstWhere('is_current_user', true)['rank'] ?? null,
            'xp_earned' => $myParticipant->xp_earned,
            'accuracy' => (float) $myParticipant->accuracy_percent,
            'questions' => $myParticipant->questions_answered,
            'avg_time_ms' => $myParticipant->avg_answer_time_ms,
        ] : null;

        return [
            'ranking' => $ranking,
            'myStats' => $myStats,
            'timeRemaining' => $instance->getTimeRemainingSeconds(),
            'participantCount' => $participants->count(),
        ];
    }

    // Alle Sessions für einen User abrufen (für Index-Seite)
    public function getSessionsForUser(User $user): Collection
    {
        return LearningSession::forUser($user)
            ->with(['creator', 'ortsverband', 'instances' => function ($q) {
                $q->orderByDesc('starts_at');
            }])
            ->orderByDesc('created_at')
            ->get();
    }

    // Gewonnene Sessions zählen
    public function getWonSessionsCount(User $user): int
    {
        return LearningSessionInstance::where('winner_user_id', $user->id)
            ->where('status', 'completed')
            ->count();
    }

    // Letzte Ergebnisse des Users
    public function getRecentResults(User $user, int $limit = 5): Collection
    {
        return LearningSessionParticipant::where('user_id', $user->id)
            ->whereNotNull('final_rank')
            ->whereHas('instance', function ($q) {
                $q->where('status', 'completed');
            })
            ->with(['instance.learningSession'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
