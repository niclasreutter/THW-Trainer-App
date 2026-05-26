@extends('layouts.app')
@section('title', 'Zusatz-Frage vorschlagen - THW Trainer')

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
            'desc'  => 'Ein Bild beschreiben, User wählt aus 2–6 Text-Optionen',
        ],
        [
            'key'   => 'image_select',
            'icon'  => 'bi-grid-3x3-gap-fill',
            'title' => 'Bild auswählen',
            'desc'  => 'Textfrage mit 2–6 Bild-Optionen (Bilder werden nur beschrieben)',
        ],
    ];
@endphp

<div class="dash-container" style="max-width: 52rem;">
    <div style="margin-bottom: 0.75rem;">
        <a href="{{ route('zusatzfragen-vorschlagen.index') }}" class="zf-back-btn">
            <i class="bi bi-arrow-left"></i> Zurück zu meinen Einreichungen
        </a>
    </div>

    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">Zusatz-Fragen vorschlagen</div>
        <h1 class="zf-page-title__h1">Neue Zusatz-Frage einreichen</h1>
        <div class="zf-page-title__sub">Fragetyp wählen und Inhalte angeben – ein Admin prüft deine Einreichung anschließend.</div>
    </div>

    <div class="zf-alert zf-alert--info" style="margin-bottom: 1.25rem;">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Hinweis zum Urheberrecht:</strong>
            Bitte lade keine Bilder hoch. Beschreibe Bilder stattdessen so genau wie möglich – Admins fügen passende Bilder selbst hinzu, wenn deine Frage übernommen wird.
        </div>
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

    <div x-data="{ typ: @js(old('typ', $typ)) }" class="zf-form-stack">
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

        <form method="POST" action="{{ route('zusatzfragen-vorschlagen.store') }}" x-show="typ" x-cloak class="zf-form-stack">
            @csrf
            <input type="hidden" name="typ" :value="typ">

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
                    @endif
                    @error('lernabschnitt')<span class="zf-field-error">{{ $message }}</span>@enderror
                </div>

                <div class="zf-field">
                    <label class="zf-label" for="frage">
                        Frage-Titel <span class="zf-req">*</span>
                    </label>
                    <textarea id="frage" name="frage" class="zf-textarea" required maxlength="2000"
                              placeholder="z.B. Ordne die Knoten den Einsatzzwecken zu">{{ old('frage') }}</textarea>
                    <span class="zf-help">Wird als Frage-Stellung angezeigt.</span>
                    @error('frage')<span class="zf-field-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <template x-if="typ === 'matching'">
                <div class="zf-form-stack">
                    @include('user-extra-questions.partials.form-matching')
                </div>
            </template>

            <template x-if="typ === 'pair_matching'">
                <div class="zf-form-stack">
                    @include('user-extra-questions.partials.form-pairs')
                </div>
            </template>

            <template x-if="typ === 'image_name'">
                <div class="zf-form-stack">
                    @include('user-extra-questions.partials.form-image-name')
                </div>
            </template>

            <template x-if="typ === 'image_select'">
                <div class="zf-form-stack">
                    @include('user-extra-questions.partials.form-image-select')
                </div>
            </template>

            <div class="zf-form-actions">
                <a href="{{ route('zusatzfragen-vorschlagen.index') }}" class="btn-ghost">Abbrechen</a>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-send"></i> Frage einreichen
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
