@php
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
    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Kategorien</span>
            <span class="zf-hint" x-text="`${categories.length} / 5 · min. 2`"></span>
        </div>

        <template x-for="(cat, i) in categories" :key="i">
            <div class="zf-option-row">
                <div class="zf-option-num" x-text="i + 1"></div>
                <div class="zf-option-body">
                    <input type="text" class="zf-input"
                           :name="`categories[${i}][name]`"
                           x-model="cat.name"
                           :placeholder="`Kategorie ${i + 1}`" required>
                </div>
                <button type="button" class="zf-option-remove"
                        @click="removeCategory(i)"
                        :disabled="categories.length <= 2"
                        title="Kategorie entfernen">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </template>

        <div class="zf-add-row">
            <button type="button" class="zf-add-btn"
                    @click="addCategory()"
                    :disabled="categories.length >= 5">
                <i class="bi bi-plus-lg"></i> Kategorie hinzufügen
            </button>
        </div>
        @error('categories')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Items --}}
    <div class="zf-form-card">
        <div class="zf-form-card__label">
            <span class="zf-section-label">Items &amp; Zuordnung</span>
            <span class="zf-hint" x-text="`${items.length} / 10 · min. 3`"></span>
        </div>

        <template x-for="(it, i) in items" :key="i">
            <div class="zf-pair">
                <div class="zf-pair__head">
                    <span x-text="`Item ${i + 1}`"></span>
                    <button type="button" class="zf-option-remove"
                            @click="removeItem(i)"
                            :disabled="items.length <= 3"
                            title="Item entfernen">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <input type="text" class="zf-input"
                       :name="`items[${i}][text]`"
                       x-model="it.text"
                       placeholder="Item-Text (z.B. 'Mastwurf')" required>
                <div class="zf-pair__arrow"><i class="bi bi-arrow-down"></i></div>
                <select class="zf-select" :name="`items[${i}][category_index]`" x-model.number="it.category_index" required>
                    <template x-for="(cat, ci) in categories" :key="ci">
                        <option :value="ci" x-text="(ci + 1) + '. ' + (cat.name || 'Kategorie ' + (ci + 1))"></option>
                    </template>
                </select>
            </div>
        </template>

        <div class="zf-add-row">
            <button type="button" class="zf-add-btn"
                    @click="addItem()"
                    :disabled="items.length >= 10">
                <i class="bi bi-plus-lg"></i> Item hinzufügen
            </button>
        </div>
        @error('items')<span class="zf-field-error">{{ $message }}</span>@enderror
    </div>
</div>
