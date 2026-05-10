<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private const ALLOWED_DIFFICULTIES = ['easy', 'medium', 'hard'];
    private const ALLOWED_STATUSES = ['draft', 'review', 'published'];

    private const ALLOWED_FIELDS = [
        'lernabschnitt',
        'nummer',
        'frage',
        'antwort_a',
        'antwort_b',
        'antwort_c',
        'loesung',
        'loesungsweg',
        'difficulty',
        'status',
    ];

    private const TEXT_FIELDS = ['frage', 'antwort_a', 'antwort_b', 'antwort_c', 'loesungsweg'];

    private function normalizeWhitespace(?string $value): ?string
    {
        if ($value === null) return null;
        $value = preg_replace('/[ \t]+/u', ' ', $value);
        $value = preg_replace('/[ \t]+$/m', '', $value);
        return trim($value);
    }

    private function abortIfNotAdmin(): void
    {
        if (!auth()->check() || auth()->user()->useroll !== 'admin') {
            abort(403, 'Kein Zugriff');
        }
    }

    private function abortIfCannotEditQuestions(): void
    {
        if (!auth()->check() || !auth()->user()->canEditQuestions()) {
            abort(403, 'Kein Zugriff');
        }
    }

    public function index()
    {
        $this->abortIfCannotEditQuestions();

        $questions = Question::orderBy('lernabschnitt')->orderBy('nummer')->get();

        $stats = DB::table('question_statistics')
            ->selectRaw('question_id, COUNT(*) as total, SUM(is_correct) as correct')
            ->groupBy('question_id')
            ->get()
            ->keyBy('question_id');

        $payload = $questions->map(function (Question $q) use ($stats) {
            $row = $stats->get($q->id);
            $total = (int) ($row->total ?? 0);
            $correct = (int) ($row->correct ?? 0);
            $rate = $total > 0 ? (int) round(($correct / $total) * 100) : 0;

            $solutions = collect(explode(',', (string) $q->loesung))
                ->map(fn ($s) => strtoupper(trim($s)))
                ->filter(fn ($s) => in_array($s, ['A', 'B', 'C'], true))
                ->values()
                ->all();

            return [
                'id' => $q->id,
                'lernabschnitt' => (string) $q->lernabschnitt,
                'nummer' => (int) $q->nummer,
                'frage' => (string) $q->frage,
                'antwort_a' => (string) $q->antwort_a,
                'antwort_b' => (string) $q->antwort_b,
                'antwort_c' => (string) $q->antwort_c,
                'loesung' => $solutions,
                'difficulty' => $q->difficulty ?: 'medium',
                'status' => $q->status ?: 'published',
                'loesungsweg' => (string) ($q->loesungsweg ?? ''),
                'stats' => [
                    'total' => $total,
                    'correct' => $correct,
                    'rate' => $rate,
                ],
            ];
        })->values()->all();

        return view('admin.questions.index', [
            'questions' => $questions,
            'questionsJson' => $payload,
            'aiEnabled' => !empty(config('services.openai.api_key')),
        ]);
    }

    public function create()
    {
        $this->abortIfNotAdmin();
        return view('admin.questions.create');
    }

    public function store(Request $request)
    {
        $this->abortIfNotAdmin();

        $validated = $request->validate([
            'lernabschnitt' => 'required|string',
            'nummer' => 'required|integer',
            'frage' => 'required|string',
            'antwort_a' => 'required|string',
            'antwort_b' => 'required|string',
            'antwort_c' => 'required|string',
            'loesung' => 'required|array|min:1',
            'loesung.*' => 'in:A,B,C',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'status' => 'nullable|in:draft,review,published',
            'loesungsweg' => 'nullable|string',
        ]);

        $solutions = array_unique(array_map('strtoupper', $validated['loesung']));
        sort($solutions);

        foreach (self::TEXT_FIELDS as $field) {
            if (array_key_exists($field, $validated)) {
                $validated[$field] = $this->normalizeWhitespace($validated[$field]);
            }
        }

        $question = Question::create([
            'lernabschnitt' => $validated['lernabschnitt'],
            'nummer' => $validated['nummer'],
            'frage' => $validated['frage'],
            'antwort_a' => $validated['antwort_a'],
            'antwort_b' => $validated['antwort_b'],
            'antwort_c' => $validated['antwort_c'],
            'loesung' => implode(',', $solutions),
            'difficulty' => $validated['difficulty'] ?? 'medium',
            'status' => $validated['status'] ?? 'draft',
            'loesungsweg' => $validated['loesungsweg'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id' => $question->id,
                'message' => 'Frage erstellt',
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Frage erfolgreich erstellt!');
    }

    public function edit(Question $question)
    {
        $this->abortIfCannotEditQuestions();
        return redirect()->route('admin.questions.index', ['focus' => $question->id]);
    }

    public function update(Request $request, Question $question)
    {
        $this->abortIfCannotEditQuestions();

        $validated = $request->validate([
            'lernabschnitt' => 'sometimes|required|string',
            'nummer' => 'sometimes|required|integer',
            'frage' => 'sometimes|required|string',
            'antwort_a' => 'sometimes|required|string',
            'antwort_b' => 'sometimes|required|string',
            'antwort_c' => 'sometimes|required|string',
            'loesung' => 'sometimes|required',
            'difficulty' => 'sometimes|nullable|in:easy,medium,hard',
            'status' => 'sometimes|nullable|in:draft,review,published',
            'loesungsweg' => 'sometimes|nullable|string',
        ]);

        if (isset($validated['loesung']) && is_array($validated['loesung'])) {
            $solutions = array_unique(array_map('strtoupper', $validated['loesung']));
            sort($solutions);
            $validated['loesung'] = implode(',', $solutions);
        }

        foreach (self::TEXT_FIELDS as $field) {
            if (array_key_exists($field, $validated)) {
                $validated[$field] = $this->normalizeWhitespace($validated[$field]);
            }
        }

        $question->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.questions.index');
    }

    public function destroy(Request $request, Question $question)
    {
        $this->abortIfNotAdmin();
        $question->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.questions.index');
    }

    public function updateField(Request $request, Question $question): JsonResponse
    {
        $this->abortIfCannotEditQuestions();

        $field = $request->input('field');
        $value = $request->input('value');

        if (!in_array($field, self::ALLOWED_FIELDS, true)) {
            return response()->json(['success' => false, 'message' => 'Feld nicht erlaubt'], 400);
        }

        try {
            switch ($field) {
                case 'loesung':
                    $solutions = is_array($value)
                        ? $value
                        : array_map('trim', explode(',', (string) $value));
                    $solutions = array_values(array_unique(array_map('strtoupper', $solutions)));
                    $solutions = array_filter($solutions, fn ($s) => in_array($s, ['A', 'B', 'C'], true));

                    if (count($solutions) === 0) {
                        return response()->json(['success' => false, 'message' => 'Mindestens eine Lösung erforderlich'], 422);
                    }

                    sort($solutions);
                    $value = implode(',', $solutions);
                    break;

                case 'nummer':
                    if (!is_numeric($value)) {
                        return response()->json(['success' => false, 'message' => 'Nummer muss eine Zahl sein'], 422);
                    }
                    $value = (int) $value;
                    break;

                case 'difficulty':
                    if (!in_array($value, self::ALLOWED_DIFFICULTIES, true)) {
                        return response()->json(['success' => false, 'message' => 'Ungültige Schwierigkeit'], 422);
                    }
                    break;

                case 'status':
                    if (!in_array($value, self::ALLOWED_STATUSES, true)) {
                        return response()->json(['success' => false, 'message' => 'Ungültiger Status'], 422);
                    }
                    break;

                case 'loesungsweg':
                    $value = $this->normalizeWhitespace((string) $value);
                    break;

                default:
                    if (!is_string($value) || trim($value) === '') {
                        return response()->json(['success' => false, 'message' => 'Wert darf nicht leer sein'], 422);
                    }
                    if (in_array($field, self::TEXT_FIELDS, true)) {
                        $value = $this->normalizeWhitespace($value);
                    }
            }

            $question->update([$field => $value]);

            return response()->json([
                'success' => true,
                'message' => 'Erfolgreich gespeichert',
                'field' => $field,
                'value' => $value,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Datenbankfehler'], 500);
        }
    }

    public function aiSuggestExplanation(Request $request, Question $question, OpenAIService $openAI): JsonResponse
    {
        $this->abortIfCannotEditQuestions();

        try {
            $suggestion = $openAI->suggestExplanation($question);

            return response()->json([
                'success' => true,
                'suggestion' => $suggestion,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'KI-Vorschlag fehlgeschlagen',
            ], 502);
        }
    }
}
