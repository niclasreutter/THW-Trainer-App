<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        // Honeypot Spam-Schutz (unsichtbares Feld)
        if ($request->filled('website')) {
            return back()->with('success', '✅ Vielen Dank! Wir haben deine Nachricht erhalten.');
        }

        // Rate Limiting: Max 3 Anfragen pro Stunde pro IP
        $key = 'contact-form:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'rate_limit' => "⏱️ Zu viele Anfragen. Bitte versuche es in " . ceil($seconds / 60) . " Minuten erneut."
            ])->withInput();
        }

        // Validation
        $validated = $request->validate([
            'type' => 'required|in:feedback,feature,bug,other',
            'email' => 'required|email|max:255',
            'hermine_contact' => 'nullable|boolean',
            'vorname' => 'nullable|required_if:hermine_contact,1|string|max:255',
            'nachname' => 'nullable|required_if:hermine_contact,1|string|max:255',
            'ortsverband' => 'nullable|required_if:hermine_contact,1|string|max:255',
            'error_location' => 'nullable|required_if:type,bug|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'type.required' => 'Bitte wähle eine Kategorie aus.',
            'email.required' => 'Bitte gib deine E-Mail-Adresse an.',
            'email.email' => 'Bitte gib eine gültige E-Mail-Adresse an.',
            'vorname.required_if' => 'Bitte gib deinen Vornamen an, wenn du über Hermine kontaktiert werden möchtest.',
            'nachname.required_if' => 'Bitte gib deinen Nachnamen an, wenn du über Hermine kontaktiert werden möchtest.',
            'ortsverband.required_if' => 'Bitte gib deinen Ortsverband an, wenn du über Hermine kontaktiert werden möchtest.',
            'error_location.required_if' => 'Bitte gib an, wo der Fehler aufgetreten ist.',
            'message.required' => 'Bitte schreibe eine Nachricht.',
            'message.min' => 'Die Nachricht muss mindestens 10 Zeichen lang sein.',
            'message.max' => 'Die Nachricht darf maximal 5000 Zeichen lang sein.',
        ]);

        // XSS-Schutz: Sanitize Message (entfernt HTML-Tags)
        $validated['message'] = strip_tags($validated['message']);

        // Speichere in Datenbank
        $contactMessage = ContactMessage::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'email' => $validated['email'],
            'hermine_contact' => $request->boolean('hermine_contact'),
            'vorname' => $validated['vorname'] ?? null,
            'nachname' => $validated['nachname'] ?? null,
            'ortsverband' => $validated['ortsverband'] ?? null,
            'error_location' => $validated['error_location'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Rate Limiter erhöhen
        RateLimiter::hit($key, 3600); // 1 Stunde

        // E-Mail versenden
        try {
            Mail::to('niclas@thw-trainer.de')
                ->cc($validated['email'])
                ->send(new ContactMail($contactMessage));
        } catch (\Exception $e) {
            // Log error but don't show to user
            \Log::error('Contact form email failed: ' . $e->getMessage());
        }

        return back()->with('success', '✅ Vielen Dank! Ich habe deine Nachricht erhalten und melde mich so schnell wie möglich bei dir.');
    }
}

