<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrtsverbandController extends Controller
{
    /**
     * Zeigt die Übersicht der Ortsverbände des Users
     */
    public function index()
    {
        $user = Auth::user();
        
        // User kann nur in einem Ortsverband sein - direkt weiterleiten
        $ortsverband = $user->ortsverbande()->first();
        
        if ($ortsverband) {
            // Prüfe ob User Ausbildungsbeauftragter ist
            if ($ortsverband->isAusbildungsbeauftragter($user)) {
                return redirect()->route('ortsverband.dashboard', $ortsverband);
            }
            return redirect()->route('ortsverband.show', $ortsverband);
        }
        
        // Kein Ortsverband - zeige Erstellen/Beitreten Seite
        return view('ortsverband.index');
    }

    /**
     * Zeigt das Formular zum Erstellen eines Ortsverbands
     */
    public function create()
    {
        $user = Auth::user();
        
        // Prüfe ob User bereits in einem Ortsverband ist
        $existingOrtsverband = $user->ortsverbände->first();
        if ($existingOrtsverband) {
            return redirect()->route('ortsverband.index')
                           ->with('error', 'Du bist bereits Mitglied eines Ortsverbands.');
        }
        
        return view('ortsverband.create');
    }

    /**
     * Erstellt einen neuen Ortsverband
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Prüfe ob User bereits einen Ortsverband hat
        if ($user->ownedOrtsverband) {
            return redirect()->route('ortsverband.show', $user->ownedOrtsverband)
                           ->with('error', 'Du hast bereits einen Ortsverband erstellt.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        // Erstelle Ortsverband
        $ortsverband = Ortsverband::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => $user->id,
        ]);
        
        // Füge Ersteller als Ausbildungsbeauftragter hinzu
        $ortsverband->members()->attach($user->id, [
            'role' => 'ausbildungsbeauftragter',
            'joined_at' => now()
        ]);
        
        return redirect()->route('ortsverband.show', $ortsverband)
                       ->with('success', 'Ortsverband erfolgreich erstellt!');
    }

    /**
     * Zeigt Details eines Ortsverbands
     */
    public function show(Request $request, Ortsverband $ortsverband)
    {
        $user = Auth::user();

        // Admin kann alle Ortsverbände sehen (via Session check)
        $isAdminViewing = session('admin_viewing_ortsverband_id') === $ortsverband->id;

        // Prüfe ob User Mitglied ist oder Admin ist
        if (!$ortsverband->isMember($user) && !$isAdminViewing && !$user->is_admin) {
            abort(403, 'Du bist kein Mitglied dieses Ortsverbands.');
        }

        $isAusbildungsbeauftragter = $ortsverband->isAusbildungsbeauftragter($user) && !$isAdminViewing;

        // Statistiken nur für Ausbildungsbeauftragte
        $stats = null;
        if ($isAusbildungsbeauftragter) {
            $stats = $ortsverband->getAverageStats();
        }

        // Sammle alle verfügbaren Tags für den Filter
        $allTags = $ortsverband->lernpools()
            ->where('is_active', true)
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $selectedTag = $request->get('tag');

        // Mitglieder-Fortschritt für Rangliste
        $memberProgress = null;
        if ($ortsverband->ranking_visible) {
            $memberProgress = $ortsverband->getMemberProgress()->take(10);
        }

        return view('ortsverband.show', compact('ortsverband', 'isAusbildungsbeauftragter', 'stats', 'isAdminViewing', 'allTags', 'selectedTag', 'memberProgress'));
    }

    /**
     * Zeigt das Bearbeitungsformular
     */
    public function edit(Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        return view('ortsverband.edit', compact('ortsverband'));
    }

    /**
     * Aktualisiert einen Ortsverband
     */
    public function update(Request $request, Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        $ortsverband->update($validated);
        
        return redirect()->route('ortsverband.show', $ortsverband)
                       ->with('success', 'Ortsverband aktualisiert!');
    }

    /**
     * Löscht einen Ortsverband
     */
    public function destroy(Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $ortsverband->delete();
        
        return redirect()->route('ortsverband.index')
                       ->with('success', 'Ortsverband gelöscht.');
    }

    /**
     * Zeigt die Mitgliederliste mit Statistiken
     */
    public function members(Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        // Alle Mitglieder für die Liste
        $allMemberProgress = $ortsverband->getMemberProgress();
        
        // Nur normale Mitglieder (keine Ausbilder) für die Statistiken
        $memberProgress = $allMemberProgress->filter(fn($m) => $m['role'] === 'member');
        
        // Ausbilder separat für Anzeige
        $ausbilderProgress = $allMemberProgress->filter(fn($m) => $m['role'] === 'ausbildungsbeauftragter');
        
        return view('ortsverband.members', compact('ortsverband', 'memberProgress', 'ausbilderProgress'));
    }

    /**
     * Entfernt ein Mitglied
     */
    public function removeMember(Ortsverband $ortsverband, User $user)
    {
        $currentUser = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($currentUser)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        // Kann sich nicht selbst entfernen
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Du kannst dich nicht selbst entfernen.');
        }
        
        $ortsverband->members()->detach($user->id);
        
        return back()->with('success', 'Mitglied entfernt.');
    }

    /**
     * Ändert die Rolle eines Mitglieds
     */
    public function changeRole(Request $request, Ortsverband $ortsverband, User $user)
    {
        $currentUser = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($currentUser)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $validated = $request->validate([
            'role' => 'required|in:ausbildungsbeauftragter,member'
        ]);
        
        $ortsverband->members()->updateExistingPivot($user->id, [
            'role' => $validated['role']
        ]);
        
        return back()->with('success', 'Rolle aktualisiert.');
    }

    /**
     * Dashboard mit umfassenden Statistiken
     */
    public function dashboard(Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        // Nur normale Mitglieder (keine Ausbilder) für Statistiken
        $allProgress = $ortsverband->getMemberProgress();
        $memberProgress = $allProgress->filter(fn($m) => $m['role'] === 'member');
        $weaknesses = $ortsverband->getWeaknesses();
        $stats = $ortsverband->getAverageStats();
        
        return view('ortsverband.dashboard', compact('ortsverband', 'memberProgress', 'weaknesses', 'stats'));
    }

    /**
     * API Endpoint für Statistiken (optional für AJAX)
     */
    public function stats(Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        return response()->json([
            'member_progress' => $ortsverband->getMemberProgress(),
            'weaknesses' => $ortsverband->getWeaknesses(),
            'average_stats' => $ortsverband->getAverageStats()
        ]);
    }

    /**
     * Ortsverband als Mitglied verlassen
     */
    public function leave(Ortsverband $ortsverband)
    {
        $user = Auth::user();

        // Prüfe ob User Mitglied ist
        if (!$ortsverband->isMember($user)) {
            abort(403, 'Du bist kein Mitglied dieses Ortsverbands.');
        }

        // Prüfe ob User der einzige Ausbildungsbeauftragte ist
        if ($ortsverband->isAusbildungsbeauftragter($user)) {
            $ausbilderCount = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count();
            if ($ausbilderCount <= 1) {
                return back()->with('error', 'Du bist der einzige Ausbildungsbeauftragte. Ernenne zuerst einen anderen Ausbilder oder lösche den Ortsverband.');
            }
        }

        // Mitgliedschaft entfernen
        $ortsverband->members()->detach($user->id);

        return redirect()->route('ortsverband.index')
                       ->with('success', 'Du hast den Ortsverband "' . $ortsverband->name . '" verlassen.');
    }

    /**
     * Toggle Ranglisten-Sichtbarkeit
     */
    public function toggleRankingVisibility(Ortsverband $ortsverband)
    {
        $user = Auth::user();

        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }

        $ortsverband->ranking_visible = !$ortsverband->ranking_visible;
        $ortsverband->save();

        $message = $ortsverband->ranking_visible
            ? 'Rangliste ist jetzt für alle Mitglieder sichtbar!'
            : 'Rangliste ist jetzt nur noch für Ausbilder sichtbar.';

        return back()->with('success', $message);
    }
}
