@php
    $initialOptions = [];
    $existingImageUrls = [];

    if (!empty(old('options'))) {
        foreach (old('options') as $o) {
            $initialOptions[] = [
                'is_correct' => !empty($o['is_correct']),
                'image_source' => $o['image_source'] ?? '',
            ];
        }
    } elseif (isset($question) && $question && $question->options->count()) {
        foreach ($question->options as $opt) {
            $initialOptions[] = [
                'is_correct' => (bool) $opt->is_correct,
                'image_source' => (string) ($opt->image_source ?? ''),
            ];
            $existingImageUrls[] = $opt->image_path
                ? \Illuminate\Support\Facades\Storage::url($opt->image_path)
                : null;
        }
    } else {
        $initialOptions = [
            ['is_correct' => false, 'image_source' => ''],
            ['is_correct' => false, 'image_source' => ''],
        ];
    }

    $isEdit = isset($question) && $question && $question->exists;
@endphp

<div x-data="{
    options: @js($initialOptions),
    existingImages: @js($existingImageUrls),
    previews: {},
    onPick(i, ev) {
        const f = ev.target.files[0];
        this.previews[i] = f ? URL.createObjectURL(f) : null;
    },
    addOption() {
        if (this.options.length < 6) this.options.push({ is_correct: false, image_source: '' });
    },
    removeOption(i) {
        if (this.options.length > 2) {
            this.options.splice(i, 1);
            const np = {};
            this.options.forEach((_, idx) => {
                if (this.previews[idx]) np[idx] = this.previews[idx];
            });
            this.previews = np;
        }
    },
    displayImage(i) {
        if (this.previews[i]) return this.previews[i];
        if (this.existingImages[i]) return this.existingImages[i];
        return null;
    },
}">
    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Bild-Optionen</span>
            <span class="zf-hint" x-text="`${options.length} / 6 · mind. 1 richtig`"></span>
        </div>

        @if($isEdit)
            <div class="zf-alert zf-alert--info">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <strong>Hinweis:</strong>
                    Beim Bearbeiten einer „Bild auswählen"-Frage müssen alle Bilder neu hochgeladen werden.
                    Die bisherigen Bilder werden zur Orientierung angezeigt.
                </div>
            </div>
        @endif

        <template x-for="(opt, i) in options" :key="i">
            <div class="zf-image-tile">
                <div class="zf-image-tile__head">
                    <span class="zf-image-tile__num" x-text="'Option ' + String.fromCharCode(65 + i)"></span>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label class="zf-correct-toggle" :class="{ 'is-correct': opt.is_correct }">
                            <input type="hidden" :name="`options[${i}][is_correct]`" :value="opt.is_correct ? 1 : 0">
                            <input type="checkbox" x-model="opt.is_correct">
                            <span>Richtig</span>
                        </label>
                        <button type="button" class="zf-option-remove"
                                @click="removeOption(i)"
                                :disabled="options.length <= 2"
                                title="Option entfernen">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="zf-image-tile__thumb">
                    <template x-if="displayImage(i)">
                        <img :src="displayImage(i)" alt="Bild-Vorschau">
                    </template>
                    <template x-if="!displayImage(i)">
                        <i class="bi bi-cloud-arrow-up" style="font-size: 1.25rem;"></i>
                    </template>
                </div>

                <div class="zf-image-tile__body">
                    <label class="zf-upload" style="display: block; position: relative; padding: 0.75rem;">
                        <input type="file"
                               :name="`options[${i}][image]`"
                               accept="image/jpeg,image/png,image/webp"
                               @change="onPick(i, $event)"
                               required>
                        <div class="zf-upload__title" style="margin-bottom: 0.15rem;">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <span x-text="previews[i] ? 'Anderes Bild wählen' : 'Bild wählen'">Bild wählen</span>
                        </div>
                        <div class="zf-upload__sub">JPG, PNG oder WebP · max. 5 MB</div>
                    </label>
                    <input type="text"
                           class="zf-input"
                           :name="`options[${i}][image_source]`"
                           x-model="opt.image_source"
                           placeholder="Bildquelle (z.B. THW / Fotograf)"
                           required
                           style="margin-top: 0.5rem;">
                </div>
            </div>
        </template>

        <div class="zf-add-row">
            <button type="button" class="zf-add-btn"
                    @click="addOption()"
                    :disabled="options.length >= 6">
                <i class="bi bi-plus-lg"></i> Bild-Option hinzufügen
            </button>
        </div>
        @error('options')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>
</div>
