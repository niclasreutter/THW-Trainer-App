@extends('layouts.app')

@section('title', $logTitle . '-Logs - Admin')

@push('styles')
<style>
    .log-container {
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.15) transparent;
    }
    .log-line {
        padding: 0.3rem 0.75rem;
        font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
        font-size: 0.8rem;
        line-height: 1.6;
        white-space: pre-wrap;
        word-break: break-all;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        color: #1e293b;
    }
    .log-line:nth-child(even) {
        background: rgba(0,0,0,0.02);
    }
    .log-line:hover {
        background: rgba(0,0,0,0.04);
    }
    .log-line-highlight {
        background: rgba(255, 215, 0, 0.12) !important;
    }
    .log-toolbar {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .log-input {
        padding: 0.625rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        color: var(--text-primary);
        outline: none;
        font-size: 0.875rem;
    }
    .log-input:focus {
        border-color: rgba(255, 215, 0, 0.4);
    }
    .log-input::placeholder {
        color: var(--text-muted);
    }
    .log-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .log-tab {
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s;
    }
    .log-tab:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.06);
    }
    .log-tab-active {
        color: var(--gold);
        background: rgba(255, 215, 0, 0.08);
        border-color: rgba(255, 215, 0, 0.25);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">{{ $logTitle }} <span>Logs</span></h1>
        <p class="page-subtitle">{{ $logSubtitle }}</p>
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

    {{-- Tab Navigation --}}
    <div class="log-tabs">
        @foreach($logTypes as $key => $config)
            <a href="{{ route('admin.logs.index', $key) }}"
               class="log-tab {{ $logType === $key ? 'log-tab-active' : '' }}">
                {{ $config['title'] }}-Log
            </a>
        @endforeach
    </div>

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-hdd"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['size_formatted'] }}</div>
                <div class="stat-pill-label">Dateigröße</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-list-ol"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['total_lines']) }}</div>
                <div class="stat-pill-label">Zeilen gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-clock"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['last_modified'] ?? '-' }}</div>
                <div class="stat-pill-label">Letzte Änderung</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-eye"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ count($lines) }}</div>
                <div class="stat-pill-label">Angezeigte Zeilen</div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="glass" style="padding: 1.25rem; margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.logs.index', $logType) }}" class="log-toolbar">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Log durchsuchen..."
                   class="log-input"
                   style="flex: 1; min-width: 200px;">

            <select name="lines" class="log-input">
                @foreach([100, 500, 1000, 2000] as $opt)
                    <option value="{{ $opt }}" {{ (int) $linesLimit === $opt ? 'selected' : '' }}>{{ $opt }} Zeilen</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary" style="padding: 0.625rem 1.25rem;">Filtern</button>
            <a href="{{ route('admin.logs.index', $logType) }}" class="btn-ghost" style="padding: 0.625rem 1.25rem;">Zurücksetzen</a>

            <div style="margin-left: auto;">
                <form method="POST" action="{{ route('admin.logs.destroy', $logType) }}"
                      onsubmit="return confirm('{{ $logTitle }}-Log wirklich leeren? Diese Aktion kann nicht rückgängig gemacht werden.')"
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" style="padding: 0.625rem 1.25rem;">Log leeren</button>
                </form>
            </div>
        </form>
    </div>

    {{-- Log Content --}}
    <div class="glass" style="padding: 1.5rem;">
        @if($stats['exists'] && count($lines) > 0)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted);">
                    Neueste Einträge zuerst
                    @if($search)
                        &mdash; Suche: <strong style="color: var(--text-primary);">{{ $search }}</strong>
                    @endif
                </span>
            </div>
            <div class="log-container">
                @foreach($lines as $line)
                    <div class="log-line {{ $search && stripos($line, $search) !== false ? 'log-line-highlight' : '' }}">{{ $line }}</div>
                @endforeach
            </div>
        @else
            <div style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-terminal"></i></div>
                <p style="margin: 0 0 0.5rem 0; font-weight: 700;">Keine Log-Einträge vorhanden</p>
                <p style="margin: 0; font-size: 0.95rem;">Sobald Prozesse ausgeführt werden, erscheinen die Ausgaben hier.</p>
            </div>
        @endif
    </div>
</div>
@endsection
