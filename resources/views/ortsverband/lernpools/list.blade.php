@extends('layouts.app')

@section('title', $ortsverband->name . ' - Lernpools')

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

    .ov-filter-pill {
        font-size: 0.6875rem; font-weight: 600; padding: 0.3rem 0.75rem;
        border-radius: 2rem; border: 1px solid var(--glass-border);
        background: transparent; color: var(--text-muted); cursor: pointer;
        transition: all 150ms; font-family: 'IBM Plex Mono', monospace; text-decoration: none;
        display: inline-block;
    }
    .ov-filter-pill:hover { border-color: rgba(91,154,255,0.3); color: var(--text-secondary); text-decoration: none; }
    .ov-filter-pill.active { background: rgba(91,154,255,0.12); color: #5b9aff; border-color: rgba(91,154,255,0.3); }
    html.light-mode .ov-filter-pill.active { background: rgba(0,51,127,0.08); color: #00337F; border-color: rgba(0,51,127,0.2); }

    .lp-tag {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.15rem 0.5rem;
        border-radius: 2rem;
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border: 1px solid rgba(91,154,255,0.2);
    }
    html.light-mode .lp-tag {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.15);
    }

    .lp-card {
        padding: 1.25rem;
        border-radius: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .lp-progress-bar {
        height: 4px;
        background: rgba(255,255,255,0.1);
        border-radius: 2px;
        overflow: hidden;
    }
    html.light-mode .lp-progress-bar {
        background: rgba(0,0,0,0.08);
    }
    .lp-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    .lp-meta-item {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .lp-dropdown {
        background: var(--glass-bg, rgba(26,26,29,0.95));
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        padding: 0.5rem;
        min-width: 200px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    html.light-mode .lp-dropdown {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .lp-dropdown a {
        display: block;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 150ms;
    }
    .lp-dropdown a:hover {
        background: rgba(255,255,255,0.06);
        color: var(--text-primary);
    }
    html.light-mode .lp-dropdown a:hover {
        background: rgba(0,0,0,0.04);
        color: #1a1a1d;
    }

    /* ─── Separator ─── */
    .ov-separator {
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    html.light-mode .ov-separator {
        border-top-color: rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="dash-container">
    <!-- Header -->
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">LERNPOOLS</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $ortsverband->name }}</h1>
        <p class="text-sm" style="color: var(--text-muted);">Verfügbare Lernpools</p>
    </div>

    <!-- Stat Pills -->
    @php
        $enrolledCount = count($enrolledIds);
    @endphp
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $lernpools->count() }}</div>
            <div class="gami-pill__label">Lernpools</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $enrolledCount }}</div>
            <div class="gami-pill__label">Eingeschrieben</div>
        </div>
    </div>

    <!-- Content -->
    <div class="space-y-4">
        <!-- Tags Filter -->
        @if($allTags->isNotEmpty())
        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;display:block;margin-bottom:0.75rem;">FILTER</span>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('ortsverband.lernpools.list', $ortsverband) }}"
                   class="ov-filter-pill {{ !$selectedTag ? 'active' : '' }}">
                    Alle
                </a>
                @foreach($allTags as $tag)
                    <a href="{{ route('ortsverband.lernpools.list', ['ortsverband' => $ortsverband, 'tag' => $tag]) }}"
                       class="ov-filter-pill {{ $selectedTag === $tag ? 'active' : '' }}">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Lernpools -->
        @if($lernpools->isEmpty())
        <div class="glass p-4" style="border-radius:0.75rem; text-align: center; padding: 3rem;">
            <div style="font-size: 2rem; color: var(--text-muted); opacity: 0.5; margin-bottom: 0.75rem;">
                <i class="bi bi-collection"></i>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Keine Lernpools verfügbar</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Der Ortsverband hat noch keine Lernpools erstellt.</p>
        </div>
        @else
            @foreach($lernpools as $lernpool)
            <div class="glass lp-card">
                <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.75rem;">
                    <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $lernpool->name }}</h2>
                </div>

                @if($lernpool->tags && count($lernpool->tags) > 0)
                <div style="display: flex; gap: 0.375rem; flex-wrap: wrap;">
                    @foreach($lernpool->tags as $tag)
                        <span class="lp-tag">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif

                @if($lernpool->description)
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin: 0;">{{ $lernpool->description }}</p>
                @endif

                <div style="display: flex; gap: 1.25rem; flex-wrap: wrap;">
                    <span class="lp-meta-item">{{ $lernpool->getQuestionCount() }} Fragen</span>
                    <span class="lp-meta-item">{{ $lernpool->getEnrollmentCount() }} eingeschrieben</span>
                </div>

                @if(in_array($lernpool->id, $enrolledIds))
                    @php
                        $enrollment = auth()->user()->lernpoolEnrollments()->where('lernpool_id', $lernpool->id)->first();
                        $progress = $enrollment ? $enrollment->getProgress() : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">Fortschritt</span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-primary);">{{ $progress }}%</span>
                        </div>
                        <div class="lp-progress-bar">
                            <div class="lp-progress-fill" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @endif

                <div class="ov-separator" style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 0.5rem;">
                    @if(in_array($lernpool->id, $enrolledIds))
                        @php
                            $lpSections = \App\Models\OrtsverbandLernpoolQuestion::where('lernpool_id', $lernpool->id)
                                ->select('lernabschnitt')
                                ->distinct()
                                ->orderBy('lernabschnitt')
                                ->pluck('lernabschnitt');
                        @endphp
                        <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $lernpool]) }}" class="btn-primary btn-sm">
                            Alle üben
                        </a>
                        <a href="{{ route('ortsverband.lernpools.practice.unsolved', [$ortsverband, $lernpool]) }}" class="btn-secondary btn-sm">
                            Ungelöste üben
                        </a>
                        @if($lpSections->count() > 1)
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="btn-ghost btn-sm">Nach Abschnitt</button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 z-10 mt-2 lp-dropdown">
                                    @foreach($lpSections as $lpSection)
                                        <a href="{{ route('ortsverband.lernpools.practice.section', [$ortsverband, $lernpool, $lpSection]) }}">
                                            Lernabschnitt {{ $lpSection }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <form action="{{ route('ortsverband.lernpools.enroll', [$ortsverband, $lernpool]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-primary btn-sm">
                                Einschreiben
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        @endif

        <!-- Back Link -->
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('ortsverband.show', $ortsverband) }}" style="font-size:0.8125rem;color:var(--text-muted);text-decoration:none;font-weight:600;transition:color 150ms;">Zurück zum Ortsverband</a>
        </div>
    </div>
</div>
@endsection
