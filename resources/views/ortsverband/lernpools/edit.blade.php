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
            <div class="form-checkbox">
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
