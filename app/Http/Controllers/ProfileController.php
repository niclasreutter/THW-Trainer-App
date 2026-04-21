<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserAvatarAccessory;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        
        $user = $request->user();
        
        // Validierung mit unique Check für Name (außer der aktuelle User)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'email_consent' => ['boolean'],
            'leaderboard_consent' => ['boolean'],
            'exam_date' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'name.required' => 'Bitte gib einen Namen ein.',
            'name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'email.required' => 'Bitte gib eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        ]);
        
        $originalEmail = $user->email;
        $originalName = $user->name;
        $newEmail = $request->input('email');
        $newName = $request->input('name');
        
        \Log::info('Original email: ' . $originalEmail . ', New email: ' . $newEmail);
        \Log::info('Original name: ' . $originalName . ', New name: ' . $newName);
        
        // Name aktualisieren falls geändert
        if ($originalName !== $newName) {
            $user->name = $newName;
            \Log::info('Name changed to: ' . $newName);
        }
        
        // E-Mail-Zustimmung verarbeiten
        $emailConsent = $request->boolean('email_consent');
        $user->email_consent = $emailConsent;
        $user->email_consent_at = $emailConsent ? now() : null;

        // Leaderboard-Zustimmung verarbeiten
        $leaderboardConsent = $request->boolean('leaderboard_consent');
        $user->leaderboard_consent = $leaderboardConsent;
        $user->leaderboard_consent_at = $leaderboardConsent ? now() : null;

        // Prüfungsdatum verarbeiten (privat, nur für den eigenen User sichtbar)
        $user->exam_date = $request->input('exam_date') ?: null;

        // Wenn E-Mail-Feld unverändert und pending_email existiert → pending löschen
        if ($originalEmail === $newEmail && $user->pending_email) {
            $user->pending_email = null;
            $user->verification_code = null;
            $user->verification_code_expires_at = null;
        }

        // Prüfe ob E-Mail geändert wurde
        if ($originalEmail !== $newEmail) {
            \Log::info('Email change detected');

            // Neue E-Mail als pending speichern - Account bleibt aktiv mit alter E-Mail
            $user->pending_email = $newEmail;
            $user->verification_code = null;
            $user->verification_code_expires_at = null;
            $user->save();

            \Log::info('Pending email saved: ' . $newEmail . ' (current email stays: ' . $originalEmail . ')');

            // Verifizierungscode an die NEUE E-Mail senden
            try {
                $user->sendEmailVerificationNotification();
                \Log::info('Email verification sent to pending email: ' . $newEmail);
            } catch (\Exception $e) {
                \Log::error('Failed to send email verification: ' . $e->getMessage());
            }

            return Redirect::route('profile')->with('status', 'email-verification-sent');
        }

        $user->save();
        \Log::info('User profile updated successfully');

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
     * Update the user's extras_enabled setting (toggle for Zusatz-Fragen).
     */
    public function updateExtrasEnabled(Request $request): RedirectResponse
    {
        $request->validate([
            'extras_enabled' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $user->extras_enabled = $request->boolean('extras_enabled');
        $user->save();

        return Redirect::route('profile')
            ->with('status', 'extras-enabled-updated');
    }

    /**
     * Dismiss the leaderboard banner and optionally opt-in.
     */
    public function dismissLeaderboardBanner(Request $request): RedirectResponse
    {
        $request->validate([
            'accept' => ['boolean'],
        ]);

        $user = $request->user();
        $user->leaderboard_banner_dismissed = true;
        
        // Wenn der Nutzer zugestimmt hat
        if ($request->boolean('accept')) {
            $user->leaderboard_consent = true;
            $user->leaderboard_consent_at = now();
        }
        
        $user->save();

        return Redirect::route('dashboard')->with('status', 'leaderboard-banner-dismissed');
    }

    /**
     * Regenerate the user's avatar with a new random seed.
     */
    public function regenerateAvatar(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $seed = Str::random(16);

        // avataaars ohne Brille, Bart, Hüte — die werden später per XP-Shop freigeschaltet
        // Nur positive Gesichtsausdrücke erlaubt
        $user->avatar_path = 'avataaars/svg?radius=50'
            . '&seed=' . $seed
            . '&backgroundType=gradientLinear'
            . '&backgroundColor=00337f,0055cc'
            . '&backgroundRotation=135'
            . '&accessoriesProbability=0'
            . '&facialHairProbability=0'
            . '&top=bob,bun,curly,curvy,dreads,frida,fro,froBand,longButNotTooLong,miaWallace,shavedSides,straight01,straight02,straightAndStrand,dreads01,dreads02,frizzle,shaggy,shaggyMullet,shortCurly,shortFlat,shortRound,shortWaved,sides,theCaesar,theCaesarAndSidePart,bigHair'
            . '&mouth=default,smile,tongue,twinkle'
            . '&eyes=default,happy,hearts,wink'
            . '&eyebrows=defaultNatural,flatNatural,raisedExcitedNatural,upDownNatural,default,raisedExcited,upDown';
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['avatar_url' => $user->avatar_url]);
        }

        return Redirect::route('profile')->with('status', 'avatar-updated');
    }

    /**
     * Toggle an avatar accessory on/off.
     */
    public function toggleAccessory(Request $request): JsonResponse
    {
        $request->validate([
            'accessory_slug' => 'required|string|max:50',
            'color' => 'nullable|string|max:10',
        ]);

        $user = $request->user();
        $slug = $request->input('accessory_slug');

        // Besitz prüfen
        $owned = UserAvatarAccessory::where('user_id', $user->id)
            ->where('accessory_slug', $slug)
            ->exists();

        if (!$owned) {
            return response()->json(['success' => false, 'message' => 'Accessoire nicht im Besitz.'], 422);
        }

        $item = ShopService::SHOP_ITEMS[$slug] ?? null;
        if (!$item || ($item['category'] ?? '') !== 'accessory') {
            return response()->json(['success' => false, 'message' => 'Unbekanntes Accessoire.'], 422);
        }

        $active = $user->active_accessories ?? [];
        $paramName = $item['accessory_type'];
        $colorKey = match ($paramName) {
            'accessories' => 'accessoriesColor',
            'facialHair' => 'facialHairColor',
            default => null,
        };

        // Toggle: wenn aktiv -> deaktivieren, sonst aktivieren
        if (isset($active[$paramName]) && $active[$paramName] === $item['accessory_value']) {
            unset($active[$paramName]);
            if ($colorKey) {
                unset($active[$colorKey]);
            }
        } else {
            $active[$paramName] = $item['accessory_value'];
            // Farbe setzen falls mitgegeben
            if ($colorKey && $request->input('color')) {
                $allowedColors = $item['colors'] ?? [];
                $color = $request->input('color');
                if (in_array($color, $allowedColors)) {
                    $active[$colorKey] = $color;
                }
            }
        }

        $user->active_accessories = !empty($active) ? $active : null;
        $user->save();

        // Active states für alle Kategorien zurückgeben
        $shopService = new ShopService();
        $ownedAccessories = $shopService->getOwnedAccessories($user->fresh());

        return response()->json([
            'success' => true,
            'avatar_url' => $user->fresh()->avatar_url,
            'owned_accessories' => $ownedAccessories,
        ]);
    }

    /**
     * Update the color of an active accessory.
     */
    public function updateAccessoryColor(Request $request): JsonResponse
    {
        $request->validate([
            'accessory_slug' => 'required|string|max:50',
            'color' => 'required|string|max:10',
        ]);

        $user = $request->user();
        $slug = $request->input('accessory_slug');
        $color = $request->input('color');

        $item = ShopService::SHOP_ITEMS[$slug] ?? null;
        if (!$item || ($item['category'] ?? '') !== 'accessory') {
            return response()->json(['success' => false, 'message' => 'Unbekanntes Accessoire.'], 422);
        }

        // Farbe muss erlaubt sein
        $allowedColors = $item['colors'] ?? [];
        if (!in_array($color, $allowedColors)) {
            return response()->json(['success' => false, 'message' => 'Ungültige Farbe.'], 422);
        }

        $active = $user->active_accessories ?? [];
        $paramName = $item['accessory_type'];

        // Prüfen ob dieses Accessoire aktiv ist
        if (!isset($active[$paramName]) || $active[$paramName] !== $item['accessory_value']) {
            return response()->json(['success' => false, 'message' => 'Accessoire ist nicht aktiv.'], 422);
        }

        $colorKey = match ($paramName) {
            'accessories' => 'accessoriesColor',
            'facialHair' => 'facialHairColor',
            default => null,
        };

        if ($colorKey) {
            $active[$colorKey] = $color;
            $user->active_accessories = $active;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'avatar_url' => $user->fresh()->avatar_url,
        ]);
    }

    /**
     * Cancel a pending email change.
     */
    public function cancelEmailChange(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->pending_email) {
            \Log::info('Email change cancelled', [
                'user_id' => $user->id,
                'pending_email' => $user->pending_email,
            ]);
            $user->pending_email = null;
            $user->verification_code = null;
            $user->verification_code_expires_at = null;
            $user->save();
        }

        return Redirect::route('profile')->with('status', 'email-change-cancelled');
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
