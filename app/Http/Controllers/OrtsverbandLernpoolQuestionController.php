<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class OrtsverbandLernpoolQuestionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Zeige alle Fragen eines Lernpools
     */
    public function index(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('view', [$lernpool, $ortsverband]);
        
        $questions = $lernpool->questions()->with('creator')->get();
        
        $data = [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'questions' => $questions,
        ];
        
        // Wenn AJAX-Request, gib nur Modal-Inhalt zurück
        $isAjax = request()->ajax() || 
                  request()->header('X-Requested-With') === 'XMLHttpRequest' || 
                  request()->query('ajax') === '1' ||
                  request()->input('ajax') === '1';
        
        if ($isAjax) {
            return view('ortsverband.lernpools.questions.index-modal', $data);
        }
        
        return view('ortsverband.lernpools.questions.index', $data);
    }

    /**
     * Zeige Formular zum Erstellen einer Frage
     */
    public function create(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        
        // Hole alle existierenden Fragen für Nummerierung
        $questions = $lernpool->questions()->get();
        
        // Existierende Lernabschnitte für Autocomplete
        $existingSections = $questions->pluck('lernabschnitt')->unique()->filter()->values();
        
        // Höchste Nummer pro Lernabschnitt
        $sectionNumbers = $questions->groupBy('lernabschnitt')
            ->map(fn($q) => $q->max('nummer'))
            ->toArray();
        
        // Nächste globale Nummer
        $nextNumber = $questions->max('nummer') ? $questions->max('nummer') + 1 : 1;
        
        $data = [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'existingSections' => $existingSections,
            'sectionNumbers' => $sectionNumbers,
            'nextNumber' => $nextNumber,
        ];
        
        // Wenn AJAX-Request, gib nur Modal-Inhalt zurück
        $isAjax = request()->ajax() || 
                  request()->header('X-Requested-With') === 'XMLHttpRequest' || 
                  request()->query('ajax') === '1' ||
                  request()->input('ajax') === '1';
        
        if ($isAjax) {
            return view('ortsverband.lernpools.questions.create-modal', $data);
        }
        
        return view('ortsverband.lernpools.questions.create', $data);
    }

    /**
     * Speichere eine neue Frage
     */
    public function store(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);

        $validated = $request->validate([
            'lernabschnitt' => 'nullable|string|max:255',
            'nummer' => 'required|integer|min:1',
            'frage' => 'required|string|max:1000',
            'antwort_a' => 'required|string|max:255',
            'antwort_b' => 'required|string|max:255',
            'antwort_c' => 'required|string|max:255',
            'loesung' => 'required|array|min:1', // Array von Lösungen
            'loesung.*' => 'in:a,b,c',
        ]);

        // Konvertiere Array zu String (z.B. "a,b")
        $validated['loesung'] = implode(',', $validated['loesung']);

        OrtsverbandLernpoolQuestion::create([
            'lernpool_id' => $lernpool->id,
            'created_by' => auth()->id(),
            ...$validated,
        ]);

        // Bei AJAX-Request: JSON zurückgeben
        $isAjax = $request->ajax() ||
                  $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                  $request->expectsJson();

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'Frage erfolgreich hinzugefügt!'
            ]);
        }

        return redirect()
            ->route('ortsverband.lernpools.index', $ortsverband)
            ->with('success', 'Frage hinzugefügt!');
    }

    /**
     * Zeige Bearbeitungsformular für Frage
     */
    public function edit(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool, OrtsverbandLernpoolQuestion $question)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        $this->authorize('view', $question);
        
        return view('ortsverband.lernpools.questions.edit', [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'question' => $question,
        ]);
    }

    /**
     * Aktualisiere eine Frage
     */
    public function update(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool, OrtsverbandLernpoolQuestion $question)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        $this->authorize('update', $question);
        
        $validated = $request->validate([
            'lernabschnitt' => 'nullable|string|max:255',
            'nummer' => 'nullable|integer|min:1',
            'frage' => 'required|string|max:1000',
            'antwort_a' => 'required|string|max:255',
            'antwort_b' => 'required|string|max:255',
            'antwort_c' => 'required|string|max:255',
            'loesung' => 'required|string|max:10',
        ]);

        $question->update($validated);

        return redirect()
            ->route('ortsverband.lernpools.questions.index', [$ortsverband, $lernpool])
            ->with('success', 'Frage aktualisiert!');
    }

    /**
     * Lösche eine Frage
     */
    public function destroy(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool, OrtsverbandLernpoolQuestion $question)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        $this->authorize('delete', $question);
        
        $question->delete();

        return redirect()
            ->route('ortsverband.lernpools.questions.index', [$ortsverband, $lernpool])
            ->with('success', 'Frage gelöscht!');
    }
}
