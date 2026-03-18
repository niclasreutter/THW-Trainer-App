@extends('layouts.app')
@section('title', 'Wochenbelohnungen - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Lootbox Cards Grid ──────────────────────── */
    .lb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem;
    }

    .lb-card {
        padding: 1.5rem 1.25rem;
        border-radius: 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: transform 250ms ease, box-shadow 250ms ease;
        position: relative;
        overflow: hidden;
    }

    .lb-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }

    /* Type variants */
    .lb-card--gold {
        border: 1px solid rgba(251, 191, 36, 0.25);
        background: rgba(251, 191, 36, 0.06);
    }

    .lb-card--gold::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 60%;
        height: 100%;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .lb-card--silber {
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(148, 163, 184, 0.06);
    }

    .lb-card--silber::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 60%;
        height: 100%;
        background: radial-gradient(circle, rgba(148, 163, 184, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .lb-card--bronze {
        border: 1px solid rgba(205, 127, 50, 0.25);
        background: rgba(205, 127, 50, 0.06);
    }

    .lb-card--bronze::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 60%;
        height: 100%;
        background: radial-gradient(circle, rgba(205, 127, 50, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Light mode card variants */
    html.light-mode .lb-card--gold {
        border-color: rgba(217, 119, 6, 0.25);
        background: rgba(217, 119, 6, 0.05);
    }
    html.light-mode .lb-card--silber {
        border-color: rgba(100, 116, 139, 0.2);
        background: rgba(100, 116, 139, 0.05);
    }
    html.light-mode .lb-card--bronze {
        border-color: rgba(180, 83, 9, 0.2);
        background: rgba(180, 83, 9, 0.05);
    }

    /* Card icon */
    .lb-card__icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: inline-block;
        animation: lb-float 3s ease-in-out infinite;
    }

    .lb-card--gold .lb-card__icon { color: #fbbf24; }
    .lb-card--silber .lb-card__icon { color: #94a3b8; }
    .lb-card--bronze .lb-card__icon { color: #cd7f32; }

    html.light-mode .lb-card--gold .lb-card__icon { color: #d97706; }
    html.light-mode .lb-card--silber .lb-card__icon { color: #64748b; }
    html.light-mode .lb-card--bronze .lb-card__icon { color: #92400e; }

    @keyframes lb-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    .lb-card__type {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .lb-card__desc {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* Open buttons per type */
    .lb-card__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.4rem 1rem;
        border-radius: 0.375rem;
        font-weight: 700;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .lb-card__btn:hover {
        transform: scale(1.04);
    }

    .lb-card--gold .lb-card__btn {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #000;
    }
    .lb-card--gold .lb-card__btn:hover {
        box-shadow: 0 4px 16px rgba(251, 191, 36, 0.3);
    }

    .lb-card--silber .lb-card__btn {
        background: linear-gradient(135deg, #94a3b8, #64748b);
        color: #fff;
    }
    .lb-card--silber .lb-card__btn:hover {
        box-shadow: 0 4px 16px rgba(148, 163, 184, 0.3);
    }

    .lb-card--bronze .lb-card__btn {
        background: linear-gradient(135deg, #cd7f32, #a0522d);
        color: #fff;
    }
    .lb-card--bronze .lb-card__btn:hover {
        box-shadow: 0 4px 16px rgba(205, 127, 50, 0.3);
    }

    html.light-mode .lb-card--gold .lb-card__btn {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #fff;
    }

    .lb-card.opened { display: none; }

    /* ─── History Items ───────────────────────────── */
    .lb-history-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.25rem;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .lb-history-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .lb-history-item:hover { background: rgba(0,0,0,0.03); }

    .lb-history-item + .lb-history-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }
    html.light-mode .lb-history-item + .lb-history-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .lb-history-badge {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .lb-history-badge--gold { background: rgba(251, 191, 36, 0.12); color: #fbbf24; }
    .lb-history-badge--silber { background: rgba(148, 163, 184, 0.12); color: #94a3b8; }
    .lb-history-badge--bronze { background: rgba(205, 127, 50, 0.12); color: #cd7f32; }

    html.light-mode .lb-history-badge--gold { background: rgba(217, 119, 6, 0.1); color: #d97706; }
    html.light-mode .lb-history-badge--silber { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    html.light-mode .lb-history-badge--bronze { background: rgba(180, 83, 9, 0.1); color: #92400e; }

    .lb-history-reward {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.8125rem;
    }

    .lb-history-meta {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lb-history-tag {
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        margin-left: auto;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lb-history-tag--xp { background: rgba(251, 191, 36, 0.12); color: #fbbf24; }
    .lb-history-tag--streak_freeze { background: rgba(96, 165, 250, 0.12); color: #60a5fa; }
    .lb-history-tag--double_xp { background: rgba(192, 132, 252, 0.12); color: #c084fc; }

    html.light-mode .lb-history-tag--xp { background: rgba(217, 119, 6, 0.1); color: #b45309; }
    html.light-mode .lb-history-tag--streak_freeze { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
    html.light-mode .lb-history-tag--double_xp { background: rgba(147, 51, 234, 0.1); color: #7c3aed; }

    /* ─── Empty State ─────────────────────────────── */
    .lb-empty {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .lb-empty__icon {
        width: 3rem;
        height: 3rem;
        margin: 0 auto 1rem;
        border-radius: 0.75rem;
        background: rgba(251, 191, 36, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fbbf24;
        font-size: 1.25rem;
    }

    html.light-mode .lb-empty__icon {
        background: rgba(217, 119, 6, 0.06);
        color: #d97706;
    }

    .lb-empty__title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
    }

    .lb-empty__desc {
        font-size: 0.8125rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* ─── Opening Overlay ─────────────────────────── */
    .lb-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 1rem;
    }

    html.light-mode .lb-overlay {
        background: rgba(0, 0, 0, 0.6);
    }

    .lb-overlay.active { display: flex; }

    .lb-overlay__content {
        text-align: center;
        position: relative;
        padding: 2.5rem 2rem;
        max-width: 340px;
        width: 100%;
    }

    .lb-opening-icon {
        font-size: 5rem;
        display: inline-block;
        animation: lb-shake 0.6s ease-in-out;
    }

    .lb-opening-icon.opening {
        animation: lb-open 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes lb-shake {
        0%, 100% { transform: rotate(0deg) scale(1); }
        25% { transform: rotate(-5deg) scale(1.05); }
        50% { transform: rotate(5deg) scale(1.1); }
        75% { transform: rotate(-3deg) scale(1.05); }
    }

    @keyframes lb-open {
        0% { transform: scale(1) rotate(0deg); opacity: 1; }
        40% { transform: scale(1.3) rotate(10deg); opacity: 1; }
        60% { transform: scale(1.5) rotate(-5deg); opacity: 0.8; }
        80% { transform: scale(2) rotate(0deg); opacity: 0.3; }
        100% { transform: scale(0.5) rotate(0deg); opacity: 0; }
    }

    .lb-reward {
        display: none;
        animation: lb-reward-appear 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .lb-reward.visible { display: block; }

    @keyframes lb-reward-appear {
        0% { transform: scale(0) rotate(-180deg); opacity: 0; }
        50% { transform: scale(1.2) rotate(10deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    .lb-reward__icon {
        font-size: 3.5rem;
        margin-bottom: 0.75rem;
        display: inline-block;
    }

    .lb-reward__label {
        font-size: 1.5rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.35rem;
    }

    .lb-reward__sublabel {
        font-size: 0.8125rem;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 1.5rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    html.light-mode .lb-reward__sublabel {
        color: rgba(255, 255, 255, 0.7);
    }

    .lb-reward__close {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 700;
        font-size: 0.8125rem;
        border: none;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #000;
        cursor: pointer;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .lb-reward__close:hover {
        transform: scale(1.04);
        box-shadow: 0 4px 20px rgba(251, 191, 36, 0.3);
    }

    /* Particles */
    .lb-particles {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .lb-particle {
        position: absolute;
        width: 6px; height: 6px;
        border-radius: 50%;
    }

    /* ─── Stagger Animation ──────────────────────── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > .space-y-4 > * {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
        .lb-card__icon { animation: none; }
    }

    /* ─── Responsive ──────────────────────────────── */
    @media (max-width: 480px) {
        .lb-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .lb-card {
            padding: 1.25rem 1rem;
        }

        .lb-card__icon { font-size: 2rem; }
        .lb-card__type { font-size: 0.8125rem; }
        .lb-card__btn { font-size: 0.6875rem; padding: 0.35rem 0.75rem; }
    }
</style>
@endpush

@section('content')
@php
    $unopenedCount = $unopened->count();
    $openedCount = $opened->count();
    $totalXp = $opened->where('reward_type', 'xp')->sum('reward_amount');
    $goldCount = $unopened->where('type', 'gold')->count() + $opened->where('type', 'gold')->count();
@endphp

<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Belohnungen</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#fbbf24,#f59e0b);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Wochenbelohnungen</h1>
        <p class="text-sm" style="color: var(--text-muted);">Belohnungen aus deinen Liga-Platzierungen</p>
    </div>

    {{-- ── Gami Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#fbbf24;-webkit-text-fill-color:#fbbf24;">{{ $unopenedCount }}</div>
            <div class="gami-pill__label">Ungeöffnet</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--success);-webkit-text-fill-color:var(--success);">{{ $openedCount }}</div>
            <div class="gami-pill__label">Eingelöst</div>
        </div>
        @if($totalXp > 0)
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#a855f7;-webkit-text-fill-color:#a855f7;">{{ number_format($totalXp) }}</div>
            <div class="gami-pill__label">XP erhalten</div>
        </div>
        @endif
    </div>

    <div class="space-y-4">

        {{-- ── Unopened Lootboxes ── --}}
        @if($unopenedCount > 0)
        <div>
            <div class="lb-grid">
                @foreach($unopened as $lootbox)
                    <div class="glass lb-card lb-card--{{ $lootbox->type }}" data-lootbox-id="{{ $lootbox->id }}" onclick="openLootbox({{ $lootbox->id }}, '{{ $lootbox->type }}')">
                        <div class="lb-card__icon"><i class="bi bi-gift-fill"></i></div>
                        <div class="lb-card__type">{{ ucfirst($lootbox->type) }}-Belohnung</div>
                        <div class="lb-card__desc">Liga-Platzierung</div>
                        <button class="lb-card__btn" onclick="event.stopPropagation(); openLootbox({{ $lootbox->id }}, '{{ $lootbox->type }}')">Öffnen</button>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="glass p-4 lb-empty" style="border-radius:0.75rem;">
            <div class="lb-empty__icon">
                <i class="bi bi-gift"></i>
            </div>
            <h2 class="lb-empty__title">Keine Belohnungen verfügbar</h2>
            <p class="lb-empty__desc">Erreiche Platz 1–3 in deiner Liga,<br>um Wochenbelohnungen zu erhalten.</p>
        </div>
        @endif

        {{-- ── History ── --}}
        @if($openedCount > 0)
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Eingelöste Belohnungen</span>
                <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $openedCount }} gesamt</span>
            </div>

            @foreach($opened as $lootbox)
                <div class="lb-history-item">
                    <div class="lb-history-badge lb-history-badge--{{ $lootbox->type }}">
                        <i class="bi bi-gift-fill"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="lb-history-reward">
                            @if($lootbox->reward_type === 'xp')
                                {{ number_format($lootbox->reward_amount) }} XP
                            @elseif($lootbox->reward_type === 'streak_freeze')
                                Streak Freeze
                            @elseif($lootbox->reward_type === 'double_xp')
                                Doppel-XP Boost (24h)
                            @endif
                        </div>
                        <div class="lb-history-meta">{{ ucfirst($lootbox->type) }} &middot; {{ $lootbox->opened_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <span class="lb-history-tag lb-history-tag--{{ $lootbox->reward_type }}">
                        @if($lootbox->reward_type === 'xp')
                            XP
                        @elseif($lootbox->reward_type === 'streak_freeze')
                            Freeze
                        @elseif($lootbox->reward_type === 'double_xp')
                            2x XP
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

{{-- ── Opening Overlay ── --}}
<div class="lb-overlay" id="lbOverlay">
    <div class="lb-overlay__content">
        <div id="lbOpeningPhase">
            <div class="lb-opening-icon" id="lbOpeningIcon"><i class="bi bi-gift-fill"></i></div>
            <p style="color:rgba(255,255,255,0.5);margin-top:0.75rem;font-size:0.8125rem;font-family:'IBM Plex Mono',monospace;">Wird geöffnet...</p>
        </div>
        <div class="lb-reward" id="lbRewardPhase">
            <div class="lb-particles" id="lbParticles"></div>
            <div class="lb-reward__icon" id="lbRewardIcon"></div>
            <div class="lb-reward__label" id="lbRewardLabel"></div>
            <div class="lb-reward__sublabel" id="lbRewardSublabel"></div>
            <button class="lb-reward__close" onclick="closeLbOverlay()">Einsammeln</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openLootbox(lootboxId, type) {
        const overlay = document.getElementById('lbOverlay');
        const openingPhase = document.getElementById('lbOpeningPhase');
        const rewardPhase = document.getElementById('lbRewardPhase');
        const openingIcon = document.getElementById('lbOpeningIcon');

        openingPhase.style.display = 'block';
        rewardPhase.classList.remove('visible');
        openingIcon.classList.remove('opening');

        const colors = { gold: '#fbbf24', silber: '#94a3b8', bronze: '#cd7f32' };
        openingIcon.style.color = colors[type] || '#fbbf24';

        overlay.classList.add('active');

        setTimeout(() => {
            openingIcon.classList.add('opening');
        }, 300);

        fetch('{{ route("gamification.lootbox.open") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ lootbox_id: lootboxId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                setTimeout(() => {
                    openingPhase.style.display = 'none';
                    showLbReward(data.reward, type);

                    const card = document.querySelector(`[data-lootbox-id="${lootboxId}"]`);
                    if (card) card.classList.add('opened');

                    const remaining = document.querySelectorAll('.lb-card:not(.opened)');
                    if (remaining.length === 0) {
                        window._reloadAfterClose = true;
                    }
                }, 1500);
            }
        })
        .catch(() => {
            overlay.classList.remove('active');
        });
    }

    function showLbReward(reward, type) {
        const rewardPhase = document.getElementById('lbRewardPhase');
        const rewardIcon = document.getElementById('lbRewardIcon');
        const rewardLabel = document.getElementById('lbRewardLabel');
        const rewardSublabel = document.getElementById('lbRewardSublabel');

        const icons = {
            xp: '<i class="bi bi-star-fill" style="color: #fbbf24;"></i>',
            streak_freeze: '<i class="bi bi-shield-fill" style="color: #60a5fa;"></i>',
            double_xp: '<i class="bi bi-lightning-charge-fill" style="color: #c084fc;"></i>'
        };

        rewardIcon.innerHTML = icons[reward.type] || icons.xp;
        rewardLabel.textContent = reward.label;
        rewardSublabel.textContent = type.charAt(0).toUpperCase() + type.slice(1) + '-Belohnung';

        rewardPhase.classList.add('visible');
        createLbParticles(type);
    }

    function createLbParticles(type) {
        const container = document.getElementById('lbParticles');
        container.innerHTML = '';

        const colors = {
            gold: ['#fbbf24', '#f59e0b', '#fcd34d'],
            silber: ['#94a3b8', '#cbd5e1', '#e2e8f0'],
            bronze: ['#cd7f32', '#a0522d', '#daa520']
        };

        const particleColors = colors[type] || colors.gold;

        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'lb-particle';
            const angle = (Math.PI * 2 / 20) * i;
            const distance = 70 + Math.random() * 50;
            const x = Math.cos(angle) * distance;
            const y = Math.sin(angle) * distance;

            particle.style.background = particleColors[Math.floor(Math.random() * particleColors.length)];

            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${x}px, ${y}px) scale(0.3)`, opacity: 0 }
            ], {
                duration: 1000 + Math.random() * 500,
                easing: 'ease-out',
                delay: Math.random() * 300,
                fill: 'forwards'
            });

            container.appendChild(particle);
        }
    }

    function closeLbOverlay() {
        document.getElementById('lbOverlay').classList.remove('active');
        if (window._reloadAfterClose) {
            location.reload();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLbOverlay();
    });
</script>
@endpush

@endsection
