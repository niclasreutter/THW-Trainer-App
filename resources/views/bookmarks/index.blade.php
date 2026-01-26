@extends('layouts.app')
@section('title', 'Gespeicherte Fragen - THW Trainer')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .bookmarks-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .bookmarks-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .bookmarks-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-top: 1rem;
    }

    .bookmarks-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        line-height: 1.2;
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .bookmarks-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
    }

    .alert-banner {
        margin-bottom: 1.5rem;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        border-left: 4px solid;
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }

    .alert-success {
        border-left-color: #22c55e;
        background-color: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .alert-error {
        border-left-color: #ef4444;
        background-color: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }

    .practice-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .practice-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .practice-card-description {
        font-size: 0.95rem;
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .practice-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1.25rem 2rem;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        border: none;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .practice-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
    }

    .questions-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .questions-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .questions-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .question-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: all 0.3s ease;
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        background: white;
    }

    .question-info {
        flex: 1;
        padding-right: 1rem;
    }

    .question-section {
        font-size: 0.8rem;
        font-weight: 700;
        color: #00337F;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .question-text {
        font-size: 0.95rem;
        color: #1f2937;
        font-weight: 500;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .question-answer {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .question-answer-label {
        color: #4b5563;
        font-weight: 600;
    }

    .question-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .remove-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(239, 68, 68, 0.1);
        border: none;
        border-radius: 0.75rem;
        color: #ef4444;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
    }

    .remove-button:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(1.05);
    }

    .remove-button svg {
        width: 20px;
        height: 20px;
    }

    .empty-state {
        background: white;
        border: 2px dashed #e5e7eb;
        border-radius: 1.25rem;
        padding: 3rem;
        text-align: center;
    }

    .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-state-title { font-size: 1.5rem; font-weight: 700; color: #00337F; margin-bottom: 0.5rem; }
    .empty-state-description { font-size: 0.95rem; color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6; }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        color: #4b5563;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        background: #f9fafb;
        border-color: #00337F;
        color: #00337F;
    }

    .navigate-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .navigate-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .text-center { text-align: center; }
    .mt-8 { margin-top: 2rem; }

    @media (max-width: 640px) {
        .bookmarks-container { padding: 1rem; }
        .bookmarks-title { font-size: 1.75rem; }
        .question-card { flex-direction: column; }
        .question-info { padding-right: 0; margin-bottom: 1rem; }
        .question-actions { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="bookmarks-wrapper">
    <div class="bookmarks-container">
        <header class="bookmarks-header">
            <h1 class="bookmarks-title">Gespeicherte Fragen</h1>
            <p class="bookmarks-subtitle">Deine Favoriten zum gezielten Üben</p>
        </header>

        @if(session('success'))
            <div class="alert-banner alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert-banner alert-error">
                ✗ {{ session('error') }}
            </div>
        @endif
        
        @if($questions->count() > 0)
            <!-- Practice Button Card -->
            <div class="practice-card">
                <h2 class="practice-card-title">Alle gespeicherten Fragen üben</h2>
                <p class="practice-card-description">
                    Starte eine komplette Übungssession mit allen {{ $questions->count() }} gespeicherten Fragen.
                </p>
                <a href="{{ route('bookmarks.practice') }}" class="practice-button">
                    <i class="bi bi-book"></i> Jetzt üben ({{ $questions->count() }} Fragen)
                </a>
            </div>

            <!-- Questions List -->
            <div class="questions-section">
                <h2 class="questions-title">Deine Lesezeichen</h2>
                <div class="questions-grid">
                    @foreach($questions as $question)
                        <div class="question-card">
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
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-bookmark"></i></div>
                <h2 class="empty-state-title">Noch keine Fragen gespeichert</h2>
                <p class="empty-state-description">
                    Du kannst Fragen während des Übens speichern, um sie später gezielt nochmal zu üben.
                </p>
                <a href="{{ route('practice.menu') }}" class="navigate-button">
                    → Zum Übungsmenü
                </a>
            </div>
        @endif
        
        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="{{ route('dashboard') }}" class="back-link">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
