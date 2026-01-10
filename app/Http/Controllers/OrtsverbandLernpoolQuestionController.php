<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use Illuminate\Http\Request;

class OrtsverbandLernpoolQuestionController extends Controller
{
    /**
     * Zeige alle Fragen eines Lernpools
     */
    public function index(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('view', [$lernpool, $ortsverband]);
        
        $questions = $lernpool->questions()->with('creator')->get();
        
        return view('ortsverband.lernpools.questions.index', [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'questions' => $questions,
        ]);
    }

    /**
     * Zeige Formular zum Erstellen einer Frage
     */
    public function create(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        
        return view('ortsverband.lernpools.questions.create', [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
        ]);
    }

    /**
     * Speichere eine neue Frage
     */
    public function store(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        
        $validated = $request->validate([
            'lernabschnitt' => 'nullable|string|max:255',
            'nummer' => 'nullable|integer|min:1',
            'frage' => 'required|string|max:1000',
            'antwort_a' => 'required|string|max:255',
            'antwort_b' => 'required|string|max:255',
            'antwort_c' => 'required|string|max:255',
            'loesung' => 'required|string|max:10', // z.B. "A,C"
        ]);

        OrtsverbandLernpoolQuestion::create([
            'lernpool_id' => $lernpool->id,
            'created_by' => auth()->id(),
            ...$validated,
        ]);

        return redirect()
            ->route('ortsverband.lernpools.questions.index', [$ortsverband, $lernpool])
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
