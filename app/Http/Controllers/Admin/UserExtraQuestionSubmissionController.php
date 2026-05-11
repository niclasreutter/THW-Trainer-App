<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ExtraQuestionSubmissionStatusMail;
use App\Models\Notification;
use App\Models\UserExtraQuestionSubmission;
use App\Notifications\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserExtraQuestionSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = UserExtraQuestionSubmission::with(['user', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('typ')) {
            $query->where('typ', $request->typ);
        }

        $submissions = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => UserExtraQuestionSubmission::count(),
            'pending' => UserExtraQuestionSubmission::where('status', 'pending')->count(),
            'approved' => UserExtraQuestionSubmission::where('status', 'approved')->count(),
            'changed' => UserExtraQuestionSubmission::where('status', 'changed')->count(),
            'rejected' => UserExtraQuestionSubmission::where('status', 'rejected')->count(),
        ];

        return view('admin.extra-question-submissions.index', compact('submissions', 'stats'));
    }

    public function show(UserExtraQuestionSubmission $submission)
    {
        $submission->load(['user', 'reviewer', 'extraQuestion']);

        return view('admin.extra-question-submissions.show', compact('submission'));
    }

    public function reject(Request $request, UserExtraQuestionSubmission $submission)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $submission->update([
            'status' => UserExtraQuestionSubmission::STATUS_REJECTED,
            'admin_notes' => $validated['admin_notes'],
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        cache()->forget('admin_pending_extra_q_submissions_count');
        self::notifyUser($submission);

        return redirect()
            ->route('admin.extra-question-submissions.index')
            ->with('success', 'Einreichung wurde abgelehnt und der User benachrichtigt.');
    }

    public function markChanged(Request $request, UserExtraQuestionSubmission $submission)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        if ($submission->status !== UserExtraQuestionSubmission::STATUS_APPROVED && $submission->status !== UserExtraQuestionSubmission::STATUS_CHANGED) {
            return back()->withErrors(['status' => 'Nur übernommene Einreichungen können als "geändert" markiert werden.']);
        }

        $submission->update([
            'status' => UserExtraQuestionSubmission::STATUS_CHANGED,
            'admin_notes' => $validated['admin_notes'] ?? $submission->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        self::notifyUser($submission);

        return back()->with('success', 'Einreichung wurde als "mit Änderungen übernommen" markiert.');
    }

    /**
     * Sendet In-App, E-Mail und (falls verfügbar) Push-Notification
     * über das Status-Update an den User.
     */
    public static function notifyUser(UserExtraQuestionSubmission $submission): void
    {
        $user = $submission->user;
        if (!$user) {
            return;
        }

        $title = match ($submission->status) {
            UserExtraQuestionSubmission::STATUS_APPROVED => 'Deine Zusatz-Frage wurde übernommen',
            UserExtraQuestionSubmission::STATUS_REJECTED => 'Deine Zusatz-Frage wurde abgelehnt',
            UserExtraQuestionSubmission::STATUS_CHANGED  => 'Deine Zusatz-Frage wurde angepasst übernommen',
            default => 'Status deiner Zusatz-Frage',
        };

        $shortFrage = \Illuminate\Support\Str::limit($submission->frage, 80);
        $message = match ($submission->status) {
            UserExtraQuestionSubmission::STATUS_APPROVED => "Deine Frage \"$shortFrage\" wurde in den Fragenkatalog aufgenommen. Vielen Dank!",
            UserExtraQuestionSubmission::STATUS_REJECTED => "Deine Frage \"$shortFrage\" wurde leider nicht übernommen.",
            UserExtraQuestionSubmission::STATUS_CHANGED  => "Deine Frage \"$shortFrage\" wurde mit Anpassungen übernommen.",
            default => "Es gibt ein Update zu deiner eingereichten Frage \"$shortFrage\".",
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => 'extra_question_submission',
            'title' => $title,
            'message' => $message,
            'data' => [
                'submission_id' => $submission->id,
                'status' => $submission->status,
                'url' => route('zusatzfragen-vorschlagen.index'),
            ],
        ]);

        try {
            Mail::to($user->email)->send(new ExtraQuestionSubmissionStatusMail($submission));
        } catch (\Throwable $e) {
            Log::error('Zusatz-Frage Status-Mail fehlgeschlagen: ' . $e->getMessage());
        }

        try {
            if ($user->pushSubscriptions()->exists()) {
                $user->notify(new PushNotification(
                    $title,
                    $message,
                    route('zusatzfragen-vorschlagen.index'),
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Zusatz-Frage Push-Notification fehlgeschlagen: ' . $e->getMessage());
        }
    }
}
