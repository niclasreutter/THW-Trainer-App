@extends('layouts.app')

@section('title', 'Theorie lernen - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Page Label ─────────────────────────────── */
    .pm-label {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #5b9aff;
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.625rem;
    }

    html.light-mode .pm-label { color: #00337F; }

    /* ─── Section Labels ─────────────────────────── */
    .pm-section-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Smart Action Card ──────────────────────── */
    .pm-smart-action {
        display: block;
        text-decoration: none;
        background: linear-gradient(135deg, #00337F, #004db3);
        border-radius: 0.75rem;
        padding: 1.125rem 1.25rem;
        position: relative;
        overflow: hidden;
        transition: transform 250ms ease, box-shadow 250ms ease;
    }

    .pm-smart-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,51,127,0.25);
        text-decoration: none;
    }

    .pm-smart-action::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(91,154,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .pm-smart-action__label {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.5);
        font-weight: 700;
        margin-bottom: 0.35rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pm-smart-action__title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 0.2rem;
    }

    .pm-smart-action__desc {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        margin-bottom: 0.75rem;
    }

    .pm-smart-action__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, #5b9aff, #0055cc);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.875rem;
        border-radius: 0.375rem;
    }

    /* ─── Stat Pill font override ────────────────── */
    .pm-container .stat-pill-value {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
    }

    /* ─── Mode Tiles ─────────────────────────────── */
    .pm-mode-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .pm-mode-grid > * {
        flex: 1 1 0;
        min-width: 90px;
    }

    .pm-mode-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.875rem 0.5rem;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        text-decoration: none;
        transition: transform 150ms ease, box-shadow 150ms ease, border-color 150ms ease;
        text-align: center;
    }

    .pm-mode-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,51,127,0.12);
        border-color: rgba(255,255,255,0.12);
        text-decoration: none;
    }

    html.light-mode .pm-mode-tile:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    .pm-mode-tile__count {
        font-size: 1.5rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .pm-mode-tile__label {
        font-size: 0.625rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 600;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.5rem;
    }

    .pm-mode-tile__action {
        font-size: 0.6875rem;
        color: #5b9aff;
        font-weight: 600;
    }

    html.light-mode .pm-mode-tile__action { color: #00337F; }

    /* ─── Split Row ──────────────────────────────── */
    .pm-split-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    @media (max-width: 400px) {
        .pm-split-row { grid-template-columns: 1fr; }
    }

    /* ─── SR Nudge ───────────────────────────────── */
    .pm-sr-nudge {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1rem;
        text-decoration: none;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .pm-sr-nudge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,127,0.1);
        text-decoration: none;
    }

    .pm-sr-nudge__badge {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #0055cc, #5b9aff);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Barlow Condensed', sans-serif;
        flex-shrink: 0;
    }

    .pm-sr-nudge__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .pm-sr-nudge__desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Search Card ────────────────────────────── */
    .pm-search-card { padding: 0.875rem 1rem; }

    .pm-search-input {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        color: var(--text-primary);
        font-family: inherit;
        outline: none;
        transition: border-color 150ms ease;
        margin-top: 0.5rem;
    }

    .pm-search-input::placeholder { color: var(--text-muted); }
    .pm-search-input:focus { border-color: rgba(91,154,255,0.4); }

    html.light-mode .pm-search-input {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.1);
    }

    /* ─── Sections Card ──────────────────────────── */
    .pm-sections-card { padding: 1rem; }

    .pm-section-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        text-decoration: none;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .pm-section-item:hover {
        background: rgba(255,255,255,0.03);
        text-decoration: none;
    }

    html.light-mode .pm-section-item:hover { background: rgba(0,0,0,0.03); }

    .pm-section-item + .pm-section-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .pm-section-item + .pm-section-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .pm-section-num {
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
    }

    .pm-section-num--green { background: rgba(34,197,94,0.12); color: #22c55e; }
    .pm-section-num--blue  { background: rgba(0,85,204,0.12); color: #5b9aff; }
    .pm-section-num--red   { background: rgba(239,68,68,0.12); color: #ef4444; }

    html.light-mode .pm-section-num--green { background: rgba(34,197,94,0.1); }
    html.light-mode .pm-section-num--blue  { background: rgba(0,51,127,0.08); color: #00337F; }
    html.light-mode .pm-section-num--red   { background: rgba(239,68,68,0.08); }

    .pm-section-info { flex: 1; min-width: 0; }

    .pm-section-name {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pm-section-bar {
        height: 3px;
        background: rgba(255,255,255,0.06);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.35rem;
    }

    html.light-mode .pm-section-bar { background: rgba(0,0,0,0.06); }

    .pm-section-bar__fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.6s ease-out;
    }

    .pm-section-pct {
        font-size: 0.75rem;
        font-weight: 700;
        color: #5b9aff;
        flex-shrink: 0;
        font-family: 'Barlow Condensed', sans-serif;
    }

    html.light-mode .pm-section-pct { color: #00337F; }

    .pm-section-arrow {
        color: var(--text-muted);
        font-size: 0.75rem;
        flex-shrink: 0;
        transition: color 150ms ease;
    }

    .pm-section-item:hover .pm-section-arrow { color: #5b9aff; }
    html.light-mode .pm-section-item:hover .pm-section-arrow { color: #00337F; }

    /* ─── Stagger Animation ──────────────────────── */
    @keyframes pm-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pm-container > * {
        animation: pm-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .pm-container > *:nth-child(1) { animation-delay: 0.03s; }
    .pm-container > *:nth-child(2) { animation-delay: 0.07s; }
    .pm-container > *:nth-child(3) { animation-delay: 0.11s; }
    .pm-container > *:nth-child(4) { animation-delay: 0.15s; }
    .pm-container > *:nth-child(5) { animation-delay: 0.19s; }
    .pm-container > *:nth-child(6) { animation-delay: 0.23s; }

    @media (prefers-reduced-motion: reduce) {
        .pm-container > * { animation: none; }
    }

    /* ─── Container ──────────────────────────────── */
    .pm-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .pm-container > * + * { margin-top: 0.75rem; }
</style>
@endpush

@section('content')
<div class="pm-container">

    {{-- ── Page Label ── --}}
    <div class="pm-label">Theorie lernen</div>

    {{-- ── Stat Pills ── --}}
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-value" style="color:var(--error);">{{ $failedCount }}</span>
            <span class="stat-pill-label">Fehlgeschlagen</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-value" style="color:var(--warning);">{{ $unsolvedCount }}</span>
            <span class="stat-pill-label">Ungelöst</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-value" style="color:var(--success);">{{ $solvedCount }}</span>
            <span class="stat-pill-label">Gemeistert</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-value">{{ $totalQuestions }}</span>
            <span class="stat-pill-label">Gesamt</span>
        </div>
    </div>

    {{-- ── Smart Action Card ── --}}
    <a href="{{ $smartAction['route'] }}" class="pm-smart-action">
        <div class="pm-smart-action__label">{{ $smartAction['label'] }}</div>
        <div class="pm-smart-action__title">{{ $smartAction['title'] }}</div>
        <div class="pm-smart-action__desc">{{ $smartAction['desc'] }}</div>
        <span class="pm-smart-action__btn">
            Jetzt starten
            <i class="bi bi-arrow-right"></i>
        </span>
    </a>

    {{-- ── Training Mode Tiles ── --}}
    <div class="pm-mode-grid">
        <a href="{{ route('practice.all') }}" class="pm-mode-tile">
            <div class="pm-mode-tile__count" style="color:var(--text-primary);">{{ $totalQuestions }}</div>
            <div class="pm-mode-tile__label">Alle Fragen</div>
            <div class="pm-mode-tile__action">Starten →</div>
        </a>
        <a href="{{ route('practice.unsolved') }}" class="pm-mode-tile">
            <div class="pm-mode-tile__count" style="color:var(--warning);">{{ $unsolvedCount }}</div>
            <div class="pm-mode-tile__label">Ungelöste</div>
            <div class="pm-mode-tile__action">Starten →</div>
        </a>
        <a href="{{ route('failed.index') }}" class="pm-mode-tile">
            <div class="pm-mode-tile__count" style="color:var(--error);">{{ $failedCount }}</div>
            <div class="pm-mode-tile__label">Fehler</div>
            <div class="pm-mode-tile__action">Starten →</div>
        </a>
    </div>

    {{-- ── Spaced Repetition + Fragensuche ── --}}
    <div class="pm-split-row">
        @if($spacedRepetitionDue > 0)
        <a href="{{ route('practice.spaced-repetition') }}" class="glass pm-sr-nudge">
            <div class="pm-sr-nudge__badge">{{ $spacedRepetitionDue }}</div>
            <div>
                <div class="pm-sr-nudge__title">Reviews fällig</div>
                <div class="pm-sr-nudge__desc">Jetzt wiederholen</div>
            </div>
        </a>
        @endif
        <form action="{{ route('practice.search') }}" method="GET"
              class="glass pm-search-card"
              style="{{ $spacedRepetitionDue <= 0 ? 'grid-column: 1 / -1;' : '' }}">
            <div class="pm-section-label">Fragensuche</div>
            <input type="text" name="search" class="pm-search-input" placeholder="Frage Nr. eingeben...">
        </form>
    </div>

    {{-- ── Lernabschnitte ── --}}
    <div class="glass pm-sections-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <span class="pm-section-label">Lernabschnitte</span>
            <span style="font-size:0.6875rem;color:var(--text-muted);">10 Abschnitte</span>
        </div>

        @for($i = 1; $i <= 10; $i++)
            @php
                $total = $sectionStats[$i]['total'];
                $solved = $sectionStats[$i]['solved'];
                $pct = $total > 0 ? round(($solved / $total) * 100) : 0;
                $colorClass = $pct >= 80 ? 'green' : ($pct >= 50 ? 'blue' : 'red');
                $barColor = $pct >= 80 ? '#22c55e' : ($pct >= 50 ? '#0055cc' : '#ef4444');
            @endphp
            <a href="{{ route('practice.section', $i) }}" class="pm-section-item">
                <div class="pm-section-num pm-section-num--{{ $colorClass }}">{{ $i }}</div>
                <div class="pm-section-info">
                    <div class="pm-section-name">{{ $sectionNames[$i] }}</div>
                    <div class="pm-section-bar">
                        <div class="pm-section-bar__fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                    </div>
                </div>
                <span class="pm-section-pct">{{ $pct }}%</span>
                <i class="bi bi-chevron-right pm-section-arrow"></i>
            </a>
        @endfor
    </div>

</div>
@endsection
