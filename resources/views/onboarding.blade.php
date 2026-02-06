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
        padding: 1rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 0.75rem;
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

            <div class="onboarding-feature">
                <div class="onboarding-feature-icon" style="background: rgba(251, 191, 36, 0.15); color: var(--gold-start);">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">{{ $totalQuestions }} Theoriefragen</div>
                    <div class="onboarding-feature-desc">Alle offiziellen Fragen der THW-Grundausbildung. Eine Frage gilt als gemeistert, wenn du sie 2x in Folge richtig beantwortest.</div>
                </div>
            </div>

            <div class="onboarding-feature">
                <div class="onboarding-feature-icon" style="background: rgba(59, 130, 246, 0.15); color: var(--info);">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Prüfungssimulation</div>
                    <div class="onboarding-feature-desc">40 Fragen in 30 Minuten &ndash; genau wie die echte Prüfung.</div>
                </div>
            </div>

            <div class="onboarding-feature">
                <div class="onboarding-feature-icon" style="background: rgba(139, 92, 246, 0.15); color: #a855f7;">
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
            <div class="onboarding-icon" style="background: rgba(139, 92, 246, 0.15);">
                <i class="bi bi-arrow-repeat" style="color: #a855f7;"></i>
            </div>
            <h1 class="onboarding-title">Intelligentes Wiederholen</h1>
            <p class="onboarding-desc">
                Unser System merkt sich, welche Fragen dir schwer fallen und wiederholt sie in optimalen Abständen.
                So lernst du effizienter &ndash; wissenschaftlich bewährt als "Spaced Repetition".
            </p>

            <div class="onboarding-feature">
                <div class="onboarding-feature-icon" style="background: rgba(34, 197, 94, 0.15); color: var(--success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Richtig beantwortet?</div>
                    <div class="onboarding-feature-desc">Der Abstand bis zur nächsten Wiederholung wird größer: 1 Tag, 3 Tage, 8 Tage, ...</div>
                </div>
            </div>

            <div class="onboarding-feature">
                <div class="onboarding-feature-icon" style="background: rgba(239, 68, 68, 0.15); color: var(--error);">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Falsch beantwortet?</div>
                    <div class="onboarding-feature-desc">Die Frage kommt morgen wieder dran &ndash; so bleiben schwierige Themen präsent.</div>
                </div>
            </div>

            <div class="onboarding-feature">
                <div class="onboarding-feature-icon" style="background: rgba(251, 191, 36, 0.15); color: var(--gold-start);">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="onboarding-feature-title">Gamification</div>
                    <div class="onboarding-feature-desc">Sammle Punkte, steige Level auf und tritt gegen andere auf dem Leaderboard an.</div>
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

            <div class="onboarding-feature">
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
