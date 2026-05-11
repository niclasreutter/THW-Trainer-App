@extends('layouts.app')
@section('title', 'Eingereichte Zusatz-Fragen - THW Trainer Admin')
@section('description', 'Verwalte von Usern eingereichte Zusatz-Fragen.')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .ueq-status {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.55rem;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .ueq-status--pending  { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .ueq-status--approved { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }
    .ueq-status--changed  { background: rgba(217, 119, 6, 0.12);  color: #d97706; }
    .ueq-status--rejected { background: rgba(239, 68, 68, 0.12);  color: #ef4444; }
    html:not(.light-mode) .ueq-status--pending  { background: rgba(96, 165, 250, 0.18); color: #93c5fd; }
    html:not(.light-mode) .ueq-status--approved { background: rgba(34, 197, 94, 0.18);  color: #86efac; }
    html:not(.light-mode) .ueq-status--changed  { background: rgba(245, 158, 11, 0.18); color: #fcd34d; }
    html:not(.light-mode) .ueq-status--rejected { background: rgba(248, 113, 113, 0.18); color: #fca5a5; }

    .ueq-row-link {
        display: grid;
        grid-template-columns: 1fr auto auto auto;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        border-radius: 0.625rem;
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.08);
        margin-bottom: 0.5rem;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s ease;
    }
    .ueq-row-link:hover { background: rgba(0, 51, 127, 0.04); }
    html:not(.light-mode) .ueq-row-link:hover { background: rgba(91, 154, 255, 0.06); }
    .ueq-row-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
    }
    .ueq-row-meta {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
    .ueq-filter-bar {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .ueq-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.10);
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .ueq-filter-chip:hover { border-color: var(--thw-blue); }
    .ueq-filter-chip.is-active { background: var(--thw-blue); border-color: var(--thw-blue); color: #fff; }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Eingereichte <span>Zusatz-Fragen</span></h1>
        <p class="page-subtitle">Von Usern eingereichte Vorschläge prüfen und übernehmen.</p>
    </header>

    @if(session('success'))
        <div class="zf-alert zf-alert--success">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="zf-stat-pills">
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value">{{ $stats['total'] }}</div>
            <div class="zf-stat-pill__label">Gesamt</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value zf-stat-pill__value--blue">{{ $stats['pending'] }}</div>
            <div class="zf-stat-pill__label">Offen</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value zf-stat-pill__value--ok">{{ $stats['approved'] + $stats['changed'] }}</div>
            <div class="zf-stat-pill__label">Übernommen</div>
        </div>
        <div class="zf-stat-pill">
            <div class="zf-stat-pill__value zf-stat-pill__value--warn">{{ $stats['rejected'] }}</div>
            <div class="zf-stat-pill__label">Abgelehnt</div>
        </div>
    </div>

    <div class="ueq-filter-bar">
        @php $currentStatus = request('status'); @endphp
        <a href="{{ route('admin.extra-question-submissions.index') }}"
           class="ueq-filter-chip {{ !$currentStatus ? 'is-active' : '' }}">Alle</a>
        <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'pending']) }}"
           class="ueq-filter-chip {{ $currentStatus === 'pending' ? 'is-active' : '' }}">Offen</a>
        <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'approved']) }}"
           class="ueq-filter-chip {{ $currentStatus === 'approved' ? 'is-active' : '' }}">Übernommen</a>
        <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'changed']) }}"
           class="ueq-filter-chip {{ $currentStatus === 'changed' ? 'is-active' : '' }}">Mit Änderungen</a>
        <a href="{{ route('admin.extra-question-submissions.index', ['status' => 'rejected']) }}"
           class="ueq-filter-chip {{ $currentStatus === 'rejected' ? 'is-active' : '' }}">Abgelehnt</a>
    </div>

    @if($submissions->count() === 0)
        <div class="zf-form-card" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
            <div style="font-size: 2rem; margin-bottom: 0.75rem; color: var(--thw-blue);">
                <i class="bi bi-inbox"></i>
            </div>
            <p style="margin: 0;">Keine Einreichungen gefunden.</p>
        </div>
    @else
        <div class="zf-form-card">
            @foreach($submissions as $s)
                <a href="{{ route('admin.extra-question-submissions.show', $s) }}" class="ueq-row-link">
                    <div>
                        <div class="ueq-row-title">{{ \Illuminate\Support\Str::limit($s->frage, 140) }}</div>
                        <div class="ueq-row-meta">
                            {{ $s->user->name ?? 'Unbekannt' }} · {{ $s->typLabel() }} · Lernabschnitt {{ $s->lernabschnitt }} · {{ $s->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    <span class="ueq-status ueq-status--{{ $s->status }}">{{ $s->statusLabel() }}</span>
                    <div></div>
                    <i class="bi bi-chevron-right" style="color: var(--text-muted);"></i>
                </a>
            @endforeach
        </div>

        <div style="margin-top: 1rem;">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
@endsection
