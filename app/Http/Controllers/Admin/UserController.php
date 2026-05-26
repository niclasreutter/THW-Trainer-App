<?php
namespace App\Http\Controllers\Admin;

use App\Models\AdminAuditLog;
use App\Models\Question;
use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;
use App\Services\SpacedRepetitionService;
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
        
        // Hole alle aktiven Sessions aus der Datenbank
        $activeSessions = \DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(30)->timestamp)
            ->pluck('user_id')
            ->filter()
            ->toArray();
        
        $users = User::all()->map(function ($user) use ($activeSessions) {
            // Prüfe ob User in aktiven Sessions ist oder vor weniger als 30 Minuten aktiv war
            $user->is_online = in_array($user->id, $activeSessions) || 
                               $user->updated_at->diffInMinutes(now()) < 30;
            return $user;
        });
        
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

    public function destroy($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);

        AdminAuditLog::logChange(
            auth()->user(),
            $user->id,
            'delete',
            null,
            $user->name . ' <' . $user->email . '>',
            null,
            request()
        );

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Nutzer gelöscht');
    }

    public function xpHistory($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        $entries = $user->xpHistories()->paginate(50);

        return view('admin.xp-history', compact('user', 'entries'));
    }
}
