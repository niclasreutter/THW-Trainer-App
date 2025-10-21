@extends('layouts.app')
@section('title', 'THW Prüfungssimulation - 40 Fragen in 30 Minuten')
@section('description', 'THW Prüfungssimulation: Teste dein Wissen mit 40 zufälligen Fragen in 30 Minuten. Realistische Prüfungsbedingungen und sofortige Auswertung. Übe jetzt kostenlos!')

@push('styles')
<style>
    /* CACHE BUST - EXAM VIEW FIX - 2025-10-21-16:00 */
    
    /* Footer und Navigation ausblenden - HÖCHSTE PRIORITÄT */
    footer,
    footer *,
    .footer,
    [role="contentinfo"] {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        position: absolute !important;
        left: -9999px !important;
    }
    
    nav,
    nav *,
    header nav,
    [role="navigation"] {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        position: absolute !important;
        left: -9999px !important;
    }
    
    /* Body auf Vollbild */
    body {
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 100vh !important;
    }
    
    /* Wrapper div entfernen falls vorhanden */
    body > div.min-h-screen {
        min-height: 100vh !important;
        height: 100vh !important;
    }
    
    /* Main Container auf volle Höhe mit Scroll */
    main {
        height: 100vh !important;
        max-height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        position: relative !important;
    }
    
    /* Exam Container - Mobile First */
    #examContainer {
        margin: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding: 0.75rem !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
        width: 100% !important;
        min-height: 100vh !important;
        background: white !important;
    }
    
    /* Desktop: Container mit Schatten und Rundungen */
    @media (min-width: 641px) {
        main {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        
        #examContainer {
            margin: 1rem auto !important;
            padding: 1rem !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            max-width: 42rem !important;
            min-height: auto !important;
        }
    }
    
    /* Fragenübersicht Grid */
    .question-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
        gap: 0.5rem;
        max-width: 100%;
    }
    
    .question-bubble {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    
    .question-bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    /* Status-Farben */
    .question-bubble.answered {
        background-color: #10b981;
        color: white;
        border-color: #059669;
    }
    
    .question-bubble.open {
        background-color: #e5e7eb;
        color: #374151;
        border-color: #d1d5db;
    }
    
    .question-bubble.marked {
        background-color: #fbbf24;
        color: white;
        border-color: #f59e0b;
    }
    
    .question-bubble.current {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }
    
    /* Timer Styling */
    .timer {
        font-size: 1.5rem;
        font-weight: bold;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        background: linear-gradient(to right, #fef3c7, #fde68a);
        box-shadow: 0 0 10px rgba(251, 191, 36, 0.4);
    }
    
    .timer.warning {
        background: linear-gradient(to right, #fee2e2, #fecaca);
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
</style>
@endpush

@section('content')
<!-- Exam Container - wie Practice -->
<div class="max-w-xl mx-auto mt-0 sm:mt-4 p-3 sm:p-4 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300" 
     id="examContainer">
    @if(!isset($submitted))
        <!-- Header mit Titel und Timer -->
        <div class="mb-3">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-bold text-blue-900">🎓 THW Prüfung</h2>
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
        <form id="exam-form" method="POST" action="{{ route('exam.submit') }}">
            @csrf
            
            @foreach($fragen as $index => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                
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
                        @foreach(['A','B','C'] as $option)
                            <label class="flex items-start p-3 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                                <input type="checkbox" 
                                       name="answer[{{ $index }}][]" 
                                       value="{{ $option }}"
                                       onchange="updateAnswerStatus({{ $index }})"
                                       class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <span class="ml-3 text-sm text-gray-900">
                                    <span class="font-bold">{{ $option }}:</span> {{ $frage['antwort_'.strtolower($option)] }}
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
                    
                    <div class="question-grid grid grid-cols-10 gap-2">
                        @for($i = 0; $i < 40; $i++)
                            <button type="button" 
                                    onclick="goToQuestion({{ $i }})"
                                    id="bubble-{{ $i }}"
                                    class="question-bubble w-8 h-8 flex items-center justify-center rounded-full text-xs font-semibold cursor-pointer transition-all duration-200 hover:scale-110 {{ $i === 0 ? 'current' : 'open' }}"
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
                    submitExam();
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
                document.querySelectorAll('.question-bubble').forEach(bubble => {
                    const bubbleIndex = parseInt(bubble.textContent.trim()) - 1;
                    if (bubbleIndex === index) {
                        bubble.style.borderColor = '#3b82f6';
                        bubble.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';
                    } else {
                        bubble.style.borderColor = marked[bubbleIndex] ? '#f59e0b' : (answers[bubbleIndex] ? '#059669' : '#d1d5db');
                        bubble.style.boxShadow = 'none';
                    }
                });
                
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
                
                let message = `<div class="text-center p-6">`;
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
                message += `<button type="button" onclick="showQuestion(currentQuestion)" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300">⬅️ Zurück zur Prüfung</button>`;
                message += `<button type="button" onclick="submitExam()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">✓ Prüfung abgeben</button>`;
                message += `</div>`;
                message += `</div>`;
                
                document.getElementById('examContainer').innerHTML = message;
            }
            
            // Prüfung abgeben
            function submitExam() {
                document.getElementById('exam-form').submit();
            }
            
            // Initial state
            updateProgress();
        </script>
        
    @else
        <!-- Ergebnis-Ansicht -->
        <div class="p-4">
            @if(isset($gamification_result) && $gamification_result)
                <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded-lg shadow-md animate-pulse">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">🎉</div>
                        <div>
                            <div class="text-sm font-medium text-green-800">
                                Prüfung bestanden! +{{ $gamification_result['points_awarded'] }} Punkte!
                                @if($gamification_result['level_up'])
                                    🎊 Level UP! Neues Level: {{ $gamification_result['new_level'] }}
                                @endif
                            </div>
                            <div class="text-xs text-green-600">{{ $gamification_result['reason'] }}</div>
                        </div>
                    </div>
                </div>
            @endif
            
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
                <div class="bg-yellow-400 h-4 rounded-full shadow-lg" style="width: {{ $percent }}%;"></div>
                <div class="absolute flex items-center" style="left: 80%; top: 0; height: 100%;">
                    <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                    <div class="ml-1 text-xs font-bold text-red-600 bg-white px-1 rounded">80%</div>
                </div>
            </div>
            
            <div class="text-center mb-4 text-sm text-gray-600">
                {{ $correctCount }} von {{ $total }} richtig
                <span class="ml-2 text-xs font-bold" style="color: {{ $percent }}%">{{ $percent }}% erreicht</span>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
                   style="background: linear-gradient(to right, #4b5563, #374151); color: white;">
                    🏠 Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
