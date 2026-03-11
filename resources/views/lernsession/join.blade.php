@extends('layouts.app')
@section('title', 'Session beitreten - THW Trainer')

@push('styles')
<style>
    .join-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 60vh;
    }

    .join-card {
        padding: 2rem;
    }

    .join-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .join-subtitle {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }

    .boost-highlight {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.2);
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .boost-highlight-text {
        font-size: 0.9rem;
        color: var(--text-primary);
    }

    .boost-highlight-value {
        font-weight: 700;
        color: var(--gold);
    }

    .consent-box {
        padding: 1rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .consent-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .consent-text {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .consent-label {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .consent-label input {
        accent-color: var(--gold);
        margin-top: 0.15rem;
    }

    .join-actions {
        display: flex;
        gap: 0.75rem;
    }

    @media (max-width: 640px) {
        .join-container { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="join-container">
    <div class="glass-gold join-card">
        <div class="join-title">{{ $session->title }}</div>
        <div class="join-subtitle">
            {{ $instance->starts_at->format('d.m.Y H:i') }} - {{ $instance->ends_at->format('H:i') }} Uhr
            &middot; Noch {{ $instance->getTimeRemainingMinutes() }} Minuten
            &middot; {{ $instance->getParticipantCount() }} Teilnehmer
        </div>

        <div class="boost-highlight">
            <div class="boost-highlight-text">
                Während dieser Session erhältst du <span class="boost-highlight-value">+50% XP</span> auf alle beantworteten Fragen.
                Der Gewinner erhält eine Gold-Belohnungskiste!
            </div>
        </div>

        <form action="{{ route('lernsession.join', $instance) }}" method="POST">
            @csrf

            <div class="consent-box">
                <div class="consent-title">Datenschutz-Einwilligung</div>
                <div class="consent-text">
                    Wenn du am Ranking teilnimmst, werden dein Name und deine Punktzahl für andere Teilnehmer dieser Session sichtbar.
                    Diese Einwilligung gilt nur für diese Session-Instanz und wird nicht dauerhaft gespeichert.
                </div>
                <label class="consent-label">
                    <input type="checkbox" name="ranking_consent" value="1">
                    Ich stimme der Anzeige meiner Daten im Session-Ranking zu.
                    Ohne Zustimmung nehme ich anonym teil.
                </label>
            </div>

            <div class="join-actions">
                <button type="submit" class="btn-primary">Session beitreten</button>
                <a href="{{ route('lernsession.index') }}" class="btn-ghost">Abbrechen</a>
            </div>
        </form>
    </div>
</div>
@endsection
