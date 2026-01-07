<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    private function abortIfNotAdmin()
    {
        if (!auth()->check() || auth()->user()->useroll !== 'admin') {
            abort(403, 'Kein Zugriff');
        }
    }
    public function index()
    {
        $this->abortIfNotAdmin();
        $questions = Question::all();
        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        $this->abortIfNotAdmin();
        return view('admin.questions.create');
    }

    public function store(Request $request)
    {
        $this->abortIfNotAdmin();
        $request->validate([
            'lernabschnitt' => 'required|string',
            'nummer' => 'required|integer',
            'frage' => 'required|string',
            'antwort_a' => 'required|string',
            'antwort_b' => 'required|string',
            'antwort_c' => 'required|string',
            'loesung' => 'required|array|min:1',
            'loesung.*' => 'in:A,B,C',
        ]);
        
        $data = $request->all();
        // Convert array to comma-separated string
        $data['loesung'] = implode(',', $request->input('loesung', []));
        
        Question::create($data);
        return redirect()->route('admin.questions.index')->with('success', 'Frage erfolgreich erstellt!');
    }

    public function edit(Question $question)
    {
        $this->abortIfNotAdmin();
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $this->abortIfNotAdmin();
        $request->validate([
            'lernabschnitt' => 'required|string',
            'nummer' => 'required|integer',
            'frage' => 'required|string',
            'antwort_a' => 'required|string',
            'antwort_b' => 'required|string',
            'antwort_c' => 'required|string',
            'loesung' => 'required|string',
        ]);
        $question->update($request->all());
        return redirect()->route('admin.questions.index');
    }

    public function destroy(Question $question)
    {
        $this->abortIfNotAdmin();
        $question->delete();
        return redirect()->route('admin.questions.index');
    }

    public function updateField(Request $request, Question $question)
    {
        $this->abortIfNotAdmin();
        
        $field = $request->input('field');
        $value = $request->input('value');
        
        // Validiere erlaubte Felder
        $allowedFields = ['lernabschnitt', 'nummer', 'frage', 'antwort_a', 'antwort_b', 'antwort_c', 'loesung'];
        
        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Feld nicht erlaubt'], 400);
        }
        
        // Einfache Validierung
        if (empty($value) && $field !== 'antwort_a' && $field !== 'antwort_b' && $field !== 'antwort_c') {
            return response()->json(['success' => false, 'message' => 'Wert darf nicht leer sein'], 400);
        }
        
        // Spezielle Validierung für Lösung
        if ($field === 'loesung') {
            // Split by comma and validate each solution
            $solutions = array_map('trim', explode(',', $value));
            $validSolutions = ['A', 'B', 'C'];
            
            foreach ($solutions as $solution) {
                if (!in_array($solution, $validSolutions)) {
                    return response()->json(['success' => false, 'message' => 'Lösung muss A, B und/oder C sein'], 400);
                }
            }
            
            // Remove duplicates and sort for consistency
            $solutions = array_unique($solutions);
            sort($solutions);
            $value = implode(',', $solutions);
        }
        
        try {
            $question->update([$field => $value]);
            return response()->json([
                'success' => true, 
                'message' => 'Erfolgreich gespeichert',
                'field' => $field,
                'value' => $value
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Datenbankfehler'], 500);
        }
    }
}
