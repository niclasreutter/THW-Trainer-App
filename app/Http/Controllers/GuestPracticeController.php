<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionStatistic;

class GuestPracticeController extends Controller
{
    /**
     * Zeige das Guest Practice-Menü
     */
    public function menu()
    {
        return view('guest.practice-menu');
    }

    /**
     * Alle Fragen üben (Gast-Modus)
     */
    public function all()
    {
        // Session zurücksetzen für neuen Modus
        session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
        
        return $this->practiceMode('all');
    }

    /**
     * Zentrale Methode für verschiedene Practice-Modi (Gast)
     */
    private function practiceMode($mode, $parameter = null)
    {
        $skipped = session('guest_practice_skipped', []);
        
        // Basis-Query je nach Modus
        $query = Question::query();
        
        switch ($mode) {
            case 'all':
                // Alle Fragen in komplett zufälliger Reihenfolge (Gästemodus)
                $allIds = Question::pluck('id')->toArray();
                shuffle($allIds);
                $idsToShow = $allIds;
                break;
                
            default:
                $idsToShow = [];
        }
        
        // Geskippte Fragen temporär entfernen
        $idsToShow = array_diff($idsToShow, $skipped);
        
        if (empty($idsToShow)) {
            $message = 'Alle Fragen wurden bereits bearbeitet! 🎉';
                
            return redirect()->route('guest.practice.menu')->with('success', $message);
        }
        
        // Zusätzlicher Sicherheitscheck
        if (!isset($idsToShow[0])) {
            return redirect()->route('guest.practice.menu')->with('error', 'Fehler beim Laden der Fragen.');
        }
        
        $question = Question::find($idsToShow[0]);
        
        // Nochmals prüfen ob Frage existiert
        if (!$question) {
            return redirect()->route('guest.practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
        }
        
        // Session für aktuellen Modus speichern
        session([
            'guest_practice_mode' => $mode,
            'guest_practice_parameter' => $parameter,
            'guest_practice_ids' => $idsToShow
        ]);
        
        $totalQuestions = Question::count();
        $progress = 0; // Im Gast-Modus keine Fortschrittsverfolgung
        $total = $totalQuestions;
        
        return view('guest.practice', compact('question', 'progress', 'total', 'mode'));
    }

    public function show(Request $request)
    {
        $skipped = session('guest_practice_skipped', []);
        
        // Check if we're in a specific practice mode
        $practiceIds = session('guest_practice_ids', []);
        $mode = session('guest_practice_mode', 'all');
        
        if (!empty($practiceIds)) {
            // Continue with current practice session
            $idsToShow = $practiceIds; // Alle IDs aus der Session
            
            if ($request->has('skip_id')) {
                $skipId = $request->input('skip_id');
                // Entferne die geskippte Frage nur temporär von der Anzeige
                $idsToShow = array_diff($idsToShow, [$skipId]);
                
                // Füge zur geskippten Liste für diese Runde hinzu
                $skipped = array_merge($skipped, [$skipId]);
                session(['guest_practice_skipped' => array_unique($skipped)]);
            } else {
                // Normale Anzeige: entferne nur bereits verarbeitete Fragen
                $idsToShow = array_diff($idsToShow, $skipped);
            }
            
            if (empty($idsToShow)) {
                session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
                return redirect()->route('guest.practice.menu')->with('success', 'Alle Fragen bearbeitet! 🎉');
            }
            
            // Sicherheitscheck vor Zugriff auf Array
            if (!isset($idsToShow[0])) {
                session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
                return redirect()->route('guest.practice.menu')->with('error', 'Fehler beim Laden der nächsten Frage.');
            }
            
            $question = Question::find($idsToShow[0]);
            
            // Prüfe ob Frage existiert
            if (!$question) {
                session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
                return redirect()->route('guest.practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
            }
            
            $total = Question::count();
            $progress = 0; // Im Gast-Modus keine Fortschrittsverfolgung
            
        } else {
            // Legacy mode - redirect to menu
            return redirect()->route('guest.practice.menu');
        }
        
        return view('guest.practice', compact('question', 'progress', 'total', 'mode'));
    }

    public function submit(Request $request)
    {
        $question = Question::findOrFail($request->question_id);
        $userAnswer = collect($request->answer ?? []);
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
        $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();

        // Anonyme Statistik erfassen
        QuestionStatistic::create([
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
        ]);

        $skipped = session('guest_practice_skipped', []);
        
        if ($isCorrect) {
            // Entferne Frage aus geskippten Liste falls dort
            $skipped = array_diff($skipped, [$question->id]);
            session(['guest_practice_skipped' => $skipped]);
            
            // WICHTIG: Entferne gelöste Frage auch aus der aktuellen Practice Session
            $practiceIds = session('guest_practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['guest_practice_ids' => array_values($practiceIds)]);
            }
        } else {
            // Bei falscher Antwort: Frage nicht aus der Session entfernen, sondern weiter hinten einreihen
            $practiceIds = session('guest_practice_ids', []);
            if (!empty($practiceIds)) {
                // Entferne aktuelle Frage und füge sie am Ende wieder hinzu
                $currentIndex = array_search($question->id, $practiceIds);
                if ($currentIndex !== false) {
                    unset($practiceIds[$currentIndex]);
                    $practiceIds[] = $question->id; // Am Ende hinzufügen
                    session(['guest_practice_ids' => array_values($practiceIds)]);
                }
            }
            
            // Temporär zu skipped hinzufügen (nur für diese Anzeige)
            $skipped[] = $question->id;
            session(['guest_practice_skipped' => array_unique($skipped)]);
        }
        
        // Aktuelle Practice Session Info
        $practiceIds = session('guest_practice_ids', []);
        $mode = session('guest_practice_mode', 'all');
        
        $total = Question::count();
        $progress = 0; // Im Gast-Modus keine Fortschrittsverfolgung
        
        if ($isCorrect) {
            // Direkt zur nächsten Frage weiterleiten
            return redirect()->route('guest.practice.index');
        }

        return view('guest.practice', [
            'question' => $question,
            'isCorrect' => $isCorrect,
            'userAnswer' => $userAnswer,
            'progress' => $progress,
            'total' => $total,
            'mode' => $mode
        ]);
    }
}
