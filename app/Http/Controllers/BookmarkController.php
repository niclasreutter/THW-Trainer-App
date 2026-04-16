<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;

class BookmarkController extends Controller
{
    /**
     * Zeige alle gespeicherten Fragen
     */
    public function index()
    {
        $user = Auth::user();
        $bookmarked = is_array($user->bookmarked_questions ?? null) 
            ? $user->bookmarked_questions 
            : json_decode($user->bookmarked_questions ?? '[]', true);
        
        $questions = Question::whereIn('id', $bookmarked)->orderBy('lernabschnitt')->get();
        $grouped = $questions->groupBy('lernabschnitt');

        return view('bookmarks.index', compact('questions', 'grouped'));
    }
    
    /**
     * Frage zu Lesezeichen hinzufügen/entfernen
     */
    public function toggle(Request $request)
    {
        $user = Auth::user();
        $questionId = (int) $request->input('question_id');
        
        $bookmarked = is_array($user->bookmarked_questions ?? null) 
            ? $user->bookmarked_questions 
            : json_decode($user->bookmarked_questions ?? '[]', true);
        
        if (in_array($questionId, $bookmarked)) {
            // Entfernen
            $bookmarked = array_values(array_filter($bookmarked, fn($id) => $id !== $questionId));
            $message = 'Frage aus Lesezeichen entfernt';
            $isBookmarked = false;
        } else {
            // Hinzufügen
            $bookmarked[] = $questionId;
            $message = 'Frage zu Lesezeichen hinzugefügt';
            $isBookmarked = true;
        }
        
        $user->bookmarked_questions = $bookmarked;
        $user->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_bookmarked' => $isBookmarked,
                'count' => count($bookmarked)
            ]);
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Mehrere Fragen auf einmal zu Lesezeichen hinzufügen
     */
    public function addMany(Request $request)
    {
        $user = Auth::user();
        $questionIds = $request->input('question_ids', []);

        if (!is_array($questionIds)) {
            $questionIds = [];
        }

        $questionIds = array_values(array_unique(array_map('intval', $questionIds)));

        $bookmarked = is_array($user->bookmarked_questions ?? null)
            ? $user->bookmarked_questions
            : json_decode($user->bookmarked_questions ?? '[]', true);

        $before = count($bookmarked);
        $merged = array_values(array_unique(array_merge($bookmarked, $questionIds)));
        $added = count($merged) - $before;

        $user->bookmarked_questions = $merged;
        $user->save();

        $message = $added > 0
            ? ($added . ' Frage' . ($added === 1 ? '' : 'n') . ' zu Lesezeichen hinzugefügt')
            : 'Alle Fragen bereits gespeichert';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'added' => $added,
                'count' => count($merged),
                'bookmarked_ids' => $merged,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Gespeicherte Fragen üben
     */
    public function practice()
    {
        $user = Auth::user();
        $bookmarked = is_array($user->bookmarked_questions ?? null) 
            ? $user->bookmarked_questions 
            : json_decode($user->bookmarked_questions ?? '[]', true);
        
        if (empty($bookmarked)) {
            return redirect()->route('bookmarks.index')->with('error', 'Keine gespeicherten Fragen vorhanden.');
        }
        
        // Session für Bookmark-Modus setzen
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        session([
            'practice_mode' => 'bookmarked',
            'practice_parameter' => null,
            'practice_ids' => $bookmarked
        ]);
        
        $question = Question::find($bookmarked[0]);
        if (!$question) {
            return redirect()->route('bookmarks.index')->with('error', 'Frage nicht gefunden.');
        }
        
        // Fortschritt: Bookmark-spezifisch
        $total = count($bookmarked);
        $progress = 0; // Startet bei 0 für Bookmark-Session
        $mode = 'bookmarked';
        
        return view('practice', compact('question', 'progress', 'total', 'mode'));
    }
}
