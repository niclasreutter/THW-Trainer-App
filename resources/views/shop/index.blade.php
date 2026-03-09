@extends('layouts.app')
@section('title', 'Shop - THW Trainer')

@push('styles')
<style>
    /* Match dashboard 3-column bento grid */
    .bento-grid {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.25rem;
    }

    .bento-wide {
        grid-column: span 3;
    }

    @media (max-width: 900px) {
        .bento-grid { grid-template-columns: 1fr 1fr; }
        .bento-wide { grid-column: span 2; }
        .bento-2of3 { grid-column: span 2; }
    }

    @media (max-width: 600px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-wide, .bento-2of3, .bento-1of3 { grid-column: span 1; }
    }

    .shop-item-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .shop-item {
        position: relative;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .shop-item:hover {
        transform: translateY(-2px);
    }

    .shop-item-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .shop-item-icon {
        font-size: 2rem;
        flex-shrink: 0;
    }

    .shop-item-name {
        font-weight: 700;
        font-size: 1rem;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .shop-item-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    .shop-item-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .shop-price {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--gold);
    }

    .shop-price-icon {
        font-size: 0.9rem;
    }

    .shop-item-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-active {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
    }

    .badge-duration {
        background: rgba(251, 191, 36, 0.12);
        color: var(--gold);
    }

    .badge-limit {
        background: rgba(161, 161, 170, 0.12);
        color: var(--text-secondary);
    }

    /* Confirmation Overlay */
    .shop-confirm-overlay {
        position: absolute;
        inset: 0;
        background: rgba(10, 10, 11, 0.92);
        backdrop-filter: blur(4px);
        border-radius: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 1.5rem;
        z-index: 5;
    }

    .shop-confirm-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        text-align: center;
    }

    .shop-confirm-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gold);
    }

    .shop-confirm-btns {
        display: flex;
        gap: 0.75rem;
    }

    /* Active Effects Widget */
    .effect-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .effect-item:last-child {
        border-bottom: none;
    }

    .effect-icon {
        font-size: 1.25rem;
        color: var(--gold);
        flex-shrink: 0;
    }

    .effect-name {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .effect-expires {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* History */
    .history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-name {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .history-meta {
        text-align: right;
    }

    .history-price {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gold);
    }

    .history-date {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    /* Color Selector */
    .color-selector {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .color-option {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .color-option:hover,
    .color-option.selected {
        transform: scale(1.15);
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
    }

    .color-option.selected {
        border-color: var(--text-primary);
    }

    .color-blue { background: #60a5fa; }
    .color-purple { background: #a78bfa; }
    .color-green { background: #4ade80; }

    /* Title Selector */
    .title-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.75rem;
    }

    .title-option {
        padding: 0.3rem 0.6rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s;
    }

    .title-option:hover,
    .title-option.selected {
        background: rgba(251, 191, 36, 0.12);
        color: var(--gold);
        border-color: rgba(251, 191, 36, 0.3);
    }

    /* Toast notification */
    .shop-toast {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 100;
    }

    .shop-toast.success {
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #4ade80;
    }

    .shop-toast.error {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
    }

    @media (max-width: 600px) {
        .shop-item-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container" x-data="shopApp()">

    <!-- Toast -->
    <div class="shop-toast" x-show="toast.show" x-cloak :class="toast.type" x-text="toast.message"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"></div>

    <header class="dashboard-header">
        <h1 class="page-title">Punkte <span>Shop</span></h1>
        <p class="page-subtitle">Gib deine verdienten Punkte für Boosts und kosmetische Effekte aus</p>
    </header>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--gold);"><i class="bi bi-gem"></i></span>
            <div>
                <div class="stat-pill-value" x-text="formatNumber(userPoints)">{{ number_format(Auth::user()->points) }}</div>
                <div class="stat-pill-label">Verfügbar</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #a78bfa;"><i class="bi bi-magic"></i></span>
            <div>
                <div class="stat-pill-value">{{ count($activeEffects) }}</div>
                <div class="stat-pill-label">Aktive Effekte</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #60a5fa;"><i class="bi bi-bag-check-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format(Auth::user()->total_points_spent ?? 0) }}</div>
                <div class="stat-pill-label">Ausgegeben</div>
            </div>
        </div>
    </div>

    <div class="bento-grid">
        <!-- Main: Shop Items -->
        <div class="glass bento-2of3 p-6">
            <div class="section-header mb-5">
                <h2 class="section-title">Verfügbare Items</h2>
            </div>

            <div class="shop-item-grid">
                @foreach($items as $slug => $item)
                <div class="glass shop-item" x-data="{ confirming: false, selectedColor: 'blue', selectedTitle: 'Meisterhelfer' }">
                    {{-- Active badge --}}
                    @if($item['is_active'])
                        <div class="shop-item-badge badge-active" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                            <i class="bi bi-check-circle-fill"></i> Aktiv
                        </div>
                    @endif

                    <div class="shop-item-header">
                        <div class="shop-item-icon" style="color: var(--gold);">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="shop-item-name">{{ $item['name'] }}</div>
                            <div class="shop-item-desc">{{ $item['description'] }}</div>
                        </div>
                    </div>

                    {{-- Duration / Limit badges --}}
                    <div class="flex flex-wrap gap-1.5">
                        @if(isset($item['duration_days']))
                            <span class="shop-item-badge badge-duration">{{ $item['duration_days'] }} Tage</span>
                        @elseif(isset($item['duration_hours']))
                            <span class="shop-item-badge badge-duration">{{ $item['duration_hours'] }}h</span>
                        @endif
                        @if($item['max_per_week'])
                            <span class="shop-item-badge badge-limit">Max {{ $item['max_per_week'] }}/Woche</span>
                        @endif
                    </div>

                    {{-- Color selector for rank_color --}}
                    @if($slug === 'rank_color')
                        <div class="color-selector">
                            <div class="color-option color-blue" :class="selectedColor === 'blue' ? 'selected' : ''" @click="selectedColor = 'blue'"></div>
                            <div class="color-option color-purple" :class="selectedColor === 'purple' ? 'selected' : ''" @click="selectedColor = 'purple'"></div>
                            <div class="color-option color-green" :class="selectedColor === 'green' ? 'selected' : ''" @click="selectedColor = 'green'"></div>
                        </div>
                    @endif

                    {{-- Title selector --}}
                    @if($slug === 'title')
                        <div class="title-selector">
                            @foreach($item['titles'] as $titleOption)
                                <div class="title-option" :class="selectedTitle === '{{ $titleOption }}' ? 'selected' : ''" @click="selectedTitle = '{{ $titleOption }}'">{{ $titleOption }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="shop-item-footer">
                        <div class="shop-price">
                            <i class="bi bi-gem shop-price-icon"></i>
                            {{ number_format($item['price']) }}
                        </div>

                        @if($item['can_purchase'])
                            <button class="btn-primary btn-sm" @click="confirming = true">Kaufen</button>
                        @else
                            <button class="btn-primary btn-sm" disabled style="opacity: 0.4; cursor: not-allowed;">
                                {{ $item['reason'] && str_contains($item['reason'], 'Limit') ? 'Limit' : 'Kaufen' }}
                            </button>
                        @endif
                    </div>

                    {{-- Reason if can't purchase --}}
                    @if(!$item['can_purchase'] && $item['reason'])
                        <div class="text-xs mt-2" style="color: var(--text-muted);">{{ $item['reason'] }}</div>
                    @endif

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
        </div>

        <!-- Sidebar: Active Effects -->
        <div class="glass bento-1of3 p-6">
            <div class="section-header mb-4">
                <h2 class="section-title">Aktive Effekte</h2>
            </div>

            @if(count($activeEffects) > 0)
                @foreach($activeEffects as $effect)
                    <div class="effect-item">
                        <div class="effect-icon"><i class="bi {{ $effect['icon'] }}"></i></div>
                        <div>
                            <div class="effect-name">{{ $effect['name'] }}</div>
                            <div class="effect-expires">
                                Läuft ab: {{ $effect['expires_at']->format('d.m.Y H:i') }}
                                @if(isset($effect['color']))
                                    <span class="color-option color-{{ $effect['color'] }}" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-left: 4px;"></span>
                                @endif
                                @if(isset($effect['title']))
                                    &mdash; {{ $effect['title'] }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-sm" style="color: var(--text-muted); padding: 1rem 0;">
                    Keine aktiven Effekte. Kaufe Items im Shop!
                </div>
            @endif

            {{-- Streak Freezes Status --}}
            @php
                $freezeStatus = (new \App\Services\GamificationService())->getStreakFreezeStatus(Auth::user());
            @endphp
            <div class="effect-item" style="margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                <div class="effect-icon"><i class="bi bi-shield-fill"></i></div>
                <div>
                    <div class="effect-name">Streak Freezes</div>
                    <div class="effect-expires">{{ $freezeStatus['remaining'] }} verfügbar ({{ $freezeStatus['used'] }} benutzt diese Woche)</div>
                </div>
            </div>
        </div>

        <!-- Purchase History -->
        @if(count($purchaseHistory) > 0)
        <div class="glass bento-wide p-6">
            <div class="section-header mb-4">
                <h2 class="section-title">Letzte Käufe</h2>
            </div>

            @foreach($purchaseHistory as $purchase)
                @php
                    $itemDef = \App\Services\ShopService::SHOP_ITEMS[$purchase->item_type] ?? null;
                @endphp
                <div class="history-item">
                    <div class="flex items-center gap-2">
                        @if($itemDef)
                            <i class="bi {{ $itemDef['icon'] }}" style="color: var(--gold); font-size: 1rem;"></i>
                        @endif
                        <span class="history-name">{{ $itemDef['name'] ?? $purchase->item_type }}</span>
                    </div>
                    <div class="history-meta">
                        <div class="history-price">-{{ number_format($purchase->price) }}</div>
                        <div class="history-date">{{ $purchase->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
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
                    // Reload after short delay to show updated state
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
