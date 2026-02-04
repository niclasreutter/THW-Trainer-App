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
            return back()->withErrors(['code' => 'Der Code ist abgelaufen. Bitte fordere einen neuen Code an.']);
        }

        if (!hash_equals($user->verification_code, $request->code)) {
            return back()->withErrors(['code' => 'Der Code ist ungültig. Bitte überprüfe die Eingabe.']);
        }

        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
