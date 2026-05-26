<?php

namespace App\Http\Controllers;

use App\Models\ExtraMatchCategory;
use App\Models\ExtraMatchItem;
use App\Models\ExtraPairItem;
use App\Models\ExtraQuestion;
use App\Models\UserExtraQuestionSubmission;
use App\Services\ExtraQuestionAnswerEvaluator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;

/**
 * Try-Out / Vorschau für Zusatz-Fragen.
 *
 * Zwei Varianten:
 *  - Live-`ExtraQuestion`: Admin + Contributor können jede angelegte Frage testen.
 *  - Pending `UserExtraQuestionSubmission`: Der Einreicher, Admins und Contributors
 *    können die Vorschau prüfen – aber nur für `matching` und `pair_matching`, weil
 *    Bild-Submissions lediglich Text-Beschreibungen enthalten.
 *
 * Es wird KEIN Fortschritt gespeichert (kein Spaced-Repetition-Update, keine XP).
 */
class ExtraQuestionTryoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ---------------------------------------------------------------------
    // Live-ExtraQuestion (Admin + Contributor)
    // ---------------------------------------------------------------------

    public function show(ExtraQuestion $extra_question)
    {
        $this->abortIfCannotEditQuestions();
        $extra_question->load(['options', 'matchCategories', 'matchItems', 'pairItems']);

        return view('extra-questions.tryout', [
            'question' => $extra_question,
            'context' => 'extra-question',
            'submitUrl' => route('admin.extra-questions.tryout.submit', $extra_question),
            'backUrl' => route('admin.extra-questions.edit', $extra_question),
            'backLabel' => 'Zur Bearbeitung',
            'isCorrect' => null,
            'answerResult' => null,
            'userAnswer' => null,
        ]);
    }

    public function submit(Request $request, ExtraQuestion $extra_question)
    {
        $this->abortIfCannotEditQuestions();
        $extra_question->load(['options', 'matchCategories', 'matchItems', 'pairItems']);

        [$isCorrect, $answerResult, $userAnswer] = $this->evaluate($request, $extra_question);

        return view('extra-questions.tryout', [
            'question' => $extra_question,
            'context' => 'extra-question',
            'submitUrl' => route('admin.extra-questions.tryout.submit', $extra_question),
            'retryUrl' => route('admin.extra-questions.tryout', $extra_question),
            'backUrl' => route('admin.extra-questions.edit', $extra_question),
            'backLabel' => 'Zur Bearbeitung',
            'isCorrect' => $isCorrect,
            'answerResult' => $answerResult,
            'userAnswer' => $userAnswer,
        ]);
    }

    // ---------------------------------------------------------------------
    // Pending UserExtraQuestionSubmission (Owner + Admin + Contributor)
    // ---------------------------------------------------------------------

    public function showSubmission(UserExtraQuestionSubmission $submission)
    {
        $this->abortIfCannotAccessSubmission($submission);
        $this->abortIfSubmissionNotPreviewable($submission);
        $this->abortIfSubmissionNotPending($submission);

        $virtual = $this->buildVirtualQuestionFromSubmission($submission);

        return view('extra-questions.tryout', [
            'question' => $virtual,
            'context' => 'submission',
            'submission' => $submission,
            'submitUrl' => route('zusatzfragen-vorschlagen.tryout.submit', $submission),
            'backUrl' => $this->submissionBackUrl($submission),
            'backLabel' => 'Zurück',
            'isCorrect' => null,
            'answerResult' => null,
            'userAnswer' => null,
        ]);
    }

    public function submitSubmission(Request $request, UserExtraQuestionSubmission $submission)
    {
        $this->abortIfCannotAccessSubmission($submission);
        $this->abortIfSubmissionNotPreviewable($submission);
        $this->abortIfSubmissionNotPending($submission);

        $virtual = $this->buildVirtualQuestionFromSubmission($submission);

        [$isCorrect, $answerResult, $userAnswer] = $this->evaluate($request, $virtual);

        return view('extra-questions.tryout', [
            'question' => $virtual,
            'context' => 'submission',
            'submission' => $submission,
            'submitUrl' => route('zusatzfragen-vorschlagen.tryout.submit', $submission),
            'retryUrl' => route('zusatzfragen-vorschlagen.tryout', $submission),
            'backUrl' => $this->submissionBackUrl($submission),
            'backLabel' => 'Zurück',
            'isCorrect' => $isCorrect,
            'answerResult' => $answerResult,
            'userAnswer' => $userAnswer,
        ]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function abortIfCannotEditQuestions(): void
    {
        if (!auth()->check() || !auth()->user()->canEditQuestions()) {
            abort(403, 'Kein Zugriff');
        }
    }

    private function abortIfCannotAccessSubmission(UserExtraQuestionSubmission $submission): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }
        $isOwner = $submission->user_id === $user->id;
        if (!$isOwner && !$user->canEditQuestions()) {
            abort(403, 'Kein Zugriff');
        }
    }

    private function abortIfSubmissionNotPreviewable(UserExtraQuestionSubmission $submission): void
    {
        $previewable = in_array($submission->typ, [
            UserExtraQuestionSubmission::TYP_MATCHING,
            UserExtraQuestionSubmission::TYP_PAIR_MATCHING,
        ], true);

        if (!$previewable) {
            abort(404, 'Für diesen Fragetyp ist keine Vorschau möglich.');
        }
    }

    private function abortIfSubmissionNotPending(UserExtraQuestionSubmission $submission): void
    {
        // Approved submissions sind als ExtraQuestion live – dort gibt es die echte
        // Tryout-Route. Abgelehnte / mit-Änderungen-übernommene sind nicht mehr relevant.
        if (!$submission->isPending()) {
            abort(404, 'Diese Einreichung ist nicht mehr offen – kein Vorschau-Modus.');
        }
    }

    private function submissionBackUrl(UserExtraQuestionSubmission $submission): string
    {
        // Admins/Contributors landen auf der Admin-Detailseite, Owner auf ihrer Liste.
        $user = auth()->user();
        if ($user && $user->canEditQuestions() && $user->id !== $submission->user_id) {
            return route('admin.extra-question-submissions.show', $submission);
        }
        return route('zusatzfragen-vorschlagen.index');
    }

    /**
     * Konstruiert eine in-memory ExtraQuestion (inkl. relations) aus einem
     * Submission-Payload. Synthetische IDs werden anhand der Reihenfolge im Payload
     * vergeben, sodass GET-Render und POST-Validate dasselbe Schema sehen.
     */
    private function buildVirtualQuestionFromSubmission(UserExtraQuestionSubmission $submission): ExtraQuestion
    {
        $payload = $submission->payload ?? [];

        $q = new ExtraQuestion([
            'typ' => $submission->typ,
            'lernabschnitt' => $submission->lernabschnitt,
            'frage' => $submission->frage,
        ]);
        // Synthetische ID (nur in-Memory). Verwende die Submission-ID als
        // Basis, damit das JS-Variablen-Suffix eindeutig bleibt.
        $q->id = $submission->id;
        $q->exists = false;

        if ($submission->typ === UserExtraQuestionSubmission::TYP_MATCHING) {
            $catModels = [];
            foreach (($payload['categories'] ?? []) as $i => $cat) {
                $m = new ExtraMatchCategory([
                    'name' => $cat['name'] ?? '',
                    'sort_order' => $i,
                ]);
                $m->id = $i + 1; // 1-basiert, eindeutig
                $catModels[] = $m;
            }

            $itemModels = [];
            foreach (($payload['items'] ?? []) as $i => $item) {
                $catIdx = (int) ($item['category_index'] ?? 0);
                $m = new ExtraMatchItem([
                    'text' => $item['text'] ?? '',
                    'sort_order' => $i,
                ]);
                $m->id = 1000 + $i + 1; // Offset, um Kollision mit category-ids zu vermeiden
                $m->correct_category_id = isset($catModels[$catIdx]) ? $catModels[$catIdx]->id : null;
                $itemModels[] = $m;
            }

            $q->setRelation('matchCategories', new EloquentCollection($catModels));
            $q->setRelation('matchItems', new EloquentCollection($itemModels));
            $q->setRelation('options', new EloquentCollection());
            $q->setRelation('pairItems', new EloquentCollection());
        } elseif ($submission->typ === UserExtraQuestionSubmission::TYP_PAIR_MATCHING) {
            $pairModels = [];
            foreach (($payload['pairs'] ?? []) as $i => $pair) {
                $m = new ExtraPairItem([
                    'left_text' => $pair['left_text'] ?? '',
                    'right_text' => $pair['right_text'] ?? '',
                    'sort_order' => $i,
                ]);
                $m->id = $i + 1;
                $pairModels[] = $m;
            }
            $q->setRelation('pairItems', new EloquentCollection($pairModels));
            $q->setRelation('options', new EloquentCollection());
            $q->setRelation('matchCategories', new EloquentCollection());
            $q->setRelation('matchItems', new EloquentCollection());
        }

        return $q;
    }

    /**
     * Liest die Antwort-Felder aus dem Request und bewertet sie via
     * ExtraQuestionAnswerEvaluator. Gibt ein Tripel zurück:
     * [bool $isCorrect, array $answerResult, ?array $userAnswer].
     */
    private function evaluate(Request $request, ExtraQuestion $question): array
    {
        $isCorrect = false;
        $userAnswer = null;

        $answerResult = [
            'question_id' => $question->id,
            'question_kind' => 'extra',
            'question_typ' => $question->typ,
        ];

        if ($question->typ === ExtraQuestion::TYP_MATCHING) {
            $request->validate(['assignments' => ['required', 'string', 'json']]);
            $submitted = json_decode($request->input('assignments'), true) ?: [];
            $isCorrect = ExtraQuestionAnswerEvaluator::evaluateMatching($question, $submitted);
            $answerResult['assignments'] = $submitted;
        } elseif ($question->typ === ExtraQuestion::TYP_PAIR_MATCHING) {
            $request->validate(['pair_assignments' => ['required', 'string', 'json']]);
            $submitted = json_decode($request->input('pair_assignments'), true) ?: [];
            $isCorrect = ExtraQuestionAnswerEvaluator::evaluatePairMatching($question, $submitted);
            $answerResult['pair_assignments'] = $submitted;
        } else {
            // image_name + image_select
            $request->validate([
                'answer' => ['required', 'array', 'min:1'],
                'answer.*' => ['integer'],
            ]);
            $userAnswer = array_map('intval', (array) $request->input('answer', []));
            $isCorrect = ExtraQuestionAnswerEvaluator::evaluateOptionIds($question, $userAnswer);
            $answerResult['user_answer'] = $userAnswer;
        }

        $answerResult['is_correct'] = $isCorrect;

        return [$isCorrect, $answerResult, $userAnswer];
    }
}
