@extends('layouts.app')
@section('title', 'Shop - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Lootbox Nudge ─── */
    .lootbox-nudge {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(251, 191, 36, 0.2);
        background: rgba(251, 191, 36, 0.06);
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .lootbox-nudge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.12);
        text-decoration: none;
    }
    .lootbox-nudge__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #1a1a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: lootbox-shimmer 3s ease-in-out infinite;
    }
    @keyframes lootbox-shimmer {
        0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); }
        50% { box-shadow: 0 0 12px 2px rgba(251, 191, 36, 0.3); }
    }
    .lootbox-nudge__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #fbbf24;
    }
    html.light-mode .lootbox-nudge__title { color: #d97706; }
    .lootbox-nudge__desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Header (Dashboard-pattern) ─── */
    .shop-greeting {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .shop-title {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
        font-family: 'Barlow Condensed', sans-serif;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .shop-points-line {
        font-size: 0.8125rem;
        color: #5b9aff;
        font-weight: 600;
    }

    html.light-mode .shop-points-line { color: #00337F; }

    /* ─── 2-Col Grid (dash-grid pattern) ─── */
    @media (min-width: 1024px) {
        .shop-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.5rem;
        }
    }

    /* ─── Filter Pills (exam-history pattern) ─── */
    .shop-filter-pills {
        display: flex;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    .shop-filter-pill {
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.6875rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--glass-border);
        background: transparent;
        color: var(--text-secondary);
        transition: all 150ms ease;
        font-family: inherit;
    }

    .shop-filter-pill:hover {
        background: rgba(255,255,255,0.05);
        color: var(--text-primary);
    }

    html.light-mode .shop-filter-pill:hover { background: rgba(0,51,127,0.04); }

    .shop-filter-pill.active {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
        border-color: rgba(0,85,204,0.25);
    }

    html.light-mode .shop-filter-pill.active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.2);
    }

    /* ─── Shop Item (section-item pattern) ─── */
    .shop-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0.5rem;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .shop-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .shop-item:hover { background: rgba(0,0,0,0.03); }

    .shop-item + .shop-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .shop-item + .shop-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .shop-item__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .shop-item__icon--boost {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
    }
    html.light-mode .shop-item__icon--boost {
        background: rgba(0,51,127,0.08);
        color: #00337F;
    }

    .shop-item__icon--cosmetic {
        background: rgba(251,191,36,0.12);
        color: var(--gold);
    }

    .shop-item__icon--utility {
        background: rgba(34,197,94,0.12);
        color: #22c55e;
    }

    .shop-item__icon--accessory {
        background: rgba(168,85,247,0.12);
        color: #a855f7;
    }

    .shop-item__preview {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }

    html.light-mode .shop-item__preview {
        border-color: rgba(0,0,0,0.08);
    }

    .shop-item__body { flex: 1; min-width: 0; }

    .shop-item__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .shop-item__name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .shop-item__price {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--gold);
        font-family: 'Barlow Condensed', sans-serif;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 0.2rem;
    }

    .shop-item__price i { font-size: 0.6rem; opacity: 0.7; }

    .shop-item__desc {
        font-size: 0.6875rem;
        color: var(--text-muted);
        line-height: 1.4;
        margin-top: 0.15rem;
    }

    .shop-item__tags {
        display: flex;
        gap: 0.35rem;
        margin-top: 0.35rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .shop-item__tag {
        display: inline-flex;
        align-items: center;
        gap: 0.15rem;
        padding: 0.1rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.5625rem;
        font-weight: 600;
        font-family: 'IBM Plex Mono', monospace;
    }

    .shop-item__tag--duration {
        background: rgba(251,191,36,0.08);
        color: var(--gold);
    }

    .shop-item__tag--limit {
        background: rgba(161,161,170,0.08);
        color: var(--text-muted);
    }

    .shop-item__tag--active {
        background: rgba(34,197,94,0.12);
        color: #4ade80;
    }

    html.light-mode .shop-item__tag--active { color: #16a34a; }

    .shop-item__action {
        flex-shrink: 0;
        align-self: center;
    }

    .shop-item__reason {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    /* ─── Color Selector ─── */
    .shop-color-selector {
        display: flex;
        gap: 0.375rem;
        margin-top: 0.35rem;
    }

    .shop-color-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 150ms ease;
    }

    .shop-color-dot:hover, .shop-color-dot.selected { transform: scale(1.15); }
    .shop-color-dot.selected { border-color: var(--text-primary); }
    .shop-color-dot--blue { background: #60a5fa; }
    .shop-color-dot--purple { background: #a78bfa; }
    .shop-color-dot--green { background: #4ade80; }

    /* ─── Title Selector ─── */
    .shop-title-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        margin-top: 0.35rem;
    }

    .shop-title-chip {
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 600;
        cursor: pointer;
        background: rgba(255,255,255,0.04);
        color: var(--text-secondary);
        border: 1px solid rgba(255,255,255,0.06);
        transition: all 150ms ease;
    }

    html.light-mode .shop-title-chip {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.08);
    }

    .shop-title-chip:hover, .shop-title-chip.selected {
        background: rgba(251,191,36,0.1);
        color: var(--gold);
        border-color: rgba(251,191,36,0.25);
    }

    /* ─── Confirmation Overlay ─── */
    .shop-confirm-overlay {
        position: absolute;
        inset: 0;
        background: rgba(10,10,11,0.93);
        backdrop-filter: blur(6px);
        border-radius: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1.25rem;
        z-index: 5;
    }

    html.light-mode .shop-confirm-overlay { background: rgba(255,255,255,0.95); }

    .shop-confirm-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        text-align: center;
    }

    .shop-confirm-price {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--gold);
        font-family: 'Barlow Condensed', sans-serif;
    }

    .shop-confirm-btns { display: flex; gap: 0.625rem; }

    /* ─── Effect Item (section-item pattern) ─── */
    .shop-effect-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .shop-effect-item + .shop-effect-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .shop-effect-item + .shop-effect-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .shop-effect-item__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        background: rgba(251,191,36,0.1);
        color: var(--gold);
        flex-shrink: 0;
    }

    .shop-effect-item__name {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .shop-effect-item__detail {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    .shop-effect-item__countdown {
        margin-left: auto;
        font-size: 0.75rem;
        font-weight: 700;
        color: #5b9aff;
        font-family: 'Barlow Condensed', sans-serif;
        flex-shrink: 0;
    }

    html.light-mode .shop-effect-item__countdown { color: #00337F; }

    /* ─── History Item ─── */
    .shop-history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4rem 0.25rem;
    }

    .shop-history-item + .shop-history-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .shop-history-item + .shop-history-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .shop-history-item__left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .shop-history-item__icon { font-size: 0.75rem; color: var(--gold); opacity: 0.6; }

    .shop-history-item__name {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .shop-history-item__price {
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--gold);
        font-family: 'IBM Plex Mono', monospace;
    }

    .shop-history-item__date {
        font-size: 0.5625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        text-align: right;
    }

    /* ─── Toast ─── */
    .shop-toast {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        padding: 0.875rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.8125rem;
        z-index: 100;
    }

    .shop-toast.success {
        background: rgba(34,197,94,0.12);
        border: 1px solid rgba(34,197,94,0.25);
        color: #4ade80;
    }

    .shop-toast.error {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.25);
        color: #f87171;
    }

    /* ─── Stagger animation ─── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > * {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .dash-container > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > *:nth-child(5) { animation-delay: 0.19s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > * { animation: none; }
    }
</style>
@endpush

@section('content')
<div class="dash-container" x-data="shopApp()">

    {{-- Toast --}}
    <div class="shop-toast" x-show="toast.show" x-cloak :class="toast.type" x-text="toast.message"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"></div>

    {{-- ── Header ── --}}
    <div style="margin-bottom:1.25rem;">
        <div class="shop-greeting">Punkte Shop</div>
        <div class="shop-title">Boosts & Belohnungen</div>
        <div class="shop-points-line">
            <i class="bi bi-gem" style="font-size:0.7rem;"></i>
            <span x-text="formatNumber(userPoints)">{{ number_format(Auth::user()->points) }}</span> Punkte verfügbar
            &middot; {{ number_format(Auth::user()->total_points_spent ?? 0) }} ausgegeben
        </div>
    </div>

    {{-- ── Gami Pills ── --}}
    <div style="display:flex;gap:0.5rem;margin-bottom:1.25rem;flex-wrap:wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--gold);-webkit-text-fill-color:var(--gold);" x-text="formatNumber(userPoints)">{{ number_format(Auth::user()->points) }}</div>
            <div class="gami-pill__label">Verfügbar</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#a78bfa;-webkit-text-fill-color:#a78bfa;">{{ count($activeEffects) }}</div>
            <div class="gami-pill__label">Aktive Effekte</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ number_format(Auth::user()->total_points_spent ?? 0) }}</div>
            <div class="gami-pill__label">Ausgegeben</div>
        </div>
    </div>

    {{-- ── Main Grid ── --}}
    <div class="shop-grid">

        {{-- ═══ MAIN COLUMN ═══ --}}
        <div class="space-y-4">

            {{-- Lootbox Nudge --}}
            @if($unopenedLootboxes->count() > 0)
            <a href="{{ route('gamification.lootboxes') }}" class="lootbox-nudge">
                <div style="display:flex;align-items:center;gap:0.625rem;">
                    <div class="lootbox-nudge__icon">
                        <i class="bi bi-gift-fill" style="font-size:0.875rem;"></i>
                    </div>
                    <div>
                        <div class="lootbox-nudge__title">{{ $unopenedLootboxes->count() }} {{ $unopenedLootboxes->count() === 1 ? 'Wochenbelohnung' : 'Wochenbelohnungen' }}</div>
                        <div class="lootbox-nudge__desc">
                            @foreach($unopenedLootboxes->groupBy('type') as $type => $boxes)
                                {{ $boxes->count() }}x {{ ucfirst($type) }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            &mdash; jetzt öffnen
                        </div>
                    </div>
                </div>
                <i class="bi bi-arrow-right" style="color:#fbbf24;font-size:0.75rem;"></i>
            </a>
            @endif

            {{-- Filter Pills --}}
            <div class="shop-filter-pills" x-data="{ filter: 'all' }">
                <button class="shop-filter-pill" :class="filter === 'all' && 'active'" @click="filter = 'all'; $dispatch('shop-filter', 'all')">Alle</button>
                <button class="shop-filter-pill" :class="filter === 'boost' && 'active'" @click="filter = 'boost'; $dispatch('shop-filter', 'boost')">Boosts</button>
                <button class="shop-filter-pill" :class="filter === 'cosmetic' && 'active'" @click="filter = 'cosmetic'; $dispatch('shop-filter', 'cosmetic')">Kosmetisch</button>
                <button class="shop-filter-pill" :class="filter === 'utility' && 'active'" @click="filter = 'utility'; $dispatch('shop-filter', 'utility')">Nützlich</button>
                <button class="shop-filter-pill" :class="filter === 'accessory' && 'active'" @click="filter = 'accessory'; $dispatch('shop-filter', 'accessory')">Accessoires</button>
            </div>

            {{-- Shop Items --}}
            <div class="glass" style="padding:1rem;" x-data="{ activeFilter: 'all' }" @shop-filter.window="activeFilter = $event.detail">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="section-label">Verfügbare Items</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ count($items) }} Items</span>
                </div>

                @foreach($items as $slug => $item)
                <div class="shop-item"
                     data-category="{{ $item['category'] }}"
                     x-show="activeFilter === 'all' || activeFilter === '{{ $item['category'] }}'"
                     x-data="{ confirming: false, selectedColor: '{{ isset($item['colors']) ? $item['colors'][0] : 'blue' }}', selectedTitle: 'Meisterhelfer' }"
                     style="position:relative;">

                    @if($item['category'] === 'accessory')
                    <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=ShopPreview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&{{ $item['accessory_type'] }}={{ $item['accessory_value'] }}&{{ $item['accessory_type'] === 'accessories' ? 'accessoriesProbability=100' : ($item['accessory_type'] === 'facialHair' ? 'facialHairProbability=100' : '') }}&eyes=default&mouth=smile" alt="{{ $item['name'] }}" class="shop-item__preview" loading="lazy">
                    @else
                    <div class="shop-item__icon shop-item__icon--{{ $item['category'] }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </div>
                    @endif

                    <div class="shop-item__body">
                        <div class="shop-item__top">
                            <span class="shop-item__name">{{ $item['name'] }}</span>
                            <span class="shop-item__price"><i class="bi bi-gem"></i> {{ number_format($item['price']) }}</span>
                        </div>
                        <div class="shop-item__desc">{{ $item['description'] }}</div>

                        {{-- Color selector for rank_color --}}
                        @if($slug === 'rank_color')
                        <div class="shop-color-selector">
                            <div class="shop-color-dot shop-color-dot--blue" :class="selectedColor === 'blue' ? 'selected' : ''" @click="selectedColor = 'blue'"></div>
                            <div class="shop-color-dot shop-color-dot--purple" :class="selectedColor === 'purple' ? 'selected' : ''" @click="selectedColor = 'purple'"></div>
                            <div class="shop-color-dot shop-color-dot--green" :class="selectedColor === 'green' ? 'selected' : ''" @click="selectedColor = 'green'"></div>
                        </div>
                        @endif

                        {{-- Title selector --}}
                        @if($slug === 'title')
                        <div class="shop-title-selector">
                            @foreach($item['titles'] as $titleOption)
                            <div class="shop-title-chip" :class="selectedTitle === '{{ $titleOption }}' ? 'selected' : ''" @click="selectedTitle = '{{ $titleOption }}'">{{ $titleOption }}</div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Color selector for accessories with colors --}}
                        @if($item['category'] === 'accessory' && isset($item['colors']) && !($item['is_active'] ?? false) && ($item['can_purchase'] ?? true))
                        <div class="shop-color-selector">
                            @foreach($item['colors'] as $colorHex)
                            <div class="shop-color-dot" :class="selectedColor === '{{ $colorHex }}' ? 'selected' : ''" @click="selectedColor = '{{ $colorHex }}'" style="background:#{{ $colorHex }};"></div>
                            @endforeach
                        </div>
                        @endif

                        <div class="shop-item__tags">
                            @if($item['type'] === 'permanent')
                                <span class="shop-item__tag shop-item__tag--duration">Permanent</span>
                            @elseif(isset($item['duration_days']) && $item['duration_days'])
                                <span class="shop-item__tag shop-item__tag--duration">{{ $item['duration_days'] }} Tage</span>
                            @elseif(isset($item['duration_hours']))
                                <span class="shop-item__tag shop-item__tag--duration">{{ $item['duration_hours'] }}h</span>
                            @elseif($item['type'] === 'instant')
                                <span class="shop-item__tag shop-item__tag--duration">Sofort</span>
                            @endif
                            @if($item['max_per_week'])
                                <span class="shop-item__tag shop-item__tag--limit">Max {{ $item['max_per_week'] }}/Wo</span>
                            @endif
                            @if($item['is_active'])
                                <span class="shop-item__tag shop-item__tag--active"><i class="bi bi-check-circle-fill" style="font-size:0.5rem;"></i> Aktiv</span>
                            @endif
                        </div>

                        @if(!$item['can_purchase'] && $item['reason'])
                            <div class="shop-item__reason">{{ $item['reason'] }}</div>
                        @endif
                    </div>

                    <div class="shop-item__action">
                        @if($item['can_purchase'])
                            <button class="btn-primary btn-sm" @click="confirming = true">Kaufen</button>
                        @else
                            <button class="btn-primary btn-sm" disabled>
                                @if($item['reason'] && str_contains($item['reason'], 'Besitz'))
                                    Besitzt
                                @elseif($item['reason'] && str_contains($item['reason'], 'Limit'))
                                    Limit
                                @else
                                    Kaufen
                                @endif
                            </button>
                        @endif
                    </div>

                    {{-- Confirmation overlay --}}
                    <div class="shop-confirm-overlay" x-show="confirming" x-cloak x-transition.opacity>
                        <div class="shop-confirm-text">{{ $item['name'] }} kaufen?</div>
                        <div class="shop-confirm-price"><i class="bi bi-gem"></i> {{ number_format($item['price']) }} Punkte</div>
                        <div class="shop-confirm-btns">
                            <button class="btn-primary btn-sm"
                                @click="purchaseItem('{{ $slug }}', { color: selectedColor, title: selectedTitle }); confirming = false"
                                :disabled="purchasing">
                                <span x-show="!purchasing">Kaufen</span>
                                <span x-show="purchasing">...</span>
                            </button>
                            <button class="btn-ghost btn-sm" @click="confirming = false">Abbrechen</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Mobile: Active Effects --}}
            <div class="glass lg:hidden" style="padding:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="section-label">Aktive Effekte</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ count($activeEffects) }} aktiv</span>
                </div>

                @if(count($activeEffects) > 0)
                    @foreach($activeEffects as $effect)
                    <div class="shop-effect-item">
                        <div class="shop-effect-item__icon"><i class="bi {{ $effect['icon'] }}"></i></div>
                        <div style="flex:1;min-width:0;">
                            <div class="shop-effect-item__name">{{ $effect['name'] }}</div>
                            <div class="shop-effect-item__detail">
                                {{ $effect['expires_at']->format('d.m.Y H:i') }}
                                @if(isset($effect['color']))
                                    &mdash; <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $effect['color'] === 'blue' ? '#60a5fa' : ($effect['color'] === 'purple' ? '#a78bfa' : '#4ade80') }};vertical-align:middle;"></span>
                                @endif
                                @if(isset($effect['title']))
                                    &mdash; {{ $effect['title'] }}
                                @endif
                            </div>
                        </div>
                        <div class="shop-effect-item__countdown">{{ $effect['expires_at']->diffForHumans(null, true, true) }}</div>
                    </div>
                    @endforeach
                @else
                    <div style="padding:1rem 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">
                        Keine aktiven Effekte. Kaufe Items im Shop!
                    </div>
                @endif

                @php
                    $freezeStatus = (new \App\Services\GamificationService())->getStreakFreezeStatus(Auth::user());
                @endphp
                <div style="border-top:1px solid rgba(255,255,255,0.06);margin-top:0.25rem;padding-top:0.5rem;">
                    <div class="shop-effect-item" style="border-top:none;">
                        <div class="shop-effect-item__icon" style="background:rgba(34,197,94,0.1);color:#22c55e;"><i class="bi bi-shield-fill"></i></div>
                        <div style="flex:1;min-width:0;">
                            <div class="shop-effect-item__name">Streak Freezes</div>
                            <div class="shop-effect-item__detail">{{ $freezeStatus['remaining'] }} verfügbar ({{ $freezeStatus['used'] }} benutzt diese Woche)</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile: Purchase History --}}
            @if(count($purchaseHistory) > 0)
            <div class="glass lg:hidden" style="padding:1rem;">
                <div class="section-label" style="margin-bottom:0.75rem;">Letzte Käufe</div>

                @foreach($purchaseHistory as $purchase)
                    @php $itemDef = \App\Services\ShopService::SHOP_ITEMS[$purchase->item_type] ?? null; @endphp
                    <div class="shop-history-item">
                        <div class="shop-history-item__left">
                            @if($itemDef)<i class="bi {{ $itemDef['icon'] }} shop-history-item__icon"></i>@endif
                            <span class="shop-history-item__name">{{ $itemDef['name'] ?? $purchase->item_type }}</span>
                        </div>
                        <div>
                            <div class="shop-history-item__price">-{{ number_format($purchase->price) }}</div>
                            <div class="shop-history-item__date">{{ $purchase->created_at->format('d.m.Y') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

        </div>

        {{-- ═══ DESKTOP SIDEBAR ═══ --}}
        <div class="hidden lg:block space-y-4">

            {{-- Active Effects --}}
            <div class="glass" style="padding:1rem;">
                <div class="section-label" style="margin-bottom:0.75rem;">Aktive Effekte</div>

                @if(count($activeEffects) > 0)
                    @foreach($activeEffects as $effect)
                    <div class="shop-effect-item">
                        <div class="shop-effect-item__icon"><i class="bi {{ $effect['icon'] }}"></i></div>
                        <div style="flex:1;min-width:0;">
                            <div class="shop-effect-item__name">{{ $effect['name'] }}</div>
                            <div class="shop-effect-item__detail">
                                {{ $effect['expires_at']->format('d.m.Y H:i') }}
                                @if(isset($effect['color']))
                                    &mdash; <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $effect['color'] === 'blue' ? '#60a5fa' : ($effect['color'] === 'purple' ? '#a78bfa' : '#4ade80') }};vertical-align:middle;"></span>
                                @endif
                                @if(isset($effect['title']))
                                    &mdash; {{ $effect['title'] }}
                                @endif
                            </div>
                        </div>
                        <div class="shop-effect-item__countdown">{{ $effect['expires_at']->diffForHumans(null, true, true) }}</div>
                    </div>
                    @endforeach
                @else
                    <div style="padding:1rem 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">
                        Keine aktiven Effekte
                    </div>
                @endif

                @php
                    if (!isset($freezeStatus)) {
                        $freezeStatus = (new \App\Services\GamificationService())->getStreakFreezeStatus(Auth::user());
                    }
                @endphp
                <div style="border-top:1px solid rgba(255,255,255,0.06);margin-top:0.35rem;padding-top:0.5rem;">
                    <div class="shop-effect-item" style="border-top:none;">
                        <div class="shop-effect-item__icon" style="background:rgba(34,197,94,0.1);color:#22c55e;"><i class="bi bi-shield-fill"></i></div>
                        <div style="flex:1;min-width:0;">
                            <div class="shop-effect-item__name">Streak Freezes</div>
                            <div class="shop-effect-item__detail">{{ $freezeStatus['remaining'] }} verfügbar ({{ $freezeStatus['used'] }} benutzt)</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Purchase History --}}
            @if(count($purchaseHistory) > 0)
            <div class="glass" style="padding:1rem;">
                <div class="section-label" style="margin-bottom:0.75rem;">Letzte Käufe</div>

                @foreach($purchaseHistory as $purchase)
                    @php $itemDef = \App\Services\ShopService::SHOP_ITEMS[$purchase->item_type] ?? null; @endphp
                    <div class="shop-history-item">
                        <div class="shop-history-item__left">
                            @if($itemDef)<i class="bi {{ $itemDef['icon'] }} shop-history-item__icon"></i>@endif
                            <span class="shop-history-item__name">{{ $itemDef['name'] ?? $purchase->item_type }}</span>
                        </div>
                        <div>
                            <div class="shop-history-item__price">-{{ number_format($purchase->price) }}</div>
                            <div class="shop-history-item__date">{{ $purchase->created_at->format('d.m.Y') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

        </div>

    </div>
</div>

<script>
function shopApp() {
    return {
        userPoints: {{ Auth::user()->points }},
        purchasing: false,
        toast: { show: false, message: '', type: 'success' },

        formatNumber(n) {
            return new Intl.NumberFormat('de-DE').format(n);
        },

        async purchaseItem(slug, metadata) {
            if (this.purchasing) return;
            this.purchasing = true;

            try {
                const response = await fetch('{{ route("shop.purchase") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ item: slug, ...metadata }),
                    cache: 'no-store',
                });

                const data = await response.json();

                if (data.success) {
                    this.userPoints = data.new_balance;
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showToast(data.message, 'error');
                }
            } catch (error) {
                this.showToast('Ein Fehler ist aufgetreten.', 'error');
            } finally {
                this.purchasing = false;
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 4000);
        }
    };
}
</script>
@endsection
