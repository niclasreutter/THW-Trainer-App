@php
    // Vorbereitung initial-Daten (für Alpine)
    $initialCategories = [];
    $initialItems = [];

    if (!empty(old('categories'))) {
        foreach (old('categories') as $c) {
            $initialCategories[] = ['name' => $c['name'] ?? ''];
        }
    } elseif (isset($question) && $question && $question->matchCategories->count()) {
        foreach ($question->matchCategories as $cat) {
            $initialCategories[] = ['name' => $cat->name];
        }
    } else {
        $initialCategories = [['name' => ''], ['name' => '']];
    }

    if (!empty(old('items'))) {
        foreach (old('items') as $it) {
            $initialItems[] = [
                'text' => $it['text'] ?? '',
                'category_index' => isset($it['category_index']) ? (int) $it['category_index'] : 0,
            ];
        }
    } elseif (isset($question) && $question && $question->matchItems->count()) {
        // Mapping: correct_category_id -> index in matchCategories (sort_order)
        $catIdToIndex = [];
        foreach ($question->matchCategories as $i => $cat) {
            $catIdToIndex[$cat->id] = $i;
        }
        foreach ($question->matchItems as $item) {
            $initialItems[] = [
                'text' => $item->text,
                'category_index' => $catIdToIndex[$item->correct_category_id] ?? 0,
            ];
        }
    } else {
        $initialItems = [
            ['text' => '', 'category_index' => 0],
            ['text' => '', 'category_index' => 0],
            ['text' => '', 'category_index' => 0],
        ];
    }
@endphp

<div x-data="{
    categories: @js($initialCategories),
    items: @js($initialItems),
    addCategory() { if (this.categories.length < 5) this.categories.push({ name: '' }); },
    removeCategory(i) {
        if (this.categories.length <= 2) return;
        this.categories.splice(i, 1);
        // Items, die auf entfernte oder spätere Kategorie verweisen, anpassen
        this.items = this.items.map(it => {
            let idx = parseInt(it.category_index);
            if (idx === i) idx = 0;
            else if (idx > i) idx = idx - 1;
            return { ...it, category_index: idx };
        });
    },
    addItem() { if (this.items.length < 10) this.items.push({ text: '', category_index: 0 }); },
    removeItem(i) { if (this.items.length > 3) this.items.splice(i, 1); },
}">
    {{-- Kategorien --}}
    <div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                <h2 class="section-title" style="font-size: 1.05rem;">Kategorien</h2>
            </div>
            <span class="hint" x-text="`${categories.length} / 5 (min. 2)`"></span>
        </div>

        <template x-for="(cat, i) in categories" :key="i">
            <div class="dyn-row">
                <span class="dyn-index" x-text="i + 1"></span>
                <div class="dyn-body">
                    <input type="text" class="field-input"
                           :name="`categories[${i}][name]`"
                           x-model="cat.name"
                           :placeholder="`Kategorie ${i + 1}`" required>
                </div>
                <button type="button" class="btn-icon-remove"
                        @click="removeCategory(i)"
                        :disabled="categories.length <= 2"
                        title="Kategorie entfernen">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </template>

        <button type="button" class="btn-add-row"
                @click="addCategory()"
                :disabled="categories.length >= 5">
            <i class="bi bi-plus-lg"></i> Kategorie hinzufügen
        </button>
        @error('categories')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Items --}}
    <div class="glass" style="padding: 1.5rem; margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                <h2 class="section-title" style="font-size: 1.05rem;">Items &amp; Zuordnung</h2>
            </div>
            <span class="hint" x-text="`${items.length} / 10 (min. 3)`"></span>
        </div>

        <template x-for="(it, i) in items" :key="i">
            <div class="dyn-row">
                <span class="dyn-index" x-text="i + 1"></span>
                <div class="dyn-body">
                    <input type="text" class="field-input"
                           :name="`items[${i}][text]`"
                           x-model="it.text"
                           placeholder="Item-Text (z.B. 'Personalführung')" required>
                    <div>
                        <label class="field-label" style="margin-bottom: 0.25rem;">Gehört zu:</label>
                        <select class="field-input" :name="`items[${i}][category_index]`" x-model.number="it.category_index" required>
                            <template x-for="(cat, ci) in categories" :key="ci">
                                <option :value="ci" x-text="(ci + 1) + '. ' + (cat.name || 'Kategorie ' + (ci + 1))"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn-icon-remove"
                        @click="removeItem(i)"
                        :disabled="items.length <= 3"
                        title="Item entfernen">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </template>

        <button type="button" class="btn-add-row"
                @click="addItem()"
                :disabled="items.length >= 10">
            <i class="bi bi-plus-lg"></i> Item hinzufügen
        </button>
        @error('items')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>
