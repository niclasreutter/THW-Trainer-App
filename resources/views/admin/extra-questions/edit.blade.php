@extends('layouts.app')
@section('title', 'Zusatz-Frage bearbeiten - THW Trainer Admin')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .zf-type-display {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: rgba(0, 51, 127, 0.08);
        color: var(--thw-blue);
    }
    html:not(.light-mode) .zf-type-display {
        background: rgba(91, 154, 255, 0.12);
        color: #5b9aff;
    }

    .zf-danger-zone {
        padding: 1rem 1.25rem;
        margin-top: 1.25rem;
        border-radius: 0.75rem;
        background: rgba(239, 68, 68, 0.05);
        border: 1px solid rgba(239, 68, 68, 0.20);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .zf-danger-zone strong { color: #ef4444; }
    .zf-danger-zone__meta { font-size: 0.8rem; color: var(--text-muted); margin: 0.25rem 0 0; }
</style>
@endpush

@section('content')
@php
    $typLabels = [
        'matching'     => 'Zuordnung',
        'image_name'   => 'Bild benennen',
        'image_select' => 'Bild auswählen',
    ];
    $typIcons = [
        'matching'     => 'bi-diagram-3-fill',
        'image_name'   => 'bi-image-fill',
        'image_select' => 'bi-grid-3x3-gap-fill',
    ];

    $sections = [
        '1'  => '1 – Das THW im Gefüge',
        '2'  => '2 – Persönliche Ausrüstung',
        '3'  => '3 – UVV und Rettung',
        '4'  => '4 – Stromerzeuger & Beleuchtung',
        '5'  => '5 – Rettungs- und Bergungsarbeiten',
        '6'  => '6 – Knoten und Stiche',
        '7'  => '7 – Leitern',
        '8'  => '8 – Transportieren von Lasten',
        '9'  => '9 – Einfache Holzverbindungen',
        '10' => '10 – Erste Hilfe',
    ];
    $currentLern = (string) old('lernabschnitt', $question->lernabschnitt);
    $hasPredefinedLern = isset($sections[$currentLern]);
@endphp

<div class="dash-container" style="max-width: 52rem;">
    <div style="margin-bottom: 0.75rem;">
        <a href="{{ route('admin.extra-questions.index') }}" class="zf-back-btn">
            <i class="bi bi-arrow-left"></i> Zurück zu Zusatz-Fragen
        </a>
    </div>

    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">Admin · Zusatz-Fragen · #{{ $question->id }}</div>
        <h1 class="zf-page-title__h1">Zusatz-Frage bearbeiten</h1>
        <div class="zf-page-title__sub">Typ ist gesperrt, Inhalte können geändert werden</div>
    </div>

    @if(session('success'))
        <div class="zf-alert zf-alert--success">
            <i class="bi bi-check-circle-fill"></i>
            <div><strong>Erfolg!</strong>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="zf-alert zf-alert--error">
            <i class="bi bi-x-circle-fill"></i>
            <div>
                <strong>Bitte korrigiere folgende Fehler:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.extra-questions.update', $question) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Allgemein</span>
                <span class="zf-type-display">
                    <i class="bi {{ $typIcons[$question->typ] ?? 'bi-question' }}"></i>
                    {{ $typLabels[$question->typ] ?? $question->typ }}
                </span>
            </div>

            <div class="zf-field">
                <label class="zf-label" for="lernabschnitt">
                    Lernabschnitt <span class="zf-req">*</span>
                </label>
                @if($hasPredefinedLern)
                    <select id="lernabschnitt" name="lernabschnitt" class="zf-select" required>
                        @foreach($sections as $val => $label)
                            <option value="{{ $val }}" {{ $currentLern === (string) $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input id="lernabschnitt" type="text" name="lernabschnitt"
                           value="{{ $currentLern }}"
                           class="zf-input" required>
                @endif
                @error('lernabschnitt')<span class="zf-field-error">{{ $message }}</span>@enderror
            </div>

            <div class="zf-field">
                <label class="zf-label" for="frage">
                    Frage-Text <span class="zf-req">*</span>
                </label>
                <textarea id="frage" name="frage" class="zf-textarea" required>{{ old('frage', $question->frage) }}</textarea>
                @error('frage')<span class="zf-field-error">{{ $message }}</span>@enderror
            </div>
        </div>

        @if($question->typ === 'matching')
            @include('admin.extra-questions.partials.form-matching', ['question' => $question])
        @elseif($question->typ === 'image_name')
            @include('admin.extra-questions.partials.form-image-name', ['question' => $question])
        @elseif($question->typ === 'image_select')
            @include('admin.extra-questions.partials.form-image-select', ['question' => $question])
        @endif

        <div class="zf-form-actions">
            <a href="{{ route('admin.extra-questions.index') }}" class="btn-ghost">Abbrechen</a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2"></i> Änderungen speichern
            </button>
        </div>
    </form>

    <div class="zf-danger-zone">
        <div>
            <strong>Zusatz-Frage löschen</strong>
            <p class="zf-danger-zone__meta">Unwiderruflich. Zugehörige Bilder werden aus dem Storage entfernt.</p>
        </div>
        <form method="POST" action="{{ route('admin.extra-questions.destroy', $question) }}"
              onsubmit="return confirm('Zusatz-Frage #{{ $question->id }} wirklich löschen?')" style="margin: 0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">
                <i class="bi bi-trash"></i> Löschen
            </button>
        </form>
    </div>
</div>
@endsection
