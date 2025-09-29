@extends('layouts.app')
@section('title', 'THW Prüfungssimulation - 40 Fragen in 30 Minuten')
@section('description', 'THW Prüfungssimulation: Teste dein Wissen mit 40 zufälligen Fragen in 30 Minuten. Realistische Prüfungsbedingungen und sofortige Auswertung. Übe jetzt kostenlos!')
@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 relative">
    @if(!isset($submitted))
    <div id="exam-timer" class="absolute top-0 right-0 text-lg font-bold text-blue-900 bg-gradient-to-r from-yellow-200 to-yellow-300 px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300" style="box-shadow: 0 0 10px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.2);">30:00</div>
    <script>
        let timeLeft = 30 * 60;
        const timerEl = document.getElementById('exam-timer');
        const formEl = document.querySelector('form[action="{{ route('exam.submit') }}"]');
        function updateTimer() {
            const min = Math.floor(timeLeft / 60).toString().padStart(2, '0');
            const sec = (timeLeft % 60).toString().padStart(2, '0');
            timerEl.textContent = `${min}:${sec}`;
            if (timeLeft <= 0) {
                timerEl.textContent = '00:00';
                if(formEl) formEl.submit();
            } else {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
        }
        updateTimer();
    </script>
    
    <script>
        // Progress Update basierend auf beantworteten Fragen
        function updateProgress() {
            const totalQuestions = 40;
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="answer"]');
            const answeredQuestions = new Set();
            
            // Zähle beantwortete Fragen
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const name = checkbox.name;
                    const questionIndex = name.match(/answer\[(\d+)\]/);
                    if (questionIndex) {
                        answeredQuestions.add(parseInt(questionIndex[1]));
                    }
                }
            });
            
            const answeredCount = answeredQuestions.size;
            const progressPercent = (answeredCount / totalQuestions) * 100;
            
            document.getElementById('progress-text').textContent = `${answeredCount}/${totalQuestions}`;
            document.getElementById('progress-bar').style.width = `${progressPercent}%`;
        }
        
        // Event Listener für alle Checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox' && e.target.name.startsWith('answer[')) {
                updateProgress();
            }
        });
        
        updateProgress(); // Initial update
    </script>
    @endif
    @if(isset($fragen) && $fragen->count())
    <h2 class="text-2xl font-bold mb-4">Prüfung: 40 Fragen</h2>
    @if(!isset($submitted))
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-blue-800">Fortschritt:</span>
            <span class="text-sm font-medium text-blue-800" id="progress-text">1/40</span>
        </div>
        <div class="w-full bg-blue-200 rounded-full h-3">
            <div id="progress-bar" class="h-3 rounded-full transition-all duration-300" style="width: 2.5%; background-color: #2563eb; box-shadow: 0 0 8px rgba(37, 99, 235, 0.4);"></div>
        </div>
    </div>
    @endif
    @if(isset($submitted))
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
             style="{{ $passed ? 'background-color: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3); box-shadow: 0 0 20px rgba(34, 197, 94, 0.4), 0 0 40px rgba(34, 197, 94, 0.2);' : 'background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); box-shadow: 0 0 20px rgba(239, 68, 68, 0.4), 0 0 40px rgba(239, 68, 68, 0.2);' }}">
            <div class="text-4xl mb-2">{{ $passed ? '🎉' : '😔' }}</div>
            <div class="text-xl font-bold">
                Du hast mit {{ round(($correctCount/$total)*100) }}% {{ $passed ? 'Bestanden' : 'Nicht Bestanden' }}.
            </div>
        </div>
        @php
            $percent = $total > 0 ? round($correctCount / $total * 100) : 0;
            $threshold = 80;
        @endphp
    <div class="w-full bg-gray-200 rounded-full h-6 mb-2 relative">
            <div class="bg-yellow-400 h-6 rounded-full shadow-lg" style="width: {{ $percent }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
            <div class="absolute flex items-center" style="left: 80%; top: 0; height: 100%;">
                <div class="w-1 h-6 bg-red-500 rounded-full shadow-lg" style="box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);"></div>
                <div class="ml-1 text-xs font-bold text-red-600 bg-white px-1 rounded">80%</div>
            </div>
    </div>
    <div class="text-center mb-2">
        <span class="text-sm font-bold text-blue-900 bg-white px-2 py-1 rounded shadow-sm">{{ $percent }}% erreicht</span>
    </div>
    <div class="text-sm text-gray-600 text-center mb-6">Im Dashboard kannst du deine falschen Antworten wiederholen</div>
    @endif
    <form method="POST" action="{{ route('exam.submit') }}">
            @csrf
            @foreach($fragen as $nr => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                <div class="mb-6 p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="mb-2 text-xs text-gray-500 flex items-center gap-2">
                        <span>ID: {{ $frage->id }}</span>
                        <span class="mx-2">&middot;</span>
                        <span>Lernabschnitt: {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</span>
                    </div>
                    <div class="mb-2 font-bold">Frage {{ $nr+1 }}:</div>
                    <div class="mb-4">{{ $frage->frage }}</div>
                    <div class="mb-4">
                        <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                        <div class="flex flex-col gap-3">
                            @foreach(['A','B','C'] as $option)
                                <label class="inline-flex items-center p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                    @if(isset($submitted))
                                        @if($results[$nr]['solution']->contains($option))
                                            <span class="mr-2 text-green-600 text-lg">✅</span>
                                        @elseif(isset($results[$nr]['userAnswer']) && $results[$nr]['userAnswer']->contains($option))
                                            <span class="mr-2 text-red-600 text-lg">❌</span>
                                        @else
                                            <span class="mr-2 text-gray-400 text-lg">⚪</span>
                                        @endif
                                    @endif
                                    <input type="checkbox" name="answer[{{ $nr }}][]" value="{{ $option }}"
                                        @if(isset($submitted) && isset($results[$nr]['userAnswer']) && $results[$nr]['userAnswer']->contains($option)) checked @endif
                                        @if(isset($submitted)) disabled @endif
                                        class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2">{{ $option }}: {{ $frage['antwort_'.strtolower($option)] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @if(isset($submitted))
                        @if($results[$nr]['isCorrect'])
                            <div class="mt-4 p-4 rounded-lg font-bold shadow-lg" style="background-color: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3); color: #16a34a; box-shadow: 0 0 15px rgba(34, 197, 94, 0.3), 0 0 30px rgba(34, 197, 94, 0.1);">
                                <div class="flex items-center">
                                    <div class="text-2xl mr-3">✅</div>
                                    <span>Richtig beantwortet!</span>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-4 rounded-lg font-bold shadow-lg" style="background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3), 0 0 30px rgba(239, 68, 68, 0.1);">
                                <div class="flex items-center">
                                    <div class="text-2xl mr-3">❌</div>
                                    <span>Leider falsch. Die richtigen Antworten sind markiert.</span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
            @if(!isset($submitted))
                <button type="submit" class="w-full bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Prüfung beenden</button>
            @endif
        </form>
    @else
        <div class="text-center text-lg">Keine Fragen gefunden. <a href="{{ route('exam.index') }}" class="text-blue-900 underline">Neue Simulation starten</a></div>
    @endif
    
    @if(isset($submitted))
    <!-- Action Buttons -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
        @php
            $user = Auth::user();
            $failedQuestions = is_array($user->exam_failed_questions ?? null) 
                ? $user->exam_failed_questions 
                : (is_string($user->exam_failed_questions) ? json_decode($user->exam_failed_questions, true) ?? [] : []);
            $hasFailedQuestions = !empty($failedQuestions) && count($failedQuestions) > 0;
        @endphp
        
        @if($hasFailedQuestions)
            <!-- Fragen wiederholen Button -->
            <a href="{{ route('failed.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
               style="background: linear-gradient(to right, #facc15, #f59e0b); color: white; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);">
                🔄 Fragen wiederholen
            </a>
        @else
            <!-- Neue Prüfung Button -->
            <a href="{{ route('exam.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
               style="background: linear-gradient(to right, #00337F, #002A66); color: white; box-shadow: 0 4px 15px rgba(0, 51, 127, 0.4), 0 0 20px rgba(0, 51, 127, 0.3), 0 0 40px rgba(0, 51, 127, 0.1);">
                🎓 Neue Prüfung
            </a>
        @endif
        
        <!-- Dashboard Button -->
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
           style="background: linear-gradient(to right, #4b5563, #374151); color: white; box-shadow: 0 4px 15px rgba(75, 85, 99, 0.4), 0 0 20px rgba(75, 85, 99, 0.3), 0 0 40px rgba(75, 85, 99, 0.1);">
            🏠 Dashboard
        </a>
    </div>
    @endif
</div>
@endsection
