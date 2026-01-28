@extends(isset($isLanding) && $isLanding ? 'layouts.landing' : 'layouts.app')
@section('title', 'THW Prüfungssimulation anonym - 40 Fragen ohne Anmeldung')
@section('description', 'THW Prüfungssimulation anonym: Teste dich mit 40 zufälligen Fragen in 30 Minuten ohne Anmeldung. Sofortige Auswertung und Ergebnisanzeige!')
@section('content')
<style>
    /* CACHE BUST v8.6 - GUEST EXAM MODERNIZED - 2025-10-21-17:30 */
    
    /* Desktop: Modernes Kartendesign */
    @media (min-width: 641px) {
        /* Nav und Footer bleiben sichtbar */
        nav, footer {
            display: block !important;
        }
        
        /* Body als Flexbox für Footer am Rand */
        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
        }
        
        main {
            flex: 1 0 auto !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
        }
        
        footer {
            flex-shrink: 0 !important;
        }
        
        /* Exam Container - OPTIMALE BREITE */
        #guestExamContainer {
            max-width: 950px !important;
            width: 95% !important;
            margin: 0 auto 1rem auto !important;
            padding: 2.5rem !important;
            background: white !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }
        
        #guestExamContainer:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08) !important;
        }
    }
</style>

<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 relative" id="guestExamContainer">
    @if(!isset($submitted))
    <div id="exam-timer" class="absolute top-0 right-0 text-lg font-bold text-blue-900 bg-gradient-to-r from-yellow-200 to-yellow-300 px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300" style="box-shadow: 0 0 10px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.2);">30:00</div>
    <script>
        let timeLeft = 30 * 60;
        const timerEl = document.getElementById('exam-timer');
        const formEl = document.querySelector('form[action="{{ route('landing.guest.exam.submit') }}"]');
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
    @endif
    
    @if(isset($fragen) && $fragen->count())
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Prüfung: 40 Fragen (Anonym)</h2>
        <a href="{{ route('landing.guest.practice.menu') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Zurück zum Menü</a>
    </div>
    
    @if(isset($submitted))
        <div class="mb-6 p-6 text-center rounded-lg {{ $passed ? 'text-green-800' : 'text-red-800' }}" 
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
    
    @endif
    
    <form method="POST" action="{{ route('landing.guest.exam.submit') }}">
            @csrf
            @foreach($fragen as $nr => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                
                @php
                    // Erstelle ein Array mit den Antworten
                    $answersOriginal = [
                        ['letter' => 'A', 'text' => $frage->antwort_a],
                        ['letter' => 'B', 'text' => $frage->antwort_b],
                        ['letter' => 'C', 'text' => $frage->antwort_c],
                    ];
                    
                    // Neue Prüfung: shuffle - Submitted: nutze das Mapping
                    if (isset($submitted) && isset($results[$nr]['mapping'])) {
                        $mappingArray = $results[$nr]['mapping'];
                        
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
                        shuffle($answers);
                        
                        $mappingArray = [];
                        foreach ($answers as $index => $answer) {
                            $mappingArray[$index] = $answer['letter'];
                        }
                    }
                    
                    $mappingJson = json_encode($mappingArray);
                    $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
                @endphp
                
                <input type="hidden" name="answer_mappings[{{ $nr }}]" value="{{ $mappingJson }}">
                
                <div class="mb-6 p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="mb-2 text-[9px] sm:text-xs text-gray-500 flex items-center gap-1">
                        <span>ID: {{ $frage->id }}</span>
                        <span class="mx-0.5 sm:mx-2">&middot;</span>
                        <span>Lernabschnitt: {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</span>
                    </div>
                    <div class="mb-2 font-bold">Frage {{ $nr+1 }}:</div>
                    <div class="mb-4">{{ $frage->frage }}</div>
                    <div class="mb-4">
                        <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                        <div class="flex flex-col gap-3">
                            @foreach($answers as $index => $answer)
                                @php
                                    $originalLetter = $answer['letter'];
                                    $isCorrectAnswer = $solution->contains($originalLetter);
                                    $isUserAnswer = isset($submitted) && isset($results[$nr]['userAnswer']) && $results[$nr]['userAnswer']->contains($originalLetter);
                                    
                                    // Farbe bestimmen (wie bei exam)
                                    if (isset($submitted)) {
                                        if ($isCorrectAnswer && $isUserAnswer) {
                                            // Richtig ausgewählt
                                            $bgColor = 'bg-green-100';
                                            $borderColor = 'border-green-400';
                                            $textColor = 'text-green-900';
                                            $icon = '✓';
                                        } elseif ($isCorrectAnswer && !$isUserAnswer) {
                                            // Nicht ausgewählt aber richtig
                                            $bgColor = 'bg-green-50';
                                            $borderColor = 'border-green-300';
                                            $textColor = 'text-green-800';
                                            $icon = '✓';
                                        } elseif (!$isCorrectAnswer && $isUserAnswer) {
                                            // Falsch ausgewählt
                                            $bgColor = 'bg-red-100';
                                            $borderColor = 'border-red-400';
                                            $textColor = 'text-red-900';
                                            $icon = '✗';
                                        } else {
                                            // Nicht relevant
                                            $bgColor = 'bg-gray-50';
                                            $borderColor = 'border-gray-200';
                                            $textColor = 'text-gray-700';
                                            $icon = '';
                                        }
                                    } else {
                                        $bgColor = '';
                                        $borderColor = '';
                                        $textColor = '';
                                        $icon = '';
                                    }
                                @endphp
                                
                                @if(isset($submitted))
                                    <!-- Lösung-Anzeige mit Farben (wie bei exam) -->
                                    <div class="flex items-start p-3 rounded-lg border-2 {{ $bgColor }} {{ $borderColor }}">
                                        <div class="flex items-center min-w-0 flex-1">
                                            @if($isUserAnswer)
                                                <span class="w-5 h-5 mr-3 flex-shrink-0 text-lg">{{ $icon }}</span>
                                            @else
                                                <span class="w-5 h-5 mr-3 flex-shrink-0"></span>
                                            @endif
                                            <span class="text-sm {{ $textColor }}">
                                                {{ $answer['text'] }}
                                            </span>
                                        </div>
                                        @if($isCorrectAnswer && !$isUserAnswer)
                                            <span class="ml-2 text-xs font-semibold text-green-700 bg-green-200 px-2 py-1 rounded flex-shrink-0">
                                                Richtig
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <!-- Normale Antwort-Auswahl -->
                                    <label class="inline-flex items-center p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                        <input type="checkbox" name="answer[{{ $nr }}][]" value="{{ $index }}"
                                            class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2">{{ $answer['text'] }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @if(isset($submitted))
                        <!-- Zusammenfassung -->
                        <div class="mt-3 pt-3 border-t {{ $results[$nr]['isCorrect'] ? 'border-green-200' : 'border-red-200' }}">
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
                    @endif
                </div>
            @endforeach
            @if(!isset($submitted))
                <button type="submit" class="w-full bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Prüfung beenden</button>
            @endif
        </form>
        
        @if(isset($submitted))
        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing.guest.exam.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
               style="background: linear-gradient(to right, #00337F, #002A66); color: white; box-shadow: 0 4px 15px rgba(0, 51, 127, 0.4), 0 0 20px rgba(0, 51, 127, 0.3), 0 0 40px rgba(0, 51, 127, 0.1);">
                🎓 Neue Prüfung
            </a>
            <a href="{{ route('landing.guest.practice.all') }}" 
               class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
               style="background: linear-gradient(to right, #facc15, #f59e0b); color: white; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);">
                📚 Weiter üben
            </a>
            <a href="{{ route('landing.home') }}" 
               class="inline-flex items-center justify-center px-6 py-3 font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
               style="background: linear-gradient(to right, #4b5563, #374151); color: white; box-shadow: 0 4px 15px rgba(75, 85, 99, 0.4), 0 0 20px rgba(75, 85, 99, 0.3), 0 0 40px rgba(75, 85, 99, 0.1);">
                🏠 Startseite
            </a>
        </div>
        @endif
    @else
        <div class="text-center text-lg">Keine Fragen gefunden. <a href="{{ route('landing.guest.exam.index') }}" class="text-blue-900 underline">Neue Simulation starten</a></div>
    @endif
</div>
@endsection
