@extends('layouts.app')

@section('title', 'Lehrgänge - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Lehrgang Cards ──────────────────────────── */
    .lg-card {
        display: flex;
        flex-direction: column;
        padding: 1.25rem;
        border-radius: 0.75rem;
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .lg-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }

    .lg-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .lg-card__name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.3;
        flex: 1;
    }

    .lg-card__desc {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .lg-card__stats {
        display: flex;
        gap: 1.25rem;
        padding: 0.625rem 0;
        border-top: 1px solid rgba(255,255,255,0.06);
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 0.75rem;
    }

    html.light-mode .lg-card__stats {
        border-top-color: rgba(0,51,127,0.08);
        border-bottom-color: rgba(0,51,127,0.08);
    }

    .lg-card__stat {
        text-align: center;
        flex: 1;
    }

    .lg-card__stat-value {
        font-size: 1.125rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        color: #5b9aff;
    }

    html.light-mode .lg-card__stat-value { color: #00337F; }

    .lg-card__stat-label {
        font-size: 0.5625rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Progress ──────────────────────────────── */
    .lg-progress {
        margin-bottom: 0.75rem;
    }

    .lg-progress__info {
        display: flex;
        justify-content: space-between;
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-bottom: 0.375rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lg-progress__bar {
        height: 4px;
        background: rgba(255,255,255,0.08);
        border-radius: 2px;
        overflow: hidden;
    }

    html.light-mode .lg-progress__bar {
        background: rgba(0,51,127,0.08);
    }

    .lg-progress__fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.6s ease-out;
        background: linear-gradient(90deg, #5b9aff, #0055cc);
    }

    .lg-progress__fill--complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .lg-card__actions {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
        margin-top: auto;
    }

    /* ─── Card Grid ─────────────────────────────── */
    .lg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 0.75rem;
    }

    @media (max-width: 480px) {
        .lg-grid { grid-template-columns: 1fr; }
    }

    /* ─── Status Badge ──────────────────────────── */
    .lg-badge {
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lg-badge--enrolled {
        background: rgba(34,197,94,0.12);
        color: #22c55e;
    }

    .lg-badge--complete {
        background: rgba(34,197,94,0.12);
        color: #22c55e;
    }

    html.light-mode .lg-badge--enrolled { background: rgba(34,197,94,0.1); }
    html.light-mode .lg-badge--complete { background: rgba(34,197,94,0.1); }

    /* ─── Empty State ───────────────────────────── */
    .lg-empty {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .lg-empty__icon {
        width: 3rem;
        height: 3rem;
        margin: 0 auto 1rem;
        border-radius: 0.75rem;
        background: rgba(0,85,204,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5b9aff;
        font-size: 1.25rem;
    }

    html.light-mode .lg-empty__icon {
        background: rgba(0,51,127,0.06);
        color: #00337F;
    }

    .lg-empty__title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
    }

    .lg-empty__desc {
        font-size: 0.8125rem;
        color: var(--text-muted);
        line-height: 1.5;
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

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Weiterbildung</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Weitere Lehrgänge</h1>
        <p class="text-sm" style="color: var(--text-muted);">Spezialisiere dich auf bestimmte THW-Themenbereiche</p>
    </div>

    {{-- ── Stat Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $lehrgaenge->count() }}</div>
            <div class="gami-pill__label">Lehrgänge</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--success);-webkit-text-fill-color:var(--success);">{{ count($enrolledIds) }}</div>
            <div class="gami-pill__label">Eingeschrieben</div>
        </div>
    </div>

    @if($lehrgaenge->isEmpty())
        <div class="glass p-4 lg-empty" style="border-radius:0.75rem;">
            <div class="lg-empty__icon">
                <i class="bi bi-mortarboard"></i>
            </div>
            <h2 class="lg-empty__title">Noch keine Lehrgänge verfügbar</h2>
            <p class="lg-empty__desc">Bald werden hier weitere Lehrgänge erscheinen</p>
        </div>
    @else
        <div class="space-y-4">

            {{-- ── Smart Action Card (wenn eingeschrieben) ── --}}
            @php
                $firstEnrolledLehrgang = $lehrgaenge->first(fn($l) => in_array($l->id, $enrolledIds));
            @endphp
            @if($firstEnrolledLehrgang)
                <a href="{{ route('lehrgaenge.practice', $firstEnrolledLehrgang->slug) }}" class="smart-action" style="display:block;text-decoration:none;">
                    <div class="smart-action__label">Weiterlernen</div>
                    <div class="smart-action__title">{{ $firstEnrolledLehrgang->lehrgang }}</div>
                    <div class="smart-action__desc">Setze dein Training im letzten Lehrgang fort</div>
                    <span class="smart-action__btn">
                        Jetzt starten
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            @endif

            {{-- ── Lehrgang Cards ── --}}
            <div class="lg-grid">
                @foreach($lehrgaenge as $lehrgang)
                    @php
                        $isEnrolled = in_array($lehrgang->id, $enrolledIds);
                        $progressPercent = 0;
                        $solvedCount = 0;
                        $totalCount = 0;
                        $isCompleted = false;

                        if ($isEnrolled) {
                            $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                                ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                                ->where('solved', true)
                                ->count();
                            $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();

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

                        $questionCount = $lehrgang->questions()->count();
                        $sectionCount = $lehrgang->questions()->distinct('lernabschnitt')->count('lernabschnitt');
                    @endphp

                    <div class="glass lg-card">
                        <div class="lg-card__header">
                            <h2 class="lg-card__name">{{ $lehrgang->lehrgang }}</h2>
                            @if($isCompleted)
                                <span class="lg-badge lg-badge--complete">Abgeschlossen</span>
                            @elseif($isEnrolled)
                                <span class="lg-badge lg-badge--enrolled">Aktiv</span>
                            @endif
                        </div>

                        @if($lehrgang->beschreibung)
                            <p class="lg-card__desc">{{ $lehrgang->beschreibung }}</p>
                        @endif

                        <div class="lg-card__stats">
                            <div class="lg-card__stat">
                                <div class="lg-card__stat-value">{{ $questionCount }}</div>
                                <div class="lg-card__stat-label">Fragen</div>
                            </div>
                            <div class="lg-card__stat">
                                <div class="lg-card__stat-value">{{ $sectionCount }}</div>
                                <div class="lg-card__stat-label">Abschnitte</div>
                            </div>
                            @if($isEnrolled)
                                <div class="lg-card__stat">
                                    <div class="lg-card__stat-value">{{ $progressPercent }}%</div>
                                    <div class="lg-card__stat-label">Fortschritt</div>
                                </div>
                            @endif
                        </div>

                        @if($isEnrolled)
                            <div class="lg-progress">
                                <div class="lg-progress__info">
                                    <span>{{ $solvedCount }}/{{ $totalCount }} gemeistert</span>
                                    <span>{{ $progressPercent }}%</span>
                                </div>
                                <div class="lg-progress__bar">
                                    <div class="lg-progress__fill {{ $isCompleted ? 'lg-progress__fill--complete' : '' }}" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="lg-card__actions">
                            @if($isEnrolled)
                                @if($isCompleted)
                                    <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" class="btn-ghost btn-sm" style="text-align:center;">Details anzeigen</a>
                                @else
                                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="smart-action__btn" style="display:flex;justify-content:center;text-decoration:none;border-radius:0.5rem;padding:0.5rem 1rem;">
                                        Weiterlernen
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                    <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" class="btn-ghost btn-sm" style="text-align:center;">Details</a>
                                @endif
                            @else
                                <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" class="smart-action__btn" style="display:flex;justify-content:center;text-decoration:none;border-radius:0.5rem;padding:0.5rem 1rem;">
                                    Details anzeigen
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endif

</div>
@endsection
