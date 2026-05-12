<?php

namespace App\Http\Controllers;

use App\Models\ExtraQuestion;
use App\Models\Question;
use App\Models\UserExtraQuestionSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UserExtraQuestionSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $submissions = UserExtraQuestionSubmission::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('user-extra-questions.index', compact('submissions'));
    }

    public function create(Request $request)
    {
        $typ = $request->query('typ');
        if (!in_array($typ, [
            UserExtraQuestionSubmission::TYP_MATCHING,
            UserExtraQuestionSubmission::TYP_IMAGE_NAME,
            UserExtraQuestionSubmission::TYP_IMAGE_SELECT,
        ], true)) {
            $typ = null;
        }

        $sections = $this->buildLernabschnitte();

        return view('user-extra-questions.create', compact('typ', 'sections'));
    }

    public function store(Request $request)
    {
        $key = 'user-extra-question-submission:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withErrors(['rate_limit' => 'Zu viele Einreichungen. Bitte versuche es in ' . ceil($seconds / 60) . ' Minuten erneut.'])
                ->withInput();
        }

        $validated = $this->validateForType($request);
        $payload = $this->buildPayload($validated);

        UserExtraQuestionSubmission::create([
            'user_id' => auth()->id(),
            'typ' => $validated['typ'],
            'lernabschnitt' => $validated['lernabschnitt'],
            'frage' => $validated['frage'],
            'payload' => $payload,
            'status' => UserExtraQuestionSubmission::STATUS_PENDING,
        ]);

        cache()->forget('admin_pending_extra_q_submissions_count');
        RateLimiter::hit($key, 3600);

        return redirect()
            ->route('zusatzfragen-vorschlagen.index')
            ->with('success', 'Vielen Dank! Deine Zusatz-Frage wurde eingereicht und wird von einem Admin geprüft.');
    }

    public function destroy(UserExtraQuestionSubmission $submission)
    {
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }
        if (!$submission->isPending()) {
            abort(403, 'Nur offene Einreichungen können gelöscht werden.');
        }

        $submission->delete();

        return redirect()
            ->route('zusatzfragen-vorschlagen.index')
            ->with('success', 'Einreichung wurde gelöscht.');
    }

    // ----------------- Helpers -----------------

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

    private function buildLernabschnitte(): array
    {
        $fromQuestions = Question::query()->select('lernabschnitt')->distinct()->pluck('lernabschnitt');
        $fromExtra = ExtraQuestion::query()->select('lernabschnitt')->distinct()->pluck('lernabschnitt');

        $values = $fromQuestions->merge($fromExtra)
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (string) $v)
            ->unique()
            ->values()
            ->all();

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
            $sections[$val] = is_numeric($val) && isset(self::SECTION_NAMES[(int) $val])
                ? $val . ' – ' . self::SECTION_NAMES[(int) $val]
                : $val;
        }

        return $sections;
    }

    private function validateForType(Request $request): array
    {
        $typ = $request->input('typ');

        $rules = [
            'typ' => 'required|in:matching,image_name,image_select',
            'lernabschnitt' => 'required|string|max:255',
            'frage' => 'required|string|max:2000',
        ];

        if ($typ === UserExtraQuestionSubmission::TYP_IMAGE_NAME) {
            $rules = array_merge($rules, [
                'image_description' => 'required|string|max:2000',
                'image_source_hint' => 'nullable|string|max:500',
                'options' => 'required|array|min:2|max:6',
                'options.*.text' => 'required|string|max:500',
                'options.*.is_correct' => 'required|boolean',
            ]);
        } elseif ($typ === UserExtraQuestionSubmission::TYP_IMAGE_SELECT) {
            $rules = array_merge($rules, [
                'options' => 'required|array|min:2|max:6',
                'options.*.image_description' => 'required|string|max:2000',
                'options.*.image_source_hint' => 'nullable|string|max:500',
                'options.*.is_correct' => 'required|boolean',
            ]);
        } elseif ($typ === UserExtraQuestionSubmission::TYP_MATCHING) {
            $rules = array_merge($rules, [
                'categories' => 'required|array|min:2|max:5',
                'categories.*.name' => 'required|string|max:255',
                'items' => 'required|array|min:3|max:10',
                'items.*.text' => 'required|string|max:500',
                'items.*.category_index' => 'required|integer|min:0',
            ]);
        }

        $validated = $request->validate($rules);

        if (in_array($typ, [UserExtraQuestionSubmission::TYP_IMAGE_NAME, UserExtraQuestionSubmission::TYP_IMAGE_SELECT], true)) {
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

        if ($typ === UserExtraQuestionSubmission::TYP_MATCHING) {
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

    private function buildPayload(array $validated): array
    {
        $typ = $validated['typ'];

        if ($typ === UserExtraQuestionSubmission::TYP_MATCHING) {
            return [
                'categories' => array_map(fn ($c) => ['name' => $c['name']], $validated['categories']),
                'items' => array_map(fn ($i) => [
                    'text' => $i['text'],
                    'category_index' => (int) $i['category_index'],
                ], $validated['items']),
            ];
        }

        if ($typ === UserExtraQuestionSubmission::TYP_IMAGE_NAME) {
            return [
                'image_description' => $validated['image_description'],
                'image_source_hint' => $validated['image_source_hint'] ?? null,
                'options' => array_map(fn ($o) => [
                    'text' => $o['text'],
                    'is_correct' => (bool) $o['is_correct'],
                ], $validated['options']),
            ];
        }

        return [
            'options' => array_map(fn ($o) => [
                'image_description' => $o['image_description'],
                'image_source_hint' => $o['image_source_hint'] ?? null,
                'is_correct' => (bool) $o['is_correct'],
            ], $validated['options']),
        ];
    }
}
