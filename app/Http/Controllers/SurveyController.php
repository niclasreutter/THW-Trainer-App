<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $survey = Survey::active()->first();

        if (!$survey) {
            return redirect()->route('dashboard')->with('error', 'Aktuell gibt es keine aktive Umfrage.');
        }

        $existingResponse = $survey->responses()
            ->where('user_id', auth()->id())
            ->first();

        return view('survey', [
            'survey' => $survey,
            'existingResponse' => $existingResponse,
        ]);
    }

    public function store(Request $request)
    {
        $survey = Survey::active()->firstOrFail();

        // Check if user already responded
        $exists = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return redirect()->route('umfrage.index')->with('error', 'Du hast an dieser Umfrage bereits teilgenommen.');
        }

        $validated = $request->validate([
            'rating_overall'   => 'required|integer|min:1|max:5',
            'rating_usability' => 'required|integer|min:1|max:5',
            'rating_design'    => 'required|integer|min:1|max:5',
            'found_via'        => 'required|in:empfehlung,google,social_media,thw_ausbildung,sonstiges',
            'feedback_general' => 'nullable|string|max:2000',
            'feedback_wishes'  => 'nullable|string|max:2000',
            'feedback_changes' => 'nullable|string|max:2000',
            'hermine_interest' => 'required|in:ja,nein,unknown',
            'publish_mode'     => 'required|in:name,anonymous,private',
            'consent'          => 'accepted',
        ], [
            'rating_overall.required'   => 'Bitte bewerte den THW Trainer insgesamt.',
            'rating_usability.required' => 'Bitte bewerte die Benutzerfreundlichkeit.',
            'rating_design.required'    => 'Bitte bewerte das Design.',
            'found_via.required'        => 'Bitte waehle aus, wie du uns gefunden hast.',
            'hermine_interest.required' => 'Bitte beantworte die Frage zur Hermine-Gruppe.',
            'publish_mode.required'     => 'Bitte waehle eine Veroeffentlichungs-Option.',
            'consent.accepted'          => 'Bitte stimme der Datenverarbeitung zu.',
        ]);

        // XSS protection
        $validated['feedback_general'] = $validated['feedback_general'] ? strip_tags($validated['feedback_general']) : null;
        $validated['feedback_wishes'] = $validated['feedback_wishes'] ? strip_tags($validated['feedback_wishes']) : null;
        $validated['feedback_changes'] = $validated['feedback_changes'] ? strip_tags($validated['feedback_changes']) : null;

        SurveyResponse::create([
            'survey_id'        => $survey->id,
            'user_id'          => auth()->id(),
            'rating_overall'   => $validated['rating_overall'],
            'rating_usability' => $validated['rating_usability'],
            'rating_design'    => $validated['rating_design'],
            'found_via'        => $validated['found_via'],
            'feedback_general' => $validated['feedback_general'],
            'feedback_wishes'  => $validated['feedback_wishes'],
            'feedback_changes' => $validated['feedback_changes'],
            'hermine_interest' => $validated['hermine_interest'],
            'publish_mode'     => $validated['publish_mode'],
            'consent_given'    => true,
            'consent_given_at' => now(),
        ]);

        return redirect()->route('umfrage.index')->with('success', 'Vielen Dank fuer dein Feedback!');
    }

    public function destroy(SurveyResponse $survey_response)
    {
        if ($survey_response->user_id !== auth()->id()) {
            abort(403);
        }

        $survey_response->delete();

        return redirect()->route('dashboard')->with('success', 'Deine Umfrage-Antwort wurde geloescht.');
    }
}
