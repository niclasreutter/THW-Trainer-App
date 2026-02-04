<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    /**
     * Generiert QR-Code mit THW-Trainer Logo für Einladung
     */
    public function qrcode(Ortsverband $ortsverband, OrtsverbandInvitation $invitation)
    {
        $user = Auth::user();

        if (!$invitation->ortsverband->isAusbildungsbeauftragter($user)) {
            abort(403, 'Keine Berechtigung.');
        }

        // URL für die Einladung
        $url = route('register', ['code' => $invitation->code]);

        // Logo-Pfad
        $logoPath = public_path('logo-thwtrainer.png');

        // QR-Code generieren mit höherer Fehlerkorrektur
        $qrCodeBase = QrCode::format('png')
            ->size(600)
            ->errorCorrection('H') // 30% Fehlertoleranz
            ->margin(2)
            ->style('square')
            ->eye('square')
            ->generate($url);

        // QR-Code und Logo kombinieren mit GD
        $qrImage = imagecreatefromstring($qrCodeBase);
        $logo = imagecreatefrompng($logoPath);

        if (!$qrImage || !$logo) {
            return response($qrCodeBase)
                ->header('Content-Type', 'image/png');
        }

        // Dimensionen
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);
        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);

        // Quadratische Aussparung (20% des QR-Codes)
        $cutoutSize = $qrWidth * 0.20;
        $padding = 15;

        // Position der quadratischen Aussparung (zentriert)
        $cutoutX = ($qrWidth - $cutoutSize) / 2;
        $cutoutY = ($qrHeight - $cutoutSize) / 2;

        // Weißer Hintergrund für Logo (quadratische Aussparung)
        $white = imagecolorallocate($qrImage, 255, 255, 255);
        imagefilledrectangle(
            $qrImage,
            $cutoutX - $padding,
            $cutoutY - $padding,
            $cutoutX + $cutoutSize + $padding,
            $cutoutY + $cutoutSize + $padding,
            $white
        );

        // Logo proportional in die quadratische Fläche einpassen
        $logoRatio = $logoWidth / $logoHeight;
        if ($logoRatio > 1) {
            // Logo ist breiter als hoch
            $logoTargetWidth = $cutoutSize;
            $logoTargetHeight = $cutoutSize / $logoRatio;
        } else {
            // Logo ist höher als breit
            $logoTargetHeight = $cutoutSize;
            $logoTargetWidth = $cutoutSize * $logoRatio;
        }

        // Logo zentriert in der quadratischen Aussparung platzieren
        $logoX = $cutoutX + ($cutoutSize - $logoTargetWidth) / 2;
        $logoY = $cutoutY + ($cutoutSize - $logoTargetHeight) / 2;

        // Logo einfügen
        imagecopyresampled(
            $qrImage,
            $logo,
            $logoX,
            $logoY,
            0,
            0,
            $logoTargetWidth,
            $logoTargetHeight,
            $logoWidth,
            $logoHeight
        );

        // Ausgabe
        ob_start();
        imagepng($qrImage, null, 9);
        $output = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($logo);

        return response($output)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="qr-code-' . $invitation->code . '.png"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
