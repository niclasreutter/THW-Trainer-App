@extends('layouts.app')
@section('title', 'Einreichung #' . $submission->id . ' - THW Trainer Admin')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .ueq-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        font-size: 0.8125rem;
        font-weight: 700;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .ueq-status--pending  { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .ueq-status--approved { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }
    .ueq-status--changed  { background: rgba(217, 119, 6, 0.12);  color: #d97706; }
    .ueq-status--rejected { background: rgba(239, 68, 68, 0.12);  color: #ef4444; }
    html:not(.light-mode) .ueq-status--pending  { background: rgba(96, 165, 250, 0.18); color: #93c5fd; }
    html:not(.light-mode) .ueq-status--approved { background: rgba(34, 197, 94, 0.18);  color: #86efac; }
    html:not(.light-mode) .ueq-status--changed  { background: rgba(245, 158, 11, 0.18); color: #fcd34d; }
    html:not(.light-mode) .ueq-status--rejected { background: rgba(248, 113, 113, 0.18); color: #fca5a5; }
    .ueq-kv-row { display: flex; gap: 0.5rem; align-items: baseline; padding: 0.35rem 0; }
    .ueq-kv-row + .ueq-kv-row { border-top: 1px solid rgba(0, 51, 127, 0.06); }
    html:not(.light-mode) .ueq-kv-row + .ueq-kv-row { border-top-color: rgba(255, 255, 255, 0.05); }
    .ueq-kv-key {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        min-width: 140px;
    }
    .ueq-kv-val { font-size: 0.9375rem; color: var(--text-primary); flex: 1; }
    .ueq-block {
        background: rgba(0, 51, 127, 0.04);
        border-left: 3px solid var(--thw-blue);
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
    }
    html:not(.light-mode) .ueq-block { background: rgba(91, 154, 255, 0.06); border-left-color: #5b9aff; }
    .ueq-option-correct {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.1rem 0.45rem;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 999px;
        background: rgba(22, 163, 74, 0.15);
        color: #16a34a;
    }
</style>
@endpush

@section('content')
@php
    $typ = $submission->typ;
    $payload = $submission->payload ?? [];
@endphp

<div class="dashboard-container">
    <div style="margin-bottom: 0.75rem;">
        <a href="{{ route('admin.extra-question-submissions.index') }}" class="zf-back-btn">
            <i class="bi bi-arrow-left"></i> Zurück zur Übersicht
        </a>
    </div>

    <header class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
        <div>
            <h1 class="page-title">Einreichung <span>#{{ $submission->id }}</span></h1>
            <p class="page-subtitle">{{ $submission->user->name ?? 'Unbekannt' }} · {{ $submission->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <span class="ueq-status ueq-status--{{ $submission->status }}">{{ $submission->statusLabel() }}</span>
    </header>

    @if(session('success'))
        <div class="zf-alert zf-alert--success">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($submission->isPending() && auth()->user()->canEditQuestions())
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Aktionen</span>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <a href="{{ route('admin.extra-questions.create', ['from_submission' => $submission->id]) }}"
                   class="btn-primary">
                    <i class="bi bi-check2-circle"></i> Übernehmen &amp; Frage anlegen
                </a>
                <button type="button" class="btn-danger"
                        onclick="document.getElementById('reject-form').style.display='block'">
                    <i class="bi bi-x-circle"></i> Ablehnen
                </button>
            </div>

            <form id="reject-form" method="POST" action="{{ route('admin.extra-question-submissions.reject', $submission) }}"
                  style="display: none; margin-top: 1rem;">
                @csrf
                <div class="zf-field">
                    <label class="zf-label" for="admin_notes">
                        Begründung für die Ablehnung <span class="zf-req">*</span>
                    </label>
                    <textarea id="admin_notes" name="admin_notes" class="zf-textarea" required maxlength="2000"
                              placeholder="z.B. Frage zu unspezifisch / passt nicht zum Lernabschnitt / Duplikat"></textarea>
                    <span class="zf-help">Der User erhält diese Begründung per E-Mail und In-App-Mitteilung.</span>
                </div>
                <div class="zf-form-actions">
                    <button type="button" class="btn-ghost"
                            onclick="document.getElementById('reject-form').style.display='none'">Abbrechen</button>
                    <button type="submit" class="btn-danger">Einreichung ablehnen</button>
                </div>
            </form>
        </div>
    @elseif($submission->status === 'approved' || $submission->status === 'changed')
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Aktionen</span>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                @if($submission->extraQuestion)
                    <a href="{{ route('admin.extra-questions.edit', $submission->extra_question_id) }}" class="btn-secondary">
                        <i class="bi bi-pencil"></i> Erstellte Frage bearbeiten
                    </a>
                @endif
                @if(auth()->user()->canEditQuestions())
                    <button type="button" class="btn-ghost"
                            onclick="document.getElementById('changed-form').style.display='block'">
                        <i class="bi bi-pencil-square"></i> Als "mit Änderungen übernommen" markieren
                    </button>
                @endif
            </div>

            @if(auth()->user()->canEditQuestions())
                <form id="changed-form" method="POST" action="{{ route('admin.extra-question-submissions.mark-changed', $submission) }}"
                      style="display: {{ $submission->status === 'changed' ? 'block' : 'none' }}; margin-top: 1rem;">
                    @csrf
                    <div class="zf-field">
                        <label class="zf-label" for="admin_notes_changed">Hinweis an den User (optional)</label>
                        <textarea id="admin_notes_changed" name="admin_notes" class="zf-textarea" maxlength="2000"
                                  placeholder="z.B. Frage wurde umformuliert / Optionen erweitert / Lernabschnitt angepasst">{{ $submission->admin_notes }}</textarea>
                    </div>
                    <div class="zf-form-actions">
                        <button type="button" class="btn-ghost"
                                onclick="document.getElementById('changed-form').style.display='none'">Abbrechen</button>
                        <button type="submit" class="btn-primary">Status &amp; Hinweis speichern</button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Übersicht</span>
        </div>
        <div class="ueq-kv-row">
            <div class="ueq-kv-key">User</div>
            <div class="ueq-kv-val">
                {{ $submission->user->name ?? 'Unbekannt' }}
                @if($submission->user)
                    <span style="color: var(--text-muted);">· {{ $submission->user->email }}</span>
                @endif
            </div>
        </div>
        <div class="ueq-kv-row">
            <div class="ueq-kv-key">Fragetyp</div>
            <div class="ueq-kv-val">{{ $submission->typLabel() }}</div>
        </div>
        <div class="ueq-kv-row">
            <div class="ueq-kv-key">Lernabschnitt</div>
            <div class="ueq-kv-val">{{ $submission->lernabschnitt }}</div>
        </div>
        <div class="ueq-kv-row">
            <div class="ueq-kv-key">Eingereicht am</div>
            <div class="ueq-kv-val">{{ $submission->created_at->format('d.m.Y H:i') }}</div>
        </div>
        @if($submission->reviewed_at)
            <div class="ueq-kv-row">
                <div class="ueq-kv-key">Bearbeitet</div>
                <div class="ueq-kv-val">
                    {{ $submission->reviewed_at->format('d.m.Y H:i') }}
                    @if($submission->reviewer)
                        <span style="color: var(--text-muted);">durch {{ $submission->reviewer->name }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Frage</span>
        </div>
        <div class="ueq-block">{{ $submission->frage }}</div>
    </div>

    @if($typ === 'image_name')
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Bild-Beschreibung</span>
            </div>
            <div class="ueq-block" style="white-space: pre-wrap;">{{ $payload['image_description'] ?? '' }}</div>
            @if(!empty($payload['image_source_hint']))
                <div class="ueq-kv-row">
                    <div class="ueq-kv-key">Quellen-Hinweis</div>
                    <div class="ueq-kv-val">{{ $payload['image_source_hint'] }}</div>
                </div>
            @endif
        </div>

        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Antwort-Optionen</span>
            </div>
            @foreach(($payload['options'] ?? []) as $i => $opt)
                <div class="ueq-block" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div>
                        <strong>{{ chr(65 + $i) }})</strong> {{ $opt['text'] ?? '' }}
                    </div>
                    @if(!empty($opt['is_correct']))
                        <span class="ueq-option-correct"><i class="bi bi-check-lg"></i> Richtig</span>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif($typ === 'image_select')
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Bild-Optionen (Beschreibungen)</span>
            </div>
            @foreach(($payload['options'] ?? []) as $i => $opt)
                <div class="ueq-block">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.4rem;">
                        <strong>Option {{ chr(65 + $i) }}</strong>
                        @if(!empty($opt['is_correct']))
                            <span class="ueq-option-correct"><i class="bi bi-check-lg"></i> Richtig</span>
                        @endif
                    </div>
                    <div style="white-space: pre-wrap;">{{ $opt['image_description'] ?? '' }}</div>
                    @if(!empty($opt['image_source_hint']))
                        <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.35rem;">
                            <strong>Quellen-Hinweis:</strong> {{ $opt['image_source_hint'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif($typ === 'matching')
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Kategorien</span>
            </div>
            @foreach(($payload['categories'] ?? []) as $i => $cat)
                <div class="ueq-block">
                    <strong>{{ $i + 1 }}.</strong> {{ $cat['name'] ?? '' }}
                </div>
            @endforeach
        </div>

        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Items &amp; Zuordnung</span>
            </div>
            @foreach(($payload['items'] ?? []) as $i => $item)
                @php
                    $catIdx = (int) ($item['category_index'] ?? 0);
                    $catName = $payload['categories'][$catIdx]['name'] ?? 'Kategorie ' . ($catIdx + 1);
                @endphp
                <div class="ueq-block" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div><strong>Item {{ $i + 1 }}:</strong> {{ $item['text'] ?? '' }}</div>
                    <div style="color: var(--text-muted); font-size: 0.875rem;">
                        <i class="bi bi-arrow-right"></i> {{ $catName }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($submission->admin_notes)
        <div class="zf-form-card">
            <div class="zf-form-card__label">
                <span class="zf-section-label">Admin-Hinweis (an User gesendet)</span>
            </div>
            <div class="ueq-block" style="background: rgba(217, 119, 6, 0.08); border-left-color: #d97706; white-space: pre-wrap;">
                {{ $submission->admin_notes }}
            </div>
        </div>
    @endif
</div>
@endsection
