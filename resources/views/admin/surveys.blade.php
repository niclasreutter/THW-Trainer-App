@extends('layouts.app')
@section('title', 'Umfragen verwalten')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ── Card Label ── */
    .card-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ── Stat Rows ── */
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .stat-row:last-child { border-bottom: none; }
    .stat-row-label { font-size: 0.8125rem; color: var(--text-secondary); }
    .stat-row-value {
        font-size: 0.875rem;
        font-weight: 700;
        font-family: 'Barlow Condensed', sans-serif;
    }

    /* ── Response Items (like activity-item) ── */
    .response-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 0.375rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all var(--transition-fast);
    }
    .response-item:last-child { margin-bottom: 0; }
    .response-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }

    /* ── Survey Items (like activity-item) ── */
    .survey-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem;
        border-radius: 0.5rem;
        margin-bottom: 0.375rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all var(--transition-fast);
        flex-wrap: wrap;
    }
    .survey-item:last-child { margin-bottom: 0; }
    .survey-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .survey-item.active {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.08), rgba(245, 158, 11, 0.03));
        border-color: rgba(251, 191, 36, 0.15);
    }

    /* ── Feedback Fields ── */
    .feedback-field { margin-bottom: 0.375rem; }
    .feedback-field:last-child { margin-bottom: 0; }
    .feedback-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gold-start);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.15rem;
    }
    .feedback-text {
        font-size: 0.775rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* ── Response Header ── */
    .response-name {
        font-size: 0.775rem;
        font-weight: 600;
        color: var(--text-primary);
    }
    .response-time {
        font-size: 0.6rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        white-space: nowrap;
    }

    /* ── Badges (like badge-sm) ── */
    .badge-sm {
        font-size: 0.5rem;
        font-weight: 700;
        padding: 0.1rem 0.3rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-family: 'IBM Plex Mono', monospace;
        vertical-align: middle;
        margin-left: 0.2rem;
    }
    .badge-success { background: rgba(34, 197, 94, 0.15); color: #86efac; }
    .badge-warning { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
    .badge-error { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .badge-active { background: rgba(34, 197, 94, 0.15); color: #86efac; }

    /* ── Chart Grid (like admin dashboard) ── */
    .chart-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 600px) {
        .chart-grid { grid-template-columns: 1fr; }
    }

    .chart-container {
        height: 200px;
        position: relative;
    }
    .chart-container canvas {
        max-width: 100% !important;
        height: auto !important;
    }
    .chart-container--short { height: 140px; }

    /* ── Stagger Animation ── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .space-y-4 > * {
        opacity: 0;
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }
    .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
    .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }
    .space-y-4 > *:nth-child(8) { animation-delay: 0.31s; }
    .space-y-4 > *:nth-child(9) { animation-delay: 0.35s; }
    .space-y-4 > *:nth-child(10) { animation-delay: 0.39s; }
    .space-y-4 > * + * { margin-top: 1rem; }

    /* ── Reduced Motion ── */
    @media (prefers-reduced-motion: reduce) {
        .space-y-4 > * {
            opacity: 1;
            animation: none;
        }
    }

    /* ── Light Mode ── */
    html.light-mode .response-item {
        background: rgba(0, 51, 127, 0.02);
        border-color: rgba(0, 51, 127, 0.06);
    }
    html.light-mode .response-item:hover {
        background: rgba(0, 51, 127, 0.04);
    }
    html.light-mode .survey-item {
        background: rgba(0, 51, 127, 0.02);
        border-color: rgba(0, 51, 127, 0.06);
    }
    html.light-mode .survey-item:hover {
        background: rgba(0, 51, 127, 0.04);
    }
    html.light-mode .survey-item.active {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.06), rgba(245, 158, 11, 0.02));
        border-color: rgba(251, 191, 36, 0.12);
    }
    html.light-mode .stat-row {
        border-bottom-color: rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="dash-container">
<div class="space-y-4">

    {{-- ── 1. Header ── --}}
    <div>
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Administration</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Nutzerumfragen</h1>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-top:0.125rem;">Ergebnisse, Auswertung und Verwaltung</p>
    </div>

    @if (session('success'))
        <div class="glass" style="padding:0.75rem 1rem;border-color:rgba(34,197,94,0.2);background:rgba(34,197,94,0.06);">
            <span style="font-size:0.8125rem;color:#86efac;">{{ session('success') }}</span>
        </div>
    @endif

    @if ($activeSurvey && $stats['total'] > 0)
        {{-- ── 2. Gami Pills ── --}}
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <div class="gami-pill">
                <div class="gami-pill__value gami-pill__value--blue">{{ $stats['total'] }}</div>
                <div class="gami-pill__label">Teilnehmer</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value gami-pill__value--gold">{{ $stats['avg_overall'] }}</div>
                <div class="gami-pill__label">Gesamt</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:var(--success);">{{ $stats['avg_usability'] }}</div>
                <div class="gami-pill__label">Bedienbarkeit</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:#8b5cf6;">{{ $stats['avg_design'] }}</div>
                <div class="gami-pill__label">Design</div>
            </div>
        </div>

        {{-- ── 3. Charts ── --}}
        <div class="card-label" style="margin-bottom:0;">Auswertung</div>

        <div class="chart-grid">
            <div class="glass" style="padding:1rem;">
                <div class="card-label">Bewertungsverteilung</div>
                <div class="chart-container">
                    <canvas id="ratingsChart"></canvas>
                </div>
            </div>
            <div class="glass" style="padding:1rem;">
                <div class="card-label">Hermine-Gruppe</div>
                <div class="chart-container">
                    <canvas id="hermineChart"></canvas>
                </div>
            </div>
        </div>

        <div class="glass" style="padding:1rem;">
            <div class="card-label">Wie gefunden?</div>
            <div class="chart-container chart-container--short">
                <canvas id="foundViaChart"></canvas>
            </div>
        </div>

        {{-- ── 4. Übersicht ── --}}
        <div class="glass" style="padding:1rem;">
            <div class="card-label">Übersicht</div>
            <div class="stat-row">
                <span class="stat-row-label">Teilnehmer</span>
                <span class="stat-row-value" style="color:#5b9aff;">{{ $stats['total'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Gesamt-Bewertung</span>
                <span class="stat-row-value gami-pill__value--gold">{{ $stats['avg_overall'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Bedienbarkeit</span>
                <span class="stat-row-value" style="color:var(--success);">{{ $stats['avg_usability'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Design</span>
                <span class="stat-row-value" style="color:#8b5cf6;">{{ $stats['avg_design'] }}</span>
            </div>
            <div style="margin-top:0.5rem;">
                <a href="{{ route('admin.umfragen.export', ['survey_id' => $activeSurvey->id]) }}" class="btn-secondary" style="font-size:0.75rem;padding:0.35rem 0.75rem;">CSV Export</a>
            </div>
        </div>

        {{-- ── 5. Textantworten ── --}}
        <div class="card-label" style="margin-bottom:0;">Textantworten</div>

        <div class="glass" style="padding:1rem;">
            <div class="card-label">Feedback</div>
            <div style="display:flex;flex-direction:column;gap:0.25rem;">
                @forelse ($responses->filter(fn($r) => $r->feedback_general || $r->feedback_wishes || $r->feedback_changes) as $response)
                    <div class="response-item">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.375rem;flex-wrap:wrap;gap:0.25rem;">
                                <div style="display:flex;align-items:center;gap:0.375rem;">
                                    <span class="response-name">
                                        @if ($response->publish_mode === 'name')
                                            {{ $response->user->name ?? 'Gelöscht' }}
                                        @elseif ($response->publish_mode === 'anonymous')
                                            Anonym
                                        @else
                                            {{ $response->user->name ?? 'Gelöscht' }}
                                        @endif
                                    </span>
                                    @if ($response->publish_mode === 'name')
                                        <span class="badge-sm badge-success">NAME</span>
                                    @elseif ($response->publish_mode === 'anonymous')
                                        <span class="badge-sm badge-warning">ANONYM</span>
                                    @else
                                        <span class="badge-sm badge-error">PRIVAT</span>
                                    @endif
                                </div>
                                <span class="response-time">{{ $response->created_at->format('d.m.Y H:i') }}</span>
                            </div>

                            @if ($response->feedback_general)
                                <div class="feedback-field">
                                    <div class="feedback-label">Allgemein</div>
                                    <p class="feedback-text">{{ $response->feedback_general }}</p>
                                </div>
                            @endif
                            @if ($response->feedback_wishes)
                                <div class="feedback-field">
                                    <div class="feedback-label">Wünsche</div>
                                    <p class="feedback-text">{{ $response->feedback_wishes }}</p>
                                </div>
                            @endif
                            @if ($response->feedback_changes)
                                <div class="feedback-field">
                                    <div class="feedback-label">Änderungen</div>
                                    <p class="feedback-text">{{ $response->feedback_changes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:1.5rem;color:var(--text-muted);">
                        Noch keine Textantworten vorhanden.
                    </div>
                @endforelse
            </div>
        </div>

    @elseif ($activeSurvey)
        <div class="glass" style="padding:1.5rem;text-align:center;">
            <p style="color:var(--text-muted);font-size:0.875rem;">Noch keine Teilnehmer für diese Umfrage.</p>
        </div>
    @else
        <div class="glass" style="padding:1.5rem;text-align:center;">
            <p style="color:var(--text-muted);font-size:0.875rem;">Keine aktive Umfrage. Erstelle oder aktiviere eine Umfrage.</p>
        </div>
    @endif

    {{-- ── 6. Umfragen verwalten ── --}}
    <div class="card-label" style="margin-bottom:0;">Verwaltung</div>

    <div class="glass" style="padding:1rem;">
        <div class="card-label">Alle Umfragen</div>
        <div style="display:flex;flex-direction:column;gap:0.25rem;">
            @foreach ($surveys as $survey)
                <div class="survey-item {{ $survey->is_active ? 'active' : '' }}">
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;gap:0.375rem;flex-wrap:wrap;">
                            <span style="font-weight:600;color:var(--text-primary);font-size:0.775rem;">{{ $survey->title }}</span>
                            <span style="font-size:0.6rem;color:var(--text-muted);font-family:'IBM Plex Mono',monospace;">v{{ $survey->version }}</span>
                            @if ($survey->is_active)
                                <span class="badge-sm badge-active">AKTIV</span>
                            @endif
                        </div>
                        <span style="font-size:0.7rem;color:var(--text-muted);display:block;margin-top:0.15rem;">{{ $survey->responses_count }} Teilnehmer</span>
                    </div>
                    <form action="{{ route('admin.umfragen.toggle', $survey) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="{{ $survey->is_active ? 'btn-ghost' : 'btn-secondary' }}" style="font-size:0.75rem;padding:0.35rem 0.75rem;">
                            {{ $survey->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── 7. Neue Umfrage ── --}}
    <div class="glass" style="padding:1rem;">
        <div class="card-label">Neue Umfrage erstellen</div>
        <form action="{{ route('admin.umfragen.store') }}" method="POST" style="display:flex;flex-direction:column;gap:0.75rem;">
            @csrf
            <input type="text" name="title" class="input-glass" placeholder="Titel (z.B. Nutzerumfrage 2026)" required>
            <textarea name="description" class="textarea-glass" rows="2" placeholder="Beschreibung (optional)"></textarea>
            <div>
                <button type="submit" class="btn-primary" style="font-size:0.8125rem;">Erstellen</button>
            </div>
        </form>
    </div>

</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    if (!chartData || !chartData.overall) return;

    const isLightMode = document.documentElement.classList.contains('light-mode');
    const gridColor = isLightMode ? 'rgba(0, 0, 0, 0.06)' : 'rgba(255, 255, 255, 0.06)';
    const textColor = isLightMode ? '#374151' : '#a1a1aa';

    const emojiLabels = ['\u{1F621}', '\u{1F61F}', '\u{1F610}', '\u{1F60A}', '\u{1F60D}'];

    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = gridColor;

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                padding: 12,
                borderRadius: 8,
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                titleFont: { size: 13, weight: 'bold' },
                titleColor: '#f5f5f5',
                bodyColor: '#a1a1aa',
            }
        }
    };

    // Ratings Bar Chart
    new Chart(document.getElementById('ratingsChart'), {
        type: 'bar',
        data: {
            labels: emojiLabels,
            datasets: [
                {
                    label: 'Gesamt',
                    data: Object.values(chartData.overall),
                    backgroundColor: 'rgba(0, 51, 127, 0.7)',
                    borderRadius: 4,
                },
                {
                    label: 'Bedienbarkeit',
                    data: Object.values(chartData.usability),
                    backgroundColor: 'rgba(251, 191, 36, 0.7)',
                    borderRadius: 4,
                },
                {
                    label: 'Design',
                    data: Object.values(chartData.design),
                    backgroundColor: 'rgba(139, 92, 246, 0.7)',
                    borderRadius: 4,
                },
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
                x: { grid: { display: false } }
            }
        }
    });

    // Hermine Doughnut Chart
    new Chart(document.getElementById('hermineChart'), {
        type: 'doughnut',
        data: {
            labels: ['Ja', 'Nein', 'Kenne ich nicht'],
            datasets: [{
                data: [chartData.hermine.ja, chartData.hermine.nein, chartData.hermine.unknown],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(161, 161, 170, 0.5)',
                ],
                borderWidth: 0,
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
            }
        }
    });

    // Found Via Bar Chart
    const foundLabels = {
        empfehlung: 'Empfehlung',
        google: 'Google',
        social_media: 'Social Media',
        thw_ausbildung: 'THW Ausbildung',
        sonstiges: 'Sonstiges',
    };
    new Chart(document.getElementById('foundViaChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartData.found_via).map(k => foundLabels[k] || k),
            datasets: [{
                label: 'Anzahl',
                data: Object.values(chartData.found_via),
                backgroundColor: 'rgba(91, 154, 255, 0.6)',
                borderRadius: 4,
            }]
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
                y: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
@endsection
