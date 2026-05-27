<?php
namespace App\Http\Controllers\Admin;

use App\Models\AdminAuditLog;
use App\Models\Question;
use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;
use App\Services\SpacedRepetitionService;
use App\Services\UserDeletionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function editProgress($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        $questions = \App\Models\Question::all();
        $solved = is_array($user->solved_questions) ? $user->solved_questions : json_decode($user->solved_questions ?? '[]', true);
        $failed = is_array($user->exam_failed_questions) ? $user->exam_failed_questions : json_decode($user->exam_failed_questions ?? '[]', true);
        
        // Lade Lehrgang-Daten
        $lehrgaenge = $user->enrolledLehrgaenge()->get();
        
        // Für jeden Lehrgang: hole alle Fragen und den Fortschritt
        $lehrgangData = [];
        foreach ($lehrgaenge as $lehrgang) {
            $allQuestions = $lehrgang->questions()
                ->orderByRaw('CAST(lernabschnitt AS UNSIGNED)')
                ->orderBy('nummer')
                ->get();
            
            // Hole Fortschrittsdaten
            $progressData = \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                ->whereIn('lehrgang_question_id', $allQuestions->pluck('id')->toArray())
                ->get()
                ->keyBy('lehrgang_question_id');
            
            // Berechne Gesamt-Fortschritt
            $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
            $totalProgressPoints = 0;
            $solvedCount = 0;
            foreach ($progressData as $prog) {
                $totalProgressPoints += min($prog->consecutive_correct, $threshold);
                if ($prog->solved) {
                    $solvedCount++;
                }
            }
            $maxProgressPoints = $allQuestions->count() * $threshold;
            $totalPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
            
            $lehrgangData[$lehrgang->id] = [
                'lehrgang' => $lehrgang,
                'questions' => $allQuestions,
                'progressData' => $progressData,
                'totalSolved' => $solvedCount,
                'totalQuestions' => $allQuestions->count(),
                'totalPercent' => $totalPercent,
            ];
        }
        
        $lehrgangData = collect($lehrgangData);

        // Spaced Repetition Statistiken
        $srService = new SpacedRepetitionService();
        $srStats = $srService->getStats($user->id);

        return view('admin.edit_progress', compact('user', 'questions', 'solved', 'failed', 'lehrgangData', 'srStats'));
    }

    public function updateProgress(Request $request, $id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        $solved = $request->input('solved_questions', []);
        $failed = $request->input('exam_failed_questions', []);
        
        // Speichere alte Werte für Vergleich
        $oldSolved = is_array($user->solved_questions) 
            ? $user->solved_questions 
            : json_decode($user->solved_questions ?? '[]', true);
        
        // Aktualisiere User-Felder
        $user->solved_questions = $solved;
        $user->exam_failed_questions = $failed;
        $user->save();
        
        // Synchronisiere mit user_question_progress (Grundausbildung)
        foreach ($solved as $questionId) {
            \App\Models\UserQuestionProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                ],
                [
                    'consecutive_correct' => \App\Models\UserQuestionProgress::MASTERY_THRESHOLD,
                    'last_answered_at' => now(),
                ]
            );
        }
        
        // Für Fragen die aus "gelöst" entfernt wurden: lösche aus user_question_progress
        $removedFromSolved = array_diff($oldSolved, $solved);
        foreach ($removedFromSolved as $questionId) {
            \App\Models\UserQuestionProgress::where('user_id', $user->id)
                ->where('question_id', $questionId)
                ->delete();
        }
        
        // Verarbeite Lehrgang-Fragen
        // Hole alle Lehrgänge-Parameter von der Request
        $allLehrgaenge = $user->enrolledLehrgaenge()->get();
        
        foreach ($allLehrgaenge as $lehrgang) {
            $paramName = 'lehrgang_' . $lehrgang->id . '_solved';
            $lehrgangSolved = $request->input($paramName, []);
            
            // Hole alle Fragen dieses Lehrgangs
            $allLehrgangQuestions = $lehrgang->questions()->pluck('id')->toArray();
            
            // Markiere ausgewählte Fragen als gelöst
            foreach ($lehrgangSolved as $questionId) {
                \App\Models\UserLehrgangProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'lehrgang_question_id' => $questionId,
                    ],
                    [
                        'consecutive_correct' => \App\Models\UserQuestionProgress::MASTERY_THRESHOLD,
                        'solved' => true,
                        'last_answered_at' => now(),
                    ]
                );
            }
            
            // Für Fragen die abgewählt wurden: setze solved = false
            $removedFromLehrgangSolved = array_diff($allLehrgangQuestions, $lehrgangSolved);
            foreach ($removedFromLehrgangSolved as $questionId) {
                $progress = \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                    ->where('lehrgang_question_id', $questionId)
                    ->first();
                
                if ($progress) {
                    $progress->update([
                        'consecutive_correct' => 0,
                        'solved' => false,
                    ]);
                }
            }
        }
        
        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'update_progress',
            null,
            null,
            null,
            $request
        );

        return redirect()->route('admin.users.index')->with('success', 'Fortschritt aktualisiert (Grundausbildung + Lehrgänge)');
    }
    public function pullForwardSpacedRepetition($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $updated = UserQuestionProgress::where('user_id', $user->id)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '>', Carbon::today())
            ->update(['next_review_at' => Carbon::today()]);

        return redirect()->route('admin.users.progress.edit', $user->id)
            ->with('success', $updated . ' Spaced-Repetition-Fragen auf heute vorgezogen');
    }

    public function setSpacedRepetitionTomorrow($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $tomorrow = Carbon::tomorrow()->startOfDay();

        // Alle Fragen-IDs holen, für die noch kein Progress existiert
        $allQuestionIds = Question::pluck('id');
        $existingIds = UserQuestionProgress::where('user_id', $user->id)
            ->pluck('question_id');
        $missingIds = $allQuestionIds->diff($existingIds);

        // Fehlende Einträge erstellen
        if ($missingIds->isNotEmpty()) {
            $inserts = $missingIds->map(fn ($qId) => [
                'user_id' => $user->id,
                'question_id' => $qId,
                'consecutive_correct' => 0,
                'next_review_at' => $tomorrow,
                'review_interval' => 0,
                'easiness_factor' => 2.5,
                'repetition_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            UserQuestionProgress::insert($inserts);
        }

        // Alle bestehenden Einträge auf morgen setzen
        $updated = UserQuestionProgress::where('user_id', $user->id)
            ->update(['next_review_at' => $tomorrow]);

        return redirect()->route('admin.users.progress.edit', $user->id)
            ->with('success', $updated . ' Fragen auf morgen (' . $tomorrow->format('d.m.Y') . ') gesetzt');
    }

    public function resetProgress($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        // Grundausbildung & Spaced Repetition
        UserQuestionProgress::where('user_id', $user->id)->delete();

        // Lehrgang-Fortschritt
        \App\Models\UserLehrgangProgress::where('user_id', $user->id)->delete();

        // Prüfungsstatistiken inkl. zugehöriger Fragenstatistiken
        $examIds = \App\Models\ExamStatistic::where('user_id', $user->id)->pluck('id');
        if ($examIds->isNotEmpty()) {
            \App\Models\QuestionStatistic::whereIn('exam_statistic_id', $examIds)->delete();
        }
        \App\Models\QuestionStatistic::where('user_id', $user->id)->delete();
        \App\Models\ExamStatistic::where('user_id', $user->id)->delete();

        // Gamification-Notifications löschen
        \App\Models\Notification::where('user_id', $user->id)->delete();

        // XP-Verlauf löschen
        \App\Models\XpHistory::where('user_id', $user->id)->delete();

        // User-Felder zurücksetzen
        $user->solved_questions       = [];
        $user->exam_failed_questions  = [];
        $user->exam_passed_count      = 0;
        // Gamification
        $user->points                 = 0;
        $user->weekly_points          = 0;
        $user->weekly_reset_at        = null;
        $user->level                  = 1;
        $user->streak_days            = 0;
        $user->last_activity_date     = null;
        $user->achievements           = [];
        $user->daily_questions_solved = 0;
        $user->daily_questions_date   = null;
        $user->save();

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'reset_progress',
            null,
            null,
            null,
            request()
        );

        return redirect()->route('admin.users.progress.edit', $user->id)
            ->with('success', 'Fortschritt von ' . $user->name . ' wurde vollständig zurückgesetzt.');
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

        $users = User::all();

        return view('admin.users', compact('users'));
    }

    public function edit($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'useroll' => 'required|in:user,admin,contributor',
            'points' => 'nullable|integer|min:0',
        ]);

        $admin = auth()->user();

        // Track changes before saving
        $changes = [];
        if ($user->name !== $request->name) {
            $changes[] = ['field' => 'name', 'old' => $user->name, 'new' => $request->name];
        }
        if ($user->email !== $request->email) {
            $changes[] = ['field' => 'email', 'old' => $user->email, 'new' => $request->email];
        }
        if ($user->useroll !== $request->useroll) {
            $changes[] = ['field' => 'role', 'old' => $user->useroll, 'new' => $request->useroll];
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->useroll = $request->useroll;

        if ($request->has('points')) {
            $oldPoints = $user->points;
            $user->points = (int) $request->points;
            $user->level = $this->calculateLevelFromPoints($user->points);
            if ($oldPoints != $user->points) {
                $changes[] = ['field' => 'points', 'old' => $oldPoints, 'new' => $user->points];
            }
        }

        $user->save();

        // Log each changed field
        foreach ($changes as $change) {
            AdminAuditLog::logChange(
                $admin,
                $user->id,
                'update_field',
                $change['field'],
                $change['old'],
                $change['new'],
                $request
            );
        }

        return redirect()->route('admin.users.index')->with('success', 'Nutzer aktualisiert');
    }

    public function verify($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $user->email_verified_at = now();
        $user->save();

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'verify_email',
            null,
            null,
            now()->format('Y-m-d H:i:s'),
            request()
        );

        return redirect()->route('admin.users.index')->with('success', 'E-Mail von ' . $user->name . ' wurde verifiziert.');
    }

    public function auditLog($id)
    {
        $this->abortIfNotAdmin();
        $logs = AdminAuditLog::where('user_id', $id)
            ->with('admin:id,name')
            ->orderByDesc('created_at')
            ->take(50)
            ->get();
        return response()->json($logs);
    }

    /**
     * Liefert aggregierte Fortschrittsdaten für das Admin-Fortschritts-Modal
     * (Grundausbildung pro Lernabschnitt, Zusatzfragen pro LA, Lehrgänge).
     */
    public function progressJson($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;

        // ---- Grundausbildung gruppiert nach Lernabschnitt ----
        $questions = Question::orderByRaw('CAST(lernabschnitt AS UNSIGNED), nummer')->get();
        $progressByQ = UserQuestionProgress::where('user_id', $user->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $grundausbildung = $questions->groupBy('lernabschnitt')->map(function ($qs, $la) use ($progressByQ, $threshold) {
            return $this->aggregateCounts($qs, fn($q) => $progressByQ->get($q->id), $threshold, [
                'id'    => (string) $la,
                'title' => 'Lernabschnitt ' . $la,
            ]);
        })->sortBy(fn($r) => (int) $r['id'])->values()->all();

        // ---- Zusatzfragen gruppiert nach Lernabschnitt ----
        $extras = \App\Models\ExtraQuestion::orderBy('lernabschnitt')->get();
        $extraProgressByQ = \App\Models\UserExtraQuestionProgress::where('user_id', $user->id)
            ->whereIn('extra_question_id', $extras->pluck('id'))
            ->get()
            ->keyBy('extra_question_id');

        $zusatzfragen = $extras->groupBy('lernabschnitt')->map(function ($qs, $la) use ($extraProgressByQ, $threshold) {
            return $this->aggregateCounts($qs, fn($q) => $extraProgressByQ->get($q->id), $threshold, [
                'id'    => (string) $la,
                'title' => 'Zusatzfragen LA ' . $la,
            ]);
        })->sortBy(fn($r) => (int) $r['id'])->values()->all();

        // ---- Lehrgänge ----
        $allLehrgaenge = \App\Models\Lehrgang::orderBy('lehrgang')->get();
        $enrolledIds = $user->enrolledLehrgaenge()->pluck('lehrgaenge.id')->all();

        $lehrgaenge = $allLehrgaenge->map(function ($l) use ($user, $enrolledIds, $threshold) {
            $isEnrolled = in_array($l->id, $enrolledIds);
            $qs = $l->questions()->get();
            $progress = \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                ->whereIn('lehrgang_question_id', $qs->pluck('id'))
                ->get()
                ->keyBy('lehrgang_question_id');

            $row = $this->aggregateCounts($qs, fn($q) => $progress->get($q->id), $threshold, [
                'id'       => $l->id,
                'code'     => strtoupper(\Illuminate\Support\Str::limit($l->slug, 6, '')),
                'title'    => $l->lehrgang,
                'enrolled' => $isEnrolled,
            ]);
            return $row;
        })->values()->all();

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'level' => (int) ($user->level ?? 1),
                'xp'    => (int) ($user->points ?? 0),
            ],
            'threshold'       => $threshold,
            'grundausbildung' => $grundausbildung,
            'zusatzfragen'    => $zusatzfragen,
            'lehrgaenge'      => $lehrgaenge,
        ]);
    }

    /**
     * Aggregiert {mastered, partial, sr, open, total} für eine Frage-Collection.
     */
    private function aggregateCounts($questions, callable $progressOf, int $threshold, array $base): array
    {
        $mastered = 0;
        $partial  = 0;
        $sr       = 0;
        $open     = 0;
        foreach ($questions as $q) {
            $p = $progressOf($q);
            if (!$p || (int) $p->consecutive_correct === 0) {
                $open++;
            } elseif ((int) $p->consecutive_correct >= $threshold) {
                $mastered++;
            } else {
                $partial++;
            }
            // Spaced Repetition: zählt zusätzlich, wenn fällige Wiederholung in Vergangenheit
            if ($p && isset($p->next_review_at) && $p->next_review_at && (int) $p->consecutive_correct < $threshold) {
                $sr++;
            }
        }
        return array_merge($base, [
            'total'    => $questions->count(),
            'mastered' => $mastered,
            'partial'  => $partial,
            'sr'       => $sr,
            'open'     => $open,
        ]);
    }

    /**
     * Liefert die Einzelfragen eines Moduls (lazy load beim Aufklappen im Modal).
     */
    public function progressModuleJson(Request $request, $id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $type     = $request->query('type');
        $moduleId = $request->query('module_id');
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;

        $questions = $this->resolveModuleQuestions($type, $moduleId);
        $progressByQ = $this->resolveProgressByQuestionId($type, $user->id, $questions->pluck('id')->all());

        $items = $questions->map(function ($q) use ($progressByQ, $threshold, $type) {
            $p = $progressByQ->get($q->id);
            $streak = $p ? (int) $p->consecutive_correct : 0;
            $sr     = $p && isset($p->next_review_at) && $p->next_review_at && $streak < $threshold;
            return [
                'id'         => $q->id,
                'label'      => $this->questionLabel($q, $type),
                'streak'     => $streak,
                'sr'         => $sr,
                'isMastered' => $streak >= $threshold,
            ];
        })->values()->all();

        return response()->json([
            'type'      => $type,
            'moduleId'  => $moduleId,
            'threshold' => $threshold,
            'questions' => $items,
        ]);
    }

    /**
     * Aktion auf eine einzelne Frage (action: mastered|sr|reset|increment).
     */
    public function progressUpdateQuestion(Request $request, $id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $request->validate([
            'type'        => 'required|in:grundausbildung,zusatzfragen,lehrgaenge',
            'question_id' => 'required|integer',
            'action'      => 'required|in:mastered,sr,reset,increment',
        ]);

        $type       = $request->input('type');
        $questionId = (int) $request->input('question_id');
        $action     = $request->input('action');
        $threshold  = UserQuestionProgress::MASTERY_THRESHOLD;

        $result = $this->applyProgressAction($type, $user->id, $questionId, $action, $threshold);

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'update_progress_question',
            $type . '#' . $questionId,
            null,
            $action,
            $request
        );

        return response()->json($result);
    }

    /**
     * Bulk-Aktion auf alle Fragen eines Moduls (action: mastered|sr|reset).
     */
    public function progressUpdateModuleBulk(Request $request, $id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        $request->validate([
            'type'      => 'required|in:grundausbildung,zusatzfragen,lehrgaenge',
            'module_id' => 'required',
            'action'    => 'required|in:mastered,sr,reset',
        ]);

        $type      = $request->input('type');
        $moduleId  = $request->input('module_id');
        $action    = $request->input('action');
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;

        $questions = $this->resolveModuleQuestions($type, $moduleId);
        foreach ($questions as $q) {
            $this->applyProgressAction($type, $user->id, $q->id, $action, $threshold);
        }

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'update_progress_module',
            $type . ':' . $moduleId,
            null,
            $action . ' (' . $questions->count() . ' Fragen)',
            $request
        );

        // Aggregat zurückgeben
        $progressByQ = $this->resolveProgressByQuestionId($type, $user->id, $questions->pluck('id')->all());
        $agg = $this->aggregateCounts($questions, fn($q) => $progressByQ->get($q->id), $threshold, []);

        return response()->json([
            'type'     => $type,
            'moduleId' => $moduleId,
            'counts'   => [
                'total'    => $agg['total'],
                'mastered' => $agg['mastered'],
                'partial'  => $agg['partial'],
                'sr'       => $agg['sr'],
                'open'     => $agg['open'],
            ],
        ]);
    }

    private function resolveModuleQuestions(string $type, $moduleId)
    {
        if ($type === 'grundausbildung') {
            return Question::where('lernabschnitt', (string) $moduleId)
                ->orderBy('nummer')
                ->get();
        }
        if ($type === 'zusatzfragen') {
            return \App\Models\ExtraQuestion::where('lernabschnitt', (string) $moduleId)
                ->orderBy('id')
                ->get();
        }
        // lehrgaenge
        $lehrgang = \App\Models\Lehrgang::findOrFail($moduleId);
        return $lehrgang->questions()
            ->orderByRaw('CAST(lernabschnitt AS UNSIGNED)')
            ->orderBy('nummer')
            ->get();
    }

    private function resolveProgressByQuestionId(string $type, int $userId, array $questionIds)
    {
        if (empty($questionIds)) return collect();

        if ($type === 'grundausbildung') {
            return UserQuestionProgress::where('user_id', $userId)
                ->whereIn('question_id', $questionIds)
                ->get()->keyBy('question_id');
        }
        if ($type === 'zusatzfragen') {
            return \App\Models\UserExtraQuestionProgress::where('user_id', $userId)
                ->whereIn('extra_question_id', $questionIds)
                ->get()->keyBy('extra_question_id');
        }
        return \App\Models\UserLehrgangProgress::where('user_id', $userId)
            ->whereIn('lehrgang_question_id', $questionIds)
            ->get()->keyBy('lehrgang_question_id');
    }

    private function questionLabel($q, string $type): string
    {
        if ($type === 'grundausbildung') {
            return 'LA ' . $q->lernabschnitt . ' · #' . $q->nummer;
        }
        if ($type === 'zusatzfragen') {
            return 'LA ' . $q->lernabschnitt . ' · ZF #' . $q->id;
        }
        $nr = $q->nummer ?? $q->id;
        return 'LA ' . ($q->lernabschnitt ?? '–') . ' · #' . $nr;
    }

    /**
     * Wendet eine Aktion (mastered|sr|reset|increment) auf eine einzelne Frage an
     * und gibt den neuen Status zurück.
     */
    private function applyProgressAction(string $type, int $userId, int $questionId, string $action, int $threshold): array
    {
        $modelClass = match ($type) {
            'grundausbildung' => UserQuestionProgress::class,
            'zusatzfragen'    => \App\Models\UserExtraQuestionProgress::class,
            'lehrgaenge'      => \App\Models\UserLehrgangProgress::class,
        };
        $foreignKey = match ($type) {
            'grundausbildung' => 'question_id',
            'zusatzfragen'    => 'extra_question_id',
            'lehrgaenge'      => 'lehrgang_question_id',
        };

        $progress = $modelClass::where('user_id', $userId)
            ->where($foreignKey, $questionId)
            ->first();

        if ($action === 'reset') {
            if ($progress) $progress->delete();
            return ['streak' => 0, 'sr' => false, 'isMastered' => false];
        }

        if (!$progress) {
            $progress = new $modelClass();
            $progress->user_id = $userId;
            $progress->{$foreignKey} = $questionId;
            $progress->consecutive_correct = 0;
        }

        $table = $progress->getTable();
        $hasSrField   = \Illuminate\Support\Facades\Schema::hasColumn($table, 'next_review_at');
        $hasAnsweredAt = \Illuminate\Support\Facades\Schema::hasColumn($table, 'last_answered_at');

        $setSr      = function ($v) use ($progress, $hasSrField)    { if ($hasSrField)    $progress->next_review_at = $v; };
        $touchTime  = function ()  use ($progress, $hasAnsweredAt)  { if ($hasAnsweredAt) $progress->last_answered_at = now(); };

        if ($action === 'mastered') {
            $progress->consecutive_correct = $threshold;
            $setSr(null);
            $touchTime();
        } elseif ($action === 'sr') {
            $progress->consecutive_correct = 0;
            $setSr(now());
            $touchTime();
        } elseif ($action === 'increment') {
            $newStreak = min($threshold, (int) $progress->consecutive_correct + 1);
            $progress->consecutive_correct = $newStreak;
            if ($newStreak >= $threshold) $setSr(null);
            $touchTime();
        }

        if ($type === 'lehrgaenge') {
            $progress->solved = $progress->consecutive_correct >= $threshold;
        }

        $progress->save();

        $streak = (int) $progress->consecutive_correct;
        $sr = $hasSrField && $progress->next_review_at && $streak < $threshold;
        return [
            'streak'     => $streak,
            'sr'         => (bool) $sr,
            'isMastered' => $streak >= $threshold,
        ];
    }

    private function calculateLevelFromPoints(int $points): int
    {
        $level = 1;
        foreach (GamificationService::LEVEL_THRESHOLDS as $levelNum => $threshold) {
            if ($points >= $threshold) {
                $level = $levelNum;
            } else {
                break;
            }
        }
        return $level;
    }

    public function destroy(Request $request, $id, UserDeletionService $deletionService)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Du kannst deinen eigenen Account nicht über die Admin-Verwaltung löschen.');
        }

        $request->validate([
            'reason' => 'required|string|min:3|max:500',
        ], [
            'reason.required' => 'Aus DSGVO-Gründen ist ein Lösch-Grund erforderlich.',
            'reason.min'      => 'Der Lösch-Grund muss mindestens 3 Zeichen lang sein.',
        ]);

        $deletionService->deleteUser(
            $user,
            auth()->user(),
            $request->input('reason'),
            $request
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Nutzer und alle verbundenen Daten wurden DSGVO-konform gelöscht.');
    }

    public function xpHistory($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        $entries = $user->xpHistories()->paginate(50);

        return view('admin.xp-history', compact('user', 'entries'));
    }
}
