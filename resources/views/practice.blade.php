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
    /* CACHE BUST v7.4 - SMALLER FONT SIZES - 2025-10-20-20:40 */
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
        #practiceContainer {
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
        
        /* Bookmark Button schicker */
        #bookmarkBtnMobile {
            min-width: 48px !important;
            min-height: 48px !important;
            padding: 12px !important;
            border-radius: 12px !important;
            transition: all 0.2s ease !important;
        }
        
        #bookmarkBtnMobile:active {
            transform: scale(0.95) !important;
            background-color: #fef3c7 !important;
        }
        
        /* Frage-Container schlicht (kein Karten-Design) */
        #practiceContainer > form > div.mb-2 {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
        }
        
        /* Frage-Text Bereich ohne Kartendesign */
        #practiceContainer > form > div.mb-2 > div:first-child,
        #practiceContainer > form > div.mb-2 > div:nth-child(2),
        #practiceContainer > form > div.mb-2 > div:nth-child(3) {
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
        #practiceContainer > form {
            display: flex !important;
            flex-direction: column !important;
            min-height: calc(100vh - 140px) !important;
            padding-bottom: 4px !important;
        }
        
        #practiceContainer > form > .mb-2 {
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
        }
        
        /* Spacer um Button nach unten zu schieben */
        #practiceContainer > form::before {
            content: '' !important;
            flex-grow: 1 !important;
            order: 2 !important;
            min-height: 8px !important;
        }
        
        /* Button und Meldungen am Ende */
        #practiceContainer button[type="submit"],
        #practiceContainer a.w-full {
            order: 4 !important;
            margin-top: 0 !important;
            margin-bottom: 4px !important;
        }
        
        /* Mehr Abstand zwischen Frage und Antworten */
        #practiceContainer .mb-2.sm\:mb-3 > div:last-child {
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
    
    /* Desktop: Kompakte Ansicht für alle Bildschirmgrößen */
    @media (min-width: 641px) {
        main {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
            min-height: 100vh !important;
        }
        
        /* Kompaktere Schriften und Abstände auf Desktop */
        #practiceContainer h2 {
            font-size: 1.25rem !important;
            margin-bottom: 0.75rem !important;
        }
        
        #practiceContainer .text-sm {
            font-size: 0.95rem !important;
        }
        
        #practiceContainer .text-xs {
            font-size: 0.85rem !important;
        }
        
        #practiceContainer .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        #practiceContainer .mb-2 {
            margin-bottom: 0.5rem !important;
        }
        
        #practiceContainer label {
            padding: 0.75rem !important;
            font-size: 0.95rem !important;
            line-height: 1.5 !important;
            margin-bottom: 0.5rem !important;
        }
        
        #practiceContainer button[type="submit"],
        #practiceContainer a.w-full {
            padding: 0.75rem 1.25rem !important;
            font-size: 1rem !important;
            margin-top: 0.75rem !important;
        }
        
        /* Frage-Container kompakter */
        #practiceContainer > form > div.mb-2 {
            padding: 1rem !important;
            margin-bottom: 0.75rem !important;
        }
        
        /* Antworten kompakter stapeln */
        #practiceContainer .flex.flex-col.gap-1\.5 {
            gap: 0.5rem !important;
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

<!-- Practice Container -->
<div class="max-w-xl mx-auto mt-0 sm:mt-4 p-3 sm:p-4 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300" 
     id="practiceContainer">

    @if($question)
        <!-- Mobile: Kompakter Header -->
        <div class="sm:hidden mb-2 flex items-center justify-between p-2 bg-white border-b">
            <a href="{{ route('practice.menu') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1 mx-2">
                <div class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                    <span>
                        @if(isset($mode))
                            @switch($mode)
                                @case('unsolved')🎯 @break
                                @case('section')📖 LA{{ session('practice_parameter') }} @break
                                @case('search')🔍 @break
                                @case('bookmarked')🔖 @break
                                @default 📚 @endswitch
                        @endif
                    </span>
                    <span class="text-gray-500">{{ $progress }}/{{ $total }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                    <div class="bg-yellow-400 h-1.5 rounded-full transition-all" style="width: {{ $progressPercent ?? 0 }}%;"></div>
                </div>
            </div>
            @php
                $user = Auth::user();
                $bookmarked = is_array($user->bookmarked_questions ?? null) 
                    ? $user->bookmarked_questions 
                    : json_decode($user->bookmarked_questions ?? '[]', true);
                $isBookmarked = in_array($question->id, $bookmarked);
            @endphp
            <button type="button" class="p-2 hover:bg-gray-100 rounded-lg" id="bookmarkBtnMobile"
                    data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                    onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                <svg class="w-5 h-5" viewBox="0 0 20 20" stroke="currentColor" id="bookmarkIconMobile">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"></path>
                </svg>
            </button>
        </div>

        <!-- Desktop: Normaler Header -->
        <div class="mb-2 hidden sm:block">
            <h2 class="text-lg font-bold mb-1">
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
        
        <div class="mb-2 text-xs text-gray-600 hidden sm:block">
            Fortschritt: {{ $progress }}/{{ $total }} gemeistert
            <div class="w-full bg-gray-200 rounded-full h-2 mt-0.5 mb-0.5">
                <div class="bg-yellow-400 h-2 rounded-full transition-all duration-300 shadow-lg" 
                     style="width: {{ $progressPercent ?? 0 }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
            </div>
            <span class="text-[10px] text-gray-500">{{ $progressPercent ?? 0 }}% Gesamt-Fortschritt (inkl. 1x richtig)</span>
        </div>
        
        <!-- Desktop: Bookmark Button -->
        <div class="mb-2 flex justify-end items-center hidden sm:flex">
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
                    data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                    onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                <svg class="w-4 h-4" viewBox="0 0 20 20" stroke="currentColor" id="bookmarkIcon">
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
            
            <div class="mb-2 sm:mb-3 p-2 sm:p-3 border rounded-lg bg-gray-50 shadow-sm sm:hover:shadow-md sm:transition-shadow sm:duration-300">
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
                            @endphp
                            <label class="inline-flex items-start p-1.5 sm:p-1.5 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                @if(isset($isCorrect))
                                    @if($isCorrectAnswer)
                                        <span class="mr-1.5 sm:mr-1.5 text-green-600 text-base sm:text-base">✅</span>
                                    @elseif($isUserAnswer)
                                        <span class="mr-1.5 sm:mr-1.5 text-red-600 text-base sm:text-base">❌</span>
                                    @else
                                        <span class="mr-1.5 sm:mr-1.5 text-gray-400 text-base sm:text-base">⚪</span>
                                    @endif
                                @endif
                                <input type="checkbox" name="answer[]" value="{{ $index }}"
                                    @if($isChecked) checked @endif
                                    @if(isset($isCorrect)) disabled @endif
                                    class="mr-1.5 sm:mr-1.5 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-0.5">
                                <span class="ml-1 sm:ml-1 text-xs sm:text-sm {{ isset($isCorrect) && $isChecked ? ($isCorrectAnswer ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold') : '' }}">
                                    {{ $answer['text'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" class="w-full text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg border-none cursor-pointer transition-all duration-300" 
                        style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);" 
                        onmouseover="if(!this.disabled) { this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)'; }" 
                        onmouseout="if(!this.disabled) { this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)'; }"
                        disabled>Antwort absenden</button>
                
            @elseif(isset($isCorrect) && $isCorrect)
                <a href="{{ route('practice.index') }}" class="w-full block text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg no-underline transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">Nächste Frage</a>
                
            @elseif(isset($isCorrect) && !$isCorrect)
                <a href="{{ route('practice.index', ['skip_id' => $question->id]) }}" class="w-full block text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg no-underline transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">Nächste Frage</a>
            @endif
        </form>
        </div>
        
        <!-- Gamification Popup (Richtig beantwortet) -->
        @php
            $showGamification = isset($isCorrect) && $isCorrect && $gamificationResult && isset($gamificationResult['points_awarded']);
            
            if ($showGamification) {
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
                
                $celebrationIndex = $question->id % count($celebrations);
                $celebration = $celebrations[$celebrationIndex];
                
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
            
            $showMastered = isset($questionProgress) && $questionProgress->consecutive_correct == 2;
            $showOneMore = isset($questionProgress) && $questionProgress->consecutive_correct == 1;
            $showCorrect = isset($isCorrect) && $isCorrect;
        @endphp
        
        @if($showCorrect)
            <div id="gamificationPopup" class="gamification-popup hidden">
                <div style="background: rgba(34, 197, 94, 0.95); border-radius: 16px; padding: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 30px rgba(34, 197, 94, 0.6); border: 2px solid rgba(34, 197, 94, 0.8);">
                    @if($showGamification)
                        <div class="flex items-center gap-2 mb-3">
                            <span style="font-size: 32px;">{{ $celebration['emoji'] }}</span>
                            <div>
                                <div style="font-size: 20px; font-weight: bold; color: white;">{{ $celebration['text'] }}</div>
                                <div style="font-size: 18px; color: white; font-weight: 600;">+{{ $pointsAwarded }} Punkte</div>
                            </div>
                        </div>
                        <div style="font-size: 14px; color: white; text-align: center;">{{ $reasonText }}</div>
                    @else
                        <div class="flex items-center gap-2 mb-3">
                            <span style="font-size: 32px;">🎉</span>
                            <div>
                                <div style="font-size: 20px; font-weight: bold; color: white;">Richtig!</div>
                                @if($gamificationResult && isset($gamificationResult['points_awarded']))
                                    <div style="font-size: 18px; color: white; font-weight: 600;">+{{ $gamificationResult['points_awarded'] }} Punkte</div>
                                @else
                                    <div style="font-size: 18px; color: white; font-weight: 600;">Korrekt beantwortet!</div>
                                @endif
                            </div>
                        </div>
                        <div style="font-size: 14px; color: white; text-align: center;">
                            @if(isset($questionProgress) && $questionProgress->consecutive_correct >= 1)
                                Frage {{ $questionProgress->consecutive_correct }}x richtig beantwortet
                            @else
                                Frage richtig beantwortet
                            @endif
                        </div>
                    @endif
                    
                    @if($showMastered)
                        <div style="margin-top: 12px; padding: 12px; background-color: rgba(255, 255, 255, 0.2); border: 2px solid rgba(255, 255, 255, 0.5); border-radius: 12px; text-align: center;">
                            <div style="font-size: 16px; font-weight: bold; color: white;">✅ Gemeistert!</div>
                        </div>
                    @elseif($showOneMore)
                        <div style="margin-top: 12px; padding: 12px; background-color: rgba(255, 255, 255, 0.2); border: 2px solid rgba(255, 255, 255, 0.5); border-radius: 12px; text-align: center;">
                            <div style="font-size: 16px; font-weight: bold; color: white;">💡 Noch <strong>1x richtig</strong> für gemeistert!</div>
                        </div>
                    @endif
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
                const container = document.getElementById('practiceContainer');
                
                if (isMobile) {
                    // Mobile: Die CSS Media Queries übernehmen das Styling
                    // Nur für den Fall dass CSS nicht greift
                    container.style.cssText = 'max-width: 100% !important; margin: 0 !important; padding: 0.75rem !important; border-radius: 0 !important; box-shadow: none !important; min-height: 100vh !important;';
                } else {
                    // Desktop: Mit mehr vertikalem Platz!
                    container.style.cssText = '';
                    container.className = 'max-w-xl mx-auto mt-0 sm:mt-8 p-3 sm:p-6 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300';
                }
            }
            
            // Setup on load and resize
            setupMobileLayout();
            window.addEventListener('resize', setupMobileLayout);
            
            // Show Gamification/Error Popups on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Show Gamification Popup
                const gamificationPopup = document.getElementById('gamificationPopup');
                if (gamificationPopup) {
                    setTimeout(() => {
                        gamificationPopup.classList.remove('hidden');
                        setTimeout(() => {
                            gamificationPopup.classList.add('show');
                        }, 10);
                        
                        // Hide after 3 seconds
                        setTimeout(() => {
                            gamificationPopup.classList.remove('show');
                            setTimeout(() => {
                                gamificationPopup.classList.add('hidden');
                            }, 300);
                        }, 3000);
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
                
                // Initialize Bookmark Icon Colors
                updateBookmarkIconState('bookmarkIconMobile', 'bookmarkBtnMobile', null);
                updateBookmarkIconState('bookmarkIcon', 'bookmarkBtn', 'bookmarkText');
            });
            
            // Helper function to update bookmark icon state
            function updateBookmarkIconState(iconId, btnId, textId) {
                const icon = document.getElementById(iconId);
                const btn = document.getElementById(btnId);
                const text = textId ? document.getElementById(textId) : null;
                
                if (!icon || !btn) return;
                
                const isBookmarked = btn.getAttribute('data-bookmarked') === 'true';
                
                if (isBookmarked) {
                    icon.style.setProperty('color', '#eab308', 'important'); // yellow-500
                    icon.setAttribute('fill', 'currentColor');
                    if (text) text.textContent = 'Gespeichert';
                } else {
                    icon.style.setProperty('color', '#9ca3af', 'important'); // gray-400
                    icon.setAttribute('fill', 'none');
                    if (text) text.textContent = 'Speichern';
                }
            }
            
            // Bookmark AJAX Function (unterstützt beide Buttons: Mobile & Desktop)
            function toggleBookmark(questionId, currentlyBookmarked) {
                const btn = document.getElementById('bookmarkBtn') || document.getElementById('bookmarkBtnMobile');
                const text = document.getElementById('bookmarkText'); // Kann null sein auf Mobile
                const icon = document.getElementById('bookmarkIcon') || document.getElementById('bookmarkIconMobile');
                
                // Loading State
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                if (text) text.textContent = 'Speichere...';
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
                        // Update data-bookmarked attribute
                        btn.setAttribute('data-bookmarked', data.is_bookmarked ? 'true' : 'false');
                        
                        // SOFORT Icon-Farbe und Fill setzen (KRITISCH für Mobile!)
                        const targetColor = data.is_bookmarked ? '#eab308' : '#9ca3af';
                        const targetFill = data.is_bookmarked ? 'currentColor' : 'none';
                        
                        // Entferne alle Klassen die die Farbe beeinflussen könnten
                        icon.className = '';
                        icon.classList.add('w-5', 'h-5');
                        if (!text) icon.classList.add('w-5', 'h-5'); // Mobile size
                        
                        // Setze Farbe und Fill DIREKT
                        icon.setAttribute('fill', targetFill);
                        icon.setAttribute('stroke', 'currentColor');
                        icon.style.cssText = `color: ${targetColor} !important; stroke: ${targetColor} !important;`;
                        
                        // Update Text und Attribute
                        if (text) text.textContent = data.is_bookmarked ? 'Gespeichert' : 'Speichern';
                        btn.setAttribute('title', data.is_bookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen');
                        btn.setAttribute('onclick', `toggleBookmark(${questionId}, ${data.is_bookmarked})`);
                        
                        // Kurzes Feedback mit Animation
                        btn.classList.add('animate-pulse');
                        
                        if (text) {
                            // Desktop: Zeige temporären Feedback-Text
                            const originalText = text.textContent;
                            text.textContent = data.is_bookmarked ? 'Gespeichert!' : 'Entfernt!';
                            setTimeout(() => {
                                text.textContent = originalText;
                                btn.classList.remove('animate-pulse');
                            }, 1500);
                        } else {
                            // Mobile: Nur Animation
                            setTimeout(() => {
                                btn.classList.remove('animate-pulse');
                            }, 1500);
                        }
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

