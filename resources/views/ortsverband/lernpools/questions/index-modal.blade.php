<!-- Questions Index Modal - Glassmorphism Alpine.js -->
<div class="modal-header-glass">
    <h2>Fragen in {{ $lernpool->name }}</h2>
    <button class="modal-close-btn" type="button">&times;</button>
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
        <div style="text-align: center; padding: 2.5rem;">
            <div style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.5;">
                <i class="bi bi-question-circle"></i>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">Noch keine Fragen hinzugefügt</p>
        </div>
    @endif
</div>

<div class="modal-footer-glass">
    <button type="button" class="btn-ghost modal-close-btn">Zurück</button>
    <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" class="btn-primary modal-trigger">Neue Frage</a>
</div>
