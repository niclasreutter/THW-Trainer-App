<?php

namespace App\Http\Controllers;

use App\Models\Lehrgang;
use App\Models\LehrgangQuestion;
use App\Models\LehrgangLernabschnitt;
use App\Models\UserLehrgangProgress;
use App\Models\LehrgangQuestionStatistic;
use App\Models\LehrgangQuestionIssue;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LehrgangController extends Controller
{
    /**
     * Zeige Liste aller Lehrgänge
     */
    public function index()
    {
        // Lade nur Lehrgänge, die Fragen haben
        $lehrgaenge = Lehrgang::has('questions')->get();
        $user = auth()->user();
        
        // Lade Enrollment-Status für jeden Lehrgang
        $enrolledIds = $user ? $user->enrolledLehrgaenge()->pluck('lehrgaenge.id')->toArray() : [];
        
        return view('lehrgaenge.index', [
            'lehrgaenge' => $lehrgaenge,
            'enrolledIds' => $enrolledIds,
        ]);
    }

    /**
     * Zeige Details eines einzelnen Lehrgangs
     */
    public function show($slug)
    {
        $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
        $user = auth()->user();
        
        // Check ob User eingeschrieben ist
        $isEnrolled = false;
        $userProgress = null;
        
        if ($user) {
            $enrollment = $user->enrolledLehrgaenge()
                ->where('lehrgaenge.id', $lehrgang->id)
                ->first();
            
            $isEnrolled = $enrollment !== null;
            
            if ($isEnrolled) {
                // Zähle gelöste Fragen
                $solvedCount = UserLehrgangProgress::where('user_id', $user->id)
                    ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                    ->where('solved', true)
                    ->count();
                
                $totalCount = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                
                $userProgress = [
                    'points' => $enrollment->pivot->punkte ?? 0,
                    'solved' => $solvedCount,
                    'total' => $totalCount,
                    'completed' => $enrollment->pivot->completed ?? false,
                ];
            }
        }
        
        // Gruppiere Fragen nach Lernabschnitten
        $questions = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)
            ->orderByRaw('CAST(lernabschnitt AS UNSIGNED)')
            ->orderBy('nummer')
            ->get()
            ->groupBy('lernabschnitt');
        
        // Hole alle Lernabschnitt-Namen für diesen Lehrgang
        // Key = lernabschnitt_nr (als String für einfachen Zugriff)
        $lernabschnitteNamen = LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [(string)$item->lernabschnitt_nr => $item->lernabschnitt];
            });
        
        // Erstelle Sections-Collection basierend auf den vorhandenen Fragen
        $sections = collect();
        foreach ($questions->keys()->sort(function($a, $b) {
            return (int)$a - (int)$b;
        }) as $sectionNr) {
            // Suche den Namen für diese Abschnittsnummer
            $sectionName = $lernabschnitteNamen->get((string)$sectionNr, "Lernabschnitt {$sectionNr}");
            
            $sections->push((object)[
                'lernabschnitt_nr' => $sectionNr,
                'lernabschnitt' => $sectionName,
            ]);
        }
        
        // Für Kompatibilität: Map von Nummer zu Name
        $lernabschnitte = $lernabschnitteNamen;
        
        // Berechne Fortschritt pro Lernabschnitt (nur wenn User eingeschrieben ist)
        $sectionProgress = [];
        if ($isEnrolled && $user) {
            foreach ($questions as $section => $sectionQuestions) {
                $sectionIds = $sectionQuestions->pluck('id')->toArray();
                
                // Hole Fortschrittsdaten für diesen Abschnitt
                $progressData = UserLehrgangProgress::where('user_id', $user->id)
                    ->whereIn('lehrgang_question_id', $sectionIds)
                    ->get();
                
                // Berechne Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
                $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
                $totalProgressPoints = 0;
                foreach ($progressData as $prog) {
                    $totalProgressPoints += min($prog->consecutive_correct, $threshold);
                }
                $maxProgressPoints = count($sectionIds) * $threshold;
                $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                
                // Zähle gelöste Fragen in diesem Abschnitt
                $solvedInSection = $progressData->where('solved', true)->count();
                
                $sectionProgress[$section] = [
                    'solved' => $solvedInSection,
                    'total' => count($sectionIds),
                    'percentage' => $progressPercent,
                ];
            }
        }
        
        return view('lehrgaenge.show', [
            'lehrgang' => $lehrgang,
            'isEnrolled' => $isEnrolled,
            'enrolledIds' => $isEnrolled ? [$lehrgang->id] : [],
            'userProgress' => $userProgress,
            'questions' => $questions,
            'sections' => $sections,
            'lernabschnitte' => $lernabschnitte,
            'sectionProgress' => $sectionProgress,
        ]);
    }

    /**
     * User in Lehrgang einschreiben
     */
    public function enroll(Request $request, $slug)
    {
        $user = auth()->user();
        $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
        
        // Check ob bereits eingeschrieben
        $existing = $user->enrolledLehrgaenge()
            ->where('lehrgaenge.id', $lehrgang->id)
            ->first();
        
        if ($existing) {
            return redirect()->route('lehrgaenge.show', $slug)
                ->with('info', 'Du bist bereits in diesem Lehrgang eingeschrieben.');
        }
        
        // Einschreiben
        $user->enrolledLehrgaenge()->attach($lehrgang->id, [
            'punkte' => 0,
            'completed' => false,
            'enrolled_at' => now(),
        ]);
        
        return redirect()->route('lehrgaenge.practice', $slug)
            ->with('success', 'Du bist jetzt in diesem Lehrgang eingeschrieben!');
    }

    /**
     * Practice-Seite: Zeige nächste Frage
     */
    public function practice($slug)
    {
        $user = auth()->user();
        $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
        
        // Check Enrollment
        $enrollment = $user->enrolledLehrgaenge()
            ->where('lehrgaenge.id', $lehrgang->id)
            ->first();
        
        if (!$enrollment) {
            return redirect()->route('lehrgaenge.show', $slug)
                ->with('error', 'Du musst dich erst einschreiben.');
        }
        
        // Hole offene Fragen (nicht 2x in Folge gelöst)
        $allQuestions = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)
            ->whereNotExists(function($query) use ($user) {
                $query->select('id')
                    ->from('user_lehrgang_progress')
                    ->where('user_id', $user->id)
                    ->where('lehrgang_question_id', 'lehrgaenge_questions.id')
                    ->where('solved', true);
            })
            ->pluck('id')
            ->toArray();
        
        // Initialisiere Session mit offenen Fragen wenn leer
        $practiceIds = session("lehrgaenge_{$lehrgang->id}_practice_ids", []);
        
        if (empty($practiceIds)) {
            if (empty($allQuestions)) {
                // Alle Fragen gelöst!
                return view('lehrgaenge.complete', [
                    'lehrgang' => $lehrgang,
                    'points' => $enrollment->pivot->punkte ?? 0,
                ]);
            }
            
            // Initialisiere mit allen offenen Fragen (geshuffelt)
            $practiceIds = $allQuestions;
            shuffle($practiceIds);
            session(["lehrgaenge_{$lehrgang->id}_practice_ids" => $practiceIds]);
        }
        
        // WICHTIG: Wenn gerade eine Frage beantwortet wurde (answer_result in Session),
        // zeige diese Frage nochmal (damit die Antwort angezeigt werden kann)
        $answerResult = session('answer_result');
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);
        
        if ($showAnsweredQuestion) {
            // Zeige die gerade beantwortete Frage nochmal
            $questionId = $answerResult['question_id'];
        } else {
            // Normale Anzeige: nächste Frage aus Session
            if (empty($practiceIds)) {
                // Alle Fragen in dieser Session bearbeitet
                session()->forget("lehrgaenge_{$lehrgang->id}_practice_ids");
                return redirect()->route('lehrgaenge.practice', $slug)
                    ->with('success', 'Alle Fragen in dieser Runde bearbeitet! 🎉');
            }
            
            $questionId = reset($practiceIds);
        }
        
        $question = LehrgangQuestion::findOrFail($questionId);
        
        // Lade/erstelle Fortschritt
        $progress = UserLehrgangProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lehrgang_question_id' => $question->id,
            ],
            [
                'consecutive_correct' => 0,
                'solved' => false,
                'failed' => false,
            ]
        );
        
        // Berechne Gesamt-Fortschritt (mit consecutive_correct)
        $solvedCount = UserLehrgangProgress::where('user_id', $user->id)
            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
            ->where('solved', true)
            ->count();
        
        $totalCount = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
        
        // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserLehrgangProgress::where('user_id', $user->id)
            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
            ->get();

        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, $threshold);
        }
        $maxProgressPoints = $totalCount * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        // Hole den Lernabschnittsnamen
        $lernabschnittName = LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
            ->where(function($q) use ($question) {
                $q->where('lernabschnitt_nr', $question->lernabschnitt)
                  ->orWhere('lernabschnitt_nr', (string)$question->lernabschnitt);
            })
            ->value('lernabschnitt');
        
        // Markiere die Frage
        $question->lehrgang = $lehrgang->lehrgang;
        $question->lehrgang_slug = $slug;
        $question->is_lehrgang = true;
        $question->lernabschnitt_name = $lernabschnittName ?? ("Lernabschnitt " . $question->lernabschnitt);
        
        return view('lehrgaenge.practice', [
            'question' => $question,
            'progress' => $solvedCount,
            'total' => $totalCount,
            'progressPercent' => $progressPercent,
            'user' => $user,
        ]);
    }

    /**
     * Practice für einen bestimmten Lernabschnitt
     */
    public function practiceSection($slug, $sectionNr)
    {
        $user = auth()->user();
        $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
        
        // Check Enrollment
        $enrollment = $user->enrolledLehrgaenge()
            ->where('lehrgaenge.id', $lehrgang->id)
            ->first();
        
        if (!$enrollment) {
            return redirect()->route('lehrgaenge.show', $slug)
                ->with('error', 'Du musst dich erst einschreiben.');
        }
        
        // Hole offene Fragen aus diesem Lernabschnitt
        $allQuestions = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)
            ->where('lernabschnitt', $sectionNr)
            ->whereNotExists(function($query) use ($user) {
                $query->select('id')
                    ->from('user_lehrgang_progress')
                    ->whereColumn('lehrgang_question_id', 'lehrgaenge_questions.id')
                    ->where('user_id', $user->id)
                    ->where('solved', true);
            })
            ->pluck('id')
            ->toArray();
        
        // Initialisiere Session mit offenen Fragen wenn leer
        $practiceIds = session("lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids", []);
        
        if (empty($practiceIds)) {
            if (empty($allQuestions)) {
                // Alle Fragen in diesem Abschnitt gelöst!
                return view('lehrgaenge.complete', [
                    'lehrgang' => $lehrgang,
                    'points' => $enrollment->pivot->punkte ?? 0,
                    'sectionCompleted' => true,
                    'sectionNr' => $sectionNr,
                ]);
            }
            
            // Initialisiere mit allen offenen Fragen (geshuffelt)
            $practiceIds = $allQuestions;
            shuffle($practiceIds);
            session(["lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids" => $practiceIds]);
        }
        
        // WICHTIG: Wenn gerade eine Frage beantwortet wurde
        $answerResult = session('answer_result');
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);
        
        if ($showAnsweredQuestion) {
            $questionId = $answerResult['question_id'];
        } else {
            if (empty($practiceIds)) {
                session()->forget("lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids");
                return redirect()->route('lehrgaenge.practice-section', ['slug' => $slug, 'sectionNr' => $sectionNr])
                    ->with('success', 'Alle Fragen in diesem Abschnitt bearbeitet! 🎉');
            }
            
            $questionId = reset($practiceIds);
        }
        
        $question = LehrgangQuestion::findOrFail($questionId);
        
        // Lade/erstelle Fortschritt
        $progress = UserLehrgangProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lehrgang_question_id' => $question->id,
            ],
            [
                'consecutive_correct' => 0,
                'solved' => false,
                'failed' => false,
            ]
        );
        
        // Berechne Gesamt-Fortschritt für diesen Abschnitt
        $solvedCount = UserLehrgangProgress::where('user_id', $user->id)
            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id)->where('lernabschnitt', $sectionNr))
            ->where('solved', true)
            ->count();
        
        $totalCount = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->where('lernabschnitt', $sectionNr)->count();
        
        // Neue Fortschrittsbalken-Logik
        $progressData = UserLehrgangProgress::where('user_id', $user->id)
            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id)->where('lernabschnitt', $sectionNr))
            ->get();
        
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, $threshold);
        }
        $maxProgressPoints = $totalCount * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        // Hole den Lernabschnitt Namen
        $sectionNr = (int)$sectionNr; // Stelle sicher, dass es eine Integer ist
        $lernabschnitt = LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
            ->where(function($q) use ($sectionNr) {
                $q->where('lernabschnitt_nr', $sectionNr)
                  ->orWhere('lernabschnitt_nr', (string)$sectionNr);
            })
            ->first();
        
        $lernabschnittName = $lernabschnitt?->lernabschnitt ?? "Lernabschnitt $sectionNr";
        
        // Markiere die Frage
        $question->lehrgang = $lehrgang->lehrgang . " - $lernabschnittName";
        $question->lehrgang_slug = $slug;
        $question->is_lehrgang = true;
        $question->section_nr = $sectionNr;
        $question->lernabschnitt_name = $lernabschnittName;
        
        return view('lehrgaenge.practice', [
            'question' => $question,
            'progress' => $solvedCount,
            'total' => $totalCount,
            'progressPercent' => $progressPercent,
            'currentSectionNr' => $sectionNr,
            'user' => $user,
        ]);
    }

    /**
     * Verarbeite Antwort
     */
    public function submitAnswer(Request $request, $slug)
    {
        $user = auth()->user();
        $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
        $gamification = new GamificationService();
        
        $validated = $request->validate([
            'question_id' => 'required|exists:lehrgaenge_questions,id',
            'answer' => 'nullable|array',
            'answer_mapping' => 'required|json',
        ]);
        
        $question = LehrgangQuestion::findOrFail($validated['question_id']);
        
        // Check ob Frage zum Lehrgang gehört
        if ($question->lehrgang_id != $lehrgang->id) {
            return response()->json(['error' => 'Ungültige Frage'], 400);
        }
        
        // Hole das Mapping aus dem Hidden Field
        $mappingJson = $request->input('answer_mapping');
        $mapping = json_decode($mappingJson, true);
        
        // User-Antworten (Positionen 0, 1, 2)
        $userAnswerPositions = $request->answer ?? [];
        
        // Mappe Positionen zurück auf Original-Buchstaben
        $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
            return $mapping[$position] ?? null;
        })->filter()->sort()->values();
        
        // Lade/erstelle Fortschritt
        $progress = UserLehrgangProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lehrgang_question_id' => $question->id,
            ],
            [
                'consecutive_correct' => 0,
                'solved' => false,
                'failed' => false,
            ]
        );
        
        // Parse Lösung (z.B. "A,B" oder "A")
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s))->sort()->values();
        $isCorrect = $userAnswer->all() === $solution->all();
        
        // Speichere Statistik in separater Tabelle (wichtig für Auswertungen!)
        LehrgangQuestionStatistic::create([
            'user_id' => $user->id,
            'lehrgang_question_id' => $question->id,
            'is_correct' => $isCorrect,
        ]);
        
        if ($isCorrect) {
            $progress->consecutive_correct++;
            
            if ($progress->consecutive_correct == UserQuestionProgress::MASTERY_THRESHOLD) {
                $progress->solved = true;
                
                // Update Enrollment-Punkte
                $enrollment = $user->enrolledLehrgaenge()
                    ->where('lehrgaenge.id', $lehrgang->id)
                    ->first();
                
                if ($enrollment) {
                    $currentPoints = $enrollment->pivot->punkte ?? 0;
                    $newPoints = $currentPoints + 10;
                    $enrollment->pivot->update(['punkte' => $newPoints]);
                    
                    // Award Points im GamificationService
                    $gamificationResult = $gamification->awardPoints($user, 10, "Lehrgang: {$lehrgang->lehrgang}", 'lehrgang', $lehrgang->id);
                    
                    // Check if Lehrgang completed
                    $totalSolvedInLehrgang = UserLehrgangProgress::where('user_id', $user->id)
                        ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                        ->where('solved', true)
                        ->count();
                    
                    $totalQuestionsInLehrgang = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                    
                    if ($totalSolvedInLehrgang === $totalQuestionsInLehrgang && $totalQuestionsInLehrgang > 0) {
                        // Lehrgang komplett gelöst
                        $enrollment->pivot->update(['completed' => true, 'completed_at' => now()]);
                    }
                }
                
                // Entferne Frage aus der Practice Session (sie ist gelöst!)
                $sectionNr = $request->input('section_nr');
                
                if ($sectionNr) {
                    // Section-spezifische Session
                    $sectionPracticeIds = session("lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids", []);
                    if (!empty($sectionPracticeIds)) {
                        $sectionPracticeIds = array_diff($sectionPracticeIds, [$question->id]);
                        session(["lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids" => array_values($sectionPracticeIds)]);
                    }
                } else {
                    // Allgemeine Session
                    $practiceIds = session("lehrgaenge_{$lehrgang->id}_practice_ids", []);
                    if (!empty($practiceIds)) {
                        $practiceIds = array_diff($practiceIds, [$question->id]);
                        session(["lehrgaenge_{$lehrgang->id}_practice_ids" => array_values($practiceIds)]);
                    }
                }
            } else {
                // Auch beim ersten richtigen Beantworten Punkte vergeben (aber keine Lösung)
                $gamificationResult = $gamification->awardQuestionPoints($user, true, $question->id);
                
                // Frage bleibt in Session aber wird nach hinten verschoben (nicht direkt wiederholt)
                $sectionNr = $request->input('section_nr');
                
                if ($sectionNr) {
                    $sectionPracticeIds = session("lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids", []);
                    if (!empty($sectionPracticeIds)) {
                        $currentIndex = array_search($question->id, $sectionPracticeIds);
                        if ($currentIndex !== false) {
                            unset($sectionPracticeIds[$currentIndex]);
                            $sectionPracticeIds[] = $question->id;
                            session(["lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids" => array_values($sectionPracticeIds)]);
                        }
                    }
                } else {
                    $practiceIds = session("lehrgaenge_{$lehrgang->id}_practice_ids", []);
                    if (!empty($practiceIds)) {
                        $currentIndex = array_search($question->id, $practiceIds);
                        if ($currentIndex !== false) {
                            unset($practiceIds[$currentIndex]);
                            $practiceIds[] = $question->id;
                            session(["lehrgaenge_{$lehrgang->id}_practice_ids" => array_values($practiceIds)]);
                        }
                    }
                }
            }
        } else {
            $progress->consecutive_correct = 0;
            $progress->failed = true;
            
            // Beim Falsch-Beantworten Gamification checken
            $gamificationResult = $gamification->awardQuestionPoints($user, false, $question->id);
            
            // Frage nach hinten verschieben (später wieder versuchen)
            $sectionNr = $request->input('section_nr');
            
            if ($sectionNr) {
                $sectionPracticeIds = session("lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids", []);
                if (!empty($sectionPracticeIds)) {
                    $currentIndex = array_search($question->id, $sectionPracticeIds);
                    if ($currentIndex !== false) {
                        unset($sectionPracticeIds[$currentIndex]);
                        $sectionPracticeIds[] = $question->id;
                        session(["lehrgaenge_{$lehrgang->id}_section_{$sectionNr}_practice_ids" => array_values($sectionPracticeIds)]);
                    }
                }
            } else {
                $practiceIds = session("lehrgaenge_{$lehrgang->id}_practice_ids", []);
                if (!empty($practiceIds)) {
                    $currentIndex = array_search($question->id, $practiceIds);
                    if ($currentIndex !== false) {
                        unset($practiceIds[$currentIndex]);
                        $practiceIds[] = $question->id;
                        session(["lehrgaenge_{$lehrgang->id}_practice_ids" => array_values($practiceIds)]);
                    }
                }
            }
        }
        
        $progress->save();
        
        // Speichere answer_result in Session für Feedback-Anzeige (wie in PracticeController)
        session([
            'answer_result' => [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer->toArray(),
                'question_progress' => $progress->consecutive_correct,
                'answer_mapping' => $mapping // Mapping auch speichern für die Anzeige
            ]
        ]);
        
        // Gamification Result auch speichern
        if ($gamificationResult ?? false) {
            session(['gamification_result' => $gamificationResult]);
        }
        
        // Redirect (Post/Redirect/Get Pattern um Double-Submit zu vermeiden)
        $sectionNr = $request->input('section_nr');
        
        if ($sectionNr) {
            return redirect()->route('lehrgaenge.practice-section', ['slug' => $slug, 'sectionNr' => $sectionNr]);
        }
        
        return redirect()->route('lehrgaenge.practice', $slug);
    }

    /**
     * Unenroll aus Lehrgang
     */
    public function unenroll($slug)
    {
        $user = auth()->user();
        $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
        
        $user->enrolledLehrgaenge()->detach($lehrgang->id);
        
        // WICHTIG: Fortschritte NICHT löschen - sie laufen in die Statistiken!
        // Benutzer kann später beitreten und wird seinen alten Progress sehen
        
        // Lösche nur die Practice Session für diesen Lehrgang
        session()->forget("lehrgaenge_{$lehrgang->id}_practice_ids");
        session()->forget(['answer_result', 'gamification_result']);
        
        return redirect()->route('lehrgaenge.show', $slug)
            ->with('success', 'Du hast dich aus diesem Lehrgang abgemeldet.');
    }

    /**
     * Melde einen Fehler in einer Lehrgang-Frage
     */
    public function reportIssue(Request $request, $questionId)
    {
        // Request muss JSON sein
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'JSON Request erforderlich'], 400);
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Du musst angemeldet sein'], 401);
        }
        
        // Nur message aus dem JSON body
        $message = $request->input('message');
        
        // Validiere
        if ($message && strlen($message) > 500) {
            return response()->json(['error' => 'Nachricht zu lang (max 500 Zeichen)'], 422);
        }
        
        try {
            $question = LehrgangQuestion::findOrFail($questionId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Frage nicht gefunden'], 404);
        }
        
        // Prüfe ob bereits gemeldet - wenn ja, update
        $issue = LehrgangQuestionIssue::where('lehrgang_question_id', $question->id)->first();
        
        if ($issue) {
            // Bereits gemeldet - erhöhe Counter und update Message
            $issue->report_count++;
            $issue->latest_message = $message ?? null;
            $issue->reported_by_user_id = $user->id;
            
            // Wenn Status nicht "open" ist, setze ihn zurück auf "open"
            if ($issue->status !== 'open') {
                $issue->status = 'open';
            }
            
            $issue->save();
            $isNew = false;
        } else {
            // Erste Meldung - erstelle neuen Eintrag
            $issue = LehrgangQuestionIssue::create([
                'lehrgang_question_id' => $question->id,
                'report_count' => 1,
                'latest_message' => $message ?? null,
                'reported_by_user_id' => $user->id,
                'status' => 'open',
            ]);
            $isNew = true;
        }
        
        // Erstelle einen Report-Eintrag für diese spezifische Meldung
        \App\Models\LehrgangQuestionIssueReport::create([
            'lehrgang_question_issue_id' => $issue->id,
            'user_id' => $user->id,
            'message' => $message ?? null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $isNew ? 'Fehler gemeldet! ✓' : 'Fehler aktualisiert! ✓',
            'report_count' => $issue->report_count,
        ]);
    }
}
