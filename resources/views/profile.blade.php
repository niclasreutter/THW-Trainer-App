@extends('layouts.app')

@section('title', 'Profil bearbeiten - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account.')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Profil <span>bearbeiten</span></h1>
        <p class="page-subtitle">Verwalte deine persönlichen Daten und Einstellungen</p>
    </header>

    <!-- Status Messages -->
    @if (session('status') == 'profile-updated' || session('status') == 'password-updated')
    <div class="alert-compact glass-success" style="margin-bottom: 1.5rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">Erfolgreich aktualisiert</div>
            <div class="alert-compact-desc">
                @if (session('status') == 'profile-updated')
                    Dein Profil wurde erfolgreich aktualisiert.
                @else
                    Dein Passwort wurde erfolgreich geändert.
                @endif
            </div>
        </div>
        <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
    </div>
    @endif

    <!-- E-Mail Verification Warning -->
    @if (!$user->hasVerifiedEmail())
    <div class="alert-compact glass-warning" style="margin-bottom: 1.5rem;">
        <i class="bi bi-clock-history alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">E-Mail-Bestätigung erforderlich</div>
            <div class="alert-compact-desc">
                Deine E-Mail-Adresse muss innerhalb von 5 Minuten bestätigt werden.
                @if (session('status') == 'email-verification-sent')
                    Eine neue Bestätigungs-E-Mail wurde gesendet.
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-calendar-check"></i></span>
            <div>
                <div class="stat-pill-value">{{ $user->created_at->format('d.m.Y') }}</div>
                <div class="stat-pill-label">Beigetreten</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon {{ $user->hasVerifiedEmail() ? 'text-success' : 'text-warning' }}">
                <i class="bi bi-{{ $user->hasVerifiedEmail() ? 'patch-check-fill' : 'hourglass-split' }}"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $user->hasVerifiedEmail() ? 'Bestätigt' : 'Ausstehend' }}</div>
                <div class="stat-pill-label">E-Mail Status</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-clock-history"></i></span>
            <div>
                <div class="stat-pill-value">{{ $user->last_login_at ? $user->last_login_at->diffForHumans(null, true) : 'Gerade eben' }}</div>
                <div class="stat-pill-label">Letzte Anmeldung</div>
            </div>
        </div>
    </div>

    <!-- Bento Grid -->
    <div class="bento-grid-profile">
        <!-- Profile Information (Main) -->
        <div class="glass-gold bento-profile-main">
            <div class="section-header" style="margin-bottom: 1.25rem; padding-left: 0.75rem;">
                <h2 class="section-title">Profildaten</h2>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="label-glass">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="input-glass @error('name') input-error @enderror" required maxlength="255">
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @else
                            <p class="form-hint">Dieser Name erscheint im Leaderboard</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="label-glass">E-Mail-Adresse</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="input-glass @error('email') input-error @enderror" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- E-Mail Consent -->
                <div class="consent-card glass-subtle">
                    <label class="consent-label">
                        <input type="checkbox" name="email_consent" value="1"
                               {{ old('email_consent', $user->email_consent) ? 'checked' : '' }}
                               class="checkbox-glass">
                        <div class="consent-content">
                            <span class="consent-title">E-Mail-Benachrichtigungen</span>
                            <span class="consent-desc">
                                Erhalte E-Mails zu deinem Lernfortschritt, neuen Features und wichtigen Systeminformationen.
                            </span>
                            @if($user->email_consent_at)
                                <span class="consent-confirm">
                                    <i class="bi bi-check-circle"></i> Zustimmung erteilt am {{ $user->email_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                </span>
                            @endif
                        </div>
                    </label>
                </div>

                <!-- Leaderboard Consent -->
                <div class="consent-card glass-subtle">
                    <label class="consent-label">
                        <input type="checkbox" name="leaderboard_consent" value="1"
                               {{ $user->leaderboard_consent ? 'checked' : '' }}
                               class="checkbox-glass">
                        <div class="consent-content">
                            <span class="consent-title">Leaderboard-Teilnahme</span>
                            <span class="consent-desc">
                                Wenn aktiviert, erscheint dein Name im öffentlichen Leaderboard und andere Nutzer können deine Punkte sehen.
                            </span>
                            @if($user->leaderboard_consent)
                                <span class="consent-confirm">
                                    <i class="bi bi-check-circle"></i> Zustimmung erteilt am {{ $user->leaderboard_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                </span>
                            @endif
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Profil speichern</button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="glass-tl bento-password">
            <div class="section-header" style="margin-bottom: 1.25rem; padding-left: 0.75rem;">
                <h2 class="section-title">Passwort ändern</h2>
            </div>

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="current_password" class="label-glass">Aktuelles Passwort</label>
                    <input type="password" name="current_password" id="current_password"
                           class="input-glass @error('current_password') input-error @enderror"
                           placeholder="Aktuelles Passwort">
                    @error('current_password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="label-glass">Neues Passwort</label>
                    <input type="password" name="password" id="password"
                           class="input-glass @error('password') input-error @enderror"
                           placeholder="Mindestens 8 Zeichen"
                           oninput="checkPasswordMatch()">
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="label-glass">Passwort bestätigen</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="input-glass"
                           placeholder="Passwort wiederholen"
                           oninput="checkPasswordMatch()">
                    <div id="password-match-message" class="password-match-message"></div>
                </div>

                <button type="submit" class="btn-secondary" style="width: 100%;">Passwort ändern</button>
            </form>
        </div>

        <!-- Account Info Card -->
        <div class="glass-br bento-account-info">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h2 class="section-title" style="font-size: 1rem;">Konto-Details</h2>
            </div>

            <div class="account-info-grid">
                <div class="account-info-item">
                    <div class="account-info-label">Beitrittsdatum</div>
                    <div class="account-info-value">{{ $user->created_at->format('d.m.Y') }}</div>
                    <div class="account-info-meta">vor {{ $user->created_at->diffInDays(now()) }} Tagen</div>
                </div>

                <div class="account-info-item">
                    <div class="account-info-label">Konto-Status</div>
                    <div class="account-info-value">
                        @if($user->hasVerifiedEmail())
                            <span class="text-success">Bestätigt</span>
                        @else
                            <span class="text-warning">Ausstehend</span>
                        @endif
                    </div>
                    <div class="account-info-meta">E-Mail {{ $user->hasVerifiedEmail() ? 'verifiziert' : 'nicht verifiziert' }}</div>
                </div>

                <div class="account-info-item">
                    <div class="account-info-label">Zuletzt angemeldet</div>
                    <div class="account-info-value">
                        @if($user->last_login_at)
                            {{ $user->last_login_at->format('d.m.Y H:i') }}
                        @else
                            Gerade eben
                        @endif
                    </div>
                    <div class="account-info-meta">
                        @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                        @else
                            Erste Anmeldung
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="glass-error bento-danger">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h2 class="section-title" style="font-size: 1rem; color: #ef4444;">Account löschen</h2>
            </div>

            <p class="danger-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten werden permanent gelöscht.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}"
                  onsubmit="return confirm('Bist du dir absolut sicher? Alle deine Daten werden permanent gelöscht.')">
                @csrf
                @method('DELETE')

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="password_delete" class="label-glass">Passwort zur Bestätigung</label>
                    <input type="password" name="password" id="password_delete"
                           class="input-glass @error('password', 'userDeletion') input-error @enderror"
                           placeholder="Passwort eingeben">
                    @error('password', 'userDeletion')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-danger" style="width: 100%;">Account permanent löschen</button>
            </form>
        </div>
    </div>

    <!-- Back Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>

@push('styles')
<style>
    .bento-grid-profile {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1rem;
    }

    .bento-profile-main {
        grid-row: span 2;
        padding: 1.5rem;
    }

    .bento-password {
        padding: 1.5rem;
    }

    .bento-account-info {
        padding: 1.25rem;
    }

    .bento-danger {
        grid-column: span 2;
        padding: 1.25rem;
    }

    /* Form Styles */
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-error {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 0.35rem;
    }

    .form-hint {
        color: var(--text-muted);
        font-size: 0.75rem;
        margin-top: 0.35rem;
        margin-bottom: 0;
    }

    .input-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15) !important;
    }

    /* Consent Cards */
    .consent-card {
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
    }

    .consent-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
    }

    .consent-label input {
        margin-top: 0.15rem;
        flex-shrink: 0;
    }

    .consent-content {
        flex: 1;
    }

    .consent-title {
        font-weight: 600;
        color: var(--text-primary);
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .consent-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        display: block;
        line-height: 1.5;
    }

    .consent-confirm {
        color: #22c55e;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.5rem;
    }

    /* Account Info */
    .account-info-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .account-info-item {
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
    }

    .account-info-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.25rem;
    }

    .account-info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .account-info-meta {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    /* Alert Compact */
    .alert-compact {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-compact-icon {
        font-size: 1.25rem;
    }

    .alert-compact-content {
        flex: 1;
    }

    .alert-compact-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .alert-compact-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .alert-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 1.25rem;
        padding: 0;
        line-height: 1;
    }

    .alert-close:hover {
        color: var(--text-primary);
    }

    /* Danger Zone */
    .danger-warning {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        line-height: 1.5;
    }

    .danger-warning i {
        color: #ef4444;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    /* Password Match */
    .password-match-message {
        font-size: 0.8rem;
        margin-top: 0.35rem;
        display: none;
    }

    .password-match-success {
        color: #22c55e;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .password-match-error {
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Text Colors */
    .text-success { color: #22c55e; }
    .text-warning { color: #f59e0b; }
    .text-info { color: #3b82f6; }

    /* Responsive */
    @media (max-width: 900px) {
        .bento-grid-profile {
            grid-template-columns: 1fr;
        }

        .bento-profile-main {
            grid-row: span 1;
        }

        .bento-danger {
            grid-column: span 1;
        }
    }

    @media (max-width: 500px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .stats-row {
            flex-wrap: wrap;
        }

        .stat-pill {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 140px;
        }
    }
</style>
@endpush

<script>
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const messageDiv = document.getElementById('password-match-message');

        if (password && passwordConfirmation) {
            if (password === passwordConfirmation) {
                document.getElementById('password').classList.remove('input-error');
                document.getElementById('password_confirmation').classList.remove('input-error');

                messageDiv.className = 'password-match-message password-match-success';
                messageDiv.innerHTML = '<i class="bi bi-check-circle"></i> Passwörter stimmen überein';
                messageDiv.style.display = 'flex';
            } else {
                document.getElementById('password').classList.add('input-error');
                document.getElementById('password_confirmation').classList.add('input-error');

                messageDiv.className = 'password-match-message password-match-error';
                messageDiv.innerHTML = '<i class="bi bi-x-circle"></i> Passwörter stimmen nicht überein';
                messageDiv.style.display = 'flex';
            }
        } else {
            document.getElementById('password').classList.remove('input-error');
            document.getElementById('password_confirmation').classList.remove('input-error');
            messageDiv.style.display = 'none';
        }
    }
</script>
@endsection
