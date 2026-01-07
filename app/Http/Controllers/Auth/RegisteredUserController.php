<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrtsverbandInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'email_consent' => ['boolean'],
            'leaderboard_consent' => ['boolean'],
            'invitation_code' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_consent' => $request->has('email_consent'),
            'email_consent_at' => $request->has('email_consent') ? now() : null,
            'leaderboard_consent' => $request->has('leaderboard_consent'),
            'leaderboard_consent_at' => $request->has('leaderboard_consent') ? now() : null,
        ]);

        event(new Registered($user));

        Auth::login($user);
        
        // Verarbeite Einladungscode falls vorhanden
        if ($request->invitation_code) {
            $invitation = OrtsverbandInvitation::findByCode($request->invitation_code);
            
            if ($invitation && $invitation->isValid()) {
                try {
                    $invitation->use($user);
                    session()->flash('success', 'Erfolgreich dem Ortsverband "' . $invitation->ortsverband->name . '" beigetreten!');
                } catch (\Exception $e) {
                    // Fehler beim Beitritt ignorieren, User ist bereits registriert
                    \Log::warning('Failed to use invitation during registration', [
                        'user_id' => $user->id,
                        'invitation_code' => $request->invitation_code,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return redirect(route('dashboard', absolute: false));
    }
}
