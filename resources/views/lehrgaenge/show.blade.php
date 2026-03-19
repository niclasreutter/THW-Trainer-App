@extends('layouts.app')

@section('title', $lehrgang->lehrgang . ' - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Progress Ring ──────────────────────────── */
    .ls-ring {
        width: 56px;
        height: 56px;
        position: relative;
        flex-shrink: 0;
    }

    .ls-ring__bg {
        fill: none;
        stroke: rgba(255,255,255,0.1);
        stroke-width: 6;
    }

    html.light-mode .ls-ring__bg { stroke: rgba(0,51,127,0.08); }

    .ls-ring__fill {
        fill: none;
        stroke: #5b9aff;
        stroke-width: 6;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: center;
        transition: stroke-dashoffset 1s ease-out;
    }

    .ls-ring__fill--complete { stroke: #22c55e; }

    html.light-mode .ls-ring__fill { stroke: #00337F; }
    html.light-mode .ls-ring__fill--complete { stroke: #16a34a; }

    .ls-ring__text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Hero Card ──────────────────────────────── */
    .ls-hero {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem;
    }

    .ls-hero__info { flex: 1; min-width: 0; }

    .ls-hero__label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.25rem;
    }

    .ls-hero__value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .ls-hero__sub {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 0.125rem;
    }

    /* ─── Action Tiles ───────────────────────────── */
    .ls-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    @media (max-width: 480px) {
        .ls-actions { grid-template-columns: 1fr; }
    }

    .ls-action-tile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        text-decoration: none;
        border-radius: 0.75rem;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .ls-action-tile:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,127,0.1);
        text-decoration: none;
    }

    .ls-action-tile__badge {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        flex-shrink: 0;
        color: #5b9aff;
        background: rgba(0,85,204,0.12);
    }

    html.light-mode .ls-action-tile__badge {
        background: rgba(0,51,127,0.08);
        color: #00337F;
    }

    .ls-action-tile__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .ls-action-tile__desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Section Items ──────────────────────────── */
    .ls-section-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        text-decoration: none;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .ls-section-item:hover {
        background: rgba(255,255,255,0.03);
        text-decoration: none;
    }

    html.light-mode .ls-section-item:hover { background: rgba(0,0,0,0.03); }

    .ls-section-item + .ls-section-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .ls-section-item + .ls-section-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .ls-section-num {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6875rem;
        font-weight: 700;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
    }

    .ls-section-num--green { background: rgba(34,197,94,0.12); color: #22c55e; }
    .ls-section-num--blue  { background: rgba(0,85,204,0.12); color: #5b9aff; }
    .ls-section-num--red   { background: rgba(239,68,68,0.12); color: #ef4444; }
    .ls-section-num--gray  { background: rgba(255,255,255,0.06); color: var(--text-muted); }

    html.light-mode .ls-section-num--green { background: rgba(34,197,94,0.1); }
    html.light-mode .ls-section-num--blue  { background: rgba(0,51,127,0.08); color: #00337F; }
    html.light-mode .ls-section-num--red   { background: rgba(239,68,68,0.08); }
    html.light-mode .ls-section-num--gray  { background: rgba(0,0,0,0.04); }

    .ls-section-bar {
        height: 3px;
        background: rgba(255,255,255,0.06);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.35rem;
    }

    html.light-mode .ls-section-bar { background: rgba(0,0,0,0.06); }

    .ls-section-bar__fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.6s ease-out;
    }

    .ls-section-pct {
        font-size: 0.75rem;
        font-weight: 700;
        color: #5b9aff;
        flex-shrink: 0;
        font-family: 'Barlow Condensed', sans-serif;
    }

    html.light-mode .ls-section-pct { color: #00337F; }

    .ls-section-arrow {
        color: var(--text-muted);
        font-size: 0.75rem;
        flex-shrink: 0;
        transition: color 150ms ease;
    }

    .ls-section-item:hover .ls-section-arrow { color: #5b9aff; }
    html.light-mode .ls-section-item:hover .ls-section-arrow { color: #00337F; }

    /* ─── Unenroll ───────────────────────────────── */
    .ls-unenroll {
        text-align: center;
        padding-top: 0.75rem;
    }

    .ls-unenroll__btn {
        font-size: 0.6875rem;
        color: var(--text-muted);
        background: none;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 2rem;
        padding: 0.3rem 1rem;
        cursor: pointer;
        transition: all 150ms;
        font-family: inherit;
    }

    .ls-unenroll__btn:hover {
        color: #ef4444;
        border-color: rgba(239,68,68,0.25);
        background: rgba(239,68,68,0.06);
    }

    html.light-mode .ls-unenroll__btn {
        border-color: rgba(0,0,0,0.08);
    }

    .ls-unenroll__hint {
        font-size: 0.5625rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
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
    .dash-container > .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
    .dash-container > .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    @media (max-width: 480px) {
        .ls-hero { flex-direction: column; text-align: center; gap: 0.75rem; }
    }
</style>
@endpush

@section('content')
@php
    $questionCount = $lehrgang->questions()->count();
    $sectionCount = $sections->count();
    $solvedCount = $userProgress['solved'] ?? 0;
    $totalCount = $userProgress['total'] ?? $questionCount;

    // Progress calculation
    $progressPercent = 0;
    $isCompleted = false;
    if ($isEnrolled) {
        $progressData = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
            ->get();

        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, \App\Models\UserQuestionProgress::MASTERY_THRESHOLD);
        }
        $maxProgressPoints = $totalCount * \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
        $isCompleted = $progressPercent == 100 && $solvedCount > 0;
    }

    $circumference = 2 * 3.14159 * 22;
    $progressOffset = $circumference - ($progressPercent / 100) * $circumference;
@endphp

<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Lehrgang</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $lehrgang->lehrgang }}</h1>
        @if($lehrgang->beschreibung)
            <p class="text-sm" style="color: var(--text-muted);">{{ $lehrgang->beschreibung }}</p>
        @endif
    </div>

    {{-- ── Stat Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $questionCount }}</div>
            <div class="gami-pill__label">Fragen</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $sectionCount }}</div>
            <div class="gami-pill__label">Abschnitte</div>
        </div>
        @if($isEnrolled)
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:var(--success);-webkit-text-fill-color:var(--success);">{{ $solvedCount }}</div>
                <div class="gami-pill__label">Gemeistert</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:{{ $isCompleted ? 'var(--success)' : 'var(--warning)' }};-webkit-text-fill-color:{{ $isCompleted ? 'var(--success)' : 'var(--warning)' }};">{{ $progressPercent }}%</div>
                <div class="gami-pill__label">Fortschritt</div>
            </div>
        @endif
    </div>

    @if($isEnrolled)
        <div class="space-y-4">

            {{-- ── Smart Action Card ── --}}
            @if($isCompleted)
                <div class="smart-action" style="background:linear-gradient(135deg, #16a34a, #22c55e);">
                    <div class="smart-action__label">Abgeschlossen</div>
                    <div class="smart-action__title">Lehrgang gemeistert</div>
                    <div class="smart-action__desc">Alle {{ $totalCount }} Fragen erfolgreich beantwortet</div>
                </div>
            @else
                <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="smart-action" style="display:block;text-decoration:none;">
                    <div class="smart-action__label">Weiterlernen</div>
                    <div class="smart-action__title">Alle Fragen üben</div>
                    <div class="smart-action__desc">{{ $totalCount - $solvedCount }} von {{ $totalCount }} Fragen noch offen</div>
                    <span class="smart-action__btn">
                        Jetzt starten
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            @endif

            {{-- ── Progress Hero ── --}}
            <div class="glass ls-hero" style="border-radius:0.75rem;">
                <div class="ls-ring">
                    <svg width="56" height="56" viewBox="0 0 56 56">
                        <circle class="ls-ring__bg" cx="28" cy="28" r="22"/>
                        <circle class="ls-ring__fill {{ $isCompleted ? 'ls-ring__fill--complete' : '' }}" cx="28" cy="28" r="22"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $progressOffset }}"/>
                    </svg>
                    <div class="ls-ring__text">{{ $progressPercent }}%</div>
                </div>
                <div class="ls-hero__info">
                    <div class="ls-hero__label">Lernfortschritt</div>
                    <div class="ls-hero__value">{{ $solvedCount }} von {{ $totalCount }} Fragen gemeistert</div>
                    <div class="ls-hero__sub">Jede Frage muss {{ \App\Models\UserQuestionProgress::MASTERY_THRESHOLD }}x richtig beantwortet werden</div>
                </div>
            </div>

            {{-- ── Action Tiles ── --}}
            @if(!$isCompleted)
            <div class="ls-actions">
                <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="glass ls-action-tile">
                    <div class="ls-action-tile__badge"><i class="bi bi-play-fill"></i></div>
                    <div>
                        <div class="ls-action-tile__title">Alle üben</div>
                        <div class="ls-action-tile__desc">{{ $questionCount }} Fragen</div>
                    </div>
                </a>
                <a href="{{ route('lehrgaenge.practice.unsolved', $lehrgang->slug) }}" class="glass ls-action-tile">
                    <div class="ls-action-tile__badge"><i class="bi bi-lightning-fill"></i></div>
                    <div>
                        <div class="ls-action-tile__title">Ungelöste üben</div>
                        <div class="ls-action-tile__desc">{{ $totalCount - $solvedCount }} offen</div>
                    </div>
                </a>
            </div>
            @endif

            {{-- ── Lernabschnitte ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Lernabschnitte</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $sectionCount }} Abschnitte</span>
                </div>

                @foreach($sections as $section)
                    @php
                        $sectionNr = $section->lernabschnitt_nr;
                        $sectionName = $section->lernabschnitt;
                        $sp = $sectionProgress[$sectionNr] ?? null;
                        $sectionPct = $sp ? $sp['percentage'] : 0;
                        $sectionSolved = $sp ? $sp['solved'] : 0;
                        $sectionTotal = $sp ? $sp['total'] : ($questions[$sectionNr] ?? collect())->count();
                        $colorClass = $sectionPct >= 80 ? 'green' : ($sectionPct >= 50 ? 'blue' : 'red');
                        $barColor = $sectionPct >= 80 ? '#22c55e' : ($sectionPct >= 50 ? '#0055cc' : '#ef4444');
                    @endphp
                    <a href="{{ route('lehrgaenge.practice.section', [$lehrgang->slug, $sectionNr]) }}" class="ls-section-item">
                        <div class="ls-section-num ls-section-num--{{ $colorClass }}">{{ $sectionNr }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.75rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $sectionName }}</div>
                            <div class="ls-section-bar">
                                <div class="ls-section-bar__fill" style="width:{{ $sectionPct }}%;background:{{ $barColor }};"></div>
                            </div>
                        </div>
                        <span class="ls-section-pct">{{ $sectionPct }}%</span>
                        <i class="bi bi-chevron-right ls-section-arrow"></i>
                    </a>
                @endforeach
            </div>

            {{-- ── Unenroll ── --}}
            <div class="ls-unenroll">
                <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST"
                      onsubmit="return confirm('Lehrgang verlassen? Dein Fortschritt bleibt gespeichert.');">
                    @csrf
                    <button type="submit" class="ls-unenroll__btn">Lehrgang verlassen</button>
                </form>
                <p class="ls-unenroll__hint">Fortschritt bleibt erhalten</p>
            </div>

            {{-- ── Back Link ── --}}
            <div style="text-align:center;">
                <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm" style="text-decoration:none;">Alle Lehrgänge</a>
            </div>

        </div>

    @else
        <div class="space-y-4">

            {{-- ── Enroll Action Card ── --}}
            <div class="smart-action" style="cursor:default;">
                <div class="smart-action__label">Noch nicht eingeschrieben</div>
                <div class="smart-action__title">Bereit loszulegen?</div>
                <div class="smart-action__desc">Schreibe dich ein und erhalte Zugang zu allen {{ $questionCount }} Fragen in {{ $sectionCount }} Abschnitten</div>
                <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="smart-action__btn" style="border:none;cursor:pointer;font-family:inherit;">
                        Jetzt beitreten
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

            {{-- ── Info Tiles ── --}}
            <div class="ls-actions">
                <div class="glass p-4" style="border-radius:0.75rem;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;line-height:1;color:#5b9aff;">{{ $questionCount }}</div>
                    <div class="text-xs uppercase tracking-wider mt-1" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5rem;">Fragen</div>
                </div>
                <div class="glass p-4" style="border-radius:0.75rem;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;line-height:1;color:#5b9aff;">{{ $sectionCount }}</div>
                    <div class="text-xs uppercase tracking-wider mt-1" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5rem;">Abschnitte</div>
                </div>
            </div>

            {{-- ── Lernabschnitte Preview ── --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Lernabschnitte</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $sectionCount }} Abschnitte</span>
                </div>

                @foreach($sections as $section)
                    @php
                        $sectionNr = $section->lernabschnitt_nr;
                        $sectionName = $section->lernabschnitt;
                        $sectionTotal = ($questions[$sectionNr] ?? collect())->count();
                    @endphp
                    <div class="ls-section-item" style="cursor:default;">
                        <div class="ls-section-num ls-section-num--gray">{{ $sectionNr }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.75rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $sectionName }}</div>
                            <div style="font-size:0.5625rem;color:var(--text-muted);margin-top:0.125rem;">{{ $sectionTotal }} Fragen</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Back Link ── --}}
            <div style="text-align:center;">
                <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm" style="text-decoration:none;">Alle Lehrgänge</a>
            </div>

        </div>
    @endif

</div>
@endsection
