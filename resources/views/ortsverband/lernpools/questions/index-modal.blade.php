<!-- Modal Format (für AJAX) - Fragen-Liste -->
<div class="modal-header">
    <h2>Fragen in {{ $lernpool->name }}</h2>
    <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
</div>
<div class="modal-body">
    @if($questions->count() > 0)
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @foreach($questions as $question)
                <div class="border-l-4 border-blue-500 bg-gray-50 p-3 rounded">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-xs font-medium text-gray-600">
                                🔹 {{ $question->lernabschnitt ?? 'Allgemein' }}.{{ $question->nummer ?? '-' }}
                            </p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                {{ Str::limit($question->frage, 80) }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">
                                ✓ Lösung: {{ strtoupper($question->loesung) }}
                            </p>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <a href="{{ route('ortsverband.lernpools.questions.edit', [$ortsverband, $lernpool, $question]) }}"
                               class="modal-trigger btn-icon-edit"
                               data-modal-type="edit"
                               title="Bearbeiten">
                                ✏️
                            </a>
                            <button type="button"
                                    class="btn-icon-delete"
                                    data-question-id="{{ $question->id }}"
                                    data-delete-url="{{ route('ortsverband.lernpools.questions.destroy', [$ortsverband, $lernpool, $question]) }}"
                                    title="Löschen">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600 text-sm text-center py-4">Noch keine Fragen hinzugefügt</p>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">Zurück</button>
    <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" class="btn btn-primary modal-trigger" data-modal-type="create">➕ Neue Frage</a>
</div>

<style>
.btn-icon-edit, .btn-icon-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    background: transparent;
}
.btn-icon-edit {
    background: #eff6ff;
}
.btn-icon-edit:hover {
    background: #dbeafe;
    transform: scale(1.1);
}
.btn-icon-delete {
    background: #fef2f2;
}
.btn-icon-delete:hover {
    background: #fee2e2;
    transform: scale(1.1);
}
</style>
