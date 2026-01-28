@extends('layouts.landing')

@section('title', 'Anonym üben - THW Theorie ohne Anmeldung')
@section('description', 'THW Theorie anonym üben ohne Anmeldung. Wähle aus verschiedenen Übungsmodi und starte sofort mit dem Lernen. Kostenlos und ohne Registrierung.')

@push('styles')
<style>
    .guest-menu-wrapper {
        min-height: calc(100vh - 200px);
        padding: 2rem 1rem;
    }

    .guest-menu-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .guest-menu-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .guest-menu-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .guest-menu-title span {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .guest-menu-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
    }

    .guest-alert {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .guest-alert-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .guest-alert-content {
        flex: 1;
        min-width: 200px;
    }

    .guest-alert-title {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .guest-alert-description {
        font-size: 0.9rem;
        color: #4b5563;
    }

    .guest-alert-btn {
        padding: 0.625rem 1.25rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .guest-alert-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 51, 127, 0.3);
    }

    .guest-info-alert {
        background: rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.3);
        margin-bottom: 2rem;
    }

    .guest-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 640px) {
        .guest-cards {
            grid-template-columns: 1fr;
        }
    }

    .guest-card {
        background: white;
        border-radius: 1.25rem;
        padding: 1.75rem;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .guest-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .guest-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .guest-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .guest-card-icon.yellow {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    }

    .guest-card-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .guest-card-badge {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        text-transform: uppercase;
    }

    .guest-card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .guest-card-description {
        font-size: 0.95rem;
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 1.25rem;
        flex-grow: 1;
    }

    .guest-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .guest-card-btn.primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
    }

    .guest-card-btn.secondary {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
    }

    .guest-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        color: #4b5563;
        font-weight: 600;
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .guest-back-link:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        color: #00337F;
    }
</style>
@endpush

@section('content')
<div class="guest-menu-wrapper">
    <div class="guest-menu-container">
        <header class="guest-menu-header">
            <h1 class="guest-menu-title">Anonym <span>üben</span></h1>
            <p class="guest-menu-subtitle">Starte sofort ohne Anmeldung - Alle Funktionen verfügbar!</p>
        </header>

        {{-- Anonym Hinweis --}}
        <div class="guest-alert">
            <div class="guest-alert-icon">
                <i class="bi bi-exclamation-triangle text-amber-500"></i>
            </div>
            <div class="guest-alert-content">
                <div class="guest-alert-title">Anonymer Modus</div>
                <div class="guest-alert-description">Deine Fortschritte werden nicht gespeichert. Erstelle einen kostenlosen Account für Fortschrittsverfolgung und mehr Features!</div>
            </div>
            @php
                $registerUrl = config('domains.development')
                    ? route('register')
                    : 'https://' . config('domains.app') . '/register';
            @endphp
            <a href="{{ $registerUrl }}" class="guest-alert-btn">Kostenlos registrieren</a>
        </div>

        {{-- Account Features Info --}}
        <div class="guest-alert guest-info-alert">
            <div class="guest-alert-icon">
                <i class="bi bi-stars text-blue-500"></i>
            </div>
            <div class="guest-alert-content">
                <div class="guest-alert-title">Mit Account: Fortschritt speichern, Gamification & mehr</div>
                <div class="guest-alert-description">Lesezeichen, Achievements, Streaks, Prüfungsergebnisse und personalisierte Statistiken - Alles kostenlos!</div>
            </div>
        </div>

        {{-- Hauptaktionen --}}
        <div class="guest-cards">
            <a href="{{ route('landing.guest.practice.all') }}" class="guest-card">
                <div class="guest-card-header">
                    <div class="guest-card-icon yellow">
                        <i class="bi bi-book text-white"></i>
                    </div>
                    <span class="guest-card-badge">Grundausbildung</span>
                </div>
                <h3 class="guest-card-title">Theorie Lernen</h3>
                <p class="guest-card-description">Lerne alle THW-Theoriefragen der Grundausbildung. Nach Lernabschnitten sortiert für optimales Lernen.</p>
                <span class="guest-card-btn secondary">
                    <i class="bi bi-book"></i>
                    Fragen üben
                </span>
            </a>

            <a href="{{ route('landing.guest.exam.index') }}" class="guest-card">
                <div class="guest-card-header">
                    <div class="guest-card-icon blue">
                        <i class="bi bi-mortarboard text-white"></i>
                    </div>
                    <span class="guest-card-badge">Prüfung</span>
                </div>
                <h3 class="guest-card-title">Prüfungssimulation</h3>
                <p class="guest-card-description">Simuliere echte THW-Prüfungen mit 30 zufälligen Fragen. Teste dein Wissen unter realistischen Bedingungen!</p>
                <span class="guest-card-btn primary">
                    <i class="bi bi-clipboard-check"></i>
                    Prüfung starten
                </span>
            </a>
        </div>

        {{-- Zurück zur Startseite --}}
        <div style="text-align: center;">
            <a href="{{ route('landing.home') }}" class="guest-back-link">
                <i class="bi bi-arrow-left"></i>
                Zurück zur Startseite
            </a>
        </div>
    </div>
</div>
@endsection
