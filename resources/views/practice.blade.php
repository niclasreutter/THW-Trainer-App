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

@push('styles')
<style>
    /* Practice Page - Dark Mode Glassmorphism */

    /* Mobile: Navigation & Footer ausblenden */
    @media (max-width: 640px) {
        footer, nav {
            display: none !important;
        }

        main {
            padding: 0 !important;
            min-height: 100dvh !important;
        }
    }

    /* Practice Container */
    #practiceContainer {
        max-width: 900px;
        margin: 0 auto;
    }

    @media (max-width: 640px) {
        #practiceContainer {
            padding: 0 !important;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }
    }

    /* Question Card Styling */
    .question-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 1.5rem;
    }

    @media (max-width: 640px) {
        .question-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-radius: 0;
            border: none;
            height: auto;
            min-height: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* Platz für fixed button unten */
            padding-bottom: 100px;
        }

        .question-card form {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .question-card #questionContent {
            flex: 1;
        }
    }

    @media (min-width: 641px) {
        .question-card {
            padding: 2rem;
            border-radius: 24px 8px 24px 8px;
        }
    }

    /* Answer Option Styling */
    .answer-option {
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

    .answer-option:hover {
        background: rgba(251, 191, 36, 0.05);
        border-color: rgba(251, 191, 36, 0.3);
        transform: translateX(4px);
    }

    .answer-option:active {
        transform: scale(0.98);
    }

    .answer-option.selected {
        background: rgba(251, 191, 36, 0.1);
        border-color: var(--gold);
    }

    /* Custom Checkbox */
    .answer-checkbox {
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

    .answer-checkbox:checked {
        background: var(--gold);
        border-color: var(--gold);
    }

    .answer-checkbox:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #0a0a0b;
        font-size: 14px;
        font-weight: bold;
    }

    /* Result States */
    .answer-correct {
        background: rgba(34, 197, 94, 0.15) !important;
        border-color: rgba(34, 197, 94, 0.5) !important;
    }

    .answer-correct-missed {
        background: rgba(34, 197, 94, 0.08) !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
    }

    .answer-wrong {
        background: rgba(239, 68, 68, 0.15) !important;
        border-color: rgba(239, 68, 68, 0.5) !important;
    }

    .answer-neutral {
        background: rgba(255, 255, 255, 0.02) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
        opacity: 0.6;
    }

    /* Progress Ring */
    .progress-ring {
        width: 48px;
        height: 48px;
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 0.5s ease;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    /* Bookmark Button */
    .bookmark-btn {
        padding: 0.5rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.2s ease;
    }

    .bookmark-btn:hover {
        background: rgba(251, 191, 36, 0.1);
        border-color: rgba(251, 191, 36, 0.3);
    }

    .bookmark-btn.active {
        background: rgba(251, 191, 36, 0.2);
        border-color: var(--gold);
    }

    /* Gamification Popup */
    .result-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 340px;
        width: 90vw;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease-out;
        pointer-events: none;
    }

    .result-popup.show {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }

    /* Mobile Header */
    .mobile-header {
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

    @media (min-width: 641px) {
        .mobile-header {
            display: none;
        }
    }

    /* Submit Button Wrapper - Desktop */
    @media (min-width: 641px) {
        .submit-button-wrapper {
            margin-top: auto;
            flex-shrink: 0;
        }
    }

    /* Mode Badge */
    .mode-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        background: rgba(251, 191, 36, 0.1);
        border: 1px solid rgba(251, 191, 36, 0.2);
        border-radius: 20px;
        font-size: 0.75rem;
        color: var(--gold);
        font-weight: 500;
    }

    /* Shake Animation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-6px); }
        80% { transform: translateX(6px); }
    }

    .shake {
        animation: shake 0.4s ease;
    }

    /* ============================================
       LIGHT MODE OVERRIDES
       ============================================ */

    /* Question Card in Light Mode */
    html.light-mode .question-card {
        background: #ffffff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border: 1px solid rgba(0, 51, 127, 0.12) !important;
        box-shadow: 0 4px 20px rgba(0, 51, 127, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }

    /* Answer Options in Light Mode */
    html.light-mode .answer-option {
        background: #f8fafc !important;
        border: 2px solid rgba(0, 51, 127, 0.12) !important;
    }

    html.light-mode .answer-option:hover {
        background: rgba(217, 119, 6, 0.06) !important;
        border-color: rgba(217, 119, 6, 0.4) !important;
    }

    html.light-mode .answer-option.selected {
        background: rgba(217, 119, 6, 0.1) !important;
        border-color: #d97706 !important;
    }

    /* Checkbox in Light Mode */
    html.light-mode .answer-checkbox {
        border: 2px solid rgba(0, 51, 127, 0.25) !important;
        background: #ffffff !important;
    }

    html.light-mode .answer-checkbox:checked {
        background: #d97706 !important;
        border-color: #d97706 !important;
    }

    /* Result States in Light Mode */
    html.light-mode .answer-correct {
        background: rgba(34, 197, 94, 0.12) !important;
        border-color: rgba(34, 197, 94, 0.4) !important;
    }

    html.light-mode .answer-correct-missed {
        background: rgba(34, 197, 94, 0.06) !important;
        border-color: rgba(34, 197, 94, 0.25) !important;
    }

    html.light-mode .answer-wrong {
        background: rgba(239, 68, 68, 0.12) !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
    }

    html.light-mode .answer-neutral {
        background: #f1f5f9 !important;
        border-color: rgba(0, 51, 127, 0.08) !important;
        opacity: 0.7;
    }

    /* Bookmark Button in Light Mode */
    html.light-mode .bookmark-btn {
        background: rgba(0, 51, 127, 0.04) !important;
        border: 1px solid rgba(0, 51, 127, 0.1) !important;
    }

    html.light-mode .bookmark-btn:hover {
        background: rgba(217, 119, 6, 0.08) !important;
        border-color: rgba(217, 119, 6, 0.25) !important;
    }

    html.light-mode .bookmark-btn.active {
        background: rgba(217, 119, 6, 0.15) !important;
        border-color: #d97706 !important;
    }

    /* Mobile Header in Light Mode */
    html.light-mode .mobile-header {
        background: rgba(0, 51, 127, 0.03) !important;
        border-bottom: 1px solid rgba(0, 51, 127, 0.08) !important;
    }

    /* Mode Badge in Light Mode */
    html.light-mode .mode-badge {
        background: rgba(217, 119, 6, 0.1) !important;
        border: 1px solid rgba(217, 119, 6, 0.2) !important;
        color: #b45309 !important;
    }

    /* Progress Bar in Light Mode */
    html.light-mode .progress-glass {
        background: rgba(0, 51, 127, 0.08) !important;
    }

    html.light-mode .progress-fill-gold {
        background: linear-gradient(90deg, #d97706, #b45309) !important;
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

    /* Bookmark Icon in Light Mode - bessere Sichtbarkeit */
    html.light-mode .bookmark-btn svg path {
        stroke: rgba(0, 51, 127, 0.5) !important;
    }

    html.light-mode .bookmark-btn:hover svg path {
        stroke: #d97706 !important;
    }

    html.light-mode .bookmark-btn.active svg path {
        stroke: #d97706 !important;
        fill: #d97706 !important;
    }
</style>
@endpush

<!-- Practice Container -->
<div class="p-4 sm:p-6 sm:py-8" id="practiceContainer">
    @if($question)
        <div class="question-card">
            <!-- Mobile Header -->
            <div class="mobile-header sm:hidden">
                <a href="{{ route('practice.menu') }}" class="p-2 text-dark-secondary hover:text-gold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex items-center gap-3">
                    <span class="text-dark-muted text-xs">{{ $progress }}/{{ $total }}</span>

                    <!-- Mini Progress Ring -->
                    <svg class="w-8 h-8" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3"/>
                        <circle cx="18" cy="18" r="14" fill="none" stroke="url(#goldGradientMini)" stroke-width="3"
                                stroke-dasharray="{{ 87.96 }}"
                                stroke-dashoffset="{{ 87.96 - (87.96 * ($progressPercent ?? 0) / 100) }}"
                                class="progress-ring-circle"/>
                        <defs>
                            <linearGradient id="goldGradientMini" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#fbbf24"/>
                                <stop offset="100%" stop-color="#f59e0b"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                @php
                    $user = Auth::user();
                    $bookmarked = is_array($user->bookmarked_questions ?? null)
                        ? $user->bookmarked_questions
                        : json_decode($user->bookmarked_questions ?? '[]', true);
                    $isBookmarked = in_array($question->id, $bookmarked);
                @endphp

                <button type="button" class="bookmark-btn {{ $isBookmarked ? 'active' : '' }}" id="bookmarkBtnMobile"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                        onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" id="bookmarkIconMobile">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"
                              style="stroke: {{ $isBookmarked ? '#fbbf24' : 'rgba(255,255,255,0.4)' }}; fill: {{ $isBookmarked ? '#fbbf24' : 'none' }};"></path>
                    </svg>
                </button>
            </div>

            <!-- Desktop Header -->
            <div class="hidden sm:flex items-start justify-between mb-6">
                <div class="flex-1">
                    <!-- Mode Badge -->
                    @if(isset($mode))
                        <div class="mode-badge mb-3">
                            @switch($mode)
                                @case('unsolved')
                                    Ungelöste Fragen
                                    @break
                                @case('failed')
                                    Fehlerwiederholung
                                    @break
                                @case('section')
                                    Lernabschnitt {{ session('practice_parameter') }}
                                    @break
                                @case('search')
                                    Suche: "{{ session('practice_parameter') }}"
                                    @break
                                @case('bookmarked')
                                    Gespeicherte Fragen
                                    @break
                                @default
                                    Alle Fragen
                            @endswitch
                        </div>
                    @endif

                    <h1 class="text-xl font-bold text-dark-primary mb-2">Theorie üben</h1>

                    <!-- Progress Info -->
                    <div class="flex items-center gap-4">
                        <div class="text-dark-secondary text-sm">
                            <span class="text-gold font-semibold">{{ $progress }}</span>
                            <span class="text-dark-muted">/{{ $total }} gemeistert</span>
                        </div>
                        <div class="flex-1 max-w-xs">
                            <div class="progress-glass h-2">
                                <div class="progress-fill-gold" style="width: {{ $progressPercent ?? 0 }}%;"></div>
                            </div>
                        </div>
                        <span class="text-dark-muted text-xs">{{ $progressPercent ?? 0 }}%</span>
                    </div>
                </div>

                <!-- Bookmark Button Desktop -->
                @php
                    $user = Auth::user();
                    $bookmarked = is_array($user->bookmarked_questions ?? null)
                        ? $user->bookmarked_questions
                        : json_decode($user->bookmarked_questions ?? '[]', true);
                    $isBookmarked = in_array($question->id, $bookmarked);
                @endphp

                <button type="button"
                        class="bookmark-btn {{ $isBookmarked ? 'active' : '' }}"
                        title="{{ $isBookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen' }}"
                        id="bookmarkBtn"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                        onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                    <svg class="w-6 h-6" viewBox="0 0 20 20" id="bookmarkIcon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"
                              style="stroke: {{ $isBookmarked ? '#fbbf24' : 'rgba(255,255,255,0.4)' }}; fill: {{ $isBookmarked ? '#fbbf24' : 'none' }};"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('practice.submit') }}" id="practiceForm">
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
                        // Neue Frage: shuffle
                        $answers = $answersOriginal;
                        shuffle($answers);

                        $mappingArray = [];
                        foreach ($answers as $index => $answer) {
                            $mappingArray[$index] = $answer['letter'];
                        }
                    }

                    $mappingJson = json_encode($mappingArray);
                    $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
                @endphp

                <input type="hidden" name="answer_mapping" value="{{ $mappingJson }}">

                <!-- Question Content -->
                <div class="mb-6" id="questionContent">
                    <!-- Question Meta -->
                    <div class="flex items-center gap-2 mb-3 text-xs text-dark-muted">
                        <span>ID: {{ $question->id }}</span>
                        <span class="text-dark-muted/50">|</span>
                        <span>LA {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                    </div>

                    <!-- Question Text -->
                    <p class="text-dark-primary text-base sm:text-lg leading-relaxed mb-6">
                        {{ $question->frage }}
                    </p>

                    <!-- Answer Options -->
                    <div class="flex flex-col gap-3">
                        @foreach($answers as $index => $answer)
                            @php
                                $originalLetter = $answer['letter'];
                                $isCorrectAnswer = $solution->contains($originalLetter);
                                $isUserAnswer = isset($userAnswer) && $userAnswer->contains($originalLetter);

                                // Determine styling based on result
                                $stateClass = '';
                                $icon = '';

                                if (isset($isCorrect)) {
                                    if ($isCorrectAnswer && $isUserAnswer) {
                                        $stateClass = 'answer-correct';
                                        $icon = '✓';
                                    } elseif ($isCorrectAnswer && !$isUserAnswer) {
                                        $stateClass = 'answer-correct-missed';
                                        $icon = '✓';
                                    } elseif (!$isCorrectAnswer && $isUserAnswer) {
                                        $stateClass = 'answer-wrong';
                                        $icon = '✗';
                                    } else {
                                        $stateClass = 'answer-neutral';
                                    }
                                }
                            @endphp

                            @if(isset($isCorrect))
                                <!-- Result Display -->
                                <div class="answer-option {{ $stateClass }}">
                                    <span class="w-6 h-6 flex items-center justify-center text-lg flex-shrink-0">
                                        @if($isUserAnswer)
                                            {{ $icon }}
                                        @endif
                                    </span>
                                    <span class="text-dark-primary text-sm sm:text-base flex-1">
                                        {{ $answer['text'] }}
                                    </span>
                                    @if($isCorrectAnswer && !$isUserAnswer)
                                        <span class="text-xs font-medium px-2 py-1 rounded bg-success/20 text-success flex-shrink-0">
                                            Richtig
                                        </span>
                                    @endif
                                </div>
                            @else
                                <!-- Answer Selection -->
                                <label class="answer-option" onclick="updateSelectionStyle(this)">
                                    <input type="checkbox" name="answer[]" value="{{ $index }}"
                                           class="answer-checkbox"
                                           onchange="updateSubmitButton()">
                                    <span class="text-dark-primary text-sm sm:text-base flex-1">
                                        {{ $answer['text'] }}
                                    </span>
                                </label>
                            @endif
                        @endforeach
                    </div>

                    @if(isset($isCorrect))
                        <!-- Result Summary -->
                        <div class="mt-4 pt-4 border-t border-glass-subtle flex items-center justify-between text-sm">
                            <span class="{{ $isCorrect ? 'text-success' : 'text-error' }} font-medium">
                                {{ $isCorrect ? '✓ Richtig beantwortet' : '✗ Falsch beantwortet' }}
                            </span>
                            <span class="text-dark-muted">
                                Lösung: {{ $solution->join(', ') }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Submit/Next Button -->
                <div class="submit-button-wrapper sm:relative sm:inset-auto sm:bg-transparent sm:p-0 sm:border-0 sm:z-auto fixed bottom-0 left-0 right-0 z-[100] p-4 pb-6 border-t" style="background: var(--bg-base); border-color: var(--glass-border);">
                    @if(!isset($isCorrect))
                        <button type="submit" id="submitBtn" class="btn-primary w-full py-4 text-base" disabled>
                            Antwort absenden
                        </button>
                    @else
                        <a href="{{ route('practice.index') }}" class="btn-primary w-full py-4 text-base text-center block">
                            Nächste Frage
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Gamification Popup -->
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
        @endphp

        @if(isset($isCorrect) && $isCorrect)
            <div id="gamificationPopup" class="result-popup">
                <div class="glass-success p-5">
                    @if($showGamification)
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-4xl">{{ $celebration['emoji'] }}</span>
                            <div>
                                <div class="text-xl font-bold text-dark-primary">{{ $celebration['text'] }}</div>
                                <div class="text-lg text-gold font-semibold">+{{ $pointsAwarded }} Punkte</div>
                            </div>
                        </div>
                        <div class="text-sm text-dark-secondary text-center">{{ $reasonText }}</div>
                    @else
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-4xl">🎉</span>
                            <div>
                                <div class="text-xl font-bold text-dark-primary">Richtig!</div>
                                @if($gamificationResult && isset($gamificationResult['points_awarded']))
                                    <div class="text-lg text-gold font-semibold">+{{ $gamificationResult['points_awarded'] }} Punkte</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($showMastered)
                        <div class="mt-3 p-3 rounded-lg bg-white/10 border border-white/20 text-center">
                            <div class="font-bold text-dark-primary">Gemeistert!</div>
                        </div>
                    @elseif($showOneMore)
                        <div class="mt-3 p-3 rounded-lg bg-white/10 border border-white/20 text-center">
                            <div class="font-medium text-dark-primary">Noch 1x richtig für gemeistert!</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Error Popup -->
        @if(isset($isCorrect) && !$isCorrect)
            <div id="errorPopup" class="result-popup">
                <div class="glass-error p-4">
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-2xl">❌</span>
                        <span class="text-dark-primary font-bold">Falsch. Richtige Antworten markiert.</span>
                    </div>
                </div>
            </div>
        @endif

        <script>
            // Update selection style on label
            function updateSelectionStyle(label) {
                const checkbox = label.querySelector('input[type="checkbox"]');
                if (checkbox.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            }

            // Update submit button state
            function updateSubmitButton() {
                const checkboxes = document.querySelectorAll('input[name="answer[]"]');
                const submitBtn = document.getElementById('submitBtn');
                const checked = Array.from(checkboxes).some(cb => cb.checked);
                submitBtn.disabled = !checked;

                // Update label styles
                checkboxes.forEach(cb => {
                    const label = cb.closest('.answer-option');
                    if (cb.checked) {
                        label.classList.add('selected');
                    } else {
                        label.classList.remove('selected');
                    }
                });
            }

            // Show popups on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Show Gamification Popup
                const gamificationPopup = document.getElementById('gamificationPopup');
                if (gamificationPopup) {
                    setTimeout(() => {
                        gamificationPopup.classList.add('show');

                        // Trigger floating points animation
                        @if(isset($showGamification) && $showGamification)
                            const points = {{ $pointsAwarded ?? 0 }};
                            const x = window.innerWidth - 200;
                            const y = 80;
                            if (typeof window.showFloatingPoints === 'function') {
                                window.showFloatingPoints(x, y, points);
                            }
                        @endif

                        setTimeout(() => {
                            gamificationPopup.classList.remove('show');
                        }, 3000);
                    }, 100);
                }

                // Show Error Popup with shake
                const errorPopup = document.getElementById('errorPopup');
                if (errorPopup) {
                    const questionContent = document.getElementById('questionContent');
                    if (questionContent) {
                        questionContent.classList.add('shake');
                        setTimeout(() => questionContent.classList.remove('shake'), 400);
                    }

                    setTimeout(() => {
                        errorPopup.classList.add('show');
                        setTimeout(() => {
                            errorPopup.classList.remove('show');
                        }, 3000);
                    }, 100);
                }
            });

            // Bookmark functionality
            function toggleBookmark(questionId, currentlyBookmarked) {
                const btnMobile = document.getElementById('bookmarkBtnMobile');
                const btnDesktop = document.getElementById('bookmarkBtn');
                const btn = window.innerWidth <= 640 ? btnMobile : btnDesktop;

                if (!btn) return;

                const formData = new FormData();
                formData.append('question_id', questionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Show loading
                btn.style.opacity = '0.5';
                btn.disabled = true;

                fetch('{{ route("bookmarks.toggle") }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const targetColor = data.is_bookmarked ? '#fbbf24' : 'rgba(255,255,255,0.4)';
                        const targetFill = data.is_bookmarked ? '#fbbf24' : 'none';

                        // Update both buttons
                        [btnMobile, btnDesktop].forEach(button => {
                            if (!button) return;

                            button.setAttribute('data-bookmarked', data.is_bookmarked ? 'true' : 'false');
                            button.setAttribute('onclick', `toggleBookmark(${questionId}, ${data.is_bookmarked})`);

                            if (data.is_bookmarked) {
                                button.classList.add('active');
                            } else {
                                button.classList.remove('active');
                            }

                            const icon = button.querySelector('svg');
                            if (icon) {
                                const path = icon.querySelector('path');
                                if (path) {
                                    path.style.stroke = targetColor;
                                    path.style.fill = targetFill;
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Bookmark error:', error);
                })
                .finally(() => {
                    btn.style.opacity = '1';
                    btn.disabled = false;
                });
            }
        </script>
    @else
        <!-- No more questions -->
        <div class="glass p-8 text-center">
            <div class="text-4xl mb-4">🎉</div>
            <h2 class="text-xl font-bold text-dark-primary mb-2">Geschafft!</h2>
            <p class="text-dark-secondary mb-6">Du hast alle Fragen in diesem Modus bearbeitet!</p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('practice.menu') }}" class="btn-primary">
                    Zurück zum Übungsmenü
                </a>
                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
