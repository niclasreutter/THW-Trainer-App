@extends('layouts.app')

@section('title', $ortsverband->name . ' - Dashboard')

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
    .dash-container > .space-y-4 > *:nth-child(8) { animation-delay: 0.31s; }
    .dash-container > .space-y-4 > *:nth-child(9) { animation-delay: 0.35s; }

    /* ─── Module Completion Rows ─── */
    .ov-mod-row {
        display: grid;
        grid-template-columns: 1fr 90px auto;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
    .ov-mod-row + .ov-mod-row { border-top: 1px solid rgba(255,255,255,0.04); }
    html.light-mode .ov-mod-row + .ov-mod-row { border-top-color: rgba(0,0,0,0.06); }
    .ov-mod-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ov-mod-sub {
        font-size: 0.5625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ov-mod-count {
        font-size: 0.8125rem;
        font-weight: 700;
        font-family: 'Barlow Condensed', sans-serif;
        min-width: 2.75rem;
        text-align: right;
    }
    .ov-mod-group-title {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 600;
        font-family: 'IBM Plex Mono', monospace;
        margin: 0.75rem 0 0.5rem;
    }
    .ov-mod-group-title:first-child { margin-top: 0; }

    @media (max-width: 640px) {
        .ov-mod-row {
            grid-template-columns: 1fr auto;
            row-gap: 0.375rem;
        }
        .ov-mod-row > :first-child {
            grid-column: 1 / -1;
        }
        .ov-mod-label,
        .ov-mod-sub {
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
        }
    }

    /* ─── Side-by-side wrapper ─── */
    .ov-side-by-side {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        align-items: stretch;
    }
    @media (min-width: 1024px) {
        .ov-side-by-side {
            grid-template-columns: 1fr 1fr;
        }
    }
    .ov-side-by-side > .glass {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .ov-side-scroll {
        flex: 1;
        min-height: 0;
        max-height: 560px;
        overflow-y: auto;
    }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    /* ─── Coverage Rows ─── */
    .ov-cov-row {
        display: grid;
        grid-template-columns: 130px 1fr auto;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
    .ov-cov-row + .ov-cov-row { border-top: 1px solid rgba(255,255,255,0.04); }
    html.light-mode .ov-cov-row + .ov-cov-row { border-top-color: rgba(0,0,0,0.06); }
    .ov-cov-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ov-cov-sub {
        font-size: 0.5625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }
    .ov-cov-value {
        font-size: 0.9375rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        min-width: 2.75rem;
        text-align: right;
    }

    /* ─── Ranking Items ─── */
    .ov-rank-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.375rem;
        transition: background 150ms ease;
        border-radius: 0.5rem;
    }
    .ov-rank-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .ov-rank-item:hover { background: rgba(0,0,0,0.03); }
    .ov-rank-item + .ov-rank-item { border-top: 1px solid rgba(255,255,255,0.04); }
    html.light-mode .ov-rank-item + .ov-rank-item { border-top-color: rgba(0,0,0,0.06); }

    /* ─── Weakness Items ─── */
    .ov-weak-item {
        padding: 0.625rem;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
    }
    .ov-weak-item + .ov-weak-item { margin-top: 0.5rem; }
    html.light-mode .ov-weak-item {
        background: rgba(0,0,0,0.02);
        border-color: rgba(0,0,0,0.06);
    }

    /* ─── Toggle Pills ─── */
    .ov-toggle-wrap {
        display: flex;
        gap: 0.25rem;
        background: rgba(255,255,255,0.06);
        padding: 0.2rem;
        border-radius: 0.375rem;
    }
    html.light-mode .ov-toggle-wrap { background: rgba(0,0,0,0.04); }

    /* ─── Quick Access Tiles ─── */
    .ov-quick-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.375rem;
        padding: 1rem 0.75rem;
        border-radius: 0.625rem;
        text-decoration: none;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        transition: background 150ms, border-color 150ms, transform 200ms;
    }
    .ov-quick-tile:hover {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.1);
        transform: translateY(-1px);
        text-decoration: none;
    }
    html.light-mode .ov-quick-tile {
        background: rgba(0,51,127,0.02);
        border-color: rgba(0,51,127,0.06);
    }
    html.light-mode .ov-quick-tile:hover {
        background: rgba(0,51,127,0.05);
    }

    /* ─── Progress Track ─── */
    .ov-progress-track {
        height: 3px;
        background: rgba(255,255,255,0.06);
        border-radius: 2px;
        overflow: hidden;
    }
    html.light-mode .ov-progress-track {
        background: rgba(0,0,0,0.08);
    }

    /* ─── Alert ─── */
    .ov-alert {
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin-bottom: 0.75rem;
    }

    /* ─── Dashboard Header ─── */
    .ov-dash-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    /* ─── Member Search ─── */
    .ov-member-search {
        position: relative;
        width: 100%;
        max-width: 280px;
        margin-left: auto;
    }
    @media (max-width: 640px) {
        .ov-member-search { max-width: 100%; }
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
</style>
@endpush

@php
    $searchableMembers = $memberProgress->map(fn($m) => [
        'id' => $m['user']->id,
        'name' => $m['user']->name,
        'avatar' => $m['user']->avatar_url,
        'role' => $m['role'],
        'url' => route('ortsverband.members.manage', [$ortsverband, $m['user']]),
    ])->values();
@endphp
@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="ov-dash-header">
        <div>
            <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Ausbilder</p>
            <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $ortsverband->name }}</h1>
            <p class="text-sm" style="color: var(--text-muted);">Überblick über den Lernfortschritt deiner Mitglieder</p>
        </div>

        <div
            class="ov-member-search"
            x-data="ovMemberSearch({{ $searchableMembers->toJson() }})"
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
    </div>

    {{-- ── Stat Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $stats['total_members'] ?? 0 }}</div>
            <div class="gami-pill__label">Mitglieder</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--success);-webkit-text-fill-color:var(--success);">{{ $stats['active_members'] ?? 0 }}</div>
            <div class="gami-pill__label">Aktiv (7 Tage)</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#a855f7;-webkit-text-fill-color:#a855f7;">{{ $stats['avg_theory'] ?? 0 }}%</div>
            <div class="gami-pill__label">Theorie</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--warning);-webkit-text-fill-color:var(--warning);">{{ $stats['avg_exams'] ?? 0 }}</div>
            <div class="gami-pill__label">Prüfungen</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--error);-webkit-text-fill-color:var(--error);">{{ $stats['avg_streak'] ?? 0 }}</div>
            <div class="gami-pill__label">Streak</div>
        </div>
    </div>

    @if(session('success'))
    <div class="ov-alert glass-success">
        <i class="bi bi-check-circle" style="font-size:1rem;flex-shrink:0;"></i>
        <span style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);flex:1;">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:1.125rem;line-height:1;">&times;</button>
    </div>
    @endif

    <div class="space-y-4">

        {{-- ── Rangliste + Ausbildungsmodule (side-by-side auf lg+) ── --}}
        <div class="ov-side-by-side">

        {{-- ── Rangliste ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Rangliste</span>
                <div class="ov-toggle-wrap">
                    <form action="{{ route('ortsverband.toggle-ranking', $ortsverband) }}" method="POST" style="margin:0;display:inline;">
                        @csrf
                        @if(!$ortsverband->ranking_visible)
                            <button type="submit" class="btn-ghost btn-sm" style="padding:0.3rem 0.625rem;font-size:0.6875rem;">Alle</button>
                        @else
                            <span class="btn-primary btn-sm" style="padding:0.3rem 0.625rem;font-size:0.6875rem;">Alle</span>
                        @endif
                    </form>
                    <form action="{{ route('ortsverband.toggle-ranking', $ortsverband) }}" method="POST" style="margin:0;display:inline;">
                        @csrf
                        @if($ortsverband->ranking_visible)
                            <button type="submit" class="btn-ghost btn-sm" style="padding:0.3rem 0.625rem;font-size:0.6875rem;">Nur Ausbilder</button>
                        @else
                            <span class="btn-primary btn-sm" style="padding:0.3rem 0.625rem;font-size:0.6875rem;">Nur Ausbilder</span>
                        @endif
                    </form>
                </div>
            </div>

            <div class="ov-side-scroll">
                @forelse($memberProgress->take(10) as $index => $member)
                @php
                    $mUser = $member['user'];
                    $mHasGlow = $mUser->glowing_name_until && \Carbon\Carbon::parse($mUser->glowing_name_until)->isFuture();
                    $mHasRankColor = $mUser->rank_color_until && \Carbon\Carbon::parse($mUser->rank_color_until)->isFuture() && $mUser->rank_color;
                    $mHasFrame = $mUser->profile_frame_until && \Carbon\Carbon::parse($mUser->profile_frame_until)->isFuture();
                    $mHasTitle = $mUser->active_title_until && \Carbon\Carbon::parse($mUser->active_title_until)->isFuture() && $mUser->active_title;
                    $mNameClasses = '';
                    if ($mHasGlow) $mNameClasses .= $mUser->glowing_name_type === 'shimmer' ? ' glowing-name-shimmer' : ' glowing-name-static';
                    if ($mHasRankColor) $mNameClasses .= ' rank-color-' . $mUser->rank_color;
                    $mFrameClass = '';
                    if ($mHasFrame) $mFrameClass = ($mUser->profile_frame_type ?? 'gold') === 'diamond' ? 'profile-frame-diamond' : 'profile-frame-gold';
                    $rankColor = $index === 0 ? '#fbbf24' : ($index === 1 ? '#94a3b8' : ($index === 2 ? '#cd7f32' : 'var(--text-muted)'));
                @endphp
                <div class="ov-rank-item {{ $mFrameClass }}">
                    <div style="font-size:1.125rem;min-width:1.75rem;text-align:center;font-weight:800;color:{{ $rankColor }};font-family:'Barlow Condensed',sans-serif;">{{ $index + 1 }}</div>
                    <img src="{{ $mUser->avatar_url }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:0.375rem;margin-bottom:0.15rem;">
                            @if($mUser->id !== auth()->id())
                                <a href="{{ route('ortsverband.members.manage', [$ortsverband, $mUser]) }}" class="{{ trim($mNameClasses) }}" style="font-weight:700;font-size:0.8125rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-decoration:none;">
                                    {{ $mUser->name }}
                                </a>
                            @else
                                <span class="{{ trim($mNameClasses) }}" style="font-weight:700;font-size:0.8125rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $mUser->name }}
                                </span>
                            @endif
                            @if($member['role'] === 'ausbildungsbeauftragter')
                                <span style="font-size:0.5rem;padding:0.1rem 0.35rem;border-radius:2rem;background:rgba(91,154,255,0.12);color:#5b9aff;font-weight:700;font-family:'IBM Plex Mono',monospace;white-space:nowrap;">Ausbilder</span>
                            @endif
                        </div>
                        @if($mHasTitle)
                            <div class="user-title" style="font-size:0.625rem;margin-bottom:0.15rem;">{{ $mUser->active_title }}</div>
                        @endif
                        <div style="display:flex;gap:0.75rem;font-size:0.6875rem;color:var(--text-muted);flex-wrap:wrap;">
                            <span>Theorie {{ $member['theory_progress_percent'] }}%</span>
                            <span>Ausbildung {{ $member['training_progress_percent'] }}%</span>
                            <span>{{ $member['exams_passed'] }}/5 Prüf.</span>
                            <span>{{ $member['streak'] }}d Streak</span>
                            <span>Lvl {{ $member['level'] }}</span>
                        </div>
                        <div style="display:flex;gap:0.5rem;margin-top:0.375rem;">
                            <div class="ov-progress-track" style="flex:1;" title="Theorie-Fortschritt">
                                <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,#0055cc,#5b9aff);width:{{ $member['theory_progress_percent'] }}%;transition:width 0.6s ease-out;"></div>
                            </div>
                            <div class="ov-progress-track" style="flex:1;" title="Ausbildungsfortschritt">
                                <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,#8a6d10,#d4a017);width:{{ $member['training_progress_percent'] }}%;transition:width 0.6s ease-out;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Keine Mitglieder</div>
                    <p style="font-size:0.75rem;color:var(--text-muted);">Lade Mitglieder ein, um ihre Fortschritte zu verfolgen</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Ausbildungsmodule ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Ausbildungsmodule</span>
                <span style="font-size:0.5625rem;color:var(--text-muted);font-family:'IBM Plex Mono',monospace;">{{ $moduleCompletion['members_total'] }} Mitglieder</span>
            </div>

            @if($moduleCompletion['members_total'] === 0)
                <div style="text-align:center;padding:1.5rem 1rem;">
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Keine Mitglieder</div>
                    <p style="font-size:0.75rem;color:var(--text-muted);">Sobald Mitglieder im Ortsverband sind, erscheint hier der Ausbildungsstand.</p>
                </div>
            @else
                <div class="ov-side-scroll">
                    <div class="ov-mod-group-title">Lernabschnitte</div>
                    @foreach($moduleCompletion['lernabschnitte'] as $entry)
                        @php
                            $pct = $entry['percent'];
                            $color = $pct >= 80 ? '#22c55e' : ($pct >= 40 ? '#5b9aff' : ($pct >= 20 ? '#f59e0b' : '#ef4444'));
                        @endphp
                        <div class="ov-mod-row">
                            <div style="min-width:0;">
                                <div class="ov-mod-label">{{ $entry['nr'] }} · {{ $entry['title'] }}</div>
                                <div class="ov-mod-sub">{{ $pct }}% abgeschlossen</div>
                            </div>
                            <div class="ov-progress-track">
                                <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,#0055cc,#5b9aff);width:{{ $pct }}%;transition:width 0.6s ease-out;"></div>
                            </div>
                            <div class="ov-mod-count" style="color:{{ $color }};">{{ $entry['completed_count'] }}/{{ $entry['total_members'] }}</div>
                        </div>
                    @endforeach

                    <div class="ov-mod-group-title">Zusatzausbildungen</div>
                    @foreach($moduleCompletion['zusatz'] as $entry)
                        @php
                            $pct = $entry['percent'];
                            $color = $pct >= 80 ? '#22c55e' : ($pct >= 40 ? '#5b9aff' : ($pct >= 20 ? '#f59e0b' : '#ef4444'));
                        @endphp
                        <div class="ov-mod-row">
                            <div style="min-width:0;">
                                <div class="ov-mod-label">{{ $entry['label'] }}</div>
                                <div class="ov-mod-sub">{{ $pct }}% abgeschlossen</div>
                            </div>
                            <div class="ov-progress-track">
                                <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,#8a6d10,#d4a017);width:{{ $pct }}%;transition:width 0.6s ease-out;"></div>
                            </div>
                            <div class="ov-mod-count" style="color:{{ $color }};">{{ $entry['completed_count'] }}/{{ $entry['total_members'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        </div>
        {{-- /side-by-side --}}

        {{-- ── Schwachstellen + Themenabdeckung (side-by-side auf lg+) ── --}}
        <div class="ov-side-by-side">

        {{-- ── Schwachstellen ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Schwachstellen</span>
            </div>

            @if($weaknesses['weak_sections']->isNotEmpty())
                <div style="margin-bottom:1rem;">
                    <div style="font-size:0.5625rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.5rem;font-family:'IBM Plex Mono',monospace;">Schwierigste Abschnitte</div>
                    @php
                        $sectionNames = [
                            1 => 'THW im Gefüge',
                            2 => 'Rettung & Bergung',
                            3 => 'Leinen & Seile',
                            4 => 'Holz/Gestein/Metall',
                            5 => 'Leitern',
                            6 => 'Strom & Licht',
                            7 => 'Wasser',
                            8 => 'Einsatzgrundlagen',
                            9 => 'Wasser (erw.)',
                            10 => 'Rettung (Grundl.)'
                        ];
                    @endphp
                    @foreach($weaknesses['weak_sections']->take(3) as $section)
                    <div class="ov-weak-item">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-weight:600;font-size:0.8125rem;color:var(--text-primary);">LA {{ $section['section'] }}</div>
                                <div style="font-size:0.625rem;color:var(--text-muted);">{{ $section['total_attempts'] }} Versuche</div>
                            </div>
                            <div style="font-size:1rem;font-weight:800;color:#f59e0b;font-family:'Barlow Condensed',sans-serif;">{{ $section['success_rate'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            @if($weaknesses['common_errors']->isNotEmpty())
                <div>
                    <div style="font-size:0.5625rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.5rem;font-family:'IBM Plex Mono',monospace;">Häufigste Fehler</div>
                    @foreach($weaknesses['common_errors']->take(3) as $error)
                        @if($error['question'])
                        <div class="ov-weak-item" style="border-left:2px solid #ef4444;">
                            <div style="display:flex;justify-content:space-between;align-items:start;gap:0.5rem;">
                                <div style="font-size:0.75rem;color:var(--text-primary);flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ Str::limit($error['question']->frage, 60) }}
                                </div>
                                <div style="font-size:0.875rem;font-weight:800;color:#ef4444;white-space:nowrap;font-family:'Barlow Condensed',sans-serif;">{{ $error['error_count'] }}x</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($weaknesses['weak_sections']->isEmpty() && $weaknesses['common_errors']->isEmpty())
            <div style="text-align:center;padding:1.5rem 1rem;">
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Noch keine Daten</div>
                <p style="font-size:0.75rem;color:var(--text-muted);">Noch keine Daten verfügbar</p>
            </div>
            @endif
        </div>

        {{-- ── Themenabdeckung ── --}}
        @php
            $sectionNames = [
                1 => 'THW im Gefüge',
                2 => 'Rettung & Bergung',
                3 => 'Leinen & Seile',
                4 => 'Holz/Gestein/Metall',
                5 => 'Leitern',
                6 => 'Strom & Licht',
                7 => 'Wasser',
                8 => 'Einsatzgrundlagen',
                9 => 'Wasser (erw.)',
                10 => 'Rettung (Grundl.)',
            ];
        @endphp
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Themenabdeckung</span>
                <span style="font-size:0.5625rem;color:var(--text-muted);font-family:'IBM Plex Mono',monospace;">Wenig behandelt zuerst</span>
            </div>

            @forelse($topicCoverage as $entry)
                @php
                    $label = $sectionNames[$entry['section']] ?? ('LA ' . $entry['section']);
                    $pct = $entry['avg_coverage_percent'];
                    $valueColor = $pct < 20 ? '#ef4444' : ($pct < 50 ? '#f59e0b' : '#22c55e');
                @endphp
                <div class="ov-cov-row">
                    <div>
                        <div class="ov-cov-label">LA {{ $entry['section'] }} · {{ $label }}</div>
                        <div class="ov-cov-sub">{{ $entry['members_touched'] }}/{{ $entry['total_members'] }} aktiv · {{ $entry['total_questions'] }} Fragen</div>
                    </div>
                    <div class="ov-progress-track">
                        <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,#0055cc,#5b9aff);width:{{ $pct }}%;transition:width 0.6s ease-out;"></div>
                    </div>
                    <div class="ov-cov-value" style="color:{{ $valueColor }};">{{ $pct }}%</div>
                </div>
            @empty
                <div style="text-align:center;padding:1.5rem 1rem;">
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Noch keine Daten</div>
                    <p style="font-size:0.75rem;color:var(--text-muted);">Sobald Mitglieder Fragen beantworten, erscheint hier die Themenabdeckung.</p>
                </div>
            @endforelse
        </div>

        </div>
        {{-- /side-by-side --}}

        {{-- ── Schnellzugriff ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Schnellzugriff</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(120px, 1fr));gap:0.5rem;">
                <a href="{{ route('ortsverband.members', $ortsverband) }}" class="ov-quick-tile">
                    <i class="bi bi-people" style="font-size:1.25rem;color:#5b9aff;"></i>
                    <span style="font-weight:600;color:var(--text-primary);font-size:0.75rem;">Mitglieder</span>
                </a>
                <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="ov-quick-tile">
                    <i class="bi bi-collection" style="font-size:1.25rem;color:#5b9aff;"></i>
                    <span style="font-weight:600;color:var(--text-primary);font-size:0.75rem;">Lernpools</span>
                </a>
                <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="ov-quick-tile">
                    <i class="bi bi-link-45deg" style="font-size:1.25rem;color:#5b9aff;"></i>
                    <span style="font-weight:600;color:var(--text-primary);font-size:0.75rem;">Einladungen</span>
                </a>
                <a href="{{ route('ortsverband.edit', $ortsverband) }}" class="ov-quick-tile">
                    <i class="bi bi-gear" style="font-size:1.25rem;color:#5b9aff;"></i>
                    <span style="font-weight:600;color:var(--text-primary);font-size:0.75rem;">Einstellungen</span>
                </a>
                <a href="{{ route('ortsverband.show', $ortsverband) }}" class="ov-quick-tile">
                    <i class="bi bi-eye" style="font-size:1.25rem;color:#5b9aff;"></i>
                    <span style="font-weight:600;color:var(--text-primary);font-size:0.75rem;">Mitgliederansicht</span>
                </a>
            </div>
        </div>

    </div>
</div>

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
<style>[x-cloak] { display: none !important; }</style>
@endpush
@endsection
