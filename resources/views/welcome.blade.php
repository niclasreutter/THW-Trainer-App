@extends('layouts.app')

@section('title', 'THW Trainer - Deine Lernplattform')
@section('description', 'Bereite dich optimal auf deine THW-Prüfung vor. Übe mit realistischen Fragen, verfolge deinen Fortschritt und werde zum THW-Experten.')

@push('styles')
<style>
    .welcome-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .welcome-header {
        margin-bottom: 2.5rem;
        padding-top: 1.5rem;
        max-width: 600px;
    }

    .welcome-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .welcome-card {
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    .welcome-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .welcome-card-text {
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin-bottom: 1.25rem;
        line-height: 1.6;
    }

    .welcome-feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1.5rem 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }

    .welcome-feature-list li {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .welcome-feature-list li i {
        color: var(--gold);
        font-size: 0.75rem;
    }

    .welcome-card-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: auto;
    }

    .welcome-card-actions a {
        text-align: center;
    }

    /* Features bento grid */
    .features-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .feature-card {
        padding: 1.5rem;
    }

    .feature-card-icon {
        font-size: 1.5rem;
        color: var(--gold);
        margin-bottom: 0.75rem;
    }

    .feature-card h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .feature-card p {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* PWA section */
    .pwa-card {
        padding: 2rem;
    }

    .pwa-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .pwa-header i {
        font-size: 1.75rem;
        color: var(--gold);
    }

    .pwa-header h3 {
        font-size: 1.15rem;
        font-weight: 700;
    }

    .pwa-text {
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .pwa-features {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }

    .pwa-feature {
        padding: 1rem;
        text-align: center;
    }

    .pwa-feature i {
        font-size: 1.25rem;
        color: var(--gold);
        display: block;
        margin-bottom: 0.5rem;
    }

    .pwa-feature span {
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Authenticated welcome */
    .welcome-back {
        padding: 2.5rem;
        text-align: center;
    }

    .welcome-back h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .welcome-back p {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    @media (max-width: 900px) {
        .features-grid {
            grid-template-columns: 1fr 1fr;
        }
        .features-grid .feature-card:last-child {
            grid-column: span 2;
        }
    }

    @media (max-width: 600px) {
        .welcome-container {
            padding: 1rem;
        }
        .welcome-grid {
            grid-template-columns: 1fr;
        }
        .features-grid {
            grid-template-columns: 1fr;
        }
        .features-grid .feature-card:last-child {
            grid-column: span 1;
        }
        .pwa-features {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="welcome-container">
        <header class="welcome-header">
            <h1 class="page-title">THW <span>Trainer</span></h1>
            <p class="page-subtitle">Bereite dich optimal auf deine THW-Prüfung vor. Übe mit realistischen Fragen und verfolge deinen Fortschritt.</p>
        </header>

        @guest
            <div class="welcome-grid">
                <div class="glass-blue glass-tl welcome-card">
                    <h2 class="welcome-card-title">Anmelden & Üben</h2>
                    <p class="welcome-card-text">Erstelle einen kostenlosen Account und nutze alle Funktionen:</p>
                    <ul class="welcome-feature-list">
                        <li><i class="bi bi-check-lg"></i> Persönlicher Lernfortschritt</li>
                        <li><i class="bi bi-check-lg"></i> Schwierige Fragen markieren</li>
                        <li><i class="bi bi-check-lg"></i> Statistiken und Erfolge</li>
                        <li><i class="bi bi-check-lg"></i> Gamification & Belohnungen</li>
                    </ul>
                    <div class="welcome-card-actions">
                        <a href="{{ route('login') }}" class="btn-secondary">Anmelden</a>
                        <a href="{{ route('register') }}" class="btn-primary">Registrieren</a>
                    </div>
                </div>

                <div class="glass-gold glass-br welcome-card">
                    <h2 class="welcome-card-title">Anonym üben</h2>
                    <p class="welcome-card-text">Sofort starten ohne Anmeldung:</p>
                    <ul class="welcome-feature-list">
                        <li><i class="bi bi-check-lg"></i> Alle Fragen verfügbar</li>
                        <li><i class="bi bi-check-lg"></i> Sofortiger Start</li>
                        <li><i class="bi bi-check-lg"></i> Keine Registrierung nötig</li>
                        <li><i class="bi bi-check-lg"></i> Perfekt zum Testen</li>
                    </ul>
                    <div class="welcome-card-actions">
                        <a href="{{ route('landing.guest.practice.menu') }}" class="btn-primary">Jetzt üben</a>
                    </div>
                </div>
            </div>
        @else
            <div class="glass-success welcome-back" style="margin-bottom: 2.5rem;">
                <h3>Willkommen zurück!</h3>
                <p>Du bist bereits angemeldet. Starte direkt mit dem Üben!</p>
                <a href="{{ route('dashboard') }}" class="btn-primary">Zum Dashboard</a>
            </div>
        @endguest

        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2 class="section-title">Warum THW Trainer?</h2>
        </div>

        <div class="features-grid">
            <div class="glass-tl feature-card">
                <div class="feature-card-icon"><i class="bi bi-journal-richtext"></i></div>
                <h3>Umfangreiche Fragen</h3>
                <p>Hunderte von realistischen THW-Fragen aus allen Bereichen</p>
            </div>
            <div class="glass-slash feature-card">
                <div class="feature-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <h3>Lernfortschritt</h3>
                <p>Verfolge deine Fortschritte und identifiziere Schwachstellen</p>
            </div>
            <div class="glass-br feature-card">
                <div class="feature-card-icon"><i class="bi bi-bullseye"></i></div>
                <h3>Personalisiert</h3>
                <p>Adaptive Lernmethoden für optimale Vorbereitung</p>
            </div>
        </div>

        <div class="glass-featured pwa-card">
            <div class="pwa-header">
                <i class="bi bi-phone"></i>
                <h3>Als App installierbar!</h3>
            </div>
            <p class="pwa-text">
                Installiere THW Trainer als App auf deinem Smartphone oder Tablet für noch schnelleren Zugriff – funktioniert auch offline!
            </p>
            <div class="pwa-features">
                <div class="glass-subtle pwa-feature">
                    <i class="bi bi-lightning-charge"></i>
                    <span>Schneller Start</span>
                </div>
                <div class="glass-subtle pwa-feature">
                    <i class="bi bi-wifi-off"></i>
                    <span>Offline nutzbar</span>
                </div>
                <div class="glass-subtle pwa-feature">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Auf Homescreen</span>
                </div>
            </div>
        </div>
    </div>
@endsection
