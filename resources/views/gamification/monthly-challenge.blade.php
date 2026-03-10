@extends('layouts.app')
@section('title', 'Monats-Challenge - THW Trainer')

@push('styles')
<style>
    .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .dashboard-header { margin-bottom: 2.5rem; padding-top: 1rem; max-width: 600px; }

    .challenge-hero { padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 1.5rem; margin-bottom: 2rem; }
    .challenge-title { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem; }
    .challenge-meta { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.5rem; }

    .milestone-track {
        position: relative; padding: 2rem 0;
    }
    .milestone-bar {
        height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; position: relative; overflow: visible;
    }
    .milestone-fill {
        height: 100%; border-radius: 4px; transition: width 0.5s;
        background: linear-gradient(90deg, var(--gold), #f59e0b);
    }
    .milestone-marker {
        position: absolute; top: -10px; width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700;
        transform: translateX(-50%); cursor: pointer; transition: all 0.2s;
    }
    .milestone-marker.locked { background: rgba(255,255,255,0.1); color: var(--text-muted); border: 2px solid rgba(255,255,255,0.1); }
    .milestone-marker.claimable { background: var(--gold); color: #000; border: 2px solid var(--gold); animation: pulse 1.5s infinite; }
    .milestone-marker.claimed { background: var(--success); color: #fff; border: 2px solid var(--success); }

    .milestone-labels { display: flex; justify-content: space-between; margin-top: 1rem; padding: 0 0.5rem; }
    .milestone-label { font-size: 0.7rem; color: var(--text-muted); text-align: center; }
    .milestone-label .xp { font-weight: 700; color: var(--gold); }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(251, 191, 36, 0); }
    }

    .history-card { padding: 1rem 1.25rem; margin-bottom: 0.5rem; border-radius: 0.75rem; display: flex; justify-content: space-between; align-items: center; }

    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Monats-<span>Challenge</span></h1>
        <p class="page-subtitle">Erreiche Meilensteine und sammle Bonuspunkte</p>
    </header>

    @php
        $challenge = $challengeData['challenge'];
        $percent = $challengeData['progress_percent'];
        $claimable = $challengeData['claimable_milestones'];
        $milestoneRewards = \App\Services\MonthlyChallengeService::MILESTONE_REWARDS;
    @endphp

    <div class="glass-gold challenge-hero">
        <div class="challenge-title">{{ $challenge->challenge_title }}</div>
        <div class="challenge-meta">
            {{ $challenge->current_value }}/{{ $challenge->target_value }} &middot;
            {{ $challengeData['days_remaining'] }} Tage verbleibend
        </div>

        <!-- Milestone Track -->
        <div class="milestone-track">
            <div class="milestone-bar">
                <div class="milestone-fill" style="width: {{ $percent }}%;"></div>

                @foreach($milestoneRewards as $ms => $xp)
                    @php
                        $field = "milestone_{$ms}_claimed";
                        $isClaimed = $challenge->$field;
                        $isClaimable = in_array($ms, $claimable);
                        $isLocked = !$isClaimed && !$isClaimable;
                        $state = $isClaimed ? 'claimed' : ($isClaimable ? 'claimable' : 'locked');
                    @endphp
                    <div class="milestone-marker {{ $state }}" style="left: {{ $ms }}%;"
                         @if($isClaimable) onclick="claimMilestone({{ $ms }})" title="Klicken zum Einloesen" @endif>
                        @if($isClaimed) <i class="bi bi-check"></i>
                        @else {{ $ms }}% @endif
                    </div>
                @endforeach
            </div>

            <div class="milestone-labels">
                @foreach($milestoneRewards as $ms => $xp)
                    <div class="milestone-label">
                        <div>{{ $ms }}%</div>
                        <div class="xp">+{{ $xp }} XP</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Progress text -->
        <div style="text-align: center; margin-top: 1rem;">
            <span style="font-size: 2rem; font-weight: 800; color: var(--gold);">{{ $percent }}%</span>
        </div>
    </div>

    <!-- History -->
    @if($history->isNotEmpty())
    <div style="margin-top: 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Vergangene Challenges</h3>
        @foreach($history as $past)
            @php
                $pastPercent = $past->target_value > 0 ? min(100, round(($past->current_value / $past->target_value) * 100)) : 0;
            @endphp
            <div class="glass history-card">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ $past->challenge_title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ \Carbon\Carbon::createFromFormat('Y-m', $past->challenge_month)->translatedFormat('F Y') }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.9rem; font-weight: 700; color: {{ $past->is_completed ? 'var(--success)' : 'var(--text-muted)' }};">
                        {{ $pastPercent }}%
                    </div>
                    <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $past->current_value }}/{{ $past->target_value }}</div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm">Zurueck</a>
    </div>
</div>

<script>
function claimMilestone(milestone) {
    fetch('{{ route("gamification.claim-milestone") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify({ milestone: milestone })
    }).then(r => r.json()).then(data => {
        if (data.success) { location.reload(); }
    });
}
</script>
@endsection
