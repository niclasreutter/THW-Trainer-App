<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Bereits verifiziert und keine pending E-Mail → nichts zu tun
        if ($user->hasVerifiedEmail() && !$user->pending_email) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->session()->forget('verification_attempts');
        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-code-sent');
    }
}
