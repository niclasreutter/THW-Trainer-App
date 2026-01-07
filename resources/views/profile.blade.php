@extends('layouts.app')

@section('title', 'Profil bearbeiten - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account. Sicher und einfach.')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .profile-wrapper { min-height: 100vh; background: #f3f4f6; position: relative; overflow-x: hidden; }

    .profile-container { max-width: 900px; margin: 0 auto; padding: 2rem; position: relative; z-index: 1; }

    .profile-header { text-align: center; margin-bottom: 3rem; padding-top: 1rem; }

    .profile-header h1 { font-size: 2.5rem; font-weight: 800; color: #00337F; margin-bottom: 0.5rem; line-height: 1.2; }

    .profile-subtitle { font-size: 1.1rem; color: #4b5563; margin-bottom: 0; }

    .alert-box { border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; }

    .alert-success { background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid #22c55e; }

    .alert-warning { background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%); border: 1px solid #f59e0b; }

    .alert-icon { font-size: 1.5rem; flex-shrink: 0; }

    .alert-content h3 { margin: 0 0 0.5rem 0; font-weight: 700; font-size: 1rem; }

    .alert-content p { margin: 0; font-size: 0.95rem; }

    .alert-success .alert-content h3 { color: #16a34a; }
    .alert-success .alert-content p { color: #16a34a; }

    .alert-warning .alert-content h3 { color: #d97706; }
    .alert-warning .alert-content p { color: #92400e; }

    .card { background: white; border-radius: 10px; border: 1px solid #e5e7eb; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }

    .card-title { font-size: 1.5rem; font-weight: 700; color: #00337F; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }

    .form-group { margin-bottom: 1.5rem; }

    .form-group-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }

    .form-label { display: block; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; }

    .form-input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; transition: all 0.3s; }

    .form-input:focus { outline: none; border-color: #00337F; box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1); }

    .form-input.error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }

    .error-message { color: #dc2626; font-size: 0.85rem; margin-top: 0.4rem; }

    .help-text { color: #6b7280; font-size: 0.85rem; margin-top: 0.4rem; }

    .checkbox-group { display: flex; align-items: flex-start; gap: 0.75rem; }

    .checkbox-input { width: 1.25rem; height: 1.25rem; margin-top: 0.15rem; accent-color: #00337F; cursor: pointer; flex-shrink: 0; }

    .checkbox-label { cursor: pointer; flex: 1; }

    .checkbox-label-text { font-weight: 500; color: #1f2937; margin-bottom: 0.4rem; display: block; }

    .checkbox-description { color: #6b7280; font-size: 0.9rem; margin-bottom: 0.5rem; }

    .checkbox-confirm { color: #16a34a; font-size: 0.85rem; margin-top: 0.5rem; }

    .consent-section { background: linear-gradient(135deg, rgba(0, 51, 127, 0.05) 0%, rgba(0, 63, 153, 0.05) 100%); border: 1px solid #dbeafe; border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem; }

    .button { width: 100%; padding: 0.875rem 1.5rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-size: 1rem; }

    .button-primary { background: linear-gradient(135deg, #00337F 0%, #003F99 100%); color: white; }

    .button-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 51, 127, 0.3); }

    .button-success { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; }

    .button-success:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3); }

    .button-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }

    .button-danger:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

    .password-match-message { font-size: 0.85rem; margin-top: 0.5rem; display: none; }

    .password-match-success { color: #16a34a; }

    .password-match-error { color: #dc2626; }

    @media (max-width: 768px) {
        .profile-container { padding: 1rem; }
        .profile-header h1 { font-size: 2rem; }
        .card { padding: 1.5rem; }
        .form-group-row { grid-template-columns: 1fr; }
        .button { padding: 0.75rem 1rem; font-size: 0.95rem; }
    }
</style>
@endpush

@section('content')
<div class="profile-wrapper">
    <div class="profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1>👤 Profil bearbeiten</h1>
            <p class="profile-subtitle">Verwalte deine persönlichen Daten und Einstellungen</p>
        </div>

        <!-- Status Messages -->
        @if (session('status') == 'profile-updated' || session('status') == 'password-updated')
            <div class="alert-box alert-success">
                <div class="alert-icon">✅</div>
                <div class="alert-content">
                    <h3>Erfolgreich aktualisiert!</h3>
                    <p>
                        @if (session('status') == 'profile-updated')
                            Dein Profil wurde erfolgreich aktualisiert.
                        @else
                            Dein Passwort wurde erfolgreich geändert.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- E-Mail Verification Warning -->
        @if (!$user->hasVerifiedEmail())
            <div class="alert-box alert-warning">
                <div class="alert-icon">⏰</div>
                <div class="alert-content">
                    <h3>E-Mail-Bestätigung erforderlich</h3>
                    <p>
                        <strong>Wichtig:</strong> Deine E-Mail-Adresse muss <strong>innerhalb von 5 Minuten</strong> bestätigt werden. 
                        Bitte überprüfe dein Postfach und klicke auf den Bestätigungslink. Überprüfe auch deinen Spam-Ordner.
                        @if (session('status') == 'email-verification-sent')
                            <br><strong>📧 Eine neue Bestätigungs-E-Mail wurde gerade gesendet!</strong>
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- Profile Information -->
        <div class="card">
            <h2 class="card-title">👤 Profildaten</h2>
            
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                
                <div class="form-group-row">
                    <div>
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                               class="form-input @error('name') error @enderror" required maxlength="255">
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @else
                            <p class="help-text">Dieser Name erscheint im Leaderboard</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="form-label">E-Mail-Adresse</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                               class="form-input @error('email') error @enderror" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- E-Mail-Zustimmung -->
                <div class="consent-section">
                    <div class="checkbox-group">
                        <input type="checkbox" name="email_consent" id="email_consent" value="1" 
                               {{ old('email_consent', $user->email_consent) ? 'checked' : '' }}
                               class="checkbox-input">
                        <label for="email_consent" class="checkbox-label">
                            <span class="checkbox-label-text">📧 E-Mail-Benachrichtigungen</span>
                            <span class="checkbox-description">
                                Erhalte E-Mails zu deinem Lernfortschritt, neuen Features und wichtigen Systeminformationen.
                            </span>
                            @if($user->email_consent_at)
                                <span class="checkbox-confirm">
                                    ✅ Zustimmung erteilt am {{ $user->email_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                </span>
                            @endif
                        </label>
                    </div>
                </div>

                <!-- Leaderboard Consent -->
                <div class="consent-section">
                    <div class="checkbox-group">
                        <input type="checkbox" name="leaderboard_consent" id="leaderboard_consent" value="1"
                               {{ $user->leaderboard_consent ? 'checked' : '' }} class="checkbox-input">
                        <label for="leaderboard_consent" class="checkbox-label">
                            <span class="checkbox-label-text">🏆 Leaderboard-Teilnahme</span>
                            <span class="checkbox-description">
                                Wenn aktiviert, erscheint dein Name im öffentlichen Leaderboard und andere Nutzer können deine Punkte sehen. 
                                Du kannst diese Einstellung jederzeit ändern.
                            </span>
                            @if($user->leaderboard_consent)
                                <span class="checkbox-confirm">
                                    ✅ Zustimmung erteilt am {{ $user->leaderboard_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                </span>
                            @endif
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="button button-primary">💾 Profil speichern</button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="card">
            <h2 class="card-title">🔒 Passwort ändern</h2>
            
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PATCH')
                
                <div class="form-group-row">
                    <div>
                        <label for="current_password" class="form-label">Aktuelles Passwort</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="form-input @error('current_password') error @enderror"
                               placeholder="Aktuelles Passwort">
                        @error('current_password')
                            <div class="error-message">{{ $message }}</div>
                        @endif
                    </div>
                    
                    <div>
                        <label for="password" class="form-label">Neues Passwort</label>
                        <input type="password" name="password" id="password" 
                               class="form-input @error('password') error @enderror"
                               placeholder="Mindestens 8 Zeichen"
                               oninput="checkPasswordMatch()">
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @endif
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="form-label">Passwort bestätigen</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="form-input"
                               placeholder="Passwort wiederholen"
                               oninput="checkPasswordMatch()">
                        <div id="password-match-message" class="password-match-message"></div>
                    </div>
                </div>
                
                <button type="submit" class="button button-success">🔒 Passwort ändern</button>
            </form>
        </div>

        <!-- Account Information -->
        <div class="card">
            <h2 class="card-title">ℹ️ Konto-Informationen</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div style="padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Beitrittsdatum</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1f2937;">{{ $user->created_at->format('d.m.Y') }}</div>
                    <div style="font-size: 0.85rem; color: #9ca3af; margin-top: 0.25rem;">vor {{ $user->created_at->diffInDays(now()) }} Tagen</div>
                </div>
                
                <div style="padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Konto-Status</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1f2937;">
                        @if($user->hasVerifiedEmail())
                            ✅ Bestätigt
                        @else
                            ⏳ Ausstehend
                        @endif
                    </div>
                    <div style="font-size: 0.85rem; color: #9ca3af; margin-top: 0.25rem;">E-Mail {{ $user->hasVerifiedEmail() ? 'verifiziert' : 'nicht verifiziert' }}</div>
                </div>
                
                <div style="padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Zuletzt angemeldet</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1f2937;">
                        @if($user->last_login_at)
                            {{ $user->last_login_at->format('d.m.Y H:i') }}
                        @else
                            Gerade eben
                        @endif
                    </div>
                    <div style="font-size: 0.85rem; color: #9ca3af; margin-top: 0.25rem;">
                        @if($user->last_login_at)
                            vor {{ $user->last_login_at->diffForHumans() }}
                        @else
                            Erste Anmeldung
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Management -->
        <div class="card">
            <h2 class="card-title" style="color: #dc2626;">⚠️ Account-Verwaltung</h2>
            
            <div class="alert-box" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%); border: 1px solid #ef4444; margin-bottom: 1.5rem;">
                <div class="alert-icon">⚠️</div>
                <div class="alert-content">
                    <h3 style="color: #dc2626;">Account löschen</h3>
                    <p style="color: #991b1b;">
                        <strong>Achtung:</strong> Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten, einschließlich Lernfortschritt 
                        und Prüfungsergebnisse, werden permanent gelöscht.
                    </p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('profile.destroy') }}" 
                  onsubmit="return confirm('Bist du dir absolut sicher? Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten werden permanent gelöscht.')">
                @csrf
                @method('DELETE')
                
                <div class="form-group">
                    <label for="password_delete" class="form-label">Bestätige mit deinem Passwort</label>
                    <input type="password" name="password" id="password_delete" 
                           class="form-input @error('password', 'userDeletion') error @enderror"
                           placeholder="Gib dein Passwort ein um den Account zu löschen">
                    @error('password', 'userDeletion')
                        <div class="error-message">{{ $message }}</div>
                    @endif
                </div>
                
                <button type="submit" class="button button-danger">🗑️ Account permanent löschen</button>
            </form>
        </div>
    </div>
</div>

<script>
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const messageDiv = document.getElementById('password-match-message');
        
        if (password && passwordConfirmation) {
            if (password === passwordConfirmation) {
                document.getElementById('password').style.borderColor = '#22c55e';
                document.getElementById('password').style.boxShadow = '0 0 0 3px rgba(34, 197, 94, 0.1)';
                document.getElementById('password_confirmation').style.borderColor = '#22c55e';
                document.getElementById('password_confirmation').style.boxShadow = '0 0 0 3px rgba(34, 197, 94, 0.1)';
                
                messageDiv.className = 'password-match-message password-match-success';
                messageDiv.innerHTML = '✅ Passwörter stimmen überein';
                messageDiv.style.display = 'block';
            } else {
                document.getElementById('password').style.borderColor = '#ef4444';
                document.getElementById('password').style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
                document.getElementById('password_confirmation').style.borderColor = '#ef4444';
                document.getElementById('password_confirmation').style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
                
                messageDiv.className = 'password-match-message password-match-error';
                messageDiv.innerHTML = '❌ Passwörter stimmen nicht überein';
                messageDiv.style.display = 'block';
            }
        } else {
            document.getElementById('password').style.borderColor = '';
            document.getElementById('password').style.boxShadow = '';
            document.getElementById('password_confirmation').style.borderColor = '';
            document.getElementById('password_confirmation').style.boxShadow = '';
            messageDiv.style.display = 'none';
        }
    }
</script>
</script>
@endsection
