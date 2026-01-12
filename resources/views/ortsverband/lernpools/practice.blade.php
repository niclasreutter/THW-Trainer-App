@extends('layouts.app')
@section('title', $lernpool->name . ' - Lernpool üben')

@section('content')
@php
    // Hole Antwort-Details aus Session (falls vorhanden)
    $answerResult = session('answer_result');
    $hasAnswerResult = $answerResult && isset($answerResult['question_id']) && $question && $answerResult['question_id'] == $question->id;
    
    // Hole Gamification Result aus Session
    $gamificationResult = session('gamification_result');
    
    if ($hasAnswerResult) {
        $isCorrect = $answerResult['is_correct'];
        $userAnswer = collect($answerResult['user_answer']);
        $questionProgress = (object)['consecutive_correct' => $answerResult['question_progress']];
    } else {
        $isCorrect = null;
        $userAnswer = null;
        $questionProgress = null;
    }
@endphp
<style>
    /* CACHE BUST v9.1 - CSS FALLBACK FIX */
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
            padding-bottom: 180px !important;
            padding-bottom: calc(120px + env(safe-area-inset-bottom, 60px)) !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            max-width: 100% !important;
            width: 100% !important;
            min-height: 100vh !important;
        }
        
        .sm\:mt-4, .sm\:mt-8, .sm\:p-4, .sm\:p-6, 
        .sm\:rounded-lg, .sm\:shadow-lg {
            margin-top: 0 !important;
            padding: 0.75rem !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
    }
    
    @media (max-width: 640px) {
        /* MOBILE TOUCH OPTIMIERUNG */
        
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
            margin-bottom: 48px !important;
            margin-bottom: calc(24px + env(safe-area-inset-bottom, 24px)) !important;
        }
        
        button[type="submit"]:active,
        a.w-full:active {
            transform: translateY(2px) !important;
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2) !important;
        }
        
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
        
        #practiceContainer > form > div.mb-2 {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
        }
        
        #practiceContainer > form > div.mb-2 > div:first-child,
        #practiceContainer > form > div.mb-2 > div:nth-child(2),
        #practiceContainer > form > div.mb-2 > div:nth-child(3) {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        
        .bg-gray-200 {
            background-color: #e5e7eb !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }
        
        .bg-yellow-400 {
            box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 
                        0 0 20px rgba(251, 191, 36, 0.4), 
                        0 0 30px rgba(251, 191, 36, 0.2) !important;
        }
        
        .sm\:hidden .bg-yellow-400 {
            box-shadow: 0 0 8px rgba(251, 191, 36, 0.5), 
                        0 0 16px rgba(251, 191, 36, 0.3) !important;
        }
        
        .flex.flex-col.gap-1\.5 {
            gap: 14px !important;
        }
        
        .text-xs {
            font-size: 15px !important;
        }
        
        .font-bold {
            font-weight: 600 !important;
        }
        
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
        
        #practiceContainer > form::before {
            content: '' !important;
            flex-grow: 1 !important;
            order: 2 !important;
            min-height: 8px !important;
        }
        
        #practiceContainer button[type="submit"],
        #practiceContainer a.w-full {
            order: 4 !important;
            margin-top: 0 !important;
            margin-bottom: 4px !important;
        }
        
        #practiceContainer .mb-2.sm\:mb-3 > div:last-child {
            margin-top: 24px !important;
        }
        
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
    
    /* PWA Modus */
    @media (max-width: 640px) and (display-mode: standalone) {
        #practiceContainer {
            padding-bottom: 120px !important;
            padding-bottom: calc(80px + env(safe-area-inset-bottom, 40px)) !important;
        }
        
        #practiceContainer button[type="submit"],
        #practiceContainer a.w-full {
            margin-bottom: 60px !important;
            margin-bottom: calc(40px + env(safe-area-inset-bottom, 20px)) !important;
        }
        
        #practiceContainer > form {
            min-height: calc(100vh - 140px) !important;
        }
    }
    
    /* Desktop */
    @media (min-width: 641px) {
        nav, footer {
            display: block !important;
        }
        
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
        
        #practiceContainer {
            max-width: 950px !important;
            width: 95% !important;
            margin: 0 auto 1rem auto !important;
            padding: 2.5rem !important;
            background: white !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }
        
        #practiceContainer:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08) !important;
        }
        
        #practiceContainer input[type="checkbox"] {
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
        
        #practiceContainer input[type="checkbox"]:checked {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        #practiceContainer input[type="checkbox"]:checked::after {
            content: '✓' !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            color: white !important;
            font-size: 16px !important;
            font-weight: bold !important;
        }
        
        #practiceContainer label.inline-flex {
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
        
        #practiceContainer label.inline-flex:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            border-color: #3b82f6 !important;
            background: #f0f9ff !important;
        }
        
        #practiceContainer label.inline-flex:active {
            transform: scale(0.98) !important;
        }
        
        #practiceContainer button[type="submit"],
        #practiceContainer a.w-full {
            padding: 18px 24px !important;
            font-size: 18px !important;
            min-height: 60px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            border-radius: 14px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15), 0 2px 4px rgba(30, 58, 138, 0.1) !important;
        }
        
        #practiceContainer button[type="submit"]:hover,
        #practiceContainer a.w-full:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.25) !important;
        }
        
        #practiceContainer button[type="submit"]:active,
        #practiceContainer a.w-full:active {
            transform: translateY(1px) !important;
        }
        
        #practiceContainer h2 {
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
            font-weight: 700 !important;
            color: #1e3a8a !important;
        }
        
        #practiceContainer .bg-gray-200 {
            background-color: #e5e7eb !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05) !important;
            border-radius: 10px !important;
            height: 12px !important;
        }
        
        #practiceContainer .bg-yellow-400 {
            border-radius: 10px !important;
            box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 
                        0 0 20px rgba(251, 191, 36, 0.4), 
                        0 0 30px rgba(251, 191, 36, 0.2) !important;
        }
        
        #practiceContainer > form > div.mb-2 {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        
        #practiceContainer .flex.flex-col.gap-1\.5 {
            gap: 12px !important;
        }
        
        #practiceContainer .text-xs {
            font-size: 0.95rem !important;
        }
        
        #practiceContainer .text-sm {
            font-size: 1rem !important;
        }
        
        .gamification-popup {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            width: 380px !important;
            max-width: 90vw !important;
            z-index: 9999 !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transform: translateX(100%) !important;
            transition: all 0.3s ease-out !important;
        }
        
        .gamification-popup.show {
            opacity: 1 !important;
            transform: translateX(0) !important;
            pointer-events: auto !important;
        }
        
        .error-popup {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            width: 380px !important;
            max-width: 90vw !important;
            z-index: 9999 !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transform: translateX(100%) !important;
            transition: all 0.3s ease-out !important;
        }
        
        .error-popup.show {
            opacity: 1 !important;
            transform: translateX(0) !important;
            pointer-events: auto !important;
        }
    }
</style>

<!-- Practice Container -->
<div class="max-w-xl mx-auto mt-0 sm:mt-4 p-3 sm:p-4 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300" 
     id="practiceContainer">

    @if($question)
        <!-- Mobile: Kompakter Header -->
        <div class="sm:hidden mb-2 flex items-center justify-between p-2 bg-white border-b">
            <a href="{{ route('ortsverband.show', $ortsverband) }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1 mx-2">
                <div class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                    <span>📚 {{ Str::limit($lernpool->name, 20) }}</span>
                    <span class="text-gray-500">{{ $progress }}/{{ $total }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                    <div class="bg-yellow-400 h-1.5 rounded-full transition-all" style="width: {{ $progressPercent ?? 0 }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Desktop: Normaler Header -->
        <div class="mb-3 hidden sm:block">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-bold mb-0">📚 {{ $lernpool->name }}</h2>
            </div>
        </div>
        
        <div class="mb-2 text-xs text-gray-600 hidden sm:block">
            Fortschritt: {{ $progress }}/{{ $total }} gemeistert
            <div class="w-full bg-gray-200 rounded-full h-2 mt-0.5 mb-0.5">
                <div class="bg-yellow-400 h-2 rounded-full transition-all duration-300 shadow-lg" 
                     style="width: {{ $progressPercent ?? 0 }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
            </div>
            <span class="text-[10px] text-gray-500">{{ $progressPercent ?? 0 }}% Gesamt-Fortschritt (inkl. 1x richtig)</span>
        </div>
        
        <form method="POST" action="{{ route('ortsverband.lernpools.answer', [$ortsverband, $lernpool]) }}" id="practiceForm">
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
                if (isset($isCorrect) && isset($answerResult['answer_mapping'])) {
                    $mappingArray = $answerResult['answer_mapping'];
                    
                    // Sortiere $answers entsprechend dem Mapping
                    $answers = [];
                    foreach ($mappingArray as $position => $letter) {
                        foreach ($answersOriginal as $ans) {
                            if ($ans['letter'] === strtoupper($letter)) {
                                $answers[$position] = $ans;
                                break;
                            }
                        }
                    }
                    ksort($answers);
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
                $solution = collect(explode(',', $question->loesung))->map(fn($s) => strtoupper(trim($s)));
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
                                
                                // Farbe bestimmen
                                if (isset($isCorrect)) {
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
                                } else {
                                    $bgColor = '';
                                    $borderColor = '';
                                    $textColor = '';
                                    $icon = '';
                                }
                            @endphp
                            
                            @if(isset($isCorrect))
                                <!-- Lösung-Anzeige -->
                                <div class="flex items-start p-3 rounded-lg border-2 {{ $bgColor }} {{ $borderColor }}">
                                    <div class="flex items-center min-w-0 flex-1">
                                        @if($isUserAnswer)
                                            <span class="w-5 h-5 mr-3 flex-shrink-0 text-lg">{{ $icon }}</span>
                                        @else
                                            <span class="w-5 h-5 mr-3 flex-shrink-0"></span>
                                        @endif
                                        <span class="text-sm {{ $textColor }}">
                                            <span class="font-bold">{{ $originalLetter }}:</span> {{ $answer['text'] }}
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
                        <!-- Zusammenfassung -->
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
                <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $lernpool]) }}" class="w-full block text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg no-underline transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);"
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">
                    Nächste Frage
                </a>
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
                    container.style.cssText = 'max-width: 100% !important; margin: 0 !important; padding: 0.75rem !important; border-radius: 0 !important; box-shadow: none !important; min-height: 100vh !important;';
                } else {
                    container.style.cssText = '';
                    container.className = 'max-w-xl mx-auto mt-0 sm:mt-8 p-3 sm:p-6 bg-white sm:rounded-lg sm:shadow-lg sm:hover:shadow-xl sm:transition-shadow sm:duration-300';
                }
            }
            
            setupMobileLayout();
            window.addEventListener('resize', setupMobileLayout);
            
            document.addEventListener('DOMContentLoaded', function() {
                const gamificationPopup = document.getElementById('gamificationPopup');
                if (gamificationPopup) {
                    setTimeout(() => {
                        gamificationPopup.classList.remove('hidden');
                        setTimeout(() => {
                            gamificationPopup.classList.add('show');
                        }, 10);
                        
                        setTimeout(() => {
                            gamificationPopup.classList.remove('show');
                            setTimeout(() => {
                                gamificationPopup.classList.add('hidden');
                            }, 300);
                        }, 3000);
                    }, 100);
                }
                
                const errorPopup = document.getElementById('errorPopup');
                if (errorPopup) {
                    setTimeout(() => {
                        errorPopup.classList.remove('hidden');
                        setTimeout(() => {
                            errorPopup.classList.add('show');
                        }, 10);
                        
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
        <div class="text-center text-lg mb-4">Du hast alle Fragen in diesem Lernpool bearbeitet! 🎉</div>
        <div class="text-center">
            <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $lernpool]) }}" class="inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300 mr-4">Wiederholen</a>
            <a href="{{ route('ortsverband.show', $ortsverband) }}" class="inline-block bg-gray-600 text-white px-6 py-2 rounded font-bold hover:bg-gray-700 hover:shadow-lg hover:scale-105 transition-all duration-300">Zurück zum Ortsverband</a>
        </div>
    @endif
</div>
@endsection
