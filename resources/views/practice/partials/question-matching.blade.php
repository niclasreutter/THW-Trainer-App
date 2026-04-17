{{-- Practice Partial: Zuordnungsfrage (Matching)
     Hybrid drag-and-drop (Desktop) + Tap-to-Assign (Mobile / Touch).
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

<style>
    .match-wrap { display: flex; flex-direction: column; gap: 1rem; }
    .match-hint {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 0.875rem;
        border-radius: 0.625rem;
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.25);
        color: var(--text-primary);
        font-size: 0.8125rem;
        line-height: 1.35;
    }
    .match-hint .bi { color: var(--gold-start); font-size: 1rem; flex-shrink: 0; }
    .match-hint-dynamic {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.75rem;
        color: var(--gold-start);
        font-weight: 600;
        margin-left: auto;
    }

    .match-pool {
        display: flex; flex-wrap: wrap; gap: 0.5rem;
        padding: 0.875rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed rgba(255, 255, 255, 0.12);
        min-height: 3.5rem;
        align-content: flex-start;
    }
    html.light-mode .match-pool {
        background: #f8fafc;
        border-color: rgba(0, 51, 127, 0.15);
    }
    .match-pool-empty {
        width: 100%;
        text-align: center;
        font-size: 0.8125rem;
        color: var(--text-muted);
        padding: 0.5rem;
        font-style: italic;
    }

    .match-categories {
        display: grid; gap: 0.75rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
        .match-categories { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 900px) {
        .match-categories[data-count="3"],
        .match-categories[data-count="5"] { grid-template-columns: repeat(3, 1fr); }
        .match-categories[data-count="4"] { grid-template-columns: repeat(2, 1fr); }
    }

    .match-category {
        display: flex; flex-direction: column; gap: 0.5rem;
        padding: 0.875rem;
        border-radius: 0.875rem;
        background: rgba(255, 255, 255, 0.03);
        border: 2px solid rgba(255, 255, 255, 0.08);
        transition: border-color 0.18s ease, background 0.18s ease, transform 0.18s ease;
        min-height: 5rem;
    }
    html.light-mode .match-category {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.12);
    }
    .match-category.is-hover {
        border-color: var(--gold-start);
        background: rgba(251, 191, 36, 0.06);
        transform: translateY(-1px);
    }
    .match-category.is-active-target {
        border-color: var(--gold-start);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.15);
        cursor: pointer;
    }
    .match-category.is-active-target::after {
        content: 'Hier ablegen';
        font-size: 0.6875rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--gold-start);
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        background: rgba(251, 191, 36, 0.15);
        align-self: flex-start;
    }

    .match-category-header {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        color: var(--text-primary);
        padding-bottom: 0.375rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    html.light-mode .match-category-header {
        border-bottom-color: rgba(0, 51, 127, 0.1);
    }
    .match-category-header .bi { color: var(--gold-start); font-size: 0.875rem; }
    .match-category-count {
        margin-left: auto;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .match-category-items {
        display: flex; flex-direction: column; gap: 0.375rem;
        flex: 1;
    }

    .match-item {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 0.75rem;
        background: rgba(255, 255, 255, 0.04);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.625rem;
        cursor: grab;
        user-select: none;
        font-size: 0.875rem;
        color: var(--text-primary);
        transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        min-height: 44px;
    }
    html.light-mode .match-item {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.14);
    }
    .match-item:hover {
        border-color: rgba(0, 85, 204, 0.4);
        background: rgba(0, 77, 179, 0.06);
    }
    .match-item:active { cursor: grabbing; transform: scale(0.98); }
    .match-item.is-selected {
        border-color: var(--gold-start);
        background: rgba(251, 191, 36, 0.12);
        box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.25);
    }
    .match-item.is-dragging { opacity: 0.45; }
    .match-item-handle { color: var(--text-muted); font-size: 1rem; flex-shrink: 0; }
    .match-item-text { flex: 1; line-height: 1.3; }

    /* Result States */
    .match-item--correct {
        background: rgba(34, 197, 94, 0.12) !important;
        border-color: rgba(34, 197, 94, 0.45) !important;
        cursor: default;
    }
    .match-item--wrong {
        background: rgba(239, 68, 68, 0.12) !important;
        border-color: rgba(239, 68, 68, 0.45) !important;
        cursor: default;
    }
    .match-item--missed {
        background: rgba(239, 68, 68, 0.06) !important;
        border-color: rgba(239, 68, 68, 0.3) !important;
        cursor: default;
        opacity: 0.85;
    }
    .match-item-badge {
        margin-left: auto;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        flex-shrink: 0;
    }
    .match-item-badge--correct { background: rgba(34, 197, 94, 0.18); color: #22c55e; }
    .match-item-badge--wrong { background: rgba(239, 68, 68, 0.18); color: #ef4444; }

    @media (prefers-reduced-motion: reduce) {
        .match-category, .match-item { transition: none !important; }
    }
</style>

<div
    class="match-wrap"
    x-data="matchingQuestion({
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
            ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Zuordnung
        </span>
    </div>

    <p class="question-text">{{ $question->frage }}</p>

    {{-- Ergebnis-Banner (wie MC) --}}
    @if(isset($isCorrect))
        <div class="result-banner {{ $isCorrect ? 'result-banner--correct' : 'result-banner--wrong' }}">
            <div class="result-banner-left">
                <span class="result-banner-icon">
                    <i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color:{{ $isCorrect ? '#22c55e' : '#ef4444' }};"></i>
                </span>
                <span class="result-banner-text">{{ $isCorrect ? 'Alle richtig zugeordnet' : 'Zuordnung prüfen' }}</span>
            </div>
        </div>
    @endif

    {{-- Hidden field, das der Submit-Handler liest --}}
    <input type="hidden" name="assignments" :value="JSON.stringify(assignments)">

    @unless(isset($isCorrect))
        {{-- Interaktions-Hinweis --}}
        <div class="match-hint" role="status" aria-live="polite">
            <i class="bi bi-lightbulb"></i>
            <span x-show="selectedItemId === null">
                Ziehe Einträge in die passende Kategorie – oder tippe auf einen Eintrag und danach auf die Kategorie.
            </span>
            <span x-show="selectedItemId !== null" x-cloak>
                Eintrag ausgewählt – jetzt Kategorie antippen.
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
                <i class="bi bi-grip-vertical match-item-handle" aria-hidden="true"></i>
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
                    <i class="bi bi-folder2" aria-hidden="true"></i>
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
                            <i class="bi bi-grip-vertical match-item-handle" aria-hidden="true" x-show="!isResult"></i>
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
        <div style="margin-top:0.5rem;font-size:0.75rem;color:var(--text-muted);font-family:'IBM Plex Mono',monospace;">
            Die korrekte Zuordnung siehst du an den grünen Markierungen.
        </div>
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
