{{-- Practice Partial: Paar-Zuordnungsfrage (pair_matching)
     Drag-and-drop (Desktop) + Tap-to-Assign (Mobile).
     Each left_text has a slot; the right_text cards live in a pool (shuffled).
     User assigns one right card to each left slot.
     Liefert ein Hidden-Input "pair_assignments" (JSON: { [pairId]: pairId|null })
     an den Form-Submit des umgebenden <form> in practice.blade.php. Styles: resources/css/learn.css.
     inShell = true: Kopf (Meta, Fragetext) und Ergebnis rendert practice.blade.php. --}}

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


<div
    class="pair-wrap"
    x-data="pairMatchingQuestion({
        initialAssignments: @js($initialAssignments),
        isResult: @js(isset($isCorrect)),
        submitBtnId: 'submitBtn'
    })"
    x-init="init()"
>
    @unless($inShell ?? false)
        <div class="question-meta">
            <span class="question-meta-left">
                ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Wortpaare
            </span>
        </div>
        <p class="question-text">{{ $question->frage }}</p>
        @if(isset($isCorrect))
            <p class="l-small" style="font-weight: 600; color: {{ $isCorrect ? 'var(--ok)' : 'var(--bad)' }};">{{ $isCorrect ? 'Alle Paare korrekt' : 'Einige Paare passen nicht' }}</p>
        @endif
    @endunless

    {{-- Hidden field für submit-handler --}}
    <input type="hidden" name="pair_assignments" :value="JSON.stringify(assignments)">

    @unless(isset($isCorrect))
        <div class="pair-hint" role="status" aria-live="polite">
            <span x-show="selectedCardId === null">
                <span class="pair-hint-desktop">Ziehe eine Karte aus dem Pool zur passenden Zeile oder tippe Karte und Zeile.</span>
                <span class="pair-hint-touch">Tippe eine Karte und danach die passende Zeile.</span>
            </span>
            <span x-show="selectedCardId !== null" x-cloak>
                Karte ausgewählt, jetzt die passende Zeile antippen.
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
                <div class="pair-left">{{ $pair->left_text }}</div>
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
        <p class="pair-result-correct-hint">Die korrekten Paare zeigen wir hier zur Lernhilfe:</p>
        <ul class="pair-result-list">
            @foreach($pairItems as $pair)
                <li><b>{{ $pair->left_text }}</b>: {{ $pair->right_text }}</li>
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
                    return leftText + ', aktuell: ' + cardText;
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
