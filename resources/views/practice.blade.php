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
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ============================================
       PRACTICE PAGE - Dashboard-Matched Design
       Mobile-First, Fullscreen, No Scroll
       ============================================ */

    /* ── Mobile: Fullscreen Takeover ─────────────── */
    @media (max-width: 640px) {
        html, body {
            height: 100dvh !important;
            overflow: hidden !important;
        }
        footer, nav, header {
            display: none !important;
        }
        main {
            padding: 0 !important;
            margin: 0 !important;
            height: 100dvh !important;
            overflow: hidden !important;
        }
    }

    /* ── Practice Shell ──────────────────────────── */
    .practice-shell {
        max-width: 1100px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    @media (max-width: 640px) {
        .practice-shell {
            padding: 0;
            max-width: 100%;
            height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
    }

    /* ── Top Bar (Mobile) ────────────────────────── */
    .practice-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.625rem 1rem;
        padding-top: calc(0.625rem + env(safe-area-inset-top, 0px));
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        flex-shrink: 0;
    }

    html.light-mode .practice-topbar {
        background: rgba(0, 51, 127, 0.03);
        border-bottom-color: rgba(0, 51, 127, 0.08);
    }

    @media (min-width: 641px) {
        .practice-topbar { display: none; }
    }

    .topbar-back {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        color: var(--text-secondary);
        transition: all 0.2s;
    }

    .topbar-back:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.05);
    }

    .topbar-center {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .topbar-progress-text {
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.02em;
    }

    .topbar-progress-ring {
        width: 32px;
        height: 32px;
    }

    .topbar-progress-ring .ring-track { stroke: rgba(255, 255, 255, 0.08); }
    .topbar-progress-ring .ring-fill {
        transition: stroke-dashoffset 0.5s ease;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    html.light-mode .topbar-progress-ring .ring-track { stroke: rgba(0, 51, 127, 0.1); }

    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    /* ── Desktop Header ──────────────────────────── */
    .practice-header {
        margin-bottom: 1.25rem;
    }

    @media (max-width: 640px) {
        .practice-header { display: none; }
    }

    .practice-greeting {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.375rem;
    }

    .practice-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'Barlow Condensed', sans-serif;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .practice-level-line {
        font-size: 0.8125rem;
        color: #5b9aff;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    html.light-mode .practice-level-line { color: #00337F; }

    .practice-xp-bar {
        height: 4px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 2px;
        overflow: hidden;
        max-width: 160px;
        flex-shrink: 0;
    }

    html.light-mode .practice-xp-bar { background: rgba(0, 0, 0, 0.08); }

    .practice-xp-fill {
        height: 100%;
        background: linear-gradient(90deg, #5b9aff, #0055cc);
        border-radius: 2px;
        transition: width 0.6s ease-out;
    }

    /* ── Badge Row ────────────────────────────────── */
    .practice-badges {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    @media (max-width: 640px) {
        .practice-badges { display: none; }
    }

    .p-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.625rem;
        border-radius: 0.375rem;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1;
        white-space: nowrap;
    }

    .p-badge--mode {
        color: #5b9aff;
        background: rgba(0, 77, 179, 0.12);
        border: 1px solid rgba(0, 77, 179, 0.25);
    }

    html.light-mode .p-badge--mode {
        color: #00337F;
        background: rgba(0, 51, 127, 0.08);
        border-color: rgba(0, 51, 127, 0.2);
    }

    .p-badge--sr {
        color: #818cf8;
        background: rgba(129, 140, 248, 0.12);
        border: 1px solid rgba(129, 140, 248, 0.25);
    }

    .p-badge--easy {
        color: #22c55e;
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .p-badge--medium {
        color: #fbbf24;
        background: rgba(251, 191, 36, 0.12);
        border: 1px solid rgba(251, 191, 36, 0.2);
    }

    .p-badge--hard {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    html.light-mode .p-badge--easy { color: #16a34a; }
    html.light-mode .p-badge--medium { color: #b45309; }
    html.light-mode .p-badge--hard { color: #dc2626; }

    .p-badge--progress {
        color: var(--text-muted);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        font-family: 'IBM Plex Mono', monospace;
    }

    html.light-mode .p-badge--progress {
        background: rgba(0, 0, 0, 0.04);
        border-color: rgba(0, 0, 0, 0.08);
    }

    /* ── Question Card ───────────────────────────── */
    .practice-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 1.5rem 0.5rem 1.5rem 1.5rem;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        animation: practice-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .practice-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #00337F, #0055cc, #5b9aff);
    }

    @keyframes practice-rise {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    html.light-mode .practice-card {
        background: rgba(255, 255, 255, 0.95);
        border-color: rgba(0, 51, 127, 0.12);
        box-shadow: 0 4px 24px rgba(0, 51, 127, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    html.light-mode .practice-card::before {
        background: linear-gradient(90deg, #00337F, #0055cc, #5b9aff);
    }

    @media (max-width: 640px) {
        .practice-card {
            border-radius: 0;
            border: none;
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-base);
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            padding: 1rem;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            animation: none;
        }

        .practice-card::before { display: none; }

        html.light-mode .practice-card {
            background: var(--bg-base);
            border: none;
            box-shadow: none;
        }
    }

    /* ── Mobile Badges (inside card) ─────────────── */
    .practice-mobile-badges {
        display: none;
        gap: 0.375rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 640px) {
        .practice-mobile-badges { display: flex; }
    }

    .pm-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        padding: 0.15rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .pm-badge--sr {
        color: #818cf8;
        background: rgba(129, 140, 248, 0.12);
    }

    .pm-badge--easy { color: #22c55e; background: rgba(34, 197, 94, 0.1); }
    .pm-badge--medium { color: #fbbf24; background: rgba(251, 191, 36, 0.1); }
    .pm-badge--hard { color: #ef4444; background: rgba(239, 68, 68, 0.1); }

    html.light-mode .pm-badge--easy { color: #16a34a; }
    html.light-mode .pm-badge--medium { color: #b45309; }
    html.light-mode .pm-badge--hard { color: #dc2626; }

    /* ── Question Meta ───────────────────────────── */
    .question-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .question-meta-left {
        font-size: 0.5625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ── Question Text ───────────────────────────── */
    .question-text {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.55;
        margin-bottom: 1.25rem;
    }

    @media (min-width: 641px) {
        .question-text {
            font-size: 1.0625rem;
        }
    }

    /* ── Answer Options ──────────────────────────── */
    .answers-grid {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .answer-opt {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .answer-opt:hover {
        background: rgba(0, 77, 179, 0.06);
        border-color: rgba(0, 77, 179, 0.25);
        transform: translateX(3px);
    }

    .answer-opt:active {
        transform: scale(0.98);
    }

    .answer-opt.selected {
        background: rgba(0, 77, 179, 0.1);
        border-color: #0055cc;
        box-shadow: 0 0 0 1px rgba(0, 85, 204, 0.3);
    }

    html.light-mode .answer-opt {
        background: #f8fafc;
        border-color: rgba(0, 51, 127, 0.12);
    }

    html.light-mode .answer-opt:hover {
        background: rgba(0, 51, 127, 0.04);
        border-color: rgba(0, 51, 127, 0.3);
    }

    html.light-mode .answer-opt.selected {
        background: rgba(0, 51, 127, 0.08);
        border-color: #00337F;
    }

    /* Custom Checkbox */
    .answer-check {
        appearance: none;
        -webkit-appearance: none;
        width: 22px;
        height: 22px;
        min-width: 22px;
        border: 2px solid rgba(255, 255, 255, 0.25);
        border-radius: 0.375rem;
        background: rgba(255, 255, 255, 0.05);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        margin-top: 1px;
    }

    .answer-check:checked {
        background: linear-gradient(135deg, #0055cc, #5b9aff);
        border-color: #0055cc;
    }

    .answer-check:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: 13px;
        font-weight: bold;
    }

    html.light-mode .answer-check {
        border-color: rgba(0, 51, 127, 0.25);
        background: #fff;
    }

    html.light-mode .answer-check:checked {
        background: linear-gradient(135deg, #00337F, #0055cc);
        border-color: #00337F;
    }

    .answer-text {
        font-size: 0.9375rem;
        color: var(--text-primary);
        line-height: 1.45;
        flex: 1;
    }

    @media (max-width: 640px) {
        .answer-text { font-size: 0.875rem; }
    }

    /* ── Result States ───────────────────────────── */
    .answer-opt--correct {
        background: rgba(34, 197, 94, 0.12) !important;
        border-color: rgba(34, 197, 94, 0.4) !important;
    }

    .answer-opt--correct-missed {
        background: rgba(34, 197, 94, 0.06) !important;
        border-color: rgba(34, 197, 94, 0.25) !important;
    }

    .answer-opt--wrong {
        background: rgba(239, 68, 68, 0.12) !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
    }

    .answer-opt--neutral {
        background: rgba(255, 255, 255, 0.02) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
        opacity: 0.5;
    }

    html.light-mode .answer-opt--correct {
        background: rgba(34, 197, 94, 0.1) !important;
    }

    html.light-mode .answer-opt--wrong {
        background: rgba(239, 68, 68, 0.1) !important;
    }

    html.light-mode .answer-opt--neutral {
        background: #f1f5f9 !important;
        border-color: rgba(0, 0, 0, 0.06) !important;
    }

    .result-icon {
        width: 22px;
        height: 22px;
        min-width: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        margin-top: 1px;
    }

    .result-icon--correct {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }

    .result-icon--wrong {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    /* ── Result Summary Line ─────────────────────── */
    .result-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        font-size: 0.8125rem;
    }

    html.light-mode .result-summary {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    .result-label--correct {
        color: #22c55e;
        font-weight: 700;
    }

    .result-label--wrong {
        color: #ef4444;
        font-weight: 700;
    }

    /* ── Desktop Progress Bar ────────────────────── */
    .practice-progress-bar {
        margin-bottom: 1.25rem;
    }

    @media (max-width: 640px) {
        .practice-progress-bar { display: none; }
    }

    .ppb-track {
        height: 4px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 2px;
        overflow: hidden;
    }

    html.light-mode .ppb-track { background: rgba(0, 0, 0, 0.06); }

    .ppb-fill {
        height: 100%;
        background: linear-gradient(90deg, #0055cc, #5b9aff);
        border-radius: 2px;
        transition: width 0.5s ease-out;
    }

    /* ── Bottom Action Bar ────────────────────────── */
    .practice-actions {
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    html.light-mode .practice-actions {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    @media (max-width: 640px) {
        .practice-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            margin: 0;
            padding: 0.875rem 1rem;
            padding-bottom: calc(0.875rem + env(safe-area-inset-bottom, 0px));
            background: rgba(10, 10, 11, 0.6);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            backdrop-filter: blur(20px) saturate(180%);
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        html.light-mode .practice-actions {
            background: rgba(243, 244, 246, 0.6);
            border-top-color: rgba(0, 51, 127, 0.08);
        }
    }

    .action-submit {
        width: 100%;
        padding: 0.875rem;
        font-size: 0.9375rem;
        font-weight: 700;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
        text-align: center;
        text-decoration: none;
        display: block;
    }

    .action-submit--primary {
        background: linear-gradient(135deg, #00337F, #0055cc);
        color: #fff;
        box-shadow: 0 4px 16px rgba(0, 51, 127, 0.3);
    }

    .action-submit--primary:hover {
        box-shadow: 0 6px 24px rgba(0, 51, 127, 0.4);
        transform: translateY(-1px);
    }

    .action-submit--primary:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .action-submit--gold {
        background: var(--gradient-gold);
        color: #1a1a2e;
        box-shadow: 0 4px 16px rgba(251, 191, 36, 0.25);
    }

    .action-submit--gold:hover {
        box-shadow: 0 6px 24px rgba(251, 191, 36, 0.35);
        transform: translateY(-1px);
    }

    .action-end {
        display: block;
        width: 100%;
        text-align: center;
        margin-top: 0.5rem;
        padding: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        border-radius: 0.5rem;
        transition: color 0.2s;
    }

    .action-end:hover { color: var(--text-secondary); }

    /* ── Bookmark Button ─────────────────────────── */
    .bookmark-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s ease;
        cursor: pointer;
        flex-shrink: 0;
        padding: 0;
    }

    .bookmark-btn:hover {
        background: rgba(251, 191, 36, 0.1);
        border-color: rgba(251, 191, 36, 0.3);
    }

    .bookmark-btn.active {
        background: rgba(251, 191, 36, 0.15);
        border-color: var(--gold);
    }

    html.light-mode .bookmark-btn {
        background: rgba(0, 51, 127, 0.04);
        border-color: rgba(0, 51, 127, 0.1);
    }

    html.light-mode .bookmark-btn:hover {
        background: rgba(217, 119, 6, 0.08);
        border-color: rgba(217, 119, 6, 0.25);
    }

    html.light-mode .bookmark-btn.active {
        background: rgba(217, 119, 6, 0.12);
        border-color: #d97706;
    }

    html.light-mode .bookmark-btn svg path {
        stroke: rgba(0, 51, 127, 0.4) !important;
    }

    html.light-mode .bookmark-btn:hover svg path {
        stroke: #d97706 !important;
    }

    html.light-mode .bookmark-btn.active svg path {
        stroke: #d97706 !important;
        fill: #d97706 !important;
    }

    /* ── Report Button ───────────────────────────── */
    .report-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(255, 255, 255, 0.03);
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
        padding: 0;
    }

    .report-btn:hover {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }

    html.light-mode .report-btn {
        border-color: rgba(0, 0, 0, 0.08);
        background: rgba(0, 0, 0, 0.02);
        color: rgba(0, 0, 0, 0.25);
    }

    html.light-mode .report-btn:hover {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
    }

    /* ── Desktop Bookmark (large) ────────────────── */
    .bookmark-btn-lg {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.625rem;
    }

    /* ── Fullscreen Overlays ─────────────────────── */
    .fs-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.85);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
    }

    .fs-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    /* Correct/Wrong: compact toast instead of fullscreen */
    .fs-overlay--correct,
    .fs-overlay--wrong {
        inset: auto;
        top: 1rem;
        right: 1rem;
        left: auto;
        bottom: auto;
        background: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        align-items: flex-start;
        justify-content: flex-end;
    }

    @media (max-width: 640px) {
        .fs-overlay--correct,
        .fs-overlay--wrong {
            top: calc(env(safe-area-inset-top, 0px) + 3.5rem);
            right: 0.75rem;
            left: 0.75rem;
        }
    }

    .fs-overlay--correct .fs-overlay-card,
    .fs-overlay--wrong .fs-overlay-card {
        max-width: 320px;
        padding: 1.25rem 1.5rem;
        border-radius: 1rem 0.375rem 1rem 1rem;
        text-align: left;
    }

    @media (max-width: 640px) {
        .fs-overlay--correct .fs-overlay-card,
        .fs-overlay--wrong .fs-overlay-card {
            max-width: 100%;
            width: 100%;
        }
    }

    .fs-overlay--correct .fs-icon,
    .fs-overlay--wrong .fs-icon {
        font-size: 1.5rem;
        margin-bottom: 0;
        display: inline;
        margin-right: 0.5rem;
        vertical-align: middle;
    }

    .fs-overlay--correct .fs-title,
    .fs-overlay--wrong .fs-title {
        font-size: 1.125rem;
        display: inline;
        vertical-align: middle;
    }

    .fs-overlay--correct .fs-points {
        font-size: 1.125rem;
        margin: 0.5rem 0 0.25rem;
    }

    .fs-overlay--correct .fs-dismiss,
    .fs-overlay--wrong .fs-dismiss {
        display: none;
    }

    .fs-overlay-card {
        max-width: 380px;
        width: 90%;
        padding: 2.5rem 2rem;
        text-align: center;
        border-radius: 1.5rem 0.5rem 1.5rem 1.5rem;
        position: relative;
        overflow: hidden;
        animation: overlayPop 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes overlayPop {
        from { transform: scale(0.9) translateY(20px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }

    .fs-overlay-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    /* Correct overlay */
    .fs-overlay--correct .fs-overlay-card {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.25);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 80px rgba(34, 197, 94, 0.15);
    }

    .fs-overlay--correct .fs-overlay-card::before {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    /* Wrong overlay */
    .fs-overlay--wrong .fs-overlay-card {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.25);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 60px rgba(239, 68, 68, 0.12);
    }

    .fs-overlay--wrong .fs-overlay-card::before {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    /* Level-up overlay */
    .fs-overlay--levelup .fs-overlay-card {
        background: var(--gradient-gold-135);
        border: none;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 100px rgba(251, 191, 36, 0.2);
    }

    .fs-overlay--levelup .fs-overlay-card::before {
        display: none;
    }

    /* Achievement overlay */
    .fs-overlay--achievement .fs-overlay-card {
        background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(236, 72, 153, 0.2));
        border: 1px solid rgba(147, 51, 234, 0.35);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 80px rgba(147, 51, 234, 0.15);
    }

    .fs-overlay--achievement .fs-overlay-card::before {
        background: linear-gradient(90deg, #9333ea, #ec4899);
    }

    /* Streak overlay */
    .fs-overlay--streak .fs-overlay-card {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(251, 191, 36, 0.15));
        border: 1px solid rgba(245, 158, 11, 0.35);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 80px rgba(245, 158, 11, 0.15);
    }

    .fs-overlay--streak .fs-overlay-card::before {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }

    .fs-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }

    .fs-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 0.5rem;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .fs-subtitle {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0.375rem;
    }

    .fs-detail {
        font-size: 0.8125rem;
        color: rgba(255, 255, 255, 0.55);
        font-family: 'IBM Plex Mono', monospace;
    }

    .fs-points {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--gold);
        margin: 0.75rem 0;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .fs-mastery-bar {
        margin-top: 1rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .fs-mastery-text {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #fff;
    }

    .fs-dismiss {
        margin-top: 1.25rem;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.4);
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.05em;
    }

    /* ── Report Modal ────────────────────────────── */
    .report-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }

    .report-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        -webkit-backdrop-filter: blur(4px);
        backdrop-filter: blur(4px);
    }

    .report-card {
        position: relative;
        z-index: 1;
        width: 90%;
        max-width: 400px;
        padding: 1.5rem;
        animation: overlayPop 0.25s ease;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .report-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        font-family: 'Barlow Condensed', sans-serif;
    }

    .report-close {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0.25rem;
        transition: color 0.2s;
    }

    .report-close:hover { color: var(--text-primary); }

    .report-meta {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .report-textarea {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        padding: 0.75rem;
        color: var(--text-primary);
        font-size: 0.875rem;
        resize: vertical;
        margin-bottom: 0.75rem;
        transition: border-color 0.2s;
        font-family: inherit;
    }

    .report-textarea:focus {
        outline: none;
        border-color: rgba(0, 85, 204, 0.4);
    }

    .report-textarea::placeholder { color: var(--text-muted); }

    html.light-mode .report-textarea {
        background: rgba(0, 0, 0, 0.03);
        border-color: rgba(0, 0, 0, 0.12);
        color: #1e293b;
    }

    .report-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .report-feedback {
        margin-top: 0.75rem;
        font-size: 0.75rem;
        text-align: center;
        display: none;
    }

    /* ── Shake Animation ─────────────────────────── */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-6px); }
        80% { transform: translateX(6px); }
    }

    .shake { animation: shake 0.4s ease; }

    /* ── Empty State ─────────────────────────────── */
    .practice-empty {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .practice-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 0 30px rgba(34, 197, 94, 0.15);
    }

    /* ── Reduced Motion ──────────────────────────── */
    @media (prefers-reduced-motion: reduce) {
        .practice-card,
        .fs-overlay-card,
        .answer-opt,
        .action-submit {
            animation: none !important;
            transition: none !important;
        }
    }
</style>
@endpush

<!-- Practice Shell -->
<div class="practice-shell" id="practiceContainer">
    @if($question)
        @php
            $user = Auth::user();
            $bookmarked = is_array($user->bookmarked_questions ?? null)
                ? $user->bookmarked_questions
                : json_decode($user->bookmarked_questions ?? '[]', true);
            $isBookmarked = in_array($question->id, $bookmarked);
        @endphp

        <!-- Mobile Top Bar -->
        <div class="practice-topbar">
            <a href="{{ route('practice.menu') }}" class="topbar-back">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <div class="topbar-center">
                <span class="topbar-progress-text">{{ $progress }}/{{ $total }}</span>
                <svg class="topbar-progress-ring" viewBox="0 0 36 36">
                    <circle class="ring-track" cx="18" cy="18" r="14" fill="none" stroke-width="3"/>
                    <circle class="ring-fill" cx="18" cy="18" r="14" fill="none"
                            stroke="url(#thwGradMini)" stroke-width="3"
                            stroke-dasharray="87.96"
                            stroke-dashoffset="{{ 87.96 - (87.96 * ($progressPercent ?? 0) / 100) }}"/>
                    <defs>
                        <linearGradient id="thwGradMini" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#0055cc"/>
                            <stop offset="100%" stop-color="#5b9aff"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <div class="topbar-actions">
                <button type="button" class="bookmark-btn {{ $isBookmarked ? 'active' : '' }}" id="bookmarkBtnMobile"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                        onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" id="bookmarkIconMobile">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"
                              style="stroke: {{ $isBookmarked ? '#fbbf24' : 'rgba(255,255,255,0.4)' }}; fill: {{ $isBookmarked ? '#fbbf24' : 'none' }};"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Desktop Header -->
        <div class="practice-header">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                <div style="flex:1;">
                    <div class="practice-greeting">
                        @if(isset($mode))
                            @switch($mode)
                                @case('unsolved') Ungelöste Fragen @break
                                @case('failed') Fehlerwiederholung @break
                                @case('section') Lernabschnitt {{ session('practice_parameter') }} @break
                                @case('search') Suche: "{{ session('practice_parameter') }}" @break
                                @case('bookmarked') Gespeicherte Fragen @break
                                @case('spaced_repetition') Wiederholung @break
                                @default Alle Fragen
                            @endswitch
                        @endif
                    </div>
                    <div class="practice-title">Theorie üben</div>
                    <div class="practice-level-line">
                        <span>{{ $progress }}/{{ $total }} gemeistert &middot; {{ $progressPercent ?? 0 }}%</span>
                        <div class="practice-xp-bar">
                            <div class="practice-xp-fill" style="width: {{ $progressPercent ?? 0 }}%;"></div>
                        </div>
                    </div>
                </div>

                <button type="button"
                        class="bookmark-btn bookmark-btn-lg {{ $isBookmarked ? 'active' : '' }}"
                        title="{{ $isBookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen' }}"
                        id="bookmarkBtn"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                        onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" id="bookmarkIcon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"
                              style="stroke: {{ $isBookmarked ? '#fbbf24' : 'rgba(255,255,255,0.4)' }}; fill: {{ $isBookmarked ? '#fbbf24' : 'none' }};"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Desktop Badge Row -->
        <div class="practice-badges">
            @if(isset($isSpacedRepetition) && $isSpacedRepetition)
                <span class="p-badge p-badge--sr"><i class="bi bi-arrow-repeat"></i> Wiederholung</span>
            @endif

            @if(isset($difficultyInfo) && $difficultyInfo['level'] !== 'unknown')
                <span class="p-badge p-badge--{{ $difficultyInfo['level'] }}">
                    {{ $difficultyInfo['label'] }}
                    @if($difficultyInfo['percent'] !== null)
                        &middot; {{ $difficultyInfo['percent'] }}%
                    @endif
                </span>
            @endif

            @if(isset($totalInMode) && $totalInMode > 0)
                <span class="p-badge p-badge--progress">Frage {{ $currentInMode ?? 1 }}/{{ $totalInMode }}</span>
            @endif
        </div>

        <!-- Desktop Progress Bar -->
        <div class="practice-progress-bar">
            <div class="ppb-track">
                <div class="ppb-fill" style="width: {{ $progressPercent ?? 0 }}%;"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="practice-card" id="questionContent">
            <form method="POST" action="{{ route('practice.submit') }}" id="practiceForm">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <input type="hidden" name="answer_time_ms" id="answerTimeMs" value="0">

                @php
                    $answersOriginal = [
                        ['letter' => 'A', 'text' => $question->antwort_a],
                        ['letter' => 'B', 'text' => $question->antwort_b],
                        ['letter' => 'C', 'text' => $question->antwort_c],
                    ];

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

                <!-- Mobile Badges -->
                <div class="practice-mobile-badges">
                    @if(isset($isSpacedRepetition) && $isSpacedRepetition)
                        <span class="pm-badge pm-badge--sr"><i class="bi bi-arrow-repeat"></i> SR</span>
                    @endif
                    @if(isset($difficultyInfo) && $difficultyInfo['level'] !== 'unknown')
                        <span class="pm-badge pm-badge--{{ $difficultyInfo['level'] }}">{{ $difficultyInfo['label'] }}</span>
                    @endif
                </div>

                <!-- Question Meta -->
                <div class="question-meta">
                    <span class="question-meta-left">
                        ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}
                    </span>
                    <button type="button" onclick="openReportModal()" class="report-btn" title="Fehler melden">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </button>
                </div>

                <!-- Question Text -->
                <p class="question-text">{{ $question->frage }}</p>

                <!-- Answers -->
                <div class="answers-grid">
                    @foreach($answers as $index => $answer)
                        @php
                            $originalLetter = $answer['letter'];
                            $isCorrectAnswer = $solution->contains($originalLetter);
                            $isUserAnswer = isset($userAnswer) && $userAnswer->contains($originalLetter);

                            $stateClass = '';
                            $icon = '';

                            if (isset($isCorrect)) {
                                if ($isCorrectAnswer && $isUserAnswer) {
                                    $stateClass = 'answer-opt--correct';
                                    $icon = '✓';
                                } elseif ($isCorrectAnswer && !$isUserAnswer) {
                                    $stateClass = 'answer-opt--correct-missed';
                                    $icon = '✓';
                                } elseif (!$isCorrectAnswer && $isUserAnswer) {
                                    $stateClass = 'answer-opt--wrong';
                                    $icon = '✗';
                                } else {
                                    $stateClass = 'answer-opt--neutral';
                                }
                            }
                        @endphp

                        @if(isset($isCorrect))
                            <div class="answer-opt {{ $stateClass }}">
                                <span class="result-icon {{ $isUserAnswer ? ($isCorrectAnswer ? 'result-icon--correct' : 'result-icon--wrong') : '' }}">
                                    @if($isUserAnswer) {{ $icon }} @endif
                                </span>
                                <span class="answer-text">{{ $answer['text'] }}</span>
                                @if($isCorrectAnswer && !$isUserAnswer)
                                    <span style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:0.15rem 0.4rem;border-radius:0.25rem;background:rgba(34,197,94,0.15);color:#22c55e;flex-shrink:0;">
                                        Richtig
                                    </span>
                                @endif
                            </div>
                        @else
                            <label class="answer-opt" onclick="updateSelectionStyle(this)">
                                <input type="checkbox" name="answer[]" value="{{ $index }}"
                                       class="answer-check"
                                       onchange="updateSubmitButton()">
                                <span class="answer-text">{{ $answer['text'] }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>

                @if(isset($isCorrect))
                    <div class="result-summary">
                        <span class="{{ $isCorrect ? 'result-label--correct' : 'result-label--wrong' }}">
                            {{ $isCorrect ? 'Richtig beantwortet' : 'Falsch beantwortet' }}
                        </span>
                        <span style="font-size:0.75rem;color:var(--text-muted);font-family:'IBM Plex Mono',monospace;">
                            {{ $solution->join(', ') }}
                        </span>
                    </div>
                @endif

                <!-- Actions -->
                <div class="practice-actions">
                    @if(!isset($isCorrect))
                        <button type="submit" id="submitBtn" class="action-submit action-submit--primary" disabled>
                            Antwort absenden
                        </button>
                    @else
                        <a href="{{ route('practice.index') }}" class="action-submit action-submit--primary">
                            Nächste Frage
                        </a>
                    @endif
                    <a href="{{ route('practice.summary') }}" class="action-end">Lernen beenden</a>
                </div>
            </form>
        </div>

        <!-- Report Modal -->
        <div id="reportModal" class="report-overlay" onclick="if(event.target===this)closeReportModal()">
            <div class="report-backdrop"></div>
            <div class="report-card glass">
                <div class="report-header">
                    <h3>Fehler melden</h3>
                    <button type="button" onclick="closeReportModal()" class="report-close">&#x2715;</button>
                </div>
                <p class="report-meta">Frage {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</p>
                <textarea id="reportMessage" rows="3" maxlength="500"
                    class="report-textarea"
                    placeholder="Optionale Beschreibung des Fehlers (max. 500 Zeichen)"></textarea>
                <div class="report-actions">
                    <button type="button" onclick="closeReportModal()" class="btn-ghost btn-sm">Abbrechen</button>
                    <button type="button" onclick="submitReport()" id="reportSubmitBtn" class="btn-secondary btn-sm">Melden</button>
                </div>
                <p id="reportFeedback" class="report-feedback"></p>
            </div>
        </div>

        {{-- ═══ FULLSCREEN OVERLAYS ═══════════════════════ --}}

        @php
            $showGamification = isset($isCorrect) && $isCorrect && $gamificationResult && isset($gamificationResult['points_awarded']);

            $celebrations = [
                'Grandios!', 'Fantastisch!', 'Super!', 'Stark!',
                'Mega!', 'Klasse!', 'Volltreffer!', 'Genial!',
            ];
            $celebrationText = $celebrations[$question->id % count($celebrations)];
            $pointsAwarded = $showGamification ? ($gamificationResult['points_awarded'] ?? 0) : 0;
            $reason = $showGamification ? ($gamificationResult['reason'] ?? '') : '';

            if ($pointsAwarded >= 20) {
                $reasonText = str_contains($reason, 'Häufig falsche') ? 'Schwere Frage gelöst' : 'Mit Streak-Bonus';
            } else {
                $reasonText = $reason;
            }

            $masteryThreshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
            $showMastered = isset($questionProgress) && $questionProgress->consecutive_correct >= $masteryThreshold;
            $remaining = isset($questionProgress) ? $masteryThreshold - $questionProgress->consecutive_correct : $masteryThreshold;
            $showAlmostMastered = isset($questionProgress) && $questionProgress->consecutive_correct > 0 && $questionProgress->consecutive_correct < $masteryThreshold;

            // Check for special gamification events
            $hasLevelUp = $gamificationResult && isset($gamificationResult['level_up']) && $gamificationResult['level_up'];
            $hasAchievement = $gamificationResult && isset($gamificationResult['achievement']);
            $hasStreakMilestone = $gamificationResult && isset($gamificationResult['streak_milestone']);
            $newLevel = $hasLevelUp ? ($gamificationResult['new_level'] ?? null) : null;
            $achievementName = $hasAchievement ? ($gamificationResult['achievement']['name'] ?? '') : '';
            $achievementDesc = $hasAchievement ? ($gamificationResult['achievement']['description'] ?? '') : '';
            $streakDays = $hasStreakMilestone ? ($gamificationResult['streak_milestone'] ?? 0) : 0;
        @endphp

        {{-- Correct Answer Overlay --}}
        @if(isset($isCorrect) && $isCorrect)
            <div id="overlayCorrect" class="fs-overlay fs-overlay--correct">
                <div class="fs-overlay-card">
                    <span class="fs-icon"><i class="bi bi-check-circle-fill" style="color:#22c55e;"></i></span>
                    <div class="fs-title">{{ $celebrationText }}</div>
                    @if($showGamification && $pointsAwarded > 0)
                        <div class="fs-points">+{{ $pointsAwarded }} XP</div>
                        <div class="fs-detail">{{ $reasonText }}</div>
                    @endif
                    @if($showMastered)
                        <div class="fs-mastery-bar">
                            <div class="fs-mastery-text">Frage gemeistert!</div>
                        </div>
                    @elseif($showAlmostMastered)
                        <div class="fs-mastery-bar">
                            <div class="fs-mastery-text">Noch {{ $remaining }}x richtig</div>
                        </div>
                    @endif
                    <div class="fs-dismiss">Tippen zum Schliessen</div>
                </div>
            </div>
        @endif

        {{-- Wrong Answer Overlay --}}
        @if(isset($isCorrect) && !$isCorrect)
            <div id="overlayWrong" class="fs-overlay fs-overlay--wrong">
                <div class="fs-overlay-card">
                    <span class="fs-icon"><i class="bi bi-x-circle-fill" style="color:#ef4444;"></i></span>
                    <div class="fs-title">Nicht ganz</div>
                    <div class="fs-subtitle">Die richtigen Antworten sind markiert</div>
                    <div class="fs-dismiss">Tippen zum Schliessen</div>
                </div>
            </div>
        @endif

        {{-- Level Up Overlay --}}
        @if($hasLevelUp)
            <div id="overlayLevelUp" class="fs-overlay fs-overlay--levelup">
                <div class="fs-overlay-card">
                    <span class="fs-icon"><i class="bi bi-trophy-fill" style="color:#1a1a2e;"></i></span>
                    <div class="fs-title" style="color:#1a1a2e;">Level Up!</div>
                    <div class="fs-subtitle" style="color:rgba(26,26,46,0.7);">Du bist jetzt Level {{ $newLevel }}</div>
                    <div class="fs-dismiss" style="color:rgba(26,26,46,0.4);">Tippen zum Schliessen</div>
                </div>
            </div>
        @endif

        {{-- Achievement Overlay --}}
        @if($hasAchievement)
            <div id="overlayAchievement" class="fs-overlay fs-overlay--achievement">
                <div class="fs-overlay-card">
                    <span class="fs-icon"><i class="bi bi-award-fill" style="color:#a855f7;"></i></span>
                    <div class="fs-title">Achievement!</div>
                    <div class="fs-subtitle">{{ $achievementName }}</div>
                    @if($achievementDesc)
                        <div class="fs-detail">{{ $achievementDesc }}</div>
                    @endif
                    <div class="fs-dismiss">Tippen zum Schliessen</div>
                </div>
            </div>
        @endif

        {{-- Streak Milestone Overlay --}}
        @if($hasStreakMilestone)
            <div id="overlayStreak" class="fs-overlay fs-overlay--streak">
                <div class="fs-overlay-card">
                    <span class="fs-icon"><i class="bi bi-fire" style="color:#f59e0b;"></i></span>
                    <div class="fs-title">{{ $streakDays }} Tage Streak!</div>
                    <div class="fs-subtitle">Weiter so, du bist auf Kurs</div>
                    <div class="fs-dismiss">Tippen zum Schliessen</div>
                </div>
            </div>
        @endif

        <script>
            // Answer time measurement
            (function() {
                var questionStart = Date.now();
                var form = document.getElementById('practiceForm');
                if (form) {
                    form.addEventListener('submit', function() {
                        var elapsed = Date.now() - questionStart;
                        document.getElementById('answerTimeMs').value = elapsed;
                    });
                }
            })();

            // Selection style
            function updateSelectionStyle(label) {
                const checkbox = label.querySelector('input[type="checkbox"]');
                label.classList.toggle('selected', checkbox.checked);
            }

            // Submit button state
            function updateSubmitButton() {
                const checkboxes = document.querySelectorAll('input[name="answer[]"]');
                const submitBtn = document.getElementById('submitBtn');
                const checked = Array.from(checkboxes).some(cb => cb.checked);
                submitBtn.disabled = !checked;

                checkboxes.forEach(cb => {
                    cb.closest('.answer-opt').classList.toggle('selected', cb.checked);
                });
            }

            // Overlay management
            document.addEventListener('DOMContentLoaded', function() {
                var overlayQueue = [];
                var currentOverlay = null;

                function showNextOverlay() {
                    if (overlayQueue.length === 0) return;
                    currentOverlay = overlayQueue.shift();
                    var el = document.getElementById(currentOverlay.id);
                    if (!el) { showNextOverlay(); return; }

                    setTimeout(function() { el.classList.add('show'); }, currentOverlay.delay || 50);

                    // Trigger confetti if needed
                    if (currentOverlay.confetti && typeof window[currentOverlay.confetti] === 'function') {
                        setTimeout(function() { window[currentOverlay.confetti](); }, (currentOverlay.delay || 50) + 100);
                    }

                    // Auto-dismiss
                    var autoDismissTime = currentOverlay.duration || 2500;
                    var dismissTimer = setTimeout(function() { dismissOverlay(el); }, autoDismissTime);

                    // Click to dismiss
                    el.addEventListener('click', function handler() {
                        clearTimeout(dismissTimer);
                        dismissOverlay(el);
                        el.removeEventListener('click', handler);
                    });
                }

                function dismissOverlay(el) {
                    if (!el.classList.contains('show')) return;
                    el.classList.remove('show');
                    setTimeout(function() {
                        showNextOverlay();
                    }, 350);
                }

                // Build overlay queue based on what's available
                @if(isset($isCorrect) && $isCorrect)
                    overlayQueue.push({ id: 'overlayCorrect', delay: 50, duration: 2000 });
                @endif

                @if(isset($isCorrect) && !$isCorrect)
                    overlayQueue.push({ id: 'overlayWrong', delay: 50, duration: 2000 });
                    // Shake the card
                    var qc = document.getElementById('questionContent');
                    if (qc) {
                        qc.classList.add('shake');
                        setTimeout(function() { qc.classList.remove('shake'); }, 400);
                    }
                @endif

                @if($hasLevelUp)
                    overlayQueue.push({ id: 'overlayLevelUp', delay: 100, duration: 3500, confetti: 'triggerLevelUpConfetti' });
                @endif

                @if($hasAchievement)
                    overlayQueue.push({ id: 'overlayAchievement', delay: 100, duration: 3000, confetti: 'triggerAchievementConfetti' });
                @endif

                @if($hasStreakMilestone)
                    overlayQueue.push({ id: 'overlayStreak', delay: 100, duration: 3000, confetti: 'triggerLevelUpConfetti' });
                @endif

                // Start the queue
                if (overlayQueue.length > 0) {
                    showNextOverlay();
                }

                // Floating points for correct answers
                @if(isset($showGamification) && $showGamification && $pointsAwarded > 0)
                    setTimeout(function() {
                        if (typeof window.showFloatingPoints === 'function') {
                            window.showFloatingPoints(window.innerWidth / 2, window.innerHeight / 2 - 50, {{ $pointsAwarded }});
                        }
                    }, 600);
                @endif
            });

            // Report modal
            function openReportModal() {
                var modal = document.getElementById('reportModal');
                modal.style.display = 'flex';
                document.getElementById('reportMessage').value = '';
                document.getElementById('reportFeedback').style.display = 'none';
                document.getElementById('reportSubmitBtn').disabled = false;
            }

            function closeReportModal() {
                document.getElementById('reportModal').style.display = 'none';
            }

            function submitReport() {
                var btn = document.getElementById('reportSubmitBtn');
                var feedback = document.getElementById('reportFeedback');
                var message = document.getElementById('reportMessage').value.trim();

                btn.disabled = true;

                fetch('{{ route('practice.report-issue', $question->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ message: message || null }),
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    feedback.style.display = 'block';
                    if (data.success) {
                        feedback.style.color = '#22c55e';
                        feedback.textContent = data.message;
                        setTimeout(closeReportModal, 1500);
                    } else {
                        feedback.style.color = '#ef4444';
                        feedback.textContent = data.error || 'Fehler beim Senden.';
                        btn.disabled = false;
                    }
                })
                .catch(function() {
                    feedback.style.display = 'block';
                    feedback.style.color = '#ef4444';
                    feedback.textContent = 'Verbindungsfehler. Bitte erneut versuchen.';
                    btn.disabled = false;
                });
            }

            // Bookmark toggle
            function toggleBookmark(questionId, currentlyBookmarked) {
                var btnMobile = document.getElementById('bookmarkBtnMobile');
                var btnDesktop = document.getElementById('bookmarkBtn');
                var btn = window.innerWidth <= 640 ? btnMobile : btnDesktop;
                if (!btn) return;

                var formData = new FormData();
                formData.append('question_id', questionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                btn.style.opacity = '0.5';
                btn.disabled = true;

                fetch('{{ route("bookmarks.toggle") }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        var targetColor = data.is_bookmarked ? '#fbbf24' : 'rgba(255,255,255,0.4)';
                        var targetFill = data.is_bookmarked ? '#fbbf24' : 'none';

                        [btnMobile, btnDesktop].forEach(function(button) {
                            if (!button) return;
                            button.setAttribute('data-bookmarked', data.is_bookmarked ? 'true' : 'false');
                            button.setAttribute('onclick', 'toggleBookmark(' + questionId + ', ' + data.is_bookmarked + ')');
                            button.classList.toggle('active', data.is_bookmarked);

                            var path = button.querySelector('svg path');
                            if (path) {
                                path.style.stroke = targetColor;
                                path.style.fill = targetFill;
                            }
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Bookmark error:', error);
                })
                .finally(function() {
                    btn.style.opacity = '1';
                    btn.disabled = false;
                });
            }
        </script>
    @else
        <!-- No more questions -->
        <div class="practice-card">
            <div class="practice-empty">
                <div class="practice-empty-icon"><i class="bi bi-check-circle-fill"></i></div>
                <h2 style="font-size:1.5rem;font-weight:800;color:var(--text-primary);margin-bottom:0.25rem;font-family:'Barlow Condensed',sans-serif;">Geschafft!</h2>
                <p style="font-size:0.875rem;color:var(--text-secondary);margin-bottom:1.5rem;">Du hast alle Fragen in diesem Modus bearbeitet</p>
                <div style="display:flex;flex-direction:column;gap:0.75rem;max-width:300px;margin:0 auto;">
                    <a href="{{ route('practice.menu') }}" class="action-submit action-submit--primary">Zurück zum Menü</a>
                    <a href="{{ route('dashboard') }}" class="action-submit action-submit--gold">Dashboard</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
