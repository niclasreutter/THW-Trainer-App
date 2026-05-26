@extends('layouts.app')
@section('title', 'Vorschau · Zusatz-Frage – THW Trainer')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .tryout-banner {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border-radius: 0.625rem;
        background: rgba(251, 191, 36, 0.10);
        border: 1px solid rgba(251, 191, 36, 0.30);
        color: var(--text-primary);
        font-size: 0.875rem;
        line-height: 1.4;
    }
    .tryout-banner .bi { color: var(--gold-start); font-size: 1.1rem; flex-shrink: 0; }
    html:not(.light-mode) .tryout-banner { background: rgba(251, 191, 36, 0.08); }

    .tryout-card {
        padding: 1.25rem 1.5rem;
        border-radius: 0.875rem;
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.10);
    }
    html:not(.light-mode) .tryout-card { border-color: rgba(255, 255, 255, 0.10); }

    .tryout-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1.25rem;
    }
    .tryout-submit-btn {
        flex: 1 1 auto;
        max-width: 22rem;
        padding: 0.75rem 1.25rem;
        font-size: 0.95rem;
        font-weight: 700;
        border: 0;
        border-radius: 0.625rem;
        cursor: pointer;
        background: linear-gradient(135deg, var(--thw-blue), #2563eb);
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: opacity 0.15s ease, transform 0.15s ease;
    }
    .tryout-submit-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .tryout-submit-btn:not(:disabled):hover { transform: translateY(-1px); }
    .tryout-submit-btn:visited { color: white; }

    @media (max-width: 540px) {
        .tryout-actions { flex-direction: column-reverse; align-items: stretch; }
        .tryout-submit-btn { max-width: none; width: 100%; }
        .tryout-actions .btn-ghost { text-align: center; }
    }

    /* Reuse practice partial styles; result-banner / question-text / question-meta etc.
       are emitted by the partials and inherit from app.css. */
</style>
@endpush

@section('content')
@php
    $typLabels = [
        'matching'      => 'Zuordnung',
        'pair_matching' => 'Wortpaare',
        'image_name'    => 'Bild benennen',
        'image_select'  => 'Bild auswählen',
    ];
    $isSubmissionPreview = ($context ?? null) === 'submission';
@endphp

<div class="dash-container" style="max-width: 48rem;">
    <div style="margin-bottom: 0.75rem;">
        <a href="{{ $backUrl }}" class="zf-back-btn">
            <i class="bi bi-arrow-left"></i> {{ $backLabel ?? 'Zurück' }}
        </a>
    </div>

    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">
            @if($isSubmissionPreview)
                Vorschau · Einreichung #{{ $submission->id }}
            @else
                Vorschau · Zusatz-Frage #{{ $question->id }}
            @endif
            · {{ $typLabels[$question->typ] ?? $question->typ }}
        </div>
        <h1 class="zf-page-title__h1">Try-out</h1>
        <div class="zf-page-title__sub">Probiere die Frage in der echten Lernoberfläche aus.</div>
    </div>

    <div class="tryout-banner" role="status">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Vorschau-Modus.</strong>
            Antworten werden <strong>nicht gespeichert</strong> und beeinflussen keinen Lernfortschritt.
            @if($isSubmissionPreview)
                Diese Vorschau zeigt deine Einreichung so, wie sie nach der Freigabe in der Practice aussehen würde.
            @endif
        </div>
    </div>

    <div class="tryout-card">
        <form method="POST" action="{{ $submitUrl }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            @if($question->typ === 'matching')
                @include('practice.partials.question-matching', [
                    'question' => $question,
                    'isCorrect' => $isCorrect,
                    'answerResult' => $answerResult,
                ])
            @elseif($question->typ === 'pair_matching')
                @include('practice.partials.question-pair-matching', [
                    'question' => $question,
                    'isCorrect' => $isCorrect,
                    'answerResult' => $answerResult,
                    'context' => $context,
                ])
            @elseif($question->typ === 'image_name')
                @include('practice.partials.question-image-name', [
                    'question' => $question,
                    'isCorrect' => $isCorrect,
                    'answerResult' => $answerResult,
                    'userAnswer' => $userAnswer,
                ])
            @elseif($question->typ === 'image_select')
                @include('practice.partials.question-image-select', [
                    'question' => $question,
                    'isCorrect' => $isCorrect,
                    'answerResult' => $answerResult,
                    'userAnswer' => $userAnswer,
                ])
            @endif

            <div class="tryout-actions">
                @if($isCorrect === null)
                    <button type="submit" id="submitBtn" class="tryout-submit-btn">
                        Antwort prüfen
                    </button>
                    <a href="{{ $backUrl }}" class="btn-ghost">Abbrechen</a>
                @else
                    <a href="{{ $retryUrl ?? $submitUrl }}" class="tryout-submit-btn">
                        <i class="bi bi-arrow-repeat"></i> Nochmal versuchen
                    </a>
                    <a href="{{ $backUrl }}" class="btn-ghost">{{ $backLabel ?? 'Zurück' }}</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Image-only Lightbox-Fallback (falls Partials sie referenzieren) --}}
    @if(in_array($question->typ, ['image_name', 'image_select'], true))
        @include('practice.partials.image-lightbox')
    @endif
</div>
@endsection
