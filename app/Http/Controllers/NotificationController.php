<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Zeigt alle Notifications des Users an
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * API: Holt ungelesene Notifications
     */
    public function unread()
    {
        $notifications = auth()->user()
            ->unreadNotifications()
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Markiert eine einzelne Notification als gelesen
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Markiert alle Notifications als gelesen
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Löscht eine Notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Löscht alle gelesenen Notifications
     */
    public function clearRead()
    {
        auth()->user()
            ->notifications()
            ->where('is_read', true)
            ->delete();

        return back()->with('success', 'Alle gelesenen Mitteilungen wurden gelöscht.');
    }
}
