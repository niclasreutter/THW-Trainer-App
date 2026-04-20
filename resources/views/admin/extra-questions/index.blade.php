@extends('layouts.app')
@section('title', 'Zusatz-Fragen - THW Trainer Admin')
@section('description', 'Verwalte optionale Zusatz-Fragen (Zuordnung, Bild benennen, Bild auswählen).')

@push('styles')
<style>
    .typ-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.6rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: var(--text-secondary);
    }
    .typ-badge.typ-matching { color: #a5b4fc; border-color: rgba(165,180,252,0.35); background: rgba(99,102,241,0.08); }
    .typ-badge.typ-image_name { color: #6ee7b7; border-color: rgba(110,231,183,0.35); background: rgba(16,185,129,0.08); }
    .typ-badge.typ-image_select { color: #fcd34d; border-color: rgba(252,211,77,0.35); background: rgba(245,158,11,0.08); }

    .lernabschnitt-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 0.375rem;
        background: linear-gradient(135deg, var(--thw-blue-start) 0%, var(--thw-blue-end) 100%);
        color: #fff;
        letter-spacing: 0.02em;
    }

    .eq-row {
        display: grid;
        grid-template-columns: auto auto 1fr auto;
        gap: 1rem;
        align-items: center;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: background 0.15s;
    }
    .eq-row:last-child { border-bottom: none; }
    .eq-row:hover { background: rgba(255,255,255,0.02); }

    .eq-frage {
        color: var(--text-primary);
        font-size: 0.92rem;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .eq-actions {
        display: flex;
        gap: 0.4rem;
        align-items: center;
    }

    .eq-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }

    .section-group + .section-group { margin-top: 1.5rem; }

    @media (max-width: 640px) {
        .eq-row { grid-template-columns: 1fr; }
        .eq-actions { justify-content: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Zusatz-<span>Fragen</span></h1>
        <p class="page-subtitle">Optionale Lern-Ergänzung: Zuordnung, Bild benennen, Bild auswählen</p>
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-x-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Fehler!</strong>
                <ul style="margin: 0.25rem 0 0 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @php
        $total = $questions->count();
        $byTyp = $questions->groupBy('typ');
        $countMatching = $byTyp->get('matching', collect())->count();
        $countImageName = $byTyp->get('image_name', collect())->count();
        $countImageSelect = $byTyp->get('image_select', collect())->count();
        $grouped = $questions->groupBy('lernabschnitt');

        $typLabels = [
            'matching' => 'Zuordnung',
            'image_name' => 'Bild benennen',
            'image_select' => 'Bild auswählen',
        ];
    @endphp

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-collection"></i></span>
            <div>
                <div class="stat-pill-value">{{ $total }}</div>
                <div class="stat-pill-label">Gesamt</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue"><i class="bi bi-diagram-3"></i></span>
            <div>
                <div class="stat-pill-value">{{ $countMatching }}</div>
                <div class="stat-pill-label">Zuordnung</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-image"></i></span>
            <div>
                <div class="stat-pill-value">{{ $countImageName }}</div>
                <div class="stat-pill-label">Bild benennen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-grid-3x3-gap"></i></span>
            <div>
                <div class="stat-pill-value">{{ $countImageSelect }}</div>
                <div class="stat-pill-label">Bild auswählen</div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.5rem 0 1rem;">
        <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
            <h2 class="section-title">Alle Zusatz-Fragen</h2>
        </div>
        <a href="{{ route('admin.extra-questions.create') }}" class="btn-primary">
            Neue Zusatz-Frage
        </a>
    </div>

    @if($total === 0)
        <div class="glass eq-empty">
            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">
                <i class="bi bi-collection"></i>
            </div>
            <p style="margin: 0 0 1rem;">Noch keine Zusatz-Fragen vorhanden.</p>
            <a href="{{ route('admin.extra-questions.create') }}" class="btn-primary">
                Erste Zusatz-Frage anlegen
            </a>
        </div>
    @else
        <div class="bento-grid">
            @foreach($grouped as $lernabschnitt => $group)
                <div class="glass-tl bento-wide section-group" style="padding: 1.25rem 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; gap: 0.75rem; flex-wrap: wrap;">
                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.6rem;">
                            <span class="lernabschnitt-badge">{{ $lernabschnitt }}</span>
                            <span style="color: var(--text-muted); font-weight: 500; font-size: 0.85rem;">
                                {{ $group->count() }} {{ $group->count() === 1 ? 'Frage' : 'Fragen' }}
                            </span>
                        </h3>
                    </div>

                    <div>
                        @foreach($group as $q)
                            <div class="eq-row">
                                <span class="typ-badge typ-{{ $q->typ }}">
                                    @if($q->typ === 'matching')
                                        <i class="bi bi-diagram-3"></i>
                                    @elseif($q->typ === 'image_name')
                                        <i class="bi bi-image"></i>
                                    @else
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    @endif
                                    {{ $typLabels[$q->typ] ?? $q->typ }}
                                </span>

                                <span style="color: var(--text-muted); font-size: 0.78rem; font-variant-numeric: tabular-nums;">
                                    #{{ $q->id }}
                                </span>

                                <div class="eq-frage">{{ $q->frage }}</div>

                                <div class="eq-actions">
                                    <a href="{{ route('admin.extra-questions.edit', $q) }}" class="btn-secondary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">
                                        Bearbeiten
                                    </a>
                                    <form method="POST" action="{{ route('admin.extra-questions.destroy', $q) }}"
                                          onsubmit="return confirm('Zusatz-Frage wirklich löschen?')" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost" style="padding: 0.4rem 0.65rem; color: var(--error); font-size: 0.8rem;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
