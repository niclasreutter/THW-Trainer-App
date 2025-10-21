@extends('layouts.app')
@section('title', 'THW Prüfungssimulation - 40 Fragen in 30 Minuten')
@section('description', 'THW Prüfungssimulation: Teste dein Wissen mit 40 zufälligen Fragen in 30 Minuten. Realistische Prüfungsbedingungen und sofortige Auswertung. Übe jetzt kostenlos!')

@push('styles')
<style>
    /* Navigation und Footer ausblenden */
    nav {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
    }
    
    footer {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
    }
    
    /* Body auf Vollbild ohne Scrollen */
    body {
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 100vh !important;
    }
    
    /* Main Container auf volle Höhe */
    main {
        height: 100vh !important;
        max-height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
    }
    
    /* Container nimmt die volle Viewport-Höhe ein */
    .exam-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    /* Fragenbereich nimmt den verfügbaren Platz ein */
    .question-area {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }
    
    /* Navigation ist fixiert am unteren Rand */
    .navigation-area {
        border-top: 2px solid #e5e7eb;
        padding: 1rem 1.5rem;
        background: white;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
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
<div class="exam-container">
    @if(!isset($submitted))
        <!-- Header mit Timer und Fortschritt -->
        <div class="bg-white border-b-2 border-gray-200 p-4">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold text-blue-900">THW Prüfung</h1>
                    <div id="exam-timer" class="timer">30:00</div>
                </div>
                
                <!-- Fortschrittsbalken -->
                <div class="mb-2">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Fortschritt</span>
                        <span id="progress-text">0/40 beantwortet</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="progress-bar" class="h-3 rounded-full transition-all duration-300 bg-blue-600" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Fragenbereich -->
        <div class="question-area">
            <div class="max-w-4xl mx-auto">
                <form id="exam-form" method="POST" action="{{ route('exam.submit') }}">
                    @csrf
                    
                    @foreach($fragen as $index => $frage)
                        <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                        
                        <div class="question-slide" data-question="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <!-- Fragen-Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500 mb-2">
                                            Frage {{ $index + 1 }} von 40
                                            <span class="mx-2">•</span>
                                            ID: {{ $frage->id }}
                                            <span class="mx-2">•</span>
                                            Lernabschnitt: {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}
                                        </div>
                                        <h2 class="text-xl font-bold text-gray-900">{{ $frage->frage }}</h2>
                                    </div>
                                    
                                    <!-- Markieren Button -->
                                    <button type="button" 
                                            onclick="toggleMark({{ $index }})" 
                                            id="mark-btn-{{ $index }}"
                                            class="ml-4 px-3 py-1 rounded-lg text-sm font-medium border-2 transition-all duration-200 hover:scale-105"
                                            style="border-color: #d1d5db; background-color: white; color: #6b7280;">
                                        🔖 Markieren
                                    </button>
                                </div>
                                
                                <!-- Antwortoptionen -->
                                <div class="mt-6">
                                    <label class="block mb-3 font-semibold text-gray-700">Antwortmöglichkeiten:</label>
                                    <div class="space-y-3">
                                        @foreach(['A','B','C'] as $option)
                                            <label class="flex items-start p-4 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                                                <input type="checkbox" 
                                                       name="answer[{{ $index }}][]" 
                                                       value="{{ $option }}"
                                                       onchange="updateAnswerStatus({{ $index }})"
                                                       class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                <span class="ml-3 text-gray-900">
                                                    <span class="font-bold">{{ $option }}:</span> {{ $frage['antwort_'.strtolower($option)] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="navigation-area">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-4">
                    <button type="button" 
                            onclick="previousQuestion()" 
                            id="prev-btn"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        ⬅️ Vorherige
                    </button>
                    
                    <span class="text-sm font-medium text-gray-600" id="current-question">Frage 1/40</span>
                    
                    <button type="button" 
                            onclick="nextQuestion()" 
                            id="next-btn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200">
                        Nächste ➡️
                    </button>
                </div>
                
                <!-- Fragenübersicht Toggle -->
                <div class="border-t pt-4">
                    <button type="button" 
                            onclick="toggleOverview()" 
                            class="w-full flex justify-between items-center text-left font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200">
                        <span>Fragenübersicht</span>
                        <svg id="overview-icon" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="overview-container" class="mt-4 hidden">
                        <div class="flex gap-4 text-xs mb-3">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full bg-green-500 mr-2"></div>
                                <span>Beantwortet</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full bg-gray-300 mr-2"></div>
                                <span>Offen</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full bg-yellow-400 mr-2"></div>
                                <span>Markiert</span>
                            </div>
                        </div>
                        
                        <div class="question-grid">
                            @for($i = 0; $i < 40; $i++)
                                <button type="button" 
                                        onclick="goToQuestion({{ $i }})"
                                        id="bubble-{{ $i }}"
                                        class="question-bubble open {{ $i === 0 ? 'current' : '' }}">
                                    {{ $i + 1 }}
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
                    timerEl.classList.add('warning');
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
                document.getElementById('current-question').textContent = `Frage ${index + 1}/40`;
                document.getElementById('prev-btn').disabled = index === 0;
                
                // Update Bubbles
                document.querySelectorAll('.question-bubble').forEach(bubble => {
                    bubble.classList.remove('current');
                });
                document.getElementById(`bubble-${index}`).classList.add('current');
                
                // Scroll to top
                document.querySelector('.question-area').scrollTop = 0;
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
            }
            
            // Antwort-Status aktualisieren
            function updateAnswerStatus(index) {
                const checkboxes = document.querySelectorAll(`input[name="answer[${index}][]"]`);
                const isAnswered = Array.from(checkboxes).some(cb => cb.checked);
                answers[index] = isAnswered;
                
                const bubble = document.getElementById(`bubble-${index}`);
                if (isAnswered) {
                    bubble.classList.remove('open');
                    bubble.classList.add('answered');
                } else if (!marked[index]) {
                    bubble.classList.remove('answered');
                    bubble.classList.add('open');
                }
                
                updateProgress();
            }
            
            // Frage markieren
            function toggleMark(index) {
                marked[index] = !marked[index];
                const bubble = document.getElementById(`bubble-${index}`);
                const btn = document.getElementById(`mark-btn-${index}`);
                
                if (marked[index]) {
                    bubble.classList.add('marked');
                    bubble.classList.remove('open', 'answered');
                    btn.style.backgroundColor = '#fbbf24';
                    btn.style.color = 'white';
                    btn.style.borderColor = '#f59e0b';
                    btn.textContent = '🔖 Markiert';
                } else {
                    bubble.classList.remove('marked');
                    if (answers[index]) {
                        bubble.classList.add('answered');
                    } else {
                        bubble.classList.add('open');
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
                
                let message = `<div class="text-center">`;
                message += `<h2 class="text-2xl font-bold mb-4">Prüfung abschließen?</h2>`;
                message += `<div class="mb-6">`;
                message += `<p class="text-lg mb-2"><strong>${answeredCount} von 40</strong> Fragen beantwortet</p>`;
                
                if (unanswered.length > 0) {
                    message += `<p class="text-red-600 font-medium mb-2">⚠️ ${unanswered.length} Fragen noch offen:</p>`;
                    message += `<p class="text-sm text-gray-600">Fragen: ${unanswered.join(', ')}</p>`;
                }
                
                if (markedCount > 0) {
                    message += `<p class="text-yellow-600 font-medium mt-3">🔖 ${markedCount} Fragen markiert</p>`;
                }
                
                message += `</div>`;
                message += `<div class="flex gap-4 justify-center">`;
                message += `<button type="button" onclick="showQuestion(currentQuestion)" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300">⬅️ Zurück zur Prüfung</button>`;
                message += `<button type="button" onclick="submitExam()" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">✓ Prüfung abgeben</button>`;
                message += `</div>`;
                message += `</div>`;
                
                document.querySelector('.question-area').innerHTML = `
                    <div class="max-w-2xl mx-auto">
                        <div class="bg-white rounded-lg shadow-lg p-8">
                            ${message}
                        </div>
                    </div>
                `;
            }
            
            // Prüfung abgeben
            function submitExam() {
                document.getElementById('exam-form').submit();
            }
            
            // Initial state
            updateProgress();
        </script>
        
    @else
        <!-- Ergebnis-Ansicht (bleibt wie gehabt) -->
        <div class="max-w-4xl mx-auto p-6">
            @if(isset($gamification_result) && $gamification_result)
                <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg shadow-lg animate-pulse">
                    <div class="flex items-center">
                        <div class="text-3xl mr-3">🎉</div>
                        <div>
                            <div class="font-medium text-green-800">
                                Prüfung bestanden! +{{ $gamification_result['points_awarded'] }} Punkte!
                                @if($gamification_result['level_up'])
                                    🎊 Level UP! Neues Level: {{ $gamification_result['new_level'] }}
                                @endif
                            </div>
                            <div class="text-sm text-green-600">{{ $gamification_result['reason'] }}</div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mb-6 p-6 text-center rounded-lg shadow-lg animate-pulse {{ $passed ? 'text-green-800' : 'text-red-800' }}" 
                 style="{{ $passed ? 'background-color: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3); box-shadow: 0 0 20px rgba(34, 197, 94, 0.4);' : 'background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);' }}">
                <div class="text-4xl mb-2">{{ $passed ? '🎉' : '😔' }}</div>
                <div class="text-xl font-bold">
                    Du hast mit {{ round(($correctCount/$total)*100) }}% {{ $passed ? 'Bestanden' : 'Nicht Bestanden' }}.
                </div>
            </div>
            
            @php
                $percent = $total > 0 ? round($correctCount / $total * 100) : 0;
            @endphp
            
            <div class="w-full bg-gray-200 rounded-full h-6 mb-2 relative">
                <div class="bg-yellow-400 h-6 rounded-full shadow-lg" style="width: {{ $percent }}%;"></div>
                <div class="absolute flex items-center" style="left: 80%; top: 0; height: 100%;">
                    <div class="w-1 h-6 bg-red-500 rounded-full"></div>
                    <div class="ml-1 text-xs font-bold text-red-600 bg-white px-1 rounded">80%</div>
                </div>
            </div>
            
            <div class="text-center mb-6">
                <span class="text-sm font-bold text-blue-900 bg-white px-2 py-1 rounded shadow-sm">{{ $percent }}% erreicht</span>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
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
