@extends(isset($isLanding) && $isLanding ? 'layouts.landing' : 'layouts.app')

@section('title', 'Anonym üben - THW Theorie ohne Anmeldung')
@section('description', 'THW Theorie anonym üben ohne Anmeldung. Wähle aus verschiedenen Übungsmodi und starte sofort mit dem Lernen. Kostenlos und ohne Registrierung.')

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-top: 1rem;
    }

    .dashboard-greeting {
        font-size: 2.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dashboard-greeting span {
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
        margin-bottom: 0;
    }

    .main-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 700px) {
        .main-actions { grid-template-columns: 1fr; }
    }

    .action-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .action-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .action-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .action-card-icon.yellow { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
    .action-card-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

    .action-card-badge {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .action-card-description {
        font-size: 0.95rem;
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .action-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .action-card-btn.primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .action-card-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .action-card-btn.secondary {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .action-card-btn.secondary:hover {
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
    }

    .action-card-btn svg { width: 20px; height: 20px; }

    .alert-banner {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .alert-banner.warning { background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); }
    .alert-banner.info { background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.3); }

    .alert-banner-icon { font-size: 1.5rem; flex-shrink: 0; opacity: 0.9; }
    .alert-banner-content { flex: 1; }
    .alert-banner-title { font-size: 0.95rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem; }
    .alert-banner-description { font-size: 0.85rem; color: #4b5563; }
    .alert-banner-action { flex-shrink: 0; }

    .alert-banner-btn {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 0.625rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .alert-banner-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 51, 127, 0.3);
    }

    @media (max-width: 640px) {
        .dashboard-container { padding: 1rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .dashboard-subtitle { font-size: 0.95rem; }
        .action-card { padding: 1.5rem; }
        .action-card-title { font-size: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1 class="dashboard-greeting">Anonym <span>üben</span></h1>
            <p class="dashboard-subtitle">🎯 Starte sofort ohne Anmeldung - Alle Funktionen verfügbar!</p>
        </header>

        <!-- Anonym Hinweis -->
        <div class="alert-banner warning">
            <div class="alert-banner-icon">⚠️</div>
            <div class="alert-banner-content">
                <div class="alert-banner-title">Anonymer Modus</div>
                <div class="alert-banner-description">Deine Fortschritte werden nicht gespeichert. Erstelle einen kostenlosen Account für Fortschrittsverfolgung und mehr Features!</div>
            </div>
            <div class="alert-banner-action">
                <a href="{{ route('register') }}" class="alert-banner-btn">Kostenlos registrieren</a>
            </div>
        </div>

        <!-- Account Features Info -->
        <div class="alert-banner info" style="margin-bottom: 2.5rem;">
            <div class="alert-banner-icon">✨</div>
            <div class="alert-banner-content">
                <div class="alert-banner-title">Mit Account: Fortschritt speichern, Gamification & mehr</div>
                <div class="alert-banner-description">Lesezeichen, Achievements, Streaks, Prüfungsergebnisse und personalisierte Statistiken - Alles kostenlos!</div>
            </div>
        </div>

        <!-- Hauptaktionen -->
        <div class="main-actions">
            <a href="{{ route('landing.guest.practice.all') }}" class="action-card" style="text-decoration: none;">
                <div class="action-card-header">
                    <div class="action-card-icon yellow">📚</div>
                    <span class="action-card-badge">Grundausbildung</span>
                </div>
                <h3 class="action-card-title">Theorie Lernen</h3>
                <p class="action-card-description">Lerne alle THW-Theoriefragen der Grundausbildung. Nach Lernabschnitten sortiert für optimales Lernen.</p>
                <span class="action-card-btn secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Fragen üben
                </span>
            </a>

            <a href="{{ route('landing.guest.exam.index') }}" class="action-card" style="text-decoration: none;">
                <div class="action-card-header">
                    <div class="action-card-icon blue">🎓</div>
                    <span class="action-card-badge">Prüfung</span>
                </div>
                <h3 class="action-card-title">Prüfungssimulation</h3>
                <p class="action-card-description">Simuliere echte THW-Prüfungen mit 30 zufälligen Fragen. Teste dein Wissen unter realistischen Bedingungen!</p>
                <span class="action-card-btn primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Prüfung starten
                </span>
            </a>
        </div>

        <!-- Zurück zur Homepage -->
        <div style="text-align: center;">
            <a href="{{ route('landing.home') }}"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.8); color: #4b5563; font-weight: 600; border-radius: 0.75rem; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);"
               onmouseover="this.style.background='white'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.15)'"
               onmouseout="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 1px 3px rgba(0, 0, 0, 0.1)'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Zurück zur Startseite
            </a>
        </div>
    </div>
</div>
@endsection
