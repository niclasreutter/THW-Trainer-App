@php
    $initialOptions = [];
    if (!empty(old('options'))) {
        foreach (old('options') as $o) {
            $initialOptions[] = [
                'text' => $o['text'] ?? '',
                'is_correct' => !empty($o['is_correct']),
            ];
        }
    } else {
        $initialOptions = [
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
    }
@endphp

<div class="zf-form-card">
    <div class="zf-form-card__label">
        <span class="zf-section-label">Bild-Beschreibung</span>
    </div>

    <div class="zf-alert zf-alert--info" style="margin-bottom: 0.75rem;">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            Beschreibe das gewünschte Bild so genau wie möglich (Motiv, Detail, Perspektive).
            Ein Admin sucht ein passendes Bild aus und ergänzt es später.
        </div>
    </div>

    <div class="zf-field">
        <label class="zf-label" for="image_description">
            Beschreibung des Bildes <span class="zf-req">*</span>
        </label>
        <textarea id="image_description" name="image_description" class="zf-textarea" required maxlength="2000"
                  placeholder="z.B. Nahaufnahme eines Mastwurfs an einem Holzpfahl, von vorne fotografiert">{{ old('image_description') }}</textarea>
        @error('image_description')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="zf-field" style="margin-top: 0.75rem;">
        <label class="zf-label" for="image_source_hint">
            Bildquellen-Hinweis (optional)
        </label>
        <input id="image_source_hint" type="text" name="image_source_hint" class="zf-input" maxlength="500"
               value="{{ old('image_source_hint') }}"
               placeholder="z.B. THW-Webseite, Lehrbuch Seite 42, eigene Idee">
        <span class="zf-help">Optional – hilft dem Admin bei der Bildauswahl.</span>
        @error('image_source_hint')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>
</div>

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
