@extends('layouts.app')
@section('title', 'Einreichung #' . $submission->id . ' - THW Trainer Admin')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .ueq-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        font-size: 0.8125rem;
        font-weight: 700;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .ueq-status-badge--pending  { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .ueq-status-badge--approved { background: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .ueq-status-badge--changed  { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .ueq-status-badge--rejected { background: rgba(255, 255, 255, 0.1); color: var(--text-secondary); }

    .ueq-quote {
        background: rgba(255, 255, 255, 0.03);
        border-left: 3px solid var(--gold-start);
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
    }
    html.light-mode .ueq-quote { background: rgba(0, 51, 127, 0.04); }

    .ueq-option {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
        border-left: 3px solid var(--thw-blue);
        padding: 0.875rem 1rem;
    }
    html.light-mode .ueq-option { background: rgba(0, 51, 127, 0.04); }
    .ueq-option + .ueq-option { margin-top: 0.5rem; }

    .ueq-option-letter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.75rem;
        height: 1.75rem;
        background: var(--thw-blue);
        color: white;
        font-weight: 700;
        font-size: 0.8125rem;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .ueq-correct-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.55rem;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 9999px;
        background: rgba(34, 197, 94, 0.18);
        color: #22c55e;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .ueq-meta-row {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .ueq-meta-row:last-child { border-bottom: 0; padding-bottom: 0; margin-bottom: 0; }
    html.light-mode .ueq-meta-row { border-bottom-color: rgba(0, 51, 127, 0.08); }

    .ueq-meta-key {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }
    .ueq-meta-val {
        font-size: 0.9375rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .ueq-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 900px) { .ueq-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $typ = $submission->typ;
    $payload = $submission->payload ?? [];
    $sectionStyle = 'padding-left: 1rem; border-left: 3px solid var(--gold-start);';
@endphp

<div class="dash-container" style="max-width: 60rem;">
    <div style="margin-bottom: 1.25rem;">
        <a href="{{ route('admin.extra-question-submissions.index') }}" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            <i class="bi bi-arrow-left"></i> Zurück zur Übersicht
        </a>
    </div>

    <div class="zf-page-title" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
        <div>
            <div class="zf-page-title__eyebrow">Admin · Zusatz-Fragen</div>
            <h1 class="zf-page-title__h1">Einreichung #{{ $submission->id }}</h1>
            <div class="zf-page-title__sub">{{ $submission->user->name ?? 'Unbekannt' }} · {{ $submission->created_at->format('d.m.Y H:i') }}</div>
        </div>
        <span class="ueq-status-badge ueq-status-badge--{{ $submission->status }}">{{ $submission->statusLabel() }}</span>
    </div>

    @if(session('success'))
        <div class="glass-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 0.875rem; align-items: flex-start;">
            <i class="bi bi-check-circle-fill" style="font-size: 1.125rem; flex-shrink: 0;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="ueq-grid">
        {{-- Main column: Frage + Antwort-Inhalte --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div>
                <div class="section-header" style="{{ $sectionStyle }}">
                    <h2 class="section-title">Frage</h2>
                </div>
                <div class="glass hover-lift" style="padding: 1.5rem;">
                    <div class="ueq-quote" style="font-size: 1rem; line-height: 1.6; color: var(--text-primary); font-weight: 500;">
                        {{ $submission->frage }}
                    </div>
                </div>
            </div>

            @if($typ === 'image_name')
                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Bild-Beschreibung</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.5rem;">
                        <div class="ueq-quote" style="white-space: pre-wrap; color: var(--text-secondary); line-height: 1.55;">{{ $payload['image_description'] ?? '' }}</div>
                        @if(!empty($payload['image_source_hint']))
                            <div style="margin-top: 1rem;">
                                <div class="ueq-meta-key" style="margin-bottom: 0.25rem;">Quellen-Hinweis</div>
                                <div class="ueq-meta-val">{{ $payload['image_source_hint'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Antwort-Optionen</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.5rem;">
                        @foreach(($payload['options'] ?? []) as $i => $opt)
                            <div class="ueq-option" style="display: flex; align-items: center; gap: 1rem;">
                                <span class="ueq-option-letter">{{ chr(65 + $i) }}</span>
                                <div style="flex: 1; color: var(--text-secondary); line-height: 1.5;">{{ $opt['text'] ?? '' }}</div>
                                @if(!empty($opt['is_correct']))
                                    <span class="ueq-correct-pill"><i class="bi bi-check-lg"></i> Richtig</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($typ === 'image_select')
                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Bild-Optionen (Beschreibungen)</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.5rem;">
                        @foreach(($payload['options'] ?? []) as $i => $opt)
                            <div class="ueq-option">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.5rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.625rem; font-weight: 600; color: var(--text-primary);">
                                        <span class="ueq-option-letter">{{ chr(65 + $i) }}</span>
                                        Option {{ chr(65 + $i) }}
                                    </span>
                                    @if(!empty($opt['is_correct']))
                                        <span class="ueq-correct-pill"><i class="bi bi-check-lg"></i> Richtig</span>
                                    @endif
                                </div>
                                <div style="white-space: pre-wrap; color: var(--text-secondary); line-height: 1.55;">{{ $opt['image_description'] ?? '' }}</div>
                                @if(!empty($opt['image_source_hint']))
                                    <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.5rem;">
                                        <strong style="color: var(--text-secondary);">Quellen-Hinweis:</strong> {{ $opt['image_source_hint'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($typ === 'matching')
                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Kategorien</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.5rem;">
                        @foreach(($payload['categories'] ?? []) as $i => $cat)
                            <div class="ueq-option" style="display: flex; align-items: center; gap: 1rem;">
                                <span class="ueq-option-letter">{{ $i + 1 }}</span>
                                <div style="flex: 1; color: var(--text-secondary); font-weight: 500;">{{ $cat['name'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Items &amp; Zuordnung</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.5rem;">
                        @foreach(($payload['items'] ?? []) as $i => $item)
                            @php
                                $catIdx = (int) ($item['category_index'] ?? 0);
                                $catName = $payload['categories'][$catIdx]['name'] ?? 'Kategorie ' . ($catIdx + 1);
                            @endphp
                            <div class="ueq-option" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span class="ueq-option-letter">{{ $i + 1 }}</span>
                                    <div style="color: var(--text-secondary); line-height: 1.5;">{{ $item['text'] ?? '' }}</div>
                                </div>
                                <div style="color: var(--text-muted); font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.35rem; white-space: nowrap;">
                                    <i class="bi bi-arrow-right"></i> {{ $catName }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($submission->admin_notes)
                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Admin-Hinweis (an User gesendet)</h2>
                    </div>
                    <div class="glass-warning" style="padding: 1.25rem;">
                        <div style="white-space: pre-wrap; color: var(--text-primary); line-height: 1.55;">{{ $submission->admin_notes }}</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar: Aktionen + Meta --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @if($submission->isPending() && auth()->user()->canEditQuestions())
                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Aktionen</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.25rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <a href="{{ route('admin.extra-questions.create', ['from_submission' => $submission->id]) }}"
                               class="btn-primary" style="justify-content: center;">
                                <i class="bi bi-check2-circle"></i> Übernehmen &amp; Frage anlegen
                            </a>
                            <button type="button" class="btn-danger" style="justify-content: center;"
                                    onclick="document.getElementById('reject-form').style.display='block'; this.style.display='none';">
                                <i class="bi bi-x-circle"></i> Ablehnen
                            </button>
                        </div>

                        <form id="reject-form" method="POST" action="{{ route('admin.extra-question-submissions.reject', $submission) }}"
                              style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                            @csrf
                            <label class="label-glass" for="admin_notes" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem;">
                                Begründung für die Ablehnung *
                            </label>
                            <textarea id="admin_notes" name="admin_notes" class="textarea-glass" required maxlength="2000"
                                      style="padding: 0.625rem 1rem; min-height: 100px; resize: vertical; width: 100%;"
                                      placeholder="z.B. Frage zu unspezifisch / passt nicht zum Lernabschnitt / Duplikat"></textarea>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.4rem 0 0.875rem 0;">
                                Der User erhält diese Begründung per E-Mail und In-App-Mitteilung.
                            </p>
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="submit" class="btn-danger" style="flex: 1; justify-content: center;">Einreichung ablehnen</button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($submission->status === 'approved' || $submission->status === 'changed')
                <div>
                    <div class="section-header" style="{{ $sectionStyle }}">
                        <h2 class="section-title">Aktionen</h2>
                    </div>
                    <div class="glass hover-lift" style="padding: 1.25rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @if($submission->extraQuestion)
                                <a href="{{ route('admin.extra-questions.edit', $submission->extra_question_id) }}" class="btn-secondary" style="justify-content: center;">
                                    <i class="bi bi-pencil"></i> Erstellte Frage bearbeiten
                                </a>
                            @endif
                            @if(auth()->user()->canEditQuestions())
                                <button type="button" class="btn-ghost" style="justify-content: center;"
                                        onclick="document.getElementById('changed-form').style.display='block'; this.style.display='none';">
                                    <i class="bi bi-pencil-square"></i> Als "mit Änderungen übernommen" markieren
                                </button>
                            @endif
                        </div>

                        @if(auth()->user()->canEditQuestions())
                            <form id="changed-form" method="POST" action="{{ route('admin.extra-question-submissions.mark-changed', $submission) }}"
                                  style="display: {{ $submission->status === 'changed' ? 'block' : 'none' }}; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                                @csrf
                                <label class="label-glass" for="admin_notes_changed" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem;">
                                    Hinweis an den User (optional)
                                </label>
                                <textarea id="admin_notes_changed" name="admin_notes" class="textarea-glass" maxlength="2000"
                                          style="padding: 0.625rem 1rem; min-height: 100px; resize: vertical; width: 100%;"
                                          placeholder="z.B. Frage wurde umformuliert / Optionen erweitert / Lernabschnitt angepasst">{{ $submission->admin_notes }}</textarea>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.875rem;">
                                    <button type="submit" class="btn-primary" style="flex: 1; justify-content: center;">Status &amp; Hinweis speichern</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <div>
                <div class="section-header" style="{{ $sectionStyle }}">
                    <h2 class="section-title">Übersicht</h2>
                </div>
                <div class="glass hover-lift" style="padding: 1.25rem;">
                    <div class="ueq-meta-row">
                        <div class="ueq-meta-key">User</div>
                        <div class="ueq-meta-val">
                            {{ $submission->user->name ?? 'Unbekannt' }}
                            @if($submission->user)
                                <div style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 400; margin-top: 0.125rem;">{{ $submission->user->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="ueq-meta-row">
                        <div class="ueq-meta-key">Fragetyp</div>
                        <div class="ueq-meta-val">{{ $submission->typLabel() }}</div>
                    </div>
                    <div class="ueq-meta-row">
                        <div class="ueq-meta-key">Lernabschnitt</div>
                        <div class="ueq-meta-val">{{ $submission->lernabschnitt }}</div>
                    </div>
                    <div class="ueq-meta-row">
                        <div class="ueq-meta-key">Eingereicht</div>
                        <div class="ueq-meta-val">{{ $submission->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    @if($submission->reviewed_at)
                        <div class="ueq-meta-row">
                            <div class="ueq-meta-key">Bearbeitet</div>
                            <div class="ueq-meta-val">
                                {{ $submission->reviewed_at->format('d.m.Y H:i') }}
                                @if($submission->reviewer)
                                    <div style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 400; margin-top: 0.125rem;">durch {{ $submission->reviewer->name }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
