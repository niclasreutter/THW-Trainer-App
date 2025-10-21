<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationController extends Controller
{
    /**
     * Subscribe to push notifications
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        try {
            $subscription = PushSubscription::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'endpoint' => $request->endpoint,
                ],
                [
                    'public_key' => $request->input('keys.p256dh'),
                    'auth_token' => $request->input('keys.auth'),
                    'content_encoding' => 'aesgcm',
                    'is_active' => true,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Push-Benachrichtigungen aktiviert!'
            ]);
        } catch (\Exception $e) {
            Log::error('Push subscription failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktivieren der Push-Benachrichtigungen'
            ], 500);
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        try {
            PushSubscription::where('user_id', Auth::id())
                ->where('endpoint', $request->endpoint)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Push-Benachrichtigungen deaktiviert'
            ]);
        } catch (\Exception $e) {
            Log::error('Push unsubscribe failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Deaktivieren'
            ], 500);
        }
    }

    /**
     * Get VAPID public key
     */
    public function getPublicKey()
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key')
        ]);
    }

    /**
     * Send a test notification
     */
    public function sendTest(Request $request)
    {
        try {
            $subscriptions = Auth::user()->pushSubscriptions()->where('is_active', true)->get();

            if ($subscriptions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keine aktiven Push-Subscriptions gefunden'
                ], 404);
            }

            $auth = [
                'VAPID' => [
                    'subject' => config('webpush.vapid.subject'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ];

            $webPush = new WebPush($auth);

            $payload = json_encode([
                'title' => 'Test-Benachrichtigung',
                'body' => 'Push-Benachrichtigungen funktionieren! 🎉',
                'icon' => '/logo-thwtrainer.png',
                'url' => '/dashboard'
            ]);

            foreach ($subscriptions as $subscription) {
                $pushSubscription = Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding,
                ]);

                $webPush->queueNotification($pushSubscription, $payload);
            }

            $results = $webPush->flush();

            return response()->json([
                'success' => true,
                'message' => 'Test-Benachrichtigung gesendet!'
            ]);
        } catch (\Exception $e) {
            Log::error('Push notification send failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Senden: ' . $e->getMessage()
            ], 500);
        }
    }
}
