@extends('layouts.app')
@section('title', 'Fehlermeldungen - Admin')

@push('styles')
<style>
/* =========================================================
   ADMIN ISSUES — Design 2026-05
   Aligned with /admin und /admin/extra-questions Look
   ========================================================= */
.iss-root { width: 100%; max-width: 1280px; margin: 0 auto; }

/* Header */
.iss-root .iss-header { margin-bottom: 1.5rem; }
.iss-root .iss-eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--text-muted); margin-bottom: 0.35rem;
}
.iss-root .iss-h1 {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 800; font-size: 1.5rem; line-height: 1.2;
    background: linear-gradient(135deg, #5b9aff, #0055cc);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0 0 0.25rem;
}
.iss-root .iss-sub { font-size: 0.9375rem; color: var(--text-secondary); margin: 0; }

/* KPI grid */
.iss-root .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem; }
.iss-root .kpi {
    position: relative; padding: 1rem 1.125rem;
    border-radius: 0.875rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    overflow: hidden;
}
html.light-mode .iss-root .kpi { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
.iss-root .kpi__label {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted);
    margin-bottom: 0.375rem;
}
.iss-root .kpi__value {
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.875rem;
    line-height: 1.05; letter-spacing: -0.015em; color: var(--text-primary);
}
.iss-root .kpi__value--blue { color: #5b9aff; }
html.light-mode .iss-root .kpi__value--blue { color: var(--thw-blue); }
.iss-root .kpi__value--red   { color: #ef4444; }
.iss-root .kpi__value--gold  { color: #fbbf24; }
.iss-root .kpi__value--green { color: #22c55e; }

/* Card */
.iss-root .card {
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.875rem; padding: 1.25rem;
    margin-bottom: 1rem;
}
html.light-mode .iss-root .card { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
.iss-root .card-h {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.iss-root .section-label {
    display: inline-block; font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
}

/* Filter chips */
.iss-root .filter-row {
    display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;
}
.iss-root .filter-row + .filter-row { margin-top: 0.75rem; }
.iss-root .filter-row__label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted); min-width: 56px;
}
.iss-root .seg-link {
    padding: 0.4rem 0.85rem; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    background: var(--glass-bg); border: 1px solid rgba(255,255,255,0.06);
    color: var(--text-secondary); text-decoration: none;
    transition: border-color 150ms ease, color 150ms ease, background 150ms ease;
}
html.light-mode .iss-root .seg-link { border-color: rgba(0,51,127,0.08); background: #fff; }
.iss-root .seg-link:hover { color: var(--text-primary); border-color: rgba(91,154,255,0.4); }
.iss-root .seg-link.is-active { background: #5b9aff; color: #fff; border-color: transparent; }
html.light-mode .iss-root .seg-link.is-active { background: var(--thw-blue); }

/* Table */
.iss-root .iss-table-wrap { overflow-x: auto; margin: -0.25rem; padding: 0 0.25rem; }
.iss-root .iss-table { width: 100%; border-collapse: collapse; }
.iss-root .iss-table th {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--text-muted); padding: 0.625rem 0.75rem;
    text-align: left; border-bottom: 1px solid rgba(255,255,255,0.08);
}
html.light-mode .iss-root .iss-table th { border-bottom-color: rgba(0,51,127,0.08); }
.iss-root .iss-table th.t-right  { text-align: right; }
.iss-root .iss-table th.t-center { text-align: center; }
.iss-root .iss-table tbody tr {
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: background 0.15s ease;
}
html.light-mode .iss-root .iss-table tbody tr { border-bottom-color: rgba(0,51,127,0.06); }
.iss-root .iss-table tbody tr:hover { background: rgba(91,154,255,0.04); }
html.light-mode .iss-root .iss-table tbody tr:hover { background: rgba(0,51,127,0.03); }
.iss-root .iss-table td { padding: 0.875rem 0.75rem; vertical-align: middle; }

.iss-root .q-text {
    font-size: 0.9rem; font-weight: 600; color: var(--text-primary);
    line-height: 1.35; max-width: 380px;
}
.iss-root .q-meta {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.65rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--text-muted); margin-top: 0.3rem;
}

/* Chips */
.iss-root .chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.18rem 0.55rem; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    white-space: nowrap;
}
.iss-root .chip--lehrgang   { background: rgba(91,154,255,0.14); color: #5b9aff; }
html.light-mode .iss-root .chip--lehrgang { color: var(--thw-blue); background: rgba(0,51,127,0.10); }
.iss-root .chip--question   { background: rgba(167,139,250,0.14); color: #a78bfa; }
.iss-root .chip--count {
    background: rgba(91,154,255,0.18); color: #5b9aff; font-size: 0.7rem;
}
html.light-mode .iss-root .chip--count { color: var(--thw-blue); }
.iss-root .chip--open       { background: rgba(239,68,68,0.18);  color: #ef4444; }
.iss-root .chip--in_review  { background: rgba(251,191,36,0.18); color: #fbbf24; }
.iss-root .chip--resolved   { background: rgba(34,197,94,0.18);  color: #22c55e; }
.iss-root .chip--rejected   { background: rgba(255,255,255,0.10); color: var(--text-secondary); }

/* Pill action */
.iss-root .iss-link-btn {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.4rem 0.85rem; border-radius: 0.5rem;
    font-size: 0.8125rem; font-weight: 600;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    color: var(--text-secondary); text-decoration: none;
    transition: border-color 150ms ease, color 150ms ease;
}
.iss-root .iss-link-btn:hover { border-color: #5b9aff; color: var(--text-primary); }
html.light-mode .iss-root .iss-link-btn:hover { border-color: var(--thw-blue); }

/* Mobile cards */
.iss-root .iss-card-mobile { display: none; }
.iss-root .iss-card-mobile {
    padding: 0.875rem 1rem; border-radius: 0.75rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    text-decoration: none; color: inherit;
    transition: border-color 150ms ease, transform 150ms ease;
}
.iss-root .iss-card-mobile:hover { border-color: rgba(91,154,255,0.4); transform: translateX(2px); }
html.light-mode .iss-root .iss-card-mobile { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
.iss-root .iss-card-mobile + .iss-card-mobile { margin-top: 0.625rem; }

/* Empty state */
.iss-root .iss-empty {
    text-align: center; padding: 3rem 1.5rem;
}
.iss-root .iss-empty__icon {
    width: 56px; height: 56px; margin: 0 auto 1rem;
    border-radius: 999px;
    background: rgba(34,197,94,0.14); color: #22c55e;
    display: grid; place-items: center; font-size: 1.5rem;
}
.iss-root .iss-empty__text { font-size: 0.9375rem; color: var(--text-secondary); margin: 0; }

/* Responsive */
@media (max-width: 1100px) {
    .iss-root .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .iss-root .iss-table-wrap { display: none; }
    .iss-root .iss-card-mobile { display: block; }
    .iss-root .iss-h1 { font-size: 1.625rem; }
}
@media (max-width: 480px) {
    .iss-root .kpi-grid { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

@section('content')
<div class="iss-root">

    <div class="iss-header">
        <div class="iss-eyebrow">Administration · Qualität</div>
        <h1 class="iss-h1">Fehlermeldungen</h1>
        <p class="iss-sub">Alle Fehlermeldungen aus Lehrgängen und Grundausbildung</p>
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi__label">Gesamt</div>
            <div class="kpi__value kpi__value--blue">{{ $totalIssues }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Offen</div>
            <div class="kpi__value kpi__value--red">{{ $openIssues }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">In Bearbeitung</div>
            <div class="kpi__value kpi__value--gold">{{ $inReviewIssues }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Gelöst</div>
            <div class="kpi__value kpi__value--green">{{ $resolvedIssues }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-h">
            <span class="section-label">Filter</span>
        </div>
        <div class="filter-row">
            <span class="filter-row__label">Status</span>
            @foreach(['all' => 'Alle', 'open' => 'Offen', 'in_review' => 'In Bearbeitung', 'resolved' => 'Gelöst'] as $key => $label)
                <a href="{{ route('admin.issues.index', ['status' => $key, 'source' => $source, 'sort' => $sort]) }}"
                   class="seg-link {{ $status === $key ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="filter-row">
            <span class="filter-row__label">Quelle</span>
            @foreach(['all' => 'Alle', 'lehrgang' => 'Lehrgänge', 'question' => 'Grundausbildung'] as $key => $label)
                <a href="{{ route('admin.issues.index', ['status' => $status, 'source' => $key, 'sort' => $sort]) }}"
                   class="seg-link {{ $source === $key ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="filter-row">
            <span class="filter-row__label">Sortierung</span>
            @foreach(['recent' => 'Zuletzt aktualisiert', 'reports' => 'Meiste Meldungen'] as $key => $label)
                <a href="{{ route('admin.issues.index', ['status' => $status, 'source' => $source, 'sort' => $key]) }}"
                   class="seg-link {{ $sort === $key ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-h">
            <span class="section-label">Meldungen{{ $issues->count() ? ' · '.$issues->count() : '' }}</span>
        </div>

        @if($issues->count() > 0)
            <div class="iss-table-wrap">
                <table class="iss-table">
                    <thead>
                        <tr>
                            <th>Frage</th>
                            <th>Quelle</th>
                            <th class="t-center">Meldungen</th>
                            <th class="t-center">Status</th>
                            <th class="t-right">Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issues as $issue)
                            <tr>
                                <td>
                                    <div class="q-text">{{ Str::limit($issue->question_text, 70) }}</div>
                                    <div class="q-meta">ID #{{ $issue->question_id }}</div>
                                </td>
                                <td>
                                    <span class="chip chip--{{ $issue->type }}">
                                        {{ $issue->type === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}
                                    </span>
                                    @if($issue->context)
                                        <div class="q-meta">{{ $issue->context }}</div>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="chip chip--count">{{ $issue->report_count }}×</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="chip chip--{{ $issue->status }}">
                                        @if($issue->status === 'open') Offen
                                        @elseif($issue->status === 'in_review') In Bearbeitung
                                        @elseif($issue->status === 'resolved') Gelöst
                                        @else Abgelehnt
                                        @endif
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.issues.show', ['issue' => $issue->id, 'type' => $issue->type, 'status' => $status, 'source' => $source, 'sort' => $sort]) }}"
                                       class="iss-link-btn">
                                        Anschauen <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            @foreach($issues as $issue)
                <a href="{{ route('admin.issues.show', ['issue' => $issue->id, 'type' => $issue->type, 'status' => $status, 'source' => $source, 'sort' => $sort]) }}" class="iss-card-mobile">
                    <div style="display: flex; justify-content: space-between; gap: 0.75rem; align-items: flex-start; margin-bottom: 0.5rem;">
                        <div class="q-text">{{ Str::limit($issue->question_text, 60) }}</div>
                        <span class="chip chip--{{ $issue->status }}">
                            @if($issue->status === 'open') Offen
                            @elseif($issue->status === 'in_review') In Bearb.
                            @elseif($issue->status === 'resolved') Gelöst
                            @else Abgelehnt
                            @endif
                        </span>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                        <span class="chip chip--{{ $issue->type }}">
                            {{ $issue->type === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}
                        </span>
                        <span class="q-meta" style="margin-top: 0;">ID #{{ $issue->question_id }}</span>
                        <span class="chip chip--count" style="margin-left: auto;">{{ $issue->report_count }}×</span>
                    </div>
                </a>
            @endforeach
        @else
            <div class="iss-empty">
                <div class="iss-empty__icon"><i class="bi bi-check-circle"></i></div>
                <p class="iss-empty__text">Keine Fehlermeldungen in dieser Kategorie.</p>
            </div>
        @endif
    </div>
</div>
@endsection
