@extends('layouts.app')
@section('title', 'THW Prüfungssimulation - 40 Fragen in 30 Minuten')
@section('description', 'THW Prüfungssimulation: Teste dein Wissen mit 40 zufälligen Fragen in 30 Minuten. Realistische Prüfungsbedingungen und sofortige Auswertung. Übe jetzt kostenlos!')

@push('styles')
<style>
    /* Exam Page - Dark Mode Glassmorphism */

    /* Mobile: Navigation & Footer ausblenden */
    @media (max-width: 640px) {
        /* Hide ALL navigation and header elements */
        footer, nav, header {
            display: none !important;
        }

        /* Hide all nav elements specifically */
        nav {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 0 !important;
        }

        nav.bottom-nav-glass {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 0 !important;
            position: absolute !important;
            bottom: auto !important;
        }

        nav.lg\\:hidden {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 0 !important;
        }

        /* Hide mobile header */
        header {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 0 !important;
        }

        /* Fix the main layout container */
        body > div.flex-1 {
            min-height: auto !important;
            height: 100dvh !important;
        }

        main {
            padding: 0 !important;
            padding-bottom: 0 !important;
            height: auto !important;
            position: static !important;
        }
    }

    /* Exam Container */
    #examContainer {
        max-width: 900px;
        margin: 0 auto;
    }

    @media (max-width: 640px) {
        #examContainer {
            padding: 0 !important;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }
    }

    /* Question Card */
    .exam-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 1.5rem;
    }

    @media (max-width: 640px) {
        .exam-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-radius: 0;
            border: none;
            height: auto;
            min-height: auto;
            padding-bottom: 140px; /* Platz für fixed buttons unten */
        }
    }

    @media (min-width: 641px) {
        .exam-card {
            padding: 2rem;
            border-radius: 8px 24px 8px 24px;
        }
    }

    /* Mobile Header für Exam */
    .exam-mobile-header {
        display: none;
    }

    @media (max-width: 640px) {
        .exam-mobile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            padding-top: calc(0.75rem + env(safe-area-inset-top, 0px));
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            margin: -1.5rem -1.5rem 1rem -1.5rem;
            flex-shrink: 0;
        }
    }

    /* Desktop Header ausblenden auf Mobile */
    @media (max-width: 640px) {
        .exam-desktop-header {
            display: none !important;
        }
    }

    /* Timer */
    .exam-timer {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(245, 158, 11, 0.1));
        border: 1px solid rgba(251, 191, 36, 0.3);
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.125rem;
        color: var(--gold);
        font-variant-numeric: tabular-nums;
    }

    .exam-timer.warning {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
        animation: timerPulse 1s infinite;
    }

    @keyframes timerPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Answer Option */
    .exam-answer {
        display: flex;
        align-items: flex-start;
        padding: 1rem 1.25rem;
        background: rgba(255, 255, 255, 0.02);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        gap: 0.875rem;
    }

    .exam-answer:hover {
        background: rgba(0, 51, 127, 0.1);
        border-color: rgba(0, 51, 127, 0.4);
    }

    .exam-answer.selected {
        background: rgba(0, 51, 127, 0.15);
        border-color: var(--thw-blue);
    }

    /* Exam Checkbox */
    .exam-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        min-width: 24px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.05);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        margin-top: 2px;
    }

    .exam-checkbox:checked {
        background: var(--thw-blue);
        border-color: var(--thw-blue);
    }

    .exam-checkbox:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    /* Question Bubbles Grid */
    .bubbles-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 0.5rem;
    }

    @media (max-width: 640px) {
        .bubbles-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }

    .question-bubble {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
    }

    .question-bubble:hover {
        transform: scale(1.1);
        border-color: rgba(251, 191, 36, 0.5);
    }

    .question-bubble.answered {
        background: rgba(34, 197, 94, 0.2);
        border-color: rgba(34, 197, 94, 0.5);
        color: #4ade80;
    }

    .question-bubble.marked {
        background: rgba(251, 191, 36, 0.2);
        border-color: rgba(251, 191, 36, 0.5);
        color: var(--gold);
    }

    .question-bubble.current {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.3);
    }

    /* Mark Button */
    .mark-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        font-size: 0.75rem;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .mark-btn:hover {
        background: rgba(251, 191, 36, 0.1);
        border-color: rgba(251, 191, 36, 0.3);
    }

    .mark-btn.active {
        background: rgba(251, 191, 36, 0.2);
        border-color: var(--gold);
        color: var(--gold);
    }

    /* Result States */
    .result-correct {
        background: rgba(34, 197, 94, 0.15) !important;
        border-color: rgba(34, 197, 94, 0.5) !important;
    }

    .result-correct-missed {
        background: rgba(34, 197, 94, 0.08) !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
    }

    .result-wrong {
        background: rgba(239, 68, 68, 0.15) !important;
        border-color: rgba(239, 68, 68, 0.5) !important;
    }

    .result-neutral {
        background: rgba(255, 255, 255, 0.02) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
        opacity: 0.6;
    }

    /* Progress Result Bar */
    .result-bar {
        position: relative;
        height: 1.25rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        overflow: visible;
    }

    .result-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, var(--gold), var(--gold-dark));
        transition: width 0.5s ease;
    }

    .result-bar-marker {
        position: absolute;
        top: 0;
        left: 80%;
        height: 100%;
        width: 3px;
        background: var(--error);
        border-radius: 2px;
    }

    .result-bar-label {
        position: absolute;
        top: -1.5rem;
        left: 80%;
        transform: translateX(-50%);
        font-size: 0.625rem;
        font-weight: 700;
        color: var(--error);
        background: rgba(239, 68, 68, 0.2);
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
    }

    /* ========== FIXED NAVIGATION BUTTONS (Mobile) ========== */
    .exam-nav-wrapper {
        margin-top: auto;
        flex-shrink: 0;
        box-sizing: border-box;
    }

    @media (max-width: 640px) {
        .exam-nav-wrapper {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 99999 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom, 0px)) 1rem !important;
            background-color: rgba(15, 23, 42, 0.98) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    }

    /* Mobile: Fragenübersicht als Fullscreen Overlay */
    @media (max-width: 640px) {
        #overview-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 9999 !important;
            background: rgba(15, 23, 42, 0.98) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 1rem !important;
            padding-top: calc(1rem + env(safe-area-inset-top, 0px)) !important;
            padding-bottom: calc(140px + env(safe-area-inset-bottom, 0px)) !important;
            overflow-y: auto !important;
            margin: 0 !important;
        }

        #overview-container.hidden {
            display: none !important;
        }

        /* Close Button für Mobile Overlay */
        .overview-close-btn {
            display: flex !important;
            position: absolute;
            top: calc(0.75rem + env(safe-area-inset-top, 0px));
            right: 1rem;
            padding: 0.5rem;
            color: var(--text-secondary);
            z-index: 10;
        }

        /* Desktop: Question Overview Toggle verstecken */
        .question-overview-toggle {
            display: none !important;
        }
    }

    @media (min-width: 641px) {
        .overview-close-btn {
            display: none !important;
        }
    }

    /* Mobile Progress Stats Bar */
    .exam-mobile-stats {
        display: none;
    }

    @media (max-width: 640px) {
        .exam-mobile-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            padding: 0.5rem 0;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
        }

        .exam-mobile-stats .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .exam-mobile-stats .stat-answered {
            color: #4ade80;
        }

        .exam-mobile-stats .stat-open {
            color: var(--text-secondary);
        }

        .exam-mobile-stats .stat-marked {
            color: var(--gold);
        }
    }

    /* ============================================
       LIGHT MODE OVERRIDES
       ============================================ */

    /* Exam Card in Light Mode */
    html.light-mode .exam-card {
        background: #ffffff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border: 1px solid rgba(0, 51, 127, 0.12) !important;
        box-shadow: 0 4px 20px rgba(0, 51, 127, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }

    /* Mobile Header in Light Mode */
    html.light-mode .exam-mobile-header {
        background: rgba(0, 51, 127, 0.03) !important;
        border-bottom: 1px solid rgba(0, 51, 127, 0.08) !important;
    }

    /* Timer in Light Mode */
    html.light-mode .exam-timer {
        background: linear-gradient(135deg, rgba(217, 119, 6, 0.12), rgba(180, 83, 9, 0.08)) !important;
        border: 1px solid rgba(217, 119, 6, 0.25) !important;
        color: #b45309 !important;
    }

    html.light-mode .exam-timer.warning {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.08)) !important;
        border-color: rgba(239, 68, 68, 0.3) !important;
        color: #dc2626 !important;
    }

    /* Answer Options in Light Mode */
    html.light-mode .exam-answer {
        background: #f8fafc !important;
        border: 2px solid rgba(0, 51, 127, 0.12) !important;
    }

    html.light-mode .exam-answer:hover {
        background: rgba(0, 51, 127, 0.06) !important;
        border-color: rgba(0, 51, 127, 0.3) !important;
    }

    html.light-mode .exam-answer.selected {
        background: rgba(0, 51, 127, 0.1) !important;
        border-color: var(--thw-blue) !important;
    }

    /* Checkbox in Light Mode */
    html.light-mode .exam-checkbox {
        border: 2px solid rgba(0, 51, 127, 0.25) !important;
        background: #ffffff !important;
    }

    html.light-mode .exam-checkbox:checked {
        background: var(--thw-blue) !important;
        border-color: var(--thw-blue) !important;
    }

    /* Question Bubbles in Light Mode */
    html.light-mode .question-bubble {
        background: #f1f5f9 !important;
        border: 2px solid rgba(0, 51, 127, 0.12) !important;
        color: #475569 !important;
    }

    html.light-mode .question-bubble:hover {
        border-color: rgba(217, 119, 6, 0.4) !important;
    }

    html.light-mode .question-bubble.answered {
        background: rgba(34, 197, 94, 0.15) !important;
        border-color: rgba(34, 197, 94, 0.4) !important;
        color: #16a34a !important;
    }

    html.light-mode .question-bubble.marked {
        background: rgba(217, 119, 6, 0.12) !important;
        border-color: rgba(217, 119, 6, 0.4) !important;
        color: #b45309 !important;
    }

    html.light-mode .question-bubble.current {
        border-color: #d97706 !important;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2) !important;
    }

    /* Mark Button in Light Mode */
    html.light-mode .mark-btn {
        background: rgba(0, 51, 127, 0.04) !important;
        border: 1px solid rgba(0, 51, 127, 0.1) !important;
        color: #475569 !important;
    }

    html.light-mode .mark-btn:hover {
        background: rgba(217, 119, 6, 0.08) !important;
        border-color: rgba(217, 119, 6, 0.25) !important;
    }

    html.light-mode .mark-btn.active {
        background: rgba(217, 119, 6, 0.15) !important;
        border-color: #d97706 !important;
        color: #b45309 !important;
    }

    /* Result States in Light Mode */
    html.light-mode .result-correct {
        background: rgba(34, 197, 94, 0.12) !important;
        border-color: rgba(34, 197, 94, 0.4) !important;
    }

    html.light-mode .result-correct-missed {
        background: rgba(34, 197, 94, 0.06) !important;
        border-color: rgba(34, 197, 94, 0.25) !important;
    }

    html.light-mode .result-wrong {
        background: rgba(239, 68, 68, 0.12) !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
    }

    html.light-mode .result-neutral {
        background: #f1f5f9 !important;
        border-color: rgba(0, 51, 127, 0.08) !important;
        opacity: 0.7;
    }

    /* Progress Bar in Light Mode */
    html.light-mode .progress-glass {
        background: rgba(0, 51, 127, 0.08) !important;
    }

    html.light-mode .progress-fill-gold {
        background: linear-gradient(90deg, #d97706, #b45309) !important;
    }

    /* Fixed Navigation in Light Mode */
    html.light-mode .exam-nav-wrapper {
        background-color: rgba(255, 255, 255, 0.98) !important;
        border-top: 1px solid rgba(0, 51, 127, 0.1) !important;
    }

    /* Text colors in Light Mode */
    html.light-mode .text-dark-primary {
        color: #1e293b !important;
    }

    html.light-mode .text-dark-secondary {
        color: #475569 !important;
    }

    html.light-mode .text-dark-muted {
        color: #64748b !important;
    }

    html.light-mode .text-gold {
        color: #d97706 !important;
    }

    /* Glass-subtle in Light Mode */
    html.light-mode .glass-subtle {
        background: #f8fafc !important;
        border: 1px solid rgba(0, 51, 127, 0.08) !important;
    }

    /* Border colors in Light Mode */
    html.light-mode .border-glass-subtle {
        border-color: rgba(0, 51, 127, 0.08) !important;
    }

    /* Overview Legend in Light Mode */
    html.light-mode #overview-container .text-dark-muted {
        color: #64748b !important;
    }

    html.light-mode #overview-container .bg-success\/30 {
        background: rgba(34, 197, 94, 0.15) !important;
    }

    html.light-mode #overview-container .border-success\/50 {
        border-color: rgba(34, 197, 94, 0.35) !important;
    }

    html.light-mode #overview-container .bg-white\/5 {
        background: #f1f5f9 !important;
    }

    html.light-mode #overview-container .border-white\/10 {
        border-color: rgba(0, 51, 127, 0.1) !important;
    }

    html.light-mode #overview-container .bg-gold\/30 {
        background: rgba(217, 119, 6, 0.15) !important;
    }

    html.light-mode #overview-container .border-gold\/50 {
        border-color: rgba(217, 119, 6, 0.35) !important;
    }

    /* Light Mode: Overlay Background */
    @media (max-width: 640px) {
        html.light-mode #overview-container {
            background: rgba(255, 255, 255, 0.98) !important;
        }

        html.light-mode .overview-close-btn {
            color: #475569 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6 sm:py-8" id="examContainer">
    @if(!isset($submitted))
        <div class="exam-card">
            <!-- Mobile Header -->
            <div class="exam-mobile-header">
                <a href="{{ route('dashboard') }}" class="p-2 text-dark-secondary hover:text-gold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="exam-timer" id="exam-timer-mobile">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="timer-display-mobile">30:00</span>
                </div>

                <button type="button" onclick="toggleOverview()" class="p-2 text-dark-secondary hover:text-gold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Desktop Header -->
            <div class="exam-desktop-header flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-dark-primary">THW Prüfung</h1>
                    <p class="text-dark-muted text-sm">40 Fragen in 30 Minuten</p>
                </div>
                <div class="exam-timer" id="exam-timer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="timer-display">30:00</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2 text-sm">
                    <span class="text-dark-secondary">Fortschritt</span>
                    <span class="text-dark-muted" id="progress-text">0/40 beantwortet</span>
                </div>
                <div class="progress-glass h-2">
                    <div class="progress-fill-gold" id="progress-bar" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Question Form -->
            <form id="exam-form" method="POST" action="{{ route('exam.submit') }}">
                @csrf

                @foreach($fragen as $index => $frage)
                    <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">

                    @php
                        $answersOriginal = [
                            ['letter' => 'A', 'text' => $frage->antwort_a],
                            ['letter' => 'B', 'text' => $frage->antwort_b],
                            ['letter' => 'C', 'text' => $frage->antwort_c],
                        ];

                        $answers = $answersOriginal;
                        shuffle($answers);

                        $mappingArray = [];
                        foreach ($answers as $ansIndex => $answer) {
                            $mappingArray[$ansIndex] = $answer['letter'];
                        }

                        $mappingJson = json_encode($mappingArray);
                    @endphp

                    <input type="hidden" name="answer_mappings[{{ $index }}]" value="{{ $mappingJson }}">

                    <div class="question-slide" data-question="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                        <!-- Question Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="text-xs text-dark-muted mb-2">
                                    Frage {{ $index + 1 }} von 40
                                    <span class="mx-1 opacity-50">|</span>
                                    LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}
                                </div>
                                <p class="text-dark-primary text-base sm:text-lg leading-relaxed">
                                    {{ $frage->frage }}
                                </p>
                            </div>
                        </div>

                        <!-- Mark Button -->
                        <button type="button"
                                onclick="toggleMark({{ $index }})"
                                id="mark-btn-{{ $index }}"
                                class="mark-btn mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span id="mark-text-{{ $index }}">Markieren</span>
                        </button>

                        <!-- Answer Options -->
                        <div class="flex flex-col gap-3 mb-6">
                            @foreach($answers as $ansIndex => $answer)
                                <label class="exam-answer" onclick="updateAnswerStyle(this, {{ $index }})">
                                    <input type="checkbox"
                                           name="answer[{{ $index }}][]"
                                           value="{{ $ansIndex }}"
                                           onchange="updateAnswerStatus({{ $index }})"
                                           class="exam-checkbox">
                                    <span class="text-dark-primary text-sm sm:text-base flex-1">
                                        {{ $answer['text'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Navigation -->
                <div class="exam-nav-wrapper">
                    <!-- Mobile Stats -->
                    <div class="exam-mobile-stats">
                        <div class="stat-item stat-answered">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span id="mobile-answered-count">0</span>
                        </div>
                        <div class="stat-item stat-open">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="mobile-open-count">40</span>
                        </div>
                        <div class="stat-item stat-marked">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span id="mobile-marked-count">0</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-3 sm:pt-4 border-t border-glass-subtle">
                        <button type="button"
                                onclick="previousQuestion()"
                                id="prev-btn"
                                class="btn-ghost px-4 py-2"
                                disabled>
                            Zurück
                        </button>

                        <span class="text-dark-muted text-sm font-medium" id="current-question">1/40</span>

                        <button type="button"
                                onclick="nextQuestion()"
                                id="next-btn"
                                class="btn-secondary px-4 py-2">
                            Weiter
                        </button>
                    </div>
                </div>

                <!-- Question Overview Toggle (Desktop only) -->
                <div class="question-overview-toggle mt-6 pt-4 border-t border-glass-subtle">
                    <button type="button"
                            onclick="toggleOverview()"
                            class="w-full flex items-center justify-between text-sm text-dark-secondary hover:text-gold transition-colors p-2 rounded-lg hover:bg-white/5">
                        <span>Fragenübersicht</span>
                        <svg id="overview-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Fragenübersicht Overlay (Mobile: Fullscreen, Desktop: Inline) -->
            <div id="overview-container" class="hidden mt-4">
                <!-- Mobile Close Button -->
                <button type="button" onclick="toggleOverview()" class="overview-close-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Title (Mobile) -->
                <h2 class="text-lg font-bold text-dark-primary mb-4 sm:hidden">Fragenübersicht</h2>

                <!-- Legend -->
                <div class="flex flex-wrap gap-4 text-xs mb-4">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-success/30 border border-success/50"></div>
                        <span class="text-dark-muted">Beantwortet</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-white/5 border border-white/10"></div>
                        <span class="text-dark-muted">Offen</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-gold/30 border border-gold/50"></div>
                        <span class="text-dark-muted">Markiert</span>
                    </div>
                </div>

                <!-- Bubbles Grid -->
                <div class="bubbles-grid">
                    @for($i = 0; $i < 40; $i++)
                        <button type="button"
                                onclick="goToQuestion({{ $i }}); toggleOverview();"
                                id="bubble-{{ $i }}"
                                class="question-bubble {{ $i === 0 ? 'current' : '' }}">
                            {{ $i + 1 }}
                        </button>
                    @endfor
                </div>
            </div>
        </div>

        <script>
            // State Management
            let currentQuestion = 0;
            const totalQuestions = {{ count($fragen) }};
            let answers = new Array(totalQuestions).fill(false);
            let marked = new Array(totalQuestions).fill(false);
            let timeLeft = 30 * 60;

            // Timer
            const timerEl = document.getElementById('exam-timer');
            const timerElMobile = document.getElementById('exam-timer-mobile');
            const timerDisplay = document.getElementById('timer-display');
            const timerDisplayMobile = document.getElementById('timer-display-mobile');

            function updateTimer() {
                const min = Math.floor(timeLeft / 60).toString().padStart(2, '0');
                const sec = (timeLeft % 60).toString().padStart(2, '0');
                const timeString = `${min}:${sec}`;

                if (timerDisplay) timerDisplay.textContent = timeString;
                if (timerDisplayMobile) timerDisplayMobile.textContent = timeString;

                // Warning at 5 minutes
                if (timeLeft <= 300) {
                    if (timerEl) timerEl.classList.add('warning');
                    if (timerElMobile) timerElMobile.classList.add('warning');
                }

                if (timeLeft <= 0) {
                    if (timerDisplay) timerDisplay.textContent = '00:00';
                    if (timerDisplayMobile) timerDisplayMobile.textContent = '00:00';
                    submitExam();
                } else {
                    timeLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }
            updateTimer();

            // Mobile Stats Update
            function updateMobileStats() {
                const answeredCount = answers.filter(a => a).length;
                const openCount = totalQuestions - answeredCount;
                const markedCount = marked.filter(m => m).length;

                const answeredEl = document.getElementById('mobile-answered-count');
                const openEl = document.getElementById('mobile-open-count');
                const markedEl = document.getElementById('mobile-marked-count');

                if (answeredEl) answeredEl.textContent = answeredCount;
                if (openEl) openEl.textContent = openCount;
                if (markedEl) markedEl.textContent = markedCount;
            }

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

                // Update next button text
                const nextBtn = document.getElementById('next-btn');
                if (index === totalQuestions - 1) {
                    nextBtn.textContent = 'Abschließen';
                } else {
                    nextBtn.textContent = 'Weiter';
                }

                // Update bubbles
                document.querySelectorAll('.question-bubble').forEach((bubble, i) => {
                    bubble.classList.remove('current');
                    if (i === index) {
                        bubble.classList.add('current');
                    }
                });

                window.scrollTo(0, 0);
            }

            function nextQuestion() {
                if (currentQuestion < totalQuestions - 1) {
                    showQuestion(currentQuestion + 1);
                } else {
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

            // Answer handling
            function updateAnswerStyle(label, questionIndex) {
                const checkbox = label.querySelector('input[type="checkbox"]');
                setTimeout(() => {
                    if (checkbox.checked) {
                        label.classList.add('selected');
                    } else {
                        label.classList.remove('selected');
                    }
                }, 0);
            }

            function updateAnswerStatus(index) {
                const checkboxes = document.querySelectorAll(`input[name="answer[${index}][]"]`);
                const isAnswered = Array.from(checkboxes).some(cb => cb.checked);
                answers[index] = isAnswered;

                const bubble = document.getElementById(`bubble-${index}`);
                if (isAnswered && !marked[index]) {
                    bubble.classList.add('answered');
                    bubble.classList.remove('marked');
                } else if (!isAnswered && !marked[index]) {
                    bubble.classList.remove('answered');
                }

                // Update label styles
                checkboxes.forEach(cb => {
                    const label = cb.closest('.exam-answer');
                    if (cb.checked) {
                        label.classList.add('selected');
                    } else {
                        label.classList.remove('selected');
                    }
                });

                updateProgress();
            }

            // Marking
            function toggleMark(index) {
                marked[index] = !marked[index];
                const bubble = document.getElementById(`bubble-${index}`);
                const btn = document.getElementById(`mark-btn-${index}`);
                const text = document.getElementById(`mark-text-${index}`);

                if (marked[index]) {
                    bubble.classList.add('marked');
                    bubble.classList.remove('answered');
                    btn.classList.add('active');
                    text.textContent = 'Markiert';
                } else {
                    bubble.classList.remove('marked');
                    btn.classList.remove('active');
                    text.textContent = 'Markieren';
                    if (answers[index]) {
                        bubble.classList.add('answered');
                    }
                }

                // Update mobile stats
                updateMobileStats();
            }

            // Progress
            function updateProgress() {
                const answeredCount = answers.filter(a => a).length;
                const percent = (answeredCount / totalQuestions) * 100;

                document.getElementById('progress-text').textContent = `${answeredCount}/40 beantwortet`;
                document.getElementById('progress-bar').style.width = `${percent}%`;

                // Update mobile stats
                updateMobileStats();
            }

            // Overview toggle
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

            // Submit overview
            function showSubmitOverview() {
                const unanswered = answers.reduce((acc, answered, index) => {
                    if (!answered) acc.push(index + 1);
                    return acc;
                }, []);

                const markedCount = marked.filter(m => m).length;
                const answeredCount = answers.filter(a => a).length;

                // Hide all questions
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.style.display = 'none';
                });

                // Create overview
                let message = `<div class="question-slide" id="submit-overview" style="display: block;">`;
                message += `<div class="text-center py-8">`;
                message += `<h2 class="text-2xl font-bold text-dark-primary mb-4">Prüfung abschließen?</h2>`;
                message += `<div class="glass-subtle p-6 rounded-xl mb-6 text-left max-w-md mx-auto">`;
                message += `<p class="text-dark-primary text-lg mb-2"><span class="text-gold font-bold">${answeredCount}</span> von 40 Fragen beantwortet</p>`;

                if (unanswered.length > 0) {
                    message += `<p class="text-error font-medium mb-2">${unanswered.length} Fragen noch offen</p>`;
                    message += `<p class="text-dark-muted text-xs">Fragen: ${unanswered.join(', ')}</p>`;
                }

                if (markedCount > 0) {
                    message += `<p class="text-gold font-medium mt-3">${markedCount} Fragen markiert</p>`;
                }

                message += `</div>`;
                message += `<div class="flex flex-col gap-3 max-w-sm mx-auto">`;
                message += `<button type="button" onclick="backToPrüfung()" class="btn-ghost py-3">Zurück zur Prüfung</button>`;
                message += `<button type="button" onclick="confirmSubmit()" class="btn-primary py-3">Prüfung abgeben</button>`;
                message += `</div>`;
                message += `</div>`;
                message += `</div>`;

                // Add to form
                const form = document.getElementById('exam-form');
                const existingOverview = document.getElementById('submit-overview');
                if (existingOverview) {
                    existingOverview.remove();
                }
                form.insertAdjacentHTML('beforeend', message);

                // Hide navigation
                document.querySelector('.border-t.border-glass-subtle').style.display = 'none';
            }

            function backToPrüfung() {
                const overview = document.getElementById('submit-overview');
                if (overview) {
                    overview.remove();
                }
                document.querySelector('.border-t.border-glass-subtle').style.display = 'flex';
                showQuestion(currentQuestion);
            }

            function confirmSubmit() {
                document.getElementById('exam-form').submit();
            }

            // Initial state
            updateProgress();
            updateMobileStats();
        </script>

    @else
        <!-- Results View -->
        <div class="exam-card">
            <!-- Result Header -->
            <div class="text-center mb-8">
                @if(isset($gamification_result) && $gamification_result)
                    <div class="glass-success p-4 rounded-xl mb-6 inline-block">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🎉</span>
                            <div class="text-left">
                                <div class="text-lg font-bold text-dark-primary">
                                    +{{ $gamification_result['points_awarded'] }} Punkte!
                                    @if($gamification_result['level_up'])
                                        Level UP!
                                    @endif
                                </div>
                                <div class="text-sm text-dark-secondary">{{ $gamification_result['reason'] }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-6xl mb-4">{{ $passed ? '🎉' : '😔' }}</div>

                <div class="inline-block px-6 py-3 rounded-xl mb-4 {{ $passed ? 'glass-success' : 'glass-error' }}">
                    <span class="text-3xl font-bold text-dark-primary">
                        {{ round(($correctCount/$total)*100) }}%
                    </span>
                    <span class="text-lg font-medium text-dark-secondary ml-2">
                        {{ $passed ? 'Bestanden' : 'Nicht Bestanden' }}
                    </span>
                </div>

                @php
                    $percent = $total > 0 ? round($correctCount / $total * 100) : 0;
                @endphp

                <!-- Result Progress Bar -->
                <div class="max-w-md mx-auto mb-4">
                    <div class="result-bar">
                        <div class="result-bar-fill" style="width: {{ $percent }}%;"></div>
                        <div class="result-bar-marker"></div>
                        <div class="result-bar-label">80%</div>
                    </div>
                </div>

                <p class="text-dark-secondary">
                    {{ $correctCount }} von {{ $total }} richtig
                </p>
            </div>

            <!-- Action Button -->
            <div class="flex justify-center mb-8">
                <a href="{{ route('dashboard') }}" class="btn-secondary px-8 py-3">
                    Zum Dashboard
                </a>
            </div>

            <!-- Detailed Results -->
            <div class="border-t border-glass-subtle pt-6">
                <h3 class="text-lg font-bold text-dark-primary mb-4">Detaillierte Auswertung</h3>

                <div class="flex flex-col gap-4">
                    @foreach($results as $index => $result)
                        @php
                            $frage = $result['frage'];
                            $userAnswer = $result['userAnswer'];
                            $solution = $result['solution'];
                            $isCorrect = $result['isCorrect'];
                            $mappingArray = $result['mapping'] ?? [];

                            $answersOriginal = [
                                ['letter' => 'A', 'text' => $frage->antwort_a],
                                ['letter' => 'B', 'text' => $frage->antwort_b],
                                ['letter' => 'C', 'text' => $frage->antwort_c],
                            ];

                            if ($mappingArray) {
                                $displayAnswers = [];
                                foreach ($mappingArray as $position => $letter) {
                                    foreach ($answersOriginal as $ans) {
                                        if ($ans['letter'] === $letter) {
                                            $displayAnswers[$position] = $ans;
                                            break;
                                        }
                                    }
                                }
                                ksort($displayAnswers);
                            } else {
                                $displayAnswers = $answersOriginal;
                            }
                        @endphp

                        <div class="glass-subtle p-4 rounded-xl {{ $isCorrect ? 'border-l-4 border-l-success' : 'border-l-4 border-l-error' }}">
                            <!-- Question Header -->
                            <div class="flex items-start gap-3 mb-3">
                                <span class="text-2xl">{{ $isCorrect ? '✅' : '❌' }}</span>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 text-xs text-dark-muted mb-1">
                                        <span>Frage {{ $index + 1 }}</span>
                                        <span class="opacity-50">|</span>
                                        <span>LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</span>
                                    </div>
                                    <p class="text-dark-primary text-sm font-medium">{{ $frage->frage }}</p>
                                </div>
                            </div>

                            <!-- Answers -->
                            <div class="flex flex-col gap-2 ml-9">
                                @foreach($displayAnswers as $answerIndex => $answer)
                                    @php
                                        $originalLetter = $answer['letter'];
                                        $isUserAnswer = $userAnswer->contains($originalLetter);
                                        $isSolution = $solution->contains($originalLetter);

                                        $stateClass = '';
                                        $icon = '';

                                        if ($isSolution && $isUserAnswer) {
                                            $stateClass = 'result-correct';
                                            $icon = '✓';
                                        } elseif ($isSolution && !$isUserAnswer) {
                                            $stateClass = 'result-correct-missed';
                                            $icon = '✓';
                                        } elseif (!$isSolution && $isUserAnswer) {
                                            $stateClass = 'result-wrong';
                                            $icon = '✗';
                                        } else {
                                            $stateClass = 'result-neutral';
                                        }
                                    @endphp

                                    <div class="flex items-start gap-2 p-2 rounded-lg border {{ $stateClass }}">
                                        <span class="w-5 h-5 flex items-center justify-center text-sm flex-shrink-0">
                                            @if($isUserAnswer) {{ $icon }} @endif
                                        </span>
                                        <span class="text-dark-primary text-sm flex-1">
                                            {{ $answer['text'] }}
                                        </span>
                                        @if($isSolution && !$isUserAnswer)
                                            <span class="text-xs font-medium px-2 py-0.5 rounded bg-success/20 text-success flex-shrink-0">
                                                Richtig
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Summary -->
                            <div class="mt-3 pt-3 border-t border-glass-subtle flex items-center justify-between text-xs ml-9">
                                <span class="{{ $isCorrect ? 'text-success' : 'text-error' }} font-medium">
                                    {{ $isCorrect ? '✓ Richtig' : '✗ Falsch' }}
                                </span>
                                <span class="text-dark-muted">
                                    Lösung: {{ $solution->join(', ') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
