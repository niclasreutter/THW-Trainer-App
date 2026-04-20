@extends('layouts.app')
@section('title', 'Zusatz-Frage bearbeiten - THW Trainer Admin')

@push('styles')
<style>
    .typ-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
        background: rgba(251,191,36,0.08);
        border: 1px solid rgba(251,191,36,0.25);
        color: var(--gold-start);
    }

    .field-label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; }
    .field-required { color: var(--error); }

    .field-input, .field-textarea {
        width: 100%;
        padding: 0.65rem 0.75rem;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.03);
        border-radius: 0.5rem;
        color: var(--text-primary);
        font: inherit;
        outline: none;
        transition: all 0.15s;
    }
    .field-input:focus, .field-textarea:focus {
        border-color: var(--gold-start);
        background: rgba(255,255,255,0.06);
    }
    .field-textarea { min-height: 90px; resize: vertical; }

    .field-error { display: block; margin-top: 0.35rem; color: var(--error); font-size: 0.8rem; }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .dyn-row {
        display: flex;
        gap: 0.6rem;
        align-items: flex-start;
        padding: 0.75rem;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.02);
        margin-bottom: 0.6rem;
    }
    .dyn-row .dyn-index {
        font-weight: 700;
        color: var(--text-muted);
        font-size: 0.85rem;
        min-width: 1.5rem;
        padding-top: 0.5rem;
    }
    .dyn-row .dyn-body { flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }

    .btn-icon-remove {
        padding: 0.4rem 0.6rem;
        border: 1px solid rgba(239,68,68,0.25);
        background: rgba(239,68,68,0.08);
        color: #fca5a5;
        border-radius: 0.4rem;
        cursor: pointer;
        font-size: 0.8rem;
    }
    .btn-icon-remove:hover { background: rgba(239,68,68,0.16); color: #fff; }
    .btn-icon-remove:disabled { opacity: 0.4; cursor: not-allowed; }

    .btn-add-row {
        padding: 0.55rem 0.9rem;
        border: 1px dashed rgba(255,255,255,0.2);
        background: transparent;
        color: var(--text-secondary);
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        width: 100%;
    }
    .btn-add-row:hover:not(:disabled) {
        border-color: var(--gold-start);
        color: var(--gold-start);
        background: rgba(251,191,36,0.04);
    }
    .btn-add-row:disabled { opacity: 0.4; cursor: not-allowed; }

    .checkbox-inline {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        cursor: pointer;
        user-select: none;
    }

    .image-preview {
        margin-top: 0.5rem;
        max-width: 280px;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .image-preview img { width: 100%; height: auto; display: block; }

    .file-field {
        padding: 0.55rem;
        border: 1px dashed rgba(255,255,255,0.15);
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.02);
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255,255,255,0.06);
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .hint {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
        line-height: 1.4;
    }
</style>
@endpush

@section('content')
@php
    $typLabels = [
        'matching' => 'Zuordnung',
        'image_name' => 'Bild benennen',
        'image_select' => 'Bild auswählen',
    ];
    $typIcons = [
        'matching' => 'bi-diagram-3',
        'image_name' => 'bi-image',
        'image_select' => 'bi-grid-3x3-gap',
    ];
@endphp
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Zusatz-Frage <span>bearbeiten</span></h1>
        <p class="page-subtitle">ID #{{ $question->id }} &middot; Typ ist gesperrt, Inhalte können geändert werden</p>
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div><strong>Erfolg!</strong><p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p></div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-x-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Bitte korrigiere folgende Fehler:</strong>
                <ul style="margin: 0.35rem 0 0 0; padding-left: 1.25rem;">
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

        <div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap;">
                <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                    <h2 class="section-title" style="font-size: 1.05rem;">Allgemein</h2>
                </div>
                <span class="typ-display">
                    <i class="bi {{ $typIcons[$question->typ] ?? 'bi-question' }}"></i>
                    {{ $typLabels[$question->typ] ?? $question->typ }}
                </span>
            </div>

            <div class="form-row">
                <div>
                    <label class="field-label" for="lernabschnitt">
                        Lernabschnitt <span class="field-required">*</span>
                    </label>
                    <input id="lernabschnitt" type="text" name="lernabschnitt"
                           value="{{ old('lernabschnitt', $question->lernabschnitt) }}"
                           class="field-input" required>
                    @error('lernabschnitt')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div>
                <label class="field-label" for="frage">
                    Frage-Text <span class="field-required">*</span>
                </label>
                <textarea id="frage" name="frage" class="field-textarea" required>{{ old('frage', $question->frage) }}</textarea>
                @error('frage')<span class="field-error">{{ $message }}</span>@enderror
            </div>
        </div>

        @if($question->typ === 'matching')
            @include('admin.extra-questions.partials.form-matching', ['question' => $question])
        @elseif($question->typ === 'image_name')
            @include('admin.extra-questions.partials.form-image-name', ['question' => $question])
        @elseif($question->typ === 'image_select')
            @include('admin.extra-questions.partials.form-image-select', ['question' => $question])
        @endif

        <div class="form-actions">
            <a href="{{ route('admin.extra-questions.index') }}" class="btn-ghost">Abbrechen</a>
            <button type="submit" class="btn-primary">Änderungen speichern</button>
        </div>
    </form>

    <div class="glass-error" style="padding: 1.25rem; margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <div>
            <strong>Zusatz-Frage löschen</strong>
            <p class="hint" style="margin: 0.25rem 0 0 0;">Unwiderruflich. Zugehörige Bilder werden aus dem Storage entfernt.</p>
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
