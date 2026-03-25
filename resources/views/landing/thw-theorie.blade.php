@extends('layouts.startseite')

@section('title', 'THW Theorie 2026 — Alle Prüfungsfragen der Grundausbildung online lernen')
@section('description', 'Lerne alle {{ $totalQuestions }} offiziellen THW Theorie Fragen kostenlos online. 10 Lernabschnitte, Prüfungssimulation, Spaced Repetition. Jetzt starten und Theorieprüfung bestehen!')
@section('canonical', url('/thw-theorie'))

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
    <section class="sp-hero" style="min-height: auto; padding: 6rem 0 3rem;" aria-label="THW Theorie">
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
                        <span class="text-zinc-300">THW Theorie</span>
                    </nav>

                    <span class="sp-hero-badge">Grundausbildung (GA) Theorie 2026</span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                        THW Theorie
                        <br>
                        <span class="sp-gradient-text">online lernen</span>
                    </h1>

                    <p class="text-lg lg:text-xl text-zinc-400 mb-10 max-w-xl leading-relaxed">
                        Alle {{ $totalQuestions }} offiziellen THW Theorie Fragen der Grundausbildung in 10 Lernabschnitten. Lerne gezielt, verfolge deinen Fortschritt und bestehe die THW Theorieprüfung.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ $appUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos THW Theorie lernen">
                            Kostenlos starten
                        </a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" aria-label="THW Theorie anonym üben">
                            Anonym üben
                        </a>
                    </div>
                </div>

                {{-- App Mockup --}}
                <div class="hidden lg:block">
                    <div class="sp-mockup">
                        <div class="glass glass-blue" style="padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;">
                            {{-- Mock Dashboard Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <div class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Dein Lernfortschritt</div>
                                    <div class="text-2xl font-bold text-white">Grundausbildung</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-extrabold sp-gradient-text">75%</div>
                                    <div class="text-xs text-zinc-500">abgeschlossen</div>
                                </div>
                            </div>

                            {{-- Mock Progress Bar --}}
                            <div class="sp-progress-track" style="height: 6px; border-radius: 3px; margin-bottom: 1.75rem; overflow: hidden;">
                                <div style="width: 75%; height: 100%; background: linear-gradient(90deg, #00337F, #5b9aff); border-radius: 3px;"></div>
                            </div>

                            {{-- Mock Stats Row --}}
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.75rem; text-align: center;">
                                    <div class="text-lg font-bold text-white">342</div>
                                    <div class="text-xs text-zinc-500">Richtig</div>
                                </div>
                                <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.75rem; text-align: center;">
                                    <div class="text-lg font-bold text-white">7</div>
                                    <div class="text-xs text-zinc-500">Tage-Streak</div>
                                </div>
                                <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.75rem; text-align: center;">
                                    <div class="text-lg font-bold text-white">89%</div>
                                    <div class="text-xs text-zinc-500">Quote</div>
                                </div>
                            </div>

                            {{-- Mock Question Preview --}}
                            <div class="glass" style="padding: 1rem 1.25rem; border-radius: 0.75rem;">
                                <div class="text-xs text-zinc-500 mb-2">Frage 23 / {{ $totalQuestions }}</div>
                                <div class="text-sm text-zinc-300 leading-relaxed">Welche Aufgabe hat das THW im Rahmen der zivilen Verteidigung?</div>
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
        <h2 id="stats-heading" class="sr-only">THW Theorie in Zahlen</h2>
        <div class="sp-glow-border" style="position: absolute; top: 0; left: 0; right: 0; height: 1px;" aria-hidden="true"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sp-stats">
                <div class="sp-stat-card">
                    <div class="sp-stat-value">{{ $totalQuestions }}</div>
                    <div class="sp-stat-label">Theoriefragen</div>
                </div>
                <div class="sp-stat-card">
                    <div class="sp-stat-value">10</div>
                    <div class="sp-stat-label">Lernabschnitte</div>
                </div>
                @if($stats)
                <div class="sp-stat-card">
                    <div class="sp-stat-value">{{ number_format($stats['users'], 0, ',', '.') }}+</div>
                    <div class="sp-stat-label">Nutzer lernen bereits</div>
                </div>
                <div class="sp-stat-card">
                    <div class="sp-stat-value">{{ $stats['pass_rate'] }}%</div>
                    <div class="sp-stat-label">Bestehensquote</div>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ============================================
         LERNABSCHNITTE - Bento Grid
         ============================================ --}}
    <section id="lernabschnitte" class="py-20 lg:py-28 sp-section" aria-labelledby="sections-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="sections-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Die 10 <span class="sp-gradient-text">Lernabschnitte</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    Die THW Grundausbildung Theorie gliedert sich in folgende Themengebiete
                </p>
            </header>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sp-fade-in">
                @foreach($sections as $section)
                <article class="glass {{ $loop->first ? 'glass-blue sm:col-span-2 lg:col-span-1' : ($loop->iteration === 2 ? 'glass-tl' : ($loop->last ? 'glass-br' : '')) }}" style="padding: 1.75rem; border-radius: {{ $loop->first ? '1.5rem 0.5rem 0.5rem 0.5rem' : ($loop->last ? '0.5rem 0.5rem 1.5rem 0.5rem' : '0.75rem') }};">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full sp-feature-badge">LA {{ $section['nr'] }}</span>
                        <span class="text-xs text-zinc-500">{{ $section['count'] }} Fragen</span>
                    </div>
                    <h3 class="text-base font-bold text-white leading-snug">{{ $section['name'] }}</h3>
                </article>
                @endforeach
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
                    Was ist die <span class="sp-gradient-text">THW Grundausbildung Theorie</span>?
                </h2>

                <div class="space-y-8">
                    <div class="glass" style="padding: 2rem; border-radius: 1.5rem 0.5rem 0.5rem 0.5rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Die Grundausbildung im THW</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die <strong class="text-zinc-300">THW Grundausbildung</strong> (kurz: GA) ist die erste Ausbildungsstufe im Technischen Hilfswerk.
                            Jeder neue THW-Helfer durchläuft diese Ausbildung, die aus einem praktischen und einem
                            <strong class="text-zinc-300">theoretischen Teil</strong> besteht. Die Theorieprüfung umfasst {{ $totalQuestions }} Fragen
                            aus 10 Lernabschnitten und muss bestanden werden, um die Grundausbildung abzuschließen.
                        </p>
                    </div>

                    <div class="glass glass-slash" style="padding: 2rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Wie läuft die THW Theorieprüfung ab?</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            In der <strong class="text-zinc-300">THW Theorieprüfung</strong> der Grundausbildung werden 30 Fragen aus allen
                            10 Lernabschnitten gestellt. Pro Frage gibt es drei Antwortmöglichkeiten, von denen eine oder mehrere
                            richtig sein können. Zum Bestehen müssen mindestens 80% der Fragen korrekt beantwortet werden.
                            Mit dem THW-Trainer kannst du alle Theorie Fragen vorab üben und die Prüfungssimulation nutzen.
                        </p>
                    </div>

                    <div class="glass glass-tl" style="padding: 2rem;">
                        <h3 class="text-lg font-bold text-white mb-3">THW Theorie online lernen</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Der <strong class="text-zinc-300">THW-Trainer</strong> bietet dir alle offiziellen <strong class="text-zinc-300">THW Theorie Fragen</strong> kostenlos
                            und online zum Lernen an. Nutze das intelligente Spaced-Repetition-System, um schwierige Fragen
                            gezielt zu wiederholen. Verfolge deinen Lernfortschritt pro Lernabschnitt und starte die
                            Prüfungssimulation, wenn du bereit bist. Das THW Training funktioniert auf Handy, Tablet und PC.
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
                Bereit für die THW Theorieprüfung?
            </h2>
            <p class="text-lg text-blue-100 mb-4 max-w-2xl mx-auto leading-relaxed" style="opacity: 0.85;">
                Starte jetzt mit der <strong class="text-white">THW Training App</strong> und lerne alle
                <strong class="text-white">{{ $totalQuestions }} Theoriefragen</strong> der Grundausbildung.
            </p>
            <p class="text-sm text-blue-200 mb-10 max-w-2xl mx-auto" style="opacity: 0.65;">
                Kostenlos, werbefrei und auf allen Geräten verfügbar.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $registerUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>
                <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" style="border-color: rgba(255,255,255,0.25);" aria-label="Anonym THW Theorie üben">
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
                        <span>Was ist die THW Grundausbildung Theorie?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die THW Grundausbildung (GA) Theorie umfasst {{ $totalQuestions }} Prüfungsfragen aus 10 Lernabschnitten. Sie deckt alle theoretischen Grundlagen ab, die ein THW-Helfer benötigt: von Arbeitssicherheit über Einsatzgrundlagen bis hin zu technischem Wissen. Die Theorieprüfung muss bestanden werden, um die Grundausbildung abzuschließen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 2 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie viele Theorie Fragen hat die THW Grundausbildung?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Der vollständige Fragenkatalog der THW Grundausbildung Theorie enthalt {{ $totalQuestions }} Fragen, aufgeteilt in 10 Lernabschnitte. In der Prüfung selbst werden davon 30 Fragen zufällig ausgewählt. Im THW-Trainer kannst du alle {{ $totalQuestions }} Fragen üben.
                        </p>
                    </div>
                </article>

                {{-- FAQ 3 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Kann ich THW Theorie Fragen kostenlos online üben?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Ja, auf thw-trainer.de kannst du alle THW Theorie Fragen komplett kostenlos und ohne Werbung online üben. Du kannst sofort anonym starten oder dich kostenlos registrieren, um deinen Lernfortschritt zu speichern. Die App funktioniert auf Handy, Tablet und PC.
                        </p>
                    </div>
                </article>

                {{-- FAQ 4 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie bestehe ich die THW Theorieprüfung?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Zum Bestehen der THW Theorieprüfung müssen mindestens 80% der 30 Prüfungsfragen korrekt beantwortet werden. Der beste Weg zur Vorbereitung: Alle Theorie Fragen im THW-Trainer durcharbeiten, schwierige Fragen mit Spaced Repetition wiederholen und die Prüfungssimulation nutzen, um dich unter realistischen Bedingungen zu testen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 5 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Was sind die Themen der THW GA Prüfung?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die THW GA Prüfung (Grundausbildung) deckt 10 Lernabschnitte ab: Das THW im Zivil- und Katastrophenschutz, Arbeitssicherheit, Arbeiten mit Leinen und Seilen, Leitern, Stromerzeugung, Metall-/Holz-/Steinbearbeitung, Bewegen von Lasten, Arbeiten am Wasser, Einsatzgrundlagen und Grundlagen der Rettung. Im THW-Trainer kannst du jeden Abschnitt gezielt üben.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         FOOTER - Compact
         ============================================ --}}
    <footer class="sp-footer py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer" class="h-6 w-auto sp-logo-dark">
                    <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer" class="h-6 w-auto sp-logo-light">
                    <span class="font-bold text-sm text-white">THW-Trainer</span>
                </div>
                <div class="flex items-center gap-6 text-sm text-zinc-500">
                    <a href="{{ url('/') }}" class="hover:text-zinc-300 transition-colors">Startseite</a>
                    <a href="{{ route('landing.statistics') }}" class="hover:text-zinc-300 transition-colors">Statistiken</a>
                    <a href="{{ route('landing.impressum') }}" class="hover:text-zinc-300 transition-colors">Impressum</a>
                    <a href="{{ route('landing.datenschutz') }}" class="hover:text-zinc-300 transition-colors">Datenschutz</a>
                </div>
                <p class="text-xs text-zinc-600">&copy; {{ date('Y') }} THW-Trainer</p>
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
            "name": "Was ist die THW Grundausbildung Theorie?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die THW Grundausbildung (GA) Theorie umfasst {{ $totalQuestions }} Prüfungsfragen aus 10 Lernabschnitten. Sie deckt alle theoretischen Grundlagen ab, die ein THW-Helfer benötigt: von Arbeitssicherheit über Einsatzgrundlagen bis hin zu technischem Wissen."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie viele Theorie Fragen hat die THW Grundausbildung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Der vollständige Fragenkatalog der THW Grundausbildung Theorie enthalt {{ $totalQuestions }} Fragen, aufgeteilt in 10 Lernabschnitte. In der Prüfung selbst werden davon 30 Fragen zufällig ausgewählt."
            }
        },
        {
            "@@type": "Question",
            "name": "Kann ich THW Theorie Fragen kostenlos online üben?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, auf thw-trainer.de kannst du alle THW Theorie Fragen komplett kostenlos und ohne Werbung online üben. Du kannst sofort anonym starten oder dich kostenlos registrieren."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie bestehe ich die THW Theorieprüfung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Zum Bestehen der THW Theorieprüfung müssen mindestens 80% der 30 Prüfungsfragen korrekt beantwortet werden. Der beste Weg: Alle Theorie Fragen durcharbeiten, schwierige Fragen mit Spaced Repetition wiederholen und die Prüfungssimulation nutzen."
            }
        },
        {
            "@@type": "Question",
            "name": "Was sind die Themen der THW GA Prüfung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die THW GA Prüfung deckt 10 Lernabschnitte ab: THW im Zivil- und Katastrophenschutz, Arbeitssicherheit, Arbeiten mit Leinen und Seilen, Leitern, Stromerzeugung, Metall-/Holz-/Steinbearbeitung, Bewegen von Lasten, Arbeiten am Wasser, Einsatzgrundlagen und Grundlagen der Rettung."
            }
        }
    ]
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Course",
    "name": "THW Grundausbildung Theorie",
    "description": "Alle {{ $totalQuestions }} offiziellen Prüfungsfragen der THW Grundausbildung (GA) Theorie in 10 Lernabschnitten. Kostenlos online lernen mit Prüfungssimulation.",
    "provider": {
        "@@type": "Organization",
        "name": "THW-Trainer",
        "url": "{{ url('/') }}"
    },
    "educationalLevel": "Grundausbildung",
    "inLanguage": "de",
    "isAccessibleForFree": true,
    "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR",
        "availability": "https://schema.org/InStock"
    },
    "hasCourseInstance": {
        "@@type": "CourseInstance",
        "courseMode": "online",
        "courseWorkload": "PT20H"
    }
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
            "name": "THW Theorie",
            "item": "{{ url('/thw-theorie') }}"
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
