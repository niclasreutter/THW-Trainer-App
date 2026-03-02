@extends('layouts.landing')

@section('title', 'THW Theorie kostenlos lernen 2026 | Alle Prüfungsfragen + Lernen im Ortsverband')
@section('description', 'THW Theorie: alle aktuelle Prüfungsfragen. Grundausbildung & Lehrgänge. Eigene Fragen erstellen. Ortsverband-Lernpools. Kostenlos & werbefrei.')

@section('content')
<div class="overflow-x-hidden" style="background-color: #ffffff;">

    {{-- Account gelöscht Meldung --}}
    @if (session('status') == 'account-deleted')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            Dein Account wurde erfolgreich gelöscht. Alle deine Daten wurden permanent entfernt.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================
         HERO SECTION
         ============================================ --}}
    <section class="landing-hero" aria-label="Hauptbereich">
        <div class="landing-hero-content">
            <div class="landing-hero-split">
                {{-- Text Side --}}
                <div class="landing-hero-text">
                    <span class="landing-hero-tagline">Grundausbildung Theorie 2026</span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 tracking-tight text-white leading-tight">
                        <span class="landing-hero-gradient-text">THW Theorie</span>
                        <br>
                        <span class="text-white">kostenlos lernen</span>
                    </h1>

                    <p class="text-lg lg:text-xl text-blue-100 mb-8 max-w-xl leading-relaxed font-light" style="opacity: 0.85;">
                        Alle offiziellen Prüfungsfragen. Spaced Repetition. Ortsverband-Lernpools. Kostenlos und werbefrei.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        @php
                            $appUrl = config('domains.development')
                                ? route('dashboard')
                                : 'https://' . config('domains.app');
                        @endphp
                        <a href="{{ $appUrl }}"
                           class="landing-hero-btn-primary"
                           aria-label="Jetzt zur THW-Theorie App">
                            Jetzt Kostenlos Lernen
                        </a>

                        <a href="{{ route('landing.guest.practice.menu') }}"
                           class="landing-hero-btn-secondary"
                           aria-label="Anonym ohne Registrierung üben">
                            Anonym ohne Login üben
                        </a>
                    </div>

                    {{-- Micro Social Proof --}}
                    @if(isset($stats))
                    <p class="landing-hero-social-proof">
                        <strong>{{ number_format($stats['users'], 0, ',', '.') }}+</strong> Helfer lernen bereits mit dem THW-Trainer
                    </p>
                    @endif
                </div>

                {{-- Chart Visual --}}
                <div class="landing-hero-visual" aria-hidden="true">
                    <div class="landing-hero-chart">
                        <div class="landing-hero-chart-header">
                            <div>
                                <div class="landing-hero-chart-label">Beantwortete Fragen</div>
                                <div class="landing-hero-chart-sublabel">Letzte 15 Tage</div>
                            </div>
                            @if(isset($stats['chart']))
                                <div class="landing-hero-chart-total">{{ number_format(end($stats['chart'])['value'], 0, ',', '.') }}</div>
                            @endif
                        </div>
                        @if(isset($stats['chart']))
                            @php
                                $chartValues = collect($stats['chart'])->pluck('value')->toArray();
                                $chartLabels = collect($stats['chart'])->pluck('label')->toArray();
                                $minVal = min($chartValues);
                                $maxVal = max($chartValues);
                                $range = max(1, $maxVal - $minVal);
                                $width = 320;
                                $height = 160;
                                $padX = 10;
                                $padY = 15;
                                $usableW = $width - 2 * $padX;
                                $usableH = $height - 2 * $padY;
                                $coords = [];
                                foreach ($chartValues as $i => $v) {
                                    $x = $padX + ($i / (count($chartValues) - 1)) * $usableW;
                                    $y = $padY + $usableH - (($v - $minVal) / $range) * $usableH;
                                    $coords[] = ['x' => round($x, 1), 'y' => round($y, 1)];
                                }
                                // Build smooth bezier path
                                $linePath = 'M' . $coords[0]['x'] . ',' . $coords[0]['y'];
                                for ($i = 0; $i < count($coords) - 1; $i++) {
                                    $cx = ($coords[$i]['x'] + $coords[$i + 1]['x']) / 2;
                                    $linePath .= ' C' . $cx . ',' . $coords[$i]['y'] . ' ' . $cx . ',' . $coords[$i + 1]['y'] . ' ' . $coords[$i + 1]['x'] . ',' . $coords[$i + 1]['y'];
                                }
                                // Area path (same curve, closed at bottom)
                                $lastCoord = end($coords);
                                $firstCoord = $coords[0];
                                $areaPath = $linePath . ' L' . $lastCoord['x'] . ',' . $height . ' L' . $firstCoord['x'] . ',' . $height . ' Z';
                            @endphp
                            <svg class="landing-hero-chart-svg" viewBox="0 0 {{ $width }} {{ $height }}" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#fbbf24" stop-opacity="0.3"/>
                                        <stop offset="100%" stop-color="#fbbf24" stop-opacity="0.01"/>
                                    </linearGradient>
                                    <filter id="lineGlow">
                                        <feGaussianBlur stdDeviation="5" result="blur"/>
                                        <feMerge>
                                            <feMergeNode in="blur"/>
                                            <feMergeNode in="SourceGraphic"/>
                                        </feMerge>
                                    </filter>
                                </defs>

                                {{-- Grid lines --}}
                                @for($i = 0; $i < 4; $i++)
                                    <line x1="{{ $padX }}" y1="{{ $padY + ($i / 3) * $usableH }}" x2="{{ $padX + $usableW }}" y2="{{ $padY + ($i / 3) * $usableH }}" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
                                @endfor

                                {{-- Area fill --}}
                                <path d="{{ $areaPath }}" fill="url(#chartGlow)" class="chart-area"/>

                                {{-- Glow line (behind) --}}
                                <path d="{{ $linePath }}" fill="none" stroke="#fbbf24" stroke-width="4" stroke-opacity="0.4" filter="url(#lineGlow)" stroke-linecap="round" stroke-linejoin="round" class="chart-line"/>

                                {{-- Main line --}}
                                <path d="{{ $linePath }}" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chart-line"/>

                                {{-- Data dots (only every 3rd + last) --}}
                                @foreach($coords as $i => $c)
                                    @if($i % 3 === 0 || $i === count($coords) - 1)
                                        <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="3.5" fill="#fbbf24" class="chart-dot" style="animation-delay: {{ 2.5 + $i * 0.08 }}s;"/>
                                    @endif
                                @endforeach
                            </svg>

                            {{-- X-Axis labels --}}
                            <div class="landing-hero-chart-axis">
                                <span>{{ $chartLabels[0] }}</span>
                                <span>{{ $chartLabels[7] }}</span>
                                <span>{{ $chartLabels[14] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         STATS STRIP — Overlapping Hero/Content
         ============================================ --}}
    @if(isset($stats))
    <section class="px-4 sm:px-6 lg:px-8" style="background-color: #ffffff;" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="sr-only">Plattform-Statistiken</h2>
        <div class="landing-stats-strip landing-fade-in">
            <div class="landing-stat-item">
                <div class="landing-stat-value">{{ number_format($stats['users'] ?? 0, 0, ',', '.') }}+</div>
                <div class="landing-stat-label">Registrierte Nutzer</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value">{{ number_format($stats['questions_answered'] ?? 0, 0, ',', '.') }}+</div>
                <div class="landing-stat-label">Fragen beantwortet</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value">{{ number_format($stats['exams_passed'] ?? 0, 0, ',', '.') }}+</div>
                <div class="landing-stat-label">Prüfungen bestanden</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value">{{ $stats['pass_rate'] ?? 0 }}%</div>
                <div class="landing-stat-label">Bestehensquote</div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================
         FEATURES SECTION — Bento Grid
         ============================================ --}}
    <section id="features" class="py-16 lg:py-24 bg-white" aria-labelledby="features-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="features-heading">Was bietet der THW-Trainer?</h2>
                <p>Alles was du für deine Grundausbildung und darüber hinaus brauchst</p>
            </header>

            <div class="landing-bento">
                {{-- Hauptfeature: Alle Theoriefragen (2x2) --}}
                <article class="landing-bento-card landing-bento-main landing-fade-in">
                    <span class="landing-bento-tag">Hauptfeature</span>
                    <h3 class="landing-bento-title">Alle Theoriefragen</h3>
                    <p class="landing-bento-text">
                        Umfassende Sammlung aller aktuellen THW-Theoriefragen zum Üben und Lernen.
                        Von den Grundlagen der Grundausbildung bis zu spezialisierten Bereichen.
                        Alle Fragen werden regelmäßig aktualisiert und spiegeln den aktuellen Stand
                        der THW-Ausbildung wider.
                    </p>
                </article>

                {{-- Prüfungssimulation --}}
                <article class="landing-bento-card landing-bento-side landing-fade-in">
                    <h3 class="landing-bento-title">Prüfungssimulation</h3>
                    <p class="landing-bento-text">
                        Realistische Prüfungssimulation unter echten Bedingungen.
                        Teste dein Wissen bevor es ernst wird.
                    </p>
                </article>

                {{-- Spaced Repetition --}}
                <article class="landing-bento-card landing-bento-side landing-fade-in" style="border-radius: 0.75rem 2rem 0.75rem 0.75rem;">
                    <h3 class="landing-bento-title">Spaced Repetition</h3>
                    <p class="landing-bento-text">
                        Intelligentes Wiederholungssystem priorisiert deine Schwachstellen
                        und sorgt für nachhaltiges Lernen.
                    </p>
                </article>

                {{-- Breite Karte: Lehrgänge + Lernpools --}}
                <article class="landing-bento-card landing-bento-wide landing-fade-in">
                    <div>
                        <h3 class="landing-bento-title">Lehrgänge</h3>
                        <p class="landing-bento-text">
                            Nicht nur Grundausbildung: Bereite dich auf verschiedene THW-Lehrgänge vor
                            mit spezifischen Fragenkatalogen.
                        </p>
                    </div>
                    <div>
                        <h3 class="landing-bento-title">Ortsverband-Lernpools</h3>
                        <p class="landing-bento-text">
                            Dein Ortsverband kann eigene Lernpools erstellen.
                            Lerne gemeinsam mit deinen Kameraden und teile den Fortschritt.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         SO FUNKTIONIERT'S — 3 Steps
         ============================================ --}}
    <section class="py-16 lg:py-20 bg-slate-50" aria-labelledby="steps-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="steps-heading">So funktioniert's</h2>
                <p>In drei Schritten zur bestandenen Prüfung</p>
            </header>

            <div class="landing-steps landing-fade-in">
                <div class="landing-step">
                    <div class="landing-step-number">1</div>
                    <h3 class="landing-step-title">Registrieren</h3>
                    <p class="landing-step-desc">
                        Kostenlos anmelden oder anonym starten. Kein Abo, keine versteckten Kosten.
                    </p>
                </div>
                <div class="landing-step">
                    <div class="landing-step-number">2</div>
                    <h3 class="landing-step-title">Lernen</h3>
                    <p class="landing-step-desc">
                        Fragen beantworten, Fortschritt verfolgen und mit Spaced Repetition nachhaltig lernen.
                    </p>
                </div>
                <div class="landing-step">
                    <div class="landing-step-number">3</div>
                    <h3 class="landing-step-title">Prüfung bestehen</h3>
                    <p class="landing-step-desc">
                        Mit der Prüfungssimulation testen und dann die echte Prüfung souverän meistern.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         TESTIMONIALS — Asymmetric Layout
         ============================================ --}}
    <section class="py-16 lg:py-20 bg-white" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="testimonials-heading">Das sagen THW-Helfer</h2>
                <p>Erfahrungen von Helfern, die sich mit dem THW-Trainer vorbereitet haben</p>
            </header>

            <div class="landing-testimonials-grid landing-fade-in">
                {{-- Featured Testimonial (spans 2 rows) --}}
                <div class="landing-testimonial-featured">
                    <article class="landing-testimonial-card">
                        <div class="landing-quote-mark" aria-hidden="true">"</div>
                        <blockquote class="landing-quote-text">
                            Der THW-Trainer hat mir geholfen, mich optimal auf die Grundausbildung vorzubereiten.
                            Die Prüfungssimulation war besonders hilfreich! Ich konnte genau sehen, wo ich noch
                            Schwächen hatte und gezielt daran arbeiten.
                        </blockquote>
                        <div class="landing-quote-author">
                            <div class="landing-quote-name">Markus H.</div>
                            <div class="landing-quote-role">THW-Helfer</div>
                        </div>
                    </article>
                </div>

                {{-- Testimonial 2 --}}
                <article class="landing-testimonial-card">
                    <div class="landing-quote-mark" aria-hidden="true">"</div>
                    <blockquote class="landing-quote-text">
                        Endlich eine moderne Lernplattform für die THW-Theorie. Durch die Spaced Repetition Funktion habe ich mir die Inhalte nachhaltig gemerkt.
                    </blockquote>
                    <div class="landing-quote-author">
                        <div class="landing-quote-name">Sarah K.</div>
                        <div class="landing-quote-role">THW-Helferin</div>
                    </div>
                </article>

                {{-- Testimonial 3 --}}
                <article class="landing-testimonial-card">
                    <div class="landing-quote-mark" aria-hidden="true">"</div>
                    <blockquote class="landing-quote-text">
                        Sehr übersichtlich und motivierend. Durch die täglichen Lernziele habe ich regelmäßig gelernt und die Prüfung beim ersten Versuch bestanden.
                    </blockquote>
                    <div class="landing-quote-author">
                        <div class="landing-quote-name">Thomas M.</div>
                        <div class="landing-quote-role">THW-Helfer</div>
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
                Bereit zum Lernen?
            </h2>
            <p class="text-base lg:text-lg text-blue-100 mb-4 max-w-3xl mx-auto leading-relaxed font-light">
                Starte jetzt mit dem THW-Trainer und bereite dich optimal auf deine
                <strong class="font-semibold text-white">Grundausbildung Theorie-Prüfung im THW</strong> vor.
            </p>
            <p class="text-sm lg:text-base text-blue-200 max-w-3xl mx-auto leading-relaxed font-light mb-8" style="opacity: 0.8;">
                Registriere dich kostenlos und beginne sofort mit dem Lernen, egal ob Handy, Laptop oder Tablet.
                Ein Account, ein Lernstand!
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

    {{-- ============================================
         FAQ SECTION
         ============================================ --}}
    <section id="faq" class="py-16 lg:py-20 bg-slate-50" aria-labelledby="faq-heading">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="landing-section-header center">
                <h2 id="faq-heading">Häufig gestellte Fragen</h2>
                <p>Alles über THW Grundausbildung und den THW-Trainer</p>
            </header>

            <div class="space-y-3" itemscope itemtype="https://schema.org/FAQPage">
                {{-- FAQ 1 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq1')" aria-expanded="false" aria-controls="faq1">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Was ist die THW Grundausbildung?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq1" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Die <strong>THW Grundausbildung</strong> ist die erste Ausbildungsstufe im <strong>Technischen Hilfswerk</strong>.
                            Sie vermittelt die grundlegenden Kenntnisse und Fähigkeiten für alle THW-Helfer.
                            Die <strong>Theorie-Prüfung</strong> ist ein wichtiger Bestandteil dieser Ausbildung und umfasst
                            Themen wie Rechtsgrundlagen, Organisation des THW, Einsatzgrundlagen und technisches Wissen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 2 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq2')" aria-expanded="false" aria-controls="faq2">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Wie bereite ich mich auf die THW Grundausbildung Theorie vor?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq2" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Der <strong>THW-Trainer</strong> bietet dir alle aktuellen <strong>THW-Theoriefragen</strong> zur optimalen Vorbereitung.
                            Übe systematisch alle Themenbereiche, nutze die <strong>Prüfungssimulation</strong> und verfolge deinen Lernfortschritt.
                            Die App funktioniert auf allen Geräten, sodass du auch unterwegs lernen kannst.
                        </p>
                    </div>
                </article>

                {{-- FAQ 3 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq3')" aria-expanded="false" aria-controls="faq3">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Ist der THW-Trainer kostenlos?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq3" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Ja, der <strong>THW-Trainer ist komplett kostenlos</strong>! Du kannst sofort mit dem Lernen beginnen,
                            ohne jegliche Kosten. Auch eine Anmeldung ist nicht zwingend erforderlich -
                            du kannst anonym üben oder dich kostenlos registrieren, um deinen Lernfortschritt zu speichern.
                        </p>
                    </div>
                </article>

                {{-- FAQ 4 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq4')" aria-expanded="false" aria-controls="faq4">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Wie viele Fragen gibt es im THW-Trainer?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq4" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Der THW-Trainer enthält <strong>alle aktuellen THW-Theoriefragen</strong> aus allen relevanten Bereichen
                            der Grundausbildung. Die Fragen werden regelmäßig aktualisiert und spiegeln den
                            aktuellen Stand der THW-Ausbildung wider.
                        </p>
                    </div>
                </article>

                {{-- FAQ 5 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq5')" aria-expanded="false" aria-controls="faq5">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Welche Themen werden in der THW Grundausbildung abgefragt?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq5" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Die <strong>THW Grundausbildung</strong> umfasst Themen wie: <strong>Rechtsgrundlagen</strong>, Organisation des THW,
                            Einsatzgrundlagen, Gefahren der Einsatzstelle, Technische Hilfe, Einsatzablauf,
                            Führung und Kommunikation.
                        </p>
                    </div>
                </article>

                {{-- FAQ 6 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq6')" aria-expanded="false" aria-controls="faq6">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Funktioniert der THW-Trainer auf dem Handy?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq6" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Ja, der THW-Trainer ist <strong>vollständig responsive</strong> und funktioniert optimal auf Smartphones,
                            Tablets und Desktop-Computern. Du kannst die App als <strong>Progressive Web App</strong> auf deinem
                            Homescreen installieren für schnelleren Zugriff.
                        </p>
                    </div>
                </article>

                {{-- FAQ 7 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq7')" aria-expanded="false" aria-controls="faq7">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Wie schwer ist die THW Grundausbildung Theorie-Prüfung?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq7" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq7" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Mit der richtigen Vorbereitung ist die <strong>THW Grundausbildung Theorie-Prüfung</strong> gut zu schaffen.
                            Der THW-Trainer hilft dir dabei, alle wichtigen Themen zu verstehen und zu üben.
                            Nutze die <strong>Prüfungssimulation</strong>, um dich unter realistischen Bedingungen zu testen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 8 --}}
                <article class="landing-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="landing-faq-toggle" onclick="toggleFAQ('faq8')" aria-expanded="false" aria-controls="faq8">
                        <span class="text-base lg:text-lg font-semibold text-slate-900 pr-4 text-left" itemprop="name">Ist der THW-Trainer offiziell vom THW?</span>
                        <svg class="landing-faq-chevron" id="chevron-faq8" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="faq8" class="landing-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-slate-600 leading-relaxed text-sm lg:text-base" itemprop="text">
                            Der THW-Trainer ist eine <strong>private Initiative</strong> eines aktiven THW-Mitglieds und nicht offiziell
                            vom THW herausgegeben. Die Fragen basieren jedoch auf den offiziellen Ausbildungsunterlagen
                            und werden regelmäßig aktualisiert, um den aktuellen Stand der THW-Ausbildung zu reflektieren.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         DER KOPF DAHINTER
         ============================================ --}}
    <section class="py-16 lg:py-20 bg-white" aria-labelledby="about-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="landing-about-card landing-fade-in">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
                    {{-- Bild --}}
                    <div class="flex-shrink-0 flex justify-center lg:justify-start">
                        <picture>
                            <source srcset="{{ asset('niclas_compressed.webp') }}" type="image/webp">
                            <img src="{{ asset('niclas_compressed.png') }}"
                                 alt="Niclas Reutter - Entwickler und aktives THW-Mitglied, Entwickler des THW-Trainers"
                                 class="landing-about-image"
                                 style="max-height: 300px; max-width: 250px;"
                                 loading="lazy"
                                 width="250"
                                 height="300">
                        </picture>
                    </div>

                    {{-- Text --}}
                    <div class="flex-1 space-y-5" itemscope itemtype="https://schema.org/Person">
                        <div>
                            <h2 id="about-heading" class="text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">
                                Der Kopf Dahinter
                            </h2>
                            <div class="landing-about-name-accent"></div>
                        </div>
                        <div class="space-y-4 text-base lg:text-lg text-slate-600 leading-relaxed">
                            <p>
                                Hallo! Ich bin <span itemprop="name">Niclas</span>, der <span itemprop="jobTitle">Entwickler</span> hinter dem <strong>THW-Trainer</strong>. Als aktives <strong>THW-Mitglied</strong>
                                kenne ich die Herausforderungen bei der Vorbereitung auf die Theoriefragen nur zu gut.
                            </p>
                            <p>
                                Mit dieser App möchte ich dir eine moderne, intuitive und effektive Möglichkeit bieten,
                                dich optimal auf deine <strong>THW-Prüfung</strong> vorzubereiten. Alle Fragen sind sorgfältig ausgewählt
                                und spiegeln den aktuellen Stand der <strong>THW-Ausbildung</strong> wider.
                            </p>
                            <p class="font-semibold">
                                Viel Erfolg bei deiner Prüfung!
                            </p>
                            <p>
                                Diese Webseite stelle ich <strong>kostenlos zur Verfügung</strong> und finanziere alle Kosten für Webseite, Domain und Server selbst.
                                Unterstütze mich mit einem Kaffee!
                            </p>
                        </div>
                        <div class="pt-2">
                            <a href="https://bero-host.de/spenden/ks14llyclh8q"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-800 to-blue-900 text-white font-bold rounded-xl hover:scale-105 transition-all duration-300 shadow-lg"
                               aria-label="Unterstütze den Entwickler mit einer Kaffee-Spende">
                                Unterstütze mich mit einem Kaffee
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- FAQ Toggle Script --}}
<script>
function toggleFAQ(faqId) {
    const content = document.getElementById(faqId);
    const button = content?.previousElementSibling;
    const chevron = document.getElementById('chevron-' + faqId);

    if (!content || !button) return;

    const isOpen = content.classList.contains('open');

    // Close all other FAQs
    document.querySelectorAll('.landing-faq-content.open').forEach(el => {
        el.classList.remove('open');
        const btn = el.previousElementSibling;
        if (btn) btn.setAttribute('aria-expanded', 'false');
        const chev = document.getElementById('chevron-' + el.id);
        if (chev) chev.classList.remove('open');
    });

    if (!isOpen) {
        content.classList.add('open');
        button.setAttribute('aria-expanded', 'true');
        if (chevron) chevron.classList.add('open');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Keyboard accessibility for FAQ
    document.querySelectorAll('.landing-faq-toggle').forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    });

    // Scroll fade-in animation
    const fadeEls = document.querySelectorAll('.landing-fade-in');
    if (fadeEls.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        fadeEls.forEach(function(el) { observer.observe(el); });
    } else {
        // Fallback: show everything
        fadeEls.forEach(function(el) { el.classList.add('visible'); });
    }

    // Stats are rendered server-side — no JS animation needed
});
</script>

{{-- Schema.org Structured Data --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Was ist die THW Grundausbildung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die THW Grundausbildung ist die erste Ausbildungsstufe im Technischen Hilfswerk. Sie vermittelt die grundlegenden Kenntnisse und Fähigkeiten für alle THW-Helfer. Die Theorie-Prüfung ist ein wichtiger Bestandteil dieser Ausbildung."
            }
        },
        {
            "@@type": "Question",
            "name": "Ist der THW-Trainer kostenlos?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, der THW-Trainer ist komplett kostenlos! Du kannst sofort mit dem Lernen beginnen, ohne jegliche Kosten."
            }
        }
    ]
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "THW-Trainer",
    "url": "{{ url('/') }}",
    "description": "Kostenlose THW Theorie Lernplattform für Grundausbildung und mehr.",
    "applicationCategory": "EducationalApplication",
    "operatingSystem": "Web Browser, iOS, Android",
    "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR"
    },
    "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "5",
        "bestRating": "5",
        "ratingCount": "3"
    },
    "review": [
        {
            "@@type": "Review",
            "reviewBody": "Der THW-Trainer hat mir geholfen, mich optimal auf die Grundausbildung vorzubereiten. Die Prüfungssimulation war besonders hilfreich!",
            "author": { "@@type": "Person", "name": "Markus H." },
            "reviewRating": { "@@type": "Rating", "ratingValue": "5", "bestRating": "5" }
        },
        {
            "@@type": "Review",
            "reviewBody": "Endlich eine moderne Lernplattform für die THW-Theorie. Durch die Spaced Repetition Funktion habe ich mir die Inhalte nachhaltig gemerkt.",
            "author": { "@@type": "Person", "name": "Sarah K." },
            "reviewRating": { "@@type": "Rating", "ratingValue": "5", "bestRating": "5" }
        },
        {
            "@@type": "Review",
            "reviewBody": "Sehr übersichtlich und motivierend. Durch die täglichen Lernziele habe ich regelmäßig gelernt und die Prüfung beim ersten Versuch bestanden.",
            "author": { "@@type": "Person", "name": "Thomas M." },
            "reviewRating": { "@@type": "Rating", "ratingValue": "5", "bestRating": "5" }
        }
    ]
}
</script>

@endsection
