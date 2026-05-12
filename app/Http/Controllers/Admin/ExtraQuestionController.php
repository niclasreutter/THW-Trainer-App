<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\UserExtraQuestionSubmissionController;
use App\Models\ExtraQuestion;
use App\Models\Question;
use App\Models\UserExtraQuestionSubmission;
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

    private function abortIfCannotEditQuestions(): void
    {
        if (!auth()->check() || !auth()->user()->canEditQuestions()) {
            abort(403, 'Kein Zugriff');
        }
    }

    public function index()
    {
        $this->abortIfCannotEditQuestions();

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

        $submission = null;
        if ($request->filled('from_submission')) {
            $submission = UserExtraQuestionSubmission::find($request->query('from_submission'));
            if ($submission && $submission->isPending() && !$request->session()->hasOldInput()) {
                $request->session()->flashInput($this->submissionToOldInput($submission));
                $typ = $submission->typ;
            }
        }

        $sections = $this->buildLernabschnitte();

        return view('admin.extra-questions.create', compact('typ', 'sections', 'submission'));
    }

    public function store(Request $request)
    {
        $this->abortIfNotAdmin();

        $validated = $this->validateForType($request);

        $createdQuestion = null;
        DB::transaction(function () use ($request, $validated, &$createdQuestion) {
            $imagePath = null;
            $imageSource = null;
            if ($validated['typ'] === ExtraQuestion::TYP_IMAGE_NAME) {
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('extra-questions', 'public');
                }
                $imageSource = $validated['image_source'] ?? null;
            }

            $question = ExtraQuestion::create([
                'typ' => $validated['typ'],
                'lernabschnitt' => $validated['lernabschnitt'],
                'frage' => $validated['frage'],
                'image_path' => $imagePath,
                'image_source' => $imageSource,
            ]);

            $this->persistRelations($question, $request, $validated);
            $createdQuestion = $question;
        });

        $submissionId = $request->input('_from_submission_id');
        if ($submissionId && $createdQuestion) {
            $submission = UserExtraQuestionSubmission::find($submissionId);
            if ($submission && $submission->isPending()) {
                $submission->update([
                    'status' => UserExtraQuestionSubmission::STATUS_APPROVED,
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                    'extra_question_id' => $createdQuestion->id,
                ]);
                cache()->forget('admin_pending_extra_q_submissions_count');
                UserExtraQuestionSubmissionController::notifyUser($submission->fresh());
            }
        }

        return redirect()
            ->route('admin.extra-questions.index')
            ->with('success', 'Zusatz-Frage erfolgreich erstellt!');
    }

    public function edit(ExtraQuestion $extra_question)
    {
        $this->abortIfCannotEditQuestions();

        $extra_question->load(['options', 'matchCategories', 'matchItems.correctCategory']);

        $sections = $this->buildLernabschnitte($extra_question->lernabschnitt);

        return view('admin.extra-questions.edit', [
            'question' => $extra_question,
            'sections' => $sections,
        ]);
    }

    public function update(Request $request, ExtraQuestion $extra_question)
    {
        $this->abortIfCannotEditQuestions();

        // Typ darf sich nicht ändern: force original
        $request->merge(['typ' => $extra_question->typ]);
        $validated = $this->validateForType($request, $extra_question);

        DB::transaction(function () use ($request, $validated, $extra_question) {
            $updateData = [
                'lernabschnitt' => $validated['lernabschnitt'],
                'frage' => $validated['frage'],
            ];

            // Frage-Bild (image_name): ggf. ersetzen
            if ($extra_question->typ === ExtraQuestion::TYP_IMAGE_NAME) {
                if ($request->hasFile('image')) {
                    if ($extra_question->image_path) {
                        Storage::disk('public')->delete($extra_question->image_path);
                    }
                    $updateData['image_path'] = $request->file('image')->store('extra-questions', 'public');
                }
                $updateData['image_source'] = $validated['image_source'] ?? null;
            }

            $extra_question->update($updateData);

            // Alte image_paths sichern, um nach dem Persist Orphans zu identifizieren.
            $oldImagePaths = [];
            if ($extra_question->isImageSelect()) {
                $oldImagePaths = $extra_question->options
                    ->pluck('image_path')
                    ->filter()
                    ->values()
                    ->all();
            }

            // Alte verwandte Daten löschen (Records, Bilder kommen unten dran)
            if ($extra_question->isMatching()) {
                $extra_question->matchItems()->delete();
                $extra_question->matchCategories()->delete();
            } else {
                $extra_question->options()->delete();
            }

            $this->persistRelations($extra_question->fresh(), $request, $validated);

            // image_select: Orphan-Images aus Storage entfernen (alte Pfade, die in den
            // neuen Options nicht mehr referenziert werden).
            if ($extra_question->isImageSelect() && !empty($oldImagePaths)) {
                $newImagePaths = $extra_question->fresh()->options
                    ->pluck('image_path')
                    ->filter()
                    ->values()
                    ->all();
                $orphans = array_diff($oldImagePaths, $newImagePaths);
                foreach ($orphans as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
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
     * Wandelt eine User-Submission in das old()-Input-Format um, sodass
     * das normale Admin-Create-Formular vorbefüllt werden kann.
     */
    private function submissionToOldInput(UserExtraQuestionSubmission $submission): array
    {
        $payload = $submission->payload ?? [];
        $input = [
            'typ' => $submission->typ,
            'lernabschnitt' => $submission->lernabschnitt,
            'frage' => $submission->frage,
            '_from_submission_id' => (string) $submission->id,
        ];

        if ($submission->typ === ExtraQuestion::TYP_MATCHING) {
            $input['categories'] = array_map(
                fn ($c) => ['name' => $c['name'] ?? ''],
                $payload['categories'] ?? []
            );
            $input['items'] = array_map(
                fn ($i) => [
                    'text' => $i['text'] ?? '',
                    'category_index' => (int) ($i['category_index'] ?? 0),
                ],
                $payload['items'] ?? []
            );
        } elseif ($submission->typ === ExtraQuestion::TYP_IMAGE_NAME) {
            $input['image_source'] = $payload['image_source_hint'] ?? '';
            $input['options'] = array_map(
                fn ($o) => [
                    'text' => $o['text'] ?? '',
                    'is_correct' => !empty($o['is_correct']) ? 1 : 0,
                ],
                $payload['options'] ?? []
            );
        } elseif ($submission->typ === ExtraQuestion::TYP_IMAGE_SELECT) {
            $input['options'] = array_map(
                fn ($o) => [
                    'image_source' => $o['image_source_hint'] ?? '',
                    'is_correct' => !empty($o['is_correct']) ? 1 : 0,
                ],
                $payload['options'] ?? []
            );
        }

        return $input;
    }

    /**
     * Offizielle THW-Lernabschnittsnamen (2022).
     */
    private const SECTION_NAMES = [
        1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
        2 => 'Arbeitssicherheit und Gesundheitsschutz',
        3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
        4 => 'Arbeiten mit Leitern',
        5 => 'Stromerzeugung und Beleuchtung',
        6 => 'Metall-, Holz- und Steinbearbeitung',
        7 => 'Bewegen von Lasten',
        8 => 'Arbeiten am und auf dem Wasser',
        9 => 'Einsatzgrundlagen',
        10 => 'Grundlagen der Rettung und Bergung',
    ];

    /**
     * Baut die Lernabschnitt-Auswahl aus der DB:
     * distinct-Werte aus questions + extra_questions, optional ergänzt um einen
     * evtl. vorhandenen custom-Wert (z.B. beim Edit).
     *
     * @return array<string, string>  value → label
     */
    private function buildLernabschnitte(?string $includeValue = null): array
    {
        $fromQuestions = Question::query()
            ->select('lernabschnitt')
            ->distinct()
            ->pluck('lernabschnitt');

        $fromExtra = ExtraQuestion::query()
            ->select('lernabschnitt')
            ->distinct()
            ->pluck('lernabschnitt');

        $values = $fromQuestions->merge($fromExtra)
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (string) $v)
            ->unique()
            ->values()
            ->all();

        if ($includeValue !== null && $includeValue !== '' && !in_array($includeValue, $values, true)) {
            $values[] = $includeValue;
        }

        usort($values, function ($a, $b) {
            $aNum = is_numeric($a);
            $bNum = is_numeric($b);
            if ($aNum && $bNum) {
                return (int) $a <=> (int) $b;
            }
            if ($aNum !== $bNum) {
                return $aNum ? -1 : 1;
            }
            return strcmp($a, $b);
        });

        $sections = [];
        foreach ($values as $val) {
            $sections[$val] = $this->formatLernabschnittLabel($val);
        }

        return $sections;
    }

    private function formatLernabschnittLabel(string $value): string
    {
        if (is_numeric($value) && isset(self::SECTION_NAMES[(int) $value])) {
            return $value . ' – ' . self::SECTION_NAMES[(int) $value];
        }
        return $value;
    }

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
                'image_source' => 'required|string|max:255',
                'options' => 'required|array|min:2|max:6',
                'options.*.text' => 'required|string',
                'options.*.is_correct' => 'required|boolean',
            ]);
        } elseif ($typ === ExtraQuestion::TYP_IMAGE_SELECT) {
            // Beim Update sind Bilder optional: Wenn keine Datei hochgeladen wurde,
            // wird der bestehende image_path via existing_image_path weitergereicht.
            $imageRule = $existing ? 'nullable' : 'required';
            $rules = array_merge($rules, [
                'options' => 'required|array|min:2|max:6',
                'options.*.image' => $imageRule . '|image|mimes:jpeg,png,jpg,webp|max:5120',
                'options.*.existing_image_path' => 'nullable|string|max:500',
                'options.*.image_source' => 'required|string|max:255',
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

        // Custom Validierung: image_select Update braucht pro Option entweder neues File
        // oder existing_image_path.
        if ($typ === ExtraQuestion::TYP_IMAGE_SELECT && $existing !== null) {
            $files = $request->file('options', []);
            foreach ($validated['options'] as $i => $opt) {
                $hasNewFile = isset($files[$i]['image']) && $files[$i]['image'];
                $hasExisting = !empty($opt['existing_image_path']);
                if (!$hasNewFile && !$hasExisting) {
                    throw ValidationException::withMessages([
                        "options.$i.image" => 'Bitte Bild hochladen oder bestehendes Bild beibehalten.',
                    ]);
                }
            }
        }

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
                } elseif (!empty($optData['existing_image_path'])) {
                    $optImagePath = $optData['existing_image_path'];
                }
                $question->options()->create([
                    'image_path' => $optImagePath,
                    'image_source' => $optData['image_source'] ?? null,
                    'is_correct' => (bool) $optData['is_correct'],
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
