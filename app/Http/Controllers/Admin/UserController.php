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
        return view('admin.edit_progress', compact('user', 'questions', 'solved'));
    }

    public function updateProgress(Request $request, $id)
    {
        $this->abortIfNotAdmin();
        $user = User::findOrFail($id);
    $solved = $request->input('solved_questions', []);
    $failed = $request->input('exam_failed_questions', []);
    $user->solved_questions = $solved;
    $user->exam_failed_questions = $failed;
    $user->save();
    return redirect()->route('admin.users.index')->with('success', 'Fortschritt aktualisiert');
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
