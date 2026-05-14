@php
    $initialOptions = [];
    if (!empty(old('options'))) {
        foreach (old('options') as $o) {
            $initialOptions[] = [
                'image_description' => $o['image_description'] ?? '',
                'image_source_hint' => $o['image_source_hint'] ?? '',
                'is_correct' => !empty($o['is_correct']),
            ];
        }
    } else {
        $initialOptions = [
            ['image_description' => '', 'image_source_hint' => '', 'is_correct' => false],
            ['image_description' => '', 'image_source_hint' => '', 'is_correct' => false],
        ];
    }
@endphp

<div x-data="{
    options: @js($initialOptions),
    addOption() {
        if (this.options.length < 6) this.options.push({ image_description: '', image_source_hint: '', is_correct: false });
    },
    removeOption(i) {
        if (this.options.length > 2) this.options.splice(i, 1);
    },
}">
    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Bild-Optionen</span>
            <span class="zf-hint" x-text="`${options.length} / 6 · mind. 1 richtig`"></span>
        </div>

        <div class="zf-alert zf-alert--info" style="margin-bottom: 0.75rem;">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                Bitte beschreibe jede Bild-Option so genau wie möglich. Admins suchen passende Bilder
                und fügen sie später hinzu.
            </div>
        </div>

        <template x-for="(opt, i) in options" :key="i">
            <div class="zf-image-tile zf-image-tile--no-thumb">
                <div class="zf-image-tile__head">
                    <span class="zf-image-tile__num" x-text="'Option ' + String.fromCharCode(65 + i)"></span>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label class="zf-correct-toggle" :class="{ 'is-correct': opt.is_correct }">
                            <input type="hidden" :name="`options[${i}][is_correct]`" :value="opt.is_correct ? 1 : 0">
                            <input type="checkbox" x-model="opt.is_correct">
                            <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                            <span x-text="opt.is_correct ? 'Richtig' : 'Falsch'"></span>
                        </label>
                        <button type="button" class="zf-option-remove"
                                @click="removeOption(i)"
                                :disabled="options.length <= 2"
                                title="Option entfernen">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="zf-image-tile__body" style="padding-top: 0.5rem;">
                    <textarea class="zf-textarea"
                              :name="`options[${i}][image_description]`"
                              x-model="opt.image_description"
                              :placeholder="`Beschreibung Bild ${String.fromCharCode(65 + i)} (Motiv, Detail, Perspektive)`"
                              required
                              maxlength="2000"
                              rows="3"></textarea>
                    <input type="text"
                           class="zf-input"
                           :name="`options[${i}][image_source_hint]`"
                           x-model="opt.image_source_hint"
                           placeholder="Quellen-Hinweis (optional)"
                           maxlength="500"
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
