<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPushMessage;
use App\Models\Ortsverband;
use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class PushController extends Controller
{
    public function index()
    {
        $messages = AdminPushMessage::with('admin', 'ortsverband')
            ->orderByDesc('created_at')
            ->paginate(20);

        $ortsverbande = Ortsverband::orderBy('name')->get();

        return view('admin.push', compact('messages', 'ortsverbande'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'target_type' => 'required|in:all,ortsverband',
            'target_id' => 'required_if:target_type,ortsverband|nullable|exists:ortsverbände,id',
        ]);

        if ($validated['target_type'] === 'ortsverband') {
            $ortsverband = Ortsverband::findOrFail($validated['target_id']);
            $users = $ortsverband->members()->whereHas('pushSubscriptions')->get();
        } else {
            $users = User::whereHas('pushSubscriptions')->get();
        }

        $notification = new PushNotification(
            $validated['title'],
            $validated['message'],
        );

        Notification::send($users, $notification);

        // Also create in-app notifications
        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'admin_push',
                'title' => $validated['title'],
                'message' => $validated['message'],
            ]);
        }

        AdminPushMessage::create([
            'admin_user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_type' => $validated['target_type'],
            'target_id' => $validated['target_id'] ?? null,
            'recipients_count' => $users->count(),
        ]);

        return back()->with('success', "Push an {$users->count()} Empfaenger gesendet.");
    }
}
