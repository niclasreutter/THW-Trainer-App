<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ExtraQuestionController extends Controller
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

        $questions = ExtraQuestion::with(['options', 'matchCategories', 'matchItems'])
            ->orderBy('lernabschnitt')
            ->orderBy('typ')
            ->orderBy('id')
            ->get();

        return view('admin.extra-questions.index', compact('questions'));
    }

    public function create(Request $request)
    {
        $this->abortIfNotAdmin();

        $typ = $request->query('typ');
        if (!in_array($typ, [
            ExtraQuestion::TYP_MATCHING,
            ExtraQuestion::TYP_IMAGE_NAME,
            ExtraQuestion::TYP_IMAGE_SELECT,
        ], true)) {
            $typ = null;
        }

        return view('admin.extra-questions.create', compact('typ'));
    }

    public function store(Request $request)
    {
        $this->abortIfNotAdmin();

        $validated = $this->validateForType($request);

        DB::transaction(function () use ($request, $validated) {
            $imagePath = null;
            if ($validated['typ'] === ExtraQuestion::TYP_IMAGE_NAME && $request->hasFile('image')) {
                $imagePath = $request->file('image')->store('extra-questions', 'public');
            }

            $question = ExtraQuestion::create([
                'typ' => $validated['typ'],
                'lernabschnitt' => $validated['lernabschnitt'],
                'frage' => $validated['frage'],
                'image_path' => $imagePath,
            ]);

            $this->persistRelations($question, $request, $validated);
        });

        return redirect()
            ->route('admin.extra-questions.index')
            ->with('success', 'Zusatz-Frage erfolgreich erstellt!');
    }

    public function edit(ExtraQuestion $extra_question)
    {
        $this->abortIfNotAdmin();

        $extra_question->load(['options', 'matchCategories', 'matchItems.correctCategory']);

        return view('admin.extra-questions.edit', ['question' => $extra_question]);
    }

    public function update(Request $request, ExtraQuestion $extra_question)
    {
        $this->abortIfNotAdmin();

        // Typ darf sich nicht ändern: force original
        $request->merge(['typ' => $extra_question->typ]);
        $validated = $this->validateForType($request, $extra_question);

        DB::transaction(function () use ($request, $validated, $extra_question) {
            $updateData = [
                'lernabschnitt' => $validated['lernabschnitt'],
                'frage' => $validated['frage'],
            ];

            // Frage-Bild (image_name): ggf. ersetzen
            if ($extra_question->typ === ExtraQuestion::TYP_IMAGE_NAME && $request->hasFile('image')) {
                if ($extra_question->image_path) {
                    Storage::disk('public')->delete($extra_question->image_path);
                }
                $updateData['image_path'] = $request->file('image')->store('extra-questions', 'public');
            }

            $extra_question->update($updateData);

            // Alte verwandte Daten löschen
            if ($extra_question->isMatching()) {
                $extra_question->matchItems()->delete();
                $extra_question->matchCategories()->delete();
            } else {
                // Option-Bilder aus Storage entfernen (image_select)
                if ($extra_question->isImageSelect()) {
                    foreach ($extra_question->options as $opt) {
                        if ($opt->image_path) {
                            Storage::disk('public')->delete($opt->image_path);
                        }
                    }
                }
                $extra_question->options()->delete();
            }

            $this->persistRelations($extra_question->fresh(), $request, $validated);
        });

        return redirect()
            ->route('admin.extra-questions.index')
            ->with('success', 'Zusatz-Frage erfolgreich aktualisiert!');
    }

    public function destroy(ExtraQuestion $extra_question)
    {
        $this->abortIfNotAdmin();

        // Bilder aus Storage löschen
        if ($extra_question->image_path) {
            Storage::disk('public')->delete($extra_question->image_path);
        }

        foreach ($extra_question->options as $opt) {
            if ($opt->image_path) {
                Storage::disk('public')->delete($opt->image_path);
            }
        }

        // Cascade in DB löscht options/categories/items
        $extra_question->delete();

        return redirect()
            ->route('admin.extra-questions.index')
            ->with('success', 'Zusatz-Frage erfolgreich gelöscht!');
    }

    // ----------------- Helpers -----------------

    /**
     * Validiert Request je nach Typ und gibt validierte Daten zurück.
     */
    private function validateForType(Request $request, ?ExtraQuestion $existing = null): array
    {
        $typ = $request->input('typ');

        $rules = [
            'typ' => 'required|in:matching,image_name,image_select',
            'lernabschnitt' => 'required|string|max:255',
            'frage' => 'required|string',
        ];

        if ($typ === ExtraQuestion::TYP_IMAGE_NAME) {
            // Bei Update ist das Bild optional (bestehendes bleibt)
            $imageRule = $existing ? 'nullable' : 'required';
            $rules = array_merge($rules, [
                'image' => $imageRule . '|image|mimes:jpeg,png,jpg,webp|max:5120',
                'options' => 'required|array|min:2|max:6',
                'options.*.text' => 'required|string',
                'options.*.is_correct' => 'required|boolean',
            ]);
        } elseif ($typ === ExtraQuestion::TYP_IMAGE_SELECT) {
            // Bei Update sind Option-Bilder optional — aber nur wenn wir alte behalten würden.
            // Da update() alle Options ersetzt, verlangen wir beim Update ebenfalls alle Bilder.
            $rules = array_merge($rules, [
                'options' => 'required|array|min:2|max:6',
                'options.*.image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
                'options.*.is_correct' => 'required|boolean',
            ]);
        } elseif ($typ === ExtraQuestion::TYP_MATCHING) {
            $rules = array_merge($rules, [
                'categories' => 'required|array|min:2|max:5',
                'categories.*.name' => 'required|string|max:255',
                'items' => 'required|array|min:3|max:10',
                'items.*.text' => 'required|string',
                'items.*.category_index' => 'required|integer|min:0',
            ]);
        }

        $validated = $request->validate($rules);

        // Custom Validierung: mindestens eine korrekte Option (image_name / image_select)
        if (in_array($typ, [ExtraQuestion::TYP_IMAGE_NAME, ExtraQuestion::TYP_IMAGE_SELECT], true)) {
            $hasCorrect = false;
            foreach ($validated['options'] ?? [] as $opt) {
                if (!empty($opt['is_correct'])) {
                    $hasCorrect = true;
                    break;
                }
            }
            if (!$hasCorrect) {
                throw ValidationException::withMessages([
                    'options' => 'Mindestens eine Option muss als korrekt markiert sein.',
                ]);
            }
        }

        // Custom Validierung: category_index muss im gültigen Bereich liegen
        if ($typ === ExtraQuestion::TYP_MATCHING) {
            $maxIndex = count($validated['categories']) - 1;
            foreach ($validated['items'] as $i => $item) {
                if ($item['category_index'] > $maxIndex) {
                    throw ValidationException::withMessages([
                        "items.$i.category_index" => 'Ungültige Kategorie-Zuordnung.',
                    ]);
                }
            }
        }

        return $validated;
    }

    /**
     * Erstellt Options bzw. Categories+Items für eine Frage.
     * Wird sowohl aus store() als auch update() (nach Löschen alter Relationen) aufgerufen.
     */
    private function persistRelations(ExtraQuestion $question, Request $request, array $validated): void
    {
        if ($question->isMatching()) {
            $createdCategories = [];
            foreach ($validated['categories'] as $i => $catData) {
                $createdCategories[$i] = $question->matchCategories()->create([
                    'name' => $catData['name'],
                    'sort_order' => $i,
                ]);
            }
            foreach ($validated['items'] as $i => $itemData) {
                $question->matchItems()->create([
                    'text' => $itemData['text'],
                    'correct_category_id' => $createdCategories[$itemData['category_index']]->id,
                    'sort_order' => $i,
                ]);
            }
        } elseif ($question->isImageName()) {
            foreach ($validated['options'] as $i => $optData) {
                $question->options()->create([
                    'text' => $optData['text'],
                    'is_correct' => (bool) $optData['is_correct'],
                    'sort_order' => $i,
                ]);
            }
        } elseif ($question->isImageSelect()) {
            $files = $request->file('options', []);
            foreach ($validated['options'] as $i => $optData) {
                $optImagePath = null;
                if (isset($files[$i]['image']) && $files[$i]['image']) {
                    $optImagePath = $files[$i]['image']->store('extra-questions', 'public');
                }
                $question->options()->create([
                    'image_path' => $optImagePath,
                    'is_correct' => (bool) $optData['is_correct'],
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
