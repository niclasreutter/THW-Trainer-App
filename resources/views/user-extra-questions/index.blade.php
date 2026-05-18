@extends('layouts.app')
@section('title', 'Meine Zusatz-Fragen - THW Trainer')
@section('description', 'Übersicht deiner eingereichten Zusatz-Fragen und deren Status.')

@push('styles')
@include('admin.extra-questions.partials._styles')
<style>
    .ueq-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        border-radius: 0.625rem;
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.08);
        margin-bottom: 0.5rem;
        transition: background 0.15s ease;
    }
    .ueq-row__title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.15rem;
        line-height: 1.4;
    }
    .ueq-row__meta {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }
    .ueq-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
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
    .ueq-admin-note {
        font-size: 0.875rem;
        color: var(--text-secondary);
        background: rgba(217, 119, 6, 0.08);
        border-left: 3px solid #d97706;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        margin-top: 0.5rem;
        white-space: pre-wrap;
    }
    .ueq-empty {
        padding: 3rem 1.5rem;
        text-align: center;
        color: var(--text-muted);
    }
    .ueq-empty__icon {
        font-size: 2.25rem;
        color: var(--thw-blue);
        margin-bottom: 0.75rem;
    }
    html:not(.light-mode) .ueq-empty__icon { color: #5b9aff; }
</style>
@endpush

@section('content')
<div class="dash-container" style="max-width: 60rem;">
    <div class="zf-page-title">
        <div class="zf-page-title__eyebrow">Zusatz-Fragen vorschlagen</div>
        <h1 class="zf-page-title__h1">Meine Einreichungen</h1>
        <div class="zf-page-title__sub">Hier siehst du alle Zusatz-Fragen, die du eingereicht hast, und deren Status.</div>
    </div>

    @if(session('success'))
        <div class="zf-alert zf-alert--success">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="zf-action-row">
        <div></div>
        <a href="{{ route('zusatzfragen-vorschlagen.create') }}" class="btn-primary">
            <i class="bi bi-plus-lg"></i> Neue Zusatz-Frage vorschlagen
        </a>
    </div>

    @if($submissions->count() === 0)
        <div class="zf-form-card ueq-empty">
            <div class="ueq-empty__icon"><i class="bi bi-lightbulb"></i></div>
            <p style="margin: 0 0 1rem;">Du hast noch keine Zusatz-Fragen eingereicht.</p>
            <a href="{{ route('zusatzfragen-vorschlagen.create') }}" class="btn-primary">
                Jetzt erste Frage vorschlagen
            </a>
        </div>
    @else
        <div class="zf-form-card">
            @foreach($submissions as $s)
                <div class="ueq-row">
                    <div>
                        <div class="ueq-row__title">{{ \Illuminate\Support\Str::limit($s->frage, 140) }}</div>
                        <div class="ueq-row__meta">
                            {{ $s->typLabel() }} · Lernabschnitt {{ $s->lernabschnitt }} · {{ $s->created_at->format('d.m.Y H:i') }}
                        </div>
                        @if($s->admin_notes)
                            <div class="ueq-admin-note">
                                <strong>Hinweis vom Admin:</strong> {{ $s->admin_notes }}
                            </div>
                        @endif
                    </div>

                    <span class="ueq-status ueq-status--{{ $s->status }}">
                        @switch($s->status)
                            @case('pending')   <i class="bi bi-hourglass-split"></i> @break
                            @case('approved')  <i class="bi bi-check-circle-fill"></i> @break
                            @case('changed')   <i class="bi bi-pencil-square"></i> @break
                            @case('rejected')  <i class="bi bi-x-circle-fill"></i> @break
                        @endswitch
                        {{ $s->statusLabel() }}
                    </span>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        @if($s->isPending() && in_array($s->typ, ['matching', 'pair_matching'], true))
                            <a href="{{ route('zusatzfragen-vorschlagen.tryout', $s) }}" class="btn-ghost" title="Try-Out / Vorschau">
                                <i class="bi bi-play-fill"></i>
                            </a>
                        @endif
                        @if($s->isPending())
                            <form method="POST" action="{{ route('zusatzfragen-vorschlagen.destroy', $s) }}"
                                  onsubmit="return confirm('Diese Einreichung wirklich löschen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost" title="Einreichung löschen">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1rem;">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
@endsection
