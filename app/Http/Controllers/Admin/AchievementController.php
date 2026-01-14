<?php

namespace App\Http\Controllers\Admin;

use App\Models\Achievement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AchievementController extends Controller
{
    /**
     * Zeige alle Achievements
     */
    public function index()
    {
        $this->abortIfNotAdmin();

        $achievements = Achievement::withCount('users')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        // Gruppiere nach Kategorie
        $categories = [
            'general' => 'Allgemein',
            'questions' => 'Fragen',
            'streak' => 'Streak',
            'exam' => 'Prüfungen',
            'level' => 'Level',
        ];

        return view('admin.achievements.index', compact('achievements', 'categories'));
    }

    /**
     * Zeige Formular zum Erstellen eines neuen Achievements
     */
    public function create()
    {
        $this->abortIfNotAdmin();

        $categories = [
            'general' => 'Allgemein',
            'questions' => 'Fragen',
            'streak' => 'Streak',
            'exam' => 'Prüfungen',
            'level' => 'Level',
        ];

        return view('admin.achievements.create', compact('categories'));
    }

    /**
     * Speichere neues Achievement
     */
    public function store(Request $request)
    {
        $this->abortIfNotAdmin();

        $validated = $request->validate([
            'key' => 'required|string|unique:achievements,key|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'icon' => 'nullable|string|max:10',
            'category' => 'required|string|in:general,questions,streak,exam,level',
            'requirement_value' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Achievement::create($validated);

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement erfolgreich erstellt!');
    }

    /**
     * Zeige Formular zum Bearbeiten eines Achievements
     */
    public function edit($id)
    {
        $this->abortIfNotAdmin();

        $achievement = Achievement::findOrFail($id);

        $categories = [
            'general' => 'Allgemein',
            'questions' => 'Fragen',
            'streak' => 'Streak',
            'exam' => 'Prüfungen',
            'level' => 'Level',
        ];

        return view('admin.achievements.edit', compact('achievement', 'categories'));
    }

    /**
     * Aktualisiere Achievement
     */
    public function update(Request $request, $id)
    {
        $this->abortIfNotAdmin();

        $achievement = Achievement::findOrFail($id);

        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:achievements,key,' . $id,
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'icon' => 'nullable|string|max:10',
            'category' => 'required|string|in:general,questions,streak,exam,level',
            'requirement_value' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $achievement->sort_order;

        $achievement->update($validated);

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement erfolgreich aktualisiert!');
    }

    /**
     * Lösche Achievement
     */
    public function destroy($id)
    {
        $this->abortIfNotAdmin();

        $achievement = Achievement::findOrFail($id);
        $usersCount = $achievement->users()->count();

        if ($usersCount > 0) {
            return redirect()
                ->route('admin.achievements.index')
                ->with('error', "Achievement kann nicht gelöscht werden, da es von {$usersCount} Nutzern freigeschaltet wurde.");
        }

        $achievement->delete();

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement erfolgreich gelöscht!');
    }

    /**
     * Toggle active Status
     */
    public function toggleActive($id)
    {
        $this->abortIfNotAdmin();

        $achievement = Achievement::findOrFail($id);
        $achievement->is_active = !$achievement->is_active;
        $achievement->save();

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement-Status erfolgreich geändert!');
    }

    private function abortIfNotAdmin()
    {
        if (!auth()->check() || auth()->user()->useroll !== 'admin') {
            abort(403, 'Kein Zugriff');
        }
    }
}
