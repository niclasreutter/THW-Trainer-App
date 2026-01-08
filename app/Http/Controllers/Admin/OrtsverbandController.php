<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ortsverband;
use Illuminate\Http\Request;

class OrtsverbandController extends Controller
{
    /**
     * Zeigt alle Ortsverbände für Admin
     */
    public function index()
    {
        $ortsverbande = Ortsverband::with(['members', 'creator'])
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.ortsverband.index', compact('ortsverbande'));
    }

    /**
     * Simuliere, dass Admin in einen Ortsverband "eintritt" (nur View-Zugriff)
     */
    public function viewAs(Ortsverband $ortsverband)
    {
        session(['admin_viewing_ortsverband_id' => $ortsverband->id]);

        return redirect()->route('ortsverband.show', $ortsverband)
            ->with('info', 'Du betrachtest diesen Ortsverband als Admin.');
    }

    /**
     * Beende die Admin-View
     */
    public function exitView()
    {
        session()->forget('admin_viewing_ortsverband_id');
        
        return redirect()->route('admin.ortsverband.index')
            ->with('success', 'Admin-View beendet.');
    }

    /**
     * Lösche einen Ortsverband (Admin)
     */
    public function destroy(Ortsverband $ortsverband)
    {
        $name = $ortsverband->name;
        $ortsverband->delete();

        return redirect()->route('admin.ortsverband.index')
            ->with('success', "Ortsverband '{$name}' wurde gelöscht.");
    }
}
