@extends('layouts.app')

@section('title', 'Contributor Dashboard')
@section('description', 'Fragen pflegen, Einreichungen sichten und Fehlermeldungen bearbeiten')

@push('styles')
<style>
/* =========================================================
   CONTRIBUTOR DASHBOARD — 1:1 Design wie Admin Dashboard
   Nutzt dieselben Tokens (var(--thw-blue-glow), --glass-bg, …)
   und dieselben Klassennamen — gescoped via .admin-root.
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

/* Status bar (analog System-Puls) */
.admin-root .sys-pulse {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;
    padding: 0.875rem 1rem; border-radius: 0.875rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    margin-bottom: 1.25rem;
}
.admin-root .pulse-item { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
.admin-root a.pulse-item { text-decoration: none; color: inherit; }
.admin-root .pulse-item--link { cursor: pointer; transition: color var(--transition-fast); }
.admin-root .pulse-item--link:hover .pulse-value { color: #5b9aff; }
.admin-root .pulse-item--link:hover .pulse-chevron { color: #5b9aff; transform: translateX(2px); }
html.light-mode .admin-root .pulse-item--link:hover .pulse-value { color: var(--thw-blue); }
html.light-mode .admin-root .pulse-item--link:hover .pulse-chevron { color: var(--thw-blue); }
.admin-root .pulse-chevron { margin-left: auto; color: var(--text-muted); font-size: 0.875rem; transition: transform var(--transition-fast), color var(--transition-fast); flex-shrink: 0; }
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

/* Feed (Submissions / Issues) */
.admin-root .feed {
    display: flex; flex-direction: column; max-height: 360px; overflow-y: auto;
    margin: -0.25rem; padding: 0 0.25rem;
}
.admin-root .feed-item {
    display: flex; gap: 0.75rem; align-items: flex-start;
    padding: 0.625rem 0.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    text-decoration: none; color: inherit;
}
html.light-mode .admin-root .feed-item { border-bottom-color: rgba(0,51,127,0.08); }
.admin-root .feed-item:last-child { border-bottom: 0; }
.admin-root a.feed-item:hover .feed-text strong { color: #5b9aff; }
html.light-mode .admin-root a.feed-item:hover .feed-text strong { color: var(--thw-blue); }
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
.admin-root .feed-text strong { font-weight: 700; color: var(--text-primary); transition: color var(--transition-fast); }
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
.admin-root .feed-tag--pending  { color: #5b9aff; border-color: rgba(91,154,255,0.30); background: rgba(91,154,255,0.08); }
.admin-root .feed-tag--approved { color: #22c55e; border-color: rgba(34,197,94,0.30);  background: rgba(34,197,94,0.08); }
.admin-root .feed-tag--changed  { color: #fbbf24; border-color: rgba(251,191,36,0.30); background: rgba(251,191,36,0.08); }
.admin-root .feed-tag--rejected { color: #ef4444; border-color: rgba(239,68,68,0.30);  background: rgba(239,68,68,0.08); }

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

/* Tiles */
.admin-root .sr-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.625rem; margin-bottom: 1rem; }
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
.admin-root .sr-tile__val--red   { color: #ef4444; }
.admin-root .sr-tile__val--blue  { color: #5b9aff; }
html.light-mode .admin-root .sr-tile__val--blue { color: var(--thw-blue); }
.admin-root .sr-tile__lbl {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);
}

/* Responsive */
@media (max-width: 1100px) {
    .admin-root .kpi-grid { grid-template-columns: repeat(3, 1fr); }
    .admin-root .sys-pulse { grid-template-columns: repeat(2, 1fr); }
    .admin-root .pulse-item + .pulse-item:nth-child(3) { border-left: 0; padding-left: 0; }
    .admin-root .admin-grid, .admin-root .admin-grid--wide { grid-template-columns: 1fr; }
    .admin-root .sr-stats { grid-template-columns: repeat(2, 1fr); }
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

    $statusLabels = [
        'pending'  => 'Offen',
        'approved' => 'Übernommen',
        'changed'  => 'Mit Änderungen',
        'rejected' => 'Abgelehnt',
    ];
@endphp

<div class="admin-root" id="admin-root">

    {{-- ── HEADER ───────────────────────────────── --}}
    <div class="admin-header">
        <div>
            <div class="admin-page-title__eyebrow">Contributor</div>
            <h1 class="admin-page-title__h1">Dashboard</h1>
            <p class="admin-page-title__sub">Fragen pflegen, Einreichungen sichten und Fehlermeldungen bearbeiten.</p>
        </div>
        <div class="admin-header__right">
            <button class="icon-btn-outline" id="refresh-btn" type="button" title="Aktualisieren">
                <i class="bi bi-arrow-clockwise"></i> Aktualisieren
            </button>
        </div>
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

    {{-- ── ROW 1: ZULETZT EINGEREICHT + HANDLUNGSBEDARF ─── --}}
    <div class="admin-grid admin-grid--wide">

        {{-- Recent Submissions --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Zuletzt eingereichte Zusatz-Fragen</span>
                <a href="{{ route('admin.extra-question-submissions.index') }}" class="card-h-link">Alle →</a>
            </div>
            <div class="feed">
                @forelse($recentSubmissions as $s)
                    <a href="{{ route('admin.extra-question-submissions.show', $s['id']) }}" class="feed-item">
                        <div class="feed-icon feed-icon--{{ $s['status'] === 'pending' ? 'blue' : ($s['status'] === 'approved' ? 'green' : ($s['status'] === 'changed' ? 'gold' : 'red')) }}">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <div class="feed-body">
                            <div class="feed-text">
                                <strong>{{ \Illuminate\Support\Str::limit($s['frage'], 80) }}</strong>
                                <span class="muted"> · {{ $s['typ'] }} · LA {{ $s['lernabschnitt'] }}</span>
                            </div>
                            <div class="feed-meta">
                                <span class="feed-time">vor {{ $s['created_human'] }}</span>
                                <span class="feed-tag feed-tag--{{ $s['status'] }}">{{ $s['status_label'] }}</span>
                                <span class="feed-time muted">{{ $s['user_name'] }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.875rem;">
                        Noch keine Einreichungen
                    </div>
                @endforelse
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

    {{-- ── ROW 2: EINREICHUNGS-STATUS + AKTUELLE FEHLERMELDUNGEN ── --}}
    <div class="admin-grid">

        {{-- Einreichungs-Status --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Einreichungs-Status</span>
                <a href="{{ route('admin.extra-question-submissions.index') }}" class="card-h-link">Verwalten →</a>
            </div>

            <div class="sr-stats">
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--blue">{{ $submissionStats['pending'] }}</div>
                    <div class="sr-tile__lbl">Offen</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--green">{{ $submissionStats['approved'] }}</div>
                    <div class="sr-tile__lbl">Übernommen</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--gold">{{ $submissionStats['changed'] }}</div>
                    <div class="sr-tile__lbl">Geändert</div>
                </div>
                <div class="sr-tile">
                    <div class="sr-tile__val sr-tile__val--red">{{ $submissionStats['rejected'] }}</div>
                    <div class="sr-tile__lbl">Abgelehnt</div>
                </div>
            </div>

            @php
                $totalSubs = max($submissionStats['total'], 1);
                $pctPending  = round(($submissionStats['pending'] / $totalSubs) * 100);
                $pctApproved = round(($submissionStats['approved'] / $totalSubs) * 100);
                $pctChanged  = round(($submissionStats['changed'] / $totalSubs) * 100);
                $pctRejected = round(($submissionStats['rejected'] / $totalSubs) * 100);
            @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.375rem;">
                <span class="section-label" style="font-size:0.625rem;">Verteilung</span>
                <span style="font-family:'IBM Plex Mono',monospace;font-size:0.6875rem;color:var(--text-muted);">{{ number_format($submissionStats['total'], 0, ',', '.') }} Einreichungen</span>
            </div>
            <div style="display:flex;height:12px;border-radius:999px;overflow:hidden;margin-bottom:0.625rem;background:rgba(255,255,255,0.06);">
                <div style="width:{{ $pctPending }}%;background:#5b9aff;"></div>
                <div style="width:{{ $pctApproved }}%;background:#22c55e;"></div>
                <div style="width:{{ $pctChanged }}%;background:#fbbf24;"></div>
                <div style="width:{{ $pctRejected }}%;background:#ef4444;"></div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:0.875rem;font-size:0.6875rem;color:var(--text-muted);">
                <div style="display:inline-flex;align-items:center;gap:0.35rem;"><span style="width:8px;height:8px;border-radius:2px;background:#5b9aff;"></span>Offen · {{ $pctPending }}%</div>
                <div style="display:inline-flex;align-items:center;gap:0.35rem;"><span style="width:8px;height:8px;border-radius:2px;background:#22c55e;"></span>Übernommen · {{ $pctApproved }}%</div>
                <div style="display:inline-flex;align-items:center;gap:0.35rem;"><span style="width:8px;height:8px;border-radius:2px;background:#fbbf24;"></span>Geändert · {{ $pctChanged }}%</div>
                <div style="display:inline-flex;align-items:center;gap:0.35rem;"><span style="width:8px;height:8px;border-radius:2px;background:#ef4444;"></span>Abgelehnt · {{ $pctRejected }}%</div>
            </div>
        </div>

        {{-- Aktuelle Fehlermeldungen --}}
        <div class="card">
            <div class="card-h">
                <span class="section-label">Aktuelle Fehlermeldungen</span>
                <a href="{{ route('admin.issues.index') }}" class="card-h-link">Alle →</a>
            </div>
            <div class="feed">
                @forelse($recentIssues as $i)
                    @php
                        $issueLink = $i['kind'] === 'lehrgang'
                            ? route('admin.lehrgang-issues.show', $i['id'])
                            : route('admin.issues.show', $i['id']);
                    @endphp
                    <a href="{{ $issueLink }}" class="feed-item">
                        <div class="feed-icon feed-icon--red">
                            <i class="bi bi-bug-fill"></i>
                        </div>
                        <div class="feed-body">
                            <div class="feed-text">
                                <strong>{{ $i['title'] }}</strong>
                                @if(!empty($i['frage']))
                                    <span class="muted"> – {{ $i['frage'] }}</span>
                                @endif
                            </div>
                            <div class="feed-meta">
                                <span class="feed-time">vor {{ $i['created_human'] }}</span>
                                <span class="feed-tag">{{ $i['kind'] === 'lehrgang' ? 'Lehrgang' : 'Frage' }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.875rem;">
                        Keine offenen Fehlermeldungen
                    </div>
                @endforelse
            </div>
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
            fragen: '#5b9aff',
            extra: '#22c55e',
            lehrgaenge: '#a78bfa',
            entwuerfe: '#fbbf24',
            vorschlaege: '#5b9aff',
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
        const pts = data.map((_, i) => [i * stepX, y(i)]);

        function smoothPath(ctx, points) {
            if (points.length < 2) return;
            ctx.moveTo(points[0][0], points[0][1]);
            if (points.length === 2) { ctx.lineTo(points[1][0], points[1][1]); return; }
            for (let i = 0; i < points.length - 1; i++) {
                const p0 = points[i - 1] || points[i];
                const p1 = points[i];
                const p2 = points[i + 1];
                const p3 = points[i + 2] || p2;
                const cp1x = p1[0] + (p2[0] - p0[0]) / 6;
                const cp1y = p1[1] + (p2[1] - p0[1]) / 6;
                const cp2x = p2[0] - (p3[0] - p1[0]) / 6;
                const cp2y = p2[1] - (p3[1] - p1[1]) / 6;
                ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, p2[0], p2[1]);
            }
        }

        ctx.beginPath();
        ctx.moveTo(0, h);
        ctx.lineTo(pts[0][0], pts[0][1]);
        smoothPath(ctx, pts);
        ctx.lineTo(w, h); ctx.closePath();
        const grad = ctx.createLinearGradient(0, 0, 0, h);
        grad.addColorStop(0, color + '55');
        grad.addColorStop(1, color + '00');
        ctx.fillStyle = grad; ctx.fill();

        ctx.beginPath();
        smoothPath(ctx, pts);
        ctx.lineWidth = 1.75;
        ctx.strokeStyle = color;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
        ctx.stroke();
    }

    function renderSparks() {
        root.querySelectorAll('.kpi__spark').forEach(drawSpark);
    }

    // ------- Refresh -------
    document.getElementById('refresh-btn')?.addEventListener('click', () => {
        window.location.reload();
    });

    // ------- Init -------
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderSparks);
    } else {
        renderSparks();
    }
    window.addEventListener('resize', () => {
        requestAnimationFrame(renderSparks);
    });
})();
</script>
@endpush
