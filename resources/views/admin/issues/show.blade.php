@extends('layouts.app')
@section('title', 'Fehlermeldung - Admin')

@push('styles')
<style>
/* =========================================================
   ADMIN ISSUE DETAIL — Design 2026-05
   Aligned with /admin und /admin/extra-questions Look
   ========================================================= */
.iss-root { width: 100%; max-width: 1280px; margin: 0 auto; }

/* Header */
.iss-root .iss-header { margin-bottom: 1.25rem; }
.iss-root .iss-eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--text-muted); margin-bottom: 0.35rem;
}
.iss-root .iss-h1 {
    font-family: 'Figtree', sans-serif;
    font-weight: 800; font-size: 2rem; line-height: 1.15;
    letter-spacing: -0.015em;
    color: #5b9aff; margin: 0 0 0.25rem;
}
html.light-mode .iss-root .iss-h1 { color: var(--thw-blue); }
.iss-root .iss-sub {
    font-size: 0.9375rem; color: var(--text-secondary); margin: 0;
    display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;
}

/* Back link */
.iss-root .iss-back {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.4rem 0.7rem; border-radius: 0.5rem;
    font-size: 0.8125rem; font-weight: 600;
    color: var(--text-secondary); background: transparent;
    border: 1px solid transparent; text-decoration: none;
    transition: all 0.15s ease; margin-bottom: 1rem;
}
.iss-root .iss-back:hover {
    color: #5b9aff; background: rgba(91,154,255,0.08);
    border-color: rgba(91,154,255,0.20);
}
html.light-mode .iss-root .iss-back:hover { color: var(--thw-blue); background: rgba(0,51,127,0.06); }

/* Layout grid */
.iss-root .iss-grid {
    display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
    gap: 1rem; align-items: start;
}
.iss-root .iss-col { display: flex; flex-direction: column; gap: 1rem; min-width: 0; }

/* Card */
.iss-root .card {
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.875rem; padding: 1.25rem;
}
html.light-mode .iss-root .card { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
.iss-root .card-h {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.iss-root .section-label {
    display: inline-block; font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
}

/* Chip */
.iss-root .chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.18rem 0.55rem; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    white-space: nowrap;
}
.iss-root .chip--lehrgang   { background: rgba(91,154,255,0.14); color: #5b9aff; }
html.light-mode .iss-root .chip--lehrgang { color: var(--thw-blue); background: rgba(0,51,127,0.10); }
.iss-root .chip--question   { background: rgba(167,139,250,0.14); color: #a78bfa; }
.iss-root .chip--count      { background: rgba(91,154,255,0.18); color: #5b9aff; font-size: 0.7rem; }
html.light-mode .iss-root .chip--count { color: var(--thw-blue); }
.iss-root .chip--open       { background: rgba(239,68,68,0.18);  color: #ef4444; }
.iss-root .chip--in_review  { background: rgba(251,191,36,0.18); color: #fbbf24; }
.iss-root .chip--resolved   { background: rgba(34,197,94,0.18);  color: #22c55e; }
.iss-root .chip--rejected   { background: rgba(255,255,255,0.10); color: var(--text-secondary); }

/* Question display */
.iss-root .q-display {
    padding: 1rem 1.125rem;
    border-radius: 0.625rem;
    background: rgba(91,154,255,0.06);
    border-left: 3px solid #5b9aff;
    margin-bottom: 1rem;
}
html.light-mode .iss-root .q-display {
    background: rgba(0,51,127,0.04); border-left-color: var(--thw-blue);
}
.iss-root .q-display__text {
    font-size: 1rem; font-weight: 600;
    color: var(--text-primary); line-height: 1.55; margin: 0;
}

.iss-root .answer-list { display: flex; flex-direction: column; gap: 0.5rem; }
.iss-root .answer-item {
    display: flex; gap: 0.75rem; align-items: flex-start;
    padding: 0.75rem 0.875rem; border-radius: 0.625rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
}
html.light-mode .iss-root .answer-item {
    background: rgba(0,51,127,0.03); border-color: rgba(0,51,127,0.08);
}
.iss-root .answer-item.is-correct {
    background: rgba(34,197,94,0.08); border-color: rgba(34,197,94,0.25);
}
.iss-root .answer-letter {
    width: 1.875rem; height: 1.875rem; flex-shrink: 0;
    border-radius: 0.5rem;
    background: rgba(91,154,255,0.14); color: #5b9aff;
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 0.9375rem;
    display: grid; place-items: center;
}
html.light-mode .iss-root .answer-letter { background: rgba(0,51,127,0.10); color: var(--thw-blue); }
.iss-root .answer-item.is-correct .answer-letter {
    background: rgba(34,197,94,0.18); color: #22c55e;
}
.iss-root .answer-text {
    flex: 1; min-width: 0; font-size: 0.9rem; color: var(--text-secondary);
    line-height: 1.5; margin: 0; padding-top: 0.2rem;
}

/* Inline form */
.iss-root .field { margin-bottom: 0.875rem; }
.iss-root .field:last-child { margin-bottom: 0; }
.iss-root .label {
    display: block; font-size: 0.8125rem; font-weight: 600;
    color: var(--text-primary); margin-bottom: 0.375rem;
}
.iss-root .input,
.iss-root .textarea,
.iss-root .select {
    width: 100%;
    padding: 0.55rem 0.8rem;
    font-family: inherit; font-size: 0.9rem;
    color: var(--text-primary);
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 0.5rem; outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
html.light-mode .iss-root .input,
html.light-mode .iss-root .textarea,
html.light-mode .iss-root .select {
    background: #fff; border-color: rgba(0,51,127,0.15);
}
.iss-root .input:focus,
.iss-root .textarea:focus,
.iss-root .select:focus {
    border-color: #5b9aff; box-shadow: 0 0 0 3px rgba(91,154,255,0.18);
}
html.light-mode .iss-root .input:focus,
html.light-mode .iss-root .textarea:focus,
html.light-mode .iss-root .select:focus {
    border-color: var(--thw-blue); box-shadow: 0 0 0 3px rgba(0,51,127,0.12);
}
.iss-root .textarea { min-height: 84px; resize: vertical; line-height: 1.5; }

.iss-root .answer-row {
    display: grid; grid-template-columns: auto 1fr auto; gap: 0.625rem;
    align-items: center; padding: 0.5rem 0;
}
.iss-root .answer-row + .answer-row { border-top: 1px solid rgba(255,255,255,0.05); }
html.light-mode .iss-root .answer-row + .answer-row { border-top-color: rgba(0,51,127,0.06); }
.iss-root .answer-row__letter {
    width: 1.75rem; height: 1.75rem;
    border-radius: 0.5rem;
    background: rgba(91,154,255,0.14); color: #5b9aff;
    font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 0.875rem;
    display: grid; place-items: center;
}
html.light-mode .iss-root .answer-row__letter { background: rgba(0,51,127,0.10); color: var(--thw-blue); }
.iss-root .answer-row__correct {
    display: inline-flex; align-items: center; gap: 0.4rem;
    font-size: 0.75rem; font-weight: 600; color: var(--text-muted);
    cursor: pointer; user-select: none; white-space: nowrap;
}
.iss-root .answer-row__correct input { accent-color: #22c55e; }
.iss-root .answer-row__correct.is-correct { color: #22c55e; }

/* Buttons */
.iss-root .btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
    padding: 0.55rem 1rem; border-radius: 0.5rem;
    font-size: 0.875rem; font-weight: 600;
    border: 1px solid transparent; cursor: pointer; text-decoration: none;
    transition: all 0.15s ease; font-family: inherit;
}
.iss-root .btn--primary { background: #5b9aff; color: #fff; }
.iss-root .btn--primary:hover { background: #3b82f6; }
html.light-mode .iss-root .btn--primary { background: var(--thw-blue); }
html.light-mode .iss-root .btn--primary:hover { background: #002a66; }
.iss-root .btn--secondary {
    background: var(--glass-bg); border-color: var(--glass-border);
    color: var(--text-secondary);
}
.iss-root .btn--secondary:hover { color: var(--text-primary); border-color: #5b9aff; }
.iss-root .btn--danger {
    background: transparent; border-color: rgba(239,68,68,0.30); color: #ef4444;
}
.iss-root .btn--danger:hover { background: rgba(239,68,68,0.10); border-color: #ef4444; }
.iss-root .btn--ghost {
    background: transparent; color: var(--text-secondary);
    border-color: rgba(255,255,255,0.10);
}
html.light-mode .iss-root .btn--ghost { border-color: rgba(0,51,127,0.10); }
.iss-root .btn--ghost:hover { color: var(--text-primary); border-color: #5b9aff; }

/* Edit toggle / pencil button */
.iss-root .icon-btn {
    width: 2rem; height: 2rem; border-radius: 0.5rem;
    background: transparent; border: 1px solid var(--glass-border);
    color: var(--text-muted); cursor: pointer;
    display: grid; place-items: center; font-size: 0.85rem;
    transition: all 0.15s ease;
}
.iss-root .icon-btn:hover { color: #5b9aff; border-color: #5b9aff; background: rgba(91,154,255,0.06); }
html.light-mode .iss-root .icon-btn:hover { color: var(--thw-blue); border-color: var(--thw-blue); }

.iss-root .form-actions {
    display: flex; gap: 0.5rem; align-items: center;
    margin-top: 1rem; padding-top: 0.75rem;
    border-top: 1px solid rgba(255,255,255,0.06);
    flex-wrap: wrap;
}
html.light-mode .iss-root .form-actions { border-top-color: rgba(0,51,127,0.08); }

/* Meta list */
.iss-root .meta-list { display: grid; gap: 0.5rem; }
.iss-root .meta-row {
    display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;
    padding: 0.55rem 0.75rem; border-radius: 0.5rem;
    background: rgba(255,255,255,0.03);
    font-size: 0.8125rem;
}
html.light-mode .iss-root .meta-row { background: rgba(0,51,127,0.03); }
.iss-root .meta-row__label { color: var(--text-muted); font-weight: 600; }
.iss-root .meta-row__value { color: var(--text-primary); font-weight: 600; }

/* Reports */
.iss-root .reports {
    max-height: 360px; overflow-y: auto;
    padding: 0.25rem;
}
.iss-root .report {
    display: flex; gap: 0.625rem; padding: 0.625rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
html.light-mode .iss-root .report { border-bottom-color: rgba(0,51,127,0.06); }
.iss-root .report:last-child { border-bottom: 0; }
.iss-root .report__avatar {
    width: 2rem; height: 2rem; flex-shrink: 0;
    border-radius: 50%;
    background: #5b9aff; color: #fff;
    display: grid; place-items: center;
    font-weight: 700; font-size: 0.8125rem;
}
html.light-mode .iss-root .report__avatar { background: var(--thw-blue); }
.iss-root .report__body { flex: 1; min-width: 0; }
.iss-root .report__head {
    display: flex; align-items: baseline; justify-content: space-between;
    gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;
}
.iss-root .report__name { font-weight: 700; font-size: 0.8125rem; color: var(--text-primary); }
.iss-root .report__time {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; color: var(--text-muted);
}
.iss-root .report__msg { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.5; margin: 0; }
.iss-root .report__msg--empty { font-style: italic; color: var(--text-muted); }

/* Activity feed (Jira-style: report = note = status_change = comment) */
.iss-root .activity-feed { display: flex; flex-direction: column; gap: 0.5rem; }
.iss-root .activity {
    display: flex; gap: 0.75rem;
    padding: 0.75rem 0.875rem;
    border-radius: 0.625rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
}
html.light-mode .iss-root .activity {
    background: rgba(0,51,127,0.02); border-color: rgba(0,51,127,0.06);
}
.iss-root .activity--note {
    background: rgba(251,191,36,0.06);
    border-color: rgba(251,191,36,0.18);
}
.iss-root .activity--status {
    background: rgba(91,154,255,0.05);
    border-color: rgba(91,154,255,0.16);
    align-items: center;
}
html.light-mode .iss-root .activity--status {
    background: rgba(0,51,127,0.04); border-color: rgba(0,51,127,0.12);
}
.iss-root .activity__avatar {
    width: 2rem; height: 2rem; flex-shrink: 0;
    border-radius: 50%;
    background: #5b9aff; color: #fff;
    display: grid; place-items: center;
    font-weight: 700; font-size: 0.8125rem;
}
html.light-mode .iss-root .activity__avatar { background: var(--thw-blue); }
.iss-root .activity__avatar--note   { background: #fbbf24; color: #1a1a1a; }
.iss-root .activity__avatar--status { background: rgba(91,154,255,0.18); color: #5b9aff; font-size: 0.9rem; }
html.light-mode .iss-root .activity__avatar--status {
    background: rgba(0,51,127,0.12); color: var(--thw-blue);
}
.iss-root .activity__body { flex: 1; min-width: 0; }
.iss-root .activity__head {
    display: flex; align-items: center; gap: 0.5rem;
    margin-bottom: 0.25rem; flex-wrap: wrap;
}
.iss-root .activity__name {
    font-weight: 700; font-size: 0.8125rem; color: var(--text-primary);
}
.iss-root .activity__badge {
    display: inline-flex; align-items: center;
    padding: 0.1rem 0.45rem; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.5625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    background: rgba(251,191,36,0.16); color: #fbbf24;
}
.iss-root .activity__badge--report {
    background: rgba(91,154,255,0.16); color: #5b9aff;
}
html.light-mode .iss-root .activity__badge--report { color: var(--thw-blue); }
.iss-root .activity__time {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; color: var(--text-muted);
    margin-left: auto;
}
.iss-root .activity__msg {
    font-size: 0.875rem; color: var(--text-secondary);
    line-height: 1.5; margin: 0;
    white-space: pre-wrap; word-break: break-word;
}
.iss-root .activity__msg--empty { font-style: italic; color: var(--text-muted); }
.iss-root .activity__line {
    font-size: 0.8125rem; color: var(--text-secondary);
    margin: 0; line-height: 1.5;
    display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;
}
.iss-root .activity__line strong { color: var(--text-primary); font-weight: 700; }
.iss-root .activity--status .activity__time { margin-left: 0; flex-basis: 100%; margin-top: 0.2rem; }

/* Compose / new comment form */
.iss-root .activity-compose {
    display: flex; gap: 0.75rem;
    margin-top: 1rem; padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
html.light-mode .iss-root .activity-compose { border-top-color: rgba(0,51,127,0.08); }
.iss-root .activity-compose__avatar {
    width: 2rem; height: 2rem; flex-shrink: 0;
    border-radius: 50%;
    background: #5b9aff; color: #fff;
    display: grid; place-items: center;
    font-weight: 700; font-size: 0.8125rem;
}
html.light-mode .iss-root .activity-compose__avatar { background: var(--thw-blue); }
.iss-root .activity-compose__body { flex: 1; min-width: 0; }
.iss-root .activity-compose__body .textarea { min-height: 72px; }
.iss-root .activity-compose__actions {
    display: flex; justify-content: flex-end;
    margin-top: 0.5rem;
}

/* Alert */
.iss-root .alert {
    display: flex; gap: 0.75rem; align-items: flex-start;
    padding: 0.875rem 1rem; border-radius: 0.625rem;
    margin-bottom: 1rem; font-size: 0.875rem;
}
.iss-root .alert--success {
    background: rgba(34,197,94,0.08);
    border: 1px solid rgba(34,197,94,0.22);
    color: #22c55e;
}
.iss-root .alert--error {
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.22);
    color: #ef4444;
}
.iss-root .alert strong { display: block; }

/* Responsive */
@media (max-width: 1024px) {
    .iss-root .iss-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .iss-root .iss-h1 { font-size: 1.625rem; }
    .iss-root .meta-row { flex-direction: column; align-items: flex-start; gap: 0.2rem; }
}

[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="iss-root" x-data="{ editing: false }">

    <a href="{{ route('admin.issues.index') }}" class="iss-back">
        <i class="bi bi-arrow-left"></i> Zurück zur Übersicht
    </a>

    <div class="iss-header">
        <div class="iss-eyebrow">Administration · Fehlermeldung #{{ $issue->id }}</div>
        <h1 class="iss-h1">Fehlermeldung Details</h1>
        @if($question)
            <p class="iss-sub">
                <span class="chip chip--{{ $type }}">{{ $type === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}</span>
                @if($contextLabel)<span>{{ $contextLabel }}</span>@endif
            </p>
        @else
            <p class="iss-sub" style="color: #ef4444;">Frage wurde gelöscht</p>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert--success">
            <i class="bi bi-check-circle-fill"></i>
            <div>
                <strong>Erfolg!</strong>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert--error">
            <i class="bi bi-x-circle-fill"></i>
            <div>
                <strong>Fehler beim Speichern</strong>
                <ul style="margin: 0.25rem 0 0 1.25rem; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="iss-grid">
        {{-- Linke Spalte: Frage + Meldungen --}}
        <div class="iss-col">

            @if($question)
                @php
                    $sol = collect(explode(',', (string) ($question->loesung ?? '')))
                        ->map(fn ($s) => strtoupper(trim($s)))
                        ->filter()
                        ->all();
                    $editAction = $type === 'lehrgang'
                        ? route('admin.lehrgaenge.update-question', ['lehrgang' => $question->lehrgang_id, 'question' => $question->id])
                        : route('admin.questions.update', $question);
                    $editMethod = $type === 'lehrgang' ? 'PATCH' : 'PUT';
                @endphp

                <div class="card">
                    <div class="card-h">
                        <span class="section-label">Frage</span>
                        <button type="button"
                                class="icon-btn"
                                title="Frage bearbeiten"
                                @click="editing = !editing"
                                x-bind:class="{ 'is-active': editing }">
                            <i class="bi" x-bind:class="editing ? 'bi-x-lg' : 'bi-pencil'"></i>
                        </button>
                    </div>

                    {{-- View-Modus --}}
                    <div x-show="!editing" x-cloak>
                        <div class="q-display">
                            <p class="q-display__text">{{ $question->frage }}</p>
                        </div>

                        @php
                            $solutions = collect(explode(',', (string) ($question->loesung ?? '')))
                                ->map(fn ($s) => strtoupper(trim($s)))
                                ->filter()
                                ->all();
                        @endphp

                        <div class="answer-list">
                            @foreach(['A' => $question->antwort_a, 'B' => $question->antwort_b, 'C' => $question->antwort_c] as $letter => $text)
                                <div class="answer-item {{ in_array($letter, $solutions, true) ? 'is-correct' : '' }}">
                                    <div class="answer-letter">{{ $letter }}</div>
                                    <p class="answer-text">{{ $text }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 0.5rem;">
                            <span class="section-label">Lösung</span>
                            <span style="font-family: 'Figtree', sans-serif; font-weight: 800; color: #22c55e; font-size: 1rem; letter-spacing: 0.1em;">
                                {{ $question->loesung }}
                            </span>
                        </div>

                        @if(!empty($question->loesungsweg ?? null))
                            <div style="margin-top: 0.875rem; padding: 0.875rem 1rem; border-radius: 0.625rem; background: rgba(255,255,255,0.03); font-size: 0.875rem; color: var(--text-secondary); line-height: 1.55;">
                                <div class="section-label" style="margin-bottom: 0.4rem;">Lösungsweg</div>
                                {{ $question->loesungsweg }}
                            </div>
                        @endif

                        <div style="font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; color: var(--text-muted); margin-top: 0.875rem; text-transform: uppercase; letter-spacing: 0.08em;">
                            Frage-ID #{{ $question->id }}
                        </div>
                    </div>

                    {{-- Edit-Modus (für alle Frage-Typen) --}}
                    <div x-show="editing" x-cloak>
                        <form id="iss-question-edit-form"
                              method="POST"
                              action="{{ $editAction }}"
                              x-data="{ correct: @js($sol) }">
                            @csrf
                            @method($editMethod)

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="field">
                                    <label class="label">Lernabschnitt</label>
                                    <input type="{{ $type === 'lehrgang' ? 'number' : 'text' }}" name="lernabschnitt" class="input" value="{{ old('lernabschnitt', $question->lernabschnitt) }}" required>
                                </div>
                                <div class="field">
                                    <label class="label">Nummer</label>
                                    <input type="number" name="nummer" class="input" value="{{ old('nummer', $question->nummer) }}" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Frage</label>
                                <textarea name="frage" class="textarea" required>{{ old('frage', $question->frage) }}</textarea>
                            </div>

                            <div class="field">
                                <label class="label">Antworten · Richtige markieren</label>
                                @foreach(['A' => 'antwort_a', 'B' => 'antwort_b', 'C' => 'antwort_c'] as $letter => $field)
                                    <div class="answer-row">
                                        <div class="answer-row__letter">{{ $letter }}</div>
                                        <input type="text" name="{{ $field }}" class="input"
                                               value="{{ old($field, $question->{$field}) }}" required>
                                        <label class="answer-row__correct" x-bind:class="{ 'is-correct': correct.includes('{{ $letter }}') }">
                                            <input type="checkbox" value="{{ $letter }}" x-model="correct">
                                            Richtig
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Hidden field: comma-separated solution string (works for both controllers) --}}
                            <input type="hidden" name="loesung" x-bind:value="[...correct].sort().join(',')">

                            @if($type === 'question')
                                <div class="field">
                                    <label class="label">Lösungsweg <span style="color: var(--text-muted); font-weight: 500; font-size: 0.75rem;">(optional)</span></label>
                                    <textarea name="loesungsweg" class="textarea">{{ old('loesungsweg', $question->loesungsweg ?? '') }}</textarea>
                                </div>
                            @endif

                            <div class="form-actions">
                                <button type="submit" class="btn btn--primary">
                                    <i class="bi bi-check-lg"></i> Speichern
                                </button>
                                <button type="button" class="btn btn--ghost" @click="editing = false">
                                    Abbrechen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card alert alert--error" style="margin: 0;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Frage nicht gefunden</strong>
                        <div>Diese Frage wurde gelöscht oder existiert nicht mehr.</div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-h">
                    <span class="section-label">Aktivität</span>
                    <span class="chip chip--count">{{ $issue->report_count }}× gemeldet</span>
                </div>

                @php
                    // Status-Labels für Statuswechsel-Einträge
                    $statusLabels = [
                        'open'      => 'Offen',
                        'in_review' => 'In Bearbeitung',
                        'resolved'  => 'Gelöst',
                        'rejected'  => 'Abgelehnt',
                    ];

                    // Aktivitäten = alle reports + synthetischer Initial-Eintrag
                    // (falls Issue keine 'report'-Zeilen hat, aber latest_message existiert)
                    $activities = $issue->reports->sortBy('created_at')->values();
                    $hasReportRow = $activities->contains(fn ($a) => ($a->type ?? 'report') === 'report');
                    if (! $hasReportRow && $issue->reportedByUser) {
                        $activities = collect([(object) [
                            '_synthetic' => true,
                            'type' => 'report',
                            'message' => $issue->latest_message,
                            'user' => $issue->reportedByUser,
                            'created_at' => $issue->created_at,
                            'meta' => null,
                        ]])->merge($activities)->values();
                    }
                @endphp

                <div class="activity-feed">
                    @foreach($activities as $act)
                        @php
                            $actType = $act->type ?? 'report';
                            $actUserName = $act->user?->name ?? 'Anonym';
                            $actInitial = strtoupper(substr($actUserName, 0, 1));
                        @endphp

                        @if($actType === 'status_change')
                            <div class="activity activity--status">
                                <div class="activity__avatar activity__avatar--status">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                                <div class="activity__body">
                                    <p class="activity__line">
                                        <strong>{{ $actUserName }}</strong>
                                        änderte Status
                                        @php
                                            $old = $act->meta['old'] ?? null;
                                            $new = $act->meta['new'] ?? null;
                                        @endphp
                                        @if($old)
                                            von <span class="chip chip--{{ $old }}">{{ $statusLabels[$old] ?? $old }}</span>
                                        @endif
                                        @if($new)
                                            auf <span class="chip chip--{{ $new }}">{{ $statusLabels[$new] ?? $new }}</span>
                                        @endif
                                    </p>
                                    <span class="activity__time">{{ $act->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>
                        @elseif($actType === 'note')
                            <div class="activity activity--note">
                                <div class="activity__avatar activity__avatar--note">{{ $actInitial }}</div>
                                <div class="activity__body">
                                    <div class="activity__head">
                                        <span class="activity__name">{{ $actUserName }}</span>
                                        <span class="activity__badge">Notiz</span>
                                        <span class="activity__time">{{ $act->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p class="activity__msg">{{ $act->message }}</p>
                                </div>
                            </div>
                        @else
                            <div class="activity activity--report">
                                <div class="activity__avatar">{{ $actInitial }}</div>
                                <div class="activity__body">
                                    <div class="activity__head">
                                        <span class="activity__name">{{ $actUserName }}</span>
                                        <span class="activity__badge activity__badge--report">Meldung</span>
                                        <span class="activity__time">{{ $act->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    @if($act->message)
                                        <p class="activity__msg">{{ $act->message }}</p>
                                    @else
                                        <p class="activity__msg activity__msg--empty">Keine Nachricht</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Kommentar-Eingabe (Jira-Style: alles ist ein Kommentar) --}}
                <form method="POST"
                      action="{{ route('admin.issues.comments.store', ['issue' => $issue->id, 'type' => $type]) }}"
                      class="activity-compose">
                    @csrf
                    <div class="activity-compose__avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                    <div class="activity-compose__body">
                        <textarea name="message"
                                  class="textarea"
                                  placeholder="Kommentar hinzufügen…"
                                  maxlength="2000"
                                  required></textarea>
                        <div class="activity-compose__actions">
                            <button type="submit" class="btn btn--primary">
                                <i class="bi bi-send"></i> Kommentieren
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Rechte Spalte: Info + Bearbeitung --}}
        <div class="iss-col">

            <div class="card">
                <div class="card-h">
                    <span class="section-label">Information</span>
                </div>
                <div class="meta-list">
                    <div class="meta-row">
                        <span class="meta-row__label">Quelle</span>
                        <span class="chip chip--{{ $type }}">{{ $type === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}</span>
                    </div>
                    @if($question)
                        @foreach($contextDetails as $label => $value)
                            <div class="meta-row">
                                <span class="meta-row__label">{{ $label }}</span>
                                <span class="meta-row__value">{{ $value }}</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="meta-row">
                        <span class="meta-row__label">Status</span>
                        <span class="chip chip--{{ $issue->status }}">
                            @if($issue->status === 'open') Offen
                            @elseif($issue->status === 'in_review') In Bearbeitung
                            @elseif($issue->status === 'resolved') Gelöst
                            @else Abgelehnt
                            @endif
                        </span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-row__label">Meldungen</span>
                        <span class="chip chip--count">{{ $issue->report_count }}×</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-row__label">Zuletzt von</span>
                        <span class="meta-row__value">{{ $issue->reportedByUser?->name ?? 'Anonym' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-row__label">Aktualisiert</span>
                        <span class="meta-row__value">{{ $issue->updated_at?->format('d.m.Y H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-h">
                    <span class="section-label">Status</span>
                </div>
                <form method="POST" action="{{ route('admin.issues.update', ['issue' => $issue->id, 'type' => $type]) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label class="label">Status ändern</label>
                        <select name="status" class="select">
                            <option value="open" {{ $issue->status === 'open' ? 'selected' : '' }}>Offen</option>
                            <option value="in_review" {{ $issue->status === 'in_review' ? 'selected' : '' }}>In Bearbeitung</option>
                            <option value="resolved" {{ $issue->status === 'resolved' ? 'selected' : '' }}>Gelöst</option>
                            <option value="rejected" {{ $issue->status === 'rejected' ? 'selected' : '' }}>Abgelehnt</option>
                        </select>
                        <div style="font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; color: var(--text-muted); margin-top: 0.4rem; letter-spacing: 0.06em;">
                            Statuswechsel erscheinen als Eintrag in der Aktivität.
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary" style="flex: 1;">
                            <i class="bi bi-check-lg"></i> Status speichern
                        </button>
                        @if(auth()->user()->isAdmin())
                        <button type="button" onclick="confirmIssueDelete()" class="btn btn--danger">
                            <i class="bi bi-trash"></i> Löschen
                        </button>
                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="{{ route('admin.issues.destroy', ['issue' => $issue->id, 'type' => $type]) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmIssueDelete() {
        if (confirm('Willst du diese Fehlermeldung wirklich löschen?')) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Inline question edit form: submit via fetch & redirect zurück zur Issue-Seite
    (function () {
        const form = document.getElementById('iss-question-edit-form');
        if (!form) return;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn?.innerHTML;
            if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Speichert...'; }

            try {
                const fd = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                if (res.ok) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('saved', '1');
                    window.location.href = url.toString();
                } else {
                    let msg = 'Fehler beim Speichern.';
                    try {
                        const data = await res.json();
                        if (data?.errors) msg = Object.values(data.errors).flat().join('\n');
                        else if (data?.message) msg = data.message;
                    } catch (_) {}
                    alert(msg);
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; }
                }
            } catch (err) {
                alert('Netzwerkfehler beim Speichern.');
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; }
            }
        });

        // Wenn nach Speichern zurückkommen: Erfolgshinweis zeigen
        const params = new URLSearchParams(window.location.search);
        if (params.get('saved') === '1') {
            const wrap = document.querySelector('.iss-root');
            if (wrap) {
                const a = document.createElement('div');
                a.className = 'alert alert--success';
                a.innerHTML = '<i class="bi bi-check-circle-fill"></i><div><strong>Frage aktualisiert</strong><div>Die Änderungen wurden gespeichert.</div></div>';
                wrap.insertBefore(a, wrap.querySelector('.iss-grid'));
            }
            params.delete('saved');
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }
    })();
</script>
@endsection
