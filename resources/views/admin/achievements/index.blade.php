@extends('layouts.app')
@section('title', 'Achievement Verwaltung - THW Trainer Admin')

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-top: 1rem;
    }

    .dashboard-greeting {
        font-size: 2.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dashboard-greeting span {
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
        margin-bottom: 0;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .achievements-table {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: linear-gradient(135deg, #00337F, #0055cc);
        color: white;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table tbody tr:hover {
        background: #f9fafb;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-general { background: #dbeafe; color: #1e40af; }
    .badge-questions { background: #fef3c7; color: #92400e; }
    .badge-streak { background: #fce7f3; color: #831843; }
    .badge-exam { background: #e0e7ff; color: #3730a3; }
    .badge-level { background: #ddd6fe; color: #5b21b6; }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .icon-lg {
        font-size: 1.5rem;
    }

    .actions-cell {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">🏆 <span>Achievement Verwaltung</span></h1>
            <p class="dashboard-subtitle">Verwalte alle Achievements</p>
        </div>

        <div class="button-group">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                ← Zurück zum Admin Dashboard
            </a>
            <a href="{{ route('admin.achievements.create') }}" class="btn btn-primary">
                ✨ Neues Achievement erstellen
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="achievements-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Titel</th>
                        <th>Trigger-Typ</th>
                        <th>Kategorie</th>
                        <th>Config</th>
                        <th>Status</th>
                        <th>Nutzer</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($achievements as $achievement)
                        <tr>
                            <td>
                                <span class="icon-lg">{{ $achievement->icon ?? '🏆' }}</span>
                            </td>
                            <td>
                                <strong>{{ $achievement->title }}</strong>
                                <br>
                                <small style="color: #6b7280;">{{ $achievement->description }}</small>
                                <br>
                                <small style="color: #9ca3af; font-size: 0.75rem;">Key: {{ $achievement->key }}</small>
                            </td>
                            <td>
                                <span class="badge badge-general" style="font-size: 0.75rem;">
                                    {{ \App\Models\Achievement::TRIGGER_TYPES[$achievement->trigger_type] ?? $achievement->trigger_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $achievement->category }}">
                                    {{ $categories[$achievement->category] ?? $achievement->category }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $config = $achievement->trigger_config ?? [];
                                @endphp
                                @if(isset($config['value']))
                                    <small style="color: #6b7280;">Wert: {{ $config['value'] }}</small>
                                @endif
                                @if(isset($config['section']))
                                    <br><small style="color: #6b7280;">Abschnitt: {{ $config['section'] }}</small>
                                @endif
                                @if(isset($config['any_section']) && $config['any_section'])
                                    <small style="color: #6b7280;">Beliebiger Abschnitt</small>
                                @endif
                                @if(empty($config))
                                    <small style="color: #9ca3af;">-</small>
                                @endif
                            </td>
                            <td>
                                @if($achievement->is_active)
                                    <span class="badge badge-active">Aktiv</span>
                                @else
                                    <span class="badge badge-inactive">Inaktiv</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $achievement->category }}">
                                    {{ $achievement->users_count }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('admin.achievements.edit', $achievement->id) }}"
                                       class="btn btn-warning btn-sm">
                                        ✏️ Bearbeiten
                                    </a>

                                    <form action="{{ route('admin.achievements.toggle-active', $achievement->id) }}"
                                          method="POST"
                                          style="margin: 0;">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-secondary btn-sm">
                                            @if($achievement->is_active)
                                                ⏸️ Deaktivieren
                                            @else
                                                ▶️ Aktivieren
                                            @endif
                                        </button>
                                    </form>

                                    @if($achievement->users_count == 0)
                                        <form action="{{ route('admin.achievements.destroy', $achievement->id) }}"
                                              method="POST"
                                              style="margin: 0;"
                                              onsubmit="return confirm('Achievement wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                🗑️ Löschen
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: #6b7280;">
                                Keine Achievements vorhanden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
