<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ExamFeedback;
use App\Models\ExamStatistic;
use App\Models\LehrgangQuestionIssue;
use App\Models\Ortsverband;
use App\Models\Question;
use App\Models\QuestionIssue;
use App\Models\QuestionStatistic;
use App\Models\User;
use App\Models\UserExtraQuestionSubmission;
use App\Models\UserQuestionProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Supported ranges for delta/sparkline calculations.
     *
     * Value = number of days for the "current window".
     */
    private const RANGES = [
        '24h' => 1,
        '7d'  => 7,
        '30d' => 30,
        '90d' => 90,
    ];

    public function index(Request $request)
    {
        $range = $request->query('range', '7d');
        if (! array_key_exists($range, self::RANGES)) {
            $range = '7d';
        }
        $days = self::RANGES[$range];

        $systemPulse    = $this->getSystemPulse();
        $kpis           = $this->getKpis($days);
        $activityChart  = $this->getActivityChart($days);
        $handlungsbedarf = $this->getHandlungsbedarf();
        $fragenQualitaet = $this->getFragenQualitaet($days);
        $ortsverbaende  = $this->getOrtsverbaende();
        $srStats        = $this->getSpacedRepetitionStats();
        $liveFeed       = $this->getLiveFeed();

        return view('admin.dashboard', compact(
            'range',
            'systemPulse',
            'kpis',
            'activityChart',
            'handlungsbedarf',
            'fragenQualitaet',
            'ortsverbaende',
            'srStats',
            'liveFeed'
        ));
    }

    /* =========================================================
       SYSTEM PULSE — DB / Cache / Worker / Issues
       ========================================================= */
    private function getSystemPulse(): array
    {
        return [
            'database' => $this->pulseDatabase(),
            'cache'    => $this->pulseCache(),
            'worker'   => $this->pulseWorker(),
            'issues'   => $this->pulseIssues(),
        ];
    }

    private function pulseDatabase(): array
    {
        try {
            $startedAt = microtime(true);
            DB::connection()->getPdo();
            $database = config('database.connections.mysql.database');
            $row = DB::select("
                SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$database]);
            $sizeMb = $row[0]->size_mb ?? 0;
            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

            return [
                'status' => 'ok',
                'label'  => 'Datenbank',
                'value'  => number_format((float) $sizeMb, 0, ',', '.') . ' MB',
                'sub'    => 'MySQL · ' . $latencyMs . ' ms',
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'err',
                'label'  => 'Datenbank',
                'value'  => 'offline',
                'sub'    => 'Verbindung fehlgeschlagen',
            ];
        }
    }

    private function pulseCache(): array
    {
        try {
            $probe = 'pulse_' . bin2hex(random_bytes(3));
            Cache::put($probe, '1', 2);
            $ok = Cache::get($probe) === '1';
            Cache::forget($probe);
            $driver = config('cache.default');

            return [
                'status' => $ok ? 'ok' : 'err',
                'label'  => 'Cache',
                'value'  => $ok ? 'Online' : 'Fehler',
                'sub'    => strtoupper($driver) . ' · Hit-Rate',
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'err',
                'label'  => 'Cache',
                'value'  => 'offline',
                'sub'    => 'Probe fehlgeschlagen',
            ];
        }
    }

    private function pulseWorker(): array
    {
        try {
            $pending = DB::table('jobs')->count();
            $failed  = DB::table('failed_jobs')->count();

            if ($failed > 0) {
                $status = 'warn';
                $value  = $failed . ' ' . ($failed === 1 ? 'Fehler' : 'Fehler');
            } elseif ($pending > 0) {
                $status = 'ok';
                $value  = $pending . ' Queue';
            } else {
                $status = 'ok';
                $value  = 'Bereit';
            }

            return [
                'status' => $status,
                'label'  => 'Worker',
                'value'  => $value,
                'sub'    => 'Queue: ' . $pending . ' · Failed: ' . $failed,
                'link'   => $failed > 0
                    ? route('admin.failed-jobs.index')
                    : route('admin.logs.index', 'worker'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'err',
                'label'  => 'Worker',
                'value'  => 'n/a',
                'sub'    => 'Queue nicht erreichbar',
                'link'   => route('admin.logs.index', 'worker'),
            ];
        }
    }

    private function pulseIssues(): array
    {
        $openIssues   = QuestionIssue::where('status', 'open')->count()
                      + LehrgangQuestionIssue::where('status', 'open')->count();
        $unreadContact = ContactMessage::where('is_read', false)->count();
        $totalOpen = $openIssues + $unreadContact;

        if ($totalOpen === 0) {
            $status = 'ok';
            $value  = 'Keine offen';
        } elseif ($totalOpen >= 5) {
            $status = 'err';
            $value  = $totalOpen . ' offen';
        } else {
            $status = 'warn';
            $value  = $totalOpen . ' offen';
        }

        return [
            'status' => $status,
            'label'  => 'Issues',
            'value'  => $value,
            'sub'    => $openIssues . ' Bugs · ' . $unreadContact . ' Kontakt',
            'link'   => route('admin.issues.index'),
        ];
    }

    /* =========================================================
       KPI CARDS — value, delta, sparkline
       ========================================================= */
    private function getKpis(int $days): array
    {
        return [
            'users'     => $this->kpiUsers($days),
            'wau'       => $this->kpiWau($days),
            'answers'   => $this->kpiAnswers($days),
            'success'   => $this->kpiSuccessRate($days),
            'verified'  => $this->kpiVerified($days),
        ];
    }

    private function kpiUsers(int $days): array
    {
        $total = User::count();
        $before = User::where('created_at', '<', now()->subDays($days))->count();
        $delta = $total - $before;

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $spark[] = User::where('created_at', '<=', now()->subDays($i)->endOfDay())->count();
        }

        return [
            'label' => 'Nutzer gesamt',
            'value' => number_format($total, 0, ',', '.'),
            'delta' => $this->formatDelta($delta, 'abs', $days),
            'spark' => $spark,
            'color' => 'blue',
        ];
    }

    private function kpiWau(int $days): array
    {
        $current = QuestionStatistic::where('created_at', '>=', now()->subDays($days))
            ->distinct('user_id')
            ->count('user_id');
        $previous = QuestionStatistic::whereBetween('created_at', [now()->subDays($days * 2), now()->subDays($days)])
            ->distinct('user_id')
            ->count('user_id');

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $spark[] = QuestionStatistic::whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
                ->distinct('user_id')
                ->count('user_id');
        }

        return [
            'label' => $this->activeLabel($days),
            'value' => number_format($current, 0, ',', '.'),
            'delta' => $this->formatDelta($current - $previous, 'abs', $days),
            'spark' => $spark,
        ];
    }

    private function kpiAnswers(int $days): array
    {
        $current = QuestionStatistic::where('created_at', '>=', now()->subDays($days))->count();
        $previous = QuestionStatistic::whereBetween('created_at', [now()->subDays($days * 2), now()->subDays($days)])->count();

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $spark[] = QuestionStatistic::whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])->count();
        }

        return [
            'label' => 'Beantwortete Fragen',
            'value' => number_format($current, 0, ',', '.'),
            'delta' => $this->formatDelta($current - $previous, 'abs', $days),
            'spark' => $spark,
        ];
    }

    private function kpiSuccessRate(int $days): array
    {
        [$current, $currentRate] = $this->successRate(now()->subDays($days), now());
        [$previous, $previousRate] = $this->successRate(now()->subDays($days * 2), now()->subDays($days));
        $deltaPp = round($currentRate - $previousRate, 1);

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            [, $rate] = $this->successRate($day->copy()->startOfDay(), $day->copy()->endOfDay());
            $spark[] = $rate;
        }

        return [
            'label' => 'Erfolgsrate',
            'value' => number_format($currentRate, 1, ',', '.') . ' %',
            'delta' => $this->formatDelta($deltaPp, 'pp', $days),
            'spark' => $spark,
            'color' => $currentRate >= 50 ? 'ok' : null,
        ];
    }

    /** @return array{0:int,1:float} [total, rate] */
    private function successRate(Carbon $from, Carbon $to): array
    {
        $total = QuestionStatistic::whereBetween('created_at', [$from, $to])->count();
        if ($total === 0) {
            return [0, 0.0];
        }
        $correct = QuestionStatistic::whereBetween('created_at', [$from, $to])->where('is_correct', true)->count();
        return [$total, round(($correct / $total) * 100, 1)];
    }

    private function kpiVerified(int $days): array
    {
        $total = max(User::count(), 1);
        $verified = User::whereNotNull('email_verified_at')->count();
        $rate = round(($verified / $total) * 100, 1);

        $previousTotal = max(User::where('created_at', '<', now()->subDays($days))->count(), 1);
        $previousVerified = User::whereNotNull('email_verified_at')
            ->where('email_verified_at', '<', now()->subDays($days))
            ->count();
        $previousRate = round(($previousVerified / $previousTotal) * 100, 1);
        $deltaPp = round($rate - $previousRate, 1);

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $dayEnd = now()->subDays($i)->endOfDay();
            $tot = max(User::where('created_at', '<=', $dayEnd)->count(), 1);
            $ver = User::whereNotNull('email_verified_at')
                ->where('email_verified_at', '<=', $dayEnd)
                ->count();
            $spark[] = round(($ver / $tot) * 100, 1);
        }

        return [
            'label' => 'Verifiziert',
            'value' => number_format($rate, 1, ',', '.') . ' %',
            'delta' => $this->formatDelta($deltaPp, 'pp', $days),
            'spark' => $spark,
        ];
    }

    private function activeLabel(int $days): string
    {
        return match ($days) {
            1 => 'Aktiv 24 h',
            7 => 'Aktive Woche',
            30 => 'Aktiv 30 T',
            90 => 'Aktiv 90 T',
            default => 'Aktiv',
        };
    }

    /**
     * @param int|float $value
     * @param 'abs'|'pp' $type
     */
    private function formatDelta($value, string $type, int $days): array
    {
        $sign = $value > 0 ? '+' : ($value < 0 ? '−' : '');
        $abs = abs($value);

        if ($type === 'pp') {
            $label = $sign . number_format($abs, 1, ',', '.') . ' pp';
        } else {
            $label = $sign . number_format($abs, 0, ',', '.');
        }

        $direction = $value > 0 ? 'up' : ($value < 0 ? 'down' : 'flat');
        $suffix = $days === 1 ? ' · 24 h' : ($days === 7 ? ' · 7 T' : ($days === 30 ? ' · 30 T' : ' · 90 T'));

        return [
            'direction' => $direction,
            'text'      => $direction === 'flat' ? 'unverändert' : $label . $suffix,
        ];
    }

    /* =========================================================
       ACTIVITY CHARTS — abhängig vom gewählten Zeitraum
       ========================================================= */
    private function getActivityChart(int $days): array
    {
        $labels = [];
        $active = [];
        $answered = [];
        $correct = [];
        $wrong = [];

        $statTables = [
            'question_statistics',
            'lehrgang_question_statistics',
            'ortsverband_lernpool_question_statistics',
        ];

        $countAnswers = function ($from, $to) use ($statTables) {
            $total = 0;
            $correct = 0;
            $userIds = collect();
            foreach ($statTables as $t) {
                $rows = DB::table($t)
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('COUNT(*) AS total, SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) AS correct')
                    ->first();
                $total   += (int) ($rows->total ?? 0);
                $correct += (int) ($rows->correct ?? 0);
                $userIds = $userIds->merge(
                    DB::table($t)->whereBetween('created_at', [$from, $to])->pluck('user_id')
                );
            }
            return [
                'total'   => $total,
                'correct' => $correct,
                'active'  => $userIds->filter()->unique()->count(),
            ];
        };

        // Bei 24h zeigen wir Stunden, sonst Tage.
        if ($days <= 1) {
            for ($i = 23; $i >= 0; $i--) {
                $h = now()->subHours($i);
                $labels[] = $h->format('H') . ':00';
                $r = $countAnswers($h->copy()->startOfHour(), $h->copy()->endOfHour());
                $active[]   = $r['active'];
                $answered[] = $r['total'];
                $correct[]  = $r['correct'];
                $wrong[]    = $r['total'] - $r['correct'];
            }
        } else {
            for ($i = $days - 1; $i >= 0; $i--) {
                $d = now()->subDays($i);
                $labels[] = $d->format('d.m');
                $r = $countAnswers($d->copy()->startOfDay(), $d->copy()->endOfDay());
                $active[]   = $r['active'];
                $answered[] = $r['total'];
                $correct[]  = $r['correct'];
                $wrong[]    = $r['total'] - $r['correct'];
            }
        }

        $count = max(count($active), 1);
        $avgActive = round(array_sum($active) / $count, 1);
        $avgAnswered = round(array_sum($answered) / $count);
        $peakActive = max($active);
        $peakAnswered = max($answered);
        $peakActiveIndex = array_search($peakActive, $active, true);
        $peakAnsweredIndex = array_search($peakAnswered, $answered, true);

        $newUsersInRange = User::where('created_at', '>=', now()->subDays($days))->count();

        $totalCorrect = array_sum($correct);
        $totalWrong = array_sum($wrong);
        $totalAnswered = $totalCorrect + $totalWrong;

        // Retention im Zeitraum: Nutzer, die in der ersten Hälfte aktiv waren und in der zweiten wieder.
        $half = max(1, intdiv($days, 2));
        $firstHalfUsers = collect();
        $secondHalfUsers = collect();
        foreach ($statTables as $t) {
            $firstHalfUsers = $firstHalfUsers->merge(
                DB::table($t)->whereBetween('created_at', [now()->subDays($days), now()->subDays($half)])->pluck('user_id')
            );
            $secondHalfUsers = $secondHalfUsers->merge(
                DB::table($t)->where('created_at', '>=', now()->subDays($half))->pluck('user_id')
            );
        }
        $firstHalfUsers = $firstHalfUsers->filter()->unique();
        $secondHalfUsers = $secondHalfUsers->filter()->unique();
        $retained = $firstHalfUsers->intersect($secondHalfUsers)->count();
        $retention = $firstHalfUsers->count() > 0
            ? round(($retained / $firstHalfUsers->count()) * 100)
            : 0;

        return [
            'labels'       => $labels,
            'active'       => $active,
            'answered'     => $answered,
            'avgActive'    => $avgActive,
            'avgAnswered'  => $avgAnswered,
            'peakActive'   => $peakActive,
            'peakActiveLabel'   => $labels[$peakActiveIndex] ?? '',
            'peakAnswered' => $peakAnswered,
            'peakAnsweredLabel' => $labels[$peakAnsweredIndex] ?? '',
            'newUsers'     => $newUsersInRange,
            'retention'    => $retention,
            'totalCorrect' => $totalCorrect,
            'totalWrong'   => $totalWrong,
            'totalAnswered' => $totalAnswered,
        ];
    }

    /* =========================================================
       HANDLUNGSBEDARF — priorisierte Queue
       ========================================================= */
    private function getHandlungsbedarf(): array
    {
        $queue = [];

        $openBugs = QuestionIssue::where('status', 'open')->count()
                  + LehrgangQuestionIssue::where('status', 'open')->count();
        if ($openBugs > 0) {
            $newest = max(
                optional(QuestionIssue::where('status', 'open')->latest()->first())->created_at,
                optional(LehrgangQuestionIssue::where('status', 'open')->latest()->first())->created_at,
            );
            $oldest = min(
                optional(QuestionIssue::where('status', 'open')->oldest()->first())->created_at ?: now(),
                optional(LehrgangQuestionIssue::where('status', 'open')->oldest()->first())->created_at ?: now(),
            );
            $queue[] = [
                'count'  => $openBugs,
                'title'  => 'Fehlermeldungen',
                'sub'    => 'neueste ' . ($newest ? $newest->diffForHumans() : 'unbekannt')
                          . ' · älteste ' . ($oldest ? $oldest->diffForHumans() : 'unbekannt'),
                'link'   => route('admin.issues.index'),
                'variant' => 'red',
                'urgent' => $openBugs >= 3,
            ];
        }

        $unreadMessages = ContactMessage::where('is_read', false)->count();
        if ($unreadMessages > 0) {
            $newest = ContactMessage::where('is_read', false)->latest()->first();
            $queue[] = [
                'count'  => $unreadMessages,
                'title'  => 'Kontaktanfragen',
                'sub'    => 'unbeantwortet · neueste ' . ($newest ? $newest->created_at->diffForHumans() : ''),
                'link'   => route('admin.contact-messages.index'),
                'variant' => 'blue',
                'urgent' => false,
            ];
        }

        $feedback = ExamFeedback::whereNotNull('feedback')
            ->where('created_at', '>=', now()->subDays(14))
            ->count();
        if ($feedback > 0) {
            $queue[] = [
                'count'  => $feedback,
                'title'  => 'Prüfungs-Feedback',
                'sub'    => 'letzte 14 Tage · zur Sichtung',
                'link'   => route('admin.exam-feedback.index'),
                'variant' => 'gold',
                'urgent' => false,
            ];
        }

        $unverified = User::whereNull('email_verified_at')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        if ($unverified > 0) {
            $queue[] = [
                'count'  => $unverified,
                'title'  => 'Nutzer-Verifizierung',
                'sub'    => 'E-Mail nicht bestätigt · letzte 7 T',
                'link'   => route('admin.users.index') . '?filter=unverified',
                'variant' => 'green',
                'urgent' => false,
            ];
        }

        $pendingQuestions = Question::whereNull('loesung')->orWhere('loesung', '')->count();
        if ($pendingQuestions > 0) {
            $queue[] = [
                'count'  => $pendingQuestions,
                'title'  => 'Fragen ohne Lösung',
                'sub'    => 'Entwurf · warten auf Freigabe',
                'link'   => route('admin.questions.index'),
                'variant' => 'purp',
                'urgent' => false,
            ];
        }

        $pendingSubmissions = UserExtraQuestionSubmission::where('status', 'pending')->count();
        if ($pendingSubmissions > 0) {
            $newest = UserExtraQuestionSubmission::where('status', 'pending')->latest()->first();
            $queue[] = [
                'count'  => $pendingSubmissions,
                'title'  => 'Zusatz-Frage-Vorschläge',
                'sub'    => 'eingereicht · neueste ' . ($newest ? $newest->created_at->diffForHumans() : ''),
                'link'   => route('admin.extra-question-submissions.index', ['status' => 'pending']),
                'variant' => 'gold',
                'urgent' => false,
            ];
        }

        return $queue;
    }

    /* =========================================================
       FRAGEN-QUALITÄT
       ========================================================= */
    private function getFragenQualitaet(int $days): array
    {
        $from = now()->subDays($days);

        // Antworten aus ALLEN Quellen: Practice/Exam global + Lehrgang + Lernpool
        $sources = [
            ['table' => 'question_statistics'],
            ['table' => 'lehrgang_question_statistics'],
            ['table' => 'ortsverband_lernpool_question_statistics'],
        ];

        $totalAnswered = 0;
        $correct = 0;
        foreach ($sources as $src) {
            $totalAnswered += DB::table($src['table'])->where('created_at', '>=', $from)->count();
            $correct       += DB::table($src['table'])->where('created_at', '>=', $from)->where('is_correct', true)->count();
        }
        $wrong = $totalAnswered - $correct;
        $successRate = $totalAnswered > 0 ? round(($correct / $totalAnswered) * 100, 1) : 0;

        $totalFragen = Question::count();
        $entwuerfe = Question::whereNull('loesung')->orWhere('loesung', '')->count();

        // Top 3 "am häufigsten richtig" (nur Fragen mit >=10 Antworten, sortiert nach Erfolgsquote dann Volumen)
        // Bezieht sich auf die globale Fragen-Tabelle (Practice/Exam) – Lehrgang/Lernpool haben eigene Fragen-IDs.
        $topRichtig = $this->topQuestions('correct', $from);
        $topFalsch  = $this->topQuestions('wrong', $from);

        return [
            'correct'      => $correct,
            'wrong'        => $wrong,
            'totalAnswered' => $totalAnswered,
            'successRate'  => $successRate,
            'totalFragen'  => $totalFragen,
            'entwuerfe'    => $entwuerfe,
            'topRichtig'   => $topRichtig,
            'topFalsch'    => $topFalsch,
        ];
    }

    /**
     * @param 'correct'|'wrong' $mode
     */
    private function topQuestions(string $mode, Carbon $from): array
    {
        $rows = DB::table('question_statistics')
            ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
            ->where('question_statistics.created_at', '>=', $from)
            ->select(
                'questions.id',
                'questions.nummer',
                'questions.frage',
                'questions.lernabschnitt',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('SUM(CASE WHEN question_statistics.is_correct = 1 THEN 1 ELSE 0 END) as correct')
            )
            ->groupBy('questions.id', 'questions.nummer', 'questions.frage', 'questions.lernabschnitt')
            ->having('attempts', '>=', 10)
            ->get();

        $mapped = $rows->map(function ($r) {
            $rate = $r->attempts > 0 ? round(($r->correct / $r->attempts) * 100, 1) : 0;
            return [
                'id'            => $r->id,
                'nummer'        => $r->nummer,
                'frage'         => $this->shortenFrage($r->frage),
                'lernabschnitt' => $r->lernabschnitt,
                'attempts'      => (int) $r->attempts,
                'correct_rate'  => $rate,
                'wrong_rate'    => round(100 - $rate, 1),
            ];
        });

        if ($mode === 'correct') {
            return $mapped->sortByDesc('correct_rate')->take(3)->values()->all();
        }

        return $mapped->sortByDesc('wrong_rate')->take(3)->values()->all();
    }

    private function shortenFrage(?string $text): string
    {
        $text = trim(strip_tags($text ?? ''));
        if (mb_strlen($text) <= 40) {
            return $text;
        }
        return mb_substr($text, 0, 37) . '…';
    }

    /* =========================================================
       ORTSVERBÄNDE
       ========================================================= */
    private function getOrtsverbaende(): array
    {
        try {
            $rows = DB::table('ortsverbände')
                ->leftJoin('ortsverband_members', 'ortsverbände.id', '=', 'ortsverband_members.ortsverband_id')
                ->leftJoin('users', 'ortsverband_members.user_id', '=', 'users.id')
                ->select(
                    'ortsverbände.id',
                    'ortsverbände.name',
                    DB::raw('COUNT(DISTINCT ortsverband_members.user_id) as member_count'),
                    DB::raw('MAX(users.last_activity_date) as last_activity')
                )
                ->groupBy('ortsverbände.id', 'ortsverbände.name')
                ->having('member_count', '>', 0)
                ->orderByDesc('member_count')
                ->orderBy('ortsverbände.name')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $rows = collect();
        }

        $list = $rows->map(function ($row) {
            $lastActivity = $row->last_activity
                ? Carbon::parse($row->last_activity)->diffForHumans()
                : 'noch keine Aktivität';
            return [
                'name'         => $row->name,
                'members'      => (int) $row->member_count,
                'last_activity' => $lastActivity,
            ];
        })->all();

        $activeOvCount = Ortsverband::query()
            ->whereHas('members')
            ->count();
        $totalOvMembers = DB::table('ortsverband_members')->distinct('user_id')->count('user_id');
        $avgPerOv = $activeOvCount > 0 ? round($totalOvMembers / $activeOvCount, 1) : 0;

        return [
            'summary' => [
                'active' => $activeOvCount,
                'users'  => $totalOvMembers,
                'avg'    => number_format($avgPerOv, 1, ',', '.'),
            ],
            'list' => $list,
        ];
    }

    /* =========================================================
       SPACED REPETITION (gleiche Daten wie zuvor)
       ========================================================= */
    private function getSpacedRepetitionStats(): array
    {
        $totalInSr = UserQuestionProgress::where('review_interval', '>', 0)->count();
        $mastered = UserQuestionProgress::whereNull('next_review_at')
            ->where('consecutive_correct', '>=', 3)
            ->count();

        $dueToday = UserQuestionProgress::whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now()->endOfDay())
            ->count();

        $activeUsers = UserQuestionProgress::whereNotNull('next_review_at')
            ->distinct('user_id')
            ->count('user_id');

        $dueTomorrow = UserQuestionProgress::whereNotNull('next_review_at')
            ->whereBetween('next_review_at', [now()->addDay()->startOfDay(), now()->addDay()->endOfDay()])
            ->count();

        $dueThisWeek = UserQuestionProgress::whereNotNull('next_review_at')
            ->whereBetween('next_review_at', [now(), now()->endOfWeek()])
            ->count();

        $masteryTotal = $totalInSr + $mastered;
        $masteryRate = $masteryTotal > 0 ? round(($mastered / $masteryTotal) * 100, 1) : 0;

        $avgEasiness = round((float) UserQuestionProgress::where('review_interval', '>', 0)->avg('easiness_factor'), 2);

        $dist = [
            '1_3'     => UserQuestionProgress::whereBetween('review_interval', [1, 3])->count(),
            '4_7'     => UserQuestionProgress::whereBetween('review_interval', [4, 7])->count(),
            '8_14'    => UserQuestionProgress::whereBetween('review_interval', [8, 14])->count(),
            '15_plus' => UserQuestionProgress::where('review_interval', '>', 14)->count(),
        ];
        $distSum = max(array_sum($dist), 1);
        $distPct = [
            '1_3'     => round(($dist['1_3'] / $distSum) * 100),
            '4_7'     => round(($dist['4_7'] / $distSum) * 100),
            '8_14'    => round(($dist['8_14'] / $distSum) * 100),
            '15_plus' => round(($dist['15_plus'] / $distSum) * 100),
        ];

        return [
            'active_users'  => $activeUsers,
            'mastered'      => $mastered,
            'due_today'     => $dueToday,
            'due_tomorrow'  => $dueTomorrow,
            'due_this_week' => $dueThisWeek,
            'total_in_sr'   => $totalInSr,
            'mastery_rate'  => $masteryRate,
            'avg_easiness'  => $avgEasiness,
            'dist'          => $dist,
            'dist_pct'      => $distPct,
            'cards_total'   => $distSum,
        ];
    }

    /* =========================================================
       LIVE-FEED — DSGVO-konform anonymisiert
       ========================================================= */
    private function getLiveFeed(): array
    {
        $events = collect();

        // 1. Neue Registrierungen (anonymisiert: nur OV oder Region)
        User::where('created_at', '>=', now()->subDays(2))
            ->latest()
            ->limit(8)
            ->get(['id', 'created_at'])
            ->each(function ($u) use (&$events) {
                $ov = DB::table('ortsverband_members')
                    ->join('ortsverbände', 'ortsverband_members.ortsverband_id', '=', 'ortsverbände.id')
                    ->where('ortsverband_members.user_id', $u->id)
                    ->value('ortsverbände.name');

                $events->push([
                    'icon'  => 'bi-person-plus-fill',
                    'color' => 'green',
                    'text'  => '<strong>Neue Registrierung</strong>'
                              . ($ov ? ' <span class="muted">· OV ' . e($ov) . '</span>' : ''),
                    'time'  => $u->created_at,
                    'tag'   => 'Nutzer',
                ]);
            });

        // 2. Bestandene Prüfungen (anonymisiert: nur Ergebnis)
        ExamStatistic::where('is_passed', true)
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->limit(5)
            ->get()
            ->each(function ($e) use (&$events) {
                $events->push([
                    'icon'  => 'bi-check-circle-fill',
                    'color' => 'blue',
                    'text'  => '<strong>Prüfung bestanden</strong> <span class="muted">· '
                              . ($e->correct_answers ?? 0) . '/40 Fragen</span>',
                    'time'  => $e->created_at,
                    'tag'   => 'Prüfung',
                ]);
            });

        // 3. Nicht bestandene Prüfungen
        ExamStatistic::where('is_passed', false)
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($e) use (&$events) {
                $events->push([
                    'icon'  => 'bi-x-circle-fill',
                    'color' => 'red',
                    'text'  => '<span class="muted">Prüfung</span> <strong>nicht bestanden</strong> <span class="muted">· '
                              . ($e->correct_answers ?? 0) . '/40</span>',
                    'time'  => $e->created_at,
                    'tag'   => 'Prüfung',
                ]);
            });

        // 4. Bug-Reports (Betreff ohne Autor)
        QuestionIssue::where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($i) use (&$events) {
                $events->push([
                    'icon'  => 'bi-bug-fill',
                    'color' => 'red',
                    'text'  => '<span class="muted">Neue Fehlermeldung:</span> <strong>Frage #'
                              . ($i->question_id ?? '–') . '</strong>',
                    'time'  => $i->created_at,
                    'tag'   => 'Bug',
                ]);
            });

        LehrgangQuestionIssue::where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($i) use (&$events) {
                $events->push([
                    'icon'  => 'bi-bug-fill',
                    'color' => 'red',
                    'text'  => '<span class="muted">Lehrgang-Fehler:</span> <strong>Issue #' . $i->id . '</strong>',
                    'time'  => $i->created_at,
                    'tag'   => 'Bug',
                ]);
            });

        // 5. Prüfungs-Feedback
        ExamFeedback::whereNotNull('feedback')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($f) use (&$events) {
                $mode = $f->publish_mode === 'named' ? 'namentlich' : 'anonym';
                $events->push([
                    'icon'  => 'bi-chat-square-text-fill',
                    'color' => 'purple',
                    'text'  => '<strong>Prüfungs-Feedback</strong> <span class="muted">· ' . $mode . '</span>',
                    'time'  => $f->created_at,
                    'tag'   => 'Feedback',
                ]);
            });

        // 6. Kontaktanfragen (Betreff, ohne Autor-Namen)
        ContactMessage::where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($m) use (&$events) {
                $subject = $m->subject ?? 'Kontaktanfrage';
                $events->push([
                    'icon'  => 'bi-envelope-open-fill',
                    'color' => 'blue',
                    'text'  => '<span class="muted">Kontaktanfrage:</span> <strong>' . e(mb_strimwidth($subject, 0, 48, '…')) . '</strong>',
                    'time'  => $m->created_at,
                    'tag'   => 'Kontakt',
                ]);
            });

        return $events
            ->sortByDesc('time')
            ->take(12)
            ->values()
            ->map(function ($e) {
                $e['time_human'] = Carbon::parse($e['time'])->diffForHumans(null, true, true);
                return $e;
            })
            ->all();
    }
}
