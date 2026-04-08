@extends(isset($isLanding) && $isLanding ? 'layouts.landing' : 'layouts.app')

@section('title', 'Anonym üben ohne Anmeldung | THW-Trainer.de')
@section('description', 'THW Theorie anonym üben ohne Anmeldung. Wähle aus verschiedenen Übungsmodi und starte sofort mit dem Lernen. Kostenlos und ohne Registrierung.')
@section('robots', 'noindex, follow')
@section('canonical', url('/guest/practice-menu'))

@section('content')
<div class="dash-container" style="padding: 2rem 1rem;">
<div class="space-y-4">

    <header class="dashboard-header">
        <h1 class="page-title">Anonym <span>üben</span></h1>
        <p class="page-subtitle">Starte sofort ohne Anmeldung &ndash; alle Funktionen verfügbar</p>
    </header>

    {{-- Anonym Hinweis --}}
    <div class="glass-warning" style="padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <i class="bi bi-shield-exclamation" style="font-size: 1.5rem; color: #f59e0b; flex-shrink: 0;"></i>
        <div style="flex: 1; min-width: 200px;">
            <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Anonymer Modus</div>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">Deine Fortschritte werden nicht gespeichert. Erstelle einen kostenlosen Account für Fortschrittsverfolgung und mehr Features.</div>
        </div>
        <a href="{{ route('register') }}" class="btn-primary" style="flex-shrink: 0;">Kostenlos registrieren</a>
    </div>

    {{-- Account Features Info --}}
    <div class="glass-accent" style="padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <i class="bi bi-stars" style="font-size: 1.5rem; color: #fbbf24; flex-shrink: 0;"></i>
        <div>
            <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Mit Account: Fortschritt speichern, Gamification &amp; mehr</div>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">Lesezeichen, Achievements, Streaks, Prüfungsergebnisse und personalisierte Statistiken &ndash; Alles kostenlos.</div>
        </div>
    </div>

    {{-- Hauptaktionen --}}
    <div class="bento-grid">
        <a href="{{ route('landing.guest.practice.all') }}" class="glass-gold bento-half" style="text-decoration: none; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; position: relative; z-index: 1;">
                <div style="width: 48px; height: 48px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff;">
                    <i class="bi bi-book"></i>
                </div>
                <span style="background: rgba(251,191,36,0.12); color: #f59e0b; font-size: 0.65rem; font-weight: 700; padding: 0.3rem 0.7rem; border-radius: 2rem; text-transform: uppercase; letter-spacing: 0.5px;">Grundausbildung</span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; position: relative; z-index: 1;">Theorie Lernen</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1; position: relative; z-index: 1;">Lerne alle THW-Theoriefragen der Grundausbildung. Nach Lernabschnitten sortiert für optimales Lernen.</p>
            <span class="btn-secondary" style="align-self: flex-start; position: relative; z-index: 1;">Fragen üben</span>
        </a>

        <a href="{{ route('landing.guest.exam.index') }}" class="glass-blue bento-half" style="text-decoration: none; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; position: relative; z-index: 1;">
                <div style="width: 48px; height: 48px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff;">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <span style="background: rgba(59,130,246,0.12); color: #5b9aff; font-size: 0.65rem; font-weight: 700; padding: 0.3rem 0.7rem; border-radius: 2rem; text-transform: uppercase; letter-spacing: 0.5px;">Prüfung</span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; position: relative; z-index: 1;">Prüfungssimulation</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1; position: relative; z-index: 1;">Simuliere echte THW-Prüfungen mit 30 zufälligen Fragen. Teste dein Wissen unter realistischen Bedingungen.</p>
            <span class="btn-primary" style="align-self: flex-start; position: relative; z-index: 1;">Prüfung starten</span>
        </a>
    </div>

    {{-- Zurück zur Homepage --}}
    <div style="text-align: center;">
        <a href="{{ route('landing.home') }}" class="btn-ghost">
            <i class="bi bi-arrow-left"></i>
            Zurück zur Startseite
        </a>
    </div>

</div>
</div>
@endsection
