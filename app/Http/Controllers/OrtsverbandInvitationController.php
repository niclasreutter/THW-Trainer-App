<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrtsverbandInvitationController extends Controller
{
    /**
     * Zeigt alle Einladungen eines Ortsverbands
     */
    public function index(Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $invitations = $ortsverband->invitations()
                                   ->with('creator', 'logs.user')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('ortsverband.invitations.index', compact('ortsverband', 'invitations'));
    }

    /**
     * Erstellt eine neue Einladung
     */
    public function store(Request $request, Ortsverband $ortsverband)
    {
        $user = Auth::user();
        
        if (!$ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_uses' => 'nullable|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now|before:' . now()->addYears(10)->format('Y-m-d'),
        ]);
        
        $invitation = OrtsverbandInvitation::create([
            'ortsverband_id' => $ortsverband->id,
            'code' => OrtsverbandInvitation::generateCode(),
            'name' => $validated['name'],
            'created_by' => $user->id,
            'max_uses' => $validated['max_uses'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        
        return back()->with('success', 'Einladung erstellt! Code: ' . $invitation->code);
    }

    /**
     * Löscht eine Einladung
     */
    public function destroy(Ortsverband $ortsverband, OrtsverbandInvitation $invitation)
    {
        $user = Auth::user();
        
        if (!$invitation->ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $invitation->delete();
        
        return back()->with('success', 'Einladung gelöscht.');
    }

    /**
     * Aktiviert/Deaktiviert eine Einladung
     */
    public function toggle(Ortsverband $ortsverband, OrtsverbandInvitation $invitation)
    {
        $user = Auth::user();
        
        if (!$invitation->ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }
        
        $invitation->update([
            'is_active' => !$invitation->is_active
        ]);
        
        $status = $invitation->is_active ? 'aktiviert' : 'deaktiviert';
        
        return back()->with('success', "Einladung {$status}.");
    }

    /**
     * Tritt einem Ortsverband über Einladungscode bei
     */
    public function join(string $code)
    {
        $invitation = OrtsverbandInvitation::findByCode($code);
        
        if (!$invitation) {
            return redirect()->route('dashboard')
                           ->with('error', 'Einladungscode ungültig.');
        }
        
        if (!$invitation->isValid()) {
            return redirect()->route('dashboard')
                           ->with('error', 'Diese Einladung ist nicht mehr gültig.');
        }
        
        $user = Auth::user();
        
        // Wenn nicht eingeloggt, zur Registrierung mit Code
        if (!$user) {
            return redirect()->route('register', ['code' => $code]);
        }
        
        try {
            $invitation->use($user);
            
            return redirect()->route('ortsverband.show', $invitation->ortsverband)
                           ->with('success', 'Erfolgreich dem Ortsverband beigetreten!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * Tritt einem Ortsverband per Code-Eingabe bei (für eingeloggte User)
     */
    public function joinByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);
        
        $code = trim($request->code);
        $invitation = OrtsverbandInvitation::findByCode($code);
        
        if (!$invitation) {
            return back()->with('error', 'Einladungscode ungültig. Bitte überprüfe den Code.');
        }
        
        if (!$invitation->isValid()) {
            return back()->with('error', 'Diese Einladung ist nicht mehr gültig oder abgelaufen.');
        }
        
        $user = Auth::user();
        
        try {
            $invitation->use($user);
            
            return redirect()->route('ortsverband.show', $invitation->ortsverband)
                           ->with('success', 'Erfolgreich dem Ortsverband "' . $invitation->ortsverband->name . '" beigetreten!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
