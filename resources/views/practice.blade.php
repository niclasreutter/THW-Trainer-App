@extends('layouts.app')
@section('title', 'THW Theorie üben - Interaktive Fragen mit Lernfortschritt')
@section('description', 'Übe THW Theoriefragen mit deinem persönlichen Lernfortschritt. Markiere schwierige Fragen, filtere nach Lernabschnitten und verfolge deinen Erfolg. Kostenlos und effektiv!')

@section('content')
@php
    // Hole Antwort-Details aus Session (falls vorhanden)
    $answerResult = session('answer_result');
    $hasAnswerResult = $answerResult && isset($answerResult['question_id']) && $answerResult['question_id'] == $question->id;
    
    // Hole Gamification Result aus Session
    $gamificationResult = session('gamification_result');
    
    if ($hasAnswerResult) {
        $isCorrect = $answerResult['is_correct'];
        $userAnswer = collect($answerResult['user_answer']);
        $questionProgress = (object)['consecutive_correct' => $answerResult['question_progress']];
        
        // Lösche BEIDE Sessions nach dem Auslesen (sie gehören zusammen)
        session()->forget(['answer_result', 'gamification_result']);
    } else {
        $isCorrect = null;
        $userAnswer = null;
        $questionProgress = null;
    }
@endphp
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
</style>
<div class="max-w-xl mx-auto mt-4 p-4 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">

    @if($question)
        <div class="mb-3 hidden sm:block">
            <h2 class="text-xl font-bold mb-2">
                @if(isset($mode))
                    @switch($mode)
                        @case('unsolved')
                            🎯 Ungelöste Fragen
                            @break
                        @case('section')
                            📖 Lernabschnitt {{ session('practice_parameter') }}
                            @break
                        @case('search')
                            🔍 Suche: "{{ session('practice_parameter') }}"
                            @break
                        @case('bookmarked')
                            🔖 Gespeicherte Fragen
                            @break
                        @default
                            📚 Alle Fragen
                    @endswitch
                @else
                    Theorie üben
                @endif
            </h2>
        </div>
        
        <div class="mb-3 text-sm text-gray-600">
            Fortschritt: {{ $progress }}/{{ $total }} gemeistert
            <div class="w-full bg-gray-200 rounded-full h-3 mt-1 mb-1">
                <div class="bg-yellow-400 h-3 rounded-full transition-all duration-300 shadow-lg" 
                     style="width: {{ $progressPercent ?? 0 }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
            </div>
            <span class="text-xs text-gray-500">{{ $progressPercent ?? 0 }}% Gesamt-Fortschritt (inkl. 1x richtig)</span>
        </div>
        
        <!-- Bookmark Button -->
        <div class="mb-3 flex justify-end items-center">
            @php
                $user = Auth::user();
                $bookmarked = is_array($user->bookmarked_questions ?? null) 
                    ? $user->bookmarked_questions 
                    : json_decode($user->bookmarked_questions ?? '[]', true);
                $isBookmarked = in_array($question->id, $bookmarked);
            @endphp
            
            <button type="button" 
                    class="flex items-center gap-1 px-2 py-1 hover:bg-gray-100 hover:shadow-md hover:scale-105 rounded-lg transition-all duration-300 cursor-pointer text-sm"
                    title="{{ $isBookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen' }}"
                    id="bookmarkBtn"
                    onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                <svg class="w-4 h-4 {{ $isBookmarked ? 'text-yellow-500 fill-current' : 'text-gray-400' }}" 
                     viewBox="0 0 20 20" stroke="currentColor" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" id="bookmarkIcon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"></path>
                </svg>
                <span class="text-xs text-gray-600" id="bookmarkText">
                    {{ $isBookmarked ? 'Gespeichert' : 'Speichern' }}
                </span>
            </button>
        </div>
        
        <form method="POST" action="{{ route('practice.submit') }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            
            @php
                // Erstelle ein Array mit den Antworten
                $answersOriginal = [
                    ['letter' => 'A', 'text' => $question->antwort_a],
                    ['letter' => 'B', 'text' => $question->antwort_b],
                    ['letter' => 'C', 'text' => $question->antwort_c],
                ];
                
                // Wenn eine Antwort angezeigt wird (isCorrect gesetzt), nutze das gespeicherte Mapping
                // aus der answer_result Session
                if (isset($isCorrect) && isset($answerResult['answer_mapping'])) {
                    $mappingArray = $answerResult['answer_mapping'];
                    
                    // Sortiere $answers entsprechend dem Mapping
                    $answers = [];
                    foreach ($mappingArray as $position => $letter) {
                        foreach ($answersOriginal as $ans) {
                            if ($ans['letter'] === $letter) {
                                $answers[$position] = $ans;
                                break;
                            }
                        }
                    }
                    ksort($answers); // Sortiere nach Position
                } else {
                    // Neue Frage: shuffle
                    $answers = $answersOriginal;
                    shuffle($answers);
                    
                    // Erstelle Mapping: Position -> Buchstabe
                    $mappingArray = [];
                    foreach ($answers as $index => $answer) {
                        $mappingArray[$index] = $answer['letter'];
                    }
                }
                
                $mappingJson = json_encode($mappingArray);
                $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
            @endphp
            
            <input type="hidden" name="answer_mapping" value="{{ $mappingJson }}">
            
            <div class="mb-4 p-4 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="mb-2 text-xs text-gray-500 flex items-center gap-2">
                    <span>ID: {{ $question->id }}</span>
                    <span class="mx-2">&middot;</span>
                    <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                </div>
                <div class="mb-2 font-bold text-sm">Frage:</div>
                <div class="mb-3 text-sm">{{ $question->frage }}</div>
                <div class="mb-3">
                    <label class="block mb-2 font-semibold text-sm">Antwortmöglichkeiten:</label>
                    <div class="flex flex-col gap-2">
                        @foreach($answers as $index => $answer)
                            @php
                                $originalLetter = $answer['letter'];
                                $isCorrectAnswer = $solution->contains($originalLetter);
                                $isUserAnswer = isset($userAnswer) && $userAnswer->contains($originalLetter);
                                $isChecked = isset($isCorrect) && $isUserAnswer;
                            @endphp
                            <label class="inline-flex items-start p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                @if(isset($isCorrect))
                                    @if($isCorrectAnswer)
                                        <span class="mr-2 text-green-600 text-lg">✅</span>
                                    @elseif($isUserAnswer)
                                        <span class="mr-2 text-red-600 text-lg">❌</span>
                                    @else
                                        <span class="mr-2 text-gray-400 text-lg">⚪</span>
                                    @endif
                                @endif
                                <input type="checkbox" name="answer[]" value="{{ $index }}"
                                    @if($isChecked) checked @endif
                                    @if(isset($isCorrect)) disabled @endif
                                    class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-0.5">
                                <span class="ml-2 text-sm {{ isset($isCorrect) && $isChecked ? ($isCorrectAnswer ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold') : '' }}">
                                    {{ $answer['text'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" style="width: 100%; display: block; text-align: center; background-color: #1e3a8a; color: #fbbf24; padding: 12px 16px; border-radius: 8px; font-weight: bold; font-size: 14px; border: none; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2);" 
                        onmouseover="if(!this.disabled) { this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 25px rgba(251, 191, 36, 0.5), 0 0 50px rgba(251, 191, 36, 0.3)'; }" 
                        onmouseout="if(!this.disabled) { this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2)'; }"
                        disabled>Antwort absenden</button>
                
                <!-- Gamification Anzeige unten (für gemeisterte Fragen aus Session) -->
                @php
                    $topGamificationResult = session('gamification_result');
                    $showTopGamification = $topGamificationResult && isset($topGamificationResult['points_awarded']);
                    
                    if ($showTopGamification) {
                        // Verschiedene Emojis und Texte für Abwechslung
                        $celebrations = [
                            ['emoji' => '🥳', 'text' => 'Grandios!'],
                            ['emoji' => '🎉', 'text' => 'Fantastisch!'],
                            ['emoji' => '⭐', 'text' => 'Super!'],
                            ['emoji' => '💪', 'text' => 'Stark!'],
                            ['emoji' => '🔥', 'text' => 'Mega!'],
                            ['emoji' => '✨', 'text' => 'Klasse!'],
                            ['emoji' => '🎯', 'text' => 'Volltreffer!'],
                            ['emoji' => '🚀', 'text' => 'Genial!'],
                        ];
                        
                        // Wähle basierend auf Fragen-ID eine konsistente Variation
                        $celebrationIndex = $question->id % count($celebrations);
                        $topCelebration = $celebrations[$celebrationIndex];
                        
                        // Grund-Text basierend auf Punkten
                        $topPointsAwarded = $topGamificationResult['points_awarded'] ?? 0;
                        $topReason = $topGamificationResult['reason'] ?? 'Frage beantwortet';
                        
                        if ($topPointsAwarded >= 20) {
                            if (str_contains($topReason, 'Häufig falsche')) {
                                $topReasonText = 'Häufig falsche Frage gelöst';
                            } else {
                                $topReasonText = 'Mit Streak-Bonus';
                            }
                        } else {
                            $topReasonText = $topReason;
                        }
                        
                        // Lösche die Session nach der Anzeige
                        session()->forget('gamification_result');
                    }
                @endphp
                
                @if($showTopGamification)
                    <div class="mt-3 animate-fade-in">
                        <div class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm"
                             style="background-color: #f0fdf4; 
                                    border: 1px solid #bbf7d0; 
                                    box-shadow: 0 0 15px rgba(34, 197, 94, 0.4), 
                                               0 0 30px rgba(34, 197, 94, 0.2),
                                               0 0 45px rgba(34, 197, 94, 0.1);">
                            <span class="text-base">{{ $topCelebration['emoji'] }}</span>
                            <span class="font-bold" style="color: #15803d;">{{ $topCelebration['text'] }}</span>
                            <span style="color: #16a34a;">+{{ $topPointsAwarded }} Punkte</span>
                            <span class="text-xs" style="color: #6b7280;">({{ $topReasonText }})</span>
                        </div>
                        <div class="mt-2 px-3 py-2 rounded-lg text-sm font-semibold text-center" 
                             style="background-color: #dcfce7; 
                                    border: 1px solid #86efac; 
                                    color: #15803d;">
                            ✅ Frage gemeistert! Du hast sie 2x hintereinander richtig beantwortet!
                        </div>
                    </div>
                @endif
                
            @elseif(isset($isCorrect) && $isCorrect)
                <a href="{{ route('practice.index') }}" style="width: 100%; display: block; text-align: center; background-color: #1e3a8a; color: #fbbf24; padding: 12px 16px; border-radius: 8px; font-weight: bold; font-size: 14px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 25px rgba(251, 191, 36, 0.5), 0 0 50px rgba(251, 191, 36, 0.3)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2)';">Nächste Frage</a>
                
                @php
                    $showGamification = $gamificationResult && isset($gamificationResult['points_awarded']);
                    
                    if ($showGamification) {
                        // Verschiedene Emojis und Texte für Abwechslung
                        $celebrations = [
                            ['emoji' => '🥳', 'text' => 'Grandios!'],
                            ['emoji' => '🎉', 'text' => 'Fantastisch!'],
                            ['emoji' => '⭐', 'text' => 'Super!'],
                            ['emoji' => '💪', 'text' => 'Stark!'],
                            ['emoji' => '🔥', 'text' => 'Mega!'],
                            ['emoji' => '✨', 'text' => 'Klasse!'],
                            ['emoji' => '🎯', 'text' => 'Volltreffer!'],
                            ['emoji' => '🚀', 'text' => 'Genial!'],
                        ];
                        
                        // Wähle basierend auf Fragen-ID eine konsistente Variation
                        $celebrationIndex = $question->id % count($celebrations);
                        $celebration = $celebrations[$celebrationIndex];
                        
                        // Grund-Text basierend auf Punkten
                        $pointsAwarded = $gamificationResult['points_awarded'] ?? 0;
                        $reason = $gamificationResult['reason'] ?? 'Frage beantwortet';
                        
                        if ($pointsAwarded >= 20) {
                            if (str_contains($reason, 'Häufig falsche')) {
                                $reasonText = 'Häufig falsche Frage gelöst';
                            } else {
                                $reasonText = 'Mit Streak-Bonus';
                            }
                        } else {
                            $reasonText = $reason;
                        }
                    }
                @endphp
                
                <!-- Gamification Anzeige -->
                @if($showGamification)
                    <div class="mt-3 animate-fade-in">
                        <div class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm"
                             style="background-color: #f0fdf4; 
                                    border: 1px solid #bbf7d0; 
                                    box-shadow: 0 0 15px rgba(34, 197, 94, 0.4), 
                                               0 0 30px rgba(34, 197, 94, 0.2),
                                               0 0 45px rgba(34, 197, 94, 0.1);">
                            <span class="text-base">{{ $celebration['emoji'] }}</span>
                            <span class="font-bold" style="color: #15803d;">{{ $celebration['text'] }}</span>
                            <span style="color: #16a34a;">+{{ $pointsAwarded }} Punkte</span>
                            <span class="text-xs" style="color: #6b7280;">({{ $reasonText }})</span>
                        </div>
                        @if(isset($questionProgress) && $questionProgress->consecutive_correct == 1)
                            <div class="mt-2 px-3 py-2 rounded-lg text-sm font-semibold" 
                                 style="background-color: #dbeafe; 
                                        border: 1px solid #93c5fd; 
                                        color: #1e40af;
                                        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5), 
                                                   0 0 30px rgba(59, 130, 246, 0.3),
                                                   0 0 45px rgba(59, 130, 246, 0.1);">
                                💡 Noch <strong>1x richtig</strong> beantworten, um die Frage zu meistern!
                            </div>
                        @endif
                    </div>
                @endif
                
            @elseif(isset($isCorrect) && !$isCorrect)
                <a href="{{ route('practice.index', ['skip_id' => $question->id]) }}" style="width: 100%; display: block; text-align: center; background-color: #1e3a8a; color: #fbbf24; padding: 12px 16px; border-radius: 8px; font-weight: bold; font-size: 14px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 25px rgba(251, 191, 36, 0.5), 0 0 50px rgba(251, 191, 36, 0.3)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2)';">Weiter zur nächsten Frage</a>
                
                <div class="mt-3 p-3 rounded-lg font-bold shadow-lg text-sm" style="background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3), 0 0 30px rgba(239, 68, 68, 0.1);">
                    <div class="flex items-center">
                        <div class="text-xl mr-2">❌</div>
                        <span>Leider falsch. Die richtigen Antworten sind markiert.</span>
                    </div>
                </div>
            @endif
        </form>
        
        <script>
            // Bookmark AJAX Function
            function toggleBookmark(questionId, currentlyBookmarked) {
                const btn = document.getElementById('bookmarkBtn');
                const text = document.getElementById('bookmarkText');
                const icon = document.getElementById('bookmarkIcon');
                
                // Loading State
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                text.textContent = 'Speichere...';
                icon.classList.add('animate-spin');
                
                const formData = new FormData();
                formData.append('question_id', questionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                fetch('{{ route("bookmarks.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.is_bookmarked) {
                            icon.classList.add('text-yellow-500', 'fill-current');
                            icon.classList.remove('text-gray-400');
                            icon.setAttribute('fill', 'currentColor');
                            text.textContent = 'Gespeichert';
                            btn.setAttribute('title', 'Aus Lesezeichen entfernen');
                            btn.setAttribute('onclick', `toggleBookmark(${questionId}, true)`);
                        } else {
                            icon.classList.remove('text-yellow-500', 'fill-current');
                            icon.classList.add('text-gray-400');
                            icon.setAttribute('fill', 'none');
                            text.textContent = 'Speichern';
                            btn.setAttribute('title', 'Zu Lesezeichen hinzufügen');
                            btn.setAttribute('onclick', `toggleBookmark(${questionId}, false)`);
                        }
                        
                        // Kurzes Feedback mit Animation
                        const originalText = text.textContent;
                        text.textContent = data.is_bookmarked ? 'Gespeichert!' : 'Entfernt!';
                        btn.classList.add('animate-pulse');
                        setTimeout(() => {
                            text.textContent = originalText;
                            btn.classList.remove('animate-pulse');
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Bookmark error:', error);
                    alert('Fehler beim Speichern der Markierung');
                })
                .finally(() => {
                    // Reset Loading State
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    icon.classList.remove('animate-spin');
                });
            }
            
            @if(!isset($isCorrect))
            // Original checkbox logic - nur wenn Frage noch nicht beantwortet
            const checkboxes = document.querySelectorAll('input[type=checkbox][name="answer[]"]');
            const submitBtn = document.getElementById('submitBtn');
            function updateBtn() {
                let checked = 0;
                checkboxes.forEach(cb => { if(cb.checked) checked++; });
                submitBtn.disabled = checked === 0;
            }
            checkboxes.forEach(cb => cb.addEventListener('change', updateBtn));
            updateBtn();
            @endif
        </script>
    @else
        <div class="text-center text-lg mb-4">Du hast alle Fragen in diesem Modus bearbeitet! 🎉</div>
        <div class="text-center">
            <a href="{{ route('practice.menu') }}" class="inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300 mr-4">Zurück zum Übungsmenü</a>
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-600 text-white px-6 py-2 rounded font-bold hover:bg-gray-700 hover:shadow-lg hover:scale-105 transition-all duration-300">Dashboard</a>
        </div>
    @endif
</div>
@endsection

