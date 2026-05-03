<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Models\UserLehrgangProgress;
use App\Models\UserExtraQuestionProgress;
use App\Models\ExamStatistic;
use App\Models\XpHistory;

class DataExportService
{
    /**
     * Erzeugt alle CSV-Dateien für den Datenexport eines Users.
     *
     * @return array<int, array{name: string, content: string}>
     */
    public function buildCsvFiles(User $user): array
    {
        return array_values(array_filter([
            $this->buildProfileCsv($user),
            $this->buildNotificationsCsv($user),
            $this->buildOrtsverbaendeCsv($user),
            $this->buildQuestionProgressCsv($user),
            $this->buildLehrgangProgressCsv($user),
            $this->buildExtraQuestionProgressCsv($user),
            $this->buildExamStatisticsCsv($user),
            $this->buildXpHistoryCsv($user),
            $this->buildBookmarksCsv($user),
        ]));
    }

    /**
     * Liefert eine Zusammenfassung der enthaltenen Daten als reine Zahlen,
     * die in der Begleit-Mail angezeigt werden.
     *
     * @return array<string, int>
     */
    public function buildSummary(User $user): array
    {
        return [
            'questionProgress' => UserQuestionProgress::where('user_id', $user->id)->count(),
            'lehrgangProgress' => UserLehrgangProgress::where('user_id', $user->id)->count(),
            'extraQuestionProgress' => UserExtraQuestionProgress::where('user_id', $user->id)->count(),
            'examStatistics' => ExamStatistic::where('user_id', $user->id)->count(),
            'xpHistory' => XpHistory::where('user_id', $user->id)->count(),
            'bookmarks' => is_array($user->bookmarked_questions) ? count($user->bookmarked_questions) : 0,
            'ortsverbaende' => $user->ortsverbande()->count(),
        ];
    }

    private function buildProfileCsv(User $user): array
    {
        $rows = [
            ['Feld', 'Wert'],
            ['ID', $user->id],
            ['Name', $user->name],
            ['E-Mail', $user->email],
            ['E-Mail bestätigt am', $user->email_verified_at?->format('Y-m-d H:i:s') ?? ''],
            ['Account erstellt am', $user->created_at?->format('Y-m-d H:i:s') ?? ''],
            ['Letzte Aktivität', $user->last_activity_at?->format('Y-m-d H:i:s') ?? ''],
            ['Prüfungsdatum', $user->exam_date?->format('Y-m-d') ?? ''],
            ['Rolle', $user->useroll ?? ''],
            ['Punkte (gesamt)', $user->points ?? 0],
            ['Level', $user->level ?? 0],
            ['Streak (Tage)', $user->streak_days ?? 0],
            ['Bestandene Prüfungen', $user->exam_passed_count ?? 0],
            ['E-Mail-Consent', $user->email_consent ? 'ja' : 'nein'],
            ['E-Mail-Consent erteilt am', $user->email_consent_at?->format('Y-m-d H:i:s') ?? ''],
            ['Push-Consent', $user->push_consent ? 'ja' : 'nein'],
            ['Push-Consent erteilt am', $user->push_consent_at?->format('Y-m-d H:i:s') ?? ''],
            ['Leaderboard-Consent', $user->leaderboard_consent ? 'ja' : 'nein'],
            ['Leaderboard-Consent erteilt am', $user->leaderboard_consent_at?->format('Y-m-d H:i:s') ?? ''],
            ['Onboarding abgeschlossen', $user->onboarding_completed ? 'ja' : 'nein'],
            ['Zusatz-Fragen aktiviert', $user->extras_enabled ? 'ja' : 'nein'],
            ['Avatar-Pfad', $user->avatar_path ?? ''],
        ];

        return [
            'name' => 'profil.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildNotificationsCsv(User $user): array
    {
        $rows = [['Kategorie', 'E-Mail', 'Push']];
        foreach (User::NOTIFICATION_CATEGORIES as $cat) {
            $rows[] = [
                $cat,
                $user->{"notify_{$cat}_email"} ? 'ja' : 'nein',
                $user->{"notify_{$cat}_push"} ? 'ja' : 'nein',
            ];
        }

        return [
            'name' => 'benachrichtigungen.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildOrtsverbaendeCsv(User $user): array
    {
        $rows = [['Ortsverband', 'Rolle', 'Beigetreten am']];
        foreach ($user->ortsverbande()->get() as $ov) {
            $rows[] = [
                $ov->name ?? ('OV #' . $ov->id),
                $ov->pivot->role ?? '',
                isset($ov->pivot->joined_at) ? (string) $ov->pivot->joined_at : '',
            ];
        }

        return [
            'name' => 'ortsverbaende.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildQuestionProgressCsv(User $user): array
    {
        $rows = [[
            'question_id',
            'consecutive_correct',
            'last_answered_at',
            'next_review_at',
            'review_interval',
            'easiness_factor',
            'repetition_count',
        ]];

        UserQuestionProgress::where('user_id', $user->id)
            ->orderBy('question_id')
            ->chunk(500, function ($items) use (&$rows) {
                foreach ($items as $p) {
                    $rows[] = [
                        $p->question_id,
                        $p->consecutive_correct,
                        $p->last_answered_at?->format('Y-m-d H:i:s') ?? '',
                        $p->next_review_at?->format('Y-m-d H:i:s') ?? '',
                        $p->review_interval,
                        $p->easiness_factor,
                        $p->repetition_count,
                    ];
                }
            });

        return [
            'name' => 'fragen-fortschritt.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildLehrgangProgressCsv(User $user): array
    {
        $rows = [['lehrgang_question_id', 'consecutive_correct', 'solved', 'failed', 'updated_at']];

        UserLehrgangProgress::where('user_id', $user->id)
            ->orderBy('lehrgang_question_id')
            ->chunk(500, function ($items) use (&$rows) {
                foreach ($items as $p) {
                    $rows[] = [
                        $p->lehrgang_question_id,
                        $p->consecutive_correct,
                        $p->solved ? 'ja' : 'nein',
                        $p->failed ? 'ja' : 'nein',
                        $p->updated_at?->format('Y-m-d H:i:s') ?? '',
                    ];
                }
            });

        return [
            'name' => 'lehrgang-fortschritt.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildExtraQuestionProgressCsv(User $user): array
    {
        $rows = [['extra_question_id', 'consecutive_correct', 'last_answered_at', 'next_review_at']];

        UserExtraQuestionProgress::where('user_id', $user->id)
            ->orderBy('id')
            ->chunk(500, function ($items) use (&$rows) {
                foreach ($items as $p) {
                    $rows[] = [
                        $p->extra_question_id ?? '',
                        $p->consecutive_correct ?? '',
                        isset($p->last_answered_at) ? (string) $p->last_answered_at : '',
                        isset($p->next_review_at) ? (string) $p->next_review_at : '',
                    ];
                }
            });

        return [
            'name' => 'zusatzfragen-fortschritt.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildExamStatisticsCsv(User $user): array
    {
        $rows = [['datum', 'bestanden', 'richtige_antworten']];

        ExamStatistic::where('user_id', $user->id)
            ->orderBy('created_at')
            ->chunk(500, function ($items) use (&$rows) {
                foreach ($items as $stat) {
                    $rows[] = [
                        $stat->created_at?->format('Y-m-d H:i:s') ?? '',
                        $stat->is_passed ? 'ja' : 'nein',
                        $stat->correct_answers,
                    ];
                }
            });

        return [
            'name' => 'pruefungen.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildXpHistoryCsv(User $user): array
    {
        $rows = [['datum', 'punkte', 'grund', 'quelle_typ', 'quelle_id', 'gesamt_vorher', 'gesamt_nachher', 'level_vorher', 'level_nachher']];

        XpHistory::where('user_id', $user->id)
            ->orderBy('created_at')
            ->chunk(1000, function ($items) use (&$rows) {
                foreach ($items as $xp) {
                    $rows[] = [
                        $xp->created_at?->format('Y-m-d H:i:s') ?? '',
                        $xp->points,
                        $xp->reason,
                        $xp->source_type,
                        $xp->source_id,
                        $xp->total_before,
                        $xp->total_after,
                        $xp->level_before,
                        $xp->level_after,
                    ];
                }
            });

        return [
            'name' => 'xp-historie.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    private function buildBookmarksCsv(User $user): array
    {
        $rows = [['question_id']];
        foreach ((array) ($user->bookmarked_questions ?? []) as $qid) {
            $rows[] = [$qid];
        }

        return [
            'name' => 'lesezeichen.csv',
            'content' => $this->arrayToCsv($rows),
        ];
    }

    /**
     * Wandelt ein Array in einen CSV-String um (RFC 4180, UTF-8 BOM für Excel).
     *
     * @param array<int, array<int, mixed>> $rows
     */
    private function arrayToCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        // UTF-8 BOM, damit Excel Umlaute korrekt darstellt
        fwrite($handle, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($handle, $row, ';', '"', '\\');
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
