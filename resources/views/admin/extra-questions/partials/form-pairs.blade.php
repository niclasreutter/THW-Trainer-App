@once
    @push('styles')
    <style>
        .zf-pair--inline {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 0.5rem;
            align-items: center;
        }
        .zf-pair--inline .zf-pair__head {
            grid-column: 1 / -1;
        }
        .zf-pair--inline .zf-pair__arrow {
            padding: 0 0.25rem;
            display: grid;
            place-items: center;
        }
        .zf-pair--inline .zf-pair__arrow .bi {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.625rem;
            height: 1.625rem;
            border-radius: 999px;
            background: var(--thw-blue, #00337f);
            color: #fff;
            font-size: 0.8125rem;
            line-height: 1;
            transition: transform 0.2s ease;
        }
        html:not(.light-mode) .zf-pair--inline .zf-pair__arrow .bi { background: #2563eb; }

        @media (max-width: 540px) {
            .zf-pair--inline {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .zf-pair--inline .zf-pair__arrow {
                padding: 0;
                margin: -0.5rem 0;
                z-index: 1;
                position: relative;
                pointer-events: none;
            }
            .zf-pair--inline .zf-pair__arrow .bi {
                transform: rotate(90deg);
            }
            /* Mehr Luft zwischen den einzelnen Paaren auf Mobile */
            .zf-pair { margin-bottom: 1.25rem; }
            .zf-pair:last-child { margin-bottom: 0; }
        }
    </style>
    @endpush
@endonce

@php
    $initialPairs = [];

    if (!empty(old('pairs'))) {
        foreach (old('pairs') as $p) {
            $initialPairs[] = [
                'left_text' => $p['left_text'] ?? '',
                'right_text' => $p['right_text'] ?? '',
            ];
        }
    } elseif (isset($question) && $question && $question->pairItems->count()) {
        foreach ($question->pairItems as $pair) {
            $initialPairs[] = [
                'left_text' => $pair->left_text,
                'right_text' => $pair->right_text,
            ];
        }
    } else {
        $initialPairs = [
            ['left_text' => '', 'right_text' => ''],
            ['left_text' => '', 'right_text' => ''],
        ];
    }
@endphp

<div x-data="{
    pairs: @js($initialPairs),
    addPair() { if (this.pairs.length < 6) this.pairs.push({ left_text: '', right_text: '' }); },
    removePair(i) { if (this.pairs.length > 2) this.pairs.splice(i, 1); },
}">
    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Wortpaare</span>
            <span class="zf-hint" x-text="`${pairs.length} / 6 · min. 2`"></span>
        </div>

        <p class="zf-help" style="margin: 0 0 0.75rem;">
            Je Paar einen Begriff links und den dazu passenden rechts eintragen.
            Im Spiel werden die rechten Begriffe zufällig gemischt; der Nutzer muss die richtigen
            Paare wieder zusammensetzen.
        </p>

        <template x-for="(pair, i) in pairs" :key="i">
            <div class="zf-pair zf-pair--inline">
                <div class="zf-pair__head">
                    <span x-text="`Paar ${i + 1}`"></span>
                    <button type="button" class="zf-option-remove"
                            @click="removePair(i)"
                            :disabled="pairs.length <= 2"
                            title="Paar entfernen">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <input type="text" class="zf-input"
                       :name="`pairs[${i}][left_text]`"
                       x-model="pair.left_text"
                       placeholder="Linke Seite (z.B. 'Mastwurf')" required>
                <div class="zf-pair__arrow" aria-hidden="true">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <input type="text" class="zf-input"
                       :name="`pairs[${i}][right_text]`"
                       x-model="pair.right_text"
                       placeholder="Rechte Seite (z.B. 'Anschlag an einen Pfosten')" required>
            </div>
        </template>

        <div class="zf-add-row">
            <button type="button" class="zf-add-btn"
                    @click="addPair()"
                    :disabled="pairs.length >= 6">
                <i class="bi bi-plus-lg"></i> Paar hinzufügen
            </button>
        </div>
        @error('pairs')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>
</div>
