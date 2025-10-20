@extends('layouts.app')
@section('title', 'THW Theorie anonym üben - Sofort starten ohne Anmeldung')
@section('description', 'Übe THW Theoriefragen sofort und anonym ohne Anmeldung. Perfekt zum schnellen Testen und Lernen. Jederzeit kostenlos verfügbar!')
@section('content')
<style>
    /* CACHE BUST v6.8 - GUEST VIEW GLEICHGESTELLT MIT LOGIN VIEW - 2025-10-20-20:00 */
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
    
    /* ===== MOBILE OPTIMIERUNG (nur unter 640px) ===== */
    @media (max-width: 640px) {
        /* Footer und Navigation ausblenden */
        footer {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        
        nav {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        
        /* Body auf Vollbild */
        body {
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
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
        
        /* Practice Container ohne Margins */
        #guestPracticeContainer {
            margin: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding: 0.75rem !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            max-width: 100% !important;
            width: 100% !important;
            min-height: 100vh !important;
        }
        
        /* Alle responsive Klassen überschreiben */
        .sm\:mt-4, .sm\:mt-8, .sm\:p-4, .sm\:p-6, 
        .sm\:rounded-lg, .sm\:shadow-lg {
            margin-top: 0 !important;
            padding: 0.75rem !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        
        /* MOBILE TOUCH OPTIMIERUNG - Größere Druckflächen & Schickes Design */
        
        /* Checkboxen größer mit schönem Design */
        input[type="checkbox"] {
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            min-height: 28px !important;
            margin-right: 14px !important;
            cursor: pointer !important;
            border: 2.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            background-color: white !important;
            position: relative !important;
        }
        
        input[type="checkbox"]:checked {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        input[type="checkbox"]:checked::after {
            content: '✓' !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            color: white !important;
            font-size: 18px !important;
            font-weight: bold !important;
        }
        
        /* Labels mit größerer Touch-Fläche und schönem Design */
        label.inline-flex {
            padding: 18px 16px !important;
            min-height: 64px !important;
            margin-bottom: 14px !important;
            font-size: 16px !important;
            line-height: 1.5 !important;
            cursor: pointer !important;
            background: white !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
            -webkit-tap-highlight-color: rgba(59, 130, 246, 0.1) !important;
        }
        
        label.inline-flex:active {
            transform: scale(0.98) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
            border-color: #3b82f6 !important;
            background: #f0f9ff !important;
        }
        
        /* Buttons größer für Touch mit schönem Design */
        button[type="submit"],
        a.w-full {
            padding: 20px 24px !important;
            font-size: 18px !important;
            min-height: 60px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            border-radius: 14px !important;
            transition: all 0.3s ease !important;
            -webkit-tap-highlight-color: rgba(251, 191, 36, 0.2) !important;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15), 0 2px 4px rgba(30, 58, 138, 0.1) !important;
        }
        
        button[type="submit"]:active,
        a.w-full:active {
            transform: translateY(2px) !important;
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2) !important;
        }
        
        /* Header Buttons schicker */
        .sm\:hidden button,
        .sm\:hidden a {
            padding: 12px !important;
            min-width: 48px !important;
            min-height: 48px !important;
            border-radius: 12px !important;
            transition: all 0.2s ease !important;
        }
        
        .sm\:hidden button:active,
        .sm\:hidden a:active {
            transform: scale(0.95) !important;
            background-color: #e5e7eb !important;
        }
        
        /* Frage-Container schlicht (kein Karten-Design) */
        #guestPracticeContainer > form > div.mb-2 {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
        }
        
        /* Frage-Text Bereich ohne Kartendesign */
        #guestPracticeContainer > form > div.mb-2 > div:first-child,
        #guestPracticeContainer > form > div.mb-2 > div:nth-child(2),
        #guestPracticeContainer > form > div.mb-2 > div:nth-child(3) {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        
        /* Progress Bar schicker mit Glow */
        .bg-gray-200 {
            background-color: #e5e7eb !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }
        
        .bg-yellow-400 {
            box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 
                        0 0 20px rgba(251, 191, 36, 0.4), 
                        0 0 30px rgba(251, 191, 36, 0.2) !important;
        }
        
        /* Progress Bar im Header auch mit Glow */
        .sm\:hidden .bg-yellow-400 {
            box-shadow: 0 0 8px rgba(251, 191, 36, 0.5), 
                        0 0 16px rgba(251, 191, 36, 0.3) !important;
        }
        
        /* Bessere Abstände zwischen Elementen */
        .flex.flex-col.gap-1\.5 {
            gap: 14px !important;
        }
        
        /* Text größer und lesbarer */
        .text-xs {
            font-size: 15px !important;
        }
        
        .font-bold {
            font-weight: 600 !important;
        }
        
        /* Button fixiert am unteren Rand */
        #guestPracticeContainer > form {
            display: flex !important;
            flex-direction: column !important;
            min-height: calc(100vh - 140px) !important;
            padding-bottom: 4px !important;
        }
        
        #guestPracticeContainer > form > .mb-2 {
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
        }
        
        /* Spacer um Button nach unten zu schieben */
        #guestPracticeContainer > form::before {
            content: '' !important;
            flex-grow: 1 !important;
            order: 2 !important;
            min-height: 8px !important;
        }
        
        /* Button und Meldungen am Ende */
        #guestPracticeContainer button[type="submit"],
        #guestPracticeContainer a.w-full {
            order: 4 !important;
            margin-top: 0 !important;
            margin-bottom: 4px !important;
        }
        
        /* Gamification/Meldungen über dem Button */
        #guestPracticeContainer > form > div.mt-2,
        #guestPracticeContainer > form > div.animate-fade-in {
            order: 3 !important;
            margin-top: 0 !important;
            margin-bottom: 12px !important;
        }
        
        /* Mehr Abstand zwischen Frage und Antworten */
        #guestPracticeContainer .mb-2.sm\:mb-6 > div:last-child {
            margin-top: 24px !important;
        }
        
        /* Alle Meldungskarten gleich hoch */
        #guestPracticeContainer > form > div.mt-2,
        #guestPracticeContainer > form > div.animate-fade-in {
            min-height: 70px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }
        
        /* Falsch-Meldung mit rotem Glow */
        #guestPracticeContainer > form > div.mt-2:has(> div > div:first-child:contains('❌')) {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4), 
                        0 0 25px rgba(239, 68, 68, 0.3), 
                        0 0 35px rgba(239, 68, 68, 0.2) !important;
        }
        
        /* Mehr Abstand zwischen Karten innerhalb eines Containers */
        #guestPracticeContainer > form > div.mt-2 > div + div,
        #guestPracticeContainer > form > div.animate-fade-in > div + div {
            margin-top: 12px !important;
        }
    }
    
    /* Desktop: Mehr vertikaler Platz und größere Elemente */
    @media (min-width: 641px) {
        main {
            padding-top: 2rem;
            padding-bottom: 2rem;
            min-height: calc(100vh - 200px);
        }
        
        /* Größere Schriften und Abstände auf Desktop */
        #guestPracticeContainer h2 {
            font-size: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        #guestPracticeContainer .text-sm {
            font-size: 1rem !important;
        }
        
        #guestPracticeContainer .text-xs {
            font-size: 0.9rem !important;
        }
        
        #guestPracticeContainer .mb-3 {
            margin-bottom: 1.5rem !important;
        }
        
        #guestPracticeContainer .mb-2 {
            margin-bottom: 1rem !important;
        }
        
        #guestPracticeContainer label {
            padding: 1rem !important;
            font-size: 1rem !important;
            line-height: 1.6 !important;
        }
        
        #guestPracticeContainer button[type="submit"],
        #guestPracticeContainer a.w-full {
            padding: 1rem 1.5rem !important;
            font-size: 1.1rem !important;
        }
        
        /* Alle Meldungskarten gleich hoch */
        #guestPracticeContainer > form > div.mt-2,
        #guestPracticeContainer > form > div.animate-fade-in {
            min-height: 80px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }
        
        /* Falsch-Meldung mit rotem Glow auch auf Desktop */
        #guestPracticeContainer > form > div.mt-2 > div[style*="rgba(239, 68, 68"] {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4), 
                        0 0 25px rgba(239, 68, 68, 0.3), 
                        0 0 35px rgba(239, 68, 68, 0.2) !important;
        }
        
        /* Mehr Abstand zwischen Karten innerhalb eines Containers auf Desktop */
        #guestPracticeContainer > form > div.mt-2 > div + div,
        #guestPracticeContainer > form > div.animate-fade-in > div + div {
            margin-top: 16px !important;
        }
    }
        
        /* Labels mit größerer Touch-Fläche */
        label.inline-flex {
            padding: 18px 16px !important;
            min-height: 64px !important;
            margin-bottom: 14px !important;
            font-size: 16px !important;
            line-height: 1.5 !important;
            cursor: pointer !important;
            background: white !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }
        
        label.inline-flex:active {
            transform: scale(0.98) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
            border-color: #3b82f6 !important;
            background: #f0f9ff !important;
        }
        
        /* Buttons größer für Touch */
        button[type="submit"],
        a[href*="practice.index"] {
            padding: 20px 24px !important;
            font-size: 18px !important;
            min-height: 60px !important;
            font-weight: 700 !important;
            border-radius: 14px !important;
        }
        
        /* Button fixiert am unteren Rand */
        #guestPracticeContainer > form {
            display: flex !important;
            flex-direction: column !important;
            min-height: calc(100vh - 120px) !important;
            padding-bottom: 16px !important;
        }
        
        #guestPracticeContainer > form > div:first-of-type {
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
        }
        
        /* Spacer um Button nach unten zu schieben */
        #guestPracticeContainer > form::before {
            content: '' !important;
            flex-grow: 1 !important;
            order: 2 !important;
            min-height: 20px !important;
        }
        
        /* Button und Meldungen am Ende */
        #guestPracticeContainer button[type="submit"],
        #guestPracticeContainer a[href*="practice.index"] {
            order: 4 !important;
            margin-top: 0 !important;
            margin-bottom: 16px !important;
        }
        
        /* Meldungen über dem Button */
        #guestPracticeContainer > form > div.mt-3 {
            order: 3 !important;
            margin-top: 0 !important;
            margin-bottom: 16px !important;
        }
    }
    
    /* Desktop: Mehr vertikaler Platz */
    @media (min-width: 641px) {
        main {
            padding-top: 2rem;
            padding-bottom: 2rem;
            min-height: calc(100vh - 200px);
        }
    }
</style>

<div class="max-w-xl mx-auto mt-0 sm:mt-10 p-3 sm:p-6 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300" 
     id="guestPracticeContainer">
    @if($question)
        <!-- Mobile: Kompakter Header -->
        <div class="sm:hidden mb-2 flex items-center justify-between p-2 bg-white border-b">
            <a href="{{ route('guest.practice.menu') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1 mx-2">
                <div class="text-xs font-semibold text-gray-700">
                    📚 Anonym
                </div>
            </div>
        </div>

        <!-- Desktop: Normaler Header -->
        <div class="mb-3 sm:mb-4 hidden sm:flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold">
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
        
        <!-- Anonymes Üben Hinweis nur auf Desktop -->
        <div class="mb-3 sm:mb-4 text-sm text-gray-600 hidden sm:block">
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
            
            <div class="mb-2 sm:mb-3">
                <div class="mb-2 text-xs text-gray-500 flex items-center gap-1">
                    <span>ID: {{ $question->id }}</span>
                    <span class="mx-1 sm:mx-2">&middot;</span>
                    <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                </div>
                <div class="mb-2 font-bold">Frage:</div>
                <div class="mb-3 sm:mb-4">{{ $question->frage }}</div>
                <div class="mb-2 sm:mb-4">
                    <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                    <div class="flex flex-col gap-1.5">
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
                                <span class="ml-2 {{ isset($isCorrect) && $isChecked ? ($isCorrectAnswer ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold') : '' }}">
                                    {{ $answer['text'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" class="w-full text-center font-bold py-3 px-4 rounded-lg border-none cursor-pointer transition-all duration-300" 
                        style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);" 
                        onmouseover="if(!this.disabled) { this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)'; }" 
                        onmouseout="if(!this.disabled) { this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)'; }"
                        disabled>Antwort absenden</button>
            @elseif(isset($isCorrect) && $isCorrect)
                <a href="{{ route('guest.practice.index') }}" class="w-full block text-center font-bold py-3 px-4 rounded-lg no-underline transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">Nächste Frage</a>
                <div class="mt-2 p-4 rounded-lg font-bold" style="background-color: #f0fdf4; border: 2px solid #86efac; color: #15803d; box-shadow: 0 0 15px rgba(34, 197, 94, 0.4), 0 0 25px rgba(34, 197, 94, 0.3), 0 0 35px rgba(34, 197, 94, 0.2);">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">✅</div>
                        <span>Richtig beantwortet!</span>
                    </div>
                </div>
            @elseif(isset($isCorrect) && !$isCorrect)
                <a href="{{ route('guest.practice.index', ['skip_id' => $question->id]) }}" class="w-full block text-center font-bold py-3 px-4 rounded-lg no-underline transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">Nächste Frage</a>
                <div class="mt-2 p-4 rounded-lg font-bold" style="background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.4), 0 0 25px rgba(239, 68, 68, 0.3), 0 0 35px rgba(239, 68, 68, 0.2);">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">❌</div>
                        <span>Leider falsch. Die richtigen Antworten sind markiert.</span>
                    </div>
                </div>
            @endif
        </form>
        
        <script>
            // Mobile Layout Detection & Setup
            function setupMobileLayout() {
                const isMobile = window.innerWidth <= 640;
                const container = document.getElementById('guestPracticeContainer');
                
                if (isMobile) {
                    container.style.cssText = 'max-width: 100% !important; margin: 0 !important; padding: 0.75rem !important; border-radius: 0 !important; box-shadow: none !important; min-height: 100vh !important;';
                } else {
                    container.style.cssText = '';
                    container.className = 'max-w-xl mx-auto mt-0 sm:mt-10 p-3 sm:p-6 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300';
                }
            }
            
            setupMobileLayout();
            window.addEventListener('resize', setupMobileLayout);
            
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
