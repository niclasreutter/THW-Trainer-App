<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Services\GamificationService;

class PracticeController extends Controller
{
    /**
     * THW-Lernabschnittsnamen (offiziell 2022)
     */
    private const SECTION_NAMES = [
        1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
        2 => 'Arbeitssicherheit und Gesundheitsschutz', 
        3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
        4 => 'Arbeiten mit Leitern',
        5 => 'Stromerzeugung und Beleuchtung',
        6 => 'Metall-, Holz- und Steinbearbeitung',
        7 => 'Bewegen von Lasten',
        8 => 'Arbeiten am und auf dem Wasser',
        9 => 'Einsatzgrundlagen',
        10 => 'Grundlagen der Rettung und Bergung'
    ];


    /**
     * Zeige das Practice-Menü
     */
    public function menu()
    {
        $user = Auth::user();
        // Defensive Programmierung für Legacy-Daten
        $solved = $this->ensureArray($user->solved_questions);
        $failed = $this->ensureArray($user->exam_failed_questions);
        
        // Statistiken für jeden Lernabschnitt berechnen
        $sectionStats = [];
        for ($i = 1; $i <= 10; $i++) {
            $totalQuestions = Question::where('lernabschnitt', $i)->count();
            $sectionQuestionIds = Question::where('lernabschnitt', $i)->pluck('id')->toArray();
            $solvedInSection = count(array_intersect($solved, $sectionQuestionIds));
            
            $sectionStats[$i] = [
                'total' => $totalQuestions,
                'solved' => $solvedInSection
            ];
        }
        
        // Allgemeine Statistiken
        $totalQuestions = Question::count();
        $solvedCount = count($solved);
        $failedCount = count($failed);
        $unsolvedCount = $totalQuestions - $solvedCount;
        
        $sectionNames = self::SECTION_NAMES;
        return view('practice-menu', compact('sectionStats', 'totalQuestions', 'solvedCount', 'failedCount', 'unsolvedCount', 'sectionNames'));
    }

    /**
     * Alle Fragen üben (ungelöste bevorzugt)
     */
    public function all()
    {
        // Session zurücksetzen für neuen Modus
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        
        // Debug: Prüfe ob Route erreicht wird
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        
        $totalQuestions = Question::count();
        $unsolvedCount = Question::whereNotIn('id', $solved)->count();
        
        // Debug-Ausgabe
        \Log::info('Practice All Debug', [
            'total_questions' => $totalQuestions,
            'solved_count' => count($solved),
            'unsolved_count' => $unsolvedCount
        ]);
        
        // Entfernt: Kein Redirect mehr, auch wenn alle gelöst sind
        // Alle Fragen sollen trainiert werden können!
        
        return $this->practiceMode('all');
    }

    /**
     * Nur ungelöste Fragen üben
     */
    public function unsolved()
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('unsolved');
    }

    /**
     * Lernabschnitt üben
     */
    public function section($section)
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('section', $section);
    }

    /**
     * Fragen suchen
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        if (empty($searchTerm)) {
            return redirect()->route('practice.menu')->with('error', 'Bitte gib einen Suchbegriff ein.');
        }
        
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('search', $searchTerm);
    }

    /**
     * Zentrale Methode für verschiedene Practice-Modi
     */
    private function practiceMode($mode, $parameter = null)
    {
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        $failed = $this->ensureArray($user->exam_failed_questions);
        $skipped = session('practice_skipped', []);
        
        // Basis-Query je nach Modus
        $query = Question::query();
        
        switch ($mode) {
            case 'all':
                // Intelligente Priorisierung für Practice All:
                // 1. Falsch beantwortete Fragen aus Exams (höchste Priorität)
                // 2. Ungelöste Fragen
                // 3. Wenn alle gelöst: alle Fragen in zufälliger Reihenfolge
                
                $idsToShow = [];
                
                // 1. Falsch beantwortete Fragen zuerst (zufällig gemischt)
                $failedIds = array_values($failed);
                shuffle($failedIds);
                $idsToShow = array_merge($idsToShow, $failedIds);
                
                // 2. Ungelöste Fragen hinzufügen (nach Lernabschnitten sortiert, innerhalb zufällig)
                $unsolvedIds = [];
                for ($section = 1; $section <= 10; $section++) {
                    $sectionIds = Question::where('lernabschnitt', $section)
                        ->whereNotIn('id', $solved)
                        ->whereNotIn('id', $failed) // Nicht bereits in failed list
                        ->pluck('id')->toArray();
                    
                    shuffle($sectionIds);
                    $unsolvedIds = array_merge($unsolvedIds, $sectionIds);
                }
                $idsToShow = array_merge($idsToShow, $unsolvedIds);
                
                // 3. Wenn keine ungelösten/failed Fragen vorhanden: alle Fragen zufällig
                if (empty($idsToShow)) {
                    $allIds = Question::pluck('id')->toArray();
                    shuffle($allIds);
                    $idsToShow = $allIds;
                }
                
                // Debug-Ausgabe
                \Log::info('Practice Mode All Debug', [
                    'failed_count' => count($failed),
                    'unsolved_count' => count($unsolvedIds),
                    'total_ids_to_show' => count($idsToShow),
                    'showing_all_random' => empty($failed) && empty($unsolvedIds)
                ]);
                break;
                
            case 'unsolved':
                // Nur ungelöste Fragen zufällig sortieren
                $unsolvedIds = Question::whereNotIn('id', $solved)->pluck('id')->toArray();
                
                // Zufällige Sortierung der ungelösten Fragen
                shuffle($unsolvedIds);
                
                $idsToShow = $unsolvedIds;
                break;
                
            case 'section':
                // Fragen eines Lernabschnitts zufällig sortieren
                $allSectionIds = Question::where('lernabschnitt', $parameter)->pluck('id')->toArray();
                
                // Zufällige Sortierung der Fragen innerhalb des Lernabschnitts
                shuffle($allSectionIds);
                
                $idsToShow = $allSectionIds;
                break;
                
            case 'search':
                // Fragen mit Suchbegriff zufällig sortieren
                $searchIds = Question::where(function($q) use ($parameter) {
                    $q->where('frage', 'LIKE', '%' . $parameter . '%')
                      ->orWhere('antwort_a', 'LIKE', '%' . $parameter . '%')
                      ->orWhere('antwort_b', 'LIKE', '%' . $parameter . '%')
                      ->orWhere('antwort_c', 'LIKE', '%' . $parameter . '%');
                })->pluck('id')->toArray();
                
                // Zufällige Sortierung der Suchergebnisse
                shuffle($searchIds);
                
                $idsToShow = $searchIds;
                break;
                
            case 'bookmarked':
                // Gespeicherte Fragen (bereits in richtiger Reihenfolge)
                $idsToShow = $user->bookmarked_questions ?? [];
                break;
                
            default:
                $idsToShow = [];
        }
        
        // Geskippte Fragen temporär entfernen
        $idsToShow = array_diff($idsToShow, $skipped);
        
        if (empty($idsToShow)) {
            $message = $mode === 'unsolved' 
                ? 'Alle Fragen in diesem Bereich wurden bereits gelöst! 🎉'
                : 'Keine Fragen gefunden.';
            
            \Log::info('No questions found after skipped removal', [
                'mode' => $mode,
                'parameter' => $parameter,
                'skipped_count' => count($skipped)
            ]);
                
            return redirect()->route('practice.menu')->with('success', $message);
        }
        
        // Zusätzlicher Sicherheitscheck
        if (!isset($idsToShow[0])) {
            \Log::error('Practice IDs array issue after skipped', [
                'ids_to_show' => $idsToShow,
                'mode' => $mode
            ]);
            return redirect()->route('practice.menu')->with('error', 'Fehler beim Laden der Fragen.');
        }
        
        $question = Question::find($idsToShow[0]);
        
        // Nochmals prüfen ob Frage existiert
        if (!$question) {
            \Log::error('Question not found', [
                'question_id' => $idsToShow[0],
                'mode' => $mode
            ]);
            return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
        }
        
        \Log::info('Practice session starting', [
            'mode' => $mode,
            'question_id' => $question->id,
            'total_ids' => count($idsToShow)
        ]);
        
        // Fortschritt sollte immer die tatsächlich gelösten Fragen vs Gesamtfragen zeigen
        $totalQuestions = Question::count();
        $solvedCount = count($solved);
        $total = $totalQuestions;
        $progress = $solvedCount;
        
        // Session für aktuellen Modus speichern
        session([
            'practice_mode' => $mode,
            'practice_parameter' => $parameter,
            'practice_ids' => $idsToShow
        ]);
        
        return view('practice', compact('question', 'progress', 'total', 'mode'));
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        $skipped = session('practice_skipped', []);
        
        // Check if we're in a specific practice mode
        $practiceIds = session('practice_ids', []);
        $mode = session('practice_mode', 'all');
        
        if (!empty($practiceIds)) {
            // Continue with current practice session
            $idsToShow = $practiceIds; // Alle IDs aus der Session
            
            if ($request->has('skip_id')) {
                $skipId = $request->input('skip_id');
                // Entferne die geskippte Frage nur temporär von der Anzeige
                $idsToShow = array_diff($idsToShow, [$skipId]);
                
                // Füge zur geskippten Liste für diese Runde hinzu
                $skipped = array_merge($skipped, [$skipId]);
                session(['practice_skipped' => array_unique($skipped)]);
            } else {
                // Normale Anzeige: entferne nur bereits verarbeitete Fragen
                $idsToShow = array_diff($idsToShow, $skipped);
            }
            
            if (empty($idsToShow)) {
                session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
                return redirect()->route('practice.menu')->with('success', 'Alle Fragen in diesem Modus bearbeitet! 🎉');
            }
            
            // Sicherheitscheck vor Zugriff auf Array
            if (!isset($idsToShow[0])) {
                session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
                return redirect()->route('practice.menu')->with('error', 'Fehler beim Laden der nächsten Frage.');
            }
            
            $question = Question::find($idsToShow[0]);
            
            // Prüfe ob Frage existiert
            if (!$question) {
                session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
                return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
            }
            
            // Fortschritt sollte immer die tatsächlich gelösten Fragen vs Gesamtfragen zeigen
            $total = Question::count();
            $progress = count($solved);
            
        } else {
            // Legacy mode - redirect to menu
            return redirect()->route('practice.menu');
        }
        
        return view('practice', compact('question', 'progress', 'total', 'mode'));
    }


    public function submit(Request $request)
    {
        $question = Question::findOrFail($request->question_id);
        $userAnswer = collect($request->answer ?? []);
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
        $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();

        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        $skipped = session('practice_skipped', []);
        
        $gamificationResult = null;
        
        if ($isCorrect) {
            $solved[] = $question->id;
            $user->solved_questions = array_unique($solved);
            
            // Entferne Frage aus exam_failed_questions falls dort vorhanden
            $failed = $this->ensureArray($user->exam_failed_questions);
            $failed = array_diff($failed, [$question->id]);
            $user->exam_failed_questions = array_values($failed);
            
            $user->save();
            
            // Gamification: Punkte für richtige Antwort
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, true);
            
            // Entferne Frage aus geskippten Liste falls dort
            $skipped = array_diff($skipped, [$question->id]);
            session(['practice_skipped' => $skipped]);
            
            // WICHTIG: Entferne gelöste Frage auch aus der aktuellen Practice Session
            $practiceIds = session('practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['practice_ids' => array_values($practiceIds)]);
            }
        } else {
            // Bei falscher Antwort: Zu exam_failed_questions hinzufügen für zukünftige Priorisierung
            $failed = $this->ensureArray($user->exam_failed_questions);
            
            if (!in_array($question->id, $failed)) {
                $failed[] = $question->id;
                $user->exam_failed_questions = $failed;
                $user->save();
            }
            
            // Bei falscher Antwort: Frage nicht aus der Session entfernen, sondern weiter hinten einreihen
            $practiceIds = session('practice_ids', []);
            if (!empty($practiceIds)) {
                // Entferne aktuelle Frage und füge sie am Ende wieder hinzu
                $currentIndex = array_search($question->id, $practiceIds);
                if ($currentIndex !== false) {
                    unset($practiceIds[$currentIndex]);
                    $practiceIds[] = $question->id; // Am Ende hinzufügen
                    session(['practice_ids' => array_values($practiceIds)]);
                }
            }
            
            // Temporär zu skipped hinzufügen (nur für diese Anzeige)
            $skipped[] = $question->id;
            session(['practice_skipped' => array_unique($skipped)]);
        }
        
        // Aktuelle Practice Session Info
        $practiceIds = session('practice_ids', []);
        $mode = session('practice_mode', 'all');
        
        // Fortschritt sollte immer die tatsächlich gelösten Fragen vs Gesamtfragen zeigen
        $total = Question::count();
        $progress = count($solved);
        
        if ($isCorrect) {
            // Direkt zur nächsten Frage weiterleiten
            if ($gamificationResult) {
                session(['gamification_result' => $gamificationResult]);
            }
            
            // Debug: Log notifications
            $notifications = session('gamification_notifications', []);
            \Log::info('Gamification notifications in session: ' . json_encode($notifications));
            
            return redirect()->route('practice.index');
        }

        return view('practice', [
            'question' => $question,
            'isCorrect' => $isCorrect,
            'userAnswer' => $userAnswer,
            'progress' => $progress,
            'total' => $total,
            'mode' => $mode
        ]);
    }

    /**
     * Stellt sicher, dass ein Wert ein Array ist (für Legacy-Kompatibilität)
     */
    private function ensureArray($value)
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }
}
