@extends('layouts.app')

@section('title', 'Profil bearbeiten - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account. Sicher und einfach.')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">Dein Profil</h1>
    
    <!-- Status Messages -->
    @if (session('status') == 'profile-updated' || session('status') == 'password-updated')
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <div class="p-4 rounded-lg shadow-md" style="background-color: #dcfce7; border: 2px solid #16a34a;">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 mt-0.5" style="color: #15803d;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm" style="color: #166534;">
                            @if (session('status') == 'profile-updated')
                                <p>Profil erfolgreich aktualisiert!</p>
                            @else
                                <p>🔒 Passwort erfolgreich geändert!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- E-Mail Verification Warning -->
    @if (!$user->hasVerifiedEmail())
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <div class="p-4 rounded-lg shadow-md" style="background-color: #fef3c7; border: 2px solid #f59e0b;">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 mt-0.5" style="color: #92400e;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium" style="color: #78350f;">⏰ E-Mail-Bestätigung erforderlich</h3>
                        <div class="mt-1 text-sm" style="color: #92400e;">
                            <p><strong>Wichtig:</strong> Deine E-Mail-Adresse muss <strong>innerhalb von 5 Minuten</strong> bestätigt werden. Bitte überprüfe dein Postfach und klicke auf den Bestätigungslink. Überprüfe auch deinen Spam-Ordner.</p>
                            @if (session('status') == 'email-verification-sent')
                                <p class="mt-2 font-medium">📧 Eine neue Bestätigungs-E-Mail wurde gerade gesendet!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Profilbearbeitung -->
    <div class="mb-12 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-blue-800 mb-6">👤 Profildaten</h2>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Name:</label>
            <input type="text" value="{{ Auth::user()->name }}" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-500" readonly>
        </div>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-Mail-Adresse:</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @endif
            </div>
            <button type="submit" style="width: 100%; background-color: #2563eb; color: white; font-weight: 600; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                📧 E-Mail speichern
            </button>
        </form>
    </div>

    <!-- Passwort ändern -->
    <div class="mb-12 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-blue-800 mb-6">🔒 Passwort ändern</h2>
        
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Aktuelles Passwort:</label>
                <input type="password" name="current_password" id="current_password" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                       placeholder="Gib dein aktuelles Passwort ein">
                @error('current_password')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @endif
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Neues Passwort:</label>
                <input type="password" name="password" id="password" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                       placeholder="Mindestens 8 Zeichen">
                @error('password')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @endif
            </div>
            
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Neues Passwort bestätigen:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Passwort wiederholen">
            </div>
            
            <button type="submit" style="width: 100%; background-color: #16a34a; color: white; font-weight: 600; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                🔒 Passwort ändern
            </button>
        </form>
    </div>

    <!-- Account löschen -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-red-800 mb-6">⚠️ Gefährlicher Bereich</h2>
        
        <div class="mb-6 p-4 rounded-lg" style="background-color: #fee2e2; border: 2px solid #dc2626;">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 mt-0.5" style="color: #dc2626;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium" style="color: #7f1d1d;">Account löschen</h3>
                    <div class="mt-1 text-sm" style="color: #991b1b;">
                        <p><strong>Achtung:</strong> Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten, einschließlich Lernfortschritt und Prüfungsergebnisse, werden permanent gelöscht.</p>
                    </div>
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
            
            <button type="submit" style="width: 100%; background-color: #dc2626; color: white; font-weight: 600; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
                🗑️ Account permanent löschen
            </button>
        </form>
    </div>
    
</div>
@endsection
