@extends('layouts.app')

@section('title', 'Contributor Dashboard')
@section('description', 'Übersicht: Fragen pflegen, Einreichungen sichten, Fehlermeldungen bearbeiten')

@push('styles')
<style>
/* =========================================================
   CONTRIBUTOR DASHBOARD — eigene, ergänzende Tokens
   Reuses .dash-container / .glass-* / .stat-pill / .bento-grid
   aus app.css. Hier nur dashboard-spezifische Helfer.
   ========================================================= */

.contrib-queue-row {
    display: flex; align-items: center; gap: 0.875rem;
    padding: 0.75rem 0.875rem; border-radius: 0.625rem;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background: transparent; text-decoration: none; color: inherit;
    transition: border-color 150ms ease, background 150ms ease, transform 150ms ease;
}
html.light-mode .contrib-queue-row { border-color: rgba(0, 51, 127, 0.08); }
.contrib-queue-row:hover {
    border-color: rgba(91, 154, 255, 0.35);
    background: rgba(255, 255, 255, 0.04);
    transform: translateX(2px);
}
html.light-mode .contrib-queue-row:hover { background: rgba(0, 51, 127, 0.03); }
.contrib-queue-row--urgent { border-color: rgba(239, 68, 68, 0.30); }
.contrib-queue-row--urgent:hover { border-color: rgba(239, 68, 68, 0.50); }
.contrib-queue-count {
    width: 42px; height: 42px; flex-shrink: 0; border-radius: 10px;
    display: grid; place-items: center;
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.125rem;
    letter-spacing: -0.02em;
}
.contrib-queue-count--red   { background: rgba(239, 68, 68, 0.14);  color: #ef4444; }
.contrib-queue-count--gold  { background: rgba(251, 191, 36, 0.14); color: #fbbf24; }
.contrib-queue-count--purp  { background: rgba(167, 139, 250, 0.14); color: #a78bfa; }
.contrib-queue-count--blue  { background: rgba(91, 154, 255, 0.14); color: #5b9aff; }
html.light-mode .contrib-queue-count--blue { color: var(--thw-blue); }
.contrib-queue-body { flex: 1; min-width: 0; }
.contrib-queue-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.125rem; }
.contrib-queue-sub { font-size: 0.75rem; color: var(--text-muted); }
.contrib-queue-arrow { color: var(--text-muted); font-size: 0.875rem; flex-shrink: 0; }

.contrib-section-label {
    display: inline-block; font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
}
.contrib-card-h {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.contrib-card-h-link {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em; color: #5b9aff;
    text-decoration: none;
}
html.light-mode .contrib-card-h-link { color: var(--thw-blue); }
.contrib-card-h-link:hover { color: var(--gold-start); }

/* Quick links list (sidebar) */
.contrib-quick-list { display: flex; flex-direction: column; gap: 0.5rem; }
.contrib-quick-link {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.625rem 0.875rem; border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.06);
    text-decoration: none; color: inherit;
    transition: border-color 150ms ease, background 150ms ease;
}
html.light-mode .contrib-quick-link { border-color: rgba(0, 51, 127, 0.08); }
.contrib-quick-link:hover {
    border-color: rgba(91, 154, 255, 0.4);
    background: rgba(91, 154, 255, 0.05);
}
.contrib-quick-link .bi { color: #5b9aff; font-size: 1rem; flex-shrink: 0; }
html.light-mode .contrib-quick-link .bi { color: var(--thw-blue); }
.contrib-quick-link__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
.contrib-quick-link__sub { font-size: 0.6875rem; color: var(--text-muted); }
.contrib-quick-link > div:first-of-type { flex: 1; min-width: 0; }
.contrib-quick-link > .bi-chevron-right { font-size: 0.75rem; color: var(--text-muted); }

/* Submission row */
.contrib-sub-row {
    display: flex; align-items: flex-start; gap: 0.75rem;
    padding: 0.625rem 0.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    text-decoration: none; color: inherit;
}
html.light-mode .contrib-sub-row { border-bottom-color: rgba(0, 51, 127, 0.08); }
.contrib-sub-row:last-child { border-bottom: 0; }
.contrib-sub-row:hover .contrib-sub-frage { color: #5b9aff; }
html.light-mode .contrib-sub-row:hover .contrib-sub-frage { color: var(--thw-blue); }
.contrib-sub-body { flex: 1; min-width: 0; }
.contrib-sub-frage {
    font-size: 0.8125rem; color: var(--text-primary); line-height: 1.35;
    font-weight: 500;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
}
.contrib-sub-meta {
    margin-top: 0.25rem;
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem;
    color: var(--text-muted); letter-spacing: 0.02em;
}
.contrib-status {
    flex-shrink: 0; padding: 0.15rem 0.5rem;
    border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    white-space: nowrap;
}
.contrib-status--pending  { background: rgba(91, 154, 255, 0.14); color: #5b9aff; }
.contrib-status--approved { background: rgba(34, 197, 94, 0.14);  color: #22c55e; }
.contrib-status--changed  { background: rgba(251, 191, 36, 0.14); color: #fbbf24; }
.contrib-status--rejected { background: rgba(239, 68, 68, 0.14);  color: #ef4444; }
html.light-mode .contrib-status--pending  { color: var(--thw-blue); }

/* Question rows (Top Falsch/Richtig) */
.contrib-q-row {
    display: flex; align-items: center; gap: 0.625rem;
    padding: 0.5rem 0.625rem; border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.06);
    text-decoration: none; color: inherit;
    margin-bottom: 0.375rem;
    transition: border-color 150ms ease;
}
html.light-mode .contrib-q-row { border-color: rgba(0, 51, 127, 0.08); }
.contrib-q-row:hover { border-color: rgba(91, 154, 255, 0.35); }
.contrib-q-row--urgent { border-color: rgba(239, 68, 68, 0.30); }
.contrib-q-num {
    font-family: 'IBM Plex Mono', monospace; font-size: 0.75rem;
    color: var(--text-muted); min-width: 2.5rem;
}
.contrib-q-body { flex: 1; min-width: 0; }
.contrib-q-frage { font-size: 0.75rem; color: var(--text-primary); font-weight: 600; line-height: 1.35; }
.contrib-q-sub { font-size: 0.625rem; color: var(--text-muted); margin-top: 0.125rem; }
.contrib-q-rate {
    font-family: 'IBM Plex Mono', monospace; font-weight: 700;
    font-size: 0.8125rem; flex-shrink: 0;
}

/* Empty state */
.contrib-empty {
    text-align: center; padding: 1.5rem 0.5rem;
    color: var(--text-muted); font-size: 0.875rem;
}
.contrib-empty .bi {
    display: block; font-size: 1.75rem; margin-bottom: 0.5rem;
    color: var(--text-muted); opacity: 0.6;
}
</style>
@endpush

@section('content')
@php
    $statusOrder = ['pending', 'approved', 'changed', 'rejected'];
@endphp

<div class="dash-container">
<div class="space-y-4">

    {{-- ── HEADER ───────────────────────────────── --}}
    <header class="dashboard-header">
        <h1 class="page-title">Contributor <span>Dashboard</span></h1>
        <p class="page-subtitle">Fragen pflegen, Einreichungen sichten und Fehlermeldungen bearbeiten.</p>
    </header>

    {{-- ── STAT PILLS ───────────────────────────── --}}
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-question-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['fragen'], 0, ',', '.') }}</div>
                <div class="stat-pill-label">Fragen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-plus-square"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['extraFragen'], 0, ',', '.') }}</div>
                <div class="stat-pill-label">Zusatz-Fragen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-mortarboard"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['lehrgaenge'], 0, ',', '.') }}</div>
                <div class="stat-pill-label">Lehrgänge</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-pencil-square"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['entwuerfe'], 0, ',', '.') }}</div>
                <div class="stat-pill-label">Entwürfe</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-inbox"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($submissionStats['pending'], 0, ',', '.') }}</div>
                <div class="stat-pill-label">Offene Vorschläge</div>
            </div>
        </div>
    </div>

    {{-- ── SECTION: HANDLUNGSBEDARF ─────────────── --}}
    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
        <h2 class="section-title">Handlungsbedarf</h2>
    </div>

    <div class="bento-grid">

        {{-- Priority Queue (2 von 3 Spalten) --}}
        <div class="glass-gold bento-2of3">
            <div class="contrib-card-h">
                <span class="contrib-section-label">Offene Aufgaben</span>
                @php $totalQueue = array_sum(array_column($handlungsbedarf, 'count')); @endphp
                @if($totalQueue > 0)
                    <span class="contrib-section-label" style="color: #ef4444;">
                        {{ $totalQueue }} offen
                    </span>
                @else
                    <span class="contrib-section-label" style="color: #22c55e;">Alles erledigt</span>
                @endif
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @forelse($handlungsbedarf as $row)
                    <a class="contrib-queue-row {{ $row['urgent'] ? 'contrib-queue-row--urgent' : '' }}"
                       href="{{ $row['link'] }}">
                        <div class="contrib-queue-count contrib-queue-count--{{ $row['variant'] }}">{{ $row['count'] }}</div>
                        <div class="contrib-queue-body">
                            <div class="contrib-queue-title">{{ $row['title'] }}</div>
                            <div class="contrib-queue-sub">{{ $row['sub'] }}</div>
                        </div>
                        <i class="bi bi-chevron-right contrib-queue-arrow"></i>
                    </a>
                @empty
                    <div class="contrib-empty">
                        <i class="bi bi-check-circle"></i>
                        Keine offenen Aufgaben
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Links (Sidebar) --}}
        <div class="glass-tl bento-third">
            <div class="contrib-card-h">
                <span class="contrib-section-label">Schnellzugriff</span>
            </div>

            <div class="contrib-quick-list">
                <a href="{{ route('admin.questions.index') }}" class="contrib-quick-link">
                    <i class="bi bi-question-circle"></i>
                    <div>
                        <div class="contrib-quick-link__title">Fragen pflegen</div>
                        <div class="contrib-quick-link__sub">Globaler Fragenpool</div>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('admin.extra-questions.index') }}" class="contrib-quick-link">
                    <i class="bi bi-plus-square"></i>
                    <div>
                        <div class="contrib-quick-link__title">Zusatz-Fragen</div>
                        <div class="contrib-quick-link__sub">Bearbeiten</div>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('admin.lehrgaenge.index') }}" class="contrib-quick-link">
                    <i class="bi bi-mortarboard"></i>
                    <div>
                        <div class="contrib-quick-link__title">Lehrgänge</div>
                        <div class="contrib-quick-link__sub">Fragen je Lehrgang</div>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('admin.issues.index') }}" class="contrib-quick-link">
                    <i class="bi bi-bug"></i>
                    <div>
                        <div class="contrib-quick-link__title">Fehlermeldungen</div>
                        <div class="contrib-quick-link__sub">Issues bearbeiten</div>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ── SECTION: EINGEREICHTE ZUSATZ-FRAGEN ─── --}}
    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
        <h2 class="section-title">Eingereichte Zusatz-Fragen</h2>
    </div>

    <div class="bento-grid">

        {{-- Status Übersicht --}}
        <div class="glass-blue bento-third">
            <div class="contrib-card-h">
                <span class="contrib-section-label">Status</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                <div style="display: flex; align-items: baseline; justify-content: space-between;">
                    <span style="font-size: 0.8125rem; color: var(--text-secondary);">Offen</span>
                    <span style="font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.25rem; color: #5b9aff;">
                        {{ $submissionStats['pending'] }}
                    </span>
                </div>
                <div style="display: flex; align-items: baseline; justify-content: space-between;">
                    <span style="font-size: 0.8125rem; color: var(--text-secondary);">Übernommen</span>
                    <span style="font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.25rem; color: #22c55e;">
                        {{ $submissionStats['approved'] + $submissionStats['changed'] }}
                    </span>
                </div>
                <div style="display: flex; align-items: baseline; justify-content: space-between;">
                    <span style="font-size: 0.8125rem; color: var(--text-secondary);">Abgelehnt</span>
                    <span style="font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.25rem; color: #ef4444;">
                        {{ $submissionStats['rejected'] }}
                    </span>
                </div>
                <div style="margin-top: 0.5rem; padding-top: 0.625rem; border-top: 1px solid rgba(255,255,255,0.08);">
                    <a href="{{ route('admin.extra-question-submissions.index') }}" class="contrib-card-h-link">
                        Alle Einreichungen →
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Submissions --}}
        <div class="glass-tl bento-2of3">
            <div class="contrib-card-h">
                <span class="contrib-section-label">Zuletzt eingereicht</span>
                <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'pending']) }}" class="contrib-card-h-link">
                    Offene →
                </a>
            </div>

            <div>
                @forelse($recentSubmissions as $s)
                    <a href="{{ route('admin.extra-question-submissions.show', $s['id']) }}" class="contrib-sub-row">
                        <div class="contrib-sub-body">
                            <div class="contrib-sub-frage">{{ $s['frage'] }}</div>
                            <div class="contrib-sub-meta">
                                {{ $s['user_name'] }} · {{ $s['typ'] }} · LA {{ $s['lernabschnitt'] }} · vor {{ $s['created_human'] }}
                            </div>
                        </div>
                        <span class="contrib-status contrib-status--{{ $s['status'] }}">{{ $s['status_label'] }}</span>
                    </a>
                @empty
                    <div class="contrib-empty">
                        <i class="bi bi-inbox"></i>
                        Noch keine Einreichungen
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── SECTION: FRAGEN-QUALITÄT ─────────────── --}}
    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
        <h2 class="section-title">Fragen-Qualität · letzte 30 Tage</h2>
    </div>

    <div class="bento-grid">

        {{-- Top falsch --}}
        <div class="glass-cyan bento-half">
            <div class="contrib-card-h">
                <span class="contrib-section-label">
                    <i class="bi bi-x-circle-fill" style="color: #ef4444; margin-right: 0.25rem;"></i>
                    Top 3 · am häufigsten falsch
                </span>
            </div>
            @forelse($topFalsch as $q)
                @php
                    $rateColor = $q['wrong_rate'] >= 80 ? '#ef4444' : ($q['wrong_rate'] >= 60 ? '#f59e0b' : '#fbbf24');
                    $rowUrgent = $q['wrong_rate'] >= 70;
                @endphp
                <a href="{{ route('admin.questions.edit', $q['id']) }}"
                   class="contrib-q-row {{ $rowUrgent ? 'contrib-q-row--urgent' : '' }}">
                    <div class="contrib-q-num">#{{ $q['nummer'] ?? $q['id'] }}</div>
                    <div class="contrib-q-body">
                        <div class="contrib-q-frage">{{ $q['frage'] }}</div>
                        <div class="contrib-q-sub">
                            {{ $q['lernabschnitt'] ? 'Abschnitt ' . $q['lernabschnitt'] . ' · ' : '' }}{{ $q['attempts'] }}×
                        </div>
                    </div>
                    <div class="contrib-q-rate" style="color: {{ $rateColor }};">
                        {{ number_format($q['wrong_rate'], 0, ',', '.') }} %
                    </div>
                </a>
            @empty
                <div class="contrib-empty">
                    Noch nicht genug Daten
                </div>
            @endforelse
        </div>

        {{-- Top richtig --}}
        <div class="glass-green bento-half">
            <div class="contrib-card-h">
                <span class="contrib-section-label">
                    <i class="bi bi-check-circle-fill" style="color: #22c55e; margin-right: 0.25rem;"></i>
                    Top 3 · am häufigsten richtig
                </span>
            </div>
            @forelse($topRichtig as $q)
                <a href="{{ route('admin.questions.edit', $q['id']) }}" class="contrib-q-row">
                    <div class="contrib-q-num">#{{ $q['nummer'] ?? $q['id'] }}</div>
                    <div class="contrib-q-body">
                        <div class="contrib-q-frage">{{ $q['frage'] }}</div>
                        <div class="contrib-q-sub">
                            {{ $q['lernabschnitt'] ? 'Abschnitt ' . $q['lernabschnitt'] . ' · ' : '' }}{{ $q['attempts'] }}×
                        </div>
                    </div>
                    <div class="contrib-q-rate" style="color: #22c55e;">
                        {{ number_format($q['correct_rate'], 0, ',', '.') }} %
                    </div>
                </a>
            @empty
                <div class="contrib-empty">
                    Noch nicht genug Daten
                </div>
            @endforelse
        </div>

    </div>

    {{-- ── SECTION: AKTUELLE FEHLERMELDUNGEN ──── --}}
    @if(count($recentIssues) > 0)
    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
        <h2 class="section-title">Aktuelle Fehlermeldungen</h2>
    </div>

    <div class="bento-grid">
        <div class="glass-tl bento-wide">
            <div class="contrib-card-h">
                <span class="contrib-section-label">Offen · zur Sichtung</span>
                <a href="{{ route('admin.issues.index') }}" class="contrib-card-h-link">Alle →</a>
            </div>

            <div>
                @foreach($recentIssues as $i)
                    <a href="{{ route('admin.issues.index') }}" class="contrib-sub-row">
                        <div class="contrib-sub-body">
                            <div class="contrib-sub-frage">
                                <i class="bi bi-bug-fill" style="color: #ef4444; margin-right: 0.25rem;"></i>
                                {{ $i['title'] }}
                                @if(!empty($i['frage']))
                                    <span style="color: var(--text-muted); font-weight: 400;">– {{ $i['frage'] }}</span>
                                @endif
                            </div>
                            <div class="contrib-sub-meta">
                                {{ $i['kind'] === 'lehrgang' ? 'Lehrgang' : 'Globale Frage' }} · vor {{ $i['created_human'] }}
                            </div>
                        </div>
                        <i class="bi bi-chevron-right" style="color: var(--text-muted); font-size: 0.875rem;"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
</div>
@endsection
