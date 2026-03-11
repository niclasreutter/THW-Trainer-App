@extends('layouts.app')
@section('title', 'Lernsession erstellen - THW Trainer')

@push('styles')
<style>
    .dashboard-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    .form-section {
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.4rem;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.6rem 0.8rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.5rem;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--gold);
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .radio-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.85rem;
        color: var(--text-secondary);
        transition: all 0.15s;
    }

    .radio-option:has(input:checked) {
        border-color: var(--gold);
        background: rgba(251, 191, 36, 0.08);
        color: var(--text-primary);
    }

    .radio-option input {
        accent-color: var(--gold);
    }

    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.5rem;
    }

    .checkbox-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 0.4rem;
        font-size: 0.82rem;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .checkbox-option:has(input:checked) {
        border-color: var(--gold);
        color: var(--text-primary);
    }

    .checkbox-option input {
        accent-color: var(--gold);
    }

    .day-selector {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .day-chip {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.15s;
    }

    .day-chip:has(input:checked) {
        background: rgba(251, 191, 36, 0.15);
        border-color: var(--gold);
        color: var(--gold);
    }

    .day-chip input {
        display: none;
    }

    .time-row {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .time-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .error-text {
        color: var(--error);
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 640px) {
        .dashboard-container { padding: 1rem; }
        .radio-group { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container" x-data="sessionForm()">
    <header class="dashboard-header">
        <h1 class="page-title">Session <span>erstellen</span></h1>
        <p class="page-subtitle">Erstelle eine neue Lernsession für dein Team oder alle Nutzer.</p>
    </header>

    @if($errors->any())
        <div class="glass-error" style="padding: 1rem; margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lernsession.store') }}" method="POST">
        @csrf

        {{-- Grunddaten --}}
        <div class="glass-tl form-section">
            <div class="form-section-title">Grunddaten</div>

            <div class="form-group">
                <label class="form-label">Titel</label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" placeholder="z.B. Dienstags-Training" required>
            </div>

            <div class="form-group">
                <label class="form-label">Beschreibung (optional)</label>
                <textarea name="description" class="form-textarea" placeholder="Kurze Beschreibung der Session...">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Geltungsbereich --}}
        <div class="glass form-section">
            <div class="form-section-title">Geltungsbereich</div>

            <div class="form-group">
                <div class="radio-group">
                    @if(auth()->user()->is_admin)
                        <label class="radio-option">
                            <input type="radio" name="scope" value="global" x-model="scope" {{ old('scope', $ortsverband ? 'ortsverband' : 'global') === 'global' ? 'checked' : '' }}>
                            Global (alle Nutzer)
                        </label>
                    @endif
                    <label class="radio-option">
                        <input type="radio" name="scope" value="ortsverband" x-model="scope" {{ old('scope', $ortsverband ? 'ortsverband' : '') === 'ortsverband' ? 'checked' : '' }}>
                        Ortsverband
                    </label>
                </div>
            </div>

            <div class="form-group" x-show="scope === 'ortsverband'" x-cloak>
                <label class="form-label">Ortsverband</label>
                @if($ortsverband)
                    <input type="hidden" name="ortsverband_id" value="{{ $ortsverband->id }}">
                    <div class="form-input" style="background: rgba(255,255,255,0.02);">{{ $ortsverband->name }}</div>
                @else
                    <select name="ortsverband_id" class="form-select">
                        <option value="">Bitte wählen...</option>
                        @foreach(auth()->user()->ortsverbände()->wherePivot('role', 'ausbildungsbeauftragter')->get() as $ov)
                            <option value="{{ $ov->id }}" {{ old('ortsverband_id') == $ov->id ? 'selected' : '' }}>{{ $ov->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        {{-- Fragenquelle --}}
        <div class="glass-br form-section">
            <div class="form-section-title">Fragenquelle</div>

            <div class="form-group">
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="question_source" value="all" x-model="questionSource" {{ old('question_source', 'all') === 'all' ? 'checked' : '' }}>
                        Alle Fragen
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="question_source" value="sections" x-model="questionSource" {{ old('question_source') === 'sections' ? 'checked' : '' }}>
                        Bestimmte Abschnitte
                    </label>
                    <label class="radio-option" x-show="scope === 'ortsverband'" x-cloak>
                        <input type="radio" name="question_source" value="lernpool" x-model="questionSource" {{ old('question_source') === 'lernpool' ? 'checked' : '' }}>
                        Lernpool
                    </label>
                </div>
            </div>

            {{-- Abschnitte --}}
            <div class="form-group" x-show="questionSource === 'sections'" x-cloak>
                <label class="form-label">Lernabschnitte auswählen</label>
                <div class="checkbox-grid">
                    @foreach($sections as $nr => $name)
                        <label class="checkbox-option">
                            <input type="checkbox" name="question_sections[]" value="{{ $nr }}"
                                {{ in_array($nr, old('question_sections', [])) ? 'checked' : '' }}>
                            {{ $nr }}. {{ $name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Lernpool --}}
            <div class="form-group" x-show="questionSource === 'lernpool'" x-cloak>
                <label class="form-label">Lernpool auswählen</label>
                <select name="lernpool_id" class="form-select">
                    <option value="">Bitte wählen...</option>
                    @foreach($lernpools as $pool)
                        <option value="{{ $pool->id }}" {{ old('lernpool_id') == $pool->id ? 'selected' : '' }}>
                            {{ $pool->name }} ({{ $pool->getQuestionCount() }} Fragen)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Zeitplanung --}}
        <div class="glass-slash form-section">
            <div class="form-section-title">Zeitplanung</div>

            <div class="form-group">
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="schedule_type" value="once" x-model="scheduleType" {{ old('schedule_type', 'once') === 'once' ? 'checked' : '' }}>
                        Einmalig
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="schedule_type" value="recurring" x-model="scheduleType" {{ old('schedule_type') === 'recurring' ? 'checked' : '' }}>
                        Wiederkehrend
                    </label>
                </div>
            </div>

            {{-- Einmalig --}}
            <div x-show="scheduleType === 'once'" x-cloak>
                <div class="time-row">
                    <div class="form-group">
                        <label class="form-label">Startzeitpunkt</label>
                        <input type="datetime-local" name="starts_at" class="form-input" value="{{ old('starts_at') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Endzeitpunkt</label>
                        <input type="datetime-local" name="ends_at" class="form-input" value="{{ old('ends_at') }}">
                    </div>
                </div>
            </div>

            {{-- Wiederkehrend --}}
            <div x-show="scheduleType === 'recurring'" x-cloak>
                <div class="form-group">
                    <label class="form-label">Wochentage</label>
                    <div class="day-selector">
                        @php $dayNames = [1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 7 => 'So']; @endphp
                        @foreach($dayNames as $dayNr => $dayName)
                            <label class="day-chip">
                                <input type="checkbox" name="recurrence_days[]" value="{{ $dayNr }}"
                                    {{ in_array($dayNr, old('recurrence_days', [])) ? 'checked' : '' }}>
                                {{ $dayName }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="time-row">
                    <div class="form-group">
                        <label class="form-label">Startzeit</label>
                        <input type="time" name="recurrence_start_time" class="form-input" value="{{ old('recurrence_start_time', '17:00') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Endzeit</label>
                        <input type="time" name="recurrence_end_time" class="form-input" value="{{ old('recurrence_end_time', '20:00') }}">
                    </div>
                </div>

                <div class="time-row" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Gültig ab (optional)</label>
                        <input type="date" name="recurrence_valid_from" class="form-input" value="{{ old('recurrence_valid_from') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gültig bis (optional)</label>
                        <input type="date" name="recurrence_valid_until" class="form-input" value="{{ old('recurrence_valid_until') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Session erstellen</button>
            <a href="{{ route('lernsession.index') }}" class="btn-ghost">Abbrechen</a>
        </div>
    </form>
</div>

<script>
function sessionForm() {
    return {
        scope: '{{ old('scope', $ortsverband ? 'ortsverband' : 'global') }}',
        questionSource: '{{ old('question_source', 'all') }}',
        scheduleType: '{{ old('schedule_type', 'once') }}',
    }
}
</script>
@endsection
