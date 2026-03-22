@extends('layouts.app')

@section('title', $sectionName . ' - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
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

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    .section-progress-ring {
        width: 4.5rem;
        height: 4.5rem;
        flex-shrink: 0;
    }

    .section-progress-ring circle {
        fill: none;
        stroke-width: 4;
        stroke-linecap: round;
    }

    .section-progress-ring .bg {
        stroke: rgba(255,255,255,0.06);
    }

    html.light-mode .section-progress-ring .bg {
        stroke: rgba(0,0,0,0.06);
    }

    .mode-tile {
        display: block;
        text-decoration: none;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .mode-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        text-decoration: none;
    }

    .mode-tile--disabled {
        opacity: 0.4;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('practice.menu') }}" class="text-xs uppercase tracking-wider" style="color:var(--text-muted);text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.25rem;margin-bottom:0.5rem;">
            <i class="bi bi-chevron-left" style="font-size:0.625rem;"></i> Alle Lernabschnitte
        </a>
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Lernabschnitt {{ $section }}</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $sectionName }}</h1>
    </div>

    {{-- Stat Pills --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color: var(--warning); -webkit-text-fill-color: var(--warning);">{{ $unsolvedCount }}</div>
            <div class="gami-pill__label">Ungelöst</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color: var(--success); -webkit-text-fill-color: var(--success);">{{ $solvedCount }}</div>
            <div class="gami-pill__label">Gemeistert</div>
        </div>
        @if($srDueInSection > 0)
        <div class="gami-pill">
            <div class="gami-pill__value" style="color: #5b9aff; -webkit-text-fill-color: #5b9aff;">{{ $srDueInSection }}</div>
            <div class="gami-pill__label">SR fällig</div>
        </div>
        @endif
        <div class="gami-pill">
            <div class="gami-pill__value">{{ $totalInSection }}</div>
            <div class="gami-pill__label">Gesamt</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Fortschrittsbalken --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Fortschritt</span>
                <span style="font-size:1.25rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;color:var(--text-primary);">{{ $pct }}%</span>
            </div>
            <div style="height:6px;background:rgba(255,255,255,0.06);border-radius:3px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;border-radius:3px;background:{{ $pct >= 80 ? '#22c55e' : ($pct >= 50 ? '#0055cc' : '#ef4444') }};transition:width 0.6s ease-out;"></div>
            </div>
            <div class="text-xs mt-2" style="color:var(--text-muted);">{{ $solvedCount }} von {{ $totalInSection }} Fragen gemeistert</div>
        </div>

        {{-- SR Nudge --}}
        @if($srDueInSection > 0)
        <a href="{{ route('practice.section.sr', $section) }}" class="glass mode-tile" style="background:linear-gradient(135deg, rgba(0,51,127,0.15), rgba(91,154,255,0.08));border:1px solid rgba(91,154,255,0.15);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:2.5rem;height:2.5rem;border-radius:0.5rem;background:linear-gradient(135deg,#0055cc,#5b9aff);color:#fff;font-size:1rem;font-weight:800;display:flex;align-items:center;justify-content:center;font-family:'Barlow Condensed',sans-serif;flex-shrink:0;">{{ $srDueInSection }}</div>
                <div>
                    <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">SR-Wiederholungen fällig</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.15rem;">Spaced Repetition — jetzt wiederholen</div>
                </div>
                <i class="bi bi-arrow-right" style="margin-left:auto;color:#5b9aff;"></i>
            </div>
        </a>
        @endif

        {{-- Lernmodi --}}
        <div class="grid grid-cols-1 gap-3">
            {{-- Alle Fragen (Ungelöst + SR) --}}
            <a href="{{ route('practice.section', $section) }}" class="glass mode-tile {{ $unsolvedCount === 0 && $srDueInSection === 0 ? 'mode-tile--disabled' : '' }}">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;color:var(--text-primary);min-width:2.5rem;text-align:center;">{{ $unsolvedCount + $srDueInSection }}</div>
                    <div>
                        <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">Offene Fragen lernen</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.15rem;">SR-fällige + ungelöste Fragen</div>
                    </div>
                    <i class="bi bi-arrow-right" style="margin-left:auto;color:var(--text-muted);"></i>
                </div>
            </a>

            {{-- Nur Ungelöste --}}
            <a href="{{ route('practice.section.unsolved', $section) }}" class="glass mode-tile {{ $unsolvedCount === 0 ? 'mode-tile--disabled' : '' }}">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;color:var(--warning);min-width:2.5rem;text-align:center;">{{ $unsolvedCount }}</div>
                    <div>
                        <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">Nur Ungelöste</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.15rem;">Noch nicht gemeisterte Fragen</div>
                    </div>
                    <i class="bi bi-arrow-right" style="margin-left:auto;color:var(--text-muted);"></i>
                </div>
            </a>

            {{-- Gemeisterte wiederholen --}}
            <a href="{{ route('practice.section.solved', $section) }}" class="glass mode-tile {{ $solvedCount === 0 ? 'mode-tile--disabled' : '' }}">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;color:var(--success);min-width:2.5rem;text-align:center;">{{ $solvedCount }}</div>
                    <div>
                        <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">Gemeisterte wiederholen</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.15rem;">Bereits gelöste Fragen nochmal üben</div>
                    </div>
                    <i class="bi bi-arrow-right" style="margin-left:auto;color:var(--text-muted);"></i>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
