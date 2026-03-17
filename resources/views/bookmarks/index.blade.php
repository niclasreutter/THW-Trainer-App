@extends('layouts.app')
@section('title', 'Lesezeichen - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Section Group ──────────────────────────── */
    .bm-section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        padding-left: 0.25rem;
    }

    .bm-section-num {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6875rem;
        font-weight: 700;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
    }

    html.light-mode .bm-section-num {
        background: rgba(0,51,127,0.08);
        color: #00337F;
    }

    .bm-section-name {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .bm-section-count {
        font-size: 0.625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        margin-left: auto;
    }

    /* ─── Question Item ──────────────────────────── */
    .bm-question {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0.875rem;
        text-decoration: none;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .bm-question:hover {
        background: rgba(255,255,255,0.03);
    }

    html.light-mode .bm-question:hover {
        background: rgba(0,0,0,0.03);
    }

    .bm-question + .bm-question {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .bm-question + .bm-question {
        border-top-color: rgba(0,0,0,0.06);
    }

    .bm-question__body {
        flex: 1;
        min-width: 0;
    }

    .bm-question__text {
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--text-primary);
        line-height: 1.5;
        margin-bottom: 0.25rem;
    }

    .bm-question__answer {
        font-size: 0.6875rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    .bm-question__answer strong {
        color: var(--text-secondary);
        font-weight: 600;
    }

    /* ─── Remove Button ──────────────────────────── */
    .bm-remove {
        flex-shrink: 0;
        width: 2rem;
        height: 2rem;
        border-radius: 0.375rem;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.15);
        color: #ef4444;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 150ms ease;
        padding: 0;
    }

    .bm-remove:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
    }

    .bm-remove svg {
        width: 14px;
        height: 14px;
    }

    /* ─── Empty State ────────────────────────────── */
    .bm-empty {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .bm-empty__icon {
        width: 3rem;
        height: 3rem;
        margin: 0 auto 1rem;
        border-radius: 0.75rem;
        background: rgba(0,85,204,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5b9aff;
        font-size: 1.25rem;
    }

    html.light-mode .bm-empty__icon {
        background: rgba(0,51,127,0.06);
        color: #00337F;
    }

    .bm-empty__title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
    }

    .bm-empty__desc {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }

    /* ─── Stagger Animation ──────────────────────── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > .space-y-4 > * {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }
    .dash-container > .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
    .dash-container > .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }
    .dash-container > .space-y-4 > *:nth-child(8) { animation-delay: 0.31s; }
    .dash-container > .space-y-4 > *:nth-child(9) { animation-delay: 0.35s; }
    .dash-container > .space-y-4 > *:nth-child(10) { animation-delay: 0.39s; }
    .dash-container > .space-y-4 > *:nth-child(11) { animation-delay: 0.43s; }
    .dash-container > .space-y-4 > *:nth-child(12) { animation-delay: 0.47s; }
    .dash-container > .space-y-4 > *:nth-child(13) { animation-delay: 0.51s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Sammlung</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Lesezeichen</h1>
        <p class="text-sm" style="color: var(--text-muted);">Deine Favoriten zum gezielten Wiederholen</p>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('success'))
    <div class="alert-compact glass-success" style="margin-bottom: 1rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('success') }}</div>
        </div>
        <button style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:var(--text-secondary);" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-compact glass-error" style="margin-bottom: 1rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:var(--text-secondary);" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if($questions->count() > 0)
        {{-- ── Stat Pills ── --}}
        <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
            <div class="gami-pill">
                <div class="gami-pill__value gami-pill__value--gold">{{ $questions->count() }}</div>
                <div class="gami-pill__label">Gespeichert</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value gami-pill__value--blue">{{ $grouped->count() }}</div>
                <div class="gami-pill__label">Abschnitte</div>
            </div>
        </div>

        <div class="space-y-4">

            {{-- ── Smart Action Card: Alle üben ── --}}
            <a href="{{ route('bookmarks.practice') }}" class="smart-action" style="display:block;text-decoration:none;">
                <div class="smart-action__label">Übungsmodus</div>
                <div class="smart-action__title">Alle {{ $questions->count() }} Lesezeichen üben</div>
                <div class="smart-action__desc">Starte eine gezielte Wiederholungs-Session mit deinen gespeicherten Fragen</div>
                <span class="smart-action__btn">
                    Jetzt üben
                    <i class="bi bi-arrow-right"></i>
                </span>
            </a>

            {{-- ── Grouped Questions by Lernabschnitt ── --}}
            @foreach($grouped as $section => $sectionQuestions)
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="bm-section-header">
                    @php
                        $sectionNum = preg_match('/^(\d+)/', $section, $m) ? $m[1] : '?';
                    @endphp
                    <div class="bm-section-num">{{ $sectionNum }}</div>
                    <div class="bm-section-name">{{ $section }}</div>
                    <div class="bm-section-count">{{ $sectionQuestions->count() }}</div>
                </div>

                @foreach($sectionQuestions as $question)
                <div class="bm-question">
                    <div class="bm-question__body">
                        <div class="bm-question__text">{{ Str::limit($question->frage, 180) }}</div>
                        <div class="bm-question__answer">
                            <strong>{{ $question->loesung }}:</strong>
                            @if($question->loesung === 'A')
                                {{ $question->antwort_a }}
                            @elseif($question->loesung === 'B')
                                {{ $question->antwort_b }}
                            @else
                                {{ $question->antwort_c }}
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('bookmarks.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <button type="submit" class="bm-remove" title="Entfernen">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endforeach

        </div>
    @else
        {{-- ── Empty State ── --}}
        <div class="glass p-4 bm-empty" style="border-radius:0.75rem;">
            <div class="bm-empty__icon">
                <i class="bi bi-bookmark"></i>
            </div>
            <h2 class="bm-empty__title">Noch keine Lesezeichen</h2>
            <p class="bm-empty__desc">
                Speichere Fragen während des Übens, um sie<br>später gezielt zu wiederholen.
            </p>
            <a href="{{ route('practice.menu') }}" class="smart-action__btn" style="display:inline-flex;text-decoration:none;background:linear-gradient(135deg, #0055cc, #5b9aff);padding:0.5rem 1rem;border-radius:0.5rem;">
                Zum Übungsmenü
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @endif

</div>
@endsection
