@extends('layouts.app')

@section('title', 'Willkommen')

@push('styles')
<style>
    .onboarding-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 2rem 1rem;
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .onboarding-card {
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .onboarding-steps {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }

    .onboarding-step-indicator {
        height: 4px;
        flex: 1;
        border-radius: 2px;
        background: rgba(255, 255, 255, 0.1);
        transition: background 0.5s ease;
    }

    .onboarding-step-indicator.active {
        background: var(--gradient-gold);
    }

    .onboarding-step-indicator.completed {
        background: var(--success);
    }

    .step-content {
        display: none;
        animation: fadeSlideIn 0.4s ease-out;
    }

    .step-content.active {
        display: block;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .onboarding-icon {
        width: 80px;
        height: 80px;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 2rem;
    }

    .onboarding-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .onboarding-desc {
        font-size: 1rem;
        color: var(--text-secondary);
        line-height: 1.7;
        margin-bottom: 2rem;
    }

    .onboarding-feature {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.10);
        backdrop-filter: blur(20px) saturate(1.8);
        -webkit-backdrop-filter: blur(20px) saturate(1.8);
        box-shadow:
            0 8px 32px rgba(0, 0, 0, 0.3),
            0 2px 8px rgba(0, 0, 0, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.15),
            inset 0 -1px 0 rgba(0, 0, 0, 0.08);
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .onboarding-feature::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
        pointer-events: none;
    }

    .onboarding-feature::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, transparent 60%);
        pointer-events: none;
        border-radius: inherit;
    }

    /* Gold-Variante */
    .onboarding-feature.feature-gold {
        border-color: rgba(251, 191, 36, 0.2);
        box-shadow:
            0 8px 32px rgba(251, 191, 36, 0.1),
            0 2px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(251, 191, 36, 0.2);
    }
    .onboarding-feature.feature-gold:hover {
        background: rgba(251, 191, 36, 0.08);
        border-color: rgba(251, 191, 36, 0.3);
        box-shadow:
            0 12px 40px rgba(251, 191, 36, 0.15),
            0 4px 12px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(251, 191, 36, 0.25);
        transform: translateY(-1px);
    }

    /* Blau-Variante */
    .onboarding-feature.feature-blue {
        border-color: rgba(59, 130, 246, 0.2);
        box-shadow:
            0 8px 32px rgba(59, 130, 246, 0.1),
            0 2px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(59, 130, 246, 0.2);
    }
    .onboarding-feature.feature-blue:hover {
        background: rgba(59, 130, 246, 0.08);
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow:
            0 12px 40px rgba(59, 130, 246, 0.15),
            0 4px 12px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(59, 130, 246, 0.25);
        transform: translateY(-1px);
    }

    /* THW-Blau-Variante */
    .onboarding-feature.feature-thw {
        border-color: rgba(0, 100, 220, 0.25);
        box-shadow:
            0 8px 32px rgba(0, 51, 127, 0.12),
            0 2px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(0, 120, 255, 0.2);
    }
    .onboarding-feature.feature-thw:hover {
        background: rgba(0, 51, 127, 0.1);
        border-color: rgba(0, 100, 220, 0.35);
        box-shadow:
            0 12px 40px rgba(0, 51, 127, 0.18),
            0 4px 12px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(0, 120, 255, 0.25);
        transform: translateY(-1px);
    }

    /* Grün/Erfolg-Variante */
    .onboarding-feature.feature-success {
        border-color: rgba(34, 197, 94, 0.2);
        box-shadow:
            0 8px 32px rgba(34, 197, 94, 0.1),
            0 2px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(34, 197, 94, 0.2);
    }
    .onboarding-feature.feature-success:hover {
        background: rgba(34, 197, 94, 0.08);
        border-color: rgba(34, 197, 94, 0.3);
        box-shadow:
            0 12px 40px rgba(34, 197, 94, 0.15),
            0 4px 12px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(34, 197, 94, 0.25);
        transform: translateY(-1px);
    }

    /* Rot/Fehler-Variante */
    .onboarding-feature.feature-error {
        border-color: rgba(239, 68, 68, 0.2);
        box-shadow:
            0 8px 32px rgba(239, 68, 68, 0.1),
            0 2px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(239, 68, 68, 0.2);
    }
    .onboarding-feature.feature-error:hover {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.3);
        box-shadow:
            0 12px 40px rgba(239, 68, 68, 0.15),
            0 4px 12px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(239, 68, 68, 0.25);
        transform: translateY(-1px);
    }

    .onboarding-feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .onboarding-feature-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .onboarding-feature-desc {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .onboarding-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .onboarding-actions .btn-primary,
    .onboarding-actions .btn-ghost {
        flex: 1;
        text-align: center;
        padding: 0.875rem;
    }

    .skip-link {
        text-align: center;
        margin-top: 1.5rem;
    }

    .skip-link a {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
    }

    .skip-link a:hover {
        color: var(--text-secondary);
    }

    .stat-highlight {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: block;
        margin-bottom: 0.25rem;
    }

    @media (max-width: 600px) {
        .onboarding-card { padding: 1.5rem; }
        .onboarding-title { font-size: 1.4rem; }
        .onboarding-actions { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="onboarding-container" x-data="{ step: 1, totalSteps: 3 }">
    <div class="onboarding-card glass-gold">
        <!-- Step Indicators -->
        <div class="onboarding-steps">
            <div class="onboarding-step-indicator" :class="{ 'active': step === 1, 'completed': step > 1 }"></div>
            <div class="onboarding-step-indicator" :class="{ 'active': step === 2, 'completed': step > 2 }"></div>
            <div class="onboarding-step-indicator" :class="{ 'active': step === 3 }"></div>
        </div>

        <!-- Step 1: Willkommen -->
        <div class="step-content" :class="{ 'active': step === 1 }">
            <div class="onboarding-icon" style="background: rgba(251, 191, 36, 0.15);">
                <i class="bi bi-shield-check" style="color: var(--gold-start);"></i>
            </div>
            <h1 class="onboarding-title">Willkommen, {{ $user->name }}!</h1>
            <p class="onboarding-desc">
                Hier lernst du die Theorie der THW-Grundausbildung.
                {{ $totalQuestions }} Fragen warten auf dich &ndash; aufgeteilt in 10 Lernabschnitte.
            </p>

            <div class="onboarding-feature feature-gold">
                <div class="onboarding-feature-icon" style="background: rgba(251, 191, 36, 0.15); color: var(--gold-start);">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">{{ $totalQuestions }} Theoriefragen</div>
                    <div class="onboarding-feature-desc">Alle offiziellen Fragen der THW-Grundausbildung, aufgeteilt in 10 Lernabschnitte.</div>
                </div>
            </div>

            <div class="onboarding-feature feature-blue">
                <div class="onboarding-feature-icon" style="background: rgba(59, 130, 246, 0.15); color: var(--info);">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Prüfungssimulation</div>
                    <div class="onboarding-feature-desc">40 Fragen in 30 Minuten &ndash; genau wie die echte Prüfung.</div>
                </div>
            </div>

            <div class="onboarding-feature feature-thw">
                <div class="onboarding-feature-icon" style="background: rgba(0, 51, 127, 0.15); color: var(--thw-blue-light);">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Ortsverbände &amp; Lehrgänge</div>
                    <div class="onboarding-feature-desc">Tritt deinem Ortsverband bei, nimm an Lehrgängen teil und bearbeite gemeinsame Lernpools mit deinen Kameraden.</div>
                </div>
            </div>

            <div class="onboarding-actions">
                <button class="btn-primary" @click="step = 2">Weiter</button>
            </div>
        </div>

        <!-- Step 2: Spaced Repetition -->
        <div class="step-content" :class="{ 'active': step === 2 }">
            <div class="onboarding-icon" style="background: rgba(0, 51, 127, 0.15);">
                <i class="bi bi-arrow-repeat" style="color: var(--thw-blue-light);"></i>
            </div>
            <h1 class="onboarding-title">Intelligentes Wiederholen</h1>
            <p class="onboarding-desc">
                Unser System merkt sich, welche Fragen dir schwer fallen und wiederholt sie in optimalen Abständen.
                So lernst du effizienter &ndash; wissenschaftlich bewährt als "Spaced Repetition".
            </p>

            <div class="onboarding-feature feature-success">
                <div class="onboarding-feature-icon" style="background: rgba(34, 197, 94, 0.15); color: var(--success);">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">3x richtig = Gemeistert</div>
                    <div class="onboarding-feature-desc">Eine Frage gilt erst als gemeistert, wenn du sie 3x in Folge richtig beantwortest &ndash; über mehrere Tage verteilt.</div>
                </div>
            </div>

            <div class="onboarding-feature feature-thw">
                <div class="onboarding-feature-icon" style="background: rgba(0, 51, 127, 0.15); color: var(--thw-blue-light);">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Wachsende Abstände</div>
                    <div class="onboarding-feature-desc">Bei jeder richtigen Antwort wird der Abstand größer: 1 Tag, 3 Tage, 8 Tage, 20 Tage, ... bis zu 90 Tage.</div>
                </div>
            </div>

            <div class="onboarding-feature feature-error">
                <div class="onboarding-feature-icon" style="background: rgba(239, 68, 68, 0.15); color: var(--error);">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Falsch beantwortet?</div>
                    <div class="onboarding-feature-desc">Der Fortschritt wird zurückgesetzt und die Frage kommt morgen wieder dran.</div>
                </div>
            </div>

            <div class="onboarding-actions">
                <button class="btn-ghost" @click="step = 1">Zurück</button>
                <button class="btn-primary" @click="step = 3">Weiter</button>
            </div>
        </div>

        <!-- Step 3: Los geht's -->
        <div class="step-content" :class="{ 'active': step === 3 }">
            <div class="onboarding-icon" style="background: rgba(34, 197, 94, 0.15);">
                <i class="bi bi-rocket-takeoff" style="color: var(--success);"></i>
            </div>
            <h1 class="onboarding-title">Bereit? Los geht's!</h1>
            <p class="onboarding-desc">
                Starte direkt mit dem Lernen. Du kannst jederzeit pausieren &ndash; dein Fortschritt wird gespeichert.
            </p>

            <div style="text-align: center; margin: 1.5rem 0;">
                <span class="stat-highlight">{{ $totalQuestions }}</span>
                <span style="font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Fragen warten auf dich</span>
            </div>

            <div class="onboarding-feature feature-gold">
                <div class="onboarding-feature-icon" style="background: rgba(251, 191, 36, 0.15); color: var(--gold-start);">
                    <i class="bi bi-lightbulb"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Tipp</div>
                    <div class="onboarding-feature-desc">Lerne täglich 20 Fragen für den besten Effekt. Das dauert nur ca. 10 Minuten!</div>
                </div>
            </div>

            <div class="onboarding-actions">
                <button class="btn-ghost" @click="step = 2">Zurück</button>
                <form action="{{ route('onboarding.complete') }}" method="POST" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-primary" style="width: 100%;">Lernen starten</button>
                </form>
            </div>
        </div>

        <!-- Skip Link -->
        <div class="skip-link">
            <form action="{{ route('onboarding.skip') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; font-size: 0.8rem; color: var(--text-muted); cursor: pointer; transition: color 0.2s;">
                    Überspringen
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
