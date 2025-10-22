<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        \Log::info('ProfileController update method reached');
        
        // Einfache Validierung statt ProfileUpdateRequest
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'email_consent' => ['boolean'],
            'leaderboard_consent' => ['boolean'],
        ]);
        
        $user = $request->user();
        $originalEmail = $user->email;
        $newEmail = $request->input('email');
        
        \Log::info('Original email: ' . $originalEmail . ', New email: ' . $newEmail);
        
        // E-Mail-Zustimmung verarbeiten
        $emailConsent = $request->has('email_consent');
        $user->email_consent = $emailConsent;
        $user->email_consent_at = $emailConsent ? now() : null;
        
        // Leaderboard-Zustimmung verarbeiten
        $leaderboardConsent = $request->has('leaderboard_consent');
        $user->leaderboard_consent = $leaderboardConsent;
        $user->leaderboard_consent_at = $leaderboardConsent ? now() : null;
        
        // Prüfe ob E-Mail geändert wurde
        if ($originalEmail !== $newEmail) {
            \Log::info('Email change detected');
            
            $user->email = $newEmail;
            $user->email_verified_at = null;
            $user->save();
            
            \Log::info('User saved with new email and email_verified_at = null');
            
            // E-Mail-Verifizierung senden
            try {
                $user->sendEmailVerificationNotification();
                \Log::info('Email verification sent to: ' . $user->email);
            } catch (\Exception $e) {
                \Log::error('Failed to send email verification: ' . $e->getMessage());
            }
            
            return Redirect::route('profile')->with('status', 'email-verification-sent');
        }

        $user->save();
        \Log::info('User email consent updated: ' . ($emailConsent ? 'true' : 'false'));

        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        \Log::info('Password update method reached');
        
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        \Log::info('Password updated for user: ' . $user->id);

        return Redirect::route('profile')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        \Log::info('Account deletion attempt for user: ' . $request->user()->id);
        
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userId = $user->id;
        $userEmail = $user->email;

        \Log::info('Account deletion confirmed for user: ' . $userId . ' (' . $userEmail . ')');

        // User ausloggen
        Auth::logout();

        // User und alle zugehörigen Daten löschen (Cascade Delete durch Foreign Keys)
        $user->delete();

        // Session invalidieren
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \Log::info('Account successfully deleted for user: ' . $userId);

        return Redirect::to('/')->with('status', 'account-deleted');
    }
}
