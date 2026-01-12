<!-- Modal Format (für AJAX) -->
<div class="modal-header">
    <h2>{{ $lernpool->name }}</h2>
    <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
</div>
<div class="modal-body">
    <p class="text-sm text-gray-600 mb-4">{{ $lernpool->description }}</p>

    <!-- Statistiken -->
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Gesamt Fragen</p>
            <p class="text-2xl font-bold text-blue-600">{{ $lernpool->getQuestionCount() }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Teilnehmer</p>
            <p class="text-2xl font-bold text-green-600">{{ $lernpool->getEnrollmentCount() }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Ø Fortschritt</p>
            <p class="text-2xl font-bold text-yellow-600">{{ round($lernpool->getAverageProgress()) }}%</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Status</p>
            <p class="text-lg font-bold {{ $lernpool->is_active ? 'text-green-600' : 'text-gray-600' }}">
                {{ $lernpool->is_active ? '✓ Aktiv' : '✗ Inaktiv' }}
            </p>
        </div>
    </div>

    <!-- Kurze Fragenübersicht -->
    <div class="space-y-3">
        @forelse($questionsBySection as $section => $sectionQuestions)
            <div>
                <h3 class="font-semibold text-sm text-gray-800 mb-2">📚 {{ $section }}</h3>
                <p class="text-xs text-gray-600">{{ count($sectionQuestions) }} Fragen</p>
            </div>
        @empty
            <p class="text-gray-600 text-sm">Noch keine Fragen hinzugefügt</p>
        @endforelse
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">Zurück</button>
    <a href="{{ route('ortsverband.lernpools.edit', [$ortsverband, $lernpool]) }}" class="btn btn-primary modal-trigger" data-modal-type="edit">✏️ Bearbeiten</a>
</div>
