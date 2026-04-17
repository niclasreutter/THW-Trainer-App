@extends('layouts.app')
@section('title', 'Neue Zusatz-Frage - THW Trainer Admin')

@push('styles')
<style>
    .typ-picker {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .typ-card {
        padding: 1.25rem;
        border-radius: 0.75rem;
        border: 2px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.02);
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        color: var(--text-primary);
        font: inherit;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .typ-card:hover {
        border-color: var(--gold-start);
        background: rgba(255,255,255,0.04);
        transform: translateY(-2px);
    }
    .typ-card.active {
        border-color: var(--gold-start);
        background: linear-gradient(135deg, rgba(251,191,36,0.08), rgba(245,158,11,0.03));
        box-shadow: 0 0 0 3px rgba(251,191,36,0.15);
    }
    .typ-card .typ-icon {
        font-size: 1.5rem;
        color: var(--gold-start);
    }
    .typ-card h3 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }
    .typ-card p {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.4;
    }

    .field-label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }
    .field-required { color: var(--error); }

    .field-input,
    .field-textarea {
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
    .field-input:focus,
    .field-textarea:focus {
        border-color: var(--gold-start);
        background: rgba(255,255,255,0.06);
    }
    .field-textarea { min-height: 90px; resize: vertical; }

    .field-error {
        display: block;
        margin-top: 0.35rem;
        color: var(--error);
        font-size: 0.8rem;
    }

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
        transition: all 0.15s;
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
        transition: all 0.15s;
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
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Neue <span>Zusatz-Frage</span></h1>
        <p class="page-subtitle">Fragetyp wählen und Inhalte anlegen</p>
    </header>

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

    <div x-data="{ typ: @js(old('typ', $typ)) }">
        {{-- Typ-Auswahl --}}
        <div class="glass-tl" style="padding: 1.5rem; margin-bottom: 1.5rem;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1rem;">
                <h2 class="section-title" style="font-size: 1.05rem;">Fragetyp</h2>
            </div>
            <div class="typ-picker">
                <button type="button" class="typ-card" :class="{ active: typ === 'matching' }" @click="typ = 'matching'">
                    <span class="typ-icon"><i class="bi bi-diagram-3"></i></span>
                    <h3>Zuordnung</h3>
                    <p>Items zu Kategorien zuordnen (2–5 Kategorien, 3–10 Items).</p>
                </button>
                <button type="button" class="typ-card" :class="{ active: typ === 'image_name' }" @click="typ = 'image_name'">
                    <span class="typ-icon"><i class="bi bi-image"></i></span>
                    <h3>Bild benennen</h3>
                    <p>Ein Bild zeigen, Nutzer wählt passenden Namen aus 2–6 Text-Optionen.</p>
                </button>
                <button type="button" class="typ-card" :class="{ active: typ === 'image_select' }" @click="typ = 'image_select'">
                    <span class="typ-icon"><i class="bi bi-grid-3x3-gap"></i></span>
                    <h3>Bild auswählen</h3>
                    <p>Textfrage mit 2–6 Bild-Optionen. Ein oder mehrere Bilder können korrekt sein.</p>
                </button>
            </div>
        </div>

        {{-- Formular --}}
        <form method="POST" action="{{ route('admin.extra-questions.store') }}" enctype="multipart/form-data" x-show="typ" x-cloak>
            @csrf
            <input type="hidden" name="typ" :value="typ">

            <div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
                <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1rem;">
                    <h2 class="section-title" style="font-size: 1.05rem;">Allgemein</h2>
                </div>

                <div class="form-row">
                    <div>
                        <label class="field-label" for="lernabschnitt">
                            Lernabschnitt <span class="field-required">*</span>
                        </label>
                        <input id="lernabschnitt" type="text" name="lernabschnitt" value="{{ old('lernabschnitt') }}"
                               class="field-input" placeholder="z.B. 1" required>
                        @error('lernabschnitt')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div style="margin-bottom: 0.5rem;">
                    <label class="field-label" for="frage">
                        Frage-Text <span class="field-required">*</span>
                    </label>
                    <textarea id="frage" name="frage" class="field-textarea" required
                              placeholder="Fragentext eingeben...">{{ old('frage') }}</textarea>
                    @error('frage')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Dynamische Partials je Typ --}}
            <div x-show="typ === 'matching'" x-cloak>
                @include('admin.extra-questions.partials.form-matching', ['question' => null])
            </div>

            <div x-show="typ === 'image_name'" x-cloak>
                @include('admin.extra-questions.partials.form-image-name', ['question' => null])
            </div>

            <div x-show="typ === 'image_select'" x-cloak>
                @include('admin.extra-questions.partials.form-image-select', ['question' => null])
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.extra-questions.index') }}" class="btn-ghost">Abbrechen</a>
                <button type="submit" class="btn-primary">Zusatz-Frage speichern</button>
            </div>
        </form>

        <div x-show="!typ" x-cloak class="glass" style="padding: 2rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 2rem; margin-bottom: 0.75rem; opacity: 0.5;">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <p style="margin: 0;">Wähle oben einen Fragetyp, um das Formular zu laden.</p>
        </div>
    </div>
</div>
@endsection
