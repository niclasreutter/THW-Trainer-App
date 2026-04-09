<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $surveys = Survey::withCount('responses')->latest()->get();
        $activeSurvey = $surveys->firstWhere('is_active', true);

        $stats = [];
        $chartData = [];
        $responses = collect();

        if ($activeSurvey) {
            $allResponses = $activeSurvey->responses()->with('user')->latest()->get();

            $stats = [
                'total' => $allResponses->count(),
                'avg_overall' => round($allResponses->avg('rating_overall'), 1),
                'avg_usability' => round($allResponses->avg('rating_usability'), 1),
                'avg_design' => round($allResponses->avg('rating_design'), 1),
                'hermine_ja' => $allResponses->where('hermine_interest', 'ja')->count(),
                'hermine_nein' => $allResponses->where('hermine_interest', 'nein')->count(),
                'hermine_unknown' => $allResponses->where('hermine_interest', 'unknown')->count(),
            ];

            // Rating distribution for charts
            $chartData = [
                'overall' => $this->ratingDistribution($allResponses, 'rating_overall'),
                'usability' => $this->ratingDistribution($allResponses, 'rating_usability'),
                'design' => $this->ratingDistribution($allResponses, 'rating_design'),
                'found_via' => [
                    'empfehlung' => $allResponses->where('found_via', 'empfehlung')->count(),
                    'google' => $allResponses->where('found_via', 'google')->count(),
                    'social_media' => $allResponses->where('found_via', 'social_media')->count(),
                    'thw_ausbildung' => $allResponses->where('found_via', 'thw_ausbildung')->count(),
                    'sonstiges' => $allResponses->where('found_via', 'sonstiges')->count(),
                ],
                'hermine' => [
                    'ja' => $stats['hermine_ja'],
                    'nein' => $stats['hermine_nein'],
                    'unknown' => $stats['hermine_unknown'],
                ],
            ];

            $responses = $allResponses;
        }

        return view('admin.surveys', compact('surveys', 'activeSurvey', 'stats', 'chartData', 'responses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $lastVersion = Survey::max('version') ?? 0;

        Survey::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'version' => $lastVersion + 1,
            'is_active' => false,
        ]);

        return back()->with('success', 'Neue Umfrage erstellt.');
    }

    public function toggle(Survey $survey)
    {
        if (!$survey->is_active) {
            // Deactivate all others first
            Survey::where('is_active', true)->update(['is_active' => false]);
        }

        $survey->update(['is_active' => !$survey->is_active]);

        return back()->with('success', $survey->is_active ? 'Umfrage aktiviert.' : 'Umfrage deaktiviert.');
    }

    public function export(Request $request): StreamedResponse
    {
        $surveyId = $request->query('survey_id');
        $query = SurveyResponse::with(['user', 'survey']);

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        $responses = $query->latest()->get();

        return response()->streamDownload(function () use ($responses) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($handle, [
                'ID', 'Umfrage', 'User', 'Gesamt', 'Bedienbarkeit', 'Design',
                'Gefunden ueber', 'Feedback', 'Wuensche', 'Aenderungen',
                'Hermine', 'Veroeffentlichung', 'Datum',
            ], ';');

            foreach ($responses as $r) {
                $userName = match($r->publish_mode) {
                    'name' => $r->user->name ?? 'Geloescht',
                    'anonymous' => 'Anonym',
                    default => '[Privat]',
                };

                fputcsv($handle, [
                    $r->id,
                    $r->survey->title ?? '-',
                    $userName,
                    $r->rating_overall,
                    $r->rating_usability,
                    $r->rating_design,
                    $r->found_via,
                    $r->feedback_general ?? '-',
                    $r->feedback_wishes ?? '-',
                    $r->feedback_changes ?? '-',
                    $r->hermine_interest,
                    $r->publish_mode,
                    $r->created_at->format('d.m.Y H:i'),
                ], ';');
            }

            fclose($handle);
        }, 'umfrage-export-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function ratingDistribution($responses, string $field): array
    {
        $dist = [];
        for ($i = 1; $i <= 5; $i++) {
            $dist[$i] = $responses->where($field, $i)->count();
        }
        return $dist;
    }
}
