@extends('layouts.app')
@section('title', 'Nutzerverwaltung - THW Trainer Admin')

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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1.25rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    .info-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00337F;
        margin: 0 0 1.5rem 0;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
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

    .table-container {
        overflow-x: auto;
        border-radius: 0.75rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    th {
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.15s;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    td {
        padding: 1rem;
        font-size: 0.875rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .badge-blue {
        background: rgba(37, 99, 235, 0.1);
        color: #1e40af;
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .badge-green {
        background: rgba(34, 197, 94, 0.1);
        color: #15803d;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .badge-red {
        background: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .badge-gray {
        background: rgba(107, 114, 128, 0.1);
        color: #374151;
        border: 1px solid rgba(107, 114, 128, 0.3);
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 0.85rem;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn-details {
        background: white;
        color: #4b5563;
        border-color: #d1d5db;
    }

    .action-btn-details:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .action-btn-progress {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #1e3a8a;
        border-color: #f59e0b;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .action-btn-progress:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
    }

    .action-btn-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-color: #dc2626;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .action-btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }

    .action-btn svg {
        width: 1rem;
        height: 1rem;
    }

    .details-row {
        background: #f9fafb;
    }

    .details-content {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.625rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-static {
        padding: 0.625rem;
        background: #f3f4f6;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    .mobile-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .mobile-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .mobile-card-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .mobile-card-email {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .mobile-card-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.5rem;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        .dashboard-greeting {
            font-size: 1.75rem;
        }
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .stat-card {
            padding: 1rem;
        }
        .stat-icon {
            font-size: 2rem;
        }
        .stat-value {
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">👥 <span>Nutzerverwaltung</span></h1>
            <p class="dashboard-subtitle">Verwalte alle Benutzer und ihre Daten</p>
        </div>

        <div class="button-group">
            <a href="{{ route('admin.newsletter.create') }}" class="btn btn-primary">
                📧 Newsletter senden
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Zurück zum Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ❌ {{ session('error') }}
            </div>
        @endif

        <!-- Statistiken -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ $users->count() }}</div>
                <div class="stat-label">Gesamt Benutzer</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👑</div>
                <div class="stat-value">{{ $users->where('useroll', 'admin')->count() }}</div>
                <div class="stat-label">Administratoren</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div class="stat-value">{{ $users->where('useroll', 'user')->count() }}</div>
                <div class="stat-label">Benutzer</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ $users->whereNotNull('email_verified_at')->count() }}</div>
                <div class="stat-label">E-Mail bestätigt</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">❌</div>
                <div class="stat-value">{{ $users->whereNull('email_verified_at')->count() }}</div>
                <div class="stat-label">Nicht bestätigt</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📧</div>
                <div class="stat-value">{{ $users->where('email_consent', true)->count() }}</div>
                <div class="stat-label">E-Mail-Zustimmung</div>
            </div>
        </div>

        <!-- Benutzertabelle -->
        <div class="info-card">
            <h2 class="info-title">Alle Benutzer</h2>

            <!-- Desktop Tabelle -->
            <div class="hidden md:block table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rolle</th>
                            <th>Status</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>E-Mail Status</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr id="user-row-{{ $user->id }}">
                            <td>
                                <span class="badge badge-blue">{{ $user->id }}</span>
                            </td>
                            <td style="text-align: center;">
                                @if($user->useroll === 'admin')
                                    <span style="font-size: 1.5rem;" title="Administrator">👑</span>
                                @else
                                    <span style="font-size: 1.5rem;" title="Benutzer">🎓</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($user->is_online ?? false)
                                    <span style="font-size: 1.5rem;" title="🟢 Online (letzte Session: {{ $user->updated_at->diffForHumans() }}, letzte Lern-Aktivität: {{ $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date)->diffForHumans() : 'Nie' }})">🟢</span>
                                @else
                                    <span style="font-size: 1.5rem;" title="🔴 Offline (letzte Session: {{ $user->updated_at->diffForHumans() }}, letzte Lern-Aktivität: {{ $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date)->diffForHumans() : 'Nie' }})">🔴</span>
                                @endif
                            </td>
                            <td style="font-weight: 600; color: #1f2937;">
                                {{ $user->name }}
                            </td>
                            <td style="color: #4b5563;">
                                {{ $user->email }}
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge badge-green">✅ Bestätigt</span>
                                @else
                                    <span class="badge badge-red">❌ Nicht bestätigt</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <button onclick="toggleUserDetails({{ $user->id }})" class="action-btn action-btn-details" title="Details anzeigen/verbergen">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        Details
                                    </button>

                                    <a href="{{ route('admin.users.progress.edit', $user->id) }}" class="action-btn action-btn-progress" title="Fortschritt bearbeiten">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        Fortschritt
                                    </a>

                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete" title="Benutzer löschen" onclick="return confirm('Benutzer {{ $user->name }} wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!')">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Löschen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Aufklappbare Details -->
                        <tr id="user-details-{{ $user->id }}" class="hidden details-row">
                            <td colspan="7" style="padding: 1.5rem;">
                                <div class="details-content">
                                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Benutzerdetails</h3>

                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="name" value="{{ $user->name }}" class="form-input" />
                                            </div>

                                            <div class="form-group">
                                                <label>E-Mail</label>
                                                <input type="email" name="email" value="{{ $user->email }}" class="form-input" />
                                            </div>

                                            <div class="form-group">
                                                <label>Rolle</label>
                                                <select name="useroll" class="form-select">
                                                    <option value="user" @if($user->useroll === 'user') selected @endif>🎓 Benutzer</option>
                                                    <option value="admin" @if($user->useroll === 'admin') selected @endif>👑 Administrator</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Registriert am</label>
                                                <div class="form-static">{{ $user->created_at->format('d.m.Y H:i') }}</div>
                                            </div>

                                            <div class="form-group">
                                                <label>Letzte Aktivität</label>
                                                <div class="form-static">{{ $user->updated_at->format('d.m.Y H:i') }}</div>
                                            </div>

                                            <div class="form-group">
                                                <label>E-Mail Status</label>
                                                <div class="form-static">
                                                    @if($user->email_verified_at)
                                                        <span style="color: #15803d;">✅ Bestätigt am {{ $user->email_verified_at->format('d.m.Y H:i') }}</span>
                                                    @else
                                                        <span style="color: #991b1b;">❌ Nicht bestätigt</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>E-Mail-Zustimmung</label>
                                                <div class="form-static">
                                                    @if($user->email_consent)
                                                        <span style="color: #15803d;">📧 Zustimmung erteilt
                                                            @if($user->email_consent_at)
                                                                am {{ $user->email_consent_at->format('d.m.Y H:i') }}
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span style="color: #6b7280;">📧 Keine Zustimmung erteilt</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div style="display: flex; justify-content: flex-end; padding-top: 1rem;">
                                            <button type="submit" class="btn btn-primary">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Änderungen speichern
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Karten -->
            <div class="block md:hidden">
                @foreach($users as $user)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span class="badge badge-blue">ID: {{ $user->id }}</span>
                            @if($user->useroll === 'admin')
                                <span style="font-size: 1.5rem;" title="Administrator">👑</span>
                            @else
                                <span style="font-size: 1.5rem;" title="Benutzer">🎓</span>
                            @endif
                            @if($user->is_online ?? false)
                                <span style="font-size: 1.5rem;" title="🟢 Online">🟢</span>
                            @else
                                <span style="font-size: 1.5rem;" title="🔴 Offline">🔴</span>
                            @endif
                        </div>
                    </div>

                    <div class="mobile-card-name">{{ $user->name }}</div>
                    <div class="mobile-card-email">{{ $user->email }}</div>

                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                        @if($user->email_verified_at)
                            <span class="badge badge-green">✅ E-Mail bestätigt</span>
                        @else
                            <span class="badge badge-red">❌ E-Mail nicht bestätigt</span>
                        @endif

                        @if($user->email_consent)
                            <span class="badge badge-green">📧 Zustimmung</span>
                        @else
                            <span class="badge badge-gray">📧 Keine Zustimmung</span>
                        @endif
                    </div>

                    <!-- Aufklappbare Details Mobile -->
                    <div id="mobile-details-{{ $user->id }}" class="hidden" style="margin-bottom: 1rem;">
                        <div class="details-content">
                            <h4 style="font-size: 1rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Details</h4>

                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" value="{{ $user->name }}" class="form-input" />
                                    </div>

                                    <div class="form-group">
                                        <label>E-Mail</label>
                                        <input type="email" name="email" value="{{ $user->email }}" class="form-input" />
                                    </div>

                                    <div class="form-group">
                                        <label>Rolle</label>
                                        <select name="useroll" class="form-select">
                                            <option value="user" @if($user->useroll === 'user') selected @endif>🎓 Benutzer</option>
                                            <option value="admin" @if($user->useroll === 'admin') selected @endif>👑 Administrator</option>
                                        </select>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.875rem;">
                                        <div>
                                            <div style="color: #6b7280; margin-bottom: 0.25rem;">Registriert:</div>
                                            <div style="font-weight: 600;">{{ $user->created_at->format('d.m.Y') }}</div>
                                        </div>
                                        <div>
                                            <div style="color: #6b7280; margin-bottom: 0.25rem;">Letzte Aktivität:</div>
                                            <div style="font-weight: 600;">{{ $user->updated_at->format('d.m.Y') }}</div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Änderungen speichern
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mobile-card-actions">
                        <button onclick="toggleMobileDetails({{ $user->id }})" class="action-btn action-btn-details" style="justify-content: center;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            Details
                        </button>

                        <a href="{{ route('admin.users.progress.edit', $user->id) }}" class="action-btn action-btn-progress" style="justify-content: center;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Fortschritt
                        </a>

                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="grid-column: 1 / -1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn-delete" style="width: 100%; justify-content: center;" onclick="return confirm('Benutzer {{ $user->name }} wirklich löschen?')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Löschen
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Footer Navigation -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; flex-wrap: wrap; gap: 1rem;">
            <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
                📝 Zur Fragenverwaltung
            </a>

            <div style="color: #6b7280; font-weight: 600;">
                Gesamt: {{ $users->count() }} Benutzer
            </div>
        </div>
    </div>
</div>

<script>
    function toggleUserDetails(userId) {
        const detailsRow = document.getElementById('user-details-' + userId);
        const button = event.target.closest('button');
        const icon = button.querySelector('svg path');

        if (detailsRow.classList.contains('hidden')) {
            detailsRow.classList.remove('hidden');
            icon.setAttribute('d', 'M5 15l7-7 7 7');
            button.innerHTML = button.innerHTML.replace('Details', 'Verbergen');
        } else {
            detailsRow.classList.add('hidden');
            icon.setAttribute('d', 'M19 9l-7 7-7-7');
            button.innerHTML = button.innerHTML.replace('Verbergen', 'Details');
        }
    }

    function toggleMobileDetails(userId) {
        const detailsDiv = document.getElementById('mobile-details-' + userId);
        const button = event.target.closest('button');
        const icon = button.querySelector('svg path');

        if (detailsDiv.classList.contains('hidden')) {
            detailsDiv.classList.remove('hidden');
            icon.setAttribute('d', 'M5 15l7-7 7 7');
            button.innerHTML = button.innerHTML.replace('Details', 'Verbergen');
        } else {
            detailsDiv.classList.add('hidden');
            icon.setAttribute('d', 'M19 9l-7 7-7-7');
            button.innerHTML = button.innerHTML.replace('Verbergen', 'Details');
        }
    }
</script>
@endsection
