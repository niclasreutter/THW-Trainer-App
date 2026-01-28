@extends('layouts.app')
@section('title', 'Gespeicherte Fragen - THW Trainer')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Bento Grid Layout */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .bento-wide {
        grid-column: span 3;
        padding: 1.5rem;
    }

    @media (max-width: 768px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
        .bento-wide {
            grid-column: span 1;
        }
        .dashboard-container {
            padding: 1rem;
        }
    }

    /* Practice Card */
    .practice-card-content {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .practice-card-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .practice-card-description {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* Questions List */
    .questions-section {
        margin-top: 2rem;
    }

    .questions-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .question-card {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .question-card:hover {
        transform: translateY(-2px);
    }

    .question-info {
        flex: 1;
        min-width: 0;
    }

    .question-section {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--gold-start);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .question-text {
        font-size: 0.9rem;
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    .question-answer {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .question-answer-label {
        color: var(--text-muted);
        font-weight: 600;
    }

    .question-actions {
        flex-shrink: 0;
    }

    .remove-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 0.5rem;
        color: #ef4444;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
    }

    .remove-button:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.4);
        transform: scale(1.05);
    }

    .remove-button svg {
        width: 18px;
        height: 18px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-left: 1rem;
        border-left: 3px solid var(--gold-start);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

    /* Navigation */
    .nav-footer {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    @media (max-width: 640px) {
        .question-card {
            flex-direction: column;
        }
        .question-actions {
            width: 100%;
        }
        .remove-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Gespeicherte <span>Fragen</span></h1>
        <p class="page-subtitle">Deine Favoriten zum gezielten Wiederholen</p>
    </header>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert-compact glass-success" style="margin-bottom: 1rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('success') }}</div>
        </div>
        <button class="text-dark-secondary hover:text-dark-primary" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-compact glass-error" style="margin-bottom: 1rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button class="text-dark-secondary hover:text-dark-primary" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    @if($questions->count() > 0)
        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-pill">
                <span class="stat-pill-icon text-gold"><i class="bi bi-bookmark-fill"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $questions->count() }}</div>
                    <div class="stat-pill-label">Gespeichert</div>
                </div>
            </div>
        </div>

        <!-- Practice Card -->
        <div class="bento-grid">
            <a href="{{ route('bookmarks.practice') }}" class="glass-gold bento-wide hover-lift" style="text-decoration: none;">
                <div class="practice-card-content">
                    <div>
                        <span class="badge-gold" style="margin-bottom: 0.75rem; display: inline-block;">Übungsmodus</span>
                        <h2 class="practice-card-title">Alle gespeicherten Fragen üben</h2>
                        <p class="practice-card-description">
                            Starte eine Übungssession mit allen {{ $questions->count() }} gespeicherten Fragen.
                        </p>
                    </div>
                    <span class="btn-primary" style="align-self: flex-start;">
                        Jetzt üben
                    </span>
                </div>
            </a>
        </div>

        <!-- Questions List -->
        <div class="questions-section">
            <div class="section-header">
                <h2 class="section-title">Deine Lesezeichen</h2>
            </div>

            <div class="questions-grid">
                @foreach($questions as $question)
                    <div class="glass question-card">
                        <div class="question-info">
                            <div class="question-section">
                                {{ $question->lernabschnitt }}
                            </div>
                            <div class="question-text">
                                {{ Str::limit($question->frage, 200) }}
                            </div>
                            <div class="question-answer">
                                <span class="question-answer-label">Antwort ({{ $question->loesung }}):</span>
                                @if($question->loesung === 'A')
                                    {{ $question->antwort_a }}
                                @elseif($question->loesung === 'B')
                                    {{ $question->antwort_b }}
                                @else
                                    {{ $question->antwort_c }}
                                @endif
                            </div>
                        </div>

                        <div class="question-actions">
                            <form action="{{ route('bookmarks.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                <button type="submit" class="remove-button" title="Aus Lesezeichen entfernen">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="glass-slash empty-state">
            <div class="empty-state-icon"><i class="bi bi-bookmark"></i></div>
            <h2 class="empty-state-title">Noch keine Fragen gespeichert</h2>
            <p class="empty-state-desc">
                Du kannst Fragen während des Übens speichern, um sie später gezielt nochmal zu üben.
            </p>
            <a href="{{ route('practice.menu') }}" class="btn-primary">
                Zum Übungsmenü
            </a>
        </div>
    @endif

    <!-- Navigation -->
    <div class="nav-footer">
        <a href="{{ route('dashboard') }}" class="btn-ghost">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>
@endsection
