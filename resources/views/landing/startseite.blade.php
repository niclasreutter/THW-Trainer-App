@extends('layouts.startseite')

@section('title', 'THW Theorie kostenlos lernen 2026 | THW-Trainer.de')
@section('description', 'THW Theorie kostenlos lernen: Alle aktuellen Prüfungsfragen für die Grundausbildung 2026. Prüfungssimulation, Spaced Repetition, Ortsverband-Lernpools. Kostenlos & werbefrei.')
@section('canonical', url('/'))

@section('content')
@php
    $loginUrl = config('domains.development') ? route('login') : 'https://' . config('domains.app') . '/login';
    $registerUrl = config('domains.development') ? route('register') : 'https://' . config('domains.app') . '/register';
    $appUrl = config('domains.development') ? route('dashboard') : 'https://' . config('domains.app');
@endphp
<div x-data="{ mobileMenuOpen: false }">

    {{-- Account gelöscht Meldung --}}
    @if (session('status') == 'account-deleted')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
            <div class="glass glass-success p-4 rounded-lg">
                <p class="text-sm text-green-300 font-medium">
                    Dein Account wurde erfolgreich gelöscht. Alle deine Daten wurden permanent entfernt.
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================
         NAVBAR - Dark Glass
         ============================================ --}}
    <nav x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50"
         class="sp-navbar" :class="{ 'scrolled': scrolled }" aria-label="Hauptnavigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer Logo" class="h-8 w-auto">
                <span class="font-bold text-xl text-white">THW-Trainer</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200">Features</a>
                <a href="#how-it-works" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200">So geht's</a>
                <a href="#faq" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200">FAQ</a>
                <a href="{{ route('landing.statistics') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200">Statistiken</a>
            </div>

            {{-- Desktop Auth --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ $loginUrl }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors duration-200 px-4 py-2">Anmelden</a>
                <a href="{{ $registerUrl }}" class="sp-btn-gold text-sm" style="padding: 0.5rem 1.25rem;">Registrieren</a>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white p-2 cursor-pointer" aria-label="Menü">
                <i class="bi text-xl" :class="mobileMenuOpen ? 'bi-x-lg' : 'bi-list'"></i>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-white/5 bg-black/90 backdrop-blur-xl"
             style="display: none;">
            <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-3">
                <a href="#features" class="text-zinc-300 hover:text-white py-2" @click="mobileMenuOpen = false">Features</a>
                <a href="#how-it-works" class="text-zinc-300 hover:text-white py-2" @click="mobileMenuOpen = false">So geht's</a>
                <a href="#faq" class="text-zinc-300 hover:text-white py-2" @click="mobileMenuOpen = false">FAQ</a>
                <a href="{{ route('landing.statistics') }}" class="text-zinc-300 hover:text-white py-2" @click="mobileMenuOpen = false">Statistiken</a>
                <hr class="border-white/10 my-1">
                <a href="{{ $loginUrl }}" class="text-zinc-300 hover:text-white py-2">Anmelden</a>
                <a href="{{ $registerUrl }}" class="sp-btn-gold text-center text-sm" style="padding: 0.75rem 1.5rem;">Registrieren</a>
            </div>
        </div>
    </nav>

    {{-- ============================================
         HERO SECTION
         ============================================ --}}
    <section class="sp-hero" aria-label="Hauptbereich">
        {{-- Floating Gradient Orbs --}}
        <div class="sp-orb sp-orb-1" aria-hidden="true"></div>
        <div class="sp-orb sp-orb-2" aria-hidden="true"></div>
        <div class="sp-orb sp-orb-3" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center min-h-[90vh] py-24 lg:py-0">
                {{-- Text Side --}}
                <div>
                    <span class="sp-hero-badge">Grundausbildung Theorie 2026</span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                        Deine THW-Prüfung.
                        <br>
                        <span class="sp-gradient-text">Souverän bestanden.</span>
                    </h1>

                    <p class="text-lg lg:text-xl text-zinc-400 mb-10 max-w-xl leading-relaxed">
                        Alle offiziellen Prüfungsfragen der Grundausbildung. Intelligentes Lernen mit Spaced Repetition. Realistische Prüfungssimulation. Kostenlos und werbefrei.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="{{ $appUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos mit dem Lernen starten">
                            Kostenlos starten
                        </a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" aria-label="Anonym ohne Registrierung üben">
                            Anonym üben
                        </a>
                    </div>

                    {{-- Social Proof --}}
                    @if(isset($stats))
                    <p class="text-sm text-zinc-500">
                        <strong class="text-zinc-300">{{ number_format($stats['users'], 0, ',', '.') }}+</strong> Helfer lernen bereits mit dem THW-Trainer
                    </p>
                    @endif
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
                            <div style="height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; margin-bottom: 1.75rem; overflow: hidden;">
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
                                <div class="text-xs text-zinc-500 mb-2">Frage 23 / 456</div>
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
         STATS SECTION
         ============================================ --}}
    @if(isset($stats))
    <section class="py-16 lg:py-20 relative" style="background: #060608;" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="sr-only">Plattform-Statistiken</h2>
        {{-- Top border glow --}}
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(91, 154, 255, 0.25), transparent);" aria-hidden="true"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sp-stats">
                <div class="sp-stat-card sp-fade-in">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['users'] ?? 0) }}" data-suffix="+">0+</div>
                    <div class="sp-stat-label">Registrierte Nutzer</div>
                </div>
                <div class="sp-stat-card sp-fade-in">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['questions_answered'] ?? 0) }}" data-suffix="+">0+</div>
                    <div class="sp-stat-label">Fragen beantwortet</div>
                </div>
                <div class="sp-stat-card sp-fade-in">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['exams_passed'] ?? 0) }}" data-suffix="+">0+</div>
                    <div class="sp-stat-label">Prüfungen bestanden</div>
                </div>
                <div class="sp-stat-card sp-fade-in">
                    <div class="sp-stat-value" data-count="{{ (int) ($stats['pass_rate'] ?? 0) }}" data-suffix="%">0%</div>
                    <div class="sp-stat-label">Bestehensquote</div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================
         FEATURES — Bento Grid
         ============================================ --}}
    <section id="features" class="py-20 lg:py-28" style="background: #0a0a0b;" aria-labelledby="features-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="features-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Alles was du <span class="sp-gradient-text">brauchst</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    Von der ersten Frage bis zur bestandenen Prüfung — der THW-Trainer begleitet dich
                </p>
            </header>

            <div class="sp-bento">
                {{-- Main Feature: Alle Prüfungsfragen (2x2) --}}
                <article class="sp-bento-main glass glass-blue sp-fade-in" style="padding: 2.5rem; border-radius: 2rem 0.75rem 0.75rem 0.75rem;">
                    <span class="inline-block text-xs font-semibold uppercase tracking-wider mb-4 px-3 py-1 rounded-full" style="background: rgba(91, 154, 255, 0.1); color: #93c5fd;">Kernfeature</span>
                    <h3 class="text-2xl lg:text-3xl font-bold text-white mb-4">Alle Prüfungsfragen</h3>
                    <p class="text-zinc-400 leading-relaxed mb-6">
                        Die vollständige Sammlung aller offiziellen THW-Theoriefragen. Immer aktuell, regelmäßig geprüft und erweitert. Von den Grundlagen der Grundausbildung bis zu spezialisierten Lehrgängen.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-medium px-3 py-1.5 rounded-full text-zinc-300" style="background: rgba(255,255,255,0.06);">Grundausbildung</span>
                        <span class="text-xs font-medium px-3 py-1.5 rounded-full text-zinc-300" style="background: rgba(255,255,255,0.06);">Lehrgänge</span>
                        <span class="text-xs font-medium px-3 py-1.5 rounded-full text-zinc-300" style="background: rgba(255,255,255,0.06);">Aktuell 2026</span>
                    </div>
                </article>

                {{-- Prüfungssimulation --}}
                <article class="glass glass-tl sp-fade-in" style="padding: 1.75rem;">
                    <h3 class="text-lg font-bold text-white mb-3">Prüfungssimulation</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Realistische Prüfungsbedingungen mit Zeitlimit und Auswertung. Teste dein Wissen bevor es ernst wird.
                    </p>
                </article>

                {{-- Spaced Repetition --}}
                <article class="glass glass-br sp-fade-in" style="padding: 1.75rem;">
                    <h3 class="text-lg font-bold text-white mb-3">Spaced Repetition</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Intelligentes Wiederholungssystem. Schwache Fragen werden häufiger wiederholt, starke seltener.
                    </p>
                </article>

                {{-- Wide: Lehrgänge + Lernpools --}}
                <article class="sp-bento-wide glass glass-slash sp-fade-in" style="padding: 2rem;">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-3">Lehrgänge</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed">
                                Nicht nur Grundausbildung — bereite dich auf verschiedene THW-Lehrgänge vor mit eigenen Fragenkatalogen.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white mb-3">Ortsverband-Lernpools</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed">
                                Dein OV kann eigene Lernpools erstellen. Lerne gemeinsam mit deinen Kameraden.
                            </p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         HOW IT WORKS — 3 Steps
         ============================================ --}}
    <section id="how-it-works" class="py-20 lg:py-28" style="background: #060608;" aria-labelledby="steps-heading">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="steps-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    So <span class="sp-gradient-text">funktioniert's</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    In drei Schritten zur bestandenen Prüfung
                </p>
            </header>

            <div class="sp-steps sp-fade-in">
                {{-- Step 1 --}}
                <div class="text-center">
                    <div class="sp-step-number" style="background: #ffffff; color: #00337F;">1</div>
                    <h3 class="text-xl font-bold text-white mb-3">Registrieren</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed max-w-xs mx-auto">
                        Kostenlos anmelden — oder einfach anonym starten. Kein Abo, keine versteckten Kosten, keine Werbung.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center">
                    <div class="sp-step-number" style="background: rgba(91, 154, 255, 0.15); color: #93c5fd; border: 1px solid rgba(91, 154, 255, 0.3);">2</div>
                    <h3 class="text-xl font-bold text-white mb-3">Lernen</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed max-w-xs mx-auto">
                        Fragen beantworten, Fortschritt verfolgen. Spaced Repetition sorgt dafür, dass das Wissen sitzt.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center">
                    <div class="sp-step-number" style="background: rgba(0, 51, 127, 0.25); color: #5b9aff; border: 1px solid rgba(91, 154, 255, 0.3);">3</div>
                    <h3 class="text-xl font-bold text-white mb-3">Bestehen</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed max-w-xs mx-auto">
                        Mit der Prüfungssimulation testen — und dann die echte Prüfung souverän meistern.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         USPs — Why THW-Trainer
         ============================================ --}}
    <section class="py-20 lg:py-28" style="background: #0a0a0b;" aria-labelledby="usp-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="usp-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Warum <span class="sp-gradient-text">THW-Trainer</span>?
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    Entwickelt von THW-Helfern, für THW-Helfer
                </p>
            </header>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 sp-fade-in">
                <div class="glass" style="padding: 1.75rem; border-radius: 1.5rem 0.5rem 0.5rem 0.5rem;">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background: rgba(0, 51, 127, 0.15);">
                        <i class="bi bi-currency-euro text-lg" style="color: #5b9aff;"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">100% Kostenlos</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Kein Abo, keine In-App-Käufe, keine versteckten Kosten. Für immer kostenlos.
                    </p>
                </div>

                <div class="glass" style="padding: 1.75rem; border-radius: 0.5rem 1.5rem 0.5rem 0.5rem;">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background: rgba(0, 51, 127, 0.15);">
                        <i class="bi bi-ban text-lg" style="color: #5b9aff;"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Ohne Werbung</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Konzentriere dich aufs Lernen. Keine Banner, keine Pop-ups, keine Ablenkung.
                    </p>
                </div>

                <div class="glass" style="padding: 1.75rem; border-radius: 0.5rem 0.5rem 0.5rem 1.5rem;">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background: rgba(0, 51, 127, 0.15);">
                        <i class="bi bi-arrow-repeat text-lg" style="color: #5b9aff;"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Immer aktuell</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        Fragen werden regelmäßig geprüft und aktualisiert. Stand 2026.
                    </p>
                </div>

                <div class="glass" style="padding: 1.75rem; border-radius: 0.5rem 0.5rem 1.5rem 0.5rem;">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background: rgba(0, 51, 127, 0.15);">
                        <i class="bi bi-shield-check text-lg" style="color: #5b9aff;"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Datenschutz</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        DSGVO-konform. Server in Deutschland. Keine Weitergabe deiner Daten.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================
         TESTIMONIALS
         ============================================ --}}
    <section class="py-20 lg:py-28" style="background: #060608;" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-16 sp-fade-in">
                <h2 id="testimonials-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Das sagen <span class="sp-gradient-text">THW-Helfer</span>
                </h2>
                <p class="text-lg text-zinc-500 max-w-2xl mx-auto">
                    Erfahrungen von Helfern, die mit dem THW-Trainer gelernt haben
                </p>
            </header>

            <div class="grid md:grid-cols-3 gap-5 sp-fade-in">
                {{-- Testimonial 1 - Featured --}}
                <article class="glass glass-blue md:row-span-2" style="padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 0.5rem; display: flex; flex-direction: column; justify-content: center;">
                    <div class="text-4xl font-black mb-4 leading-none" style="color: rgba(91, 154, 255, 0.25);" aria-hidden="true">"</div>
                    <blockquote class="text-base text-zinc-300 leading-relaxed mb-6">
                        Der THW-Trainer hat mir richtig geholfen. Besonders die Prüfungssimulation war Gold wert — ich wusste genau, was mich erwartet. Durch Spaced Repetition haben sich auch die schwierigen Fragen eingeprägt. Prüfung beim ersten Versuch bestanden!
                    </blockquote>
                    <div>
                        <div class="font-semibold text-white">Stefan R.</div>
                        <div class="text-sm text-zinc-500">THW-Helfer, OV München</div>
                    </div>
                </article>

                {{-- Testimonial 2 --}}
                <article class="glass glass-tl" style="padding: 1.75rem;">
                    <div class="text-2xl font-black mb-3 leading-none" style="color: rgba(91, 154, 255, 0.2);" aria-hidden="true">"</div>
                    <blockquote class="text-sm text-zinc-300 leading-relaxed mb-4">
                        Endlich eine moderne Lernplattform für die THW-Theorie. Ich konnte überall lernen — im Zug, in der Pause, abends auf der Couch. Super praktisch!
                    </blockquote>
                    <div>
                        <div class="font-semibold text-white text-sm">Julia B.</div>
                        <div class="text-xs text-zinc-500">THW-Helferin</div>
                    </div>
                </article>

                {{-- Testimonial 3 --}}
                <article class="glass glass-br" style="padding: 1.75rem;">
                    <div class="text-2xl font-black mb-3 leading-none" style="color: rgba(91, 154, 255, 0.2);" aria-hidden="true">"</div>
                    <blockquote class="text-sm text-zinc-300 leading-relaxed mb-4">
                        Unser OV nutzt die Lernpools und es motiviert ungemein, gemeinsam zu lernen. Die Bestehensquote in unserer Gruppe war 100%.
                    </blockquote>
                    <div>
                        <div class="font-semibold text-white text-sm">Daniel F.</div>
                        <div class="text-xs text-zinc-500">THW-Helfer</div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         CTA SECTION
         ============================================ --}}
    <section class="sp-cta py-20 lg:py-28" aria-labelledby="cta-heading">
        <div class="max-w-3xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 id="cta-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-5">
                Bereit für deine Prüfung?
            </h2>
            <p class="text-lg text-blue-100 mb-4 max-w-2xl mx-auto leading-relaxed" style="opacity: 0.85;">
                Starte jetzt und bereite dich optimal auf deine
                <strong class="text-white">THW Grundausbildung Theorieprüfung</strong> vor.
            </p>
            <p class="text-sm text-blue-200 mb-10 max-w-2xl mx-auto" style="opacity: 0.65;">
                Ein Account — ein Lernstand. Egal ob Handy, Tablet oder Laptop.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $registerUrl }}" class="sp-btn-gold" aria-label="Jetzt kostenlos registrieren">
                    Jetzt kostenlos anmelden
                </a>
                <a href="{{ route('landing.guest.practice.menu') }}" class="sp-btn-ghost" style="border-color: rgba(255,255,255,0.25);" aria-label="Anonym ohne Registrierung üben">
                    Erst mal ausprobieren
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================
         FAQ SECTION
         ============================================ --}}
    <section id="faq" class="py-20 lg:py-28" style="background: #0a0a0b;" aria-labelledby="faq-heading">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="text-center mb-12 sp-fade-in">
                <h2 id="faq-heading" class="text-3xl lg:text-4xl font-extrabold text-white mb-4">
                    Häufig gestellte <span class="sp-gradient-text">Fragen</span>
                </h2>
            </header>

            <div class="space-y-3 sp-fade-in" itemscope itemtype="https://schema.org/FAQPage">
                {{-- FAQ 1 --}}
                <article class="sp-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span itemprop="name">Was kostet der THW-Trainer?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-sm text-zinc-400 leading-relaxed" itemprop="text">
                            Nichts. Der THW-Trainer ist komplett kostenlos, werbefrei und ohne versteckte Kosten. Er wird als private Initiative zur Unterstützung der THW-Ausbildung betrieben.
                        </p>
                    </div>
                </article>

                {{-- FAQ 2 --}}
                <article class="sp-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span itemprop="name">Welche Prüfungsfragen sind enthalten?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-sm text-zinc-400 leading-relaxed" itemprop="text">
                            Alle offiziellen Theoriefragen der THW Grundausbildung sowie Fragen verschiedener Lehrgänge. Der Fragenkatalog wird regelmäßig aktualisiert und entspricht dem Stand 2026.
                        </p>
                    </div>
                </article>

                {{-- FAQ 3 --}}
                <article class="sp-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span itemprop="name">Kann ich auch ohne Registrierung üben?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-sm text-zinc-400 leading-relaxed" itemprop="text">
                            Ja! Du kannst anonym und ohne Account Fragen üben und Prüfungssimulationen durchführen. Für Lernfortschritt, Spaced Repetition und Ortsverband-Features ist eine kostenlose Registrierung nötig.
                        </p>
                    </div>
                </article>

                {{-- FAQ 4 --}}
                <article class="sp-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span itemprop="name">Funktioniert die App auf dem Handy?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-sm text-zinc-400 leading-relaxed" itemprop="text">
                            Ja, der THW-Trainer ist als Progressive Web App (PWA) für alle Geräte optimiert. Du kannst ihn auf dem Handy wie eine echte App installieren und sogar offline nutzen.
                        </p>
                    </div>
                </article>

                {{-- FAQ 5 --}}
                <article class="sp-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="sp-faq-toggle" onclick="toggleSpFAQ(this)" aria-expanded="false">
                        <span itemprop="name">Ist das ein offizielles THW-Angebot?</span>
                        <svg class="sp-faq-chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="sp-faq-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p class="text-sm text-zinc-400 leading-relaxed" itemprop="text">
                            Nein, der THW-Trainer ist eine private Initiative zur Unterstützung der THW-Ausbildung. Er wird von einem THW-Helfer ehrenamtlich entwickelt und betrieben.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================
         FOOTER
         ============================================ --}}
    <footer class="sp-footer py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer" class="h-8 w-auto">
                        <span class="font-bold text-lg text-white">THW-Trainer</span>
                    </div>
                    <p class="text-sm text-zinc-500 max-w-xs leading-relaxed">
                        Kostenlose Prüfungsvorbereitung für die THW Grundausbildung. Lerne effektiv mit allen Theoriefragen.
                    </p>
                </div>

                {{-- Links --}}
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Links</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ url('/') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Startseite</a>
                        <a href="{{ route('landing.statistics') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Statistiken</a>
                        <a href="{{ route('landing.guest.practice.menu') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Anonym üben</a>
                    </div>
                </div>

                {{-- Rechtliches --}}
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Rechtliches</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('landing.impressum') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Impressum</a>
                        <a href="{{ route('landing.datenschutz') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Datenschutz</a>
                    </div>
                </div>

                {{-- Unterstützen --}}
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

            {{-- Copyright --}}
            <div class="pt-8" style="border-top: 1px solid rgba(255,255,255,0.06);">
                <p class="text-sm text-zinc-600">&copy; {{ date('Y') }} THW-Trainer. Entwickelt von Niclas Reutter.</p>
                <p class="text-xs text-zinc-700 mt-1">
                    Kein offizielles Angebot des THW. Private Initiative zur Prüfungsvorbereitung.
                </p>
            </div>
        </div>
    </footer>

</div>
@endsection

@push('scripts')
<script>
    // FAQ Toggle
    function toggleSpFAQ(button) {
        const content = button.nextElementSibling;
        const chevron = button.querySelector('.sp-faq-chevron');
        const isOpen = content.classList.contains('open');

        // Close all others
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

    // Scroll Fade-In Animation
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

    // Stat Count-Up Animation
    document.addEventListener('DOMContentLoaded', () => {
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.count) || 0;
                    const suffix = el.dataset.suffix || '';
                    const duration = 2000;
                    const start = performance.now();

                    function animate(now) {
                        const elapsed = now - start;
                        const progress = Math.min(elapsed / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        const current = Math.floor(eased * target);
                        el.textContent = current.toLocaleString('de-DE') + suffix;
                        if (progress < 1) requestAnimationFrame(animate);
                    }

                    requestAnimationFrame(animate);
                    statObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.sp-stat-value[data-count]').forEach(el => statObserver.observe(el));
    });
</script>
@endpush
