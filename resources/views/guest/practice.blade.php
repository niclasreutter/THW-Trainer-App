@extends('layouts.app')
@section('title', 'THW Theorie anonym üben - Sofort starten ohne Anmeldung')
@section('description', 'Übe THW Theoriefragen sofort und anonym ohne Anmeldung. Perfekt zum schnellen Testen und Lernen. Jederzeit kostenlos verfügbar!')
@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
    @if($question)
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold">
                @if(isset($mode))
                    @switch($mode)
                        @case('all')
                            📚 Alle Fragen (Anonym)
                            @break
                        @default
                            Anonym üben
                    @endswitch
                @else
                    Anonym üben
                @endif
            </h2>
            <a href="{{ route('guest.practice.menu') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Zurück zum Menü</a>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm text-yellow-800 font-medium">Anonymes Üben</p>
                        <p class="text-xs text-yellow-700 mt-1">Deine Fortschritte werden nicht gespeichert. Für vollständige Funktionen erstelle einen kostenlosen Account.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('guest.practice.submit') }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            
            @php
                // Erstelle ein Array mit den Antworten
                $answersOriginal = [
                    ['letter' => 'A', 'text' => $question->antwort_a],
                    ['letter' => 'B', 'text' => $question->antwort_b],
                    ['letter' => 'C', 'text' => $question->antwort_c],
                ];
                
                // Wenn eine Antwort angezeigt wird (isCorrect gesetzt), nutze das Mapping aus Session
                if (isset($isCorrect) && session()->has('guest_answer_mapping_' . $question->id)) {
                    $mappingArray = session('guest_answer_mapping_' . $question->id);
                    
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
                    ksort($answers);
                    
                    // Lösche Mapping nach Anzeige
                    session()->forget('guest_answer_mapping_' . $question->id);
                } else {
                    // Neue Frage: shuffle
                    $answers = $answersOriginal;
                    shuffle($answers);
                    
                    // Erstelle Mapping: Position -> Buchstabe
                    $mappingArray = [];
                    foreach ($answers as $index => $answer) {
                        $mappingArray[$index] = $answer['letter'];
                    }
                    
                    // Speichere Mapping in Session (falls die Frage beantwortet wird)
                    if (!isset($isCorrect)) {
                        session(['guest_answer_mapping_' . $question->id => $mappingArray]);
                    }
                }
                
                $mappingJson = json_encode($mappingArray);
                $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
            @endphp
            
            <input type="hidden" name="answer_mapping" value="{{ $mappingJson }}">
            
            <div class="mb-6 p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="mb-2 text-xs text-gray-500 flex items-center gap-2 flex-wrap">
                    <span>ID: {{ $question->id }}</span>
                    <span class="mx-2">&middot;</span>
                    <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                    <span class="mx-2">&middot;</span>
                    <span class="text-blue-600">Mapping: {{ $mappingJson }}</span>
                    <span class="mx-2">&middot;</span>
                    <span class="text-green-600">Lösung DB: {{ $question->loesung }}</span>
                </div>
                <div class="mb-2 font-bold">Frage:</div>
                <div class="mb-4">{{ $question->frage }}</div>
                <div class="mb-4">
                    <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                    <div class="flex flex-col gap-3">
                        @foreach($answers as $index => $answer)
                            @php
                                $originalLetter = $answer['letter'];
                                $isCorrectAnswer = $solution->contains($originalLetter);
                                $isUserAnswer = isset($userAnswer) && $userAnswer->contains($originalLetter);
                                $isChecked = isset($isCorrect) && $isUserAnswer;
                            @endphp
                            <label class="inline-flex items-center p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
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
                                    class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <span class="ml-2 {{ isset($isCorrect) && $isChecked ? ($isCorrectAnswer ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold') : '' }}">
                                    {{ $answer['text'] }}
                                    <span class="text-xs text-gray-400 ml-1">(DB: {{ $answer['letter'] }})</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" style="width: 100%; display: block; text-align: center; background-color: #1e3a8a; color: #fbbf24; padding: 12px 24px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2);" 
                        onmouseover="if(!this.disabled) { this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 25px rgba(251, 191, 36, 0.5), 0 0 50px rgba(251, 191, 36, 0.3)'; }" 
                        onmouseout="if(!this.disabled) { this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2)'; }"
                        disabled>Antwort absenden</button>
            @elseif(isset($isCorrect) && $isCorrect)
                <a href="{{ route('guest.practice.index') }}" style="width: 100%; display: block; text-align: center; background-color: #1e3a8a; color: #fbbf24; padding: 12px 24px; border-radius: 8px; font-weight: bold; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 25px rgba(251, 191, 36, 0.5), 0 0 50px rgba(251, 191, 36, 0.3)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2)';">Nächste Frage</a>
                <div class="mt-3 p-4 bg-green-50 border-2 border-green-300 rounded-lg text-green-800 font-bold shadow-lg" style="box-shadow: 0 0 15px rgba(34, 197, 94, 0.3), 0 0 30px rgba(34, 197, 94, 0.1);">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">✅</div>
                        <span>Richtig beantwortet!</span>
                    </div>
                </div>
            @elseif(isset($isCorrect) && !$isCorrect)
                <a href="{{ route('guest.practice.index', ['skip_id' => $question->id]) }}" style="width: 100%; display: block; text-align: center; background-color: #1e3a8a; color: #fbbf24; padding: 12px 24px; border-radius: 8px; font-weight: bold; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 25px rgba(251, 191, 36, 0.5), 0 0 50px rgba(251, 191, 36, 0.3)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px rgba(30, 58, 138, 0.4), 0 0 40px rgba(30, 58, 138, 0.2)';">Nächste Frage</a>
                <div class="mt-3 p-4 rounded-lg font-bold shadow-lg" style="background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3), 0 0 30px rgba(239, 68, 68, 0.1);">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">❌</div>
                        <span>Leider falsch. Die richtigen Antworten sind markiert.</span>
                    </div>
                </div>
            @endif
        </form>
        
        <script>
            @if(!isset($isCorrect))
            // Checkbox logic - nur wenn Frage noch nicht beantwortet
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
            <a href="{{ route('guest.practice.menu') }}" class="inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300 mr-4">Zurück zum Übungsmenü</a>
            <a href="{{ route('home') }}" class="inline-block bg-gray-600 text-white px-6 py-2 rounded font-bold hover:bg-gray-700 hover:shadow-lg hover:scale-105 transition-all duration-300">Zur Startseite</a>
        </div>
    @endif
</div>
@endsection
