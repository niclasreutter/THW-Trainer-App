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
        return view('admin.edit_progress', compact('user', 'questions', 'solved', 'failed'));
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
        
        // Synchronisiere mit user_question_progress
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
        
        return redirect()->route('admin.users.index')->with('success', 'Fortschritt aktualisiert (inkl. 2x-richtig-Logik)');
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
