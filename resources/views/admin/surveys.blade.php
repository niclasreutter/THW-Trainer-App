@extends('layouts.app')
@section('title', 'Umfragen verwalten')

@push('styles')
<style>
    .response-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all var(--transition-fast);
    }
    .response-item:last-child { margin-bottom: 0; }
    .response-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .survey-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
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
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.06), rgba(245, 158, 11, 0.02));
        border-color: rgba(251, 191, 36, 0.15);
    }

    .feedback-field {
        margin-bottom: 0.5rem;
    }
    .feedback-field:last-child { margin-bottom: 0; }
    .feedback-label {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gold-start);
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    .feedback-text {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .publish-badge {
        font-size: 0.625rem;
        padding: 0.15rem 0.5rem;
        border-radius: 1rem;
        font-weight: 600;
    }
    .publish-badge.name { background: rgba(34, 197, 94, 0.15); color: #86efac; }
    .publish-badge.anonymous { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
    .publish-badge.private { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .active-badge {
        font-size: 0.6875rem;
        padding: 0.15rem 0.5rem;
        background: rgba(34, 197, 94, 0.15);
        color: #86efac;
        border-radius: 1rem;
        font-weight: 600;
    }

    .chart-container { position: relative; }
    .chart-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    /* Light Mode */
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
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Nutzer<span>umfragen</span></h1>
        <p class="page-subtitle">Ergebnisse, Auswertung und Verwaltung</p>
    </header>

    @if (session('success'))
        <div class="alert-glass success" style="margin-bottom: 1.5rem;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; color: var(--success);"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($activeSurvey && $stats['total'] > 0)
        {{-- Stats Pills --}}
        <div class="stats-row">
            <div class="stat-pill">
                <span class="stat-pill-icon text-gold"><i class="bi bi-people"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['total'] }}</div>
                    <div class="stat-pill-label">Teilnehmer</div>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-star"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['avg_overall'] }}</div>
                    <div class="stat-pill-label">Gesamt</div>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon text-success"><i class="bi bi-hand-thumbs-up"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['avg_usability'] }}</div>
                    <div class="stat-pill-label">Bedienbarkeit</div>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon" style="color: #8b5cf6;"><i class="bi bi-palette"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['avg_design'] }}</div>
                    <div class="stat-pill-label">Design</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="bento-grid">
            <div class="glass-blue bento-2of3" style="padding: 1.5rem;">
                <h3 class="chart-title">Bewertungsverteilung</h3>
                <div class="chart-container">
                    <canvas id="ratingsChart" height="200"></canvas>
                </div>
            </div>
            <div class="glass-tl bento-third" style="padding: 1.5rem;">
                <h3 class="chart-title">Hermine-Gruppe</h3>
                <div class="chart-container">
                    <canvas id="hermineChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="bento-grid" style="margin-top: 1rem;">
            <div class="glass-purple bento-wide" style="padding: 1.5rem;">
                <h3 class="chart-title">Wie gefunden?</h3>
                <div class="chart-container">
                    <canvas id="foundViaChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- Text Responses --}}
        <div class="bento-grid" style="margin-top: 1rem;">
            <div class="glass bento-wide" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                        <h2 class="section-title">Textantworten</h2>
                    </div>
                    <a href="{{ route('admin.umfragen.export', ['survey_id' => $activeSurvey->id]) }}" class="btn-secondary" style="font-size: 0.8125rem;">CSV Export</a>
                </div>

                @forelse ($responses->filter(fn($r) => $r->feedback_general || $r->feedback_wishes || $r->feedback_changes) as $response)
                    <div class="response-item">
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.375rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                                        @if ($response->publish_mode === 'name')
                                            {{ $response->user->name ?? 'Gelöscht' }}
                                        @elseif ($response->publish_mode === 'anonymous')
                                            Anonym
                                        @else
                                            {{ $response->user->name ?? 'Gelöscht' }}
                                        @endif
                                    </span>
                                    @if ($response->publish_mode === 'name')
                                        <span class="publish-badge name">Mit Name</span>
                                    @elseif ($response->publish_mode === 'anonymous')
                                        <span class="publish-badge anonymous">Anonym</span>
                                    @else
                                        <span class="publish-badge private">Privat</span>
                                    @endif
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $response->created_at->format('d.m.Y H:i') }}</span>
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
                    <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
                        Noch keine Textantworten vorhanden.
                    </div>
                @endforelse
            </div>
        </div>
    @elseif ($activeSurvey)
        <div class="glass" style="padding: 2rem; text-align: center;">
            <p style="color: var(--text-muted);">Noch keine Teilnehmer für diese Umfrage.</p>
        </div>
    @else
        <div class="glass" style="padding: 2rem; text-align: center;">
            <p style="color: var(--text-muted);">Keine aktive Umfrage. Erstelle oder aktiviere eine Umfrage.</p>
        </div>
    @endif

    {{-- Survey Management + New Survey --}}
    <div class="bento-grid" style="margin-top: 1rem;">
        <div class="glass bento-2of3" style="padding: 1.5rem;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.25rem;">
                <h2 class="section-title">Umfragen verwalten</h2>
            </div>

            @foreach ($surveys as $survey)
                <div class="survey-item {{ $survey->is_active ? 'active' : '' }}">
                    <div style="min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-weight: 600; color: var(--text-primary);">{{ $survey->title }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">v{{ $survey->version }}</span>
                            @if ($survey->is_active)
                                <span class="active-badge">Aktiv</span>
                            @endif
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">{{ $survey->responses_count }} Teilnehmer</span>
                    </div>
                    <form action="{{ route('admin.umfragen.toggle', $survey) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="{{ $survey->is_active ? 'btn-ghost' : 'btn-secondary' }}" style="font-size: 0.8125rem;">
                            {{ $survey->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="glass-accent bento-third" style="padding: 1.5rem;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.25rem;">
                <h2 class="section-title">Neue Umfrage</h2>
            </div>
            <form action="{{ route('admin.umfragen.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @csrf
                <input type="text" name="title" class="input-glass" placeholder="Titel (z.B. Nutzerumfrage 2026)" required>
                <textarea name="description" class="textarea-glass" rows="3" placeholder="Beschreibung (optional)"></textarea>
                <div>
                    <button type="submit" class="btn-primary">Erstellen</button>
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

    const emojiLabels = ['\u{1F621}', '\u{1F61F}', '\u{1F610}', '\u{1F60A}', '\u{1F60D}'];

    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.color = '#a1a1aa';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';

    const tooltipStyle = {
        backgroundColor: 'rgba(0, 0, 0, 0.9)',
        padding: 12,
        borderRadius: 8,
        borderColor: 'rgba(255, 255, 255, 0.1)',
        borderWidth: 1,
        titleFont: { size: 13, weight: 'bold' },
        titleColor: '#f5f5f5',
        bodyColor: '#a1a1aa',
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
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: tooltipStyle,
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
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
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: tooltipStyle,
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
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: tooltipStyle,
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
@endsection
