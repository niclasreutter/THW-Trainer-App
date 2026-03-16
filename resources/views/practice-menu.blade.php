@extends('layouts.app')

@section('title', 'Theorie lernen - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Smart Action Card ──────────────────────── */
    .smart-action {
        display: block;
        text-decoration: none;
        background: linear-gradient(135deg, #00337F, #004db3);
        border-radius: 0.75rem;
        padding: 1.125rem 1.25rem;
        position: relative;
        overflow: hidden;
        transition: transform 250ms ease, box-shadow 250ms ease;
    }

    .smart-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,51,127,0.25);
        text-decoration: none;
    }

    .smart-action::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(91,154,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .smart-action__label {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.5);
        font-weight: 700;
        margin-bottom: 0.35rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .smart-action__title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 0.2rem;
    }

    .smart-action__desc {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        margin-bottom: 0.75rem;
    }

    .smart-action__btn {
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

    /* ─── Section Items ──────────────────────────── */
    .section-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        text-decoration: none;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .section-item:hover {
        background: rgba(255,255,255,0.03);
        text-decoration: none;
    }

    html.light-mode .section-item:hover { background: rgba(0,0,0,0.03); }

    .section-item + .section-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .section-item + .section-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .section-num {
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

    .section-num--green { background: rgba(34,197,94,0.12); color: #22c55e; }
    .section-num--blue  { background: rgba(0,85,204,0.12); color: #5b9aff; }
    .section-num--red   { background: rgba(239,68,68,0.12); color: #ef4444; }

    html.light-mode .section-num--green { background: rgba(34,197,94,0.1); }
    html.light-mode .section-num--blue  { background: rgba(0,51,127,0.08); color: #00337F; }
    html.light-mode .section-num--red   { background: rgba(239,68,68,0.08); }

    .section-bar {
        height: 3px;
        background: rgba(255,255,255,0.06);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.35rem;
    }

    html.light-mode .section-bar { background: rgba(0,0,0,0.06); }

    .section-bar__fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.6s ease-out;
    }

    .section-pct {
        font-size: 0.75rem;
        font-weight: 700;
        color: #5b9aff;
        flex-shrink: 0;
        font-family: 'Barlow Condensed', sans-serif;
    }

    html.light-mode .section-pct { color: #00337F; }

    .section-arrow {
        color: var(--text-muted);
        font-size: 0.75rem;
        flex-shrink: 0;
        transition: color 150ms ease;
    }

    .section-item:hover .section-arrow { color: #5b9aff; }
    html.light-mode .section-item:hover .section-arrow { color: #00337F; }

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

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    /* ─── SR Nudge ───────────────────────────────── */
    .sr-nudge {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1rem;
        text-decoration: none;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .sr-nudge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,127,0.1);
        text-decoration: none;
    }

    .sr-nudge__badge {
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

    .sr-nudge__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .sr-nudge__desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Search Input ───────────────────────────── */
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
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header (identisch mit Statistics) ── --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold" style="color: var(--text-primary);">Theorie lernen</h1>
        <p class="text-sm" style="color: var(--text-muted);">Wähle deinen Lernmodus</p>
    </div>

    {{-- ── Stat Pills (gami-pill wie Statistics) ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color: var(--error); -webkit-text-fill-color: var(--error);">{{ $failedCount }}</div>
            <div class="gami-pill__label">Fehler</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color: var(--warning); -webkit-text-fill-color: var(--warning);">{{ $unsolvedCount }}</div>
            <div class="gami-pill__label">Ungelöst</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color: var(--success); -webkit-text-fill-color: var(--success);">{{ $solvedCount }}</div>
            <div class="gami-pill__label">Gemeistert</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value">{{ $totalQuestions }}</div>
            <div class="gami-pill__label">Gesamt</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- ── Smart Action Card ── --}}
        <a href="{{ $smartAction['route'] }}" class="smart-action">
            <div class="smart-action__label">{{ $smartAction['label'] }}</div>
            <div class="smart-action__title">{{ $smartAction['title'] }}</div>
            <div class="smart-action__desc">{{ $smartAction['desc'] }}</div>
            <span class="smart-action__btn">
                Jetzt starten
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>

        {{-- ── Training Mode Tiles ── --}}
        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('practice.all') }}" class="glass p-3 text-center block" style="border-radius:0.75rem;text-decoration:none;">
                <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;line-height:1;color:var(--text-primary);">{{ $totalQuestions }}</div>
                <div class="text-xs uppercase tracking-wider mt-1" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5rem;">Alle Fragen</div>
                <div class="mt-2" style="font-size:0.6875rem;color:#5b9aff;font-weight:600;">Starten →</div>
            </a>
            <a href="{{ route('practice.unsolved') }}" class="glass p-3 text-center block" style="border-radius:0.75rem;text-decoration:none;">
                <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;line-height:1;color:var(--warning);">{{ $unsolvedCount }}</div>
                <div class="text-xs uppercase tracking-wider mt-1" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5rem;">Ungelöste</div>
                <div class="mt-2" style="font-size:0.6875rem;color:#5b9aff;font-weight:600;">Starten →</div>
            </a>
            <a href="{{ route('failed.index') }}" class="glass p-3 text-center block" style="border-radius:0.75rem;text-decoration:none;">
                <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;line-height:1;color:var(--error);">{{ $failedCount }}</div>
                <div class="text-xs uppercase tracking-wider mt-1" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5rem;">Fehler</div>
                <div class="mt-2" style="font-size:0.6875rem;color:#5b9aff;font-weight:600;">Starten →</div>
            </a>
        </div>

        {{-- ── Spaced Repetition + Fragensuche ── --}}
        <div class="grid grid-cols-1 gap-2" style="{{ $spacedRepetitionDue > 0 ? 'grid-template-columns:1fr 1fr;' : '' }}">
            @if($spacedRepetitionDue > 0)
            <a href="{{ route('practice.spaced-repetition') }}" class="glass sr-nudge">
                <div class="sr-nudge__badge">{{ $spacedRepetitionDue }}</div>
                <div>
                    <div class="sr-nudge__title">Reviews fällig</div>
                    <div class="sr-nudge__desc">Jetzt wiederholen</div>
                </div>
            </a>
            @endif
            <form action="{{ route('practice.search') }}" method="GET" class="glass p-3" style="border-radius:0.75rem;">
                <div class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Fragensuche</div>
                <input type="text" name="search" class="pm-search-input" placeholder="Frage Nr. eingeben...">
            </form>
        </div>

        {{-- ── Lernabschnitte ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Lernabschnitte</span>
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
                <a href="{{ route('practice.section', $i) }}" class="section-item">
                    <div class="section-num section-num--{{ $colorClass }}">{{ $i }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.75rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $sectionNames[$i] }}</div>
                        <div class="section-bar">
                            <div class="section-bar__fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                        </div>
                    </div>
                    <span class="section-pct">{{ $pct }}%</span>
                    <i class="bi bi-chevron-right section-arrow"></i>
                </a>
            @endfor
        </div>

    </div>
</div>
@endsection
