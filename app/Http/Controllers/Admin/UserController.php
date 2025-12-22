<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
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
        $lehrgaenge = $user->enrolledLehrgaenge()->with('questions')->get();
        $lehrgangProgress = [];
        
        foreach ($lehrgaenge as $lehrgang) {
            // Gruppiere Fragen nach Lernabschnitten
            $questions_grouped = $lehrgang->questions()
                ->orderByRaw('CAST(lernabschnitt AS UNSIGNED)')
                ->orderBy('nummer')
                ->get()
                ->groupBy('lernabschnitt');
            
            // Berechne Fortschritt pro Lernabschnitt
            $sectionProgress = [];
            foreach ($questions_grouped as $section => $sectionQuestions) {
                $sectionIds = $sectionQuestions->pluck('id')->toArray();
                
                // Hole Fortschrittsdaten für diesen Abschnitt
                $progressData = \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                    ->whereIn('lehrgang_question_id', $sectionIds)
                    ->get();
                
                // Berechne Fortschrittsbalken-Logik
                $totalProgressPoints = 0;
                foreach ($progressData as $prog) {
                    $totalProgressPoints += min($prog->consecutive_correct, 2);
                }
                $maxProgressPoints = count($sectionIds) * 2;
                $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                
                // Zähle gelöste Fragen
                $solvedInSection = $progressData->where('solved', true)->count();
                
                $sectionProgress[$section] = [
                    'solved' => $solvedInSection,
                    'total' => count($sectionIds),
                    'percentage' => $progressPercent,
                ];
            }
            
            // Gesamtfortschritt für diesen Lehrgang
            $allQuestions = $lehrgang->questions()->get();
            $allProgressData = \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                ->whereIn('lehrgang_question_id', $allQuestions->pluck('id')->toArray())
                ->get();
            
            $totalProgressPoints = 0;
            foreach ($allProgressData as $prog) {
                $totalProgressPoints += min($prog->consecutive_correct, 2);
            }
            $maxProgressPoints = $allQuestions->count() * 2;
            $totalPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
            $totalSolved = $allProgressData->where('solved', true)->count();
            
            $lehrgangProgress[$lehrgang->id] = [
                'lehrgang' => $lehrgang,
                'sectionProgress' => $sectionProgress,
                'questions_grouped' => $questions_grouped,
                'totalSolved' => $totalSolved,
                'totalQuestions' => $allQuestions->count(),
                'totalPercent' => $totalPercent,
            ];
        }
        
        return view('admin.edit_progress', compact('user', 'questions', 'solved', 'failed', 'lehrgangProgress'));
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
        // Für alle als "gelöst" markierten Fragen: setze consecutive_correct = 2
        foreach ($solved as $questionId) {
            \App\Models\UserQuestionProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                ],
                [
                    'consecutive_correct' => 2, // Als gemeistert markieren
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
                        'consecutive_correct' => 2, // Als gemeistert markieren
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
        
        return redirect()->route('admin.users.index')->with('success', 'Fortschritt aktualisiert (Grundausbildung + Lehrgänge)');
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
            'useroll' => 'required|in:user,admin',
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->useroll = $request->useroll;
        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'Nutzer aktualisiert');
    }

    public function destroy($id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Nutzer gelöscht');
    }
}
