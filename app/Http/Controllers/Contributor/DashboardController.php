<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ExtraQuestion;
use App\Models\Lehrgang;
use App\Models\LehrgangQuestionIssue;
use App\Models\Question;
use App\Models\QuestionIssue;
use App\Models\UserExtraQuestionSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('contributor.dashboard', [
            'statusBar'         => $this->getStatusBar(),
            'kpis'              => $this->getKpis(),
            'handlungsbedarf'   => $this->getHandlungsbedarf(),
            'recentSubmissions' => $this->getRecentSubmissions(),
            'submissionStats'   => $this->getSubmissionStats(),
            'fragenQualitaet'   => $this->getFragenQualitaet(),
            'recentIssues'      => $this->getRecentIssues(),
        ]);
    }

    /* =========================================================
       STATUS BAR — analog zur System-Puls Bar im Admin Dashboard
       ========================================================= */
    private function getStatusBar(): array
    {
        $totalFragen      = Question::count();
        $entwuerfe        = Question::whereNull('loesung')->orWhere('loesung', '')->count();
        $totalExtra       = ExtraQuestion::count();
        $pendingSubmissions = UserExtraQuestionSubmission::where('status', 'pending')->count();
        $openIssues       = QuestionIssue::where('status', 'open')->count()
                          + LehrgangQuestionIssue::where('status', 'open')->count();
        $totalLehrgaenge  = Lehrgang::count();

        return [
            'fragen' => [
                'status' => $entwuerfe > 0 ? 'warn' : 'ok',
                'label'  => 'Fragen-Pool',
                'value'  => number_format($totalFragen, 0, ',', '.'),
                'sub'    => $entwuerfe . ' ' . ($entwuerfe === 1 ? 'Entwurf' : 'Entwürfe'),
                'link'   => route('admin.questions.index'),
            ],
            'zusatz' => [
                'status' => $pendingSubmissions >= 3 ? 'warn' : 'ok',
                'label'  => 'Zusatz-Fragen',
                'value'  => number_format($totalExtra, 0, ',', '.'),
                'sub'    => $pendingSubmissions . ' ' . ($pendingSubmissions === 1 ? 'Vorschlag offen' : 'Vorschläge offen'),
                'link'   => route('admin.extra-question-submissions.index', ['status' => 'pending']),
            ],
            'lehrgaenge' => [
                'status' => 'ok',
                'label'  => 'Lehrgänge',
                'value'  => number_format($totalLehrgaenge, 0, ',', '.'),
                'sub'    => 'Kurse zum Bearbeiten',
                'link'   => route('admin.lehrgaenge.index'),
            ],
            'issues' => [
                'status' => $openIssues === 0 ? 'ok' : ($openIssues >= 5 ? 'err' : 'warn'),
                'label'  => 'Fehlermeldungen',
                'value'  => $openIssues === 0 ? 'Keine offen' : $openIssues . ' offen',
                'sub'    => 'Issues zur Bearbeitung',
                'link'   => route('admin.issues.index'),
            ],
        ];
    }

    /* =========================================================
       KPI CARDS — Wert + Sparkline (letzte 14 Tage Trend)
       ========================================================= */
    private function getKpis(): array
    {
        return [
            'fragen'     => $this->kpiFragen(),
            'extra'      => $this->kpiExtra(),
            'lehrgaenge' => $this->kpiLehrgaenge(),
            'entwuerfe'  => $this->kpiEntwuerfe(),
            'vorschlaege' => $this->kpiVorschlaege(),
        ];
    }

    private function kpiFragen(): array
    {
        $total = Question::count();
        $before = Question::where('created_at', '<', now()->subDays(30))->count();

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $spark[] = Question::where('created_at', '<=', now()->subDays($i)->endOfDay())->count();
        }

        return [
            'label' => 'Fragen gesamt',
            'value' => number_format($total, 0, ',', '.'),
            'delta' => $this->formatDelta($total - $before),
            'spark' => $spark,
            'color' => 'blue',
        ];
    }

    private function kpiExtra(): array
    {
        $total = ExtraQuestion::count();
        $before = ExtraQuestion::where('created_at', '<', now()->subDays(30))->count();

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $spark[] = ExtraQuestion::where('created_at', '<=', now()->subDays($i)->endOfDay())->count();
        }

        return [
            'label' => 'Zusatz-Fragen',
            'value' => number_format($total, 0, ',', '.'),
            'delta' => $this->formatDelta($total - $before),
            'spark' => $spark,
        ];
    }

    private function kpiLehrgaenge(): array
    {
        $total = Lehrgang::count();

        $spark = array_fill(0, 14, $total);

        return [
            'label' => 'Lehrgänge',
            'value' => number_format($total, 0, ',', '.'),
            'delta' => $this->formatDelta(0),
            'spark' => $spark,
        ];
    }

    private function kpiEntwuerfe(): array
    {
        $current = Question::whereNull('loesung')->orWhere('loesung', '')->count();

        $spark = array_fill(0, 14, $current);

        return [
            'label' => 'Entwürfe',
            'value' => number_format($current, 0, ',', '.'),
            'delta' => $this->formatDelta(0),
            'spark' => $spark,
            'color' => $current === 0 ? 'ok' : null,
        ];
    }

    private function kpiVorschlaege(): array
    {
        $pending = UserExtraQuestionSubmission::where('status', 'pending')->count();

        $spark = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $spark[] = UserExtraQuestionSubmission::where('created_at', '<=', $day->endOfDay())
                ->where('status', 'pending')
                ->count();
        }

        $previous = UserExtraQuestionSubmission::where('created_at', '<=', now()->subDays(7)->endOfDay())
            ->where('status', 'pending')
            ->count();

        return [
            'label' => 'Offene Vorschläge',
            'value' => number_format($pending, 0, ',', '.'),
            'delta' => $this->formatDelta($pending - $previous),
            'spark' => $spark,
        ];
    }

    private function formatDelta(int $value): array
    {
        $sign = $value > 0 ? '+' : ($value < 0 ? '−' : '');
        $abs = abs($value);
        $direction = $value > 0 ? 'up' : ($value < 0 ? 'down' : 'flat');

        return [
            'direction' => $direction,
            'text'      => $direction === 'flat' ? 'unverändert' : $sign . number_format($abs, 0, ',', '.') . ' · 30 T',
        ];
    }

    /* =========================================================
       HANDLUNGSBEDARF — priorisierte Queue (nur Contributor-Themen)
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

        return $queue;
    }

    /* =========================================================
       RECENT SUBMISSIONS
       ========================================================= */
    private function getRecentSubmissions(int $limit = 8): array
    {
        return UserExtraQuestionSubmission::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($s) {
                return [
                    'id'            => $s->id,
                    'frage'         => $s->frage,
                    'typ'           => $s->typLabel(),
                    'lernabschnitt' => $s->lernabschnitt,
                    'status'        => $s->status,
                    'status_label'  => $s->statusLabel(),
                    'user_name'     => $s->user->name ?? 'Unbekannt',
                    'created_human' => $s->created_at->diffForHumans(null, true, true),
                ];
            })
            ->all();
    }

    private function getSubmissionStats(): array
    {
        return [
            'total'    => UserExtraQuestionSubmission::count(),
            'pending'  => UserExtraQuestionSubmission::where('status', 'pending')->count(),
            'approved' => UserExtraQuestionSubmission::where('status', 'approved')->count(),
            'changed'  => UserExtraQuestionSubmission::where('status', 'changed')->count(),
            'rejected' => UserExtraQuestionSubmission::where('status', 'rejected')->count(),
        ];
    }

    /* =========================================================
       FRAGEN-QUALITÄT — analog Admin Dashboard
       ========================================================= */
    private function getFragenQualitaet(): array
    {
        $from = now()->subDays(30);

        $sources = [
            'question_statistics',
            'lehrgang_question_statistics',
            'ortsverband_lernpool_question_statistics',
        ];

        $totalAnswered = 0;
        $correct = 0;
        foreach ($sources as $table) {
            $totalAnswered += DB::table($table)->where('created_at', '>=', $from)->count();
            $correct       += DB::table($table)->where('created_at', '>=', $from)->where('is_correct', true)->count();
        }
        $wrong = $totalAnswered - $correct;
        $successRate = $totalAnswered > 0 ? round(($correct / $totalAnswered) * 100, 1) : 0;

        $totalFragen = Question::count();
        $entwuerfe = Question::whereNull('loesung')->orWhere('loesung', '')->count();

        return [
            'correct'      => $correct,
            'wrong'        => $wrong,
            'totalAnswered' => $totalAnswered,
            'successRate'  => $successRate,
            'totalFragen'  => $totalFragen,
            'entwuerfe'    => $entwuerfe,
            'topRichtig'   => $this->topQuestions('correct', $from),
            'topFalsch'    => $this->topQuestions('wrong', $from),
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
       RECENT ISSUES
       ========================================================= */
    private function getRecentIssues(int $limit = 6): array
    {
        $issues = QuestionIssue::with('question')
            ->where('status', 'open')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($i) {
                return [
                    'id'            => $i->id,
                    'kind'          => 'question',
                    'title'         => 'Frage #' . ($i->question_id ?? '–'),
                    'frage'         => optional($i->question)->frage
                        ? mb_substr(strip_tags($i->question->frage), 0, 80)
                        : null,
                    'created_at'    => $i->created_at,
                    'created_human' => $i->created_at->diffForHumans(null, true, true),
                ];
            });

        $lehrgangIssues = LehrgangQuestionIssue::latest()
            ->where('status', 'open')
            ->limit($limit)
            ->get()
            ->map(function ($i) {
                return [
                    'id'            => $i->id,
                    'kind'          => 'lehrgang',
                    'title'         => 'Lehrgang-Issue #' . $i->id,
                    'frage'         => null,
                    'created_at'    => $i->created_at,
                    'created_human' => $i->created_at->diffForHumans(null, true, true),
                ];
            });

        return $issues->merge($lehrgangIssues)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->all();
    }
}
