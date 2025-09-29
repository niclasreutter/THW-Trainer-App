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
            'loesung' => 'required|string',
        ]);
        Question::create($request->all());
        return redirect()->route('admin.questions.index');
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
}
