@extends('layouts.landing')

@section('title', 'Plattform-Statistiken | THW-Trainer.de')
@section('description', 'Anonyme Plattform-Statistiken des THW-Trainers: Registrierte Nutzer, beantwortete Fragen, Bestehensquote und mehr.')

@section('content')
<div class="overflow-x-hidden" style="background-color: #ffffff;">

    {{-- ============================================
         HERO SECTION
         ============================================ --}}
    <section class="landing-hero" style="min-height: 40vh;" aria-label="Statistik-Hauptbereich">
        <div class="landing-hero-content">
            <div class="text-center max-w-4xl mx-auto">
                <span class="landing-hero-tagline">Transparente Zahlen</span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 tracking-tight text-white leading-tight">
                    <span class="landing-hero-gradient-text">Plattform</span>
                    <br>
                    <span class="text-white">Statistiken</span>
                </h1>

                <p class="text-lg lg:text-xl text-blue-100 mb-8 max-w-2xl mx-auto leading-relaxed font-light" style="opacity: 0.85;">
                    Anonyme, aggregierte Zahlen der gesamten THW-Trainer Community
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================
         STATS STRIP
         ============================================ --}}
    <section class="px-4 sm:px-6 lg:px-8" style="background-color: #ffffff;" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="sr-only">Plattform-Kennzahlen</h2>
        <div class="landing-stats-strip landing-fade-in">
            <div class="landing-stat-item">
                <div class="landing-stat-value" data-count="{{ (int) ($stats['users'] ?? 0) }}" data-suffix="+">0+</div>
                <div class="landing-stat-label">Registrierte Nutzer</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value" data-count="{{ (int) ($stats['questions_answered'] ?? 0) }}" data-suffix="+">0+</div>
                <div class="landing-stat-label">Fragen beantwortet</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value" data-count="{{ (int) ($stats['exams_passed'] ?? 0) }}" data-suffix="+">0+</div>
                <div class="landing-stat-label">Prüfungen bestanden</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value" data-count="{{ (int) ($stats['pass_rate'] ?? 0) }}" data-suffix="%">0%</div>
                <div class="landing-stat-label">Bestehensquote</div>
            </div>
        </div>
    </section>

    {{-- ============================================
         CHARTS SECTION — Aktivität & Trends
         ============================================ --}}
    <section class="py-16 lg:py-24 bg-white" aria-labelledby="charts-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="charts-heading">Aktivität & Trends</h2>
                <p>30-Tage Überblick der Plattform-Nutzung</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 landing-fade-in">
                {{-- Chart 1: Aktive Nutzer (Bar) --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Aktive Nutzer pro Tag</div>
                        @php $lastDay = end($stats['chart']); @endphp
                        <div class="text-right">
                            <span class="text-2xl font-bold text-slate-900">{{ $lastDay['value'] }}</span>
                            <span class="text-xs text-slate-500 ml-1">heute</span>
                        </div>
                    </div>
                    <div style="position: relative; height: 280px;">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Beantwortete Fragen (Line) --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Beantwortete Fragen pro Tag</div>
                        @php $lastQ = end($stats['questions_per_day']['values']); @endphp
                        <div class="text-right">
                            <span class="text-2xl font-bold text-slate-900">{{ number_format($lastQ, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-500 ml-1">heute</span>
                        </div>
                    </div>
                    <div style="position: relative; height: 280px;">
                        <canvas id="questionsChart"></canvas>
                    </div>
                </div>

                {{-- Chart 3: Erfolgsquote (Line) --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Erfolgsquote im Zeitverlauf</div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-slate-900">{{ $stats['avg_hit_rate'] }}%</span>
                            <span class="text-xs text-slate-500 ml-1">gesamt</span>
                        </div>
                    </div>
                    <div style="position: relative; height: 280px;">
                        <canvas id="successRateChart"></canvas>
                    </div>
                </div>

                {{-- Chart 4: Prüfungen pro Tag (Bar) --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Prüfungen pro Tag</div>
                        @php $lastExam = end($stats['exams_per_day']['total']); @endphp
                        <div class="text-right">
                            <span class="text-2xl font-bold text-slate-900">{{ $lastExam }}</span>
                            <span class="text-xs text-slate-500 ml-1">heute</span>
                        </div>
                    </div>
                    <div style="position: relative; height: 280px;">
                        <canvas id="examsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         DETAILS — Bento Grid
         ============================================ --}}
    <section class="py-16 lg:py-24 bg-slate-50" aria-labelledby="details-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="details-heading">Plattform im Detail</h2>
                <p>Zahlen und Fakten zum THW-Trainer</p>
            </header>

            <div class="landing-bento">
                {{-- Hauptkarte: Trefferquote --}}
                <article class="landing-bento-card landing-bento-main landing-fade-in">
                    <span class="landing-bento-tag">Community</span>
                    <h3 class="landing-bento-title">Durchschnittliche Trefferquote</h3>
                    <div class="flex items-baseline gap-2 mt-4">
                        <span class="text-5xl lg:text-6xl font-extrabold" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            {{ $stats['avg_hit_rate'] }}%
                        </span>
                        <span class="text-slate-500 text-lg">aller Antworten korrekt</span>
                    </div>
                    <p class="landing-bento-text mt-4">
                        Über {{ number_format($stats['questions_answered'], 0, ',', '.') }} beantwortete Fragen
                        mit einer durchschnittlichen Trefferquote von {{ $stats['avg_hit_rate'] }}%.
                    </p>
                </article>

                {{-- Gesamtfragen --}}
                <article class="landing-bento-card landing-bento-side landing-fade-in">
                    <h3 class="landing-bento-title">Fragenkatalog</h3>
                    <div class="text-3xl font-bold text-slate-900 mt-3">{{ number_format($stats['total_questions'], 0, ',', '.') }}</div>
                    <p class="landing-bento-text mt-2">
                        Theoriefragen in {{ count($stats['section_counts']) }} Lernabschnitten
                    </p>
                </article>

                {{-- Bestehensquote --}}
                <article class="landing-bento-card landing-bento-side landing-fade-in" style="border-radius: 0.75rem 2rem 0.75rem 0.75rem;">
                    <h3 class="landing-bento-title">Bestehensquote</h3>
                    <div class="text-3xl font-bold mt-3" style="color: #16a34a;">{{ $stats['pass_rate'] }}%</div>
                    <p class="landing-bento-text mt-2">
                        {{ number_format($stats['exams_passed'], 0, ',', '.') }}+ bestandene Prüfungssimulationen
                    </p>
                </article>

                {{-- Breite Karte: Lernabschnitte als Horizontal Bar Chart --}}
                <article class="landing-bento-card landing-bento-wide landing-fade-in">
                    <div class="w-full">
                        <h3 class="landing-bento-title mb-4">Fragen pro Lernabschnitt</h3>
                        <div style="position: relative; height: 260px;">
                            <canvas id="sectionChart"></canvas>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         CTA SECTION
         ============================================ --}}
    <section class="landing-cta" aria-labelledby="cta-heading">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 pt-8">
            <h2 id="cta-heading" class="text-2xl lg:text-4xl font-bold text-white mb-4 tracking-tight">
                Werde Teil der Community
            </h2>
            <p class="text-base lg:text-lg text-blue-100 mb-4 max-w-3xl mx-auto leading-relaxed font-light">
                Registriere dich kostenlos und starte jetzt mit deiner
                <strong class="font-semibold text-white">THW Grundausbildung Theorieprüfung</strong> Vorbereitung.
            </p>
            <p class="text-sm lg:text-base text-blue-200 max-w-3xl mx-auto leading-relaxed font-light mb-8" style="opacity: 0.8;">
                Bereits {{ number_format($stats['users'], 0, ',', '.') }}+ Helfer lernen mit dem THW-Trainer.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @php
                    $registerUrl = config('domains.development')
                        ? route('register')
                        : 'https://' . config('domains.app') . '/register';
                    $loginUrl = config('domains.development')
                        ? route('login')
                        : 'https://' . config('domains.app') . '/login';
                @endphp
                <a href="{{ $registerUrl }}"
                   class="inline-block bg-gradient-to-r from-yellow-400 to-amber-500 text-blue-900 px-8 py-4 rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg shadow-yellow-500/30 text-center"
                   aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>

                <a href="{{ $loginUrl }}"
                   class="inline-block bg-white/10 text-white px-8 py-4 rounded-xl font-bold border-2 border-white/30 hover:bg-white/20 hover:border-white/50 transition-all duration-300 text-center backdrop-blur-sm"
                   aria-label="Zum Login">
                    Login
                </a>
            </div>
        </div>
    </section>

</div>

{{-- Counter Animation Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll fade-in animation
    var fadeEls = document.querySelectorAll('.landing-fade-in');
    if (fadeEls.length && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        fadeEls.forEach(function(el) { observer.observe(el); });
    } else {
        fadeEls.forEach(function(el) { el.classList.add('visible'); });
    }

    // Counter animation for stats strip
    var statEls = document.querySelectorAll('.landing-stat-value[data-count]');
    if (statEls.length && 'IntersectionObserver' in window) {
        var countObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateStatCounter(entry.target);
                    countObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        statEls.forEach(function(el) { countObserver.observe(el); });
    }
});

function animateStatCounter(el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    var suffix = el.getAttribute('data-suffix') || '';
    if (isNaN(target) || target <= 0) {
        el.textContent = '0' + suffix;
        return;
    }
    var duration = 2000;
    var start = performance.now();

    function tick(now) {
        var elapsed = now - start;
        var progress = Math.min(elapsed / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        var current = Math.floor(eased * target);
        el.textContent = current.toLocaleString('de-DE') + suffix;
        if (progress < 1) {
            requestAnimationFrame(tick);
        } else {
            el.textContent = target.toLocaleString('de-DE') + suffix;
        }
    }
    requestAnimationFrame(tick);
}
</script>
@endsection

@push('scripts')
@vite('resources/js/landing-charts.js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // Light mode defaults
    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    var lightScales = {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(0, 0, 0, 0.06)', drawBorder: false },
            ticks: { font: { size: 11 }, color: '#94a3b8' }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 45, minRotation: 45 }
        }
    };

    var tooltipStyle = {
        backgroundColor: '#1e293b',
        padding: 12,
        cornerRadius: 8,
        titleFont: { size: 13, weight: 'bold' },
        bodyFont: { size: 12 }
    };

    // Chart data from server
    var activeLabels = @json(collect($stats['chart'])->pluck('label'));
    var activeValues = @json(collect($stats['chart'])->pluck('value'));
    var qLabels = @json($stats['questions_per_day']['labels']);
    var qValues = @json($stats['questions_per_day']['values']);
    var srLabels = @json($stats['success_rate_per_day']['labels']);
    var srValues = @json($stats['success_rate_per_day']['values']);
    var exLabels = @json($stats['exams_per_day']['labels']);
    var exTotal = @json($stats['exams_per_day']['total']);
    var exPassed = @json($stats['exams_per_day']['passed']);
    var sectionLabels = @json(array_map(function($s) { return 'Abschnitt ' . $s; }, array_keys($stats['section_counts'])));
    var sectionValues = @json(array_values($stats['section_counts']));

    // 1. Active Users (Bar Chart)
    new Chart(document.getElementById('activeUsersChart'), {
        type: 'bar',
        data: {
            labels: activeLabels,
            datasets: [{
                data: activeValues,
                backgroundColor: function(ctx) {
                    return ctx.dataIndex === activeValues.length - 1
                        ? 'rgba(245, 158, 11, 0.85)'
                        : 'rgba(0, 51, 127, 0.7)';
                },
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipStyle },
            scales: lightScales
        }
    });

    // 2. Questions per Day (Line Chart)
    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: qLabels,
            datasets: [{
                data: qValues,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#f59e0b',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipStyle },
            scales: lightScales
        }
    });

    // 3. Success Rate (Line Chart)
    new Chart(document.getElementById('successRateChart'), {
        type: 'line',
        data: {
            labels: srLabels,
            datasets: [{
                data: srValues,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#16a34a',
                spanGaps: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipStyle },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(0, 0, 0, 0.06)', drawBorder: false },
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        callback: function(v) { return v + '%'; }
                    }
                },
                x: lightScales.x
            }
        }
    });

    // 4. Exams per Day (Stacked Bar Chart)
    new Chart(document.getElementById('examsChart'), {
        type: 'bar',
        data: {
            labels: exLabels,
            datasets: [
                {
                    label: 'Bestanden',
                    data: exPassed,
                    backgroundColor: 'rgba(22, 163, 74, 0.7)',
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Nicht bestanden',
                    data: exTotal.map(function(t, i) { return t - exPassed[i]; }),
                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                    borderRadius: 4,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { font: { size: 11 }, color: '#64748b', usePointStyle: true, pointStyle: 'rectRounded', padding: 16 }
                },
                tooltip: tooltipStyle
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true,
                    grid: { color: 'rgba(0, 0, 0, 0.06)', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                },
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 45, minRotation: 45 }
                }
            }
        }
    });

    // 5. Section Breakdown (Horizontal Bar)
    var sectionColors = [
        '#00337F', '#0055cc', '#3b82f6', '#60a5fa', '#93c5fd',
        '#1e40af', '#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa'
    ];
    new Chart(document.getElementById('sectionChart'), {
        type: 'bar',
        data: {
            labels: sectionLabels,
            datasets: [{
                data: sectionValues,
                backgroundColor: sectionLabels.map(function(_, i) {
                    return sectionColors[i % sectionColors.length];
                }),
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: tooltipStyle
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.06)', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 12, weight: '500' }, color: '#334155' }
                }
            }
        }
    });
});
</script>
@endpush
