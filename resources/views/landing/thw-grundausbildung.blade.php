@extends('layouts.startseite')

@section('title', 'THW Grundausbildung — Theorie online lernen & Prüfung bestehen 2026')
@section('description', 'THW Grundausbildung (GA): Alle ' . $totalQuestions . ' Theoriefragen in 10 Lernabschnitten online lernen. Prüfungssimulation, Lernfortschritt, Spaced Repetition. Kostenlos die GA-Prüfung bestehen!')
@section('canonical', url('/thw-grundausbildung'))

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
                <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW-Trainer Logo" class="h-8 w-auto sp-logo-dark">
                <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="THW-Trainer Logo" class="h-8 w-auto sp-logo-light">
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
    <section class="sp-hero" style="min-height: auto; padding: 6rem 0 3rem;" aria-label="THW Grundausbildung">
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
                        <span class="text-zinc-300">THW Grundausbildung</span>
                    </nav>

                    <span class="sp-hero-badge">Grundausbildung (GA) 2026</span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                        THW Grundausbildung
                        <br>
                        <span class="sp-gradient-text">Theorie meistern</span>
                    </h1>

                    <p class="text-lg lg:text-xl text-zinc-400 mb-10 max-w-xl leading-relaxed">
                        Lerne die komplette Theorie der THW Grundausbildung mit allen {{ $totalQuestions }} Prüfungsfragen in 10 Lernabschnitten. Verfolge deinen Fortschritt und bestehe die GA-Theorieprüfung beim ersten Versuch.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ $appUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos THW Grundausbildung lernen">
                            Kostenlos starten
                        </a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" aria-label="THW Grundausbildung anonym üben">
                            Anonym üben
                        </a>
                    </div>
                </div>

                {{-- App Mockup --}}
                <div class="hidden lg:block">
                    <div class="sp-mockup">
                        <div class="glass glass-blue" style="padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;">
                            {{-- Mock Learning Progress Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <div class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Grundausbildung</div>
                                    <div class="text-2xl font-bold text-white">Lernfortschritt</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-extrabold sp-gradient-text">67%</div>
                                    <div class="text-xs text-zinc-500">gelernt</div>
                                </div>
                            </div>

                            {{-- Mock Overall Progress Bar --}}
                            <div class="sp-progress-track" style="height: 6px; border-radius: 3px; margin-bottom: 1.75rem; overflow: hidden;">
                                <div style="width: 67%; height: 100%; background: linear-gradient(90deg, #00337F, #5b9aff); border-radius: 3px;"></div>
                            </div>

                            {{-- Mock Section Progress Bars --}}
                            <div class="space-y-2.5">
                                @php
                                    $mockSections = [
                                        ['label' => 'LA 1', 'progress' => 95],
                                        ['label' => 'LA 2', 'progress' => 88],
                                        ['label' => 'LA 3', 'progress' => 82],
                                        ['label' => 'LA 4', 'progress' => 75],
                                        ['label' => 'LA 5', 'progress' => 70],
                                        ['label' => 'LA 6', 'progress' => 62],
                                        ['label' => 'LA 7', 'progress' => 55],
                                        ['label' => 'LA 8', 'progress' => 40],
                                        ['label' => 'LA 9', 'progress' => 28],
                                        ['label' => 'LA 10', 'progress' => 12],
                                    ];
                                @endphp
                                @foreach($mockSections as $mock)
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-zinc-500 w-10 shrink-0">{{ $mock['label'] }}</span>
                                    <div class="flex-1 sp-progress-track" style="height: 4px; border-radius: 2px; overflow: hidden;">
                                        <div style="width: {{ $mock['progress'] }}%; height: 100%; background: linear-gradient(90deg, #00337F, #5b9aff); border-radius: 2px;"></div>
                                    </div>
                                    <span class="text-xs text-zinc-500 w-8 text-right">{{ $mock['progress'] }}%</span>
                                </div>
                                @endforeach
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
        <h2 id="stats-heading" class="sr-only">THW Grundausbildung in Zahlen</h2>
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
         LERNABSCHNITTE GRID
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
                    Was ist die <span class="sp-gradient-text">THW Grundausbildung</span>?
                </h2>

                <div class="space-y-8">
                    <div class="glass" style="padding: 2rem; border-radius: 1.5rem 0.5rem 0.5rem 0.5rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Die Grundausbildung im Technischen Hilfswerk</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die THW Grundausbildung (kurz: GA) ist die erste Ausbildungsstufe im Technischen Hilfswerk. Jeder neue Helfer durchläuft diese Ausbildung, die aus einem praktischen und einem theoretischen Teil besteht. Die Theorie umfasst {{ $totalQuestions }} Fragen aus 10 Lernabschnitten — von Arbeitssicherheit über Einsatzgrundlagen bis hin zu technischem Wissen. Mit dem THW-Trainer kannst du die komplette <a href="{{ url('/thw-theorie') }}" class="text-blue-400 hover:text-blue-300 transition-colors">THW Theorie</a> online lernen.
                        </p>
                    </div>

                    <div class="glass glass-slash" style="padding: 2rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Der Theorieteil der Grundausbildung</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Im theoretischen Teil der GA lernst du alle Grundlagen, die du als THW-Helfer brauchst. Der Fragenkatalog enthält {{ $totalQuestions }} Fragen zu allen 10 Lernabschnitten. Im THW-Trainer kannst du alle <a href="{{ url('/thw-pruefungsfragen') }}" class="text-blue-400 hover:text-blue-300 transition-colors">THW Prüfungsfragen</a> gezielt üben und deinen Fortschritt pro Abschnitt verfolgen.
                        </p>
                    </div>

                    <div class="glass glass-tl" style="padding: 2rem;">
                        <h3 class="text-lg font-bold text-white mb-3">Die GA-Theorieprüfung bestehen</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Am Ende der Grundausbildung steht die Theorieprüfung: 30 Fragen aus allen Lernabschnitten, 80% zum Bestehen. Mit dem THW-Trainer bereitest du dich optimal vor — übe alle Fragen, nutze Spaced Repetition für schwierige Themen und teste dich mit der <a href="{{ url('/thw-theoriepruefung') }}" class="text-blue-400 hover:text-blue-300 transition-colors">THW Theorieprüfung</a> Simulation.
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
                Bereit für die Grundausbildung?
            </h2>
            <p class="text-lg text-blue-100 mb-4 max-w-2xl mx-auto leading-relaxed" style="opacity: 0.85;">
                Starte jetzt und lerne die komplette <strong class="text-white">THW Grundausbildung Theorie</strong> mit allen <strong class="text-white">{{ $totalQuestions }} Prüfungsfragen</strong>.
            </p>
            <p class="text-sm text-blue-200 mb-10 max-w-2xl mx-auto" style="opacity: 0.65;">
                Kostenlos, werbefrei und auf allen Geräten verfügbar.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $registerUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>
                <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" style="border-color: rgba(255,255,255,0.25);" aria-label="THW Grundausbildung anonym üben">
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
                        <span>Was ist die THW Grundausbildung?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die Grundausbildung (GA) ist die erste Ausbildungsstufe im THW. Sie vermittelt alle grundlegenden Fähigkeiten und Kenntnisse, die ein THW-Helfer benötigt. Die Ausbildung besteht aus einem praktischen und einem theoretischen Teil mit {{ $totalQuestions }} Prüfungsfragen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 2 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie lange dauert die THW Grundausbildung?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die Dauer der Grundausbildung variiert je nach Ortsverband, typischerweise dauert sie mehrere Monate bei regelmäßigen Ausbildungsabenden. Die Theorie kann parallel mit dem THW-Trainer online gelernt werden.
                        </p>
                    </div>
                </article>

                {{-- FAQ 3 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Was sind die 10 Lernabschnitte der GA?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Die 10 Lernabschnitte umfassen: THW im Zivil- und Katastrophenschutz, Arbeitssicherheit, Arbeiten mit Leinen und Seilen, Leitern, Stromerzeugung, Metall-/Holz-/Steinbearbeitung, Bewegen von Lasten, Arbeiten am Wasser, Einsatzgrundlagen und Grundlagen der Rettung.
                        </p>
                    </div>
                </article>

                {{-- FAQ 4 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Wie bestehe ich die GA-Theorieprüfung?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Zum Bestehen der Theorieprüfung musst du mindestens 80% der 30 Prüfungsfragen korrekt beantworten. Arbeite alle Fragen im THW-Trainer durch, nutze Spaced Repetition für schwierige Fragen und teste dich mit der Prüfungssimulation.
                        </p>
                    </div>
                </article>

                {{-- FAQ 5 --}}
                <article class="sp-faq-item">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span>Kann ich die Grundausbildung Theorie online lernen?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content">
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Ja, mit dem THW-Trainer kannst du alle {{ $totalQuestions }} Theoriefragen der Grundausbildung kostenlos online lernen. Die App bietet gezieltes Lernen pro Lernabschnitt, Lernfortschritt-Tracking und eine realistische Prüfungssimulation.
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
                        <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW-Trainer" class="h-8 w-auto sp-logo-dark">
                        <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="THW-Trainer" class="h-8 w-auto sp-logo-light">
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
            "name": "Was ist die THW Grundausbildung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die Grundausbildung (GA) ist die erste Ausbildungsstufe im THW. Sie vermittelt alle grundlegenden Fähigkeiten und Kenntnisse, die ein THW-Helfer benötigt. Die Ausbildung besteht aus einem praktischen und einem theoretischen Teil mit {{ $totalQuestions }} Prüfungsfragen."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie lange dauert die THW Grundausbildung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die Dauer der Grundausbildung variiert je nach Ortsverband, typischerweise dauert sie mehrere Monate bei regelmäßigen Ausbildungsabenden. Die Theorie kann parallel mit dem THW-Trainer online gelernt werden."
            }
        },
        {
            "@@type": "Question",
            "name": "Was sind die 10 Lernabschnitte der GA?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Die 10 Lernabschnitte umfassen: THW im Zivil- und Katastrophenschutz, Arbeitssicherheit, Arbeiten mit Leinen und Seilen, Leitern, Stromerzeugung, Metall-/Holz-/Steinbearbeitung, Bewegen von Lasten, Arbeiten am Wasser, Einsatzgrundlagen und Grundlagen der Rettung."
            }
        },
        {
            "@@type": "Question",
            "name": "Wie bestehe ich die GA-Theorieprüfung?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Zum Bestehen der Theorieprüfung musst du mindestens 80% der 30 Prüfungsfragen korrekt beantworten. Arbeite alle Fragen im THW-Trainer durch, nutze Spaced Repetition für schwierige Fragen und teste dich mit der Prüfungssimulation."
            }
        },
        {
            "@@type": "Question",
            "name": "Kann ich die Grundausbildung Theorie online lernen?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ja, mit dem THW-Trainer kannst du alle {{ $totalQuestions }} Theoriefragen der Grundausbildung kostenlos online lernen. Die App bietet gezieltes Lernen pro Lernabschnitt, Lernfortschritt-Tracking und eine realistische Prüfungssimulation."
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
            "name": "THW Grundausbildung",
            "item": "{{ url('/thw-grundausbildung') }}"
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
