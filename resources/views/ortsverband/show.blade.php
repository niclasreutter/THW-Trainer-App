@extends('layouts.app')

@section('title', $ortsverband->name)

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

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    /* ─── OV Items ─── */
    .ov-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }
    .ov-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .ov-item:hover { background: rgba(0,0,0,0.03); }
    .ov-item + .ov-item { border-top: 1px solid rgba(255,255,255,0.04); }
    html.light-mode .ov-item + .ov-item { border-top-color: rgba(0,0,0,0.06); }

    /* ─── Tag Filter Pills ─── */
    .ov-tag-pills {
        display: flex;
        gap: 0.375rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }
    .ov-tag-pill {
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        border: 1px solid var(--glass-border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 150ms;
        text-decoration: none;
        font-family: 'IBM Plex Mono', monospace;
    }
    .ov-tag-pill:hover {
        border-color: rgba(91,154,255,0.3);
        color: var(--text-secondary);
        text-decoration: none;
    }
    .ov-tag-pill.active {
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border-color: rgba(91,154,255,0.3);
    }
    html.light-mode .ov-tag-pill.active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.2);
    }

    /* ─── Lernpool cards ─── */
    .ov-pool-card {
        position: relative;
        padding: 1rem;
        border-radius: 0.625rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        display: flex;
        flex-direction: column;
        transition: background 150ms, border-color 150ms;
    }
    .ov-pool-card:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    html.light-mode .ov-pool-card {
        background: rgba(0,51,127,0.02);
        border-color: rgba(0,51,127,0.06);
    }
    html.light-mode .ov-pool-card:hover {
        background: rgba(0,51,127,0.05);
    }

    .ov-pool-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .ov-btn-leave {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(239,68,68,0.12);
        border: none;
        color: #ef4444;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6875rem;
        transition: background 150ms;
    }
    .ov-btn-leave:hover { background: rgba(239,68,68,0.22); }

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
</style>
@endpush

@section('content')
@php
    $userMember = $ortsverband->members()->where('user_id', auth()->id())->first();
    $userIsAusbilder = $userMember && $userMember->pivot->role === 'ausbildungsbeauftragter';
    $ausbilder = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->get();
@endphp

<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Ortsverband</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $ortsverband->name }}</h1>
        @if($ortsverband->description)
            <p class="text-sm" style="color: var(--text-muted);">{{ $ortsverband->description }}</p>
        @else
            <p class="text-sm" style="color: var(--text-muted);">Dein Ortsverband auf THW Trainer</p>
        @endif
    </div>

    {{-- ── Stat Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $ortsverband->members()->count() }}</div>
            <div class="gami-pill__label">Mitglieder</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#a855f7;-webkit-text-fill-color:#a855f7;">{{ $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count() }}</div>
            <div class="gami-pill__label">Ausbilder</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value">{{ $ortsverband->created_at->format('d.m.Y') }}</div>
            <div class="gami-pill__label">Gegründet</div>
        </div>
    </div>

    {{-- ── Admin Alert ── --}}
    @if($isAdminViewing)
    <div class="ov-alert glass" style="border-left:3px solid #5b9aff;">
        <div style="flex:1;">
            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">Admin-Ansicht</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);">Du betrachtest diesen Ortsverband als Admin.</div>
        </div>
        <form method="POST" action="{{ route('admin.ortsverband.exit-view') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn-ghost btn-sm">Beenden</button>
        </form>
    </div>
    @endif

    @if(session('success'))
    <div class="ov-alert glass-success">
        <i class="bi bi-check-circle" style="font-size:1rem;flex-shrink:0;"></i>
        <span style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);flex:1;">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:1.125rem;line-height:1;">&times;</button>
    </div>
    @endif

    <div class="space-y-4">

        {{-- ── Lernpools ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Lernpools</span>
                @if($userIsAusbilder)
                    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="btn-primary btn-sm" style="text-decoration:none;">Verwalten</a>
                @endif
            </div>

            {{-- Tags Filter --}}
            @if($allTags->isNotEmpty())
            <div class="ov-tag-pills">
                <a href="{{ route('ortsverband.show', $ortsverband) }}"
                   class="ov-tag-pill {{ !$selectedTag ? 'active' : '' }}">Alle</a>
                @foreach($allTags as $tag)
                    <a href="{{ route('ortsverband.show', ['ortsverband' => $ortsverband, 'tag' => $tag]) }}"
                       class="ov-tag-pill {{ $selectedTag === $tag ? 'active' : '' }}">{{ $tag }}</a>
                @endforeach
            </div>
            @endif

            @php
                $activeLernpools = $ortsverband->activeLernpools;
                if ($selectedTag) {
                    $activeLernpools = $activeLernpools->filter(function($pool) use ($selectedTag) {
                        return $pool->tags && in_array($selectedTag, $pool->tags);
                    });
                }
                $userEnrollments = auth()->user()->lernpoolEnrollments->pluck('lernpool_id')->toArray();
            @endphp

            @if($activeLernpools->count() > 0)
            <div class="ov-pool-grid">
                @foreach($activeLernpools as $pool)
                    @php
                        $isEnrolled = in_array($pool->id, $userEnrollments);
                        $totalQuestions = $pool->getQuestionCount();
                        $enrollment = auth()->user()->lernpoolEnrollments()->where('lernpool_id', $pool->id)->first();
                        $progress = $enrollment ? $enrollment->getProgress() : 0;
                    @endphp
                    <div class="ov-pool-card">
                        @if($isEnrolled)
                            <form action="{{ route('ortsverband.lernpools.unenroll', [$ortsverband, $pool]) }}" method="POST" style="position:absolute;top:0.5rem;right:0.5rem;">
                                @csrf
                                <button type="submit" class="ov-btn-leave" title="Verlassen" onclick="return confirm('Möchtest du diesen Lernpool wirklich verlassen?')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @endif

                        <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);margin-bottom:0.375rem;padding-right:1.75rem;">{{ $pool->name }}</div>

                        @if($pool->tags && count($pool->tags) > 0)
                        <div style="display:flex;gap:0.25rem;flex-wrap:wrap;margin-bottom:0.375rem;">
                            @foreach($pool->tags as $tag)
                                <span style="font-size:0.5625rem;padding:0.125rem 0.375rem;border-radius:2rem;background:rgba(91,154,255,0.1);color:#5b9aff;font-weight:600;font-family:'IBM Plex Mono',monospace;">{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif

                        <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4;">
                            {{ Str::limit($pool->description, 60) }}
                        </p>

                        <div style="display:flex;justify-content:space-between;font-size:0.6875rem;color:var(--text-muted);margin-bottom:0.5rem;">
                            <span>{{ $totalQuestions }} Fragen</span>
                            <span style="font-weight:700;color:#5b9aff;">{{ round($progress) }}%</span>
                        </div>

                        <div class="ov-progress-track" style="margin-bottom:0.625rem;">
                            <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,#0055cc,#5b9aff);width:{{ $progress }}%;transition:width 0.6s ease-out;"></div>
                        </div>

                        @if($isEnrolled)
                            <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $pool]) }}" class="btn-primary btn-sm" style="width:100%;text-align:center;text-decoration:none;">
                                Weiter
                            </a>
                        @else
                            <form action="{{ route('ortsverband.lernpools.enroll', [$ortsverband, $pool]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm" style="width:100%;">Beitreten</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
            @else
            <div style="text-align:center;padding:2rem 1rem;">
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Keine Lernpools</div>
                <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.75rem;">{{ $selectedTag ? 'Keine Lernpools mit diesem Tag gefunden.' : 'Noch keine Lernpools verfügbar.' }}</p>
                @if($userIsAusbilder && !$selectedTag)
                    <a href="{{ route('ortsverband.lernpools.create', $ortsverband) }}" class="btn-primary btn-sm" style="text-decoration:none;">Erstellen</a>
                @endif
            </div>
            @endif
        </div>

        {{-- ── Mitglieder / Ausbilder ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">{{ $userIsAusbilder ? 'Mitglieder' : 'Deine Ausbilder' }}</span>
                @if($userIsAusbilder && $ortsverband->members->count() > 5)
                    <a href="{{ route('ortsverband.members', $ortsverband) }}" style="font-size:0.6875rem;color:#5b9aff;text-decoration:none;font-weight:600;">Alle anzeigen</a>
                @endif
            </div>

            @if($userIsAusbilder)
                @foreach($ortsverband->members->take(5) as $member)
                <div class="ov-item">
                    <img src="{{ $member->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:0.8125rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $member->name }}</div>
                        <div style="font-size:0.625rem;color:var(--text-muted);">{{ $member->pivot->role === 'ausbildungsbeauftragter' ? 'Ausbilder' : 'Mitglied' }}</div>
                    </div>
                </div>
                @endforeach
            @else
                @foreach($ausbilder as $member)
                <div class="ov-item">
                    <img src="{{ $member->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:0.8125rem;color:var(--text-primary);">{{ $member->name }}</div>
                        <div style="font-size:0.625rem;color:var(--text-muted);">Ausbilder</div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- ── Rangliste ── --}}
        @if($ortsverband->ranking_visible && $memberProgress)
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Rangliste</span>
            </div>

            @foreach($memberProgress->take(5) as $index => $member)
            @php
                $sUser = $member['user'];
                $sHasGlow = $sUser->glowing_name_until && \Carbon\Carbon::parse($sUser->glowing_name_until)->isFuture();
                $sHasRankColor = $sUser->rank_color_until && \Carbon\Carbon::parse($sUser->rank_color_until)->isFuture() && $sUser->rank_color;
                $sHasFrame = $sUser->profile_frame_until && \Carbon\Carbon::parse($sUser->profile_frame_until)->isFuture();
                $sHasTitle = $sUser->active_title_until && \Carbon\Carbon::parse($sUser->active_title_until)->isFuture() && $sUser->active_title;
                $sNameClasses = '';
                if ($sHasGlow) $sNameClasses .= $sUser->glowing_name_type === 'shimmer' ? ' glowing-name-shimmer' : ' glowing-name-static';
                if ($sHasRankColor) $sNameClasses .= ' rank-color-' . $sUser->rank_color;
                $sFrameClass = '';
                if ($sHasFrame) $sFrameClass = ($sUser->profile_frame_type ?? 'gold') === 'diamond' ? 'profile-frame-diamond' : 'profile-frame-gold';
                $rankColor = $index === 0 ? '#fbbf24' : ($index === 1 ? '#94a3b8' : ($index === 2 ? '#cd7f32' : 'var(--text-muted)'));
            @endphp
            <div class="ov-item {{ $sFrameClass }}">
                <div style="font-size:0.9375rem;min-width:1.5rem;font-weight:800;text-align:center;color:{{ $rankColor }};font-family:'Barlow Condensed',sans-serif;">{{ $index + 1 }}</div>
                <img src="{{ $sUser->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                <div style="flex:1;min-width:0;">
                    <div class="{{ trim($sNameClasses) }}" style="font-weight:600;font-size:0.8125rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $sUser->name }}
                    </div>
                    @if($sHasTitle)
                        <div class="user-title" style="font-size:0.5625rem;">{{ $sUser->active_title }}</div>
                    @endif
                    <div style="font-size:0.625rem;color:var(--text-muted);">{{ number_format($member['points']) }} Punkte</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── Info Card ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Info</span>
            </div>
            <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.375rem;">Was sehen Ausbilder?</div>
            <p style="font-size:0.75rem;color:var(--text-muted);margin:0;line-height:1.5;">
                Theorie-Fortschritt, Prüfungs-Streak, Lern-Streak, Level & Punkte, letzte Aktivität und Schwachstellen.
            </p>
        </div>

        {{-- ── Verlassen ── --}}
        @php
            $currentMember = $ortsverband->members()->where('user_id', auth()->id())->first();
            $isAusbildungsbeauftragter = $currentMember && $currentMember->pivot->role === 'ausbildungsbeauftragter';
            $ausbilderCount = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count();
            $canLeave = !$isAusbildungsbeauftragter || $ausbilderCount > 1;
        @endphp

        <div style="text-align:center;padding-top:0.25rem;">
            @if($canLeave)
            <form action="{{ route('ortsverband.leave', $ortsverband) }}" method="POST"
                  onsubmit="return confirm('Möchtest du diesen Ortsverband wirklich verlassen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger btn-sm">Ortsverband verlassen</button>
            </form>
            @else
            <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">
                Du bist der einzige Ausbilder und kannst nicht verlassen.
            </p>
            @endif
        </div>

        {{-- ── Zurück ── --}}
        <div style="text-align:center;">
            <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm" style="text-decoration:none;">Zurück zum Dashboard</a>
        </div>

    </div>
</div>
@endsection
