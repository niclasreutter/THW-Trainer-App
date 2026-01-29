@extends('layouts.app')
@section('title', 'Nutzerverwaltung - THW Trainer Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Nutzer<span>verwaltung</span></h1>
        <p class="page-subtitle">Verwalte alle Benutzer und ihre Daten</p>
    </header>

    <!-- Stats Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">{{ $users->count() }}</div>
                <div class="stat-pill-label">Gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-shield-check"></i></span>
            <div>
                <div class="stat-pill-value">{{ $users->where('useroll', 'admin')->count() }}</div>
                <div class="stat-pill-label">Admins</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-dark-secondary"><i class="bi bi-person"></i></span>
            <div>
                <div class="stat-pill-value">{{ $users->where('useroll', 'user')->count() }}</div>
                <div class="stat-pill-label">Benutzer</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-envelope-check"></i></span>
            <div>
                <div class="stat-pill-value">{{ $users->whereNotNull('email_verified_at')->count() }}</div>
                <div class="stat-pill-label">Verifiziert</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-error"><i class="bi bi-envelope-x"></i></span>
            <div>
                <div class="stat-pill-value">{{ $users->whereNull('email_verified_at')->count() }}</div>
                <div class="stat-pill-label">Nicht verifiziert</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-warning"><i class="bi bi-envelope-heart"></i></span>
            <div>
                <div class="stat-pill-value">{{ $users->where('email_consent', true)->count() }}</div>
                <div class="stat-pill-label">Newsletter</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem;">
        <a href="{{ route('admin.newsletter.create') }}" class="btn-primary">
            Newsletter senden
        </a>
        <a href="{{ route('dashboard') }}" class="btn-ghost">
            <i class="bi bi-arrow-left"></i> Zum Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert-glass success" style="margin-bottom: 1.5rem;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; color: var(--success);"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-glass error" style="margin-bottom: 1.5rem;">
            <i class="bi bi-x-circle" style="font-size: 1.25rem; color: var(--error);"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Benutzertabelle -->
    <div class="glass" style="padding: 1.5rem;">
        <div class="section-header" style="margin-bottom: 1.5rem; padding-left: 1rem; border-left: 3px solid var(--gold-start);">
            <h2 class="section-title">Alle Benutzer</h2>
        </div>

        <!-- Desktop Tabelle -->
        <div class="hidden md:block" style="overflow-x: auto;">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rolle</th>
                        <th>Status</th>
                        <th>Name</th>
                        <th>E-Mail</th>
                        <th>Verifiziert</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr id="user-row-{{ $user->id }}">
                        <td>
                            <span class="badge-thw">{{ $user->id }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($user->useroll === 'admin')
                                <span class="badge-gold" title="Administrator"><i class="bi bi-shield-check"></i> Admin</span>
                            @else
                                <span class="badge-glass" title="Benutzer"><i class="bi bi-person"></i> User</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($user->is_online ?? false)
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: var(--success);" title="Online (letzte Session: {{ $user->updated_at->diffForHumans() }})">
                                    <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Online
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-muted);" title="Offline (letzte Session: {{ $user->updated_at->diffForHumans() }})">
                                    <i class="bi bi-circle" style="font-size: 0.5rem;"></i> Offline
                                </span>
                            @endif
                        </td>
                        <td style="font-weight: 600; color: var(--text-primary);">
                            {{ $user->name }}
                        </td>
                        <td style="color: var(--text-secondary);">
                            {{ $user->email }}
                        </td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge-success"><i class="bi bi-check"></i> Verifiziert</span>
                            @else
                                <span class="badge-error"><i class="bi bi-x"></i> Ausstehend</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button onclick="toggleUserDetails({{ $user->id }})" class="btn-ghost btn-sm" title="Details anzeigen/verbergen">
                                    <i class="bi bi-chevron-down" id="toggle-icon-{{ $user->id }}"></i>
                                    Details
                                </button>

                                <a href="{{ route('admin.users.progress.edit', $user->id) }}" class="btn-primary btn-sm" title="Fortschritt bearbeiten">
                                    <i class="bi bi-graph-up"></i>
                                    Fortschritt
                                </a>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm" title="Benutzer löschen" onclick="return confirm('Benutzer {{ $user->name }} wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Aufklappbare Details -->
                    <tr id="user-details-{{ $user->id }}" class="hidden">
                        <td colspan="7" style="padding: 1.5rem; background: rgba(255, 255, 255, 0.02);">
                            <div class="glass-subtle" style="padding: 1.5rem;">
                                <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Benutzerdetails bearbeiten</h3>

                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                        <div>
                                            <label class="label-glass">Name</label>
                                            <input type="text" name="name" value="{{ $user->name }}" class="input-glass" />
                                        </div>

                                        <div>
                                            <label class="label-glass">E-Mail</label>
                                            <input type="email" name="email" value="{{ $user->email }}" class="input-glass" />
                                        </div>

                                        <div>
                                            <label class="label-glass">Rolle</label>
                                            <select name="useroll" class="select-glass">
                                                <option value="user" @if($user->useroll === 'user') selected @endif>Benutzer</option>
                                                <option value="admin" @if($user->useroll === 'admin') selected @endif>Administrator</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="label-glass">Registriert am</label>
                                            <div style="padding: 0.875rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem; color: var(--text-secondary); font-size: 0.9rem;">
                                                {{ $user->created_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="label-glass">Letzte Aktivität</label>
                                            <div style="padding: 0.875rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem; color: var(--text-secondary); font-size: 0.9rem;">
                                                {{ $user->updated_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="label-glass">E-Mail Status</label>
                                            <div style="padding: 0.875rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem; font-size: 0.9rem;">
                                                @if($user->email_verified_at)
                                                    <span style="color: var(--success);"><i class="bi bi-check-circle"></i> Bestätigt am {{ $user->email_verified_at->format('d.m.Y H:i') }}</span>
                                                @else
                                                    <span style="color: var(--error);"><i class="bi bi-x-circle"></i> Nicht bestätigt</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <label class="label-glass">Newsletter-Zustimmung</label>
                                            <div style="padding: 0.875rem 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem; font-size: 0.9rem;">
                                                @if($user->email_consent)
                                                    <span style="color: var(--success);"><i class="bi bi-envelope-check"></i> Zustimmung erteilt
                                                        @if($user->email_consent_at)
                                                            am {{ $user->email_consent_at->format('d.m.Y H:i') }}
                                                        @endif
                                                    </span>
                                                @else
                                                    <span style="color: var(--text-muted);"><i class="bi bi-envelope-x"></i> Keine Zustimmung</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display: flex; justify-content: flex-end; padding-top: 1rem;">
                                        <button type="submit" class="btn-primary">
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
            <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($users as $user)
            <div class="glass-subtle" style="padding: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="badge-thw">ID: {{ $user->id }}</span>
                        @if($user->useroll === 'admin')
                            <span class="badge-gold"><i class="bi bi-shield-check"></i></span>
                        @endif
                        @if($user->is_online ?? false)
                            <span style="color: var(--success);"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i></span>
                        @else
                            <span style="color: var(--text-muted);"><i class="bi bi-circle" style="font-size: 0.5rem;"></i></span>
                        @endif
                    </div>
                </div>

                <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $user->name }}</div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.75rem;">{{ $user->email }}</div>

                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                    @if($user->email_verified_at)
                        <span class="badge-success"><i class="bi bi-check"></i> E-Mail bestätigt</span>
                    @else
                        <span class="badge-error"><i class="bi bi-x"></i> E-Mail nicht bestätigt</span>
                    @endif

                    @if($user->email_consent)
                        <span class="badge-gold"><i class="bi bi-envelope-check"></i> Newsletter</span>
                    @endif
                </div>

                <!-- Aufklappbare Details Mobile -->
                <div id="mobile-details-{{ $user->id }}" class="hidden" style="margin-bottom: 1rem;">
                    <div style="padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem; margin-top: 1rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Details bearbeiten</h4>

                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <div>
                                    <label class="label-glass">Name</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="input-glass" />
                                </div>

                                <div>
                                    <label class="label-glass">E-Mail</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="input-glass" />
                                </div>

                                <div>
                                    <label class="label-glass">Rolle</label>
                                    <select name="useroll" class="select-glass">
                                        <option value="user" @if($user->useroll === 'user') selected @endif>Benutzer</option>
                                        <option value="admin" @if($user->useroll === 'admin') selected @endif>Administrator</option>
                                    </select>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.875rem;">
                                    <div>
                                        <div style="color: var(--text-muted); margin-bottom: 0.25rem;">Registriert:</div>
                                        <div style="font-weight: 600; color: var(--text-secondary);">{{ $user->created_at->format('d.m.Y') }}</div>
                                    </div>
                                    <div>
                                        <div style="color: var(--text-muted); margin-bottom: 0.25rem;">Letzte Aktivität:</div>
                                        <div style="font-weight: 600; color: var(--text-secondary);">{{ $user->updated_at->format('d.m.Y') }}</div>
                                    </div>
                                </div>

                                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0.5rem;">
                                    Änderungen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 0.5rem;">
                    <button onclick="toggleMobileDetails({{ $user->id }})" class="btn-ghost btn-sm" style="justify-content: center;">
                        <i class="bi bi-chevron-down" id="mobile-toggle-icon-{{ $user->id }}"></i>
                        Details
                    </button>

                    <a href="{{ route('admin.users.progress.edit', $user->id) }}" class="btn-primary btn-sm" style="justify-content: center;">
                        <i class="bi bi-graph-up"></i>
                        Fortschritt
                    </a>

                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="grid-column: 1 / -1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm" style="width: 100%; justify-content: center;" onclick="return confirm('Benutzer {{ $user->name }} wirklich löschen?')">
                            <i class="bi bi-trash"></i>
                            Löschen
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </div>

    <!-- Footer Navigation -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; flex-wrap: wrap; gap: 1rem;">
        <a href="{{ route('admin.questions.index') }}" class="btn-secondary">
            Zur Fragenverwaltung
        </a>

        <div style="color: var(--text-muted); font-weight: 600;">
            Gesamt: {{ $users->count() }} Benutzer
        </div>
    </div>
</div>

<script>
    function toggleUserDetails(userId) {
        const detailsRow = document.getElementById('user-details-' + userId);
        const icon = document.getElementById('toggle-icon-' + userId);

        if (detailsRow.classList.contains('hidden')) {
            detailsRow.classList.remove('hidden');
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-up');
        } else {
            detailsRow.classList.add('hidden');
            icon.classList.remove('bi-chevron-up');
            icon.classList.add('bi-chevron-down');
        }
    }

    function toggleMobileDetails(userId) {
        const detailsDiv = document.getElementById('mobile-details-' + userId);
        const icon = document.getElementById('mobile-toggle-icon-' + userId);

        if (detailsDiv.classList.contains('hidden')) {
            detailsDiv.classList.remove('hidden');
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-up');
        } else {
            detailsDiv.classList.add('hidden');
            icon.classList.remove('bi-chevron-up');
            icon.classList.add('bi-chevron-down');
        }
    }
</script>
@endsection
