<!-- Modal Format (für AJAX) -->
<div class="modal-header">
    <h2>{{ $lernpool->name }} bearbeiten</h2>
    <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
</div>
<form action="{{ route('ortsverband.lernpools.update', [$ortsverband, $lernpool]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="form-group">
            <label for="name" class="form-label">
                Name <span class="required">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $lernpool->name) }}" 
                   class="form-input @error('name') error @enderror" required>
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">
                Beschreibung <span class="required">*</span>
            </label>
            <textarea name="description" id="description" rows="5" class="form-textarea @error('description') error @enderror" required>{{ old('description', $lernpool->description) }}</textarea>
            @error('description')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="tags" class="form-label">
                Schlagwörter / Tags
            </label>
            @if($existingTags->isNotEmpty())
                <div style="margin-bottom: 0.75rem;">
                    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem;">
                        Vorhandene Tags (zum Hinzufügen anklicken):
                    </p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        @foreach($existingTags as $tag)
                            <button type="button" onclick="addTagToModal('{{ $tag }}')" class="tag-suggestion-modal" style="background: #e0f2fe; color: #0c4a6e; padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; border: 1px solid #7dd3fc; cursor: pointer; transition: all 0.2s;">
                                + {{ $tag }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
            <input type="text" name="tags" id="tags" value="{{ old('tags', is_array($lernpool->tags) ? implode(', ', $lernpool->tags) : '') }}"
                   class="form-input @error('tags') error @enderror"
                   placeholder="z.B. ZTR, B FGr, N FGr (mit Komma trennen)">
            <p style="font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem;">
                Mehrere Tags mit Komma trennen (z.B. "ZTR, B FGr, N FGr")
            </p>
            @error('tags')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <style>
            .tag-suggestion-modal:hover {
                background: #0ea5e9 !important;
                color: white !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
            }
        </style>

        <div class="form-group">
            <div class="form-checkbox">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $lernpool->is_active) ? 'checked' : '' }}>
                <label for="is_active">Aktiv</label>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="submit" class="btn btn-primary">
            ✓ Speichern
        </button>
    </div>
</form>
