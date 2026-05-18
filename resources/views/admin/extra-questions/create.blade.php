@extends('layouts.app')
@section('title', 'Neue Zusatz-Frage - THW Trainer Admin')

@push('styles')
@include('admin.extra-questions.partials._styles')
@endpush

@section('content')
@php
    $typeOptions = [
        [
            'key'   => 'matching',
            'icon'  => 'bi-diagram-3-fill',
            'title' => 'Zuordnung',
            'desc'  => 'Items zu Kategorien zuordnen (2–5 Kategorien, 3–10 Items)',
        ],
        [
            'key'   => 'pair_matching',
            'icon'  => 'bi-link-45deg',
            'title' => 'Wortpaare',
            'desc'  => '1-zu-1-Zuordnung von Begriffen (2–6 Paare)',
        ],
        [
            'key'   => 'image_name',
            'icon'  => 'bi-image-fill',
            'title' => 'Bild benennen',
            'desc'  => 'Ein Bild anzeigen, Nutzer wählt aus 2–6 Text-Optionen',
        ],
        [
            'key'   => 'image_select',
            'icon'  => 'bi-grid-3x3-gap-fill',
            'title' => 'Bild auswählen',
            'desc'  => 'Textfrage mit 2–6 Bild-Optionen (ein oder mehrere korrekt)',
        ],
    ];
@endphp

<div class="dash-container" style="max-width: 52rem;">
    <div style="margin-bottom: 0.75rem;">
        <a href="{{ route('admin.extra-questions.index') }}" class="zf-back-btn">
            <i class="bi bi-arrow-left"></i> Zurück zu Zusatz-Fragen
        </a>
    </div>

    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">Admin · Zusatz-Fragen</div>
        <h1 class="zf-page-title__h1">Neue Zusatz-Frage</h1>
        <div class="zf-page-title__sub">Fragetyp wählen und Inhalte anlegen</div>
    </div>

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

    @isset($submission)
        @if($submission)
        <div class="zf-alert zf-alert--info">
            <i class="bi bi-person-check-fill"></i>
            <div>
                <strong>Du übernimmst Einreichung #{{ $submission->id }}</strong> von
                {{ $submission->user->name ?? 'Unbekannt' }}. Felder sind vorbefüllt.
                Bitte ergänze die Bild-Uploads anhand der Beschreibungen unten.
                @php $payload = $submission->payload ?? []; @endphp
                @if($submission->typ === 'image_name' && !empty($payload['image_description']))
                    <div style="margin-top: 0.5rem;">
                        <strong>Bild-Beschreibung des Users:</strong>
                        <div style="white-space: pre-wrap; margin-top: 0.25rem;">{{ $payload['image_description'] }}</div>
                    </div>
                @endif
                @if($submission->typ === 'image_select' && !empty($payload['options']))
                    <div style="margin-top: 0.5rem;">
                        <strong>Bild-Beschreibungen der Optionen:</strong>
                        <ol style="margin: 0.25rem 0 0 1rem; padding: 0;">
                            @foreach($payload['options'] as $i => $opt)
                                <li style="margin-bottom: 0.35rem;">
                                    <strong>{{ chr(65 + $i) }})</strong>
                                    @if(!empty($opt['is_correct'])) <em>(richtig)</em> @endif
                                    {{ $opt['image_description'] ?? '' }}
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endisset

    <div x-data="{ typ: @js(old('typ', $typ)) }" class="zf-form-stack">
        {{-- Fragetyp-Auswahl --}}
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Fragetyp</span>
            </div>
            <div class="zf-type-grid">
                @foreach($typeOptions as $opt)
                    <button type="button"
                            class="zf-type-card"
                            :class="{ 'is-selected': typ === '{{ $opt['key'] }}' }"
                            @click="typ = '{{ $opt['key'] }}'">
                        <div class="zf-type-card__check"><i class="bi bi-check-lg"></i></div>
                        <div class="zf-type-card__icon"><i class="bi {{ $opt['icon'] }}"></i></div>
                        <div class="zf-type-card__title">{{ $opt['title'] }}</div>
                        <div class="zf-type-card__desc">{{ $opt['desc'] }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('admin.extra-questions.store') }}" enctype="multipart/form-data" x-show="typ" x-cloak class="zf-form-stack">
            @csrf
            <input type="hidden" name="typ" :value="typ">
            @if(old('_from_submission_id'))
                <input type="hidden" name="_from_submission_id" value="{{ old('_from_submission_id') }}">
            @endif

            {{-- Allgemein --}}
            <div class="zf-form-card">
                <div class="zf-form-card__label">
                    <span class="zf-section-label">Allgemein</span>
                </div>

                <div class="zf-field">
                    <label class="zf-label" for="lernabschnitt">
                        Lernabschnitt <span class="zf-req">*</span>
                    </label>
                    @if(!empty($sections))
                        <select id="lernabschnitt" name="lernabschnitt" class="zf-select" required>
                            @foreach($sections as $val => $label)
                                <option value="{{ $val }}" {{ old('lernabschnitt') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input id="lernabschnitt" type="text" name="lernabschnitt"
                               value="{{ old('lernabschnitt') }}"
                               class="zf-input" required
                               placeholder="z.B. 1">
                        <span class="zf-help">Noch keine Lernabschnitte in der Datenbank – bitte manuell eintragen.</span>
                    @endif
                    @error('lernabschnitt')<span class="zf-field-error">{{ $message }}</span>@enderror
                </div>

                <div class="zf-field">
                    <label class="zf-label" for="frage">
                        Frage-Titel <span class="zf-req">*</span>
                    </label>
                    <textarea id="frage" name="frage" class="zf-textarea" required
                              placeholder="z.B. Ordne die Knoten den Einsatzzwecken zu">{{ old('frage') }}</textarea>
                    <span class="zf-help">Wird als Frage-Stellung angezeigt.</span>
                    @error('frage')<span class="zf-field-error">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Typ-spezifische Partials: x-if statt x-show, damit inaktive `required`-Felder
                 die Formular-Validierung im Browser nicht blockieren. --}}
            <template x-if="typ === 'matching'">
                <div class="zf-form-stack">
                    @include('admin.extra-questions.partials.form-matching', ['question' => null])
                </div>
            </template>

            <template x-if="typ === 'pair_matching'">
                <div class="zf-form-stack">
                    @include('admin.extra-questions.partials.form-pairs', ['question' => null])
                </div>
            </template>

            <template x-if="typ === 'image_name'">
                <div class="zf-form-stack">
                    @include('admin.extra-questions.partials.form-image-name', ['question' => null])
                </div>
            </template>

            <template x-if="typ === 'image_select'">
                <div class="zf-form-stack">
                    @include('admin.extra-questions.partials.form-image-select', ['question' => null])
                </div>
            </template>

            <div class="zf-form-actions">
                <a href="{{ route('admin.extra-questions.index') }}" class="btn-ghost">Abbrechen</a>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check2"></i> Zusatz-Frage speichern
                </button>
            </div>
        </form>

        <div x-show="!typ" x-cloak class="zf-form-card" style="text-align: center; padding: 2rem; color: var(--text-muted);">
            <div style="font-size: 1.5rem; margin-bottom: 0.75rem; color: var(--thw-blue);">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <p style="margin: 0;">Wähle oben einen Fragetyp, um das Formular zu laden.</p>
        </div>
    </div>
</div>
@endsection
