<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Überprüft den eingegebenen Zahlencode und bestätigt die E-Mail.
     * Nach 3 Fehlversuchen wird der Code ungültig gemacht.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if ($user->verification_code === null || $user->verification_code_expires_at->isPast()) {
            $request->session()->forget('verification_attempts');
            return back()->withErrors(['code' => 'Der Code ist abgelaufen. Bitte fordere einen neuen Code an.']);
        }

        if (!hash_equals($user->verification_code, $request->code)) {
            $attempts = $request->session()->get('verification_attempts', 0) + 1;
            $request->session()->put('verification_attempts', $attempts);

            if ($attempts >= 3) {
                $user->verification_code = null;
                $user->verification_code_expires_at = null;
                $user->save();
                $request->session()->forget('verification_attempts');
                return back()->withErrors(['code' => 'Zu viele Fehlversuche. Bitte fordere einen neuen Code an.']);
            }

            $remaining = 3 - $attempts;
            return back()->withErrors(['code' => "Der Code ist ungültig. Es bleiben {$remaining} Versuche."]);
        }

        $request->session()->forget('verification_attempts');

        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
