{{-- Practice Partial: Paar-Zuordnungsfrage (pair_matching)
     Drag-and-drop (Desktop) + Tap-to-Assign (Mobile).
     Each left_text has a slot; the right_text cards live in a pool (shuffled).
     User assigns one right card to each left slot.
     Liefert ein Hidden-Input "pair_assignments" (JSON: { [pairId]: pairId|null })
     an den Form-Submit des umgebenden <form> in practice.blade.php. --}}

@php
    $pairItems = $question->pairItems;

    // Initial: alle Slots leer.
    $initialAssignments = [];
    foreach ($pairItems as $pair) {
        $initialAssignments[$pair->id] = null;
    }

    // Falls Ergebnis-Anzeige: submittete Zuordnungen aus $answerResult lesen.
    if (isset($isCorrect) && is_array($answerResult ?? null) && isset($answerResult['pair_assignments'])) {
        $submitted = $answerResult['pair_assignments'];
        if (is_string($submitted)) {
            $submitted = json_decode($submitted, true);
        }
        if (is_array($submitted)) {
            foreach ($submitted as $leftId => $rightId) {
                $initialAssignments[(int) $leftId] = $rightId === null ? null : (int) $rightId;
            }
        }
    }

    // Rechte Karten: gemischt, damit der User nicht raten kann.
    // Im Result-Modus liefern wir sie unsortiert (Alpine zeigt nur unzugewiesene).
    $rightCards = $pairItems
        ->map(fn ($p) => ['id' => $p->id, 'right_text' => $p->right_text])
        ->shuffle()
        ->values();
@endphp

<style>
    .pair-wrap { display: flex; flex-direction: column; gap: 1rem; }

    .pair-hint {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 0.875rem;
        border-radius: 0.625rem;
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.25);
        color: var(--text-primary);
        font-size: 0.8125rem;
        line-height: 1.35;
    }
    .pair-hint .bi { color: var(--gold-start); font-size: 1rem; flex-shrink: 0; }
    .pair-hint-dynamic {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.75rem;
        color: var(--gold-start);
        font-weight: 600;
        margin-left: auto;
    }
    .pair-hint-touch { display: none; }
    @media (hover: none) and (pointer: coarse) {
        .pair-hint-desktop { display: none; }
        .pair-hint-touch { display: inline; }
    }

    .pair-pool {
        display: flex; flex-wrap: wrap; gap: 0.5rem;
        padding: 0.875rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed rgba(255, 255, 255, 0.12);
        min-height: 3.5rem;
        align-content: flex-start;
    }
    html.light-mode .pair-pool {
        background: #f8fafc;
        border-color: rgba(0, 51, 127, 0.15);
    }
    .pair-pool.is-hover {
        border-color: var(--gold-start);
        background: rgba(251, 191, 36, 0.06);
    }
    .pair-pool-empty {
        width: 100%;
        text-align: center;
        font-size: 0.8125rem;
        color: var(--text-muted);
        padding: 0.5rem;
        font-style: italic;
    }
    .pair-pool-title {
        width: 100%;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .pair-rows {
        display: flex; flex-direction: column; gap: 0.625rem;
    }

    .pair-row {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 0.5rem;
        align-items: stretch;
    }
    @media (max-width: 540px) {
        /* Mobile: enge Verbindung links <-> Slot, dafür mehr Luft zwischen den Paaren. */
        .pair-rows { gap: 1.25rem; }
        .pair-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        .pair-row__arrow {
            padding: 0;
            margin: -0.5rem 0;
            z-index: 1;
            position: relative;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pair-row__arrow .bi {
            transform: rotate(90deg);
        }
    }

    .pair-left {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.75rem 0.875rem;
        background: rgba(0, 77, 179, 0.06);
        border: 1.5px solid rgba(0, 85, 204, 0.18);
        border-radius: 0.625rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        min-height: 44px;
    }
    html:not(.light-mode) .pair-left {
        background: rgba(91, 154, 255, 0.08);
        border-color: rgba(91, 154, 255, 0.22);
    }
    .pair-left .bi { color: var(--thw-blue); flex-shrink: 0; }
    html:not(.light-mode) .pair-left .bi { color: #5b9aff; }

    .pair-row__arrow {
        display: grid; place-items: center;
        padding: 0 0.25rem;
    }
    .pair-row__arrow .bi {
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
    html:not(.light-mode) .pair-row__arrow .bi { background: #2563eb; }

    .pair-slot {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border: 2px dashed rgba(255, 255, 255, 0.14);
        border-radius: 0.625rem;
        font-size: 0.875rem;
        color: var(--text-muted);
        min-height: 44px;
        transition: border-color 0.18s ease, background 0.18s ease, transform 0.18s ease;
        cursor: pointer;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    html.light-mode .pair-slot {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.16);
    }
    .pair-slot.is-hover {
        border-color: var(--gold-start);
        background: rgba(251, 191, 36, 0.06);
        transform: translateY(-1px);
    }
    .pair-slot.is-active-target {
        border-color: var(--gold-start);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.15);
    }
    .pair-slot.is-filled {
        border-style: solid;
        border-color: rgba(255, 255, 255, 0.18);
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        cursor: grab;
    }
    html.light-mode .pair-slot.is-filled {
        border-color: rgba(0, 51, 127, 0.18);
        background: #ffffff;
    }
    .pair-slot.is-filled:active { cursor: grabbing; }
    .pair-slot.is-dragging { opacity: 0.45; }

    .pair-slot-text { flex: 1; line-height: 1.3; }
    .pair-slot-placeholder { flex: 1; opacity: 0.7; }

    .pair-card {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: rgba(255, 255, 255, 0.04);
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        border-radius: 0.5rem;
        cursor: grab;
        user-select: none;
        font-size: 0.875rem;
        color: var(--text-primary);
        transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        min-height: 40px;
    }
    html.light-mode .pair-card {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.14);
    }
    .pair-card:hover {
        border-color: rgba(0, 85, 204, 0.4);
        background: rgba(0, 77, 179, 0.06);
    }
    .pair-card:active { cursor: grabbing; transform: scale(0.98); }
    .pair-card.is-selected {
        border-color: var(--gold-start);
        background: rgba(251, 191, 36, 0.12);
        box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.25);
    }
    .pair-card.is-dragging { opacity: 0.45; }
    .pair-card-handle { color: var(--text-muted); font-size: 0.95rem; flex-shrink: 0; }

    /* Result states */
    .pair-slot--correct {
        border-style: solid !important;
        background: rgba(34, 197, 94, 0.12) !important;
        border-color: rgba(34, 197, 94, 0.45) !important;
        color: var(--text-primary);
        cursor: default;
    }
    .pair-slot--wrong {
        border-style: solid !important;
        background: rgba(239, 68, 68, 0.12) !important;
        border-color: rgba(239, 68, 68, 0.45) !important;
        color: var(--text-primary);
        cursor: default;
    }
    .pair-slot-badge {
        margin-left: auto;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        flex-shrink: 0;
    }
    .pair-slot-badge--correct { background: rgba(34, 197, 94, 0.18); color: #22c55e; }
    .pair-slot-badge--wrong { background: rgba(239, 68, 68, 0.18); color: #ef4444; }

    .pair-result-correct-hint {
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    @media (prefers-reduced-motion: reduce) {
        .pair-slot, .pair-card { transition: none !important; }
    }
</style>

<div
    class="pair-wrap"
    x-data="pairMatchingQuestion({
        initialAssignments: @js($initialAssignments),
        isResult: @js(isset($isCorrect)),
        submitBtnId: 'submitBtn'
    })"
    x-init="init()"
>
    {{-- Mobile Badges --}}
    <div class="practice-mobile-badges">
        @if(isset($isSpacedRepetition) && $isSpacedRepetition)
            <span class="pm-badge pm-badge--sr"><i class="bi bi-arrow-repeat"></i> SR</span>
        @endif
        @if(isset($difficultyInfo) && $difficultyInfo['level'] !== 'unknown')
            <span class="pm-badge pm-badge--{{ $difficultyInfo['level'] }}">{{ $difficultyInfo['label'] }}</span>
        @endif
    </div>

    <div class="question-meta">
        <span class="question-meta-left">
            ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Wortpaare
        </span>
    </div>

    <p class="question-text">{{ $question->frage }}</p>

    {{-- Ergebnis-Banner --}}
    @if(isset($isCorrect))
        <div class="result-banner {{ $isCorrect ? 'result-banner--correct' : 'result-banner--wrong' }}">
            <div class="result-banner-left">
                <span class="result-banner-icon">
                    <i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color:{{ $isCorrect ? '#22c55e' : '#ef4444' }};"></i>
                </span>
                <span class="result-banner-text">{{ $isCorrect ? 'Alle Paare korrekt' : 'Einige Paare passen nicht' }}</span>
            </div>
        </div>
    @endif

    {{-- Hidden field für submit-handler --}}
    <input type="hidden" name="pair_assignments" :value="JSON.stringify(assignments)">

    @unless(isset($isCorrect))
        <div class="pair-hint" role="status" aria-live="polite">
            <i class="bi bi-lightbulb"></i>
            <span x-show="selectedCardId === null">
                <span class="pair-hint-desktop">Ziehe eine Karte aus dem Pool zur passenden Zeile – oder tippe Karte + Zeile.</span>
                <span class="pair-hint-touch">Tippe eine Karte und danach die passende Zeile.</span>
            </span>
            <span x-show="selectedCardId !== null" x-cloak>
                Karte ausgewählt – jetzt die passende Zeile antippen.
            </span>
            <span class="pair-hint-dynamic" x-text="unassignedCount + ' offen'"></span>
        </div>
    @endunless

    {{-- Pool: ungenutzte rechte Karten --}}
    @unless(isset($isCorrect))
    <div
        class="pair-pool"
        @dragover.prevent="onDragOver($event, 'pool')"
        @dragleave="onDragLeave($event)"
        @drop.prevent="onDropToPool($event)"
        :class="{ 'is-hover': dragOverTarget === 'pool' }"
    >
        <div class="pair-pool-title">Rechte Begriffe</div>
        <template x-for="card in unassignedCards" :key="card.id">
            <div
                class="pair-card"
                :class="{ 'is-selected': selectedCardId === card.id, 'is-dragging': draggingCardId === card.id }"
                :draggable="!isResult"
                @dragstart="onDragStart($event, card.id)"
                @dragend="onDragEnd()"
                @click.stop="onCardClick(card.id)"
                role="button"
                :aria-pressed="selectedCardId === card.id ? 'true' : 'false'"
                :aria-label="'Begriff: ' + card.right_text"
                tabindex="0"
                @keydown.enter.prevent="onCardClick(card.id)"
                @keydown.space.prevent="onCardClick(card.id)"
            >
                <i class="bi bi-grip-vertical pair-card-handle" aria-hidden="true"></i>
                <span x-text="card.right_text"></span>
            </div>
        </template>
        <div class="pair-pool-empty" x-show="unassignedCards.length === 0" x-cloak>
            Alle Begriffe zugeordnet
        </div>
    </div>
    @endunless

    {{-- Pair rows --}}
    <div class="pair-rows">
        @foreach($pairItems as $pair)
            <div class="pair-row">
                <div class="pair-left">
                    <i class="bi bi-link-45deg" aria-hidden="true"></i>
                    <span class="pair-slot-text">{{ $pair->left_text }}</span>
                </div>
                <div class="pair-row__arrow" aria-hidden="true">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div
                    class="pair-slot"
                    :class="slotClass({{ $pair->id }})"
                    @dragover.prevent="onDragOver($event, {{ $pair->id }})"
                    @dragleave="onDragLeave($event)"
                    @drop.prevent="onDropToSlot($event, {{ $pair->id }})"
                    @click="onSlotClick({{ $pair->id }})"
                    @keydown.enter.prevent="onSlotClick({{ $pair->id }})"
                    @keydown.space.prevent="onSlotClick({{ $pair->id }})"
                    tabindex="0"
                    role="button"
                    :aria-label="slotAriaLabel({{ $pair->id }}, {{ Js::from($pair->left_text) }})"
                    :draggable="!isResult && assignments[{{ $pair->id }}] !== null"
                    @dragstart="onSlotDragStart($event, {{ $pair->id }})"
                    @dragend="onDragEnd()"
                >
                    <template x-if="assignments[{{ $pair->id }}] !== null">
                        <span class="pair-slot-text" x-text="cardTextById(assignments[{{ $pair->id }}])"></span>
                    </template>
                    <template x-if="assignments[{{ $pair->id }}] === null">
                        <span class="pair-slot-placeholder">Hier ablegen…</span>
                    </template>

                    <template x-if="isResult">
                        <span
                            class="pair-slot-badge"
                            :class="assignments[{{ $pair->id }}] === {{ $pair->id }} ? 'pair-slot-badge--correct' : 'pair-slot-badge--wrong'"
                            x-text="assignments[{{ $pair->id }}] === {{ $pair->id }} ? 'Richtig' : 'Falsch'"
                        ></span>
                    </template>
                </div>
            </div>
        @endforeach
    </div>

    @if(isset($isCorrect) && !$isCorrect)
        <div class="pair-result-correct-hint">
            Die korrekten Paare zeigen wir hier zur Lernhilfe:
        </div>
        <ul style="margin: 0.35rem 0 0; padding-left: 1.25rem; font-size: 0.8125rem; line-height: 1.5;">
            @foreach($pairItems as $pair)
                <li><strong>{{ $pair->left_text }}</strong> ↔ {{ $pair->right_text }}</li>
            @endforeach
        </ul>
    @endif
</div>

<script>
    (function () {
        if (window._pairMatchingQuestionRegistered) return;
        window._pairMatchingQuestionRegistered = true;

        document.addEventListener('alpine:init', () => {
            Alpine.data('pairMatchingQuestion', (config) => ({
                assignments: {},
                cards: [],
                selectedCardId: null,
                draggingCardId: null,
                dragOverTarget: null,
                isResult: !!config.isResult,

                init() {
                    this.assignments = { ...config.initialAssignments };
                    this.cards = window._pairMatchingCards_{{ $context ?? 'q' }}_{{ $question->id }} || [];
                    this.syncSubmitState();
                    this.$watch('assignments', () => this.syncSubmitState(), { deep: true });
                },

                cardById(id) {
                    return this.cards.find(c => c.id === id) || null;
                },

                cardTextById(id) {
                    const c = this.cardById(id);
                    return c ? c.right_text : '';
                },

                get unassignedCards() {
                    const assignedIds = new Set(
                        Object.values(this.assignments).filter(v => v !== null && v !== undefined)
                    );
                    return this.cards.filter(c => !assignedIds.has(c.id));
                },

                get unassignedCount() {
                    return Object.values(this.assignments).filter(v => v === null || v === undefined).length;
                },

                slotAriaLabel(slotId, leftText) {
                    const assigned = this.assignments[slotId];
                    const cardText = assigned !== null && assigned !== undefined
                        ? this.cardTextById(assigned)
                        : 'leer';
                    return leftText + ' – aktuell: ' + cardText;
                },

                slotClass(slotId) {
                    const classes = {};
                    const assigned = this.assignments[slotId] ?? null;
                    if (assigned !== null) classes['is-filled'] = true;
                    if (this.draggingCardId !== null && this.dragOverTarget === slotId) classes['is-hover'] = true;
                    if (this.selectedCardId !== null && !this.isResult && assigned === null) classes['is-active-target'] = true;

                    if (this.isResult) {
                        if (assigned === slotId) classes['pair-slot--correct'] = true;
                        else classes['pair-slot--wrong'] = true;
                    }
                    return classes;
                },

                onCardClick(cardId) {
                    if (this.isResult) return;
                    this.selectedCardId = this.selectedCardId === cardId ? null : cardId;
                },

                onSlotClick(slotId) {
                    if (this.isResult) return;

                    // If we have a selected card from the pool, place it.
                    if (this.selectedCardId !== null) {
                        this.assignCardToSlot(this.selectedCardId, slotId);
                        this.selectedCardId = null;
                        return;
                    }

                    // Otherwise: clicking a filled slot moves its card back to the pool.
                    const currentlyAssigned = this.assignments[slotId] ?? null;
                    if (currentlyAssigned !== null) {
                        this.assignments = { ...this.assignments, [slotId]: null };
                    }
                },

                onDragStart(event, cardId) {
                    if (this.isResult) return;
                    this.draggingCardId = cardId;
                    this.selectedCardId = null;
                    if (event.dataTransfer) {
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', String(cardId));
                    }
                },

                onSlotDragStart(event, slotId) {
                    if (this.isResult) return;
                    const cardId = this.assignments[slotId] ?? null;
                    if (cardId === null) {
                        event.preventDefault();
                        return;
                    }
                    // Detach from slot first so the drop target sees it as free.
                    this.assignments = { ...this.assignments, [slotId]: null };
                    this.onDragStart(event, cardId);
                },

                onDragEnd() {
                    this.draggingCardId = null;
                    this.dragOverTarget = null;
                },

                onDragOver(event, target) {
                    if (this.isResult) return;
                    this.dragOverTarget = target;
                },

                onDragLeave(event) {
                    if (event.currentTarget && !event.currentTarget.contains(event.relatedTarget)) {
                        this.dragOverTarget = null;
                    }
                },

                onDropToSlot(event, slotId) {
                    if (this.isResult) return;
                    const cardId = parseInt(event.dataTransfer.getData('text/plain'), 10);
                    if (!isNaN(cardId)) {
                        this.assignCardToSlot(cardId, slotId);
                    }
                    this.dragOverTarget = null;
                    this.draggingCardId = null;
                },

                onDropToPool(event) {
                    if (this.isResult) return;
                    const cardId = parseInt(event.dataTransfer.getData('text/plain'), 10);
                    if (!isNaN(cardId)) {
                        // Free this card from any slot it was in.
                        const update = { ...this.assignments };
                        for (const [k, v] of Object.entries(update)) {
                            if (v === cardId) update[k] = null;
                        }
                        this.assignments = update;
                    }
                    this.dragOverTarget = null;
                    this.draggingCardId = null;
                },

                assignCardToSlot(cardId, slotId) {
                    const update = { ...this.assignments };

                    // If card was in another slot, free it.
                    for (const [k, v] of Object.entries(update)) {
                        if (v === cardId) update[k] = null;
                    }

                    // If target slot already had a card, that card returns to the pool (i.e., set to null).
                    update[slotId] = cardId;
                    this.assignments = update;
                },

                syncSubmitState() {
                    const btn = document.getElementById(config.submitBtnId);
                    if (!btn) return;
                    const allAssigned = Object.values(this.assignments).every(v => v !== null && v !== undefined);
                    btn.disabled = !allAssigned;
                },
            }));
        });
    })();

    // Rechte Karten als JSON für Alpine bereitstellen (gemischt im Backend, stabile Reihenfolge im Frontend).
    window._pairMatchingCards_{{ $context ?? 'q' }}_{{ $question->id }} = @js($rightCards);
</script>
