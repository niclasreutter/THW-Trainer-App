<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\User;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    /**
     * Zeige das Newsletter-Formular
     */
    public function create()
    {
        $newsletters = Newsletter::with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.newsletter.create', compact('newsletters'));
    }

    /**
     * Test-Newsletter an Admin senden
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Platzhalter ersetzen
        $content = Newsletter::replacePlaceholders($request->content, $user);

        try {
            \Log::info('Sending test newsletter', [
                'user_id' => $user->id,
                'email' => $user->email,
                'subject' => $request->subject
            ]);
            
            Mail::to($user->email)->send(
                new NewsletterMail($user, $request->subject, $content)
            );

            \Log::info('Test newsletter sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test-Newsletter erfolgreich an ' . $user->email . ' gesendet!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send test newsletter', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Senden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Newsletter an alle User mit Zustimmung senden
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Nur User mit E-Mail-Zustimmung
        $users = User::where('email_consent', true)->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Empfänger gefunden. Es gibt keine User mit E-Mail-Zustimmung.'
            ], 400);
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            try {
                // Platzhalter für jeden User individuell ersetzen
                $personalizedContent = Newsletter::replacePlaceholders($request->content, $user);

                \Log::info('Sending newsletter', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'subject' => $request->subject
                ]);

                Mail::to($user->email)->send(
                    new NewsletterMail($user, $request->subject, $personalizedContent)
                );

                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Newsletter send failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Newsletter in Datenbank speichern
        $newsletter = Newsletter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'recipients_count' => $sentCount,
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Newsletter erfolgreich gesendet! Erfolgreich: {$sentCount}, Fehlgeschlagen: {$failedCount}",
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);
    }

    /**
     * Newsletter-Historie anzeigen
     */
    public function index()
    {
        $newsletters = Newsletter::with('sender')
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return view('admin.newsletter.index', compact('newsletters'));
    }

    /**
     * Newsletter Details anzeigen
     */
    public function show(Newsletter $newsletter)
    {
        $newsletter->load('sender');
        return view('admin.newsletter.show', compact('newsletter'));
    }
}

