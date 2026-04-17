@php
    $initialOptions = [];
    if (!empty(old('options'))) {
        foreach (old('options') as $o) {
            $initialOptions[] = [
                'text' => $o['text'] ?? '',
                'is_correct' => !empty($o['is_correct']),
            ];
        }
    } elseif (isset($question) && $question && $question->options->count()) {
        foreach ($question->options as $opt) {
            $initialOptions[] = [
                'text' => $opt->text,
                'is_correct' => (bool) $opt->is_correct,
            ];
        }
    } else {
        $initialOptions = [
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
    }

    $existingImage = isset($question) && $question && $question->image_path
        ? \Illuminate\Support\Facades\Storage::url($question->image_path)
        : null;
@endphp

{{-- Frage-Bild --}}
<div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1rem;">
        <h2 class="section-title" style="font-size: 1.05rem;">Frage-Bild</h2>
    </div>

    @if($existingImage)
        <div class="hint" style="margin-bottom: 0.5rem;">Aktuelles Bild (wird bei neuem Upload ersetzt):</div>
        <div class="image-preview" style="margin-bottom: 0.75rem;">
            <img src="{{ $existingImage }}" alt="Aktuelles Frage-Bild">
        </div>
    @endif

    <div x-data="{ previewUrl: null, onPick(ev) { const f = ev.target.files[0]; this.previewUrl = f ? URL.createObjectURL(f) : null; } }">
        <div class="file-field">
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   @change="onPick($event)"
                   {{ $existingImage ? '' : 'required' }}>
        </div>
        <p class="hint">JPG, PNG oder WebP. Max. 5 MB.</p>

        <template x-if="previewUrl">
            <div class="image-preview">
                <img :src="previewUrl" alt="Vorschau">
            </div>
        </template>

        @error('image')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>

{{-- Text-Optionen --}}
<div x-data="{
    options: @js($initialOptions),
    addOption() { if (this.options.length < 6) this.options.push({ text: '', is_correct: false }); },
    removeOption(i) { if (this.options.length > 2) this.options.splice(i, 1); },
}">
    <div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                <h2 class="section-title" style="font-size: 1.05rem;">Antwort-Optionen</h2>
            </div>
            <span class="hint" x-text="`${options.length} / 6 (min. 2, mind. 1 richtig)`"></span>
        </div>

        <template x-for="(opt, i) in options" :key="i">
            <div class="dyn-row">
                <span class="dyn-index" x-text="i + 1"></span>
                <div class="dyn-body">
                    <input type="text" class="field-input"
                           :name="`options[${i}][text]`"
                           x-model="opt.text"
                           placeholder="Antwort-Text" required>
                    <label class="checkbox-inline">
                        {{-- Hidden-Value-Trick: stellt sicher, dass is_correct immer mit 0 oder 1 übermittelt wird --}}
                        <input type="hidden" :name="`options[${i}][is_correct]`" :value="opt.is_correct ? 1 : 0">
                        <input type="checkbox" x-model="opt.is_correct">
                        <span>Richtige Antwort</span>
                    </label>
                </div>
                <button type="button" class="btn-icon-remove"
                        @click="removeOption(i)"
                        :disabled="options.length <= 2"
                        title="Option entfernen">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </template>

        <button type="button" class="btn-add-row"
                @click="addOption()"
                :disabled="options.length >= 6">
            <i class="bi bi-plus-lg"></i> Option hinzufügen
        </button>
        @error('options')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>
