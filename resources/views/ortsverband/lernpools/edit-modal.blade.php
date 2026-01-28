<!-- Edit Modal - Glassmorphism Alpine.js -->
<div class="modal-header-glass">
    <h2>{{ $lernpool->name }} bearbeiten</h2>
    <button class="modal-close-btn" type="button">&times;</button>
</div>

<form action="{{ route('ortsverband.lernpools.update', [$ortsverband, $lernpool]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="modal-body-glass">
        <div class="form-group-alpine">
            <label class="form-label-alpine">
                Name <span class="required-alpine">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $lernpool->name) }}"
                   class="input-alpine" required>
        </div>

        <div class="form-group-alpine">
            <label class="form-label-alpine">
                Beschreibung <span class="required-alpine">*</span>
            </label>
            <textarea name="description" rows="4" class="textarea-alpine" required>{{ old('description', $lernpool->description) }}</textarea>
        </div>

        <div class="form-group-alpine">
            <label class="form-label-alpine">
                Schlagwörter / Tags <span class="optional-alpine">(optional)</span>
            </label>
            @if($existingTags->isNotEmpty())
            <div class="tag-suggestions-alpine">
                @foreach($existingTags as $tag)
                    <button type="button" onclick="addTagToInput('{{ $tag }}')" class="tag-btn-alpine">+ {{ $tag }}</button>
                @endforeach
            </div>
            @endif
            <input type="text" name="tags" id="edit-tags-input"
                   value="{{ old('tags', is_array($lernpool->tags) ? implode(', ', $lernpool->tags) : '') }}"
                   class="input-alpine"
                   placeholder="z.B. ZTR, B FGr, N FGr (mit Komma trennen)">
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">
                Mehrere Tags mit Komma trennen
            </p>
        </div>

        <label class="checkbox-alpine">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $lernpool->is_active) ? 'checked' : '' }}>
            <span>Aktiv</span>
        </label>
    </div>

    <div class="modal-footer-glass">
        <button type="button" class="btn-ghost modal-close-btn">Abbrechen</button>
        <button type="submit" class="btn-primary">Speichern</button>
    </div>
</form>

<script>
function addTagToInput(tag) {
    const input = document.getElementById('edit-tags-input');
    const currentValue = input.value.trim();
    const existingTags = currentValue.split(',').map(t => t.trim()).filter(t => t);

    if (!existingTags.includes(tag)) {
        input.value = existingTags.length ? currentValue + ', ' + tag : tag;
    }
    input.focus();
}
</script>
