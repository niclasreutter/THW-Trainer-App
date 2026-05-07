@extends('layouts.app')

@section('title', 'Worker-Fehler - Admin')

@push('styles')
<style>
    .fj-toolbar {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .fj-input {
        padding: 0.625rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        color: var(--text-primary);
        outline: none;
        font-size: 0.875rem;
    }
    .fj-input:focus {
        border-color: rgba(255, 215, 0, 0.4);
    }
    .fj-input::placeholder {
        color: var(--text-muted);
    }
    .fj-card {
        padding: 1.25rem 1.5rem;
        border-radius: 0.875rem;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        margin-bottom: 1rem;
    }
    .fj-card__head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 1rem;
        margin-bottom: 0.75rem;
    }
    .fj-job-name {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
        word-break: break-all;
    }
    .fj-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        color: var(--text-secondary);
    }
    .fj-badge--err {
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.25);
    }
    .fj-badge--time {
        color: var(--text-muted);
    }
    .fj-exception {
        margin-top: 0.5rem;
        padding: 0.75rem 1rem;
        background: rgba(239, 68, 68, 0.06);
        border-left: 3px solid rgba(239, 68, 68, 0.5);
        border-radius: 0.375rem;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 0.8rem;
        color: #fecaca;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .fj-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.85rem;
        flex-wrap: wrap;
    }
    .fj-actions form { margin: 0; }
    .fj-detail-toggle {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.1);
        color: var(--text-secondary);
        padding: 0.4rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .fj-detail-toggle:hover {
        color: var(--text-primary);
        border-color: rgba(255,255,255,0.2);
    }
    .fj-detail {
        margin-top: 0.75rem;
        padding: 0.75rem 1rem;
        background: rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 0.5rem;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 0.75rem;
        color: var(--text-secondary);
        max-height: 320px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .fj-detail h4 {
        margin: 0 0 0.4rem 0;
        font-size: 0.7rem;
        font-family: 'IBM Plex Mono', monospace;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 700;
    }
    .fj-detail + .fj-detail { margin-top: 0.5rem; }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Worker <span>Fehler</span></h1>
        <p class="page-subtitle">Fehlgeschlagene Queue-Jobs prüfen, erneut ausführen oder löschen</p>
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

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-error">
                <i class="bi bi-exclamation-triangle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ number_format($totalCount, 0, ',', '.') }}</div>
                <div class="stat-pill-label">Fehlgeschlagen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-hourglass-split"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ number_format($pendingCount, 0, ',', '.') }}</div>
                <div class="stat-pill-label">In Queue</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-clock-history"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ number_format($last24h, 0, ',', '.') }}</div>
                <div class="stat-pill-label">Letzte 24h</div>
            </div>
        </div>

        <div class="stat-pill">
            <a href="{{ route('admin.logs.index', 'worker') }}" style="display: contents;">
                <span class="stat-pill-icon text-thw-blue">
                    <i class="bi bi-terminal"></i>
                </span>
                <div>
                    <div class="stat-pill-value" style="font-size: 1rem;">Worker-Log</div>
                    <div class="stat-pill-label">Volltext öffnen</div>
                </div>
            </a>
        </div>
    </div>

    <div class="glass" style="padding: 1.25rem; margin-bottom: 1.5rem;">
        <div class="fj-toolbar">
            <form method="GET" action="{{ route('admin.failed-jobs.index') }}" style="display: contents;">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Exception, Payload oder Queue durchsuchen..."
                       class="fj-input"
                       style="flex: 1; min-width: 220px;">

                <select name="queue" class="fj-input">
                    <option value="">Alle Queues</option>
                    @foreach($availableQueues as $q)
                        <option value="{{ $q }}" {{ $queueFilter === $q ? 'selected' : '' }}>{{ $q }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn-primary" style="padding: 0.625rem 1.25rem;">Filtern</button>
                <a href="{{ route('admin.failed-jobs.index') }}" class="btn-ghost" style="padding: 0.625rem 1.25rem;">Zurücksetzen</a>
            </form>

            @if($totalCount > 0)
                <div style="display: flex; gap: 0.5rem; margin-left: auto;">
                    <form method="POST" action="{{ route('admin.failed-jobs.retry-all') }}"
                          onsubmit="return confirm('Wirklich alle {{ $totalCount }} fehlgeschlagenen Jobs erneut ausführen?')">
                        @csrf
                        <button type="submit" class="btn-secondary" style="padding: 0.625rem 1.25rem;">
                            <i class="bi bi-arrow-clockwise"></i> Alle erneut ausführen
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.failed-jobs.flush') }}"
                          onsubmit="return confirm('Wirklich alle {{ $totalCount }} fehlgeschlagenen Jobs unwiderruflich löschen?')">
                        @csrf
                        <button type="submit" class="btn-danger" style="padding: 0.625rem 1.25rem;">
                            <i class="bi bi-trash"></i> Alle löschen
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @if($failedJobs->total() === 0)
        <div class="glass" style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-check2-circle"></i></div>
            <p style="margin: 0 0 0.5rem 0; font-weight: 700;">Keine fehlgeschlagenen Jobs</p>
            <p style="margin: 0; font-size: 0.95rem;">
                @if($search !== '' || $queueFilter !== '')
                    Für den aktuellen Filter wurden keine Einträge gefunden.
                @else
                    Alle Queue-Jobs laufen sauber durch.
                @endif
            </p>
        </div>
    @else
        @foreach($failedJobs as $job)
            <div class="fj-card" x-data="{ open: false }">
                <div class="fj-card__head">
                    <span class="fj-job-name">{{ $job->job_name }}</span>
                    @if($job->exception_class)
                        <span class="fj-badge fj-badge--err">{{ $job->exception_class }}</span>
                    @endif
                    <span class="fj-badge"><i class="bi bi-diagram-3"></i> {{ $job->queue }}</span>
                    <span class="fj-badge"><i class="bi bi-hdd-network"></i> {{ $job->connection }}</span>
                    @if(!is_null($job->attempts))
                        <span class="fj-badge">Versuche: {{ $job->attempts }}</span>
                    @endif
                    <span class="fj-badge fj-badge--time" style="margin-left: auto;">
                        <i class="bi bi-clock"></i>
                        {{ \Carbon\Carbon::parse($job->failed_at)->format('d.m.Y H:i:s') }}
                        ({{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }})
                    </span>
                </div>

                @if($job->exception_first)
                    <div class="fj-exception">{{ $job->exception_first }}</div>
                @endif

                <div class="fj-actions">
                    <button type="button" class="fj-detail-toggle" @click="open = !open" x-text="open ? 'Details ausblenden' : 'Details anzeigen'">Details anzeigen</button>

                    <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->id) }}"
                          onsubmit="return confirm('Diesen Job erneut ausführen?')">
                        @csrf
                        <button type="submit" class="btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">
                            <i class="bi bi-arrow-clockwise"></i> Erneut ausführen
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.failed-jobs.destroy', $job->id) }}"
                          onsubmit="return confirm('Diesen Job-Eintrag löschen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">
                            <i class="bi bi-trash"></i> Löschen
                        </button>
                    </form>
                </div>

                <div x-show="open" x-cloak x-transition style="margin-top: 1rem;">
                    <div class="fj-detail">
                        <h4>Exception (vollständig)</h4>{{ $job->exception_full }}
                    </div>
                    <div class="fj-detail">
                        <h4>Payload</h4>{{ $job->payload }}
                    </div>
                    <div class="fj-detail">
                        <h4>UUID</h4>{{ $job->uuid }}
                    </div>
                </div>
            </div>
        @endforeach

        <div style="margin-top: 1.5rem;">
            {{ $failedJobs->links() }}
        </div>
    @endif
</div>
@endsection
