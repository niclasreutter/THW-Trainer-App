@extends('layouts.app')

@section('title', $lernpool->name . ' · Fragen')

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

    .q-section-badge {
        font-size: 0.6rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 2rem;
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border: 1px solid rgba(91,154,255,0.2);
        white-space: nowrap;
        font-family: 'IBM Plex Mono', monospace;
    }
    html.light-mode .q-section-badge {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.15);
    }

    .q-number {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-primary);
        min-width: 2rem;
        text-align: center;
        font-family: 'IBM Plex Mono', monospace;
    }

    .q-text {
        flex: 1;
        font-size: 0.8rem;
        color: var(--text-secondary);
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .q-solution {
        font-size: 0.7rem;
        font-weight: 600;
        color: #22c55e;
        white-space: nowrap;
        font-family: 'IBM Plex Mono', monospace;
    }

    .q-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .q-action-link {
        font-size: 0.7rem;
        font-weight: 600;
        color: #5b9aff;
        text-decoration: none;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        transition: all 150ms;
    }
    .q-action-link:hover {
        background: rgba(91,154,255,0.1);
    }
    .q-action-link--danger {
        color: #ef4444;
    }
    .q-action-link--danger:hover {
        background: rgba(239,68,68,0.1);
    }

    @media (max-width: 640px) {
        .ov-item {
            flex-wrap: wrap;
        }
        .q-text {
            width: 100%;
            white-space: normal;
        }
    }
</style>
@endpush

@section('content')
<div class="dash-container">
    <!-- Header -->
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">FRAGEN</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $lernpool->name }}</h1>
        <p class="text-sm" style="color: var(--text-muted);">Fragenverwaltung</p>
    </div>

    <!-- Stat Pills -->
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $questions->count() }}</div>
            <div class="gami-pill__label">Fragen</div>
        </div>
    </div>

    <!-- Content -->
    <div class="space-y-4">
        @if($questions->count() > 0)
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">ALLE FRAGEN</span>
            </div>

            <div>
                @foreach($questions as $question)
                <div class="ov-item">
                    <span class="q-section-badge">{{ $question->lernabschnitt ?? '-' }}</span>
                    <span class="q-number">{{ $question->nummer }}</span>
                    <span class="q-text">{{ Str::limit($question->frage, 60) }}</span>
                    <span class="q-solution">{{ ucfirst($question->loesung) }}</span>
                    <div class="q-actions">
                        <a href="{{ route('ortsverband.lernpools.questions.edit', [$ortsverband, $lernpool, $question]) }}"
                           class="q-action-link">Bearbeiten</a>
                        <form action="{{ route('ortsverband.lernpools.questions.destroy', [$ortsverband, $lernpool, $question]) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Frage wirklich löschen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="q-action-link q-action-link--danger">
                                Löschen
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="glass p-4" style="border-radius:0.75rem; text-align: center; padding: 3rem;">
            <div style="font-size: 2rem; color: var(--text-muted); opacity: 0.5; margin-bottom: 0.75rem;">
                <i class="bi bi-question-circle"></i>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.75rem;">Noch keine Fragen.</p>
            <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" class="btn-primary btn-sm">Jetzt eine erstellen</a>
        </div>
        @endif

        <!-- Back Link -->
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $lernpool]) }}" style="font-size:0.8125rem;color:var(--text-muted);text-decoration:none;font-weight:600;transition:color 150ms;">Zurück zur Fragenübersicht</a>
        </div>
    </div>
</div>
@endsection
