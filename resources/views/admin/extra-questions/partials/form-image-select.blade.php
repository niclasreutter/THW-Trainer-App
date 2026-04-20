@php
    $initialOptions = [];
    $existingOptionImages = [];

    if (!empty(old('options'))) {
        foreach (old('options') as $o) {
            $initialOptions[] = [
                'is_correct' => !empty($o['is_correct']),
            ];
        }
    } elseif (isset($question) && $question && $question->options->count()) {
        foreach ($question->options as $opt) {
            $initialOptions[] = [
                'is_correct' => (bool) $opt->is_correct,
            ];
            $existingOptionImages[] = $opt->image_path
                ? \Illuminate\Support\Facades\Storage::url($opt->image_path)
                : null;
        }
    } else {
        $initialOptions = [
            ['is_correct' => false],
            ['is_correct' => false],
        ];
    }

    $isEdit = isset($question) && $question && $question->exists;
@endphp

<div x-data="{
    options: @js($initialOptions),
    previews: {},
    onPick(i, ev) {
        const f = ev.target.files[0];
        this.previews[i] = f ? URL.createObjectURL(f) : null;
    },
    addOption() { if (this.options.length < 6) this.options.push({ is_correct: false }); },
    removeOption(i) {
        if (this.options.length > 2) {
            this.options.splice(i, 1);
            // Preview-Keys nachziehen
            const np = {};
            this.options.forEach((_, idx) => {
                if (this.previews[idx]) np[idx] = this.previews[idx];
            });
            this.previews = np;
        }
    },
}">
    <div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                <h2 class="section-title" style="font-size: 1.05rem;">Bild-Optionen</h2>
            </div>
            <span class="hint" x-text="`${options.length} / 6 (min. 2, mind. 1 richtig)`"></span>
        </div>

        @if($isEdit)
            <div class="glass-warning" style="padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.85rem;">
                <strong>Hinweis:</strong> Beim Bearbeiten einer „Bild auswählen"-Frage müssen alle Bilder neu hochgeladen werden.
                Die bisherigen Bilder werden zur Orientierung angezeigt.
            </div>
        @endif

        <template x-for="(opt, i) in options" :key="i">
            <div class="dyn-row">
                <span class="dyn-index" x-text="i + 1"></span>
                <div class="dyn-body">
                    <div class="file-field">
                        <input type="file"
                               :name="`options[${i}][image]`"
                               accept="image/jpeg,image/png,image/webp"
                               @change="onPick(i, $event)"
                               required>
                    </div>

                    @if($isEdit)
                        <template x-if="!previews[i]">
                            <div>
                                @foreach($existingOptionImages as $idx => $imgUrl)
                                    <template x-if="i === {{ $idx }}">
                                        <div>
                                            @if($imgUrl)
                                                <div class="hint">Bisheriges Bild:</div>
                                                <div class="image-preview">
                                                    <img src="{{ $imgUrl }}" alt="Bisheriges Option-Bild">
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                @endforeach
                            </div>
                        </template>
                    @endif

                    <template x-if="previews[i]">
                        <div>
                            <div class="hint">Neue Vorschau:</div>
                            <div class="image-preview">
                                <img :src="previews[i]" alt="Vorschau">
                            </div>
                        </div>
                    </template>

                    <label class="checkbox-inline">
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
            <i class="bi bi-plus-lg"></i> Bild-Option hinzufügen
        </button>
        <p class="hint">JPG, PNG oder WebP. Max. 5 MB pro Bild.</p>
        @error('options')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>
