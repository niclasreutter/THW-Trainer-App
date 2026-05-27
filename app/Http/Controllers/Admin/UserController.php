<?php
namespace App\Http\Controllers\Admin;

use App\Models\AdminAuditLog;
use App\Models\Question;
use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;
use App\Services\SpacedRepetitionService;
use App\Services\UserDeletionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function editProgress($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'view_progress_detail',
            null,
            null,
            'Detail-Fortschrittsansicht geöffnet',
            request()
        );

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

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'view_progress_detail',
            null,
            null,
            'Fortschritts-Modal geöffnet',
            request()
        );

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

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'view_xp_history',
            null,
            null,
            'XP-Verlauf geöffnet',
            request()
        );

        $entries = $user->xpHistories()->paginate(50);

        return view('admin.xp-history', compact('user', 'entries'));
    }
}
