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
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-medium text-gray-600">
                                🔹 {{ $question->learning_section ?? 'Allgemein' }}
                            </p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                {{ Str::limit($question->frage ?? $question->text, 80) }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">
                                ✓ {{ $question->loesung ?? $question->answer }}
                            </p>
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
