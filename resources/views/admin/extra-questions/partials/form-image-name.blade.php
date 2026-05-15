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
<div class="zf-form-card" x-data="{
    previewUrl: null,
    hasFile: false,
    onPick(ev) {
        const f = ev.target.files[0];
        this.previewUrl = f ? URL.createObjectURL(f) : null;
        this.hasFile = !!f;
    }
}">
    <div class="zf-form-card__label">
        <span class="zf-section-label">Frage-Bild</span>
    </div>

    @if($existingImage)
        <div class="zf-help" style="margin-bottom: 0.5rem;">Aktuelles Bild (wird bei neuem Upload ersetzt):</div>
        <div class="zf-image-preview" style="margin-bottom: 0.75rem;">
            <img src="{{ $existingImage }}" alt="Aktuelles Frage-Bild">
        </div>
    @endif

    <label class="zf-upload" style="display: block; position: relative;">
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
               @change="onPick($event)"
               {{ $existingImage ? '' : 'required' }}>
        <div class="zf-upload__icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
        <div class="zf-upload__title" x-text="hasFile ? 'Anderes Bild wählen' : 'Bild hochladen'">Bild hochladen</div>
        <div class="zf-upload__sub">JPG, PNG oder WebP · max. 5 MB</div>
    </label>

    <template x-if="previewUrl">
        <div class="zf-image-preview">
            <img :src="previewUrl" alt="Vorschau">
        </div>
    </template>

    @error('image')<span class="zf-field-error">{{ $message }}</span>@enderror

    <div class="zf-field" style="margin-top: 0.75rem;">
        <label class="zf-label" for="image_source">
            Bildquelle <span class="zf-req">*</span>
        </label>
        <input id="image_source" type="text" name="image_source" class="zf-input"
               value="{{ old('image_source', $question->image_source ?? '') }}"
               placeholder="z.B. THW / Fotograf / CC-BY 4.0" required>
        <span class="zf-help">Urheber oder Quelle des Bildes – wird unter der Frage angezeigt.</span>
        @error('image_source')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>
</div>

{{-- Text-Optionen --}}
<div x-data="{
    options: @js($initialOptions),
    addOption() { if (this.options.length < 6) this.options.push({ text: '', is_correct: false }); },
    removeOption(i) { if (this.options.length > 2) this.options.splice(i, 1); },
}">
    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Antwort-Optionen</span>
            <span class="zf-hint" x-text="`${options.length} / 6 · mind. 1 richtig`"></span>
        </div>

        <template x-for="(opt, i) in options" :key="i">
            <div class="zf-option-row" :class="{ 'is-correct': opt.is_correct }">
                <div class="zf-option-num" x-text="String.fromCharCode(65 + i)"></div>
                <div class="zf-option-body">
                    <input type="text" class="zf-input"
                           :name="`options[${i}][text]`"
                           x-model="opt.text"
                           :placeholder="`Antwort ${i + 1}`" required>
                    <label class="zf-correct-toggle" :class="{ 'is-correct': opt.is_correct }">
                        <input type="hidden" :name="`options[${i}][is_correct]`" :value="opt.is_correct ? 1 : 0">
                        <input type="checkbox" x-model="opt.is_correct">
                        <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                        <span x-text="opt.is_correct ? 'Richtig' : 'Falsch'"></span>
                    </label>
                </div>
                <button type="button" class="zf-option-remove"
                        @click="removeOption(i)"
                        :disabled="options.length <= 2"
                        title="Option entfernen">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </template>

        <div class="zf-add-row">
            <button type="button" class="zf-add-btn"
                    @click="addOption()"
                    :disabled="options.length >= 6">
                <i class="bi bi-plus-lg"></i> Option hinzufügen
            </button>
        </div>
        @error('options')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>
</div>
