@props(['instance'])

@php
    $session = $instance->learningSession;
@endphp

<div class="active-session-banner glass-accent" style="
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1.25rem;
    border-radius: 0.75rem;
    gap: 1rem;
"
x-data="{ remaining: {{ $instance->getTimeRemainingSeconds() }} }"
x-init="setInterval(() => { if(remaining > 0) remaining-- }, 1000)"
>
    <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
        <span style="
            display: inline-flex;
            padding: 0.2rem 0.5rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            font-weight: 700;
            background: rgba(34, 197, 94, 0.15);
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.3);
            white-space: nowrap;
        ">LIVE</span>
        <span style="font-size: 0.88rem; font-weight: 600; color: var(--text-primary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
            {{ $session->title }}
        </span>
        <span style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;"
              x-text="Math.floor(remaining/3600) > 0
                  ? Math.floor(remaining/3600) + ':' + String(Math.floor((remaining%3600)/60)).padStart(2,'0') + ':' + String(remaining%60).padStart(2,'0')
                  : Math.floor(remaining/60) + ':' + String(remaining%60).padStart(2,'0')">
        </span>
        <span style="
            display: inline-flex;
            padding: 0.15rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.7rem;
            font-weight: 700;
            background: rgba(251, 191, 36, 0.12);
            color: var(--gold);
            white-space: nowrap;
        ">+50% XP</span>
    </div>
    <a href="{{ route('lernsession.live', $instance) }}" class="btn-primary btn-sm" style="white-space: nowrap;">Teilnehmen</a>
</div>
