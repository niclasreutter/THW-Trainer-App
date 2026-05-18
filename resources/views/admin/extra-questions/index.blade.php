@extends('layouts.app')
@section('title', 'Zusatz-Fragen - THW Trainer Admin')
@section('description', 'Verwalte optionale Zusatz-Fragen (Zuordnung, Bild benennen, Bild auswählen).')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .zf-empty {
        padding: 3.25rem 1.5rem;
        text-align: center;
    }
    .zf-empty__icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        border-radius: 999px;
        background: rgba(0, 51, 127, 0.08);
        display: grid;
        place-items: center;
        color: var(--thw-blue);
        font-size: 1.75rem;
    }
    html:not(.light-mode) .zf-empty__icon {
        background: rgba(91, 154, 255, 0.12);
        color: #5b9aff;
    }
    .zf-empty__text {
        font-size: 0.9375rem;
        color: var(--text-secondary);
        margin: 0 0 1.25rem;
    }

    .zf-section-group { padding: 0.25rem 0.75rem 0.75rem; }
    .zf-section-group + .zf-section-group { margin-top: 1.25rem; }
    .zf-section-group__head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.875rem 0.5rem;
        border-bottom: 1px solid rgba(0, 51, 127, 0.06);
        margin-bottom: 0.25rem;
    }
    html:not(.light-mode) .zf-section-group__head { border-bottom-color: rgba(255, 255, 255, 0.06); }
    .zf-section-group__title {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--thw-blue);
    }
    html:not(.light-mode) .zf-section-group__title { color: #5b9aff; }
    .zf-section-group__count {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .zf-list-row {
        display: grid;
        grid-template-columns: 2.5rem 1fr auto auto auto;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 0.75rem;
        border-radius: 0.625rem;
        transition: background 0.15s ease;
    }
    .zf-list-row + .zf-list-row { border-top: 1px solid rgba(0, 51, 127, 0.06); }
    html:not(.light-mode) .zf-list-row + .zf-list-row { border-top-color: rgba(255, 255, 255, 0.05); }
    .zf-list-row:hover { background: rgba(0, 51, 127, 0.04); }
    html:not(.light-mode) .zf-list-row:hover { background: rgba(91, 154, 255, 0.06); }
    .zf-list-num {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
    }
    .zf-list-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    .zf-list-meta {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .zf-type-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.325rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        white-space: nowrap;
    }
    .zf-type-chip--matching     { background: rgba(0, 51, 127, 0.10);   color: var(--thw-blue); }
    html:not(.light-mode) .zf-type-chip--matching { background: rgba(91, 154, 255, 0.14); color: #5b9aff; }
    .zf-type-chip--image_name   { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }
    .zf-type-chip--image_select { background: rgba(139, 92, 246, 0.14); color: #8b5cf6; }
    .zf-type-chip--pair_matching { background: rgba(251, 191, 36, 0.14); color: #d97706; }
    html:not(.light-mode) .zf-type-chip--pair_matching { background: rgba(251, 191, 36, 0.18); color: #fbbf24; }

    .zf-row-actions {
        display: flex;
        gap: 0.35rem;
        align-items: center;
    }
    .zf-icon-btn {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: transparent;
        border: 1px solid rgba(0, 51, 127, 0.10);
        color: var(--text-muted);
        cursor: pointer;
        display: grid;
        place-items: center;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    html:not(.light-mode) .zf-icon-btn { border-color: rgba(255, 255, 255, 0.10); }
    .zf-icon-btn:hover {
        border-color: var(--thw-blue);
        color: var(--thw-blue);
        background: rgba(0, 51, 127, 0.04);
    }
    html:not(.light-mode) .zf-icon-btn:hover {
        border-color: #5b9aff;
        color: #5b9aff;
        background: rgba(91, 154, 255, 0.08);
    }
    .zf-icon-btn--danger:hover {
        border-color: #ef4444;
        color: #ef4444;
        background: rgba(239, 68, 68, 0.06);
    }

    @media (max-width: 640px) {
        .zf-list-row { grid-template-columns: 1fr; gap: 0.5rem; }
        .zf-list-num { display: none; }
        .zf-row-actions { justify-content: flex-start; }
    }
</style>
@endpush

@section('content')
@php
    $total = $questions->count();
    $byTyp = $questions->groupBy('typ');
    $countMatching     = $byTyp->get('matching', collect())->count();
    $countImageName    = $byTyp->get('image_name', collect())->count();
    $countImageSelect  = $byTyp->get('image_select', collect())->count();
    $countPairMatching = $byTyp->get('pair_matching', collect())->count();
    $grouped = $questions->groupBy('lernabschnitt');

    $typLabels = [
        'matching'      => 'Zuordnung',
        'image_name'    => 'Bild benennen',
        'image_select'  => 'Bild auswählen',
        'pair_matching' => 'Wortpaare',
    ];
    $typIcons = [
        'matching'      => 'bi-diagram-3-fill',
        'image_name'    => 'bi-image-fill',
        'image_select'  => 'bi-grid-3x3-gap-fill',
        'pair_matching' => 'bi-link-45deg',
    ];
@endphp

<div class="dash-container">
    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">Admin · Fragen</div>
        <h1 class="zf-page-title__h1">Zusatz-Fragen</h1>
        <div class="zf-page-title__sub">Optionale Lern-Ergänzung: Zuordnung, Bild benennen, Bild auswählen</div>
    </div>

    @if(session('success'))
        <div class="zf-alert zf-alert--success">
            <i class="bi bi-check-circle-fill"></i>
            <div><strong>Erfolg!</strong>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="zf-alert zf-alert--error">
            <i class="bi bi-x-circle-fill"></i>
            <div>
                <strong>Fehler!</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="zf-stat-pills">
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value">{{ $total }}</div>
            <div class="zf-stat-pill__label">Gesamt</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value zf-stat-pill__value--blue">{{ $countMatching }}</div>
            <div class="zf-stat-pill__label">Zuordnung</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value zf-stat-pill__value--ok">{{ $countImageName }}</div>
            <div class="zf-stat-pill__label">Bild benennen</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value zf-stat-pill__value--warn">{{ $countImageSelect }}</div>
            <div class="zf-stat-pill__label">Bild auswählen</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value">{{ $countPairMatching }}</div>
            <div class="zf-stat-pill__label">Wortpaare</div>
        </div>
    </div>

    <div class="zf-action-row">
        <span class="zf-section-label">
            Alle Zusatz-Fragen{{ $total > 0 ? ' · '.$total : '' }}
        </span>
        @if(auth()->user()->canEditQuestions())
        <a href="{{ route('admin.extra-questions.create') }}" class="btn-primary">
            <i class="bi bi-plus-lg"></i> Neue Zusatz-Frage
        </a>
        @endif
    </div>

    <div class="zf-form-card" style="padding: 0.75rem; overflow: hidden;">
        @if($total === 0)
            <div class="zf-empty">
                <div class="zf-empty__icon"><i class="bi bi-inboxes"></i></div>
                <p class="zf-empty__text">Noch keine Zusatz-Fragen vorhanden.</p>
                @if(auth()->user()->canEditQuestions())
                <a href="{{ route('admin.extra-questions.create') }}" class="btn-primary">
                    <i class="bi bi-plus-lg"></i> Erste Zusatz-Frage anlegen
                </a>
                @endif
            </div>
        @else
            @foreach($grouped as $lernabschnitt => $group)
                <div class="zf-section-group">
                    <div class="zf-section-group__head">
                        <span class="zf-section-group__title">Lernabschnitt {{ $lernabschnitt }}</span>
                        <span class="zf-section-group__count">
                            {{ $group->count() }} {{ $group->count() === 1 ? 'Frage' : 'Fragen' }}
                        </span>
                    </div>
                    @foreach($group as $idx => $q)
                        <div class="zf-list-row">
                            <div class="zf-list-num">#{{ str_pad($q->id, 2, '0', STR_PAD_LEFT) }}</div>
                            <div>
                                <div class="zf-list-title">{{ $q->frage }}</div>
                                <div class="zf-list-meta">
                                    @if($q->typ === 'matching')
                                        {{ $q->matchCategories->count() }} Kategorien · {{ $q->matchItems->count() }} Items
                                    @elseif($q->typ === 'image_name')
                                        {{ $q->options->count() }} Antworten
                                    @elseif($q->typ === 'image_select')
                                        {{ $q->options->count() }} Bilder
                                    @elseif($q->typ === 'pair_matching')
                                        {{ $q->pairItems->count() }} Paare
                                    @endif
                                </div>
                            </div>
                            <span class="zf-type-chip zf-type-chip--{{ $q->typ }}">
                                <i class="bi {{ $typIcons[$q->typ] ?? 'bi-question' }}"></i>
                                {{ $typLabels[$q->typ] ?? $q->typ }}
                            </span>
                            <div class="zf-row-actions">
                                <a href="{{ route('admin.extra-questions.tryout', $q) }}" class="zf-icon-btn" title="Try-Out / Vorschau">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                                <a href="{{ route('admin.extra-questions.edit', $q) }}" class="zf-icon-btn" title="Bearbeiten">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('admin.extra-questions.destroy', $q) }}"
                                      onsubmit="return confirm('Zusatz-Frage wirklich löschen?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="zf-icon-btn zf-icon-btn--danger" title="Löschen">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                            <i class="bi bi-chevron-right" style="color: var(--text-muted); font-size: 0.85rem;"></i>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
