@extends('layouts.app')
@section('title', 'Eingereichte Zusatz-Fragen - THW Trainer Admin')
@section('description', 'Verwalte von Usern eingereichte Zusatz-Fragen.')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem; }
    @media (max-width: 640px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    .kpi {
        position: relative; padding: 1rem 1.125rem;
        border-radius: 0.875rem;
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        overflow: hidden;
    }
    html.light-mode .kpi { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
    .kpi__label {
        font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted);
        margin-bottom: 0.375rem;
    }
    .kpi__value {
        font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 1.875rem;
        line-height: 1.05; letter-spacing: -0.015em; color: var(--text-primary);
    }
    .kpi__value--blue  { color: #5b9aff; }
    html.light-mode .kpi__value--blue { color: var(--thw-blue); }
    .kpi__value--red   { color: #ef4444; }
    .kpi__value--gold  { color: #fbbf24; }
    .kpi__value--green { color: #22c55e; }

    .ueq-status-badge {
        display: inline-block;
        padding: 0.3rem 0.7rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .ueq-status-badge--pending  { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .ueq-status-badge--approved { background: rgba(34, 197, 94, 0.2);  color: #22c55e; }
    .ueq-status-badge--changed  { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .ueq-status-badge--rejected { background: rgba(255, 255, 255, 0.1); color: var(--text-secondary); }
    html.light-mode .ueq-status-badge--rejected { background: rgba(0, 51, 127, 0.06); color: var(--text-secondary); }

    .ueq-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }
    html.light-mode .ueq-row {
        background: rgba(0, 51, 127, 0.025);
        border-color: rgba(0, 51, 127, 0.08);
    }
    .ueq-row + .ueq-row { margin-top: 0.5rem; }
    .ueq-row:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(91, 154, 255, 0.25);
        transform: translateX(3px);
    }
    html.light-mode .ueq-row:hover {
        background: rgba(0, 51, 127, 0.05);
        border-color: rgba(0, 51, 127, 0.25);
    }
    .ueq-row__title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
    }
    .ueq-row__meta {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }
    .ueq-row__chevron { color: var(--text-muted); font-size: 1rem; }

    @media (max-width: 640px) {
        .ueq-row { grid-template-columns: 1fr auto; }
        .ueq-row__chevron { display: none; }
    }
</style>
@endpush

@section('content')
<div class="dash-container" style="max-width: 60rem;">
    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">Admin · Zusatz-Fragen</div>
        <h1 class="zf-page-title__h1">Eingereichte Zusatz-Fragen</h1>
        <div class="zf-page-title__sub">Von Usern eingereichte Vorschläge prüfen und übernehmen.</div>
    </div>

    @if(session('success'))
        <div class="glass-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 0.875rem; align-items: flex-start;">
            <i class="bi bi-check-circle-fill" style="font-size: 1.125rem; flex-shrink: 0;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi__label">Gesamt</div>
            <div class="kpi__value kpi__value--blue">{{ $stats['total'] }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Offen</div>
            <div class="kpi__value kpi__value--gold">{{ $stats['pending'] }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Übernommen</div>
            <div class="kpi__value kpi__value--green">{{ $stats['approved'] + $stats['changed'] }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Abgelehnt</div>
            <div class="kpi__value kpi__value--red">{{ $stats['rejected'] }}</div>
        </div>
    </div>

    @php $currentStatus = request('status'); @endphp
    <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Filter nach Status</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <a href="{{ route('admin.extra-question-submissions.index') }}"
               class="btn-{{ !$currentStatus ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Alle
            </a>
            <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'pending']) }}"
               class="btn-{{ $currentStatus === 'pending' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Offen
            </a>
            <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'approved']) }}"
               class="btn-{{ $currentStatus === 'approved' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Übernommen
            </a>
            <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'changed']) }}"
               class="btn-{{ $currentStatus === 'changed' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Mit Änderungen
            </a>
            <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'rejected']) }}"
               class="btn-{{ $currentStatus === 'rejected' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Abgelehnt
            </a>
        </div>
    </div>

    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
        <h2 class="section-title">Einreichungen{{ $submissions->total() > 0 ? ' · ' . $submissions->total() : '' }}</h2>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem;">
        @if($submissions->count() === 0)
            <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted);">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem; color: var(--thw-blue);"><i class="bi bi-inbox"></i></div>
                <p style="margin: 0;">Keine Einreichungen gefunden.</p>
            </div>
        @else
            @foreach($submissions as $s)
                <a href="{{ route('admin.extra-question-submissions.show', $s) }}" class="ueq-row">
                    <div style="min-width: 0;">
                        <div class="ueq-row__title">{{ \Illuminate\Support\Str::limit($s->frage, 140) }}</div>
                        <div class="ueq-row__meta">
                            {{ $s->user->name ?? 'Unbekannt' }} · {{ $s->typLabel() }} · Lernabschnitt {{ $s->lernabschnitt }} · {{ $s->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    <span class="ueq-status-badge ueq-status-badge--{{ $s->status }}">{{ $s->statusLabel() }}</span>
                    <i class="bi bi-chevron-right ueq-row__chevron"></i>
                </a>
            @endforeach

            <div style="margin-top: 1.5rem;">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
