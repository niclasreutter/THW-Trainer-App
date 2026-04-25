@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('description', 'System-Puls, KPI und Handlungsbedarf auf einen Blick')

@push('styles')
<style>
/* =========================================================
   ADMIN DASHBOARD — Design 2026-04
   Scope: nur /admin Hauptseite. Nutzt vorhandene Tokens aus
   app.css (var(--thw-blue-glow), --glass-bg, --gold, …).
   ========================================================= */

/* Container */
.admin-root { width: 100%; max-width: 1280px; margin: 0 auto; }

/* Header */
.admin-root .admin-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;
}
.admin-root .admin-header__right { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

.admin-root .admin-page-title { margin: 0; }
.admin-root .admin-page-title__eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--text-muted); margin-bottom: 0.35rem;
}
.admin-root .admin-page-title__h1 {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 800; font-size: 2rem; line-height: 1.1;
    letter-spacing: -0.015em;
    color: #5b9aff; margin: 0 0 0.25rem;
}
html.light-mode .admin-root .admin-page-title__h1 { color: var(--thw-blue); }
.admin-root .admin-page-title__sub { font-size: 0.9375rem; color: var(--text-secondary); margin: 0; }

/* Segmented time range */
.admin-root .seg {
    display: inline-flex; padding: 3px; border-radius: 999px;
    background: var(--glass-bg); border: 1px solid rgba(255,255,255,0.06);
}
html.light-mode .admin-root .seg { border-color: rgba(0,51,127,0.08); background: #fff; }
.admin-root .seg button {
    padding: 0.375rem 0.75rem; border-radius: 999px; border: 0;
    background: transparent; color: var(--text-secondary);
    font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em; cursor: pointer;
    transition: background var(--transition-fast), color var(--transition-fast);
}
.admin-root .seg button.active { background: #5b9aff; color: #fff; }
html.light-mode .admin-root .seg button.active { background: var(--thw-blue); }

.admin-root .icon-btn-outline {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 0.875rem; border-radius: 0.5rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    color: var(--text-secondary); font-size: 0.8125rem; font-weight: 600;
    text-decoration: none; cursor: pointer;
    transition: border-color var(--transition-fast), color var(--transition-fast);
}
.admin-root .icon-btn-outline:hover { border-color: #5b9aff; color: var(--text-primary); }
.admin-root .icon-btn-outline .bi { color: #5b9aff; }
html.light-mode .admin-root .icon-btn-outline .bi { color: var(--thw-blue); }

/* System-Pulse bar */
.admin-root .sys-pulse {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;
    padding: 0.875rem 1rem; border-radius: 0.875rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    margin-bottom: 1.25rem;
}
.admin-root .pulse-item { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
.admin-root .pulse-item + .pulse-item { border-left: 1px solid rgba(255,255,255,0.06); padding-left: 0.875rem; }
html.light-mode .admin-root .pulse-item + .pulse-item { border-left-color: rgba(0,51,127,0.08); }
.admin-root .pulse-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
}
.admin-root .pulse-dot--ok   { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.25); animation: pulseDot 2.4s ease-in-out infinite; }
.admin-root .pulse-dot--warn { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.25); }
.admin-root .pulse-dot--err  { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.25); }
@keyframes pulseDot {
    0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,0.25); }
    50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
}
.admin-root .pulse-meta { min-width: 0; line-height: 1.2; }
.admin-root .pulse-label {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted);
    margin-bottom: 0.15rem;
}
.admin-root .pulse-value {
    font-family: 'Figtree', sans-serif; font-weight: 700; font-size: 0.875rem;
    color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.admin-root .pulse-sub { font-size: 0.6875rem; color: var(--text-muted); }

/* KPI row */
.admin-root .kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.75rem; margin-bottom: 1.25rem; }
.admin-root .kpi {
    position: relative; padding: 1rem 1.125rem;
    border-radius: 0.875rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    overflow: hidden;
}
html.light-mode .admin-root .kpi { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
.admin-root .kpi__label {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted);
    margin-bottom: 0.375rem;
}
.admin-root .kpi__value {
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.875rem;
    line-height: 1.05; letter-spacing: -0.015em; color: var(--text-primary);
}
.admin-root .kpi__value--blue { color: #5b9aff; }
html.light-mode .admin-root .kpi__value--blue { color: var(--thw-blue); }
.admin-root .kpi__value--ok { color: #22c55e; }
.admin-root .kpi__delta {
    margin-top: 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.1rem 0.5rem 0.12rem; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
}
.admin-root .kpi__delta--up   { color: #22c55e; background: rgba(34,197,94,0.12); }
.admin-root .kpi__delta--down { color: #ef4444; background: rgba(239,68,68,0.12); }
.admin-root .kpi__delta--flat { color: var(--text-muted); background: var(--glass-bg); border: 1px solid rgba(255,255,255,0.06); }
html.light-mode .admin-root .kpi__delta--flat { border-color: rgba(0,51,127,0.08); }
.admin-root .kpi__spark { position: absolute; right: 0.6rem; bottom: 0.6rem; width: 74px; height: 28px; opacity: 0.85; }

/* Two-column bento */
.admin-root .admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.admin-root .admin-grid--wide  { grid-template-columns: minmax(0,1.5fr) minmax(0,1fr); }

.admin-root .card {
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.875rem; padding: 1.25rem;
}
html.light-mode .admin-root .card { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }

.admin-root .card-h {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.admin-root .card-h-actions { display: flex; gap: 0.5rem; align-items: center; }
.admin-root .card-h-link {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em; color: #5b9aff;
    text-decoration: none;
}
html.light-mode .admin-root .card-h-link { color: var(--thw-blue); }
.admin-root .card-h-link:hover { color: var(--gold); }

.admin-root .section-label {
    display: inline-block; font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
}
html.light-mode .admin-root .section-label { color: #6b7280; }

/* Chart */
.admin-root .chart-wrap { position: relative; height: 180px; margin: 0 -0.25rem; }
.admin-root .chart-wrap canvas { display: block; width: 100%; height: 100%; }
.admin-root .chart-meta {
    display: flex; gap: 1.5rem; margin-top: 0.5rem; padding-top: 0.625rem;
    border-top: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap;
}
html.light-mode .admin-root .chart-meta { border-top-color: rgba(0,51,127,0.08); }
.admin-root .chart-meta > div { display: flex; flex-direction: column; gap: 0.125rem; }
.admin-root .chart-meta__label {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);
}
.admin-root .chart-meta__value {
    font-family: 'Figtree', sans-serif; font-weight: 700; font-size: 0.875rem;
    color: var(--text-primary);
}

/* Live feed */
.admin-root .feed {
    display: flex; flex-direction: column; max-height: 340px; overflow-y: auto;
    margin: -0.25rem; padding: 0 0.25rem;
}
.admin-root .feed-item {
    display: flex; gap: 0.75rem; align-items: flex-start;
    padding: 0.625rem 0.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
html.light-mode .admin-root .feed-item { border-bottom-color: rgba(0,51,127,0.08); }
.admin-root .feed-item:last-child { border-bottom: 0; }
.admin-root .feed-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: grid; place-items: center; font-size: 0.8125rem; flex-shrink: 0;
}
.admin-root .feed-icon--blue  { background: rgba(91,154,255,0.14); color: #5b9aff; }
.admin-root .feed-icon--green { background: rgba(34,197,94,0.14);  color: #22c55e; }
.admin-root .feed-icon--gold  { background: rgba(251,191,36,0.14); color: #fbbf24; }
.admin-root .feed-icon--red   { background: rgba(239,68,68,0.14);  color: #ef4444; }
.admin-root .feed-icon--purple{ background: rgba(167,139,250,0.14); color: #a78bfa; }
html.light-mode .admin-root .feed-icon--blue { color: var(--thw-blue); }
.admin-root .feed-body { flex: 1; min-width: 0; }
.admin-root .feed-text { font-size: 0.8125rem; color: var(--text-primary); line-height: 1.4; }
.admin-root .feed-text strong { font-weight: 700; color: var(--text-primary); }
.admin-root .feed-text .muted { color: var(--text-muted); }
.admin-root .feed-meta { display: flex; gap: 0.625rem; margin-top: 0.25rem; align-items: center; }
.admin-root .feed-time {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    color: var(--text-muted); letter-spacing: 0.03em;
}
.admin-root .feed-tag {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    padding: 0.05rem 0.45rem; border-radius: 999px;
    background: var(--glass-bg); color: var(--text-muted);
    border: 1px solid rgba(255,255,255,0.06);
}
html.light-mode .admin-root .feed-tag { border-color: rgba(0,51,127,0.08); }

/* Handlungsbedarf queue */
.admin-root .queue-list { display: flex; flex-direction: column; gap: 0.5rem; }
.admin-root .queue-row {
    display: flex; align-items: center; gap: 0.875rem;
    padding: 0.75rem 0.875rem; border-radius: 0.625rem;
    border: 1px solid rgba(255,255,255,0.06);
    background: transparent; text-decoration: none; color: inherit;
    transition: border-color 150ms ease, background 150ms ease, transform 150ms ease;
}
html.light-mode .admin-root .queue-row { border-color: rgba(0,51,127,0.08); }
.admin-root .queue-row:hover {
    border-color: rgba(91,154,255,0.35);
    background: rgba(255,255,255,0.04);
    transform: translateX(2px);
}
html.light-mode .admin-root .queue-row:hover { background: rgba(0,51,127,0.03); }
.admin-root .queue-row--urgent { border-color: rgba(239,68,68,0.30); }
.admin-root .queue-row--urgent:hover { border-color: rgba(239,68,68,0.50); }
.admin-root .queue-count {
    width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px;
    display: grid; place-items: center;
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.125rem;
    letter-spacing: -0.02em;
}
.admin-root .queue-count--red   { background: rgba(239,68,68,0.14);  color: #ef4444; }
.admin-root .queue-count--blue  { background: rgba(91,154,255,0.14); color: #5b9aff; }
.admin-root .queue-count--gold  { background: rgba(251,191,36,0.14); color: #fbbf24; }
.admin-root .queue-count--purp  { background: rgba(167,139,250,0.14); color: #a78bfa; }
.admin-root .queue-count--green { background: rgba(34,197,94,0.14);  color: #22c55e; }
html.light-mode .admin-root .queue-count--blue { color: var(--thw-blue); }
.admin-root .queue-body { flex: 1; min-width: 0; }
.admin-root .queue-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.125rem; }
.admin-root .queue-sub { font-size: 0.75rem; color: var(--text-muted); }
.admin-root .queue-arrow { color: var(--text-muted); font-size: 0.875rem; flex-shrink: 0; }

/* Ortsverband list */
.admin-root .ov-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0; }
.admin-root .ov-item + .ov-item { border-top: 1px solid rgba(255,255,255,0.06); }
html.light-mode .admin-root .ov-item + .ov-item { border-top-color: rgba(0,51,127,0.08); }
.admin-root .ov-icon {
    width: 34px; height: 34px; border-radius: 8px;
    background: rgba(91,154,255,0.10); color: #5b9aff;
    display: grid; place-items: center; flex-shrink: 0;
}
html.light-mode .admin-root .ov-icon { color: var(--thw-blue); background: rgba(0,51,127,0.08); }
.admin-root .ov-body { flex: 1; min-width: 0; }
.admin-root .ov-name { font-size: 0.8125rem; font-weight: 600; color: var(--text-primary); }
.admin-root .ov-sub  { font-size: 0.6875rem; color: var(--text-muted); }
.admin-root .ov-count { font-family: 'Figtree', sans-serif; font-weight: 700; font-size: 0.9375rem; color: var(--text-primary); }

/* SR tiles & distribution */
.admin-root .sr-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.625rem; margin-bottom: 1rem; }
.admin-root .sr-tile {
    padding: 0.75rem; border-radius: 0.625rem;
    background: var(--glass-bg); border: 1px solid rgba(255,255,255,0.06);
    text-align: center;
}
html.light-mode .admin-root .sr-tile { border-color: rgba(0,51,127,0.08); background: rgba(255,255,255,0.8); }
.admin-root .sr-tile__val {
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.375rem;
    line-height: 1; color: var(--text-primary); margin-bottom: 0.25rem;
}
.admin-root .sr-tile__val--gold  { color: #fbbf24; }
.admin-root .sr-tile__val--green { color: #22c55e; }
.admin-root .sr-tile__val--purp  { color: #a78bfa; }
.admin-root .sr-tile__lbl {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);
}
.admin-root .sr-dist {
    display: flex; height: 12px; border-radius: 999px; overflow: hidden;
    margin-bottom: 0.625rem; background: rgba(255,255,255,0.06);
}
html.light-mode .admin-root .sr-dist { background: rgba(0,51,127,0.08); }
.admin-root .sr-dist > div { height: 100%; }
.admin-root .sr-dist-legend {
    display: flex; flex-wrap: wrap; gap: 0.875rem;
    font-size: 0.6875rem; color: var(--text-muted);
}
.admin-root .sr-dist-legend > div { display: inline-flex; align-items: center; gap: 0.35rem; }
.admin-root .sr-dist-legend .swatch { width: 8px; height: 8px; border-radius: 2px; }

/* Tweaks panel */
.admin-root .tweaks-panel {
    position: fixed; bottom: 5rem; right: 1rem; width: 280px; z-index: 50;
    background: var(--bg-elevated); border: 1px solid var(--glass-border);
    border-radius: 0.875rem; box-shadow: var(--shadow-float, 0 15px 40px rgba(0,0,0,0.4));
    padding: 1rem; display: none;
}
.admin-root .tweaks-panel.open { display: block; }
.admin-root .tweaks-panel h4 {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 0.75rem;
    color: var(--text-muted);
}
.admin-root .tweak-row { margin-bottom: 0.75rem; }
.admin-root .tweak-row label {
    display: block; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.375rem;
}
.admin-root .tweak-choices { display: flex; gap: 0.375rem; flex-wrap: wrap; }
.admin-root .tweak-choices button {
    flex: 1; padding: 0.4rem 0.5rem; border-radius: 0.5rem;
    border: 1px solid var(--glass-border); background: var(--glass-bg);
    color: var(--text-secondary); font-size: 0.75rem; font-weight: 600; cursor: pointer;
}
.admin-root .tweak-choices button.on { background: var(--thw-blue); color: #fff; border-color: var(--thw-blue); }

/* Density */
.admin-root.compact .kpi { padding: 0.75rem 0.875rem; }
.admin-root.compact .kpi__value { font-size: 1.5rem; }
.admin-root.compact .card { padding: 0.875rem; }
.admin-root.compact .chart-wrap { height: 150px; }

/* Responsive */
@media (max-width: 1100px) {
    .admin-root .kpi-grid { grid-template-columns: repeat(3, 1fr); }
    .admin-root .sys-pulse { grid-template-columns: repeat(2, 1fr); }
    .admin-root .pulse-item + .pulse-item:nth-child(3) { border-left: 0; padding-left: 0; }
    .admin-root .admin-grid, .admin-root .admin-grid--wide { grid-template-columns: 1fr; }
}
@media (max-width: 700px) {
    .admin-root .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    .admin-root .sys-pulse { grid-template-columns: 1fr; }
    .admin-root .pulse-item + .pulse-item {
        border-left: 0; padding-left: 0;
        border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.5rem;
    }
    html.light-mode .admin-root .pulse-item + .pulse-item { border-top-color: rgba(0,51,127,0.08); }
}

@media (prefers-reduced-motion: reduce) {
    .admin-root .pulse-dot--ok { animation: none; }
    .admin-root .queue-row { transition: none; }
}
</style>
@endpush

@section('content')
@php
    $ranges = ['24h' => '24 h', '7d' => '7 T', '30d' => '30 T', '90d' => '90 T'];
    $deltaClass = fn(string $dir) => match ($dir) {
        'up' => 'kpi__delta--up',
        'down' => 'kpi__delta--down',
        default => 'kpi__delta--flat',
    };
    $deltaIcon = fn(string $dir) => match ($dir) {
        'up' => 'bi-arrow-up',
        'down' => 'bi-arrow-down',
        default => '',
    };
@endphp

<div class="admin-root" id="admin-root" data-range="{{ $range }}">

    {{-- ── HEADER ───────────────────────────────── --}}
    <div class="admin-header">
        <div>
            <div class="admin-page-title__eyebrow">Administration</div>
            <h1 class="admin-page-title__h1">System Dashboard</h1>
            <p class="admin-page-title__sub">Übersicht über Nutzer, Inhalte und Systemzustand</p>
        </div>
        <div class="admin-header__right">
            <div class="seg" id="range-seg" role="tablist" aria-label="Zeitraum">
                @foreach($ranges as $key => $label)
                    <button type="button" data-range="{{ $key }}" class="{{ $range === $key ? 'active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <button class="icon-btn-outline" id="refresh-btn" type="button" title="Aktualisieren">
                <i class="bi bi-arrow-clockwise"></i> Aktualisieren
            </button>
            <button class="icon-btn-outline" id="tweaks-btn" type="button" title="Ansicht">
                <i class="bi bi-sliders"></i> Ansicht
            </button>
        </div>
    </div>

    {{-- ── SYSTEM-PULS ─────────────────────────── --}}
    <div class="sys-pulse" role="status" aria-label="System-Status">
        @foreach($systemPulse as $item)
            <div class="pulse-item">
                <span class="pulse-dot pulse-dot--{{ $item['status'] }}"></span>
                <div class="pulse-meta">
                    <div class="pulse-label">{{ $item['label'] }}</div>
                    <div class="pulse-value">{{ $item['value'] }}</div>
                    <div class="pulse-sub">{{ $item['sub'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── KPI GRID ─────────────────────────────── --}}
    <div class="kpi-grid">
        @foreach($kpis as $key => $kpi)
            @php
                $valueColorClass = match($kpi['color'] ?? null) {
                    'blue' => 'kpi__value--blue',
                    'ok'   => 'kpi__value--ok',
                    default => '',
                };
            @endphp
            <div class="kpi">
                <div class="kpi__label">{{ $kpi['label'] }}</div>
                <div class="kpi__value {{ $valueColorClass }}">{{ $kpi['value'] }}</div>
                <span class="kpi__delta {{ $deltaClass($kpi['delta']['direction']) }}">
                    @if($deltaIcon($kpi['delta']['direction']))
                        <i class="bi {{ $deltaIcon($kpi['delta']['direction']) }}"></i>
                    @endif
                    {{ $kpi['delta']['text'] }}
                </span>
                <canvas class="kpi__spark" data-spark="{{ $key }}" data-points="{{ json_encode($kpi['spark']) }}"></canvas>
            </div>
        @endforeach
    </div>

    {{-- ── ROW 1: CHARTS + HANDLUNGSBEDARF ───── --}}
    <div class="admin-grid admin-grid--wide">

        {{-- Charts --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="card">
                <div class="card-h">
                    <span class="section-label">Aktive Nutzer · 14 T</span>
                    <span style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);">Ø {{ number_format($chart14d['avgActive'], 1, ',', '.') }} / Tag</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="chart-active"
                            data-series="{{ json_encode($chart14d['active']) }}"
                            data-labels="{{ json_encode($chart14d['labels']) }}"
                            data-color="#5b9aff"
                            data-fill="rgba(91,154,255,0.28)"></canvas>
                </div>
                <div class="chart-meta">
                    <div>
                        <span class="chart-meta__label">Spitze</span>
                        <span class="chart-meta__value">{{ $chart14d['peakActive'] }} · {{ $chart14d['peakActiveLabel'] }}</span>
                    </div>
                    <div>
                        <span class="chart-meta__label">Neue (14 T)</span>
                        <span class="chart-meta__value">+{{ $chart14d['newUsers14'] }}</span>
                    </div>
                    <div>
                        <span class="chart-meta__label">Retention</span>
                        <span class="chart-meta__value" style="color:#22c55e;">{{ $chart14d['retention'] }} %</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-h">
                    <span class="section-label">Beantwortete Fragen · 14 T</span>
                    <span style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);">Ø {{ number_format($chart14d['avgAnswered'], 0, ',', '.') }} / Tag</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="chart-answered"
                            data-series="{{ json_encode($chart14d['answered']) }}"
                            data-labels="{{ json_encode($chart14d['labels']) }}"
                            data-color="#22c55e"
                            data-fill="rgba(34,197,94,0.24)"></canvas>
                </div>
                <div class="chart-meta">
                    <div>
                        <span class="chart-meta__label">Spitze</span>
                        <span class="chart-meta__value">{{ $chart14d['peakAnswered'] }} · {{ $chart14d['peakAnsweredLabel'] }}</span>
                    </div>
                    <div>
                        <span class="chart-meta__label">Richtig</span>
                        <span class="chart-meta__value" style="color:#22c55e;">{{ number_format($chart14d['totalCorrect'], 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="chart-meta__label">Falsch</span>
                        <span class="chart-meta__value" style="color:#ef4444;">{{ number_format($chart14d['totalWrong'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Handlungsbedarf --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Handlungsbedarf</span>
                <div class="card-h-actions">
                    @php $totalQueue = array_sum(array_column($handlungsbedarf, 'count')); @endphp
                    @if($totalQueue > 0)
                        <span class="feed-tag" style="color:#ef4444;border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.08);">
                            {{ $totalQueue }} offen
                        </span>
                    @else
                        <span class="feed-tag" style="color:#22c55e;border-color:rgba(34,197,94,0.3);background:rgba(34,197,94,0.08);">Alles erledigt</span>
                    @endif
                </div>
            </div>
            <div class="queue-list">
                @forelse($handlungsbedarf as $row)
                    <a class="queue-row {{ $row['urgent'] ? 'queue-row--urgent' : '' }}" href="{{ $row['link'] }}">
                        <div class="queue-count queue-count--{{ $row['variant'] }}">{{ $row['count'] }}</div>
                        <div class="queue-body">
                            <div class="queue-title">{{ $row['title'] }}</div>
                            <div class="queue-sub">{{ $row['sub'] }}</div>
                        </div>
                        <i class="bi bi-chevron-right queue-arrow"></i>
                    </a>
                @empty
                    <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:0.875rem;">
                        Keine offenen Aufgaben
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── ROW 2: LIVE FEED + FRAGEN-QUALITÄT ── --}}
    <div class="admin-grid">

        {{-- Live Feed --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Aktivitäts-Feed · DSGVO-konform</span>
                <div class="card-h-actions">
                    <span class="feed-tag">
                        <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#22c55e;margin-right:4px;"></span> Anonym
                    </span>
                </div>
            </div>
            <div class="feed" id="live-feed">
                @forelse($liveFeed as $event)
                    <div class="feed-item">
                        <div class="feed-icon feed-icon--{{ $event['color'] }}">
                            <i class="bi {{ $event['icon'] }}"></i>
                        </div>
                        <div class="feed-body">
                            <div class="feed-text">{!! $event['text'] !!}</div>
                            <div class="feed-meta">
                                <span class="feed-time">vor {{ $event['time_human'] }}</span>
                                <span class="feed-tag">{{ $event['tag'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.875rem;">
                        Keine Aktivitäten in den letzten 72 Stunden
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Fragen-Qualität --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Fragen-Qualität</span>
                <a href="{{ route('admin.questions.index') }}" class="card-h-link">Fragen →</a>
            </div>

            <div style="margin-bottom: 1rem;">
                <div style="display:flex;justify-content:space-between;align-items:baseline;gap:0.5rem;margin-bottom:0.625rem;flex-wrap:nowrap;">
                    <div style="min-width: 0;">
                        <span style="font-family:'Figtree',sans-serif;font-weight:800;font-size:1.5rem;color:#22c55e;line-height:1;">{{ number_format($fragenQualitaet['correct'], 0, ',', '.') }}</span>
                        <span style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);margin-left:0.375rem;white-space:nowrap;">RICHTIG</span>
                    </div>
                    <div style="text-align:right;min-width:0;">
                        <span style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);margin-right:0.375rem;white-space:nowrap;">FALSCH</span>
                        <span style="font-family:'Figtree',sans-serif;font-weight:800;font-size:1.5rem;color:#ef4444;line-height:1;">{{ number_format($fragenQualitaet['wrong'], 0, ',', '.') }}</span>
                    </div>
                </div>
                @php
                    $sr = max($fragenQualitaet['totalAnswered'], 1);
                    $correctPct = ($fragenQualitaet['correct'] / $sr) * 100;
                    $wrongPct = 100 - $correctPct;
                @endphp
                <div style="display:flex;height:10px;border-radius:999px;overflow:hidden;background:rgba(255,255,255,0.06);margin-bottom:0.375rem;">
                    <div style="width:{{ $correctPct }}%;background:#22c55e;"></div>
                    <div style="width:{{ $wrongPct }}%;background:#ef4444;"></div>
                </div>
                <div style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);text-align:center;">
                    {{ number_format($fragenQualitaet['successRate'], 1, ',', '.') }} % Erfolgsrate
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin-bottom:1.25rem;">
                <div class="sr-tile">
                    <div class="sr-tile__val">{{ number_format($fragenQualitaet['totalFragen'], 0, ',', '.') }}</div>
                    <div class="sr-tile__lbl">Fragen gesamt</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--purp">{{ number_format($fragenQualitaet['totalAnswered'], 0, ',', '.') }}</div>
                    <div class="sr-tile__lbl">Antworten (Zeitraum)</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val" style="color:#fbbf24;">{{ $fragenQualitaet['entwuerfe'] }}</div>
                    <div class="sr-tile__lbl">Entwürfe</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <div style="display:flex;align-items:center;gap:0.375rem;margin-bottom:0.625rem;">
                        <i class="bi bi-check-circle-fill" style="color:#22c55e;font-size:0.8125rem;"></i>
                        <span class="section-label" style="font-size:0.625rem;">Top 3 · am häufigsten richtig</span>
                    </div>
                    @forelse($fragenQualitaet['topRichtig'] as $q)
                        <div class="queue-row" style="padding:0.5rem 0.625rem;margin-bottom:0.375rem;">
                            <div style="font-family:'IBM Plex Mono',monospace;font-size:0.75rem;color:var(--text-muted);min-width:2.5rem;">#{{ $q['nummer'] ?? $q['id'] }}</div>
                            <div class="queue-body">
                                <div style="font-size:0.75rem;color:var(--text-primary);font-weight:600;">{{ $q['frage'] }}</div>
                                <div style="font-size:0.625rem;color:var(--text-muted);">{{ $q['lernabschnitt'] ? 'Abschnitt ' . $q['lernabschnitt'] . ' · ' : '' }}{{ $q['attempts'] }}×</div>
                            </div>
                            <div style="font-family:'IBM Plex Mono',monospace;font-weight:700;color:#22c55e;font-size:0.8125rem;">{{ number_format($q['correct_rate'], 0, ',', '.') }} %</div>
                        </div>
                    @empty
                        <div style="color:var(--text-muted);font-size:0.75rem;padding:0.75rem 0;">Noch nicht genug Daten</div>
                    @endforelse
                </div>

                <div>
                    <div style="display:flex;align-items:center;gap:0.375rem;margin-bottom:0.625rem;">
                        <i class="bi bi-x-circle-fill" style="color:#ef4444;font-size:0.8125rem;"></i>
                        <span class="section-label" style="font-size:0.625rem;">Top 3 · am häufigsten falsch</span>
                    </div>
                    @forelse($fragenQualitaet['topFalsch'] as $q)
                        @php
                            $rateColor = $q['wrong_rate'] >= 80 ? '#ef4444' : ($q['wrong_rate'] >= 60 ? '#f59e0b' : '#fbbf24');
                            $rowClass  = $q['wrong_rate'] >= 70 ? 'queue-row queue-row--urgent' : 'queue-row';
                        @endphp
                        <div class="{{ $rowClass }}" style="padding:0.5rem 0.625rem;margin-bottom:0.375rem;">
                            <div style="font-family:'IBM Plex Mono',monospace;font-size:0.75rem;color:var(--text-muted);min-width:2.5rem;">#{{ $q['nummer'] ?? $q['id'] }}</div>
                            <div class="queue-body">
                                <div style="font-size:0.75rem;color:var(--text-primary);font-weight:600;">{{ $q['frage'] }}</div>
                                <div style="font-size:0.625rem;color:var(--text-muted);">{{ $q['lernabschnitt'] ? 'Abschnitt ' . $q['lernabschnitt'] . ' · ' : '' }}{{ $q['attempts'] }}×</div>
                            </div>
                            <div style="font-family:'IBM Plex Mono',monospace;font-weight:700;color:{{ $rateColor }};font-size:0.8125rem;">{{ number_format($q['wrong_rate'], 0, ',', '.') }} %</div>
                        </div>
                    @empty
                        <div style="color:var(--text-muted);font-size:0.75rem;padding:0.75rem 0;">Noch nicht genug Daten</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- ── ROW 3: ORTSVERBÄNDE + SR ─────────── --}}
    <div class="admin-grid">

        {{-- Ortsverbände --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Ortsverbände</span>
                <a href="{{ route('admin.ortsverband.index') }}" class="card-h-link">Alle →</a>
            </div>
            <div class="kpi" style="padding:0.75rem 0.875rem;margin-bottom:0.75rem;background:rgba(255,255,255,0.04);">
                <div style="display:flex;gap:1.25rem;align-items:baseline;">
                    <div>
                        <div class="pulse-label">Aktiv</div>
                        <div style="font-family:'Figtree',sans-serif;font-weight:800;font-size:1.375rem;color:var(--text-primary);">{{ $ortsverbaende['summary']['active'] }}</div>
                    </div>
                    <div>
                        <div class="pulse-label">Nutzer</div>
                        <div style="font-family:'Figtree',sans-serif;font-weight:800;font-size:1.375rem;color:#5b9aff;">{{ $ortsverbaende['summary']['users'] }}</div>
                    </div>
                    <div>
                        <div class="pulse-label">Ø Nutzer / OV</div>
                        <div style="font-family:'Figtree',sans-serif;font-weight:800;font-size:1.375rem;color:var(--text-primary);">{{ $ortsverbaende['summary']['avg'] }}</div>
                    </div>
                </div>
            </div>
            @forelse($ortsverbaende['list'] as $ov)
                <div class="ov-item">
                    <div class="ov-icon"><i class="bi bi-building"></i></div>
                    <div class="ov-body">
                        <div class="ov-name">{{ $ov['name'] }}</div>
                        <div class="ov-sub">{{ $ov['members'] }} Nutzer · zuletzt aktiv {{ $ov['last_activity'] }}</div>
                    </div>
                    <div class="ov-count">{{ $ov['members'] }}</div>
                </div>
            @empty
                <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:0.875rem;">
                    Noch keine Ortsverbände mit Mitgliedern
                </div>
            @endforelse
        </div>

        {{-- Spaced Repetition --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Spaced Repetition</span>
                <a href="{{ route('admin.statistics') }}" class="card-h-link">Details →</a>
            </div>
            <div class="sr-stats">
                <div class="sr-tile">
                    <div class="sr-tile__val">{{ $srStats['active_users'] }}</div>
                    <div class="sr-tile__lbl">User aktiv</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--green">{{ number_format($srStats['mastered'], 0, ',', '.') }}</div>
                    <div class="sr-tile__lbl">Gemeistert</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--gold">{{ number_format($srStats['due_today'], 0, ',', '.') }}</div>
                    <div class="sr-tile__lbl">Fällig heute</div>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.375rem;">
                <span class="section-label" style="font-size:0.625rem;">Intervall-Verteilung</span>
                <span style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);">{{ number_format($srStats['cards_total'], 0, ',', '.') }} Karten</span>
            </div>
            <div class="sr-dist">
                <div style="width:{{ $srStats['dist_pct']['1_3'] }}%;background:#5b9aff;"></div>
                <div style="width:{{ $srStats['dist_pct']['4_7'] }}%;background:#22c55e;"></div>
                <div style="width:{{ $srStats['dist_pct']['8_14'] }}%;background:#fbbf24;"></div>
                <div style="width:{{ $srStats['dist_pct']['15_plus'] }}%;background:#a78bfa;"></div>
            </div>
            <div class="sr-dist-legend">
                <div><span class="swatch" style="background:#5b9aff;"></span>1–3 T · {{ $srStats['dist_pct']['1_3'] }}%</div>
                <div><span class="swatch" style="background:#22c55e;"></span>4–7 T · {{ $srStats['dist_pct']['4_7'] }}%</div>
                <div><span class="swatch" style="background:#fbbf24;"></span>8–14 T · {{ $srStats['dist_pct']['8_14'] }}%</div>
                <div><span class="swatch" style="background:#a78bfa;"></span>15+ T · {{ $srStats['dist_pct']['15_plus'] }}%</div>
            </div>

            <div style="margin-top:1rem;padding-top:0.875rem;border-top:1px solid rgba(255,255,255,0.06);display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div>
                    <div class="pulse-label">Mastery-Rate</div>
                    <div style="font-family:'Figtree',sans-serif;font-weight:700;font-size:1rem;color:#22c55e;">{{ number_format($srStats['mastery_rate'], 1, ',', '.') }} %</div>
                </div>
                <div>
                    <div class="pulse-label">Ø Easiness</div>
                    <div style="font-family:'Figtree',sans-serif;font-weight:700;font-size:1rem;color:var(--text-primary);">{{ number_format($srStats['avg_easiness'], 2, ',', '.') }}</div>
                </div>
                <div>
                    <div class="pulse-label">Fällig morgen</div>
                    <div style="font-family:'Figtree',sans-serif;font-weight:700;font-size:1rem;color:var(--text-primary);">{{ $srStats['due_tomorrow'] }}</div>
                </div>
                <div>
                    <div class="pulse-label">Diese Woche</div>
                    <div style="font-family:'Figtree',sans-serif;font-weight:700;font-size:1rem;color:var(--text-primary);">{{ $srStats['due_this_week'] }}</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── TWEAKS PANEL ─────────────────────────── --}}
    <div class="tweaks-panel" id="tweaks-panel">
        <h4>Ansicht</h4>
        <div class="tweak-row">
            <label>Dichte</label>
            <div class="tweak-choices" id="density-choices">
                <button type="button" data-density="cozy" class="on">Cozy</button>
                <button type="button" data-density="compact">Kompakt</button>
            </div>
        </div>
        <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.5rem;">
            Theme-Wechsel über die Sidebar unten.
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function() {
    const root = document.getElementById('admin-root');
    if (!root) return;

    // ------- Sparklines -------
    function drawSpark(canvas) {
        const raw = canvas.getAttribute('data-points');
        if (!raw) return;
        let data;
        try { data = JSON.parse(raw); } catch (e) { return; }
        if (!Array.isArray(data) || data.length < 2) return;

        const key = canvas.getAttribute('data-spark');
        const colors = {
            users: '#5b9aff',
            wau: '#22c55e',
            answers: '#a78bfa',
            success: '#fbbf24',
            verified: '#5b9aff',
        };
        const color = colors[key] || '#5b9aff';

        const dpr = window.devicePixelRatio || 1;
        const w = canvas.offsetWidth, h = canvas.offsetHeight;
        if (w === 0 || h === 0) return;
        canvas.width = w * dpr; canvas.height = h * dpr;
        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        ctx.clearRect(0, 0, w, h);

        const min = Math.min(...data), max = Math.max(...data);
        const range = (max - min) || 1;
        const stepX = w / (data.length - 1);
        const y = i => h - 4 - ((data[i] - min) / range) * (h - 8);

        ctx.beginPath();
        ctx.moveTo(0, h);
        for (let i = 0; i < data.length; i++) ctx.lineTo(i * stepX, y(i));
        ctx.lineTo(w, h); ctx.closePath();
        const grad = ctx.createLinearGradient(0, 0, 0, h);
        grad.addColorStop(0, color + '55');
        grad.addColorStop(1, color + '00');
        ctx.fillStyle = grad; ctx.fill();

        ctx.beginPath();
        ctx.moveTo(0, y(0));
        for (let i = 1; i < data.length; i++) ctx.lineTo(i * stepX, y(i));
        ctx.lineWidth = 1.75;
        ctx.strokeStyle = color;
        ctx.lineJoin = 'round';
        ctx.stroke();
    }

    function renderSparks() {
        root.querySelectorAll('.kpi__spark').forEach(drawSpark);
    }

    // ------- Activity line charts -------
    function drawLineChart(canvas) {
        const series = JSON.parse(canvas.getAttribute('data-series') || '[]');
        const labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
        const stroke = canvas.getAttribute('data-color') || '#5b9aff';
        const fill   = canvas.getAttribute('data-fill')  || 'rgba(91,154,255,0.2)';
        if (!series.length) return;

        const dpr = window.devicePixelRatio || 1;
        const w = canvas.offsetWidth, h = canvas.offsetHeight;
        if (w === 0 || h === 0) return;
        canvas.width = w * dpr; canvas.height = h * dpr;
        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        ctx.clearRect(0, 0, w, h);

        const isLight = document.documentElement.classList.contains('light-mode');
        const gridColor = isLight ? 'rgba(0,51,127,0.08)' : 'rgba(255,255,255,0.06)';
        const axisText  = isLight ? '#64748b' : '#71717a';

        const padL = 34, padR = 12, padT = 12, padB = 22;
        const plotW = w - padL - padR, plotH = h - padT - padB;

        const maxV = Math.max(...series);
        const yStep = Math.max(1, Math.ceil((maxV * 1.15) / 4));
        const top = yStep * 4;

        ctx.font = '10px "IBM Plex Mono", monospace';
        ctx.fillStyle = axisText;
        ctx.textAlign = 'right'; ctx.textBaseline = 'middle';
        for (let i = 0; i <= 4; i++) {
            const y = padT + plotH - (i / 4) * plotH;
            ctx.strokeStyle = gridColor;
            ctx.beginPath();
            ctx.moveTo(padL, y); ctx.lineTo(w - padR, y); ctx.stroke();
            ctx.fillText(String(i * yStep), padL - 6, y);
        }

        ctx.textAlign = 'center'; ctx.textBaseline = 'top';
        const stepX = plotW / Math.max(labels.length - 1, 1);
        labels.forEach((lab, i) => {
            if (i % 3 === 0 || i === labels.length - 1) {
                ctx.fillText(lab, padL + i * stepX, padT + plotH + 6);
            }
        });

        const mapY = v => padT + plotH - (v / top) * plotH;
        const mapX = i => padL + i * stepX;

        ctx.beginPath();
        ctx.moveTo(mapX(0), padT + plotH);
        series.forEach((v, i) => ctx.lineTo(mapX(i), mapY(v)));
        ctx.lineTo(mapX(series.length - 1), padT + plotH);
        ctx.closePath();
        const g = ctx.createLinearGradient(0, padT, 0, padT + plotH);
        g.addColorStop(0, fill);
        g.addColorStop(1, fill.replace(/[\d.]+\)$/, '0)'));
        ctx.fillStyle = g; ctx.fill();

        ctx.beginPath();
        series.forEach((v, i) => i === 0 ? ctx.moveTo(mapX(i), mapY(v)) : ctx.lineTo(mapX(i), mapY(v)));
        ctx.lineWidth = 2; ctx.strokeStyle = stroke;
        ctx.lineJoin = 'round'; ctx.stroke();

        series.forEach((v, i) => {
            ctx.beginPath();
            ctx.arc(mapX(i), mapY(v), 2.5, 0, Math.PI * 2);
            ctx.fillStyle = stroke; ctx.fill();
        });
    }

    function renderCharts() {
        root.querySelectorAll('#chart-active, #chart-answered').forEach(drawLineChart);
    }

    // ------- Range segmented (reload with query) -------
    root.querySelectorAll('#range-seg button').forEach(btn => {
        btn.addEventListener('click', () => {
            const range = btn.getAttribute('data-range');
            const url = new URL(window.location.href);
            url.searchParams.set('range', range);
            window.location.href = url.toString();
        });
    });

    // ------- Refresh -------
    document.getElementById('refresh-btn')?.addEventListener('click', () => {
        window.location.reload();
    });

    // ------- Tweaks panel -------
    const panel = document.getElementById('tweaks-panel');
    document.getElementById('tweaks-btn')?.addEventListener('click', () => {
        panel.classList.toggle('open');
    });

    // Density
    const densityStored = localStorage.getItem('admin-density') || 'cozy';
    if (densityStored === 'compact') root.classList.add('compact');
    root.querySelectorAll('#density-choices button').forEach(b => {
        const isOn = b.getAttribute('data-density') === densityStored;
        b.classList.toggle('on', isOn);
        b.addEventListener('click', () => {
            root.querySelectorAll('#density-choices button').forEach(x => x.classList.remove('on'));
            b.classList.add('on');
            const mode = b.getAttribute('data-density');
            root.classList.toggle('compact', mode === 'compact');
            localStorage.setItem('admin-density', mode);
            renderSparks();
            renderCharts();
        });
    });

    // ------- Init -------
    function initAll() { renderSparks(); renderCharts(); }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
    window.addEventListener('resize', () => {
        requestAnimationFrame(() => { renderSparks(); renderCharts(); });
    });
})();
</script>
@endpush
