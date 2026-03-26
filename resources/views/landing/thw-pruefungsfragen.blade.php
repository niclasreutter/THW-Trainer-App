@extends('layouts.startseite')

@section('title', 'THW Prüfungsfragen — Alle Fragen der Grundausbildung kostenlos üben 2026')
@section('description', 'Alle offiziellen THW Prüfungsfragen der Grundausbildung (GA) kostenlos online üben. ' . $totalQuestions . ' Fragen, Prüfungssimulation, Lernfortschritt. Jetzt THW Prüfungsfragen lernen!')
@section('canonical', url('/thw-pruefungsfragen'))

@section('content')
@php
    $loginUrl = config('domains.development') ? route('login') : 'https://' . config('domains.app') . '/login';
    $registerUrl = config('domains.development') ? route('register') : 'https://' . config('domains.app') . '/register';
    $appUrl = config('domains.development') ? route('dashboard') : 'https://' . config('domains.app');
@endphp
<div>

    {{-- ============================================
         NAVBAR - Compact
         ============================================ --}}
    <nav x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50"
         class="sp-navbar" :class="{ 'scrolled': scrolled }" aria-label="Hauptnavigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer Logo" class="h-8 w-auto sp-logo-dark">
                <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" class="h-8 w-auto sp-logo-light">
                <span class="font-bold text-xl text-white">THW-Trainer</span>
            </a>
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200 px-4 py-2">Startseite</a>
                <a href="{{ route('landing.statistics') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200 px-4 py-2">Statistiken</a>
                <a href="{{ $registerUrl }}" class="sp-btn-gold text-sm" style="padding: 0.5rem 1.25rem;">Kostenlos starten</a>
            </div>
        </div>
    </nav>

    {{-- ============================================
         HERO SECTION - Compact
         ============================================ --}}
    <section class="sp-hero" style="min-height: auto; padding: 6rem 0 3rem;" aria-label="THW Prüfungsfragen">
        <div class="sp-orb sp-orb-1" aria-hidden="true"></div>
        <div class="sp-orb sp-orb-2" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center py-12 lg:py-16">
                {{-- Text Side --}}
                <div>
                    {{-- Breadcrumb --}}
                    <nav class="mb-6 text-sm text-zinc-500" aria-label="Breadcrumb">
                        <a href="{{ url('/') }}" class="hover:text-white transition-colors">Startseite</a>
                        <span class="mx-2">/</span>
                        <span class="text-zinc-300">THW Prüfungsfragen</span>
                    </nav>

                    <span class="sp-hero-badge">Offizieller Fragenkatalog 2026</span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                        THW Prüfungsfragen
                        <br>
                        <span class="sp-gradient-text">online üben</span>
                    </h1>

                    <p class="text-lg lg:text-xl text-zinc-400 mb-10 max-w-xl leading-relaxed">
                        Übe alle {{ $totalQuestions }} offiziellen Prüfungsfragen der THW Grundausbildung. Lerne gezielt mit Karteikarten-System, verfolge deinen Fortschritt und bereite dich optimal auf die Theorieprüfung vor.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ $appUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos THW Prüfungsfragen üben">
                            Kostenlos starten
                        </a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" aria-label="THW Prüfungsfragen anonym üben">
                            Anonym üben
                        </a>
                    </div>
                </div>

                {{-- App Mockup --}}
                <div class="hidden lg:block">
                    <div class="sp-mockup">
                        <div class="glass glass-blue" style="padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;">
                            {{-- Mock Question Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <div class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Prüfungsfrage</div>
                                    <div class="text-2xl font-bold text-white">Frage 12 / 30</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-extrabold sp-gradient-text">83%</div>
                                    <div class="text-xs text-zinc-500">richtig</div>
                                </div>
                            </div>

                            {{-- Mock Progress Bar --}}
                            <div class="sp-progress-track" style="height: 6px; border-radius: 3px; margin-bottom: 1.75rem; overflow: hidden;">
                                <div style="width: 40%; height: 100%; background: linear-gradient(90deg, #00337F, #5b9aff); border-radius: 3px;"></div>
                            </div>

                            {{-- Mock Question --}}
                            <div class="glass" style="padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1rem;">
                                <div class="text-sm text-zinc-300 leading-relaxed">Welche persönliche Schutzausstattung gehört zur Grundausstattung eines THW-Helfers?</div>
                            </div>

                            {{-- Mock Answer Options --}}
                            <div class="space-y-2">
                                <div class="glass-subtle" style="padding: 0.75rem 1rem; border-radius: 0.75rem; border-left: 3px solid #5b9aff;">
                                    <div class="text-sm text-zinc-300"><span class="text-zinc-500 font-bold mr-2">A</span>Helm, Schutzbrille, Gehörschutz</div>
                                </div>
                                <div class="glass-subtle" style="padding: 0.75rem 1rem; border-radius: 0.75rem;">
                                    <div class="text-sm text-zinc-400"><span class="text-zinc-500 font-bold mr-2">B</span>Atemschutzgerät, Strahlenschutzanzug</div>
                                </div>
                                <div class="glass-subtle" style="padding: 0.75rem 1rem; border-radius: 0.75rem;">
                                    <div class="text-sm text-zinc-400"><span class="text-zinc-500 font-bold mr-2">C</span>Taucheranzug, Schwimmweste</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 hidden lg:block" aria-hidden="true">
            <div class="sp-scroll-indicator">
                <div class="sp-scroll-dot"></div>
            </div>
        </div>
    </section>

    {{-- ============================================
         STATS ROW
         ============================================ --}}
    <section class="py-12 lg:py-16 relative sp-section-alt" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="sr-only">THW Prüfungsfragen in Zahlen</h2>
        <div class="sp-glow-border" style="position: absolute; top: 0; left: 0; right: 0; height: 1px;" aria-hidden="true"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sp-stats">
                <div class="sp-stat-card">
                    <div class="sp-stat-value">{{ $totalQuestions }}</div>
                    <div class="sp-stat-label">Prüfungsfragen</div>
                </div>
                <div class="sp-stat-card">
                    <div class="sp-stat-value">30</div>
                    <div class="sp-stat-label">Fragen pro Prüfung</div>
                </div>
                <div class="sp-stat-card">
                    <div class="sp-stat-value">80%</div>
                    <div class="sp-stat-label">zum Bestehen</div>
                </div>
                @if($stats)
                <div class="sp-stat-card">
                    <div class="sp-stat-value">{{ $stats['pass_rate'] }}%</div>
                    <div class="sp-stat-label">Bestehensquote</div>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ============================================
         CONTENT SECTION - SEO Text
         ============================================ --}}
    <section class="py-20 lg:py-28 sp-section-alt" aria-labelledby="content-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sp-fade-in">
                <h2 id="content-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-10">
                    Der offizielle <span class="sp-gradient-text">THW Fragenkatalog</span>
                </h2>

                <div class="space-y-8">
                    <div class="glass" style="padding: 2rem; border-radius: 1.5rem 0.5rem 0.5rem 0.5rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Alle THW Prüfungsfragen auf einen Blick</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Der offizielle Fragenkatalog der <a href="{{ url('/thw-grundausbildung') }}" class="text-blue-400 hover:text-blue-300 transition-colors">THW Grundausbildung</a>
                            umfasst <strong class="text-zinc-300">{{ $totalQuestions }} Prüfungsfragen</strong> aus 10 Lernabschnitten. Jede Frage bietet drei Antwortmöglichkeiten (A, B, C),
                            von denen eine oder mehrere richtig sein können. Im THW-Trainer findest du den kompletten Fragenkatalog zum
                            kostenlosen Üben — übersichtlich nach Lernabschnitten gegliedert.
                        </p>
                    </div>

                    <div class="glass glass-slash" style="padding: 2rem;">
                        <h3 class="text-lg font-bold text-white mb-3">THW Prüfungsfragen online üben</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Mit dem THW-Trainer übst du alle Prüfungsfragen gezielt und effizient. Das intelligente
                            <strong class="text-zinc-300">Spaced-Repetition-System</strong> erkennt deine Schwächen und wiederholt schwierige Fragen
                            automatisch häufiger. Verfolge deinen Lernfortschritt pro Lernabschnitt und sieh jederzeit, welche
                            <a href="{{ url('/thw-theorie') }}" class="text-blue-400 hover:text-blue-300 transition-colors">THW Theorie</a>-Themen du noch vertiefen solltest.
                        </p>
                    </div>

                    <div class="glass glass-tl" style="padding: 2rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Von der Übung zur Prüfung</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Wenn du alle Prüfungsfragen sicher beherrschst, teste dich mit der <strong class="text-zinc-300">Prüfungssimulation</strong>.
                            Dabei werden — genau wie in der echten <a href="{{ url('/thw-theoriepruefung') }}" class="text-blue-400 hover:text-blue-300 transition-colors">THW Theorieprüfung</a> —
                            30 Fragen zufällig ausgewählt. Zum Bestehen müssen mindestens 80% korrekt beantwortet werden. So gehst du
                            optimal vorbereitet in die Prüfung.
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
        <div class="max-w-3xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 id="cta-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-5">
                Bereit für die THW Prüfungsfragen?
            </h2>
            <p class="text-lg text-blue-100 mb-4 max-w-2xl mx-auto leading-relaxed" style="opacity: 0.85;">
                Starte jetzt und übe alle <strong class="text-white">{{ $totalQuestions }} Prüfungsfragen</strong> der Grundausbildung.
            </p>
            <p class="text-sm text-blue-200 mb-10 max-w-2xl mx-auto" style="opacity: 0.65;">
                Kostenlos, werbefrei und auf allen Geräten verfügbar.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $registerUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>
                <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" style="border-color: rgba(255,255,255,0.25);" aria-label="THW Prüfungsfragen anonym üben">
                    Erst mal ausprobieren
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================
         FAQ SECTION
         ============================================ --}}
    <section id="faq" class="py-20 lg:py-28 sp-section" aria-labelledby="faq-heading">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-12 sp-fade-in">
                <h2 id="faq-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Häufig gestellte <span class="sp-gradient-text">Fragen</span>
                </h2>
            </header>

            <div class="space-y-3 sp-fade-in">
                {{-- FAQ 1 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie viele THW Prüfungsfragen gibt es?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Der offizielle Fragenkatalog der THW Grundausbildung umfasst {{ $totalQuestions }} Fragen aus 10 Lernabschnitten. In der Prüfung werden davon 30 Fragen zufällig ausgewählt. Im THW-Trainer kannst du alle Fragen üben.
                        </p>
                    </div>
                </article>

                {{-- FAQ 2 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Sind das die offiziellen THW Prüfungsfragen?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Ja, der THW-Trainer enthält alle offiziellen Prüfungsfragen der THW Grundausbildung (GA). Die Fragen werden regelmäßig auf Aktualität geprüft und aktualisiert.
                        </p>
                    </div>
                </article>

                {{-- FAQ 3 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Kann ich die THW Prüfungsfragen kostenlos üben?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Ja, alle Prüfungsfragen sind komplett kostenlos und ohne Werbung verfügbar. Du kannst sofort anonym starten oder dich registrieren, um deinen Lernfortschritt zu speichern.
                        </p>
                    </div>
                </article>

                {{-- FAQ 4 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie sind die THW Prüfungsfragen aufgebaut?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Jede Frage hat drei Antwortmöglichkeiten (A, B, C). Es können eine oder mehrere Antworten richtig sein. Die Fragen decken alle 10 Lernabschnitte der Grundausbildung ab — von Arbeitssicherheit bis Rettungsgrundlagen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 5 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie lerne ich die THW Prüfungsfragen am besten?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Arbeite alle Fragen systematisch durch, nutze die Spaced-Repetition-Funktion für schwierige Fragen und teste dich regelmäßig mit der Prüfungssimulation. So gehst du optimal vorbereitet in die THW Theorieprüfung.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         FOOTER - Full 5-Column
         ============================================ --}}
    <footer class="sp-footer py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-8 mb-10">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer" class="h-8 w-auto sp-logo-dark">
                        <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer" class="h-8 w-auto sp-logo-light">
                        <span class="font-bold text-lg text-white">THW-Trainer</span>
                    </div>
                    <p class="text-sm text-zinc-500 max-w-xs leading-relaxed">
                        Kostenlose Lernplattform für die THW Grundausbildung Theorieprüfung.
                    </p>
                </div>

                <!-- Themen -->
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Themen</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ url('/thw-theorie') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Theorie</a>
                        <a href="{{ url('/thw-pruefungsfragen') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Prüfungsfragen</a>
                        <a href="{{ url('/thw-theoriepruefung') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Theorieprüfung</a>
                        <a href="{{ url('/thw-grundausbildung') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">THW Grundausbildung</a>
                    </div>
                </div>

                <!-- Plattform -->
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Plattform</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ url('/') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Startseite</a>
                        <a href="{{ route('landing.statistics') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Statistiken</a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Anonym üben</a>
                    </div>
                </div>

                <!-- Rechtliches -->
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Rechtliches</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('landing.impressum') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Impressum</a>
                        <a href="{{ route('landing.datenschutz') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Datenschutz</a>
                    </div>
                </div>

                <!-- Unterstützen -->
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Unterstützen</h4>
                    <p class="text-sm text-zinc-500 mb-3 leading-relaxed">
                        THW-Trainer ist kostenlos. Unterstütze die Entwicklung!
                    </p>
                    <a href="https://bero-host.de/spenden/ks14llyclh8q" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors cursor-pointer">
                        <i class="bi bi-cup-hot"></i>
                        Kaffee spendieren
                    </a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-8 sp-copyright-border">
                <p class="text-sm text-zinc-600">&copy; {{ date('Y') }} THW-Trainer. Entwickelt von Niclas Reutter.</p>
                <p class="text-xs text-zinc-700 mt-1">
                    Kein offizielles Angebot des THW. Private Initiative zur Prüfungsvorbereitung.
                </p>
            </div>
        </div>
    </footer>

</div>

{{-- Schema.org Structured Data --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Wie viele THW Prüfungsfragen gibt es?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Der offizielle Fragenkatalog der THW Grundausbildung umfasst {{ $totalQuestions }} Fragen aus 10 Lernabschnitten. In der Prüfung werden davon 30 Fragen zufällig ausgewählt. Im THW-Trainer kannst du alle Fragen üben."
            }
        },
        {
            "@@type": "Question",
            "name": "Sind das die offiziellen THW Prüfungsfragen?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, der THW-Trainer enthält alle offiziellen Prüfungsfragen der THW Grundausbildung (GA). Die Fragen werden regelmäßig auf Aktualität geprüft und aktualisiert."
            }
        },
        {
            "@@type": "Question",
            "name": "Kann ich die THW Prüfungsfragen kostenlos üben?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, alle Prüfungsfragen sind komplett kostenlos und ohne Werbung verfügbar. Du kannst sofort anonym starten oder dich registrieren, um deinen Lernfortschritt zu speichern."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie sind die THW Prüfungsfragen aufgebaut?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Jede Frage hat drei Antwortmöglichkeiten (A, B, C). Es können eine oder mehrere Antworten richtig sein. Die Fragen decken alle 10 Lernabschnitte der Grundausbildung ab — von Arbeitssicherheit bis Rettungsgrundlagen."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie lerne ich die THW Prüfungsfragen am besten?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Arbeite alle Fragen systematisch durch, nutze die Spaced-Repetition-Funktion für schwierige Fragen und teste dich regelmäßig mit der Prüfungssimulation. So gehst du optimal vorbereitet in die THW Theorieprüfung."
            }
        }
    ]
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Startseite",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "THW Prüfungsfragen",
            "item": "{{ url('/thw-pruefungsfragen') }}"
        }
    ]
}
</script>
@endsection

@push('scripts')
<script>
    // FAQ Toggle
    function toggleSpFAQ(button) {
        const content = button.nextElementSibling;
        const chevron = button.querySelector('.sp-faq-chevron');
        const isOpen = content.classList.contains('open');

        document.querySelectorAll('.sp-faq-content.open').forEach(el => {
            el.classList.remove('open');
            el.previousElementSibling.setAttribute('aria-expanded', 'false');
            el.previousElementSibling.querySelector('.sp-faq-chevron').classList.remove('open');
        });

        if (!isOpen) {
            content.classList.add('open');
            chevron.classList.add('open');
            button.setAttribute('aria-expanded', 'true');
        }
    }

    // Scroll Fade-In
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.sp-fade-in').forEach(el => observer.observe(el));
    });
</script>
@endpush