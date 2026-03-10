@extends('layouts.app')
@section('title', 'Lootboxen - THW Trainer')

@push('styles')
<style>
    .dashboard-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Lootbox Grid */
    .lootbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .lootbox-card {
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .lootbox-card:hover {
        transform: translateY(-4px) scale(1.02);
    }

    .lootbox-card.type-gold {
        border: 1px solid rgba(251, 191, 36, 0.3);
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(251, 191, 36, 0.03) 100%);
    }

    .lootbox-card.type-silber {
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.1) 0%, rgba(148, 163, 184, 0.03) 100%);
    }

    .lootbox-card.type-bronze {
        border: 1px solid rgba(205, 127, 50, 0.3);
        background: linear-gradient(135deg, rgba(205, 127, 50, 0.1) 0%, rgba(205, 127, 50, 0.03) 100%);
    }

    .lootbox-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        display: inline-block;
        animation: lootbox-float 3s ease-in-out infinite;
    }

    .type-gold .lootbox-icon { color: #fbbf24; }
    .type-silber .lootbox-icon { color: #94a3b8; }
    .type-bronze .lootbox-icon { color: #cd7f32; }

    @keyframes lootbox-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .lootbox-type {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .lootbox-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .open-btn {
        margin-top: 1rem;
        padding: 0.6rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .type-gold .open-btn {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #000;
    }

    .type-silber .open-btn {
        background: linear-gradient(135deg, #94a3b8, #64748b);
        color: #fff;
    }

    .type-bronze .open-btn {
        background: linear-gradient(135deg, #cd7f32, #a0522d);
        color: #fff;
    }

    .open-btn:hover {
        transform: scale(1.05);
    }

    /* Lootbox Opening Overlay */
    .lootbox-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.85);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(8px);
    }

    .lootbox-overlay.active {
        display: flex;
    }

    .lootbox-animation {
        text-align: center;
        position: relative;
    }

    .lootbox-opening-icon {
        font-size: 6rem;
        display: inline-block;
        animation: lootbox-shake 0.6s ease-in-out;
    }

    .lootbox-opening-icon.opening {
        animation: lootbox-open 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes lootbox-shake {
        0%, 100% { transform: rotate(0deg) scale(1); }
        25% { transform: rotate(-5deg) scale(1.05); }
        50% { transform: rotate(5deg) scale(1.1); }
        75% { transform: rotate(-3deg) scale(1.05); }
    }

    @keyframes lootbox-open {
        0% { transform: scale(1) rotate(0deg); opacity: 1; }
        40% { transform: scale(1.3) rotate(10deg); opacity: 1; }
        60% { transform: scale(1.5) rotate(-5deg); opacity: 0.8; }
        80% { transform: scale(2) rotate(0deg); opacity: 0.3; }
        100% { transform: scale(0.5) rotate(0deg); opacity: 0; }
    }

    .reward-reveal {
        display: none;
        animation: reward-appear 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .reward-reveal.visible {
        display: block;
    }

    @keyframes reward-appear {
        0% { transform: scale(0) rotate(-180deg); opacity: 0; }
        50% { transform: scale(1.2) rotate(10deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    .reward-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        display: inline-block;
    }

    .reward-label {
        font-size: 1.8rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .reward-sublabel {
        font-size: 1rem;
        color: var(--text-secondary);
        margin-bottom: 2rem;
    }

    .reward-close-btn {
        padding: 0.75rem 2rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .reward-close-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* Particles */
    .particles {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .particle {
        position: absolute;
        width: 8px; height: 8px;
        border-radius: 50%;
        animation: particle-fly 1s ease-out forwards;
    }

    @keyframes particle-fly {
        0% { transform: translate(0, 0) scale(1); opacity: 1; }
        100% { opacity: 0; }
    }

    /* History Section */
    .history-section {
        margin-top: 3rem;
    }

    .history-section h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }

    .history-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .history-info {
        flex: 1;
    }

    .history-reward {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .history-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .empty-lootbox-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-lootbox-icon {
        font-size: 4rem;
        color: var(--text-muted);
        opacity: 0.4;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .lootbox-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title"><span>Lootboxen</span></h1>
        <p class="page-subtitle">Öffne deine Belohnungen aus den wöchentlichen Liga-Platzierungen</p>
    </header>

    @if($unopened->count() > 0)
        <div class="lootbox-grid">
            @foreach($unopened as $lootbox)
                <div class="glass lootbox-card type-{{ $lootbox->type }}" data-lootbox-id="{{ $lootbox->id }}">
                    <div class="lootbox-icon"><i class="bi bi-gift-fill"></i></div>
                    <div class="lootbox-type">{{ ucfirst($lootbox->type) }}-Lootbox</div>
                    <div class="lootbox-desc">Platzierung in der Liga</div>
                    <button class="open-btn" onclick="openLootbox({{ $lootbox->id }}, '{{ $lootbox->type }}')">Öffnen</button>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass empty-lootbox-state">
            <div class="empty-lootbox-icon"><i class="bi bi-gift"></i></div>
            <p style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Keine Lootboxen verfügbar</p>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Erreiche Platz 1-3 in deiner Liga, um Lootboxen zu erhalten!</p>
        </div>
    @endif

    @if($opened->count() > 0)
        <div class="history-section">
            <h2>Geöffnete Lootboxen</h2>
            <div class="glass">
                @foreach($opened as $lootbox)
                    <div class="history-item">
                        <div class="history-icon">
                            @if($lootbox->type === 'gold')
                                <i class="bi bi-gift-fill" style="color: #fbbf24;"></i>
                            @elseif($lootbox->type === 'silber')
                                <i class="bi bi-gift-fill" style="color: #94a3b8;"></i>
                            @else
                                <i class="bi bi-gift-fill" style="color: #cd7f32;"></i>
                            @endif
                        </div>
                        <div class="history-info">
                            <div class="history-reward">
                                @if($lootbox->reward_type === 'xp')
                                    {{ number_format($lootbox->reward_amount) }} XP
                                @elseif($lootbox->reward_type === 'streak_freeze')
                                    Streak Freeze
                                @elseif($lootbox->reward_type === 'double_xp')
                                    Doppel-XP Boost (24h)
                                @endif
                            </div>
                            <div class="history-meta">{{ ucfirst($lootbox->type) }}-Lootbox &middot; {{ $lootbox->opened_at->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Opening Animation Overlay -->
<div class="lootbox-overlay" id="lootboxOverlay">
    <div class="lootbox-animation">
        <div id="openingPhase">
            <div class="lootbox-opening-icon" id="openingIcon"><i class="bi bi-gift-fill"></i></div>
            <p style="color: var(--text-secondary); margin-top: 1rem; font-size: 0.9rem;">Wird geöffnet...</p>
        </div>
        <div class="reward-reveal" id="rewardPhase">
            <div class="particles" id="particles"></div>
            <div class="reward-icon" id="rewardIcon"></div>
            <div class="reward-label" id="rewardLabel"></div>
            <div class="reward-sublabel" id="rewardSublabel"></div>
            <button class="reward-close-btn" onclick="closeOverlay()">Einsammeln</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openLootbox(lootboxId, type) {
        const overlay = document.getElementById('lootboxOverlay');
        const openingPhase = document.getElementById('openingPhase');
        const rewardPhase = document.getElementById('rewardPhase');
        const openingIcon = document.getElementById('openingIcon');

        // Reset
        openingPhase.style.display = 'block';
        rewardPhase.classList.remove('visible');
        openingIcon.classList.remove('opening');

        // Set color
        const colors = { gold: '#fbbf24', silber: '#94a3b8', bronze: '#cd7f32' };
        openingIcon.style.color = colors[type] || '#fbbf24';

        overlay.classList.add('active');

        // Start shake
        setTimeout(() => {
            openingIcon.classList.add('opening');
        }, 300);

        // Send request
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
                    showReward(data.reward, type);

                    // Hide the card
                    const card = document.querySelector(`[data-lootbox-id="${lootboxId}"]`);
                    if (card) card.style.display = 'none';

                    // Check if all lootboxes opened
                    const remaining = document.querySelectorAll('.lootbox-card[style*="display: none"]');
                    const total = document.querySelectorAll('.lootbox-card');
                    if (remaining.length === total.length) {
                        setTimeout(() => location.reload(), 1500);
                    }
                }, 1500);
            }
        })
        .catch(() => {
            overlay.classList.remove('active');
        });
    }

    function showReward(reward, type) {
        const rewardPhase = document.getElementById('rewardPhase');
        const rewardIcon = document.getElementById('rewardIcon');
        const rewardLabel = document.getElementById('rewardLabel');
        const rewardSublabel = document.getElementById('rewardSublabel');

        const icons = {
            xp: '<i class="bi bi-star-fill" style="color: #fbbf24;"></i>',
            streak_freeze: '<i class="bi bi-shield-fill" style="color: #60a5fa;"></i>',
            double_xp: '<i class="bi bi-lightning-charge-fill" style="color: #c084fc;"></i>'
        };

        rewardIcon.innerHTML = icons[reward.type] || icons.xp;
        rewardLabel.textContent = reward.label;
        rewardSublabel.textContent = ucfirst(type) + '-Lootbox';

        rewardPhase.classList.add('visible');
        createParticles(type);
    }

    function createParticles(type) {
        const container = document.getElementById('particles');
        container.innerHTML = '';

        const colors = {
            gold: ['#fbbf24', '#f59e0b', '#fcd34d'],
            silber: ['#94a3b8', '#cbd5e1', '#e2e8f0'],
            bronze: ['#cd7f32', '#a0522d', '#daa520']
        };

        const particleColors = colors[type] || colors.gold;

        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            const angle = (Math.PI * 2 / 20) * i;
            const distance = 80 + Math.random() * 60;
            const x = Math.cos(angle) * distance;
            const y = Math.sin(angle) * distance;

            particle.style.background = particleColors[Math.floor(Math.random() * particleColors.length)];
            particle.style.animation = `particle-fly 1s ease-out ${Math.random() * 0.3}s forwards`;
            particle.style.setProperty('--x', x + 'px');
            particle.style.setProperty('--y', y + 'px');

            // Override animation with custom end position
            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${x}px, ${y}px) scale(0.3)`, opacity: 0 }
            ], {
                duration: 1000 + Math.random() * 500,
                easing: 'ease-out',
                fill: 'forwards'
            });

            container.appendChild(particle);
        }
    }

    function closeOverlay() {
        document.getElementById('lootboxOverlay').classList.remove('active');
    }

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeOverlay();
    });
</script>
@endpush

@endsection
