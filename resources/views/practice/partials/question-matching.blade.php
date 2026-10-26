{{-- Practice Partial: Zuordnungsfrage (Matching)
     Hybrid drag-and-drop (Desktop) + Tap-to-Assign (Mobile / Touch). Styles: resources/css/learn.css.
     inShell = true: Kopf (Meta, Fragetext) und Ergebnis rendert practice.blade.php.
     Liefert ein Hidden-Input "assignments" (JSON: { [itemId]: categoryId|null })
     an den Form-Submit des umgebenden <form> in practice.blade.php. --}}

@php
    $matchItems = $question->matchItems;
    $matchCategories = $question->matchCategories;

    // Initial-Zuweisungen: alles leer, es sei denn wir zeigen ein Ergebnis an.
    $initialAssignments = [];
    foreach ($matchItems as $mi) {
        $initialAssignments[$mi->id] = null;
    }

    // Falls Ergebnis-Anzeige: versuche, submittete Zuordnungen aus $answerResult zu lesen.
    $submittedAssignments = null;
    if (isset($isCorrect) && is_array($answerResult ?? null) && isset($answerResult['assignments'])) {
        $submittedAssignments = $answerResult['assignments'];
        if (is_string($submittedAssignments)) {
            $submittedAssignments = json_decode($submittedAssignments, true);
        }
        if (is_array($submittedAssignments)) {
            foreach ($submittedAssignments as $itemId => $catId) {
                $initialAssignments[(int)$itemId] = $catId === null ? null : (int)$catId;
            }
        }
    }
@endphp


<div
    class="match-wrap"
    x-data="matchingQuestion({
        initialAssignments: @js($initialAssignments),
        isResult: @js(isset($isCorrect)),
        submitBtnId: 'submitBtn'
    })"
    x-init="init()"
>
    @unless($inShell ?? false)
        <div class="question-meta">
            <span class="question-meta-left">
                ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Zuordnung
            </span>
        </div>
        <p class="question-text">{{ $question->frage }}</p>
        @if(isset($isCorrect))
            <p class="l-small" style="font-weight: 600; color: {{ $isCorrect ? 'var(--ok)' : 'var(--bad)' }};">{{ $isCorrect ? 'Alle richtig zugeordnet' : 'Zuordnung prüfen' }}</p>
        @endif
    @endunless

    {{-- Hidden field, das der Submit-Handler liest --}}
    <input type="hidden" name="assignments" :value="JSON.stringify(assignments)">

    @unless(isset($isCorrect))
        {{-- Interaktions-Hinweis --}}
        <div class="match-hint" role="status" aria-live="polite">
            <span x-show="selectedItemId === null">
                Ziehe Einträge in die passende Kategorie oder tippe auf einen Eintrag und danach auf die Kategorie.
            </span>
            <span x-show="selectedItemId !== null" x-cloak>
                Eintrag ausgewählt, jetzt Kategorie antippen.
            </span>
            <span class="match-hint-dynamic" x-text="unassignedCount + ' offen'"></span>
        </div>
    @endunless

    {{-- Unassigned pool --}}
    <div
        class="match-pool"
        @dragover.prevent="onDragOver($event, null)"
        @dragleave="onDragLeave($event)"
        @drop.prevent="onDrop($event, null)"
        :class="{ 'is-hover': dragOverCategory === 'pool' }"
        @click="onZoneClick(null)"
        :data-active-target="selectedItemId !== null ? 'true' : 'false'"
    >
        <template x-for="item in itemsInCategory(null)" :key="item.id">
            <div
                class="match-item"
                :class="{ 'is-selected': selectedItemId === item.id, 'is-dragging': draggingItemId === item.id }"
                :draggable="!isResult"
                @dragstart="onDragStart($event, item.id)"
                @dragend="onDragEnd()"
                @click.stop="onItemClick(item.id)"
                role="button"
                :aria-pressed="selectedItemId === item.id ? 'true' : 'false'"
                :aria-label="'Eintrag: ' + item.text + (selectedItemId === item.id ? ', ausgewählt' : '')"
                tabindex="0"
                @keydown.enter.prevent="onItemClick(item.id)"
                @keydown.space.prevent="onItemClick(item.id)"
            >
                <span class="match-item-text" x-text="item.text"></span>
            </div>
        </template>
        <div class="match-pool-empty" x-show="itemsInCategory(null).length === 0" x-cloak>
            Alle Einträge zugeordnet
        </div>
    </div>

    {{-- Categories --}}
    <div class="match-categories" data-count="{{ $matchCategories->count() }}">
        @foreach($matchCategories as $cat)
            <div
                class="match-category"
                data-category-id="{{ $cat->id }}"
                @dragover.prevent="onDragOver($event, {{ $cat->id }})"
                @dragleave="onDragLeave($event)"
                @drop.prevent="onDrop($event, {{ $cat->id }})"
                @click="onZoneClick({{ $cat->id }})"
                :class="{
                    'is-hover': dragOverCategory === {{ $cat->id }},
                    'is-active-target': selectedItemId !== null && !isResult
                }"
            >
                <div class="match-category-header">
                    <span>{{ $cat->name }}</span>
                    <span class="match-category-count" x-text="itemsInCategory({{ $cat->id }}).length + ' / ' + totalItems"></span>
                </div>
                <div class="match-category-items">
                    <template x-for="item in itemsInCategory({{ $cat->id }})" :key="item.id">
                        <div
                            class="match-item"
                            :class="resultClass(item)"
                            :draggable="!isResult"
                            @dragstart="onDragStart($event, item.id)"
                            @dragend="onDragEnd()"
                            @click.stop="onItemClick(item.id)"
                            role="button"
                            :aria-pressed="selectedItemId === item.id ? 'true' : 'false'"
                            :aria-label="'Eintrag: ' + item.text"
                            tabindex="0"
                            @keydown.enter.prevent="onItemClick(item.id)"
                            @keydown.space.prevent="onItemClick(item.id)"
                        >
                            <span class="match-item-text" x-text="item.text"></span>
                            <template x-if="isResult">
                                <span
                                    class="match-item-badge"
                                    :class="item.correct_category_id === assignments[item.id] ? 'match-item-badge--correct' : 'match-item-badge--wrong'"
                                    x-text="item.correct_category_id === assignments[item.id] ? 'Richtig' : 'Falsch'"
                                ></span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Falsch-zugeordnete Items aus den "richtigen" Kategorien anzeigen (Lerneffekt) --}}
    @if(isset($isCorrect) && !$isCorrect)
        <p class="match-note">Die korrekte Zuordnung siehst du an den grünen Markierungen.</p>
    @endif
</div>

<script>
    (function () {
        if (window._matchingQuestionRegistered) return;
        window._matchingQuestionRegistered = true;

        document.addEventListener('alpine:init', () => {
            Alpine.data('matchingQuestion', (config) => ({
                assignments: {},
                items: [],
                selectedItemId: null,
                draggingItemId: null,
                dragOverCategory: null,
                isTouch: ('ontouchstart' in window) || (navigator.maxTouchPoints > 0),
                isResult: !!config.isResult,
                totalItems: 0,

                init() {
                    this.assignments = { ...config.initialAssignments };
                    // Items-Array aus den DOM-Daten holen — wir erhalten sie über eine Server-Injection unten.
                    this.items = window._matchingItems_{{ $question->id }} || [];
                    this.totalItems = this.items.length;
                    this.syncSubmitState();
                    this.$watch('assignments', () => this.syncSubmitState(), { deep: true });
                },

                itemsInCategory(catId) {
                    return this.items.filter(it => (this.assignments[it.id] ?? null) === catId);
                },

                get unassignedCount() {
                    return this.items.filter(it => (this.assignments[it.id] ?? null) === null).length;
                },

                resultClass(item) {
                    if (!this.isResult) return '';
                    const assigned = this.assignments[item.id] ?? null;
                    if (assigned === item.correct_category_id) return 'match-item--correct';
                    return 'match-item--wrong';
                },

                onItemClick(itemId) {
                    if (this.isResult) return;
                    this.selectedItemId = this.selectedItemId === itemId ? null : itemId;
                },

                onZoneClick(categoryId) {
                    if (this.isResult) return;
                    if (this.selectedItemId === null) return;
                    this.assignItem(this.selectedItemId, categoryId);
                    this.selectedItemId = null;
                },

                onDragStart(event, itemId) {
                    if (this.isResult) return;
                    this.draggingItemId = itemId;
                    this.selectedItemId = null;
                    if (event.dataTransfer) {
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', String(itemId));
                    }
                },

                onDragEnd() {
                    this.draggingItemId = null;
                    this.dragOverCategory = null;
                },

                onDragOver(event, categoryId) {
                    if (this.isResult) return;
                    this.dragOverCategory = categoryId === null ? 'pool' : categoryId;
                },

                onDragLeave(event) {
                    // nur zurücksetzen, wenn wir wirklich die Zone verlassen
                    if (event.currentTarget && !event.currentTarget.contains(event.relatedTarget)) {
                        this.dragOverCategory = null;
                    }
                },

                onDrop(event, categoryId) {
                    if (this.isResult) return;
                    const itemId = parseInt(event.dataTransfer.getData('text/plain'), 10);
                    if (!isNaN(itemId)) {
                        this.assignItem(itemId, categoryId);
                    }
                    this.dragOverCategory = null;
                    this.draggingItemId = null;
                },

                assignItem(itemId, categoryId) {
                    this.assignments = { ...this.assignments, [itemId]: categoryId };
                },

                syncSubmitState() {
                    const btn = document.getElementById(config.submitBtnId);
                    if (!btn) return;
                    // Submit erlauben, wenn ALLE Items zugeordnet sind
                    const allAssigned = Object.values(this.assignments).every(v => v !== null && v !== undefined);
                    btn.disabled = !allAssigned;
                },
            }));
        });
    })();

    // Items als JSON bereitstellen, damit Alpine sie im init() lesen kann.
    window._matchingItems_{{ $question->id }} = @js(
        $matchItems->map(function ($mi) {
            return [
                'id' => $mi->id,
                'text' => $mi->text,
                'correct_category_id' => $mi->correct_category_id,
            ];
        })->values()
    );
</script>
