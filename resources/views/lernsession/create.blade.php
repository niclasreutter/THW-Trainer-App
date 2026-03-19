@extends('layouts.app')
@section('title', 'Lernsession erstellen - THW Trainer')
@section('description', 'Erstelle eine neue THW Lernsession für dein Team oder alle Nutzer.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Form Inputs ────────────────────────────── */
    .lsc-input,
    .lsc-select,
    .lsc-textarea {
        width: 100%;
        padding: 0.625rem 0.875rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        color: var(--text-primary);
        font-size: 0.8125rem;
        font-family: inherit;
        outline: none;
        transition: border-color 150ms ease;
    }

    .lsc-input::placeholder,
    .lsc-textarea::placeholder { color: var(--text-muted); }

    .lsc-input:focus,
    .lsc-select:focus,
    .lsc-textarea:focus {
        border-color: rgba(91,154,255,0.4);
    }

    html.light-mode .lsc-input,
    html.light-mode .lsc-select,
    html.light-mode .lsc-textarea {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.1);
    }

    html.light-mode .lsc-input:focus,
    html.light-mode .lsc-select:focus,
    html.light-mode .lsc-textarea:focus {
        border-color: rgba(0,85,204,0.4);
    }

    .lsc-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .lsc-label {
        display: block;
        font-size: 0.6875rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lsc-group {
        margin-bottom: 1rem;
    }

    .lsc-group:last-child { margin-bottom: 0; }

    /* ─── Section Title ──────────────────────────── */
    .lsc-section-title {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.75rem;
    }

    /* ─── Radio/Checkbox Option Chips ────────────── */
    .lsc-radio-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .lsc-chip {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.4rem 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border);
        border-radius: 2rem;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        transition: all 150ms ease;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lsc-chip:hover {
        border-color: rgba(91,154,255,0.3);
        color: var(--text-secondary);
    }

    .lsc-chip:has(input:checked) {
        background: rgba(91,154,255,0.12);
        border-color: rgba(91,154,255,0.3);
        color: #5b9aff;
    }

    html.light-mode .lsc-chip:has(input:checked) {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.2);
    }

    .lsc-chip input[type="radio"],
    .lsc-chip input[type="checkbox"] {
        display: none;
    }

    /* ─── Checkbox Grid (Sections) ───────────────── */
    .lsc-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.375rem;
    }

    .lsc-check-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.625rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border);
        border-radius: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 150ms ease;
    }

    .lsc-check-item:hover {
        border-color: rgba(91,154,255,0.2);
    }

    .lsc-check-item:has(input:checked) {
        background: rgba(91,154,255,0.08);
        border-color: rgba(91,154,255,0.25);
        color: var(--text-primary);
    }

    html.light-mode .lsc-check-item {
        background: rgba(0,0,0,0.02);
        border-color: rgba(0,0,0,0.06);
    }

    html.light-mode .lsc-check-item:has(input:checked) {
        background: rgba(0,51,127,0.06);
        border-color: rgba(0,51,127,0.15);
    }

    .lsc-check-item input[type="checkbox"] {
        accent-color: #5b9aff;
        flex-shrink: 0;
    }

    .lsc-check-num {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.625rem;
        font-weight: 700;
        background: rgba(0,85,204,0.1);
        color: #5b9aff;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
    }

    html.light-mode .lsc-check-num { background: rgba(0,51,127,0.08); color: #00337F; }

    /* ─── Day Selector ───────────────────────────── */
    .lsc-day-row {
        display: flex;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    .lsc-day {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--glass-border);
        cursor: pointer;
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--text-muted);
        transition: all 150ms;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lsc-day:has(input:checked) {
        background: rgba(91,154,255,0.15);
        border-color: rgba(91,154,255,0.4);
        color: #5b9aff;
    }

    html.light-mode .lsc-day {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.08);
    }

    html.light-mode .lsc-day:has(input:checked) {
        background: rgba(0,51,127,0.1);
        border-color: rgba(0,51,127,0.25);
        color: #00337F;
    }

    .lsc-day input { display: none; }

    /* ─── Time Row ───────────────────────────────── */
    .lsc-time-row {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }

    .lsc-time-row .lsc-group {
        flex: 1;
        margin-bottom: 0;
    }

    /* ─── Error List ─────────────────────────────── */
    .lsc-errors {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.2);
        margin-bottom: 1rem;
    }

    .lsc-errors ul {
        margin: 0;
        padding-left: 1.125rem;
        font-size: 0.75rem;
        color: #ef4444;
        line-height: 1.6;
    }

    /* ─── Submit Area ────────────────────────────── */
    .lsc-submit-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, #0055cc, #5b9aff);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 700;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        border: none;
        cursor: pointer;
        transition: transform 200ms, box-shadow 200ms;
    }

    .lsc-submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0,85,204,0.3);
    }

    .lsc-cancel-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: transparent;
        border: 1px solid var(--glass-border);
        color: var(--text-muted);
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: border-color 150ms, color 150ms;
    }

    .lsc-cancel-btn:hover {
        border-color: rgba(91,154,255,0.3);
        color: var(--text-secondary);
        text-decoration: none;
    }

    /* ─── Stagger Animation ──────────────────────── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > form > .space-y-4 > *,
    .dash-container > form > .lsc-actions {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .dash-container > form > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > form > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > form > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > form > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > form > .lsc-actions { animation-delay: 0.19s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > form > .space-y-4 > *,
        .dash-container > form > .lsc-actions { animation: none; }
    }

    @media (max-width: 480px) {
        .lsc-radio-group { flex-direction: column; }
        .lsc-time-row { flex-direction: column; gap: 0.75rem; }
        .lsc-checkbox-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dash-container" style="max-width:720px;" x-data="sessionForm()">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <a href="{{ route('lernsession.index') }}" style="font-size:0.6875rem;color:var(--text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:0.25rem;margin-bottom:0.5rem;">
            <i class="bi bi-arrow-left"></i> Zurück zu Lernsessions
        </a>
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Neue Session</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Session erstellen</h1>
        <p class="text-sm" style="color:var(--text-muted);">Erstelle eine neue Lernsession für dein Team oder alle Nutzer</p>
    </div>

    {{-- ── Errors ── --}}
    @if($errors->any())
        <div class="lsc-errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lernsession.store') }}" method="POST">
        @csrf

        <div class="space-y-4">

            {{-- ── Grunddaten ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="lsc-section-title">Grunddaten</div>

                <div class="lsc-group">
                    <label class="lsc-label">Titel</label>
                    <input type="text" name="title" class="lsc-input" value="{{ old('title') }}" placeholder="z.B. Dienstags-Training" required>
                </div>

                <div class="lsc-group">
                    <label class="lsc-label">Beschreibung (optional)</label>
                    <textarea name="description" class="lsc-textarea" placeholder="Kurze Beschreibung der Session...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- ── Geltungsbereich ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="lsc-section-title">Geltungsbereich</div>

                <div class="lsc-group">
                    <div class="lsc-radio-group">
                        @if(auth()->user()->useroll === 'admin')
                            <label class="lsc-chip">
                                <input type="radio" name="scope" value="global" x-model="scope" {{ old('scope', $ortsverband ? 'ortsverband' : 'global') === 'global' ? 'checked' : '' }}>
                                <i class="bi bi-globe2"></i> Global (alle Nutzer)
                            </label>
                        @endif
                        <label class="lsc-chip">
                            <input type="radio" name="scope" value="ortsverband" x-model="scope" {{ old('scope', $ortsverband ? 'ortsverband' : '') === 'ortsverband' ? 'checked' : '' }}>
                            <i class="bi bi-people-fill"></i> Ortsverband
                        </label>
                    </div>
                </div>

                <div class="lsc-group" x-show="scope === 'ortsverband'" x-cloak>
                    <label class="lsc-label">Ortsverband</label>
                    @if($ortsverband)
                        <input type="hidden" name="ortsverband_id" value="{{ $ortsverband->id }}">
                        <div class="lsc-input" style="background:rgba(255,255,255,0.02);cursor:default;">{{ $ortsverband->name }}</div>
                    @else
                        <select name="ortsverband_id" class="lsc-select">
                            <option value="">Bitte wählen...</option>
                            @if(auth()->user()->useroll === 'admin')
                                @foreach(\App\Models\Ortsverband::orderBy('name')->get() as $ov)
                                    <option value="{{ $ov->id }}" {{ old('ortsverband_id') == $ov->id ? 'selected' : '' }}>{{ $ov->name }}</option>
                                @endforeach
                            @else
                                @foreach(auth()->user()->ortsverbände()->wherePivot('role', 'ausbildungsbeauftragter')->get() as $ov)
                                    <option value="{{ $ov->id }}" {{ old('ortsverband_id') == $ov->id ? 'selected' : '' }}>{{ $ov->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    @endif
                </div>
            </div>

            {{-- ── Fragenquelle ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="lsc-section-title">Fragenquelle</div>

                <div class="lsc-group">
                    <div class="lsc-radio-group">
                        <label class="lsc-chip">
                            <input type="radio" name="question_source" value="all" x-model="questionSource" {{ old('question_source', 'all') === 'all' ? 'checked' : '' }}>
                            <i class="bi bi-collection"></i> Alle Fragen
                        </label>
                        <label class="lsc-chip">
                            <input type="radio" name="question_source" value="sections" x-model="questionSource" {{ old('question_source') === 'sections' ? 'checked' : '' }}>
                            <i class="bi bi-list-ol"></i> Bestimmte Abschnitte
                        </label>
                        <label class="lsc-chip" x-show="scope === 'ortsverband'" x-cloak>
                            <input type="radio" name="question_source" value="lernpool" x-model="questionSource" {{ old('question_source') === 'lernpool' ? 'checked' : '' }}>
                            <i class="bi bi-archive"></i> Lernpool
                        </label>
                    </div>
                </div>

                {{-- Abschnitte --}}
                <div class="lsc-group" x-show="questionSource === 'sections'" x-cloak>
                    <label class="lsc-label">Lernabschnitte auswählen</label>
                    <div class="lsc-checkbox-grid">
                        @foreach($sections as $nr => $name)
                            <label class="lsc-check-item">
                                <input type="checkbox" name="question_sections[]" value="{{ $nr }}"
                                    {{ in_array($nr, old('question_sections', [])) ? 'checked' : '' }}>
                                <span class="lsc-check-num">{{ $nr }}</span>
                                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Lernpool --}}
                <div class="lsc-group" x-show="questionSource === 'lernpool'" x-cloak>
                    <label class="lsc-label">Lernpool auswählen</label>
                    <select name="lernpool_id" class="lsc-select">
                        <option value="">Bitte wählen...</option>
                        @foreach($lernpools as $pool)
                            <option value="{{ $pool->id }}" {{ old('lernpool_id') == $pool->id ? 'selected' : '' }}>
                                {{ $pool->name }} ({{ $pool->getQuestionCount() }} Fragen)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ── Zeitplanung ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div class="lsc-section-title">Zeitplanung</div>

                <div class="lsc-group">
                    <div class="lsc-radio-group">
                        <label class="lsc-chip">
                            <input type="radio" name="schedule_type" value="once" x-model="scheduleType" {{ old('schedule_type', 'once') === 'once' ? 'checked' : '' }}>
                            <i class="bi bi-calendar-event"></i> Einmalig
                        </label>
                        <label class="lsc-chip">
                            <input type="radio" name="schedule_type" value="recurring" x-model="scheduleType" {{ old('schedule_type') === 'recurring' ? 'checked' : '' }}>
                            <i class="bi bi-arrow-repeat"></i> Wiederkehrend
                        </label>
                    </div>
                </div>

                {{-- Einmalig --}}
                <div x-show="scheduleType === 'once'" x-cloak>
                    <div class="lsc-time-row">
                        <div class="lsc-group">
                            <label class="lsc-label">Startzeitpunkt</label>
                            <input type="datetime-local" name="starts_at" class="lsc-input" value="{{ old('starts_at') }}">
                        </div>
                        <div class="lsc-group">
                            <label class="lsc-label">Endzeitpunkt</label>
                            <input type="datetime-local" name="ends_at" class="lsc-input" value="{{ old('ends_at') }}">
                        </div>
                    </div>
                </div>

                {{-- Wiederkehrend --}}
                <div x-show="scheduleType === 'recurring'" x-cloak>
                    <div class="lsc-group">
                        <label class="lsc-label">Wochentage</label>
                        <div class="lsc-day-row">
                            @php $dayNames = [1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 7 => 'So']; @endphp
                            @foreach($dayNames as $dayNr => $dayName)
                                <label class="lsc-day">
                                    <input type="checkbox" name="recurrence_days[]" value="{{ $dayNr }}"
                                        {{ in_array($dayNr, old('recurrence_days', [])) ? 'checked' : '' }}>
                                    {{ $dayName }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lsc-time-row" style="margin-top:0.75rem;">
                        <div class="lsc-group">
                            <label class="lsc-label">Startzeit</label>
                            <input type="time" name="recurrence_start_time" class="lsc-input" value="{{ old('recurrence_start_time', '17:00') }}">
                        </div>
                        <div class="lsc-group">
                            <label class="lsc-label">Endzeit</label>
                            <input type="time" name="recurrence_end_time" class="lsc-input" value="{{ old('recurrence_end_time', '20:00') }}">
                        </div>
                    </div>

                    <div class="lsc-time-row" style="margin-top:0.75rem;">
                        <div class="lsc-group">
                            <label class="lsc-label">Gültig ab (optional)</label>
                            <input type="date" name="recurrence_valid_from" class="lsc-input" value="{{ old('recurrence_valid_from') }}">
                        </div>
                        <div class="lsc-group">
                            <label class="lsc-label">Gültig bis (optional)</label>
                            <input type="date" name="recurrence_valid_until" class="lsc-input" value="{{ old('recurrence_valid_until') }}">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Actions ── --}}
        <div class="lsc-actions" style="display:flex;gap:0.75rem;margin-top:1.25rem;">
            <button type="submit" class="lsc-submit-btn">
                <i class="bi bi-plus-lg"></i> Session erstellen
            </button>
            <a href="{{ route('lernsession.index') }}" class="lsc-cancel-btn">Abbrechen</a>
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
