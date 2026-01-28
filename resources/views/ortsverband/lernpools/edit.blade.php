<!-- Modal Format (für AJAX) -->
<div class="modal-header-glass">
    <h2>{{ $lernpool->name }} bearbeiten</h2>
    <button class="modal-close-btn" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">&times;</button>
</div>
<form action="{{ route('ortsverband.lernpools.update', [$ortsverband, $lernpool]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="modal-body-glass">
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="name" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                Name <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $lernpool->name) }}"
                   class="input-glass" required>
            @error('name')
                <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="description" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                Beschreibung <span style="color: #ef4444;">*</span>
            </label>
            <textarea name="description" id="description" rows="4" class="textarea-glass" required>{{ old('description', $lernpool->description) }}</textarea>
            @error('description')
                <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="checkbox-glass" style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $lernpool->is_active) ? 'checked' : '' }}>
                <span style="font-weight: 600; color: var(--text-primary);">Aktiv</span>
            </label>
        </div>
    </div>

    <div class="modal-footer-glass">
        <button type="button" class="btn-ghost" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="submit" class="btn-primary">
            Speichern
        </button>
    </div>
</form>
