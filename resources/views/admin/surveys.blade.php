@extends('layouts.app')
@section('title', 'Umfragen verwalten')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Nutzer<span>umfragen</span></h1>
        <p class="page-subtitle">Ergebnisse und Auswertung</p>
    </header>

    @if (session('success'))
        <div class="glass-success" style="padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem;">
            <p style="color: #86efac; font-size: 0.875rem;">{{ session('success') }}</p>
        </div>
    @endif

    @if ($activeSurvey && $stats['total'] > 0)
        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-pill">
                <span class="stat-pill-icon text-gold"><i class="bi bi-people"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['total'] }}</div>
                    <div class="stat-pill-label">Teilnehmer</div>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon text-gold"><i class="bi bi-star"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['avg_overall'] }}</div>
                    <div class="stat-pill-label">Gesamt</div>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon text-gold"><i class="bi bi-hand-thumbs-up"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['avg_usability'] }}</div>
                    <div class="stat-pill-label">Bedienbarkeit</div>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon text-gold"><i class="bi bi-palette"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $stats['avg_design'] }}</div>
                    <div class="stat-pill-label">Design</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-top: 1.5rem;">
            <h2 class="section-title">Auswertung</h2>
        </div>

        <div class="bento-grid" style="margin-top: 1rem;">
            <div class="glass-blue bento-2of3" style="padding: 1.5rem;">
                <h3 style="font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Bewertungsverteilung</h3>
                <canvas id="ratingsChart" height="200"></canvas>
            </div>
            <div class="glass-tl bento-third" style="padding: 1.5rem;">
                <h3 style="font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Hermine-Gruppe</h3>
                <canvas id="hermineChart" height="200"></canvas>
            </div>
        </div>

        <div class="bento-grid" style="margin-top: 1rem;">
            <div class="glass bento-wide" style="padding: 1.5rem;">
                <h3 style="font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Wie gefunden?</h3>
                <canvas id="foundViaChart" height="120"></canvas>
            </div>
        </div>

        {{-- Text Responses --}}
        <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-top: 1.5rem;">
            <h2 class="section-title">Textantworten</h2>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
            @forelse ($responses->filter(fn($r) => $r->feedback_general || $r->feedback_wishes || $r->feedback_changes) as $response)
                <div class="glass" style="padding: 1.25rem; border-radius: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            @if ($response->publish_mode === 'name')
                                {{ $response->user->name ?? 'Geloescht' }}
                            @elseif ($response->publish_mode === 'anonymous')
                                Anonym
                            @else
                                [Privat] {{ $response->user->name ?? 'Geloescht' }}
                            @endif
                        </span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $response->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    @if ($response->feedback_general)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; color: var(--gold-start); font-weight: 600;">Allgemein:</span>
                            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $response->feedback_general }}</p>
                        </div>
                    @endif
                    @if ($response->feedback_wishes)
                        <div style="margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; color: var(--gold-start); font-weight: 600;">Wuensche:</span>
                            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $response->feedback_wishes }}</p>
                        </div>
                    @endif
                    @if ($response->feedback_changes)
                        <div>
                            <span style="font-size: 0.75rem; color: var(--gold-start); font-weight: 600;">Aenderungen:</span>
                            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $response->feedback_changes }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="glass" style="padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                    <p style="color: var(--text-muted); font-size: 0.875rem;">Noch keine Textantworten vorhanden.</p>
                </div>
            @endforelse
        </div>

        {{-- Export --}}
        <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.umfragen.export', ['survey_id' => $activeSurvey->id]) }}" class="btn-primary">CSV Export</a>
        </div>
    @elseif ($activeSurvey)
        <div class="glass" style="padding: 2rem; border-radius: 1rem; text-align: center; margin-top: 1rem;">
            <p style="color: var(--text-muted);">Noch keine Teilnehmer fuer diese Umfrage.</p>
        </div>
    @else
        <div class="glass" style="padding: 2rem; border-radius: 1rem; text-align: center; margin-top: 1rem;">
            <p style="color: var(--text-muted);">Keine aktive Umfrage. Erstelle oder aktiviere eine Umfrage.</p>
        </div>
    @endif

    {{-- Survey Management --}}
    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-top: 2rem;">
        <h2 class="section-title">Umfragen verwalten</h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
        @foreach ($surveys as $survey)
            <div class="glass{{ $survey->is_active ? '-gold' : '' }}" style="padding: 1.25rem; border-radius: 0.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <span style="font-weight: 600; color: var(--text-primary);">{{ $survey->title }}</span>
                    <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.5rem;">v{{ $survey->version }}</span>
                    @if ($survey->is_active)
                        <span style="font-size: 0.6875rem; padding: 0.15rem 0.5rem; background: rgba(34, 197, 94, 0.15); color: #86efac; border-radius: 1rem; margin-left: 0.5rem;">Aktiv</span>
                    @endif
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

    {{-- New Survey Form --}}
    <div class="glass" style="padding: 1.5rem; border-radius: 0.75rem; margin-top: 1rem;">
        <h3 style="font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Neue Umfrage erstellen</h3>
        <form action="{{ route('admin.umfragen.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
            @csrf
            <input type="text" name="title" class="input-glass" placeholder="Titel (z.B. Nutzerumfrage 2026)" required>
            <textarea name="description" class="textarea-glass" rows="2" placeholder="Beschreibung (optional)"></textarea>
            <div>
                <button type="submit" class="btn-primary">Erstellen</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    if (!chartData || !chartData.overall) return;

    const emojiLabels = ['\u{1F621}', '\u{1F61F}', '\u{1F610}', '\u{1F60A}', '\u{1F60D}'];
    const chartDefaults = {
        color: '#a1a1aa',
        borderColor: 'rgba(255,255,255,0.08)',
    };

    Chart.defaults.color = chartDefaults.color;
    Chart.defaults.borderColor = chartDefaults.borderColor;

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
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Hermine Pie Chart
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
            plugins: { legend: { position: 'bottom' } }
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
                backgroundColor: 'rgba(0, 51, 127, 0.7)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
@endsection
