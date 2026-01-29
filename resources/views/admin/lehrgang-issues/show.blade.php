@extends('layouts.app')
@section('title', 'Fehlermeldung - Admin')

@push('styles')
<style>
    .answer-option {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
        border-left: 3px solid var(--thw-blue);
        margin-bottom: 0.75rem;
    }
    .answer-option:last-child {
        margin-bottom: 0;
    }
    .answer-letter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        background: var(--thw-blue);
        color: white;
        font-weight: 700;
        border-radius: 50%;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .chat-message {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .chat-message:last-child {
        margin-bottom: 0;
    }
    .chat-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: var(--thw-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    .chat-bubble {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        border-top-left-radius: 0;
        padding: 1rem;
        flex: 1;
    }
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .status-open { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .status-in_review { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .status-resolved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .status-rejected { background: rgba(255, 255, 255, 0.1); color: var(--text-secondary); }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Fehlermeldung <span>Details</span></h1>
        @if($issue->lehrgangQuestion)
            <p class="page-subtitle">{{ $issue->lehrgangQuestion->lehrgang->lehrgang }}</p>
        @else
            <p class="page-subtitle" style="color: var(--error);">Frage wurde geloscht</p>
        @endif
    </header>

    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.lehrgang-issues.index') }}" class="btn-secondary" style="padding: 0.625rem 1.25rem;">
            Zuruck zur Ubersicht
        </a>
    </div>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($issue->lehrgangQuestion)
        <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1.5rem 0;">Frage</h3>

            <div style="background: rgba(255, 255, 255, 0.03); padding: 1.25rem; border-radius: 0.75rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.5rem;">
                <p style="font-size: 1rem; font-weight: 600; color: var(--text-primary); line-height: 1.6; margin: 0;">
                    {{ $issue->lehrgangQuestion->frage }}
                </p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div class="answer-option">
                    <div style="display: flex; align-items: flex-start;">
                        <span class="answer-letter">A</span>
                        <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; padding-top: 0.25rem;">{{ $issue->lehrgangQuestion->antwort_a }}</p>
                    </div>
                </div>

                <div class="answer-option">
                    <div style="display: flex; align-items: flex-start;">
                        <span class="answer-letter">B</span>
                        <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; padding-top: 0.25rem;">{{ $issue->lehrgangQuestion->antwort_b }}</p>
                    </div>
                </div>

                <div class="answer-option">
                    <div style="display: flex; align-items: flex-start;">
                        <span class="answer-letter">C</span>
                        <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; padding-top: 0.25rem;">{{ $issue->lehrgangQuestion->antwort_c }}</p>
                    </div>
                </div>
            </div>

            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.5rem;">
                <p style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin: 0 0 0.25rem 0;">Richtige Antwort(en):</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: #22c55e; margin: 0;">{{ $issue->lehrgangQuestion->loesung }}</p>
            </div>

            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 1rem 0 0 0;">Frage-ID: {{ $issue->lehrgangQuestion->id }}</p>
        </div>
    @else
        <div class="glass-error" style="padding: 1.5rem; margin-bottom: 2rem;">
            <p style="font-weight: 600; margin: 0 0 0.5rem 0;">Diese Frage wurde geloscht oder existiert nicht mehr.</p>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">Frage-ID war: {{ $issue->lehrgang_question_id }}</p>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="glass hover-lift" style="padding: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1.5rem 0;">Meldungsdetails</h3>

            <div style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.5rem;">
                    <span style="color: var(--text-secondary); font-weight: 600;">Gesamtmeldungen:</span>
                    <span style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 9999px; font-weight: 700;">{{ $issue->report_count }}x</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.5rem;">
                    <span style="color: var(--text-secondary); font-weight: 600;">Zuletzt gemeldet von:</span>
                    <span style="color: var(--text-primary); font-weight: 500;">{{ $issue->reportedByUser?->name ?? 'Anonym' }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.5rem;">
                    <span style="color: var(--text-secondary); font-weight: 600;">Zuletzt aktualisiert:</span>
                    <span style="color: var(--text-primary); font-weight: 500;">{{ $issue->updated_at ? $issue->updated_at->format('d.m.Y H:i') : 'Nicht verfugbar' }}</span>
                </div>
            </div>

            <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 600; margin: 0 0 1rem 0; color: var(--text-primary);">Meldungen</h4>

                <div style="max-height: 400px; overflow-y: auto; background: rgba(255, 255, 255, 0.02); border-radius: 0.5rem; padding: 1rem; border: 1px solid rgba(255, 255, 255, 0.06);">
                    @forelse($issue->reports as $report)
                        <div class="chat-message">
                            <div class="chat-avatar">
                                {{ substr($report->user?->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="chat-bubble">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">{{ $report->user?->name ?? 'Anonym' }}</span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $report->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($report->message)
                                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5;">{{ $report->message }}</p>
                                @else
                                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem; font-style: italic;">Keine Nachricht</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="chat-message">
                            <div class="chat-avatar">
                                {{ substr($issue->reportedByUser?->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="chat-bubble">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">{{ $issue->reportedByUser?->name ?? 'Anonym' }}</span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $issue->updated_at ? $issue->updated_at->format('d.m.Y H:i') : '' }}</span>
                                </div>
                                @if($issue->latest_message)
                                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5;">{{ $issue->latest_message }}</p>
                                @else
                                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem; font-style: italic;">Keine Nachricht</p>
                                @endif
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="glass hover-lift" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Informationen</h3>

                <div style="font-size: 0.9rem;">
                    @if($issue->lehrgangQuestion)
                        <div style="padding-bottom: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                            <p style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 0 0 0.25rem 0;">Lehrgang</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">{{ $issue->lehrgangQuestion->lehrgang->lehrgang }}</p>
                        </div>

                        <div style="padding-bottom: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                            <p style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 0 0 0.25rem 0;">Lernabschnitt</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">{{ $issue->lehrgangQuestion->lernabschnitt }}</p>
                        </div>

                        <div style="padding-bottom: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                            <p style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 0 0 0.25rem 0;">Frage-Nr.</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">{{ $issue->lehrgangQuestion->nummer }}</p>
                        </div>
                    @else
                        <div style="background: rgba(239, 68, 68, 0.1); padding: 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 0.75rem;">
                            <p style="color: var(--error); font-size: 0.8rem; font-weight: 600; margin: 0;">Frage wurde geloscht</p>
                        </div>
                    @endif

                    <div>
                        <p style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 0 0 0.5rem 0;">Status</p>
                        <span class="status-badge status-{{ $issue->status }}">
                            @if($issue->status === 'open') Offen
                            @elseif($issue->status === 'in_review') In Bearbeitung
                            @elseif($issue->status === 'resolved') Gelost
                            @else Abgelehnt
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="glass hover-lift" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Bearbeitung</h3>

                <form method="POST" action="{{ route('admin.lehrgang-issues.update', ['lehrgang_issue' => $issue->id]) }}">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom: 1rem;">
                        <label class="label-glass" style="margin-bottom: 0.5rem; display: block; font-size: 0.85rem;">Status</label>
                        <select name="status" class="select-glass" style="padding: 0.625rem 1rem;">
                            <option value="open" {{ $issue->status === 'open' ? 'selected' : '' }}>Offen</option>
                            <option value="in_review" {{ $issue->status === 'in_review' ? 'selected' : '' }}>In Bearbeitung</option>
                            <option value="resolved" {{ $issue->status === 'resolved' ? 'selected' : '' }}>Gelost</option>
                            <option value="rejected" {{ $issue->status === 'rejected' ? 'selected' : '' }}>Abgelehnt</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="label-glass" style="margin-bottom: 0.5rem; display: block; font-size: 0.85rem;">Notizen</label>
                        <textarea name="admin_notes"
                                  class="textarea-glass"
                                  style="padding: 0.625rem 1rem; min-height: 100px; resize: vertical;"
                                  maxlength="1000"
                                  placeholder="Notizen...">{{ $issue->admin_notes ?? '' }}</textarea>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                            <span id="noteCount">{{ strlen($issue->admin_notes ?? '') }}</span>/1000
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                        <button type="submit" class="btn-primary" style="flex: 1; padding: 0.625rem 1rem; font-size: 0.9rem;">
                            Speichern
                        </button>

                        <button type="button" onclick="confirmDelete()" class="btn-danger" style="flex: 1; padding: 0.625rem 1rem; font-size: 0.9rem;">
                            Loschen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="{{ route('admin.lehrgang-issues.destroy', ['lehrgang_issue' => $issue->id]) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete() {
        if (confirm('Willst du diese Fehlermeldung wirklich loschen?')) {
            document.getElementById('deleteForm').submit();
        }
    }

    document.querySelector('textarea[name="admin_notes"]').addEventListener('input', function() {
        document.getElementById('noteCount').textContent = this.value.length;
    });
</script>
@endsection
