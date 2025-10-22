@extends('layouts.app')
@section('title', 'THW Theorie anonym üben - Sofort starten ohne Anmeldung')
@section('description', 'Übe THW Theoriefragen sofort und anonym ohne Anmeldung. Perfekt zum schnellen Testen und Lernen. Jederzeit kostenlos verfügbar!')
@section('content')
<style>
    /* CACHE BUST v9.1 - CSS FALLBACK FIX - 2025-10-22-21:00 */
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
            padding-bottom: 180px !important; /* Fallback für ältere Browser */
            padding-bottom: calc(120px + env(safe-area-inset-bottom, 60px)) !important; /* Extra Platz für Browser-UI + Safe Area */
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
            margin-bottom: 48px !important; /* Fallback für ältere Browser */
            margin-bottom: calc(24px + env(safe-area-inset-bottom, 24px)) !important; /* Extra Abstand + Safe Area */
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
        
        /* Mehr Abstand zwischen Frage und Antworten */
        #guestPracticeContainer .mb-2.sm\:mb-3 > div:last-child {
            margin-top: 24px !important;
        }
        
        /* Gamification Popup Overlay - Oben rechts positioniert */
        .gamification-popup {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 9999 !important;
            width: 320px !important;
            max-width: 90vw !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transition: all 0.3s ease-out !important;
            transform: translateX(100%) !important;
        }
        
        .gamification-popup.show {
            opacity: 1 !important;
            transform: translateX(0) !important;
            pointer-events: auto !important;
        }
        
        /* Error Popup - Oben rechts positioniert */
        .error-popup {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 9999 !important;
            width: 320px !important;
            max-width: 90vw !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transition: all 0.3s ease-out !important;
            transform: translateX(100%) !important;
        }
        
        .error-popup.show {
            opacity: 1 !important;
            transform: translateX(0) !important;
            pointer-events: auto !important;
        }
    }
    
    /* Desktop: Modernes Kartendesign wie Mobile */
    @media (min-width: 641px) {
        /* Nav und Footer bleiben sichtbar */
        nav, footer {
            display: block !important;
        }
        
        /* Body als Flexbox für Footer am Rand - OHNE min-height */
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
        
        /* Practice Container als schöne Karte - OPTIMALE BREITE */
        #guestPracticeContainer {
            max-width: 950px !important;
            width: 95% !important;
            margin: 0 auto 1rem auto !important;
            padding: 2.5rem !important;
            background: white !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }
        
        #guestPracticeContainer:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08) !important;
        }
        
        /* DESKTOP TOUCH OPTIMIERUNG - Modernes Design wie auf Mobile */
        
        /* Checkboxen größer mit schönem Design */
        #guestPracticeContainer input[type="checkbox"] {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            min-height: 24px !important;
            margin-right: 12px !important;
            cursor: pointer !important;
            border: 2.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            background-color: white !important;
            position: relative !important;
        }
        
        #guestPracticeContainer input[type="checkbox"]:checked {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        #guestPracticeContainer input[type="checkbox"]:checked::after {
            content: '✓' !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            color: white !important;
            font-size: 16px !important;
            font-weight: bold !important;
        }
        
        /* Labels mit schönem Design */
        #guestPracticeContainer label.inline-flex {
            padding: 16px 18px !important;
            min-height: 60px !important;
            margin-bottom: 12px !important;
            font-size: 16px !important;
            line-height: 1.6 !important;
            cursor: pointer !important;
            background: white !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }
        
        #guestPracticeContainer label.inline-flex:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            border-color: #3b82f6 !important;
            background: #f0f9ff !important;
        }
        
        #guestPracticeContainer label.inline-flex:active {
            transform: scale(0.98) !important;
        }
        
        /* Buttons größer und moderner */
        #guestPracticeContainer button[type="submit"],
        #guestPracticeContainer a.w-full {
            padding: 18px 24px !important;
            font-size: 18px !important;
            min-height: 60px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            border-radius: 14px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15), 0 2px 4px rgba(30, 58, 138, 0.1) !important;
        }
        
        #guestPracticeContainer button[type="submit"]:hover,
        #guestPracticeContainer a.w-full:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.25) !important;
        }
        
        #guestPracticeContainer button[type="submit"]:active,
        #guestPracticeContainer a.w-full:active {
            transform: translateY(1px) !important;
        }
        
        /* Header modernisieren */
        #guestPracticeContainer h2 {
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
            font-weight: 700 !important;
            color: #1e3a8a !important;
        }
        
        /* Frage-Container ohne Karten-in-Karte Design */
        #guestPracticeContainer > form > div.mb-2 {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        
        /* Antworten Bereich mit besserem Spacing */
        #guestPracticeContainer .flex.flex-col.gap-1\.5 {
            gap: 12px !important;
        }
        
        /* Text lesbarer */
        #guestPracticeContainer .text-xs {
            font-size: 0.95rem !important;
        }
        
        #guestPracticeContainer .text-sm {
            font-size: 1rem !important;
        }
        
        /* Desktop Popup Styling - Oben rechts */
        .gamification-popup {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            width: 380px !important;
            max-width: 90vw !important;
            transform: translateX(100%) !important;
        }
        
        .gamification-popup.show {
            transform: translateX(0) !important;
        }
        
        .error-popup {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            width: 380px !important;
            max-width: 90vw !important;
            transform: translateX(100%) !important;
        }
        
        .error-popup.show {
            transform: translateX(0) !important;
        }
    }
</style>

<div class="max-w-xl mx-auto mt-0 sm:mt-4 p-3 sm:p-4 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300" 
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
        <div class="mb-2 hidden sm:flex items-center justify-between">
            <h2 class="text-lg font-bold">
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
        
        <form method="POST" action="{{ route('guest.practice.submit') }}" id="guestPracticeForm">
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
            
            <div class="mb-2 sm:mb-2">
                <div class="mb-1 text-[9px] sm:text-[10px] text-gray-500 flex items-center gap-1">
                    <span>ID: {{ $question->id }}</span>
                    <span class="mx-0.5 sm:mx-1">&middot;</span>
                    <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                </div>
                <div class="mb-1 font-bold text-xs sm:text-sm">Frage:</div>
                <div class="mb-2 text-xs sm:text-sm">{{ $question->frage }}</div>
                <div class="mb-2">
                    <label class="block mb-1 font-semibold text-xs sm:text-sm">Antwortmöglichkeiten:</label>
                    <div class="flex flex-col gap-1.5 sm:gap-1.5">
                        @foreach($answers as $index => $answer)
                            @php
                                $originalLetter = $answer['letter'];
                                $isCorrectAnswer = $solution->contains($originalLetter);
                                $isUserAnswer = isset($userAnswer) && $userAnswer->contains($originalLetter);
                                $isChecked = isset($isCorrect) && $isUserAnswer;
                                
                                // Farbe bestimmen (wie bei exam)
                                if (isset($isCorrect)) {
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
                            
                            @if(isset($isCorrect))
                                <!-- Lösung-Anzeige (wie bei exam) -->
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
                                <label class="inline-flex items-start p-1.5 sm:p-1.5 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                    <input type="checkbox" name="answer[]" value="{{ $index }}"
                                        class="mr-1.5 sm:mr-1.5 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-0.5">
                                    <span class="ml-1 sm:ml-1 text-xs sm:text-sm">
                                        {{ $answer['text'] }}
                                    </span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    
                    @if(isset($isCorrect))
                        <!-- Zusammenfassung (wie bei exam) -->
                        <div class="mt-3 pt-3 border-t {{ $isCorrect ? 'border-green-200' : 'border-red-200' }}">
                            <div class="flex items-center justify-between text-xs">
                                <span class="{{ $isCorrect ? 'text-green-700' : 'text-red-700' }} font-medium">
                                    @if($isCorrect)
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
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" class="w-full text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg border-none cursor-pointer transition-all duration-300" 
                        style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);" 
                        onmouseover="if(!this.disabled) { this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)'; }" 
                        onmouseout="if(!this.disabled) { this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)'; }"
                        disabled>
                    Antwort absenden
                </button>
            @else
                <a href="{{ route('guest.practice.index') }}" class="w-full block text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg no-underline transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">
                    Nächste Frage
                </a>
            @endif
        </form>
    </div>
    
    <!-- Success Popup (Richtig beantwortet) -->
    @if(isset($isCorrect) && $isCorrect)
        <div id="successPopup" class="gamification-popup hidden">
            <div style="background: rgba(34, 197, 94, 0.95); border-radius: 16px; padding: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 30px rgba(34, 197, 94, 0.6); border: 2px solid rgba(34, 197, 94, 0.8);">
                <div class="flex items-center gap-3 justify-center">
                    <span style="font-size: 40px;">✅</span>
                    <div style="font-size: 20px; font-weight: bold; color: white;">Richtig beantwortet!</div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Error Popup (Falsch beantwortet) -->
    @if(isset($isCorrect) && !$isCorrect)
        <div id="errorPopup" class="error-popup hidden">
            <div style="background: rgba(239, 68, 68, 0.95); border-radius: 16px; padding: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 30px rgba(239, 68, 68, 0.6); border: 2px solid rgba(239, 68, 68, 0.8);">
                <div class="flex items-center justify-center gap-2">
                    <div style="font-size: 24px;">❌</div>
                    <span style="color: white; font-weight: bold; font-size: 16px;">Falsch. Richtige Antworten markiert.</span>
                </div>
            </div>
        </div>
    @endif
    
    <script>
        // Mobile Layout Detection & Setup
        function setupMobileLayout() {
            const isMobile = window.innerWidth <= 640;
            const container = document.getElementById('guestPracticeContainer');
            
            if (isMobile) {
                container.style.cssText = 'max-width: 100% !important; margin: 0 !important; padding: 0.75rem !important; border-radius: 0 !important; box-shadow: none !important; min-height: 100vh !important;';
            } else {
                container.style.cssText = '';
                container.className = 'max-w-xl mx-auto mt-0 sm:mt-4 p-3 sm:p-4 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300';
            }
        }
        
        setupMobileLayout();
        window.addEventListener('resize', setupMobileLayout);
        
        // Show Success/Error Popups on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Show Success Popup
            const successPopup = document.getElementById('successPopup');
            if (successPopup) {
                setTimeout(() => {
                    successPopup.classList.remove('hidden');
                    setTimeout(() => {
                        successPopup.classList.add('show');
                    }, 10);
                    
                    // Hide after 2.5 seconds
                    setTimeout(() => {
                        successPopup.classList.remove('show');
                        setTimeout(() => {
                            successPopup.classList.add('hidden');
                        }, 300);
                    }, 2500);
                }, 100);
            }
            
            // Show Error Popup
            const errorPopup = document.getElementById('errorPopup');
            if (errorPopup) {
                setTimeout(() => {
                    errorPopup.classList.remove('hidden');
                    setTimeout(() => {
                        errorPopup.classList.add('show');
                    }, 10);
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        errorPopup.classList.remove('show');
                        setTimeout(() => {
                            errorPopup.classList.add('hidden');
                        }, 300);
                    }, 3000);
                }, 100);
            }
        });
        
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
