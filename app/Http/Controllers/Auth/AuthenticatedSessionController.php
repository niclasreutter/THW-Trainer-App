<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Helpers\DomainHelper;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
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
                'users' => (int) (floor(User::count() / 10) * 10),
                'questions' => (int) (floor(Question::count() / 10) * 10),
            ];
        });

        return view('auth.login', compact('demoQuestions', 'authStats'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // In Production: Redirect zur App-Domain
        // In Development: Normale Redirect-Logik
        // Ausbilder landen auf ihrem OV-Dashboard, alle anderen auf /dashboard
        $homePath = $request->user()->homePath();
        $dashboardUrl = config('domains.development')
            ? url($homePath)
            : DomainHelper::appUrl($homePath);

        return redirect()->intended($dashboardUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Nach Logout zur Landing-Seite
        $homeUrl = config('domains.development')
            ? route('landing.home')
            : DomainHelper::landingUrl('/');

        return redirect($homeUrl);
    }
}
