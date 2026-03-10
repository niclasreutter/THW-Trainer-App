@extends('layouts.app')
@section('title', 'Tagesaufgaben - THW Trainer')

@push('styles')
<style>
    .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .dashboard-header { margin-bottom: 2.5rem; padding-top: 1rem; max-width: 600px; }

    .quest-card {
        padding: 1.5rem; border-radius: 1rem; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 1.25rem;
    }
    .quest-status {
        width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }
    .quest-status.completed { background: rgba(34, 197, 94, 0.15); color: var(--success); }
    .quest-status.pending { background: rgba(255,255,255,0.08); color: var(--text-muted); }
    .quest-info { flex: 1; min-width: 0; }
    .quest-title { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; }
    .quest-progress-bar { height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; }
    .quest-progress-fill { height: 100%; border-radius: 3px; transition: width 0.3s; }
    .quest-meta {
        display: flex; justify-content: space-between; align-items: center; margin-top: 0.35rem;
        font-size: 0.75rem; color: var(--text-muted);
    }
    .quest-xp { font-weight: 700; color: var(--gold); font-size: 0.85rem; white-space: nowrap; }

    .chest-card {
        padding: 2rem; border-radius: 1.5rem 0.5rem 1.5rem 1.5rem; text-align: center; margin-top: 1.5rem;
    }
    .chest-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }

    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Tages<span>aufgaben</span></h1>
        <p class="page-subtitle">Erledige drei Aufgaben, um eine Belohnungstruhe freizuschalten</p>
    </header>

    @foreach($questData['quests'] as $quest)
        @php
            $percent = $quest->target_value > 0 ? min(100, round(($quest->current_value / $quest->target_value) * 100)) : 0;
            $variant = $loop->index % 2 === 0 ? 'glass-tl' : 'glass-br';
        @endphp
        <div class="{{ $variant }} quest-card">
            <div class="quest-status {{ $quest->is_completed ? 'completed' : 'pending' }}">
                @if($quest->is_completed)
                    <i class="bi bi-check-lg"></i>
                @else
                    <i class="bi bi-circle"></i>
                @endif
            </div>
            <div class="quest-info">
                <div class="quest-title">{{ $quest->quest_title }}</div>
                <div class="quest-progress-bar">
                    <div class="quest-progress-fill" style="width: {{ $percent }}%; background: {{ $quest->is_completed ? 'var(--success)' : 'var(--gold)' }};"></div>
                </div>
                <div class="quest-meta">
                    <span>{{ $quest->current_value }}/{{ $quest->target_value }}</span>
                    <span>{{ $percent }}%</span>
                </div>
            </div>
            <span class="quest-xp">+{{ $quest->xp_reward }} XP</span>
        </div>
    @endforeach

    <!-- Chest -->
    @if($questData['chest_available'])
        <div class="glass-gold chest-card">
            <div class="chest-icon"><i class="bi bi-box2-heart-fill" style="color: var(--gold);"></i></div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Belohnungstruhe bereit!</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">Alle Aufgaben erledigt. Hole dir deine Bonuspunkte.</p>
            <button onclick="claimChest()" class="btn-primary">Truhe oeffnen (+{{ \App\Services\DailyQuestService::CHEST_XP }} XP)</button>
        </div>
    @elseif($questData['chest_claimed'])
        <div class="glass-success chest-card">
            <div class="chest-icon"><i class="bi bi-box2-heart-fill" style="color: var(--success);"></i></div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Truhe beansprucht</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Komm morgen wieder fuer neue Aufgaben!</p>
        </div>
    @else
        <div class="glass chest-card" style="opacity: 0.6;">
            <div class="chest-icon"><i class="bi bi-lock-fill" style="color: var(--text-muted);"></i></div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Truhe gesperrt</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Erledige alle drei Aufgaben, um die Truhe freizuschalten.</p>
        </div>
    @endif

    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm">Zurueck</a>
    </div>
</div>

<script>
function claimChest() {
    fetch('{{ route("gamification.claim-chest") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    }).then(r => r.json()).then(data => {
        if (data.success) { location.reload(); }
    });
}
</script>
@endsection
