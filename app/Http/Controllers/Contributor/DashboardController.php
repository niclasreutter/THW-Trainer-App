<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ExtraQuestion;
use App\Models\Lehrgang;
use App\Models\LehrgangQuestionIssue;
use App\Models\Question;
use App\Models\QuestionIssue;
use App\Models\UserExtraQuestionSubmission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('contributor.dashboard', [
            'stats'              => $this->getStats(),
            'handlungsbedarf'    => $this->getHandlungsbedarf(),
            'recentSubmissions'  => $this->getRecentSubmissions(),
            'submissionStats'    => $this->getSubmissionStats(),
            'topFalsch'          => $this->getTopQuestions('wrong'),
            'topRichtig'         => $this->getTopQuestions('correct'),
            'recentIssues'       => $this->getRecentIssues(),
        ]);
    }

    private function getStats(): array
    {
        return [
            'fragen'         => Question::count(),
            'extraFragen'    => ExtraQuestion::count(),
            'lehrgaenge'     => Lehrgang::count(),
            'entwuerfe'      => Question::whereNull('loesung')->orWhere('loesung', '')->count(),
        ];
    }

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
            $queue[] = [
                'count'  => $openBugs,
                'title'  => 'Fehlermeldungen',
                'sub'    => 'offen · neueste ' . ($newest ? $newest->diffForHumans() : 'unbekannt'),
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

    private function getRecentSubmissions(int $limit = 6): array
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
                    'created_human' => $s->created_at->diffForHumans(),
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

    /**
     * Top 3 Fragen nach Korrekt-/Falsch-Quote (mindestens 10 Versuche, letzte 30 Tage).
     *
     * @param 'correct'|'wrong' $mode
     */
    private function getTopQuestions(string $mode): array
    {
        $from = now()->subDays(30);

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
        if (mb_strlen($text) <= 60) {
            return $text;
        }
        return mb_substr($text, 0, 57) . '…';
    }

    private function getRecentIssues(int $limit = 4): array
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
                    'created_human' => $i->created_at->diffForHumans(),
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
                    'created_human' => $i->created_at->diffForHumans(),
                ];
            });

        return $issues->merge($lehrgangIssues)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->all();
    }
}
