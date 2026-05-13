@extends('layouts.app')

@section('title', 'Avatar Editor - THW Trainer')
@section('description', 'Passe deinen Avatar nach deinen Wünschen an.')

@push('styles')
<style>
    .ae-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 860px) {
        .ae-layout { grid-template-columns: 1fr; }
    }

    .ae-preview {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.875rem;
        position: sticky;
        top: 1rem;
    }
    @media (max-width: 860px) {
        .ae-preview { position: static; }
    }

    .ae-preview__ring {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        padding: 4px;
        background: conic-gradient(var(--thw-blue) 100%, rgba(0,51,127,0.12) 0);
        display: grid;
        place-items: center;
    }
    html:not(.light-mode) .ae-preview__ring {
        background: conic-gradient(var(--thw-blue-glow, #5b9aff) 100%, rgba(91,154,255,0.15) 0);
    }
    .ae-preview__inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: var(--bg-elevated);
        overflow: hidden;
        display: grid;
        place-items: center;
    }
    html:not(.light-mode) .ae-preview__inner { background: #1a1a1d; }
    .ae-preview__inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .ae-preview__actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
    }

    .ae-preview__status {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        min-height: 1rem;
        text-align: center;
    }
    .ae-preview__status--saved { color: #22c55e; }

    .ae-panel {
        padding: 1.25rem;
    }

    .ae-tabs {
        display: flex;
        gap: 0.375rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        scrollbar-width: thin;
    }
    html.light-mode .ae-tabs { border-bottom-color: rgba(0,51,127,0.1); }

    .ae-tab {
        padding: 0.4rem 0.875rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--glass-border, rgba(255,255,255,0.08));
        background: transparent;
        color: var(--text-secondary);
        transition: all 150ms ease;
        font-family: inherit;
        white-space: nowrap;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    .ae-tab:hover {
        background: rgba(255,255,255,0.04);
        color: var(--text-primary);
    }
    html.light-mode .ae-tab:hover { background: rgba(0,51,127,0.04); }

    .ae-tab.is-active {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
        border-color: rgba(0,85,204,0.3);
    }
    html.light-mode .ae-tab.is-active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.25);
    }

    .ae-tab__lock {
        font-size: 0.625rem;
        color: var(--text-muted);
    }

    .ae-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
        gap: 0.625rem;
    }

    .ae-thumb {
        position: relative;
        background: rgba(255,255,255,0.03);
        border: 2px solid rgba(255,255,255,0.06);
        border-radius: 0.75rem;
        padding: 0.5rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.375rem;
        transition: all 150ms ease;
        font-family: inherit;
        color: var(--text-secondary);
    }
    html.light-mode .ae-thumb {
        background: #fff;
        border-color: rgba(0,51,127,0.08);
    }
    .ae-thumb:hover {
        border-color: rgba(91,154,255,0.4);
        transform: translateY(-1px);
    }
    html.light-mode .ae-thumb:hover {
        border-color: rgba(0,51,127,0.3);
    }

    .ae-thumb img {
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 0.5rem;
        background: rgba(0,0,0,0.1);
    }

    .ae-thumb__label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.1;
        word-break: break-word;
    }

    .ae-thumb.is-selected {
        border-color: #5b9aff;
        background: rgba(91,154,255,0.08);
        color: #5b9aff;
        box-shadow: 0 0 0 3px rgba(91,154,255,0.15);
    }
    html.light-mode .ae-thumb.is-selected {
        border-color: #00337F;
        background: rgba(0,51,127,0.05);
        color: #00337F;
        box-shadow: 0 0 0 3px rgba(0,51,127,0.1);
    }

    .ae-thumb.is-locked {
        opacity: 0.5;
        filter: grayscale(0.6);
    }
    .ae-thumb.is-locked:hover {
        opacity: 0.75;
        filter: grayscale(0.3);
    }

    .ae-thumb__lock {
        position: absolute;
        top: 0.375rem;
        right: 0.375rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(0,0,0,0.65);
        color: #fbbf24;
        display: grid;
        place-items: center;
        font-size: 0.625rem;
        backdrop-filter: blur(4px);
    }

    .ae-thumb__price {
        position: absolute;
        bottom: 0.375rem;
        left: 50%;
        transform: translateX(-50%);
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 700;
        color: #fbbf24;
        background: rgba(0,0,0,0.65);
        padding: 0.125rem 0.4rem;
        border-radius: 999px;
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }

    .ae-colors {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.06);
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    html.light-mode .ae-colors { border-top-color: rgba(0,51,127,0.1); }

    .ae-colors__label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-right: 0.5rem;
    }

    .ae-color-swatch {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 150ms ease;
        padding: 0;
    }
    .ae-color-swatch:hover { transform: scale(1.15); }
    .ae-color-swatch.is-selected {
        border-color: var(--text-primary);
        transform: scale(1.15);
    }

    .ae-hint {
        margin-top: 1rem;
        padding: 0.75rem 0.875rem;
        background: rgba(251,191,36,0.08);
        border: 1px solid rgba(251,191,36,0.25);
        border-radius: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .ae-hint i { color: #fbbf24; font-size: 0.875rem; }

    .ae-empty {
        padding: 2rem 1rem;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.8125rem;
    }
    .ae-empty a { color: #5b9aff; font-weight: 600; text-decoration: none; }
    .ae-empty a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="dash-container">
    <div class="space-y-4">
        <header class="dashboard-header" style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; flex-wrap:wrap;">
            <div>
                <h1 class="page-title">Avatar <span>Editor</span></h1>
                <p class="page-subtitle">Wähle dein Aussehen aus. Gesperrte Items findest du im Shop.</p>
            </div>
            <a href="{{ route('profile') }}" class="btn-ghost">
                <i class="bi bi-arrow-left"></i> Zurück zum Profil
            </a>
        </header>

        <div class="ae-layout" x-data="avatarEditor()" x-init="init()">
            <aside class="glass ae-preview">
                <div class="ae-preview__ring">
                    <div class="ae-preview__inner">
                        <img :src="previewUrl" alt="Avatar Vorschau">
                    </div>
                </div>

                <div class="ae-preview__status"
                     :class="{'ae-preview__status--saved': saved}"
                     x-text="saving ? 'Speichert…' : (saved ? 'Gespeichert ✓' : '')">
                </div>

                <div class="ae-preview__actions">
                    <button @click="randomize" type="button" class="btn-secondary" :disabled="randomizing">
                        <i class="bi bi-shuffle"></i>
                        <span x-text="randomizing ? 'Würfelt…' : 'Zufalls-Avatar'"></span>
                    </button>
                </div>
            </aside>

            <section class="glass ae-panel">
                <nav class="ae-tabs">
                    <template x-for="cat in categories" :key="cat.key">
                        <button type="button"
                                class="ae-tab"
                                :class="{'is-active': activeTab === cat.key}"
                                @click="activeTab = cat.key">
                            <span x-text="cat.label"></span>
                            <i x-show="cat.shop && hasLockedItems(cat)" class="bi bi-lock-fill ae-tab__lock"></i>
                        </button>
                    </template>
                </nav>

                <template x-if="currentCategory">
                    <div>
                        <template x-if="currentCategory.shop && currentCategory.options.length === 0">
                            <div class="ae-empty">
                                Keine Items in dieser Kategorie verfügbar.
                            </div>
                        </template>

                        <div class="ae-grid" x-show="currentCategory.options.length > 0">
                            <template x-for="opt in currentCategory.options" :key="opt.value + (opt.slug || '')">
                                <button type="button"
                                        class="ae-thumb"
                                        :class="{
                                            'is-selected': isSelected(opt),
                                            'is-locked': opt.locked,
                                        }"
                                        @click="select(opt)"
                                        :title="opt.locked ? opt.label + ' – im Shop kaufen' : opt.label">
                                    <img :src="opt.previewUrl" :alt="opt.label" loading="lazy">
                                    <span class="ae-thumb__label" x-text="opt.label"></span>
                                    <span x-show="opt.locked && opt.price" class="ae-thumb__price">
                                        <i class="bi bi-coin"></i> <span x-text="opt.price"></span>
                                    </span>
                                    <span x-show="opt.locked" class="ae-thumb__lock">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                </button>
                            </template>
                        </div>

                        <div x-show="showColorPicker" class="ae-colors">
                            <span class="ae-colors__label">Farbe</span>
                            <template x-for="c in currentColors" :key="c">
                                <button type="button"
                                        class="ae-color-swatch"
                                        :style="`background:#${c}`"
                                        :class="{'is-selected': isColorSelected(c)}"
                                        @click="selectColor(c)"
                                        :title="'#' + c"></button>
                            </template>
                        </div>

                        <div x-show="showShopHint" class="ae-hint">
                            <i class="bi bi-info-circle"></i>
                            <span>Tipp: Gesperrte Items kannst du im <a href="{{ route('shop.index') }}" style="color:#5b9aff; font-weight:600;">Shop</a> kaufen.</span>
                        </div>

                        <div x-show="showHairHiddenHint" class="ae-hint" style="background:rgba(91,154,255,0.08); border-color:rgba(91,154,255,0.25);">
                            <i class="bi bi-info-circle" style="color:#5b9aff;"></i>
                            <span>Dein aktiver Hut überdeckt die Frisur. Deaktiviere den Hut, um deine Frisur zu sehen.</span>
                        </div>
                    </div>
                </template>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.avatarEditorInitial = @js($initialState);

    function avatarEditor() {
        return {
            // State
            categories: window.avatarEditorInitial.categories,
            params: { ...window.avatarEditorInitial.currentParams },
            activeAccessories: { ...(window.avatarEditorInitial.activeAccessories || {}) },
            baseSeed: window.avatarEditorInitial.seed,
            activeTab: 'top',
            saving: false,
            saved: false,
            randomizing: false,
            _saveTimer: null,
            _savedTimer: null,

            baseUrl: 'https://dicebear.niclas-reutter.de/9.x/avataaars/svg',

            init() {
                // Default-Tab auf erste Kategorie setzen
                if (this.categories.length) this.activeTab = this.categories[0].key;
            },

            get currentCategory() {
                return this.categories.find(c => c.key === this.activeTab) || null;
            },

            get currentColors() {
                if (!this.currentCategory) return [];
                if (this.currentCategory.shop) {
                    // Bei Shop-Tabs: Farben des aktuell ausgewählten Items
                    const activeVal = this.activeAccessories[this.currentCategory.param];
                    const opt = this.currentCategory.options.find(o => o.value === activeVal);
                    return (opt && !opt.locked) ? (opt.colors || []) : [];
                }
                // Bei Color-Kategorien: alle Optionswerte als Farben
                if (this.currentCategory.type === 'color') {
                    return this.currentCategory.options.map(o => o.value);
                }
                return [];
            },

            get showColorPicker() {
                return this.currentColors.length > 0;
            },

            get showShopHint() {
                return !!this.currentCategory?.shop;
            },

            get showHairHiddenHint() {
                // Beim Frisur-Tab anzeigen, wenn ein Hut aktiv ist
                return this.activeTab === 'top' && !!this.activeAccessories.top;
            },

            get previewUrl() {
                const p = new URLSearchParams();
                p.set('radius', '50');
                p.set('seed', this.baseSeed);
                p.set('backgroundType', 'gradientLinear');
                p.set('backgroundColor', '00337f,0055cc');
                p.set('backgroundRotation', '135');
                p.set('accessoriesProbability', '0');
                p.set('facialHairProbability', '0');

                // Free-Choice-Parameter
                for (const [k, v] of Object.entries(this.params)) {
                    if (v === null || v === undefined || v === '') continue;
                    p.set(k, String(v));
                }

                // Active Shop-Accessoires (siehe User.php avatarUrl Accessor)
                const a = this.activeAccessories;
                if (a.accessories) {
                    p.set('accessories', a.accessories);
                    p.set('accessoriesProbability', '100');
                    if (a.accessoriesColor) p.set('accessoriesColor', a.accessoriesColor);
                }
                if (a.top) {
                    p.set('top', a.top);
                }
                if (a.facialHair) {
                    p.set('facialHair', a.facialHair);
                    p.set('facialHairProbability', '100');
                    if (a.facialHairColor) p.set('facialHairColor', a.facialHairColor);
                }

                return this.baseUrl + '?' + p.toString();
            },

            hasLockedItems(cat) {
                return cat.options.some(o => o.locked);
            },

            isSelected(opt) {
                if (!this.currentCategory) return false;
                if (this.currentCategory.shop) {
                    return this.activeAccessories[this.currentCategory.param] === opt.value;
                }
                return this.params[this.currentCategory.param] === opt.value;
            },

            isColorSelected(color) {
                if (!this.currentCategory) return false;
                if (this.currentCategory.shop) {
                    const colorKey = this.colorKeyFor(this.currentCategory.param);
                    return colorKey && this.activeAccessories[colorKey] === color;
                }
                return this.params[this.currentCategory.param] === color;
            },

            colorKeyFor(param) {
                return {
                    accessories: 'accessoriesColor',
                    facialHair:  'facialHairColor',
                }[param] || null;
            },

            select(opt) {
                if (opt.locked) {
                    window.location = '{{ route('shop.index') }}#' + opt.slug;
                    return;
                }

                if (this.currentCategory.shop) {
                    this.toggleShopItem(opt);
                    return;
                }

                // Free-Choice → debounced save
                this.params[this.currentCategory.param] = opt.value;
                this.queueSave();
            },

            selectColor(color) {
                if (!this.currentCategory) return;

                if (this.currentCategory.shop) {
                    const activeVal = this.activeAccessories[this.currentCategory.param];
                    const opt = this.currentCategory.options.find(o => o.value === activeVal);
                    if (!opt || opt.locked) return;
                    this.updateShopColor(opt, color);
                    return;
                }

                this.params[this.currentCategory.param] = color;
                this.queueSave();
            },

            queueSave() {
                if (this._saveTimer) clearTimeout(this._saveTimer);
                this._saveTimer = setTimeout(() => this.save(), 300);
            },

            async save() {
                this.saving = true;
                this.saved = false;
                try {
                    const r = await fetch('{{ route('profile.avatar.update') }}', {
                        method: 'POST',
                        headers: this.headers(),
                        body: JSON.stringify({ params: this.params }),
                    });
                    if (!r.ok) throw new Error('save failed');
                    this.flashSaved();
                } catch (e) {
                    console.error(e);
                } finally {
                    this.saving = false;
                }
            },

            async toggleShopItem(opt) {
                this.saving = true;
                try {
                    const r = await fetch('{{ route('profile.accessory.toggle') }}', {
                        method: 'POST',
                        headers: this.headers(),
                        body: JSON.stringify({ accessory_slug: opt.slug }),
                    });
                    const d = await r.json();
                    if (d.success) {
                        this.activeAccessories = d.active_accessories && typeof d.active_accessories === 'object'
                            ? { ...d.active_accessories }
                            : {};
                        this.flashSaved();
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.saving = false;
                }
            },

            async updateShopColor(opt, color) {
                this.saving = true;
                try {
                    const r = await fetch('{{ route('profile.accessory.color') }}', {
                        method: 'POST',
                        headers: this.headers(),
                        body: JSON.stringify({ accessory_slug: opt.slug, color }),
                    });
                    const d = await r.json();
                    if (d.success) {
                        this.activeAccessories = d.active_accessories && typeof d.active_accessories === 'object'
                            ? { ...d.active_accessories }
                            : {};
                        this.flashSaved();
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.saving = false;
                }
            },

            async randomize() {
                this.randomizing = true;
                this.saving = true;
                try {
                    const r = await fetch('{{ route('profile.avatar.regenerate') }}', {
                        method: 'POST',
                        headers: this.headers(),
                    });
                    const d = await r.json();
                    if (d.avatar_url) {
                        const url = new URL(d.avatar_url);
                        const newParams = {};
                        url.searchParams.forEach((v, k) => { newParams[k] = v; });
                        // Seed übernehmen, free params überschreiben
                        if (newParams.seed) this.baseSeed = newParams.seed;
                        this.params = newParams;
                        this.flashSaved();
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.randomizing = false;
                    this.saving = false;
                }
            },

            flashSaved() {
                this.saved = true;
                if (this._savedTimer) clearTimeout(this._savedTimer);
                this._savedTimer = setTimeout(() => { this.saved = false; }, 1800);
            },

            headers() {
                return {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                };
            },
        };
    }
</script>
@endpush
