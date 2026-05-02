@extends('layouts.app')

@section('title', $ortsverband->name . ' - Mitglieder')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
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
.dash-container > .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
.dash-container > .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }
@media (prefers-reduced-motion: reduce) {
    .dash-container > .space-y-4 > * { animation: none; }
}

.ov-item {
    display: flex; align-items: center; gap: 0.625rem;
    padding: 0.5rem 0.25rem; transition: background 150ms ease; border-radius: 0.375rem;
}
.ov-item:hover { background: rgba(255,255,255,0.03); }
html.light-mode .ov-item:hover { background: rgba(0,0,0,0.03); }
.ov-item + .ov-item { border-top: 1px solid rgba(255,255,255,0.04); }
html.light-mode .ov-item + .ov-item { border-top-color: rgba(0,0,0,0.06); }

.mini-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    min-width: 60px;
}
html.light-mode .mini-stat { background: rgba(0,0,0,0.04); }
.mini-stat-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary);
}
.mini-stat-label {
    font-size: 0.65rem;
    color: var(--text-muted);
    text-transform: uppercase;
}

.members-scroll {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 600px;
    overflow-y: auto;
}

/* ─── Progress Track ─── */
.ov-progress-track-lg {
    height: 4px;
    background: rgba(255,255,255,0.1);
    border-radius: 2px;
    overflow: hidden;
}
html.light-mode .ov-progress-track-lg {
    background: rgba(0,0,0,0.08);
}

@media (max-width: 768px) {
    .member-actions-row {
        flex-wrap: wrap;
    }
    .member-actions-row > div:last-child {
        width: 100%;
        margin-top: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="dash-container">

    <div class="mb-6" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Verwaltung</p>
            <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Mitglieder verwalten</h1>
            <p class="text-sm" style="color: var(--text-muted);">{{ $ortsverband->name }}</p>
        </div>
        <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="btn-primary btn-sm" style="text-decoration:none;flex-shrink:0;">
            Einladen
        </a>
    </div>

    @if(session('success'))
    <div class="glass p-4" style="border-radius:0.75rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;border-left:3px solid #22c55e;">
        <i class="bi bi-check-circle" style="color:#22c55e;font-size:1.1rem;flex-shrink:0;"></i>
        <span style="font-size:0.875rem;color:var(--text-primary);flex:1;">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.25rem;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="glass p-4" style="border-radius:0.75rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;border-left:3px solid #ef4444;">
        <i class="bi bi-exclamation-triangle" style="color:#ef4444;font-size:1.1rem;flex-shrink:0;"></i>
        <span style="font-size:0.875rem;color:var(--text-primary);flex:1;">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.25rem;">&times;</button>
    </div>
    @endif

    <div class="flex gap-3 mb-6" style="flex-wrap: wrap; align-items: center;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $memberProgress->count() }}</div>
            <div class="gami-pill__label">Mitglieder</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $ausbilderProgress->count() }}</div>
            <div class="gami-pill__label">Ausbilder</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Ausbilder Section --}}
        @if($ausbilderProgress->count() > 0)
        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">AUSBILDER</span>

            <div style="margin-top:0.75rem;">
                @foreach($ausbilderProgress as $member)
                <div class="ov-item" style="align-items: start;">
                    <img src="{{ $member['user']->avatar_url }}" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            {{ $member['user']->name }}
                            <span style="font-size:0.6rem;padding:0.125rem 0.375rem;border-radius:9999px;background:rgba(91,154,255,0.15);color:#5b9aff;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">Ausbilder</span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;">
                            {{ $member['user']->email }}
                            @if($member['user']->pivot->joined_at)
                                &middot; Beigetreten: {{ \Carbon\Carbon::parse($member['user']->pivot->joined_at)->format('d.m.Y') }}
                            @endif
                        </div>
                    </div>
                    @if($member['user']->id !== auth()->id())
                    <form action="{{ route('ortsverband.members.role', [$ortsverband, $member['user']]) }}" method="POST" style="flex-shrink:0;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="member">
                        <button type="submit" class="btn-ghost btn-sm" onclick="return confirm('Möchtest du diesen Ausbilder zum Mitglied degradieren?')">
                            Zum Mitglied
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Mitglieder Section --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">MITGLIEDER ({{ $memberProgress->count() }})</span>

            <div class="members-scroll" style="margin-top:0.75rem;">
                @forelse($memberProgress as $member)
                <div class="glass p-4" style="border-radius:0.75rem;">
                    <div class="member-actions-row" style="display: flex; align-items: start; gap: 0.625rem; margin-bottom: 0.75rem;">
                        <img src="{{ $member['user']->avatar_url }}" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem;">
                                {{ $member['user']->name }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;">
                                {{ $member['user']->email }}
                                @if($member['last_activity'])
                                    &middot; Zuletzt: {{ is_string($member['last_activity']) ? \Carbon\Carbon::parse($member['last_activity'])->diffForHumans() : $member['last_activity']->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                            <a href="{{ route('ortsverband.members.manage', [$ortsverband, $member['user']]) }}" class="btn-primary btn-sm" style="text-decoration:none;">
                                Verwalten
                            </a>
                            <form action="{{ route('ortsverband.members.role', [$ortsverband, $member['user']]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role" value="ausbildungsbeauftragter">
                                <button type="submit" class="btn-secondary btn-sm" onclick="return confirm('Möchtest du dieses Mitglied zum Ausbilder befördern?')">
                                    Befördern
                                </button>
                            </form>
                            <form action="{{ route('ortsverband.members.remove', [$ortsverband, $member['user']]) }}" method="POST" onsubmit="return confirm('Möchtest du dieses Mitglied wirklich entfernen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Entfernen</button>
                            </form>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                <span>Theorie</span>
                                <span>{{ $member['theory_progress_count'] }}/{{ $member['theory_progress_total'] }} ({{ $member['theory_progress_percent'] }}%)</span>
                            </div>
                            <div class="ov-progress-track-lg">
                                <div style="height: 100%; background: linear-gradient(135deg,#5b9aff,#0055cc); width: {{ $member['theory_progress_percent'] }}%; border-radius: 2px;"></div>
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                <span>Ausbildung</span>
                                <span>{{ $member['training_progress_done'] }}/{{ $member['training_progress_total'] }} ({{ $member['training_progress_percent'] }}%)</span>
                            </div>
                            <div class="ov-progress-track-lg">
                                <div style="height: 100%; background: linear-gradient(135deg,#d4a017,#8a6d10); width: {{ $member['training_progress_percent'] }}%; border-radius: 2px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ $member['exams_passed'] }}/5</span>
                            <span class="mini-stat-label">Prüfungen</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ $member['streak'] }}</span>
                            <span class="mini-stat-label">Streak</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ $member['level'] }}</span>
                            <span class="mini-stat-label">Level</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ number_format($member['points']) }}</span>
                            <span class="mini-stat-label">Punkte</span>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 2rem;">
                    <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Keine Mitglieder</p>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">Lade Mitglieder über Einladungslinks ein.</p>
                    <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="btn-primary btn-sm">Einladung erstellen</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Back Link --}}
        <div style="text-align: center; padding-top: 0.5rem;">
            <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" class="btn-ghost btn-sm">
                Zurück zum Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
