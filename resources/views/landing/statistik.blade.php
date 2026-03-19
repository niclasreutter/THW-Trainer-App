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
         AKTIVITÄTS-CHART — Aktive Nutzer
         ============================================ --}}
    @if(!empty($stats['chart']))
    <section class="py-16 lg:py-24 bg-white" aria-labelledby="activity-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="activity-heading">Aktive Nutzer</h2>
                <p>Tägliche Aktivität der letzten 15 Tage</p>
            </header>

            <div class="max-w-4xl mx-auto landing-fade-in">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <div class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Aktive Nutzer pro Tag</div>
                        </div>
                        @php
                            $lastDay = end($stats['chart']);
                        @endphp
                        <div class="text-right">
                            <div class="text-2xl font-bold text-slate-900">{{ $lastDay['value'] }}</div>
                            <div class="text-xs text-slate-500">heute aktiv</div>
                        </div>
                    </div>

                    @php
                        $chartValues = collect($stats['chart'])->pluck('value')->toArray();
                        $chartLabels = collect($stats['chart'])->pluck('label')->toArray();
                        $maxVal = max($chartValues) ?: 1;
                    @endphp

                    {{-- Bar Chart --}}
                    <div class="flex items-end gap-1 sm:gap-2" style="height: 200px;">
                        @foreach($chartValues as $i => $val)
                            @php
                                $heightPct = $maxVal > 0 ? max(($val / $maxVal) * 100, 4) : 4;
                                $isToday = $i === count($chartValues) - 1;
                            @endphp
                            <div class="flex-1 flex flex-col items-center justify-end h-full">
                                <span class="text-xs font-semibold mb-1 {{ $val > 0 ? 'text-slate-700' : 'text-slate-300' }}">
                                    {{ $val > 0 ? $val : '' }}
                                </span>
                                <div class="w-full rounded-t-md transition-all duration-500 {{ $isToday ? 'bg-gradient-to-t from-amber-500 to-yellow-400' : 'bg-gradient-to-t from-blue-800 to-blue-600' }}"
                                     style="height: {{ $heightPct }}%; min-height: 4px;"></div>
                                <span class="text-xs text-slate-400 mt-2 {{ $i % 2 === 0 ? '' : 'hidden sm:block' }}">
                                    {{ $chartLabels[$i] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

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

                {{-- Breite Karte: Lernabschnitte --}}
                <article class="landing-bento-card landing-bento-wide landing-fade-in">
                    <div class="w-full">
                        <h3 class="landing-bento-title mb-4">Fragen pro Lernabschnitt</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                            @foreach($stats['section_counts'] as $section => $count)
                                <div class="text-center p-3 rounded-xl bg-white/60 border border-slate-200">
                                    <div class="text-xs text-slate-500 uppercase tracking-wider">Abschnitt {{ $section }}</div>
                                    <div class="text-xl font-bold text-slate-900 mt-1">{{ $count }}</div>
                                    <div class="text-xs text-slate-400">Fragen</div>
                                </div>
                            @endforeach
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
