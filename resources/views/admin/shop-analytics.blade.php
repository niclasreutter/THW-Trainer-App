@extends('layouts.app')

@section('title', 'Shop Analyse - Admin')
@section('description', 'Detaillierte Shop-Statistiken und Kaufanalysen')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    @media (max-width: 600px) {
        .chart-container {
            height: 250px;
        }
    }

    .purchase-table {
        width: 100%;
        border-collapse: collapse;
    }

    .purchase-table th,
    .purchase-table td {
        padding: 0.625rem 0.75rem;
        text-align: left;
        font-size: 0.8rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .purchase-table th {
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.5px;
    }

    .purchase-table td {
        color: var(--text-secondary);
    }

    .purchase-table tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }

    .purchase-table tr:last-child td {
        border-bottom: none;
    }

    .item-bar {
        height: 6px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.08);
        overflow: hidden;
        margin-top: 0.25rem;
    }

    .item-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }

    .category-badge {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .category-badge.cosmetic { background: rgba(139, 92, 246, 0.2); color: #a78bfa; }
    .category-badge.boost { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .category-badge.utility { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }

    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Shop <span>Analyse</span></h1>
        <p class="page-subtitle">Kaufstatistiken, Trends und Preisübersicht</p>
    </header>

    <!-- Stats Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-bag-check"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($totalPurchases) }}</div>
                <div class="stat-pill-label">Käufe gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #fbbf24;"><i class="bi bi-star"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($totalXpSpent) }}</div>
                <div class="stat-pill-label">XP ausgegeben</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($uniqueBuyers) }}</div>
                <div class="stat-pill-label">Käufer</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-graph-up-arrow"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($avgXpPerPurchase) }}</div>
                <div class="stat-pill-label">XP / Kauf</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="bento-grid" style="margin-bottom: 1.5rem;">
        <!-- Purchase Trend Chart -->
        <div class="glass-gold bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-graph-up"></i>
                Käufe (30 Tage)
            </h3>
            <div class="chart-container">
                <canvas id="purchaseTrendChart"></canvas>
            </div>
        </div>

        <!-- XP Spent Trend Chart -->
        <div class="glass-blue bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-star"></i>
                XP ausgegeben (30 Tage)
            </h3>
            <div class="chart-container">
                <canvas id="xpTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Item Stats + Category Breakdown -->
    <div class="bento-grid" style="margin-bottom: 1.5rem;">
        <!-- Item Popularity -->
        <div class="glass-tl bento-main">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-bar-chart"></i>
                Artikel-Beliebtheit
            </h3>

            @php $maxCount = $itemStats->max('purchase_count') ?: 1; @endphp

            <div class="table-scroll">
                <table class="purchase-table">
                    <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Kategorie</th>
                            <th>Preis</th>
                            <th>Käufe</th>
                            <th>XP gesamt</th>
                            <th style="width: 120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemStats as $item)
                            <tr>
                                <td style="font-weight: 600; color: var(--text-primary);">{{ $item->item_name }}</td>
                                <td><span class="category-badge {{ $item->category }}">{{ $item->category }}</span></td>
                                <td>{{ number_format($item->item_price) }} XP</td>
                                <td style="font-weight: 700; color: var(--text-primary);">{{ number_format($item->purchase_count) }}</td>
                                <td>{{ number_format($item->total_spent) }} XP</td>
                                <td>
                                    <div class="item-bar">
                                        <div class="item-bar-fill" style="width: {{ round(($item->purchase_count / $maxCount) * 100) }}%; background: linear-gradient(90deg, #fbbf24, #f59e0b);"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">Noch keine Käufe vorhanden</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="glass-br bento-side">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-pie-chart"></i>
                Nach Kategorie
            </h3>

            @php
                $categoryNames = ['cosmetic' => 'Kosmetisch', 'boost' => 'Boosts', 'utility' => 'Nützlich'];
                $categoryColors = ['cosmetic' => '#8b5cf6', 'boost' => '#fbbf24', 'utility' => '#3b82f6'];
                $totalCategoryCount = $categoryStats->sum(fn ($c) => $c['count']);
            @endphp

            @forelse($categoryStats as $category => $stats)
                <div style="margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.85rem; font-weight: 600; color: {{ $categoryColors[$category] ?? '#a1a1aa' }};">
                            {{ $categoryNames[$category] ?? ucfirst($category) }}
                        </span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $stats['count'] }}x &middot; {{ number_format($stats['total']) }} XP
                        </span>
                    </div>
                    <div class="item-bar">
                        <div class="item-bar-fill" style="width: {{ $totalCategoryCount > 0 ? round(($stats['count'] / $totalCategoryCount) * 100) : 0 }}%; background: {{ $categoryColors[$category] ?? '#a1a1aa' }};"></div>
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted); font-size: 0.85rem;">Keine Daten</p>
            @endforelse

            <!-- Price overview -->
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                <h4 style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 0.75rem;">
                    Aktuelle Preise
                </h4>
                @foreach($shopItems as $slug => $item)
                    <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.8rem;">
                        <span style="color: var(--text-secondary);">{{ $item['name'] }}</span>
                        <span style="color: var(--text-primary); font-weight: 600;">{{ number_format($item['price']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Buyers + Recent Purchases -->
    <div class="bento-grid" style="margin-bottom: 1.5rem;">
        <!-- Top Buyers -->
        <div class="glass-purple bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-trophy"></i>
                Top Käufer
            </h3>

            <div class="table-scroll">
                <table class="purchase-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Benutzer</th>
                            <th>Level</th>
                            <th>Käufe</th>
                            <th>XP ausgegeben</th>
                            <th>XP aktuell</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBuyers as $index => $buyer)
                            <tr>
                                <td style="font-weight: 700; color: var(--text-muted);">{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $buyer->user->id) }}" style="color: var(--text-primary); font-weight: 600; text-decoration: none;">
                                        {{ $buyer->user->name }}
                                    </a>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $buyer->user->email }}</div>
                                </td>
                                <td>{{ $buyer->user->level }}</td>
                                <td style="font-weight: 700;">{{ $buyer->purchase_count }}</td>
                                <td style="color: #fbbf24; font-weight: 700;">{{ number_format($buyer->total_spent) }}</td>
                                <td>{{ number_format($buyer->user->points) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">Keine Käufer</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="glass-slash bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-clock-history"></i>
                Letzte Käufe
            </h3>

            <div class="table-scroll">
                <table class="purchase-table">
                    <thead>
                        <tr>
                            <th>Zeitpunkt</th>
                            <th>Benutzer</th>
                            <th>Artikel</th>
                            <th>Preis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPurchases as $purchase)
                            <tr>
                                <td style="white-space: nowrap; font-size: 0.75rem;">{{ $purchase->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if($purchase->user)
                                        <a href="{{ route('admin.users.edit', $purchase->user->id) }}" style="color: var(--text-primary); font-weight: 600; text-decoration: none;">
                                            {{ $purchase->user->name }}
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted);">Gelöscht</span>
                                    @endif
                                </td>
                                <td>{{ $purchase->item_name }}</td>
                                <td style="font-weight: 600;">{{ number_format($purchase->price) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Keine Käufe</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Zurück zum Dashboard</a>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/admin.js')
<script>
    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.color = '#a1a1aa';

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                padding: 12,
                borderRadius: 8,
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                titleFont: { size: 14, weight: 'bold' },
                titleColor: '#f5f5f5',
                bodyFont: { size: 13 },
                bodyColor: '#f5f5f5'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(255, 255, 255, 0.06)', drawBorder: false },
                ticks: { font: { size: 11 }, color: '#a1a1aa' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#a1a1aa', maxRotation: 45, minRotation: 45 }
            }
        }
    };

    // Purchase count chart
    new Chart(document.getElementById('purchaseTrendChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($purchaseTrend['labels']) !!},
            datasets: [{
                label: 'Käufe',
                data: {!! json_encode($purchaseTrend['values']) !!},
                backgroundColor: 'rgba(251, 191, 36, 0.4)',
                borderColor: '#fbbf24',
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: commonOptions
    });

    // XP spent chart
    new Chart(document.getElementById('xpTrendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($purchaseTrend['labels']) !!},
            datasets: [{
                label: 'XP ausgegeben',
                data: {!! json_encode($purchaseTrend['xp']) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#3b82f6'
            }]
        },
        options: commonOptions
    });
</script>
@endpush
