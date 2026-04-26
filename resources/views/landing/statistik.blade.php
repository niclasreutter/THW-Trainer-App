@extends('layouts.startseite')

@section('title', 'Plattform-Statistiken | THW-Trainer.de')
@section('description', 'Anonyme Plattform-Statistiken des THW-Trainers: Registrierte Nutzer, beantwortete Fragen, Bestehensquote und mehr.')
@section('canonical', url('/statistik'))

@section('content')
@php
    $loginUrl = config('domains.development') ? route('login') : 'https://' . config('domains.app') . '/login';
    $registerUrl = config('domains.development') ? route('register') : 'https://' . config('domains.app') . '/register';
    $appUrl = config('domains.development') ? route('dashboard') : 'https://' . config('domains.app');
@endphp
<div>

    {{-- ============================================
         NAVBAR
         ============================================ --}}
    <nav x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50"
         class="sp-navbar" :class="{ 'scrolled': scrolled }" aria-label="Hauptnavigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW-Trainer Logo" class="h-8 w-auto sp-logo-dark">
                <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="THW-Trainer Logo" class="h-8 w-auto sp-logo-light">
                <span class="font-bold text-xl text-white">THW-Trainer</span>
            </a>
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200 px-4 py-2">Startseite</a>
                <a href="{{ route('landing.statistics') }}" class="text-sm font-medium text-white transition-colors duration-200 px-4 py-2">Statistiken</a>
                <a href="{{ $registerUrl }}" class="sp-btn-gold text-sm" style="padding: 0.5rem 1.25rem;">Kostenlos starten</a>
            </div>
        </div>
    </nav>

    {{-- ============================================
         HERO SECTION — Compact
         ============================================ --}}
    <section class="sp-hero" style="min-height: auto; padding: 6rem 0 3rem;" aria-label="Statistik-Hauptbereich">
        <div class="sp-orb sp-orb-1" aria-hidden="true"></div>
        <div class="sp-orb sp-orb-2" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center py-12 lg:py-16">
                {{-- Breadcrumb --}}
                <nav class="mb-6 text-sm text-zinc-500" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}" class="hover:text-white transition-colors">Startseite</a>
                    <span class="mx-2">/</span>
                    <span class="text-zinc-300">Statistiken</span>
                </nav>

                <span class="sp-hero-badge">Plattform-Zahlen</span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                    Plattform
                    <br>
                    <span class="sp-gradient-text">Statistiken</span>
                </h1>

                <p class="text-lg lg:text-xl text-zinc-400 mb-4 max-w-2xl mx-auto leading-relaxed">
                    Anonyme, aggregierte Zahlen der gesamten THW-Trainer Community
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================
         STATS ROW — KPIs
         ============================================ --}}
    <section class="py-12 lg:py-16 relative sp-section-alt" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="sr-only">Plattform-Kennzahlen</h2>
        <div class="sp-glow-border" style="position: absolute; top: 0; left: 0; right: 0; height: 1px;" aria-hidden="true"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sp-stats sp-fade-in">
                <div class="sp-stat-card">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['users'] ?? 0) }}" data-suffix="+">0+</div>
                    <div class="sp-stat-label">Registrierte Nutzer</div>
                </div>
                <div class="sp-stat-card">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['questions_answered'] ?? 0) }}" data-suffix="+">0+</div>
                    <div class="sp-stat-label">Fragen beantwortet</div>
                </div>
                <div class="sp-stat-card">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['pass_rate'] ?? 0) }}" data-suffix="%">0%</div>
                    <div class="sp-stat-label">Bestehensquote</div>
                </div>
                <div class="sp-stat-card">
                    @if($stats['avg_rating'])
                        <div class="sp-stat-value">{{ number_format($stats['avg_rating'], 1, ',', '.') }}/5</div>
                    @else
                        <div class="sp-stat-value" data-count="{{ (int) ($stats['avg_hit_rate'] ?? 0) }}" data-suffix="%">0%</div>
                    @endif
                    <div class="sp-stat-label">{{ $stats['avg_rating'] ? 'Durchschn. Bewertung' : 'Trefferquote' }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         COMMUNITY WACHSTUM — User Growth Chart
         ============================================ --}}
    @if(count($stats['user_growth']['labels']) > 0)
    <section class="py-20 lg:py-28 sp-section" aria-labelledby="growth-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="growth-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Community <span class="sp-gradient-text">Wachstum</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    Nutzerentwicklung der letzten 90 Tage
                </p>
            </header>

            <div class="sp-fade-in">
                <div class="glass glass-blue" style="padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <div class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Gesamtnutzer</div>
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['users'], 0, ',', '.') }}+</div>
                        </div>
                        <div class="text-right">
                            @php
                                $growthTotal = $stats['user_growth']['total'];
                                $growthDiff = count($growthTotal) >= 2 ? end($growthTotal) - reset($growthTotal) : 0;
                            @endphp
                            @if($growthDiff > 0)
                                <div class="text-sm font-semibold text-green-400">+{{ number_format($growthDiff, 0, ',', '.') }}</div>
                                <div class="text-xs text-zinc-500">in 90 Tagen</div>
                            @endif
                        </div>
                    </div>
                    <div style="position: relative; height: 300px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================
         AKTIVITÄT & TRENDS — Konsolidierte Charts
         ============================================ --}}
    <section class="py-20 lg:py-28 sp-section-alt" aria-labelledby="charts-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="charts-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Aktivität & <span class="sp-gradient-text">Trends</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    30-Tage Überblick der Plattform-Nutzung
                </p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sp-fade-in">
                {{-- Chart A: Lernaktivität (Active Users + Questions) --}}
                <div class="glass" style="padding: 1.75rem; border-radius: 1.5rem 0.5rem 0.5rem 0.5rem;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Lernaktivität</div>
                        </div>
                        @php $lastQ = end($stats['questions_per_day']['values']); @endphp
                        <div class="text-right">
                            <span class="text-xl font-bold text-white">{{ number_format($lastQ, 0, ',', '.') }}</span>
                            <span class="text-xs text-zinc-500 ml-1">Fragen heute</span>
                        </div>
                    </div>
                    <div style="position: relative; height: 280px;">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>

                {{-- Chart B: Prüfungen & Erfolg (Exams + Success Rate) --}}
                <div class="glass" style="padding: 1.75rem; border-radius: 0.5rem 0.5rem 0.5rem 1.5rem;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Prüfungen & Erfolg</div>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-white">{{ $stats['avg_hit_rate'] }}%</span>
                            <span class="text-xs text-zinc-500 ml-1">Erfolgsquote</span>
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
         PLATTFORM IM DETAIL — Bento Grid
         ============================================ --}}
    <section class="py-20 lg:py-28 sp-section" aria-labelledby="details-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="details-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Plattform im <span class="sp-gradient-text">Detail</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    Zahlen und Fakten zum THW-Trainer
                </p>
            </header>

            <div class="sp-bento sp-fade-in">
                {{-- Hauptkarte: Trefferquote --}}
                <article class="sp-bento-main glass glass-blue" style="padding: 2.5rem; border-radius: 2rem 0.75rem 0.75rem 0.75rem;">
                    <span class="inline-block text-xs font-semibold uppercase tracking-wider mb-4 px-3 py-1 rounded-full sp-feature-badge">Community</span>
                    <h3 class="text-2xl lg:text-3xl font-bold text-white mb-4">Durchschnittliche Trefferquote</h3>
                    <div class="flex items-baseline gap-3 mt-4">
                        <span class="text-5xl lg:text-6xl font-extrabold sp-gradient-text">
                            {{ $stats['avg_hit_rate'] }}%
                        </span>
                        <span class="text-zinc-500 text-lg">aller Antworten korrekt</span>
                    </div>
                    <p class="text-zinc-400 leading-relaxed mt-4">
                        Über {{ number_format($stats['questions_answered'], 0, ',', '.') }} beantwortete Fragen
                        mit einer durchschnittlichen Trefferquote von {{ $stats['avg_hit_rate'] }}%.
                    </p>
                </article>

                {{-- Fragenkatalog --}}
                <article class="glass glass-tl" style="padding: 1.75rem;">
                    <h3 class="text-lg font-bold text-white mb-3">Fragenkatalog</h3>
                    <div class="text-3xl font-extrabold text-white mt-3">{{ number_format($stats['total_questions'], 0, ',', '.') }}</div>
                    <p class="text-sm text-zinc-400 leading-relaxed mt-2">
                        Theoriefragen in {{ count($stats['section_counts']) }} Lernabschnitten
                    </p>
                </article>

                {{-- Verfügbare Lehrgänge --}}
                <article class="glass glass-br" style="padding: 1.75rem;">
                    <h3 class="text-lg font-bold text-white mb-3">Lehrgänge</h3>
                    <div class="text-3xl font-extrabold text-white mt-3">{{ $stats['lehrgang_count'] }}</div>
                    <p class="text-sm text-zinc-400 leading-relaxed mt-2">
                        verfügbare Lehrgänge mit {{ number_format($stats['lehrgang_question_count'], 0, ',', '.') }} Fragen
                    </p>
                </article>

                {{-- Breite Karte: Lernabschnitte --}}
                <article class="sp-bento-wide glass" style="padding: 2rem; border-radius: 0.5rem 2rem 0.5rem 2rem;">
                    <div class="w-full">
                        <h3 class="text-lg font-bold text-white mb-4">Fragen pro Lernabschnitt</h3>
                        <div style="position: relative; height: 260px;">
                            <canvas id="sectionChart"></canvas>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         DSGVO-HINWEIS
         ============================================ --}}
    <section class="py-8 lg:py-12 sp-section-alt" aria-labelledby="privacy-heading">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass sp-fade-in" style="padding: 1.25rem 1.75rem; border-radius: 0.75rem;">
                <div class="flex items-start gap-3">
                    <i class="bi bi-shield-check text-lg" style="color: #5b9aff; margin-top: 2px;"></i>
                    <div>
                        <h3 id="privacy-heading" class="text-sm font-semibold text-white mb-1">Datenschutz-Hinweis</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">
                            Alle auf dieser Seite angezeigten Statistiken sind vollständig anonymisiert und aggregiert.
                            Es werden keine personenbezogenen Daten dargestellt. Nutzerzahlen sind gerundet.
                            Die Daten dienen ausschließlich der transparenten Darstellung der Plattformnutzung gem. Art. 6 Abs. 1 lit. f DSGVO.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         CTA SECTION
         ============================================ --}}
    <section class="sp-cta py-20 lg:py-28" aria-labelledby="cta-heading">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 id="cta-heading" class="text-2xl lg:text-4xl font-extrabold text-white mb-4 tracking-tight">
                Werde Teil der Community
            </h2>
            <p class="text-base lg:text-lg text-zinc-400 mb-4 max-w-3xl mx-auto leading-relaxed">
                Registriere dich kostenlos und starte jetzt mit deiner
                <strong class="font-semibold text-white">THW Grundausbildung Theorieprüfung</strong> Vorbereitung.
            </p>
            <p class="text-sm text-zinc-500 max-w-3xl mx-auto leading-relaxed mb-8">
                Bereits {{ number_format($stats['users'], 0, ',', '.') }}+ Helfer lernen mit dem THW-Trainer.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ $registerUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>
                <a href="{{ $loginUrl }}" class="sp-btn-ghost" aria-label="Zum Login">
                    Login
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================
         FOOTER
         ============================================ --}}
    <footer class="sp-footer py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW-Trainer" class="h-8 w-auto sp-logo-dark">
                        <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="THW-Trainer" class="h-8 w-auto sp-logo-light">
                        <span class="font-bold text-lg text-white">THW-Trainer</span>
                    </div>
                    <p class="text-sm text-zinc-500 max-w-xs leading-relaxed">
                        Kostenlose Lernplattform für die THW Grundausbildung Theorieprüfung.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Themen</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ url('/thw-theorie') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Theorie</a>
                        <a href="{{ url('/thw-pruefungsfragen') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Prüfungsfragen</a>
                        <a href="{{ url('/thw-grundausbildung') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Grundausbildung</a>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Plattform</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ url('/') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Startseite</a>
                        <a href="{{ route('landing.statistics') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Statistiken</a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Anonym üben</a>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Rechtliches</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('landing.impressum') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Impressum</a>
                        <a href="{{ route('landing.datenschutz') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Datenschutz</a>
                    </div>
                </div>
            </div>

            <div class="sp-copyright-border pt-6 text-center">
                <p class="text-xs text-zinc-600">&copy; {{ date('Y') }} THW-Trainer.de &mdash; Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </footer>

</div>

{{-- Counter Animation + Fade-in Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // sp-fade-in observer
    var fadeEls = document.querySelectorAll('.sp-fade-in');
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

    // Counter animation for stats
    var statEls = document.querySelectorAll('.sp-stat-value[data-count]');
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

    // Dark mode defaults
    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.color = '#a1a1aa';

    var darkScales = {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(255, 255, 255, 0.06)', drawBorder: false },
            ticks: { font: { size: 11 }, color: '#71717a' }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10 }, color: '#71717a', maxRotation: 45, minRotation: 45 }
        }
    };

    var tooltipStyle = {
        backgroundColor: 'rgba(15, 23, 42, 0.9)',
        titleColor: '#ffffff',
        bodyColor: '#d4d4d8',
        borderColor: 'rgba(255, 255, 255, 0.1)',
        borderWidth: 1,
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
    var srValues = @json($stats['success_rate_per_day']['values']);
    var exLabels = @json($stats['exams_per_day']['labels']);
    var exTotal = @json($stats['exams_per_day']['total']);
    var exPassed = @json($stats['exams_per_day']['passed']);
    var sectionLabels = @json(array_map(function($s) { return 'Abschnitt ' . $s; }, array_keys($stats['section_counts'])));
    var sectionValues = @json(array_values($stats['section_counts']));

    // Growth chart data
    var growthLabels = @json($stats['user_growth']['labels']);
    var growthTotal = @json($stats['user_growth']['total']);
    var growthVerified = @json($stats['user_growth']['verified']);

    // 1. User Growth (Area Chart)
    if (growthLabels.length > 0 && document.getElementById('growthChart')) {
        new Chart(document.getElementById('growthChart'), {
            type: 'line',
            data: {
                labels: growthLabels,
                datasets: [
                    {
                        label: 'Gesamt',
                        data: growthTotal,
                        borderColor: '#5b9aff',
                        backgroundColor: 'rgba(91, 154, 255, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#5b9aff',
                    },
                    {
                        label: 'Verifiziert',
                        data: growthVerified,
                        borderColor: 'rgba(147, 197, 253, 0.5)',
                        backgroundColor: 'rgba(147, 197, 253, 0.05)',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: '#93c5fd',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: { font: { size: 11 }, color: '#71717a', usePointStyle: true, pointStyle: 'line', padding: 16 }
                    },
                    tooltip: tooltipStyle
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(255, 255, 255, 0.06)', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#71717a' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#71717a',
                            maxRotation: 45,
                            minRotation: 45,
                            maxTicksLimit: 15
                        }
                    }
                }
            }
        });
    }

    // 2. Lernaktivität (Combined: Active Users Bar + Questions Line)
    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: qLabels,
            datasets: [
                {
                    label: 'Aktive Nutzer',
                    data: activeValues,
                    backgroundColor: 'rgba(91, 154, 255, 0.35)',
                    borderRadius: 4,
                    borderSkipped: false,
                    order: 2,
                    yAxisID: 'y',
                },
                {
                    label: 'Fragen',
                    data: qValues,
                    type: 'line',
                    borderColor: '#fbbf24',
                    backgroundColor: 'rgba(251, 191, 36, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#fbbf24',
                    order: 1,
                    yAxisID: 'y1',
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
                    labels: { font: { size: 11 }, color: '#71717a', usePointStyle: true, padding: 16 }
                },
                tooltip: tooltipStyle
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    grid: { color: 'rgba(255, 255, 255, 0.06)', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#71717a' },
                    title: { display: false }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#71717a' },
                    title: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#71717a', maxRotation: 45, minRotation: 45 }
                }
            }
        }
    });

    // 3. Prüfungen & Erfolg (Stacked Bar + Success Rate Line)
    new Chart(document.getElementById('examsChart'), {
        type: 'bar',
        data: {
            labels: exLabels,
            datasets: [
                {
                    label: 'Bestanden',
                    data: exPassed,
                    backgroundColor: 'rgba(34, 197, 94, 0.6)',
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'exams',
                    order: 2,
                    yAxisID: 'y',
                },
                {
                    label: 'Nicht bestanden',
                    data: exTotal.map(function(t, i) { return t - exPassed[i]; }),
                    backgroundColor: 'rgba(239, 68, 68, 0.4)',
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'exams',
                    order: 3,
                    yAxisID: 'y',
                },
                {
                    label: 'Erfolgsquote',
                    data: srValues,
                    type: 'line',
                    borderColor: '#93c5fd',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#93c5fd',
                    spanGaps: true,
                    order: 1,
                    yAxisID: 'y1',
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
                    labels: { font: { size: 11 }, color: '#71717a', usePointStyle: true, padding: 16 }
                },
                tooltip: tooltipStyle
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true,
                    position: 'left',
                    grid: { color: 'rgba(255, 255, 255, 0.06)', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#71717a' }
                },
                y1: {
                    beginAtZero: false,
                    min: 0,
                    max: 100,
                    position: 'right',
                    grid: { display: false },
                    ticks: {
                        font: { size: 11 },
                        color: '#71717a',
                        callback: function(v) { return v + '%'; }
                    }
                },
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#71717a', maxRotation: 45, minRotation: 45 }
                }
            }
        }
    });

    // 4. Section Breakdown (Horizontal Bar — Dark Mode)
    var sectionColors = [
        '#1e40af', '#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa',
        '#5b9aff', '#93c5fd', '#7dd3fc', '#38bdf8', '#0ea5e9'
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
                    grid: { color: 'rgba(255, 255, 255, 0.06)', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#71717a' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 12, weight: '500' }, color: '#d4d4d8' }
                }
            }
        }
    });
});
</script>
@endpush
