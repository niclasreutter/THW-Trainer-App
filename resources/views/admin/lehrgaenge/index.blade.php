@extends('layouts.app')

@section('title', 'Lehrgänge Verwalten')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Lehrgänge <span>Verwaltung</span></h1>
        <p class="page-subtitle">Verwalte alle verfügbaren THW-Lehrgänge</p>
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-x-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Fehler!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @php
        $totalQuestions = $lehrgaenge->sum('questions_count');
        $totalUsers = $lehrgaenge->sum(fn($l) => $l->users_count ?? 0);
        $avgQuestions = $lehrgaenge->count() ? round($lehrgaenge->avg('questions_count')) : 0;
    @endphp

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-book"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $lehrgaenge->total() }}</div>
                <div class="stat-pill-label">Lehrgänge</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-question-circle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $totalQuestions }}</div>
                <div class="stat-pill-label">Fragen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-people"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $totalUsers }}</div>
                <div class="stat-pill-label">Teilnehmer</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-graph-up"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $avgQuestions }}</div>
                <div class="stat-pill-label">Ø Fragen/Lehrgang</div>
            </div>
        </div>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-collection text-gold"></i>
                Alle Lehrgänge ({{ $lehrgaenge->total() }})
            </h2>
        </div>

        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.lehrgaenge.create') }}" class="btn-primary">
                <i class="bi bi-plus-circle"></i> Neuer Lehrgang
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
        </div>

        @if($lehrgaenge->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($lehrgaenge as $lehrgang)
                    <div class="glass" style="padding: 1.5rem; display: flex; flex-direction: column;">
                        <h3 style="margin: 0 0 0.75rem 0; font-size: 1.1rem; font-weight: 700;">{{ $lehrgang->lehrgang }}</h3>
                        <p style="margin: 0 0 1rem 0; color: var(--text-secondary); font-size: 0.95rem; flex: 1;">{{ Str::limit($lehrgang->beschreibung, 120) }}</p>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1rem 0; padding: 1rem 0; border-top: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            <div style="text-align: center;">
                                <div style="font-size: 1.75rem; font-weight: 700; color: var(--gold-start); margin-bottom: 0.25rem;">{{ $lehrgang->questions_count }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em;">Fragen</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.75rem; font-weight: 700; color: var(--gold-start); margin-bottom: 0.25rem;">{{ $lehrgang->users_count ?? 0 }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em;">Teilnehmer</div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ url('admin/lehrgaenge/' . $lehrgang->id) }}" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                <i class="bi bi-eye"></i> Details
                            </a>
                            <a href="{{ url('admin/lehrgaenge/' . $lehrgang->id . '/edit') }}" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                <i class="bi bi-pencil"></i> Bearbeiten
                            </a>
                            <form action="{{ url('admin/lehrgaenge/' . $lehrgang->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Wirklich löschen? Alle Fragen werden gelöscht!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost" style="padding: 0.5rem 1rem; font-size: 0.9rem; color: var(--error);">
                                    <i class="bi bi-trash"></i> Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem;">
                {{ $lehrgaenge->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-inbox"></i></div>
                <p>Noch keine Lehrgänge vorhanden. Erstelle deinen ersten Lehrgang!</p>
                <a href="{{ route('admin.lehrgaenge.create') }}" class="btn-primary" style="margin-top: 1rem; display: inline-block;">
                    <i class="bi bi-plus-circle"></i> Neuen Lehrgang erstellen
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
