@extends('layouts.app')

@section('title', 'Session abgeschlossen')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Layout ──────────────────────────────────── */
    .summary-container {
        max-width: 480px;
        margin: 0 auto;
        padding: 1.25rem 1rem;
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* ─── Header ──────────────────────────────────── */
    .summary-header {
        text-align: center;
        margin-bottom: 1rem;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .summary-icon--great {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        box-shadow: 0 0 24px rgba(34, 197, 94, 0.2);
    }

    .summary-icon--good {
        background: rgba(251, 191, 36, 0.15);
        color: #fbbf24;
        box-shadow: 0 0 24px rgba(251, 191, 36, 0.2);
    }

    .summary-icon--okay {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        box-shadow: 0 0 24px rgba(59, 130, 246, 0.2);
    }

    html.light-mode .summary-icon--great {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.12);
    }

    html.light-mode .summary-icon--good {
        background: rgba(251, 191, 36, 0.1);
        color: #d97706;
        box-shadow: 0 0 20px rgba(251, 191, 36, 0.12);
    }

    html.light-mode .summary-icon--okay {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.12);
    }

    .summary-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.1rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .summary-title {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'Barlow Condensed', sans-serif;
        margin-bottom: 0.25rem;
    }

    .summary-meta {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.6875rem;
        color: var(--text-muted);
        flex-wrap: wrap;
    }

    .summary-streak {
        color: #fbbf24;
    }

    html.light-mode .summary-streak {
        color: #d97706;
    }

    .summary-streak--zero {
        color: var(--text-muted);
    }

    /* ─── Accuracy Hero ───────────────────────────── */
    .accuracy-hero {
        text-align: center;
        margin-bottom: 1rem;
    }

    .accuracy-hero__value {
        font-size: 2.75rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        line-height: 1;
    }

    .accuracy-hero__value--great { color: #22c55e; }
    .accuracy-hero__value--good {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .accuracy-hero__value--okay { color: #3b82f6; }

    html.light-mode .accuracy-hero__value--great { color: #16a34a; }
    html.light-mode .accuracy-hero__value--good {
        background: linear-gradient(135deg, #d97706, #b45309);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    html.light-mode .accuracy-hero__value--okay { color: #2563eb; }

    .accuracy-hero__label {
        font-size: 0.5rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.5rem;
    }

    .accuracy-hero__track {
        height: 5px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 3px;
        overflow: hidden;
        max-width: 180px;
        margin: 0 auto;
    }

    html.light-mode .accuracy-hero__track {
        background: rgba(0, 0, 0, 0.08);
    }

    .accuracy-hero__fill {
        height: 100%;
        border-radius: 3px;
        transition: width 1.5s ease-out;
        width: 0;
    }

    .accuracy-hero__fill--great { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .accuracy-hero__fill--good { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
    .accuracy-hero__fill--okay { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .accuracy-hero__fill--low { background: linear-gradient(90deg, #ef4444, #dc2626); }

    /* ─── Stats Row ───────────────────────────────── */
    .summary-stats {
        display: flex;
        gap: 0.375rem;
        margin-bottom: 1rem;
    }

    .summary-stat {
        flex: 1;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.625rem;
        padding: 0.5rem 0.25rem;
        text-align: center;
    }

    .summary-stat__value {
        font-size: 1.125rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        line-height: 1;
    }

    .summary-stat__label {
        font-size: 0.4375rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-family: 'IBM Plex Mono', monospace;
        margin-top: 0.15rem;
    }

    /* ─── Actions ─────────────────────────────────── */
    .summary-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .summary-actions a {
        width: 100%;
        text-align: center;
    }

    /* ─── Stagger Animation ───────────────────────── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .summary-container > * {
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .summary-container > *:nth-child(1) { animation-delay: 0.03s; }
    .summary-container > *:nth-child(2) { animation-delay: 0.07s; }
    .summary-container > *:nth-child(3) { animation-delay: 0.11s; }
    .summary-container > *:nth-child(4) { animation-delay: 0.15s; }
    .summary-container > *:nth-child(5) { animation-delay: 0.19s; }

    @media (prefers-reduced-motion: reduce) {
        .summary-container > * { animation: none !important; }
        .accuracy-hero__fill { transition: none !important; }
    }
</style>
@endpush

@section('content')
<div class="summary-container">
    {{-- ── Header ── --}}
    <div class="summary-header">
        @php
            if ($accuracy >= 80) {
                $iconClass = 'summary-icon--great';
                $iconName = 'bi-rocket-takeoff-fill';
                $titleText = 'Stark!';
                $accClass = 'great';
            } elseif ($accuracy >= 50) {
                $iconClass = 'summary-icon--good';
                $iconName = 'bi-hand-thumbs-up-fill';
                $titleText = 'Gut gemacht';
                $accClass = 'good';
            } else {
                $iconClass = 'summary-icon--okay';
                $iconName = 'bi-arrow-up-circle-fill';
                $titleText = 'Weiter so';
                $accClass = $accuracy >= 30 ? 'okay' : 'low';
            }
        @endphp
        <div class="summary-icon {{ $iconClass }}">
            <i class="bi {{ $iconName }}"></i>
        </div>
        <div class="summary-label">Session abgeschlossen</div>
        <h1 class="summary-title">{{ $titleText }}</h1>
        <div class="summary-meta">
            <span>{{ $modeName }}</span>
            <span>&middot;</span>
            <span>{{ $durationMinutes }} Min.</span>
            <span>&middot;</span>
            @if($streak > 0)
                <span class="summary-streak"><i class="bi bi-fire"></i> {{ $streak }} Tage Streak</span>
            @else
                <span class="summary-streak--zero">Streak starten!</span>
            @endif
        </div>
    </div>

    {{-- ── Accuracy Hero ── --}}
    <div class="accuracy-hero">
        <div class="accuracy-hero__value accuracy-hero__value--{{ $accClass === 'low' ? 'okay' : $accClass }}" data-count="{{ $accuracy }}">0</div>
        <div class="accuracy-hero__label">Genauigkeit</div>
        <div class="accuracy-hero__track">
            <div class="accuracy-hero__fill accuracy-hero__fill--{{ $accClass }}" data-width="{{ $accuracy }}"></div>
        </div>
    </div>

    {{-- ── Stats Row ── --}}
    <div class="summary-stats">
        <div class="summary-stat">
            <div class="summary-stat__value" style="color: #fbbf24;" data-count="{{ $totalAnswered }}">0</div>
            <div class="summary-stat__label">Beantwortet</div>
        </div>
        <div class="summary-stat">
            <div class="summary-stat__value" style="color: #22c55e;" data-count="{{ $stats['correct'] }}">0</div>
            <div class="summary-stat__label">Richtig</div>
        </div>
        <div class="summary-stat">
            <div class="summary-stat__value" style="color: #f59e0b;" data-count="{{ $stats['points'] }}">0</div>
            <div class="summary-stat__label">Punkte</div>
        </div>
        <div class="summary-stat">
            <div class="summary-stat__value" style="color: #a855f7;" data-count="{{ $stats['mastered'] }}">0</div>
            <div class="summary-stat__label">Gemeistert</div>
        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="summary-actions">
        <a href="{{ route('practice.menu') }}" class="btn-primary">Weiter lernen</a>
        <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Counter animation
    document.querySelectorAll('[data-count]').forEach(function(el) {
        var target = parseInt(el.getAttribute('data-count'));
        if (reducedMotion) { el.textContent = target; return; }

        var duration = 1200;
        var start = performance.now();
        var suffix = el.closest('.accuracy-hero__value') ? '%' : '';

        function update(currentTime) {
            var elapsed = currentTime - start;
            var progress = Math.min(elapsed / duration, 1);
            var easeOut = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(target * easeOut) + suffix;
            if (progress < 1) requestAnimationFrame(update);
            else el.textContent = target + suffix;
        }

        setTimeout(function() { requestAnimationFrame(update); }, 300);
    });

    // Accuracy bar animation
    if (!reducedMotion) {
        setTimeout(function() {
            document.querySelectorAll('.accuracy-hero__fill').forEach(function(bar) {
                bar.style.width = bar.getAttribute('data-width') + '%';
            });
        }, 500);
    } else {
        document.querySelectorAll('.accuracy-hero__fill').forEach(function(bar) {
            bar.style.transition = 'none';
            bar.style.width = bar.getAttribute('data-width') + '%';
        });
    }
});
</script>
@endsection
