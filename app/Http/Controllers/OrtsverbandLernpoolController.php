<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolEnrollment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrtsverbandLernpoolController extends Controller
{
    use AuthorizesRequests;

    /**
     * Zeige alle Lernpools eines Ortsverbands (Ausbilder-View)
     */
    public function index(Ortsverband $ortsverband)
    {
        $this->authorize('viewAny', [OrtsverbandLernpool::class, $ortsverband]);
        
        $lernpools = $ortsverband->lernpools()->with('creator', 'enrollments')->get();
        
        return view('ortsverband.lernpools.index', [
            'ortsverband' => $ortsverband,
            'lernpools' => $lernpools,
        ]);
    }

    /**
     * Zeige Formular zum Erstellen eines neuen Lernpools
     */
    public function create(Ortsverband $ortsverband)
    {
        $this->authorize('create', [OrtsverbandLernpool::class, $ortsverband]);
        
        return view('ortsverband.lernpools.create', [
            'ortsverband' => $ortsverband,
        ]);
    }

    /**
     * Speichere einen neuen Lernpool
     */
    public function store(Request $request, Ortsverband $ortsverband)
    {
        $this->authorize('create', [OrtsverbandLernpool::class, $ortsverband]);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $lernpool = OrtsverbandLernpool::create([
            'ortsverband_id' => $ortsverband->id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . time(),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('ortsverband.lernpools.index', $ortsverband)
            ->with('success', 'Lernpool erfolgreich erstellt!');
    }

    /**
     * Zeige Details eines Lernpools (für Ausbilder)
     */
    public function show(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('view', [$lernpool, $ortsverband]);
        
        $questions = $lernpool->questions()->with('creator')->get();
        $enrollments = $lernpool->enrollments()->with('user')->get();
        
        // Gruppiere Fragen nach Lernabschnitt
        $questionsBySection = $questions->groupBy('learning_section')->toArray();
        
        $data = [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'questions' => $questions,
            'questionsBySection' => $questionsBySection,
            'enrollments' => $enrollments,
        ];
        
        // Wenn AJAX-Request, gib nur Modal-Inhalt zurück
        $isAjax = request()->ajax() || 
                  request()->header('X-Requested-With') === 'XMLHttpRequest' || 
                  request()->query('ajax') === '1' ||
                  request()->input('ajax') === '1';
        
        if ($isAjax) {
            return view('ortsverband.lernpools.show-modal', $data);
        }
        
        return view('ortsverband.lernpools.show', $data);
    }

    /**
     * Zeige Formular zum Bearbeiten eines Lernpools
     */
    public function edit(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        
        $data = [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
        ];
        
        // Wenn AJAX-Request, gib nur Modal-Inhalt zurück
        $isAjax = request()->ajax() || 
                  request()->header('X-Requested-With') === 'XMLHttpRequest' || 
                  request()->query('ajax') === '1' ||
                  request()->input('ajax') === '1';
        
        if ($isAjax) {
            return view('ortsverband.lernpools.edit-modal', $data);
        }
        
        return view('ortsverband.lernpools.edit', $data);
    }

    /**
     * Aktualisiere einen Lernpool
     */
    public function update(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('update', [$lernpool, $ortsverband]);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Stelle sicher, dass is_active als boolean behandelt wird
        $validated['is_active'] = (bool) $validated['is_active'];

        $lernpool->update($validated);

        return redirect()
            ->route('ortsverband.lernpools.index', $ortsverband)
            ->with('success', 'Lernpool aktualisiert!');
    }

    /**
     * Lösche einen Lernpool
     */
    public function destroy(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $this->authorize('delete', [$lernpool, $ortsverband]);
        
        $lernpool->delete();

        return redirect()
            ->route('ortsverband.lernpools.index', $ortsverband)
            ->with('success', 'Lernpool gelöscht!');
    }

    /**
     * Schreibe User in Lernpool ein
     */
    public function enroll(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();
        
        // Prüfe ob User bereits eingeschrieben ist
        $existing = OrtsverbandLernpoolEnrollment::where('user_id', $user->id)
            ->where('lernpool_id', $lernpool->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('ortsverband.show', $ortsverband)
                ->with('info', 'Du bist bereits in diesem Lernpool eingeschrieben.');
        }

        // Erstelle Enrollment
        OrtsverbandLernpoolEnrollment::create([
            'user_id' => $user->id,
            'lernpool_id' => $lernpool->id,
        ]);

        return redirect()
            ->route('ortsverband.lernpools.practice', [$ortsverband, $lernpool])
            ->with('success', 'Du bist jetzt in diesem Lernpool eingeschrieben!');
    }

    /**
     * Zeige Lernpools auf OV-Seite für Mitglieder
     */
    public function listForOv(Ortsverband $ortsverband)
    {
        $user = auth()->user();
        $lernpools = $ortsverband->lernpools()->where('is_active', true)->get();
        
        $enrolledIds = $user 
            ? $user->enrolledLernpools()->pluck('lernpool_id')->toArray()
            : [];

        return view('ortsverband.lernpools.list', [
            'ortsverband' => $ortsverband,
            'lernpools' => $lernpools,
            'enrolledIds' => $enrolledIds,
        ]);
    }
}
