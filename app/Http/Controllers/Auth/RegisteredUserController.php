<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\OrtsverbandInvitation;
use App\Helpers\DomainHelper;
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
        $demoQuestions = Question::whereRaw("LENGTH(loesung) = 1")
            ->inRandomOrder()->limit(3)
            ->get(['frage', 'antwort_a', 'antwort_b', 'antwort_c', 'loesung'])
            ->map(function ($q) {
                $map = ['A' => 0, 'B' => 1, 'C' => 2];
                return [
                    'text' => $q->frage,
                    'answers' => [$q->antwort_a, $q->antwort_b, $q->antwort_c],
                    'correctIdxs' => collect(explode(',', $q->loesung))
                        ->map(fn($l) => $map[trim($l)] ?? 0)
                        ->values()
                        ->toArray(),
                ];
            })->values();

        $authStats = cache()->remember('auth_stats', 300, function () {
            return [
                'users' => (int) (floor(\App\Models\User::count() / 10) * 10),
                'questions' => (int) (floor(Question::count() / 10) * 10),
            ];
        });

        return view('auth.register', compact('demoQuestions', 'authStats'));
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

        $seed = urlencode($user->id . str_replace(' ', '', $user->name));
        $user->update([
            'avatar_path' => 'avataaars-neutral/svg?radius=50&seed=' . $seed . '&eyes=default,happy&mouth=default,smile',
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

        // In Production: Redirect zur App-Domain
        // In Development: Normale Redirect-Logik
        $dashboardUrl = config('domains.development')
            ? route('dashboard')
            : DomainHelper::appUrl('/dashboard');

        return redirect($dashboardUrl);
    }
}
