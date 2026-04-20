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

    <div x-data="{ typ: @js(old('typ', $typ)) }">
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

        <form method="POST" action="{{ route('admin.extra-questions.store') }}" enctype="multipart/form-data" x-show="typ" x-cloak>
            @csrf
            <input type="hidden" name="typ" :value="typ">

            {{-- Allgemein --}}
            <div class="zf-form-card">
                <div class="zf-form-card__label">
                    <span class="zf-section-label">Allgemein</span>
                </div>

                <div class="zf-field">
                    <label class="zf-label" for="lernabschnitt">
                        Lernabschnitt <span class="zf-req">*</span>
                    </label>
                    <select id="lernabschnitt" name="lernabschnitt" class="zf-select" required>
                        @foreach($sections as $val => $label)
                            <option value="{{ $val }}" {{ old('lernabschnitt') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
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

            {{-- Typ-spezifische Partials --}}
            <div x-show="typ === 'matching'" x-cloak>
                @include('admin.extra-questions.partials.form-matching', ['question' => null])
            </div>

            <div x-show="typ === 'image_name'" x-cloak>
                @include('admin.extra-questions.partials.form-image-name', ['question' => null])
            </div>

            <div x-show="typ === 'image_select'" x-cloak>
                @include('admin.extra-questions.partials.form-image-select', ['question' => null])
            </div>

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
