<!-- Modal Format (für AJAX) - Fragen-Liste - Glassmorphism -->
<div class="modal-header-glass">
    <h2>Fragen in {{ $lernpool->name }}</h2>
    <button class="modal-close-btn" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">&times;</button>
</div>
<div class="modal-body-glass">
    @if($questions->count() > 0)
        <div style="max-height: 400px; overflow-y: auto;">
            @foreach($questions as $question)
                <div class="glass-subtle" style="padding: 0.875rem; border-radius: 0.75rem; margin-bottom: 0.5rem; border-left: 3px solid var(--gold-start);">
                    <div style="display: flex; align-items: start; justify-content: space-between; gap: 0.75rem;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.7rem; font-weight: 600; color: var(--gold-start); margin-bottom: 0.25rem;">
                                {{ $question->lernabschnitt ?? 'Allgemein' }}.{{ $question->nummer ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                {{ Str::limit($question->frage, 80) }}
                            </div>
                            <div style="font-size: 0.7rem; color: #22c55e;">
                                Lösung: {{ strtoupper($question->loesung) }}
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                            <a href="{{ route('ortsverband.lernpools.questions.edit', [$ortsverband, $lernpool, $question]) }}"
                               class="modal-trigger btn-icon-action btn-icon-edit"
                               data-modal-type="edit"
                               title="Bearbeiten">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                    class="btn-icon-action btn-icon-delete"
                                    data-question-id="{{ $question->id }}"
                                    data-delete-url="{{ route('ortsverband.lernpools.questions.destroy', [$ortsverband, $lernpool, $question]) }}"
                                    title="Löschen">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 2rem;">
            <div style="font-size: 2rem; color: var(--text-muted); margin-bottom: 0.75rem; opacity: 0.6;"><i class="bi bi-question-circle"></i></div>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">Noch keine Fragen hinzugefügt</p>
        </div>
    @endif
</div>
<div class="modal-footer-glass">
    <button type="button" class="btn-ghost" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">Zurück</button>
    <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" class="btn-primary modal-trigger" data-modal-type="create">Neue Frage</a>
</div>

<style>
.btn-icon-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    background: transparent;
}
.btn-icon-edit {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
    text-decoration: none;
}
.btn-icon-edit:hover {
    background: rgba(59, 130, 246, 0.25);
    transform: scale(1.05);
}
.btn-icon-delete {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}
.btn-icon-delete:hover {
    background: rgba(239, 68, 68, 0.25);
    transform: scale(1.05);
}
</style>
