{{--
    Member search dropdown for OV pages.

    Required vars:
      $members – Illuminate\Support\Collection of arrays with keys:
                 id, name, avatar (url), role, url (manage page)
--}}
@once
@push('styles')
<style>
    .ov-member-search {
        position: relative;
        width: 280px;
        max-width: 100%;
    }
    .ov-member-search__input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .ov-member-search__icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.875rem;
        line-height: 1;
        color: var(--text-muted);
        pointer-events: none;
    }
    input.ov-member-search__input {
        width: 100%;
        padding: 0.5rem 2.25rem 0.5rem 2.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        color: var(--text-primary);
        font-size: 0.8125rem;
        transition: border-color 150ms, background 150ms;
    }
    html.light-mode input.ov-member-search__input {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.08);
    }
    input.ov-member-search__input:focus {
        outline: none;
        border-color: rgba(91,154,255,0.5);
        background: rgba(255,255,255,0.06);
    }
    html.light-mode input.ov-member-search__input:focus {
        background: rgba(0,0,0,0.04);
    }
    input.ov-member-search__input::placeholder {
        color: var(--text-muted);
    }
    .ov-member-search__clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0.25rem;
        font-size: 0.875rem;
        line-height: 1;
        border-radius: 0.25rem;
    }
    .ov-member-search__clear:hover { color: var(--text-primary); }
    .ov-member-search__dropdown {
        position: absolute;
        top: calc(100% + 0.375rem);
        left: 0;
        right: 0;
        background: var(--bg-elevated, rgba(20,24,32,0.98));
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        box-shadow: 0 12px 32px rgba(0,0,0,0.4);
        max-height: 320px;
        overflow-y: auto;
        z-index: 50;
    }
    html.light-mode .ov-member-search__dropdown {
        background: #ffffff;
        border-color: rgba(0,0,0,0.1);
        box-shadow: 0 12px 32px rgba(0,0,0,0.15);
    }
    .ov-member-search__item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.75rem;
        text-decoration: none;
        color: var(--text-primary);
        transition: background 150ms;
        cursor: pointer;
    }
    .ov-member-search__item + .ov-member-search__item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }
    html.light-mode .ov-member-search__item + .ov-member-search__item {
        border-top-color: rgba(0,0,0,0.06);
    }
    .ov-member-search__item:hover,
    .ov-member-search__item.is-active {
        background: rgba(91,154,255,0.08);
        text-decoration: none;
    }
    .ov-member-search__item img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .ov-member-search__item-name {
        font-size: 0.8125rem;
        font-weight: 600;
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ov-member-search__item-role {
        font-size: 0.5625rem;
        padding: 0.1rem 0.35rem;
        border-radius: 2rem;
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        white-space: nowrap;
    }
    .ov-member-search__empty {
        padding: 0.75rem;
        text-align: center;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ovMemberSearch', (members) => ({
            members,
            query: '',
            open: false,
            activeIndex: 0,
            get filtered() {
                const q = this.query.trim().toLowerCase();
                if (!q) return [];
                const terms = q.split(/\s+/).filter(Boolean);
                return this.members
                    .filter(m => {
                        const name = m.name.toLowerCase();
                        return terms.every(t => name.includes(t));
                    })
                    .slice(0, 12);
            },
            move(delta) {
                this.open = true;
                const len = this.filtered.length;
                if (len === 0) return;
                this.activeIndex = (this.activeIndex + delta + len) % len;
            },
            selectActive() {
                const m = this.filtered[this.activeIndex];
                if (m) window.location.href = m.url;
            },
            clear() {
                this.query = '';
                this.activeIndex = 0;
                this.open = false;
            },
            close() {
                this.open = false;
            },
        }));
    });
</script>
@endpush
@endonce

<div
    class="ov-member-search"
    x-data="ovMemberSearch({{ $members->toJson() }})"
    @keydown.escape.window="close()"
    @click.outside="close()"
>
    <div class="ov-member-search__input-wrap">
        <i class="bi bi-search ov-member-search__icon"></i>
        <input
            type="text"
            class="ov-member-search__input"
            placeholder="Mitglied suchen…"
            x-model="query"
            @input="open = true; activeIndex = 0"
            @focus="open = true"
            @keydown.arrow-down.prevent="move(1)"
            @keydown.arrow-up.prevent="move(-1)"
            @keydown.enter.prevent="selectActive()"
            autocomplete="off"
            spellcheck="false"
        >
        <button
            type="button"
            class="ov-member-search__clear"
            x-show="query.length > 0"
            @click="clear()"
            aria-label="Suche zurücksetzen"
        >&times;</button>
    </div>

    <div class="ov-member-search__dropdown" x-show="open && query.length > 0" x-cloak>
        <template x-if="filtered.length === 0">
            <div class="ov-member-search__empty">Keine Mitglieder gefunden</div>
        </template>
        <template x-for="(m, i) in filtered" :key="m.id">
            <a
                :href="m.url"
                class="ov-member-search__item"
                :class="{ 'is-active': i === activeIndex }"
                @mouseenter="activeIndex = i"
            >
                <img :src="m.avatar" alt="">
                <span class="ov-member-search__item-name" x-text="m.name"></span>
                <template x-if="m.role === 'ausbildungsbeauftragter'">
                    <span class="ov-member-search__item-role">Ausbilder</span>
                </template>
            </a>
        </template>
    </div>
</div>
