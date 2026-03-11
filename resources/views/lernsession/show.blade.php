@extends('layouts.app')
@section('title', $lernsession->title . ' - THW Trainer')

@push('styles')
<style>
    .dashboard-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .detail-card {
        padding: 1.5rem;
    }

    .detail-card-full {
        grid-column: span 2;
        padding: 1.5rem;
    }

    .detail-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.3rem;
    }

    .detail-value {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .instance-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .instance-item:last-child {
        border-bottom: none;
    }

    .badge-active {
        display: inline-flex;
        padding: 0.15rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(34, 197, 94, 0.15);
        color: var(--success);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .badge-completed {
        display: inline-flex;
        padding: 0.15rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(255,255,255,0.05);
        color: var(--text-muted);
        border: 1px solid rgba(255,255,255,0.1);
    }

    @media (max-width: 640px) {
        .dashboard-container { padding: 1rem; }
        .detail-grid { grid-template-columns: 1fr; }
        .detail-card-full { grid-column: span 1; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">{{ $lernsession->title }}</h1>
        @if($lernsession->description)
            <p class="page-subtitle">{{ $lernsession->description }}</p>
        @endif
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="detail-grid">
        <div class="glass-tl detail-card">
            <div class="detail-label">Erstellt von</div>
            <div class="detail-value">{{ $lernsession->creator->name }}</div>
        </div>

        <div class="glass detail-card">
            <div class="detail-label">Geltungsbereich</div>
            <div class="detail-value">
                {{ $lernsession->isGlobal() ? 'Global' : $lernsession->ortsverband->name }}
            </div>
        </div>

        <div class="glass detail-card">
            <div class="detail-label">Fragenquelle</div>
            <div class="detail-value">
                @if($lernsession->question_source === 'all')
                    Alle Fragen
                @elseif($lernsession->question_source === 'sections')
                    Abschnitte: {{ implode(', ', array_map(fn($s) => $sections[$s] ?? $s, $lernsession->question_sections ?? [])) }}
                @elseif($lernsession->question_source === 'lernpool')
                    Lernpool: {{ $lernsession->lernpool->name ?? 'Unbekannt' }}
                @endif
            </div>
        </div>

        <div class="glass-br detail-card">
            <div class="detail-label">Zeitplanung</div>
            <div class="detail-value">
                @if($lernsession->schedule_type === 'once')
                    Einmalig: {{ $lernsession->starts_at->format('d.m.Y H:i') }} - {{ $lernsession->ends_at->format('H:i') }} Uhr
                @else
                    @php
                        $dayNames = [1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 7 => 'So'];
                        $days = array_map(fn($d) => $dayNames[$d] ?? $d, $lernsession->recurrence_days ?? []);
                    @endphp
                    Jeden {{ implode(', ', $days) }} von {{ $lernsession->recurrence_start_time }} - {{ $lernsession->recurrence_end_time }} Uhr
                @endif
            </div>
        </div>
    </div>

    {{-- Aktive Instanz --}}
    @if($activeInstance)
        <div class="glass-gold detail-card-full" style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="badge-active">Jetzt aktiv</span>
                    <span style="color: var(--text-secondary); font-size: 0.85rem; margin-left: 0.5rem;">
                        Noch {{ $activeInstance->getTimeRemainingMinutes() }} Minuten
                        &middot; {{ $activeInstance->getParticipantCount() }} Teilnehmer
                    </span>
                </div>
                <a href="{{ route('lernsession.live', $activeInstance) }}" class="btn-primary btn-sm">Teilnehmen</a>
            </div>
        </div>
    @elseif($nextInstance)
        <div class="glass-accent detail-card-full" style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-weight: 600; color: var(--text-primary);">Nächste Session</span>
                    <span style="color: var(--text-secondary); font-size: 0.85rem; margin-left: 0.5rem;">
                        {{ $nextInstance->starts_at->format('d.m.Y H:i') }} Uhr
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- Vergangene Instanzen --}}
    @if($pastInstances->isNotEmpty())
        <div class="glass detail-card-full">
            <div class="detail-label" style="margin-bottom: 0.75rem;">Vergangene Sessions</div>
            @foreach($pastInstances as $past)
                <div class="instance-item">
                    <div>
                        <span style="font-size: 0.9rem; color: var(--text-primary);">
                            {{ $past->starts_at->format('d.m.Y H:i') }} - {{ $past->ends_at->format('H:i') }}
                        </span>
                        <span class="badge-completed">Beendet</span>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                        @if($past->winner)
                            Gewinner: {{ $past->winner->name }}
                        @else
                            Kein Gewinner
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Aktionen --}}
    @can('update', $lernsession)
        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
            <a href="{{ route('lernsession.edit', $lernsession) }}" class="btn-secondary">Bearbeiten</a>
            <form action="{{ route('lernsession.destroy', $lernsession) }}" method="POST"
                  onsubmit="return confirm('Lernsession wirklich löschen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Löschen</button>
            </form>
        </div>
    @endcan

    <div style="margin-top: 1rem;">
        <a href="{{ route('lernsession.index') }}" class="btn-ghost">Zurück zur Übersicht</a>
    </div>
</div>
@endsection
