@extends('layouts.app')

@section('title', 'Profil bearbeiten - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account. Sicher und einfach.')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">👤 Profil bearbeiten</h1>
    
    <!-- Status Messages -->
    @if (session('status') == 'profile-updated' || session('status') == 'password-updated')
        <div style="margin-top: 2rem; margin-bottom: 2rem; background-color: #f0fdf4; border: 2px solid #22c55e; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1);">
            <div class="flex items-center justify-center">
                <svg class="w-6 h-6 mr-3" style="color: #16a34a;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-base font-medium" style="color: #16a34a; margin: 0;">
                    @if (session('status') == 'profile-updated')
                        ✅ Profil erfolgreich aktualisiert!
                    @else
                        🔒 Passwort erfolgreich geändert!
                    @endif
                </p>
            </div>
        </div>
    @endif

    <!-- E-Mail Verification Warning -->
    @if (!$user->hasVerifiedEmail())
        <div style="margin-top: 2rem; margin-bottom: 2rem; background-color: #fffbeb; border: 2px solid #f59e0b; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);">
            <div class="flex items-start">
                <svg class="w-6 h-6 mr-3 mt-1" style="color: #d97706;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-medium" style="color: #d97706; margin-bottom: 8px;">⏰ E-Mail-Bestätigung erforderlich</h3>
                    <p class="text-sm" style="color: #92400e; margin-bottom: 8px;">
                        <strong>Wichtig:</strong> Deine E-Mail-Adresse muss <strong>innerhalb von 5 Minuten</strong> bestätigt werden. Bitte überprüfe dein Postfach und klicke auf den Bestätigungslink. Überprüfe auch deinen Spam-Ordner.
                    </p>
                    @if (session('status') == 'email-verification-sent')
                        <p class="text-sm font-medium" style="color: #d97706;">📧 Eine neue Bestätigungs-E-Mail wurde gerade gesendet!</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-md p-6" style="margin-bottom: 2rem;">
        <h2 class="text-xl font-semibold text-blue-800 mb-6">👤 Profildaten</h2>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Name (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name:</label>
                    <input type="text" value="{{ Auth::user()->name }}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-500" readonly>
                    <p class="text-xs text-gray-500 mt-1">Der Name kann nicht geändert werden</p>
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-Mail-Adresse:</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @endif
                </div>
            </div>
            
            <!-- E-Mail-Zustimmung -->
            <div class="mb-6 p-4 rounded-lg" style="background-color: #f0f9ff; border: 2px solid #0ea5e9; box-shadow: 0 0 20px rgba(14, 165, 233, 0.3), 0 0 40px rgba(14, 165, 233, 0.1);">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 mt-1" style="color: #0284c7;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium" style="color: #0284c7; margin-bottom: 8px;">📧 E-Mail-Benachrichtigungen</h3>
                        <p class="text-sm" style="color: #0369a1; margin-bottom: 12px;">
                            Erhalte E-Mails zu deinem Lernfortschritt, neuen Features und wichtigen Systeminformationen.
                        </p>
                        <div class="flex items-center">
                            <input type="checkbox" name="email_consent" id="email_consent" value="1" 
                                   {{ old('email_consent', $user->email_consent) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="email_consent" class="ml-2 text-sm font-medium" style="color: #0369a1;">
                                Ich möchte E-Mail-Benachrichtigungen erhalten
                            </label>
                        </div>
                        @if($user->email_consent_at)
                            <p class="text-xs mt-2" style="color: #0284c7;">
                                ✅ Zustimmung erteilt am {{ $user->email_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Push-Benachrichtigungen (nur in PWA) -->
            <div id="push-settings-section" class="mb-6 p-4 rounded-lg hidden" style="background-color: #fef3c7; border: 2px solid #f59e0b; box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 mt-1" style="color: #d97706;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium" style="color: #d97706; margin-bottom: 8px;">🔔 Push-Benachrichtigungen (PWA)</h3>
                        <p class="text-sm" style="color: #b45309; margin-bottom: 12px;">
                            Erhalte Benachrichtigungen direkt auf deinem Gerät, auch wenn die App geschlossen ist.
                        </p>
                        <div id="push-status-container">
                            <div id="push-not-supported" class="hidden text-sm text-gray-600">
                                ⚠️ Push-Benachrichtigungen werden von deinem Browser nicht unterstützt.
                            </div>
                            <div id="push-granted" class="hidden">
                                <div class="flex items-center text-green-700 mb-2">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Push-Benachrichtigungen sind aktiviert
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" id="test-push-btn" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors text-sm">
                                        Test-Benachrichtigung senden
                                    </button>
                                    <button type="button" id="disable-push-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                                        Deaktivieren
                                    </button>
                                </div>
                            </div>
                            <div id="push-default" class="hidden">
                                <button type="button" id="enable-push-btn" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                                    Push-Benachrichtigungen aktivieren
                                </button>
                            </div>
                            <div id="push-denied" class="hidden text-sm text-red-600">
                                ❌ Push-Benachrichtigungen wurden blockiert. Bitte aktiviere sie in den Browser-Einstellungen.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" 
                    style="width: 100%; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-weight: 600; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1);"
                    onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 25px rgba(37, 99, 235, 0.4), 0 0 50px rgba(37, 99, 235, 0.2)'"
                    onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1)'">
                💾 Profil speichern
            </button>
        </form>
    </div>
    
    <script>
        // Show push settings only if running as PWA
        if (window.pushNotifications && window.pushNotifications.isPWA()) {
            document.getElementById('push-settings-section').classList.remove('hidden');
            
            // Check push support and permission
            if (!window.pushNotifications.isPushSupported()) {
                document.getElementById('push-not-supported').classList.remove('hidden');
            } else {
                const permission = window.pushNotifications.getPushPermission();
                
                if (permission === 'granted') {
                    // Check if actually subscribed
                    window.pushNotifications.isSubscribedToPush().then(subscribed => {
                        if (subscribed) {
                            document.getElementById('push-granted').classList.remove('hidden');
                        } else {
                            document.getElementById('push-default').classList.remove('hidden');
                        }
                    });
                } else if (permission === 'denied') {
                    document.getElementById('push-denied').classList.remove('hidden');
                } else {
                    document.getElementById('push-default').classList.remove('hidden');
                }
            }
            
            // Enable push button
            const enableBtn = document.getElementById('enable-push-btn');
            if (enableBtn) {
                enableBtn.addEventListener('click', async () => {
                    enableBtn.disabled = true;
                    enableBtn.textContent = 'Aktiviere...';
                    
                    const result = await window.pushNotifications.requestPushPermission();
                    
                    if (result.success) {
                        document.getElementById('push-default').classList.add('hidden');
                        document.getElementById('push-granted').classList.remove('hidden');
                    } else {
                        alert(result.message);
                        enableBtn.disabled = false;
                        enableBtn.textContent = 'Push-Benachrichtigungen aktivieren';
                    }
                });
            }
            
            // Test push button
            const testBtn = document.getElementById('test-push-btn');
            if (testBtn) {
                testBtn.addEventListener('click', async () => {
                    testBtn.disabled = true;
                    testBtn.textContent = 'Sende...';
                    
                    const result = await window.pushNotifications.sendTestPushNotification();
                    
                    if (result.success) {
                        alert('✅ Test-Benachrichtigung gesendet!');
                    } else {
                        alert('❌ ' + result.message);
                    }
                    
                    testBtn.disabled = false;
                    testBtn.textContent = 'Test-Benachrichtigung senden';
                });
            }
            
            // Disable push button
            const disableBtn = document.getElementById('disable-push-btn');
            if (disableBtn) {
                disableBtn.addEventListener('click', async () => {
                    if (!confirm('Push-Benachrichtigungen wirklich deaktivieren?')) return;
                    
                    disableBtn.disabled = true;
                    disableBtn.textContent = 'Deaktiviere...';
                    
                    const result = await window.pushNotifications.unsubscribeFromPush();
                    
                    if (result.success) {
                        document.getElementById('push-granted').classList.add('hidden');
                        document.getElementById('push-default').classList.remove('hidden');
                    } else {
                        alert(result.message);
                        disableBtn.disabled = false;
                        disableBtn.textContent = 'Deaktivieren';
                    }
                });
            }
        }
    </script>

    <!-- Password Change -->
    <div class="bg-white rounded-lg shadow-md p-6" style="margin-bottom: 2rem;">
        <h2 class="text-xl font-semibold text-blue-800 mb-6">🔒 Passwort ändern</h2>
        
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Aktuelles Passwort:</label>
                    <input type="password" name="current_password" id="current_password" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                           placeholder="Aktuelles Passwort">
                    @error('current_password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @endif
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Neues Passwort:</label>
                    <input type="password" name="password" id="password" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Mindestens 8 Zeichen"
                           oninput="checkPasswordMatch()">
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @endif
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Passwort bestätigen:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Passwort wiederholen"
                           oninput="checkPasswordMatch()">
                    <div id="password-match-message" class="text-sm mt-1" style="display: none;"></div>
                </div>
            </div>
            
            <button type="submit" 
                    style="width: 100%; background: linear-gradient(to right, #16a34a, #15803d); color: white; font-weight: 600; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4), 0 0 20px rgba(22, 163, 74, 0.3), 0 0 40px rgba(22, 163, 74, 0.1);"
                    onmouseover="this.style.background='linear-gradient(to right, #15803d, #166534)'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 15px rgba(22, 163, 74, 0.4), 0 0 25px rgba(22, 163, 74, 0.4), 0 0 50px rgba(22, 163, 74, 0.2)'"
                    onmouseout="this.style.background='linear-gradient(to right, #16a34a, #15803d)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(22, 163, 74, 0.4), 0 0 20px rgba(22, 163, 74, 0.3), 0 0 40px rgba(22, 163, 74, 0.1)'">
                🔒 Passwort ändern
            </button>
        </form>
    </div>

    <!-- Account Management -->
    <div class="bg-white rounded-lg shadow-md p-6" style="margin-bottom: 2rem;">
        <h2 class="text-xl font-semibold text-red-800 mb-6">⚠️ Account-Verwaltung</h2>
        
        <div class="mb-6 p-4 rounded-lg" style="background-color: #fef2f2; border: 2px solid #ef4444; box-shadow: 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1);">
            <div class="flex items-start">
                <svg class="w-6 h-6 mr-3 mt-1" style="color: #dc2626;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-medium" style="color: #dc2626; margin-bottom: 8px;">Account löschen</h3>
                    <p class="text-sm" style="color: #991b1b; margin-bottom: 0;">
                        <strong>Achtung:</strong> Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten, einschließlich Lernfortschritt und Prüfungsergebnisse, werden permanent gelöscht.
                    </p>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Bist du dir absolut sicher? Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten werden permanent gelöscht.')">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <label for="password_delete" class="block text-sm font-medium text-gray-700 mb-2">Bestätige mit deinem Passwort:</label>
                <input type="password" name="password" id="password_delete" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('password', 'userDeletion') border-red-500 @enderror"
                       placeholder="Gib dein Passwort ein um den Account zu löschen">
                @error('password', 'userDeletion')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @endif
            </div>
            
            <button type="submit" 
                    style="width: 100%; background: linear-gradient(to right, #dc2626, #b91c1c); color: white; font-weight: 600; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4), 0 0 20px rgba(220, 38, 38, 0.3), 0 0 40px rgba(220, 38, 38, 0.1);"
                    onmouseover="this.style.background='linear-gradient(to right, #b91c1c, #991b1b)'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 15px rgba(220, 38, 38, 0.4), 0 0 25px rgba(220, 38, 38, 0.4), 0 0 50px rgba(220, 38, 38, 0.2)'"
                    onmouseout="this.style.background='linear-gradient(to right, #dc2626, #b91c1c)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(220, 38, 38, 0.4), 0 0 20px rgba(220, 38, 38, 0.3), 0 0 40px rgba(220, 38, 38, 0.1)'">
                🗑️ Account permanent löschen
            </button>
        </form>
    </div>
</div>

<script>
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const messageDiv = document.getElementById('password-match-message');
        
        // Nur prüfen wenn beide Felder ausgefüllt sind
        if (password && passwordConfirmation) {
            if (password === passwordConfirmation) {
                // Passwörter stimmen überein - Grüner Glow
                document.getElementById('password').style.boxShadow = '0 0 0 3px rgba(34, 197, 94, 0.3), 0 0 0 1px rgba(34, 197, 94, 0.5)';
                document.getElementById('password_confirmation').style.boxShadow = '0 0 0 3px rgba(34, 197, 94, 0.3), 0 0 0 1px rgba(34, 197, 94, 0.5)';
                document.getElementById('password').style.borderColor = '#22c55e';
                document.getElementById('password_confirmation').style.borderColor = '#22c55e';
                
                messageDiv.style.display = 'block';
                messageDiv.style.color = '#16a34a';
                messageDiv.innerHTML = '✅ Passwörter stimmen überein';
            } else {
                // Passwörter stimmen nicht überein - Roter Glow
                document.getElementById('password').style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.3), 0 0 0 1px rgba(239, 68, 68, 0.5)';
                document.getElementById('password_confirmation').style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.3), 0 0 0 1px rgba(239, 68, 68, 0.5)';
                document.getElementById('password').style.borderColor = '#ef4444';
                document.getElementById('password_confirmation').style.borderColor = '#ef4444';
                
                messageDiv.style.display = 'block';
                messageDiv.style.color = '#dc2626';
                messageDiv.innerHTML = '❌ Passwörter stimmen nicht überein';
            }
        } else {
            // Felder zurücksetzen wenn nicht beide ausgefüllt
            document.getElementById('password').style.boxShadow = '';
            document.getElementById('password_confirmation').style.boxShadow = '';
            document.getElementById('password').style.borderColor = '';
            document.getElementById('password_confirmation').style.borderColor = '';
            messageDiv.style.display = 'none';
        }
    }
</script>
@endsection
