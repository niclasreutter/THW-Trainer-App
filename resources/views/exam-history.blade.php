@extends('layouts.app')

@section('title', 'Prüfungshistorie')
@section('description', 'Analysiere deine THW-Prüfungen: Ergebnisse, Trends und Schwachstellen im Überblick.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Trend Chart ─── */
    .eh-trend-chart {
        --chart-h: 160px;
        position: relative;
        box-sizing: content-box;
        height: var(--chart-h);
        display: flex;
        align-items: flex-end;
        gap: 6px;
        padding: 1.5rem 0;
    }

    .eh-trend-bar {
        flex: 1;
        min-width: 24px;
        border-radius: 4px 4px 0 0;
        position: relative;
        transition: height 1s ease-out, opacity 150ms;
        cursor: default;
    }

    .eh-trend-bar:hover { opacity: 0.85; }
    .eh-trend-bar--passed { background: linear-gradient(180deg, #22c55e, #16a34a); }
    .eh-trend-bar--failed { background: linear-gradient(180deg, #ef4444, #dc2626); }

    .eh-trend-bar__value {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        font-family: 'IBM Plex Mono', monospace;
    }

    .eh-trend-bar__date {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.5625rem;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .eh-trend-threshold {
        position: absolute;
        left: 0;
        right: 0;
        border-top: 2px dashed rgba(251,191,36,0.3);
        z-index: 1;
    }

    .eh-trend-threshold__label {
        position: absolute;
        right: 0;
        top: -14px;
        font-size: 0.5625rem;
        color: var(--gold);
        font-weight: 600;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Compare Row ─── */
    .eh-compare-row {
        display: flex;
        gap: 1.5rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(255,255,255,0.06);
        align-items: center;
        flex-wrap: wrap;
    }

    html.light-mode .eh-compare-row { border-top-color: rgba(0,0,0,0.06); }

    .eh-compare-value {
        font-size: 1.25rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .eh-compare-label {
        font-size: 0.5625rem;
        color: var(--text-muted);
        text-transform: uppercase;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Recommendation Items ─── */
    .eh-rec-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        text-decoration: none;
        transition: background 150ms, border-color 150ms;
    }

    .eh-rec-item:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
        text-decoration: none;
    }

    html.light-mode .eh-rec-item {
        background: rgba(0,51,127,0.03);
        border-color: rgba(0,51,127,0.06);
    }

    html.light-mode .eh-rec-item:hover {
        background: rgba(0,51,127,0.06);
    }

    .eh-rec-item + .eh-rec-item { margin-top: 0.5rem; }

    /* ─── Section Items ─── */
    .eh-section-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .eh-section-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .eh-section-item:hover { background: rgba(0,0,0,0.03); }

    .eh-section-item + .eh-section-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .eh-section-item + .eh-section-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    /* ─── Filter Pills ─── */
    .eh-filter-pills {
        display: flex;
        gap: 0.375rem;
        margin-bottom: 0.75rem;
    }

    .eh-filter-pill {
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        border: 1px solid var(--glass-border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 150ms;
        font-family: 'IBM Plex Mono', monospace;
    }

    .eh-filter-pill:hover {
        border-color: rgba(91,154,255,0.3);
        color: var(--text-secondary);
    }

    .eh-filter-pill.active {
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border-color: rgba(91,154,255,0.3);
    }

    html.light-mode .eh-filter-pill.active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.2);
    }

    /* ─── Exam Items ─── */
    .eh-exam-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.5rem;
        transition: background 150ms;
        border-radius: 0.375rem;
    }

    .eh-exam-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .eh-exam-item:hover { background: rgba(0,0,0,0.03); }

    .eh-exam-item + .eh-exam-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .eh-exam-item + .eh-exam-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .eh-exam-badge {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.6875rem;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
    }

    .eh-exam-badge--passed { background: rgba(34,197,94,0.12); color: #22c55e; }
    .eh-exam-badge--failed { background: rgba(239,68,68,0.12); color: #ef4444; }

    html.light-mode .eh-exam-badge--passed { background: rgba(34,197,94,0.1); }
    html.light-mode .eh-exam-badge--failed { background: rgba(239,68,68,0.08); }

    .eh-exam-status {
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        margin-left: auto;
        flex-shrink: 0;
    }

    .eh-exam-status--passed { background: rgba(34,197,94,0.12); color: #22c55e; }
    .eh-exam-status--failed { background: rgba(239,68,68,0.12); color: #ef4444; }

    /* ─── Stagger Animation ─── */
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

    @media (max-width: 480px) {
        .eh-compare-row { gap: 1rem; }
        .eh-trend-chart { --chart-h: 120px; gap: 4px; }
        .eh-trend-bar { min-width: 18px; }
        .eh-trend-bar__value { font-size: 0.5625rem; }
    }

    /* ─── Empfehlung Card ─── */
    .eh-action-card {
        display: block !important;
        background: linear-gradient(135deg, #00337F, #0055cc) !important;
        border-radius: 0.75rem !important;
        padding: 1.25rem !important;
        position: relative !important;
        overflow: hidden !important;
        color: #fff !important;
        text-decoration: none !important;
    }
    .eh-action-card:hover {
        text-decoration: none !important;
        color: #fff !important;
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $totalExams = $exams->count();
    $passedExams = $exams->where('is_passed', true)->count();
    $avgPercent = $totalExams > 0 ? round($exams->avg(fn($e) => ($e->correct_answers / 40) * 100)) : 0;
    $bestPercent = $totalExams > 0 ? round($exams->max(fn($e) => ($e->correct_answers / 40) * 100)) : 0;
    $passRate = $totalExams > 0 ? round(($passedExams / $totalExams) * 100) : 0;
@endphp

<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Auswertung</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Prüfungshistorie</h1>
        <p class="text-sm" style="color: var(--text-muted);">Analysiere deine Ergebnisse und finde Schwachstellen</p>
    </div>

    {{-- ── Gami Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $totalExams }}</div>
            <div class="gami-pill__label">Prüfungen</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--success);-webkit-text-fill-color:var(--success);">{{ $passedExams }}</div>
            <div class="gami-pill__label">Bestanden</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--warning);-webkit-text-fill-color:var(--warning);">{{ $avgPercent }}%</div>
            <div class="gami-pill__label">Schnitt</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#a855f7;-webkit-text-fill-color:#a855f7;">{{ $bestPercent }}%</div>
            <div class="gami-pill__label">Bestes</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:{{ $passRate >= 80 ? 'var(--success)' : 'var(--error)' }};-webkit-text-fill-color:{{ $passRate >= 80 ? 'var(--success)' : 'var(--error)' }};">{{ $passRate }}%</div>
            <div class="gami-pill__label">Quote</div>
        </div>
    </div>

    @if($totalExams === 0)
        {{-- ── Empty State ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;text-align:center;padding:3rem 1.5rem;">
            <div style="width:3rem;height:3rem;margin:0 auto 1rem;border-radius:0.75rem;background:rgba(0,85,204,0.08);display:flex;align-items:center;justify-content:center;color:#5b9aff;font-size:1.25rem;">
                <i class="bi bi-clipboard"></i>
            </div>
            <h2 style="font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:0.35rem;">Noch keine Prüfungen</h2>
            <p style="font-size:0.8125rem;color:var(--text-muted);margin-bottom:1.25rem;line-height:1.5;">Lerne zuerst alle Fragen und starte dann deine erste Prüfung.</p>
            <a href="{{ route('exam.index') }}" class="smart-action__btn" style="display:inline-flex;text-decoration:none;background:linear-gradient(135deg,#0055cc,#5b9aff);padding:0.5rem 1rem;border-radius:0.5rem;">
                Prüfung starten <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @else
        <div class="space-y-4">

            {{-- ── Smart-Action Card ── --}}
            @if(!empty($weakSections))
            <a href="{{ route('practice.section', $weakSections[0]) }}" class="eh-action-card">
                <div style="font-size:0.625rem;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.7);margin-bottom:0.375rem;">Empfehlung</div>
                <div style="font-size:1.125rem;font-weight:700;margin-bottom:0.25rem;">Lernabschnitt {{ $weakSections[0] }} wiederholen</div>
                <div style="font-size:0.8125rem;color:rgba(255,255,255,0.6);margin-bottom:0.75rem;">Dein schwächster Bereich — mit gezieltem Üben schnell verbessern</div>
                <span style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.2);color:#fff;border-radius:0.5rem;padding:0.375rem 0.75rem;font-size:0.75rem;font-weight:600;">
                    Jetzt üben <i class="bi bi-arrow-right"></i>
                </span>
            </a>
            @endif

            {{-- ── Trend Chart ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;margin-bottom:0.75rem;">Verbesserungs-Trend</div>
                <div class="eh-trend-chart">
                    <div class="eh-trend-threshold" style="bottom: calc(0.8 * var(--chart-h) + 1.5rem);">
                        <span class="eh-trend-threshold__label">80%</span>
                    </div>
                    @foreach($trend as $exam)
                        @php
                            $pct = round(($exam->correct_answers / 40) * 100);
                        @endphp
                        <div class="eh-trend-bar eh-trend-bar--{{ $exam->is_passed ? 'passed' : 'failed' }}"
                             style="height: max(10px, calc({{ $pct }} * var(--chart-h) / 100));">
                            <span class="eh-trend-bar__value">{{ $pct }}%</span>
                            <span class="eh-trend-bar__date">{{ $exam->created_at->format('d.m.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="eh-compare-row">
                    <div>
                        <div class="eh-compare-value" style="background:linear-gradient(90deg,#fbbf24,#f59e0b);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">{{ $avgPercent }}%</div>
                        <div class="eh-compare-label">Dein Schnitt</div>
                    </div>
                    <div>
                        <div class="eh-compare-value" style="color:var(--text-secondary);">{{ $globalAvgPercent }}%</div>
                        <div class="eh-compare-label">Alle Nutzer</div>
                    </div>
                    @if($avgPercent > $globalAvgPercent)
                        <div style="display:flex;align-items:center;gap:0.25rem;color:#22c55e;font-size:0.75rem;font-weight:600;">
                            <i class="bi bi-arrow-up-circle-fill"></i> +{{ $avgPercent - $globalAvgPercent }}% über Schnitt
                        </div>
                    @elseif($avgPercent < $globalAvgPercent)
                        <div style="display:flex;align-items:center;gap:0.25rem;color:#ef4444;font-size:0.75rem;font-weight:600;">
                            <i class="bi bi-arrow-down-circle-fill"></i> {{ $avgPercent - $globalAvgPercent }}% unter Schnitt
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Sachgebiet-Analyse ── --}}
            @if(!empty($sectionAnalysis))
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Stärken & Schwächen</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $linkedExamCount ?? 0 }} von {{ $exams->count() }} Prüfungen</span>
                </div>

                @foreach($sectionAnalysis as $section => $data)
                    @php
                        $colorClass = $data['percent'] >= 80 ? 'green' : ($data['percent'] >= 50 ? 'blue' : 'red');
                        $barColor = $data['percent'] >= 80 ? '#22c55e' : ($data['percent'] >= 50 ? '#0055cc' : '#ef4444');
                    @endphp
                    <div class="eh-section-item">
                        <div class="section-num section-num--{{ $colorClass }}">{{ $section }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.75rem;font-weight:600;color:var(--text-primary);">LA {{ $section }}</div>
                            <div class="section-bar">
                                <div class="section-bar__fill" style="width:{{ $data['percent'] }}%;background:{{ $barColor }};"></div>
                            </div>
                        </div>
                        <span class="section-pct">{{ $data['percent'] }}%</span>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- ── Empfehlungen (nur wenn >1 weakSection) ── --}}
            @if(count($weakSections) > 1)
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;margin-bottom:0.75rem;">Empfehlung</div>
                @foreach(array_slice($weakSections, 1) as $section)
                    <a href="{{ route('practice.section', $section) }}" class="eh-rec-item">
                        <div style="width:2rem;height:2rem;border-radius:0.5rem;background:{{ $sectionAnalysis[$section]['percent'] < 50 ? 'rgba(239,68,68,0.12)' : 'rgba(251,191,36,0.12)' }};color:{{ $sectionAnalysis[$section]['percent'] < 50 ? '#ef4444' : '#f59e0b' }};font-size:0.75rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:'IBM Plex Mono',monospace;">{{ $section }}</div>
                        <div>
                            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">Lernabschnitt {{ $section }}</div>
                            <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.1rem;">{{ $sectionAnalysis[$section]['percent'] }}% richtig — {{ $sectionAnalysis[$section]['percent'] < 50 ? 'intensiv wiederholen' : 'noch etwas üben' }}</div>
                        </div>
                        <i class="bi bi-chevron-right" style="color:var(--text-muted);font-size:0.75rem;margin-left:auto;transition:color 150ms;"></i>
                    </a>
                @endforeach
            </div>
            @endif

            {{-- ── Prüfungsliste ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;" x-data="{ filter: 'all' }">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Alle Prüfungen</span>
                </div>
                <div class="eh-filter-pills">
                    <button class="eh-filter-pill" :class="{ 'active': filter === 'all' }" @click="filter = 'all'">Alle</button>
                    <button class="eh-filter-pill" :class="{ 'active': filter === 'passed' }" @click="filter = 'passed'">Bestanden</button>
                    <button class="eh-filter-pill" :class="{ 'active': filter === 'failed' }" @click="filter = 'failed'">Durchgefallen</button>
                </div>

                @foreach($exams as $i => $exam)
                    @php $pct = round(($exam->correct_answers / 40) * 100); @endphp
                    <div class="eh-exam-item"
                         x-show="filter === 'all' || (filter === 'passed' && {{ $exam->is_passed ? 'true' : 'false' }}) || (filter === 'failed' && !{{ $exam->is_passed ? 'true' : 'false' }})"
                         x-transition.opacity>
                        <div class="eh-exam-badge eh-exam-badge--{{ $exam->is_passed ? 'passed' : 'failed' }}">{{ $totalExams - $i }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">{{ $exam->correct_answers }}/40 richtig ({{ $pct }}%)</div>
                            <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.1rem;">{{ $exam->created_at->format('d.m.Y, H:i') }} Uhr</div>
                        </div>
                        <span class="eh-exam-status eh-exam-status--{{ $exam->is_passed ? 'passed' : 'failed' }}">
                            {{ $exam->is_passed ? 'Bestanden' : 'Nicht best.' }}
                        </span>
                    </div>
                @endforeach
            </div>

        </div>
    @endif
</div>
@endsection