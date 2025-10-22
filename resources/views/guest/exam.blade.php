@extends('layouts.app')
@section('title', 'THW Prüfungssimulation anonym - 40 Fragen ohne Anmeldung')
@section('description', 'THW Prüfungssimulation anonym: Teste dich mit 40 zufälligen Fragen in 30 Minuten ohne Anmeldung. Sofortige Auswertung und Ergebnisanzeige!')

@push('styles')
<style>
    /* CACHE BUST v8.8 - GUEST EXAM NO MOBILE FOOTER - 2025-10-21-19:15 */
    
    /* Sticky Footer Layout für Exam - NUR DESKTOP */
    @media (min-width: 641px) {
        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
        }
        
        main {
            flex: 1 0 auto !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        
        footer {
            flex-shrink: 0 !important;
        }
    }
    
    /* Mobile: Footer verstecken */
    @media (max-width: 640px) {
        footer {
            display: none !important;
        }
        
        /* Extra Padding für Mobile Browser-UI */
        main {
            padding-bottom: max(180px, calc(120px + env(safe-area-inset-bottom))) !important;
        }
    }
    
    /* Exam Container - kompakt ohne unnötige Höhe */
    #guestExamContainer {
        margin: 0 auto !important;
        padding: 0.75rem !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
        width: 100% !important;
        background: white !important;
    }
    
    /* Desktop: Container mit Schatten und Rundungen - GLEICHE BREITE WIE PRACTICE */
    @media (min-width: 641px) {
        #guestExamContainer {
            margin: 1rem auto !important;
            padding: 2.5rem !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            max-width: 950px !important;
            width: 95% !important;
            transition: all 0.3s ease !important;
        }
        
        #guestExamContainer:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08) !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Exam Container - wie Practice -->
<div class="max-w-xl mx-auto mt-0 sm:mt-4 p-3 sm:p-4 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300" 
     id="guestExamContainer">
    @if(!isset($submitted))
        <!-- Header mit Titel und Timer -->
        <div class="mb-3">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-bold text-blue-900">🎓 THW Prüfung (Anonym)</h2>
                <div id="exam-timer" class="text-lg font-bold text-blue-900 bg-gradient-to-r from-yellow-200 to-yellow-300 px-3 py-1 rounded-lg shadow-md" 
                     style="box-shadow: 0 0 10px rgba(251, 191, 36, 0.4);">
                    30:00
                </div>
            </div>
            
            <!-- Fortschrittsbalken -->
            <div class="text-xs text-gray-600">
                <div class="flex justify-between mb-1">
                    <span>Fortschritt</span>
                    <span id="progress-text">0/40 beantwortet</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="h-2 rounded-full transition-all duration-300 bg-yellow-400" 
                         style="width: 0%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4);"></div>
                </div>
            </div>
        </div>
        
        <!-- Fragenbereich -->
        <form id="exam-form" method="POST" action="{{ route('guest.exam.submit') }}">
            @csrf
            
            @foreach($fragen as $index => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                
                @php
                    // Shuffle answers
                    $answersOriginal = [
                        ['letter' => 'A', 'text' => $frage->antwort_a],
                        ['letter' => 'B', 'text' => $frage->antwort_b],
                        ['letter' => 'C', 'text' => $frage->antwort_c],
                    ];
                    
                    $answers = $answersOriginal;
                    shuffle($answers);
                    
                    // Erstelle Mapping: Position -> Buchstabe
                    $mappingArray = [];
                    foreach ($answers as $ansIndex => $answer) {
                        $mappingArray[$ansIndex] = $answer['letter'];
                    }
                    
                    $mappingJson = json_encode($mappingArray);
                @endphp
                
                <input type="hidden" name="answer_mappings[{{ $index }}]" value="{{ $mappingJson }}">
                
                <div class="question-slide" data-question="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                    <!-- Fragen-Info -->
                    <div class="mb-3">
                        <div class="text-xs text-gray-500 mb-2">
                            Frage {{ $index + 1 }} von 40
                            <span class="mx-1">•</span>
                            LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-3">{{ $frage->frage }}</h3>
                        
                        <!-- Markieren Button -->
                        <button type="button" 
                                onclick="toggleMark({{ $index }})" 
                                id="mark-btn-{{ $index }}"
                                class="text-xs px-3 py-1 rounded-lg border-2 transition-all duration-200 hover:scale-105"
                                style="border-color: #d1d5db; background-color: white; color: #6b7280;">
                            🔖 Markieren
                        </button>
                    </div>
                    
                    <!-- Antwortoptionen -->
                    <div class="space-y-2 mb-4">
                        @foreach($answers as $ansIndex => $answer)
                            <label class="flex items-start p-3 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                                <input type="checkbox" 
                                       name="answer[{{ $index }}][]" 
                                       value="{{ $ansIndex }}"
                                       onchange="updateAnswerStatus({{ $index }})"
                                       class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <span class="ml-3 text-sm text-gray-900">
                                    {{ $answer['text'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
            
            <!-- Navigation Buttons -->
            <div class="flex justify-between items-center gap-3 mb-3 pt-3 border-t">
                <button type="button" 
                        onclick="previousQuestion()" 
                        id="prev-btn"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    ⬅️ Zurück
                </button>
                
                <span class="text-xs font-medium text-gray-600" id="current-question">1/40</span>
                
                <button type="button" 
                        onclick="nextQuestion()" 
                        id="next-btn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                    Weiter ➡️
                </button>
            </div>
            
            <!-- Fragenübersicht Toggle -->
            <div class="border-t pt-3">
                <button type="button" 
                        onclick="toggleOverview()" 
                        class="w-full flex justify-between items-center text-left text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200 p-2 hover:bg-gray-50 rounded">
                    <span>📊 Fragenübersicht</span>
                    <svg id="overview-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="overview-container" class="mt-3 hidden">
                    <div class="flex gap-3 text-xs mb-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-green-500 mr-1"></div>
                            <span>Beantwortet</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gray-300 mr-1"></div>
                            <span>Offen</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-yellow-400 mr-1"></div>
                            <span>Markiert</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-2" style="display: grid !important; grid-template-columns: repeat(4, 1fr) !important;">
                        @for($i = 0; $i < 40; $i++)
                            <button type="button" 
                                    onclick="goToQuestion({{ $i }})"
                                    id="bubble-{{ $i }}"
                                    class="h-10 flex items-center justify-center rounded-lg text-sm font-semibold cursor-pointer transition-all duration-200 hover:scale-105"
                                    style="background-color: #e5e7eb; color: #374151; border: 2px solid #d1d5db;">
                                {{ $i + 1 }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </form>
        
        <script>
            // State Management
            let currentQuestion = 0;
            const totalQuestions = {{ count($fragen) }};
            let answers = new Array(totalQuestions).fill(false); // Ob Frage beantwortet
            let marked = new Array(totalQuestions).fill(false); // Ob Frage markiert
            let timeLeft = 30 * 60;
            
            // Timer
            const timerEl = document.getElementById('exam-timer');
            function updateTimer() {
                const min = Math.floor(timeLeft / 60).toString().padStart(2, '0');
                const sec = (timeLeft % 60).toString().padStart(2, '0');
                timerEl.textContent = `${min}:${sec}`;
                
                // Warnung bei 5 Minuten
                if (timeLeft <= 300) {
                    timerEl.style.background = 'linear-gradient(to right, #fee2e2, #fecaca)';
                    timerEl.style.animation = 'pulse 1s infinite';
                }
                
                if (timeLeft <= 0) {
                    timerEl.textContent = '00:00';
                    confirmSubmit();
                } else {
                    timeLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }
            updateTimer();
            
            // Navigation
            function showQuestion(index) {
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.style.display = 'none';
                });
                document.querySelector(`[data-question="${index}"]`).style.display = 'block';
                currentQuestion = index;
                
                // Update UI
                document.getElementById('current-question').textContent = `${index + 1}/40`;
                document.getElementById('prev-btn').disabled = index === 0;
                
                // Update Bubbles
                for (let i = 0; i < totalQuestions; i++) {
                    const bubble = document.getElementById(`bubble-${i}`);
                    if (i === index) {
                        bubble.style.borderColor = '#3b82f6';
                        bubble.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';
                    } else {
                        bubble.style.borderColor = marked[i] ? '#f59e0b' : (answers[i] ? '#059669' : '#d1d5db');
                        bubble.style.boxShadow = 'none';
                    }
                }
                
                // Scroll to top
                window.scrollTo(0, 0);
            }
            
            function nextQuestion() {
                if (currentQuestion < totalQuestions - 1) {
                    showQuestion(currentQuestion + 1);
                } else {
                    // Letzte Frage -> Zur Übersichtsseite
                    showSubmitOverview();
                }
            }
            
            function previousQuestion() {
                if (currentQuestion > 0) {
                    showQuestion(currentQuestion - 1);
                }
            }
            
            function goToQuestion(index) {
                showQuestion(index);
                // Scroll zurück nach oben
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
            
            // Antwort-Status aktualisieren
            function updateAnswerStatus(index) {
                const checkboxes = document.querySelectorAll(`input[name="answer[${index}][]"]`);
                const isAnswered = Array.from(checkboxes).some(cb => cb.checked);
                answers[index] = isAnswered;
                
                const bubble = document.getElementById(`bubble-${index}`);
                if (isAnswered) {
                    bubble.style.backgroundColor = '#10b981';
                    bubble.style.color = 'white';
                    bubble.style.borderColor = '#059669';
                } else if (!marked[index]) {
                    bubble.style.backgroundColor = '#e5e7eb';
                    bubble.style.color = '#374151';
                    bubble.style.borderColor = '#d1d5db';
                }
                
                updateProgress();
            }
            
            // Frage markieren
            function toggleMark(index) {
                marked[index] = !marked[index];
                const bubble = document.getElementById(`bubble-${index}`);
                const btn = document.getElementById(`mark-btn-${index}`);
                
                if (marked[index]) {
                    bubble.style.backgroundColor = '#fbbf24';
                    bubble.style.color = 'white';
                    bubble.style.borderColor = '#f59e0b';
                    btn.style.backgroundColor = '#fbbf24';
                    btn.style.color = 'white';
                    btn.style.borderColor = '#f59e0b';
                    btn.textContent = '🔖 Markiert';
                } else {
                    if (answers[index]) {
                        bubble.style.backgroundColor = '#10b981';
                        bubble.style.color = 'white';
                        bubble.style.borderColor = '#059669';
                    } else {
                        bubble.style.backgroundColor = '#e5e7eb';
                        bubble.style.color = '#374151';
                        bubble.style.borderColor = '#d1d5db';
                    }
                    btn.style.backgroundColor = 'white';
                    btn.style.color = '#6b7280';
                    btn.style.borderColor = '#d1d5db';
                    btn.textContent = '🔖 Markieren';
                }
            }
            
            // Fortschritt aktualisieren
            function updateProgress() {
                const answeredCount = answers.filter(a => a).length;
                const percent = (answeredCount / totalQuestions) * 100;
                
                document.getElementById('progress-text').textContent = `${answeredCount}/40 beantwortet`;
                document.getElementById('progress-bar').style.width = `${percent}%`;
            }
            
            // Übersicht Toggle
            function toggleOverview() {
                const container = document.getElementById('overview-container');
                const icon = document.getElementById('overview-icon');
                
                if (container.classList.contains('hidden')) {
                    container.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    container.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                }
            }
            
            // Übersichtsseite vor dem Absenden
            function showSubmitOverview() {
                const unanswered = answers.reduce((acc, answered, index) => {
                    if (!answered) acc.push(index + 1);
                    return acc;
                }, []);
                
                const markedCount = marked.filter(m => m).length;
                const answeredCount = answers.filter(a => a).length;
                
                // Verstecke alle Fragen
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.style.display = 'none';
                });
                
                // Erstelle Übersicht in einem neuen Slide
                let message = `<div class="question-slide" id="submit-overview" style="display: block;">`;
                message += `<div class="text-center p-6">`;
                message += `<h2 class="text-xl font-bold mb-4">Prüfung abschließen?</h2>`;
                message += `<div class="mb-6">`;
                message += `<p class="text-base mb-2"><strong>${answeredCount} von 40</strong> Fragen beantwortet</p>`;
                
                if (unanswered.length > 0) {
                    message += `<p class="text-red-600 font-medium mb-2 text-sm">⚠️ ${unanswered.length} Fragen noch offen</p>`;
                    message += `<p class="text-xs text-gray-600">Fragen: ${unanswered.join(', ')}</p>`;
                }
                
                if (markedCount > 0) {
                    message += `<p class="text-yellow-600 font-medium mt-3 text-sm">🔖 ${markedCount} Fragen markiert</p>`;
                }
                
                message += `</div>`;
                message += `<div class="flex flex-col gap-3">`;
                message += `<button type="button" onclick="backToPrüfung()" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300">⬅️ Zurück zur Prüfung</button>`;
                message += `<button type="button" onclick="confirmSubmit()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">✓ Prüfung abgeben</button>`;
                message += `</div>`;
                message += `</div>`;
                message += `</div>`;
                
                // Füge Übersicht zum Form hinzu (nicht ersetzen!)
                const form = document.getElementById('exam-form');
                const existingOverview = document.getElementById('submit-overview');
                if (existingOverview) {
                    existingOverview.remove();
                }
                form.insertAdjacentHTML('beforeend', message);
                
                // Verstecke Navigation
                document.querySelectorAll('.border-t.pt-3').forEach(el => el.style.display = 'none');
            }
            
            // Zurück zur Prüfung
            function backToPrüfung() {
                const overview = document.getElementById('submit-overview');
                if (overview) {
                    overview.remove();
                }
                // Zeige Navigation wieder
                document.querySelectorAll('.border-t.pt-3').forEach(el => el.style.display = 'block');
                // Zeige letzte Frage
                showQuestion(currentQuestion);
            }
            
            // Prüfung bestätigen und abgeben
            function confirmSubmit() {
                document.getElementById('exam-form').submit();
            }
            
            // Initial state
            updateProgress();
        </script>
        
    @else
        <!-- Ergebnis-Ansicht -->
        <div class="p-4">
            <div class="mb-4 p-4 text-center rounded-lg shadow-md {{ $passed ? 'text-green-800' : 'text-red-800' }}" 
                 style="{{ $passed ? 'background-color: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3);' : 'background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3);' }}">
                <div class="text-3xl mb-2">{{ $passed ? '🎉' : '😔' }}</div>
                <div class="text-lg font-bold">
                    {{ round(($correctCount/$total)*100) }}% {{ $passed ? 'Bestanden' : 'Nicht Bestanden' }}
                </div>
            </div>
            
            @php
                $percent = $total > 0 ? round($correctCount / $total * 100) : 0;
            @endphp
            
            <div class="w-full bg-gray-200 rounded-full h-4 mb-2 relative">
                <div class="bg-yellow-400 h-4 rounded-full shadow-lg" style="width: {{ $percent }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4);"></div>
                <div class="absolute flex items-center" style="left: 80%; top: 0; height: 100%;">
                    <div class="w-1 h-4 bg-red-500 rounded-full shadow-lg" style="box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);"></div>
                    <div class="ml-1 text-xs font-bold text-red-600 bg-white px-1 rounded">80%</div>
                </div>
            </div>
            
            <div class="text-center mb-2">
                <span class="text-sm font-bold text-blue-900 bg-white px-2 py-1 rounded shadow-sm">{{ $percent }}% erreicht</span>
            </div>
            
            <div class="text-sm text-gray-600 text-center mb-6">Anonyme Prüfung - Ergebnis wird nicht gespeichert</div>
            
            <!-- Prominenter Registrierungs-Button -->
            <div style="background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px; margin-bottom: 24px; text-align: center;">
                <h3 style="font-size: 18px; font-weight: bold; color: #1e40af; margin-bottom: 12px;">Erstelle einen Account für mehr</h3>
                <p style="color: #1e40af; margin-bottom: 16px;">Mit einem kostenlosen Account kannst du Prüfungsergebnisse speichern und deinen Fortschritt verfolgen.</p>
                <a href="{{ route('register') }}" 
                   style="display: inline-flex; align-items: center; padding: 16px 32px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 18px; font-weight: bold; border-radius: 12px; text-decoration: none; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4); transition: all 0.3s ease; transform: scale(1);"
                   onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'"
                   onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'">
                    <svg style="width: 24px; height: 24px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    🚀 Kostenlos registrieren
                </a>
            </div>
            
            <!-- Fragen mit Lösungen (scrollbar) -->
            <div class="border-t pt-4">
                <h3 class="text-base font-bold mb-3">Lösungen</h3>
                @foreach($fragen as $nr => $frage)
                    @php
                        $mappingArray = $results[$nr]['mapping'] ?? [];
                        $answersOriginal = [
                            ['letter' => 'A', 'text' => $frage->antwort_a],
                            ['letter' => 'B', 'text' => $frage->antwort_b],
                            ['letter' => 'C', 'text' => $frage->antwort_c],
                        ];
                        
                        // Sortiere entsprechend Mapping
                        if ($mappingArray) {
                            $answers = [];
                            foreach ($mappingArray as $position => $letter) {
                                foreach ($answersOriginal as $ans) {
                                    if ($ans['letter'] === $letter) {
                                        $answers[$position] = $ans;
                                        break;
                                    }
                                }
                            }
                            ksort($answers);
                        } else {
                            $answers = $answersOriginal;
                        }
                        
                        $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
                    @endphp
                    
                    <div class="mb-4 p-3 border rounded-lg {{ $results[$nr]['isCorrect'] ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                        <div class="text-xs text-gray-500 mb-1">Frage {{ $nr + 1 }} • LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</div>
                        <div class="text-sm font-bold mb-2">{{ $frage->frage }}</div>
                        
                        <div class="space-y-1">
                            @foreach($answers as $index => $answer)
                                @php
                                    $originalLetter = $answer['letter'];
                                    $isCorrectAnswer = $solution->contains($originalLetter);
                                    $isUserAnswer = $results[$nr]['userAnswer']->contains($originalLetter);
                                    
                                    // Farbe bestimmen
                                    if ($isCorrectAnswer && $isUserAnswer) {
                                        $bgColor = 'bg-green-100';
                                        $borderColor = 'border-green-400';
                                        $textColor = 'text-green-900';
                                        $icon = '✓';
                                    } elseif ($isCorrectAnswer && !$isUserAnswer) {
                                        $bgColor = 'bg-green-50';
                                        $borderColor = 'border-green-300';
                                        $textColor = 'text-green-800';
                                        $icon = '✓';
                                    } elseif (!$isCorrectAnswer && $isUserAnswer) {
                                        $bgColor = 'bg-red-100';
                                        $borderColor = 'border-red-400';
                                        $textColor = 'text-red-900';
                                        $icon = '✗';
                                    } else {
                                        $bgColor = 'bg-gray-50';
                                        $borderColor = 'border-gray-200';
                                        $textColor = 'text-gray-700';
                                        $icon = '';
                                    }
                                @endphp
                                
                                <div class="flex items-start p-2 rounded-lg border-2 {{ $bgColor }} {{ $borderColor }}">
                                    <div class="flex items-center min-w-0 flex-1">
                                        @if($isUserAnswer)
                                            <span class="w-4 h-4 mr-2 flex-shrink-0 text-sm">{{ $icon }}</span>
                                        @else
                                            <span class="w-4 h-4 mr-2 flex-shrink-0"></span>
                                        @endif
                                        <span class="text-xs {{ $textColor }}">{{ $answer['text'] }}</span>
                                    </div>
                                    @if($isCorrectAnswer && !$isUserAnswer)
                                        <span class="ml-2 text-xs font-semibold text-green-700 bg-green-200 px-2 py-1 rounded flex-shrink-0">
                                            Richtig
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Zusammenfassung -->
                        <div class="mt-2 pt-2 border-t {{ $results[$nr]['isCorrect'] ? 'border-green-200' : 'border-red-200' }}">
                            <div class="flex items-center justify-between text-xs">
                                <span class="{{ $results[$nr]['isCorrect'] ? 'text-green-700' : 'text-red-700' }} font-medium">
                                    @if($results[$nr]['isCorrect'])
                                        ✓ Richtig beantwortet
                                    @else
                                        ✗ Falsch beantwortet
                                    @endif
                                </span>
                                <span class="text-gray-600">
                                    Lösung: {{ $solution->join(', ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('guest.exam.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
                   style="background: linear-gradient(to right, #00337F, #002A66); color: white;">
                    🎓 Neue Prüfung
                </a>
                <a href="{{ route('guest.practice.all') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
                   style="background: linear-gradient(to right, #facc15, #f59e0b); color: white;">
                    📚 Weiter üben
                </a>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
                   style="background: linear-gradient(to right, #4b5563, #374151); color: white;">
                    🏠 Startseite
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
