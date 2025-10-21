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
            Log::info('=== Push Test Start ===');
            Log::info('User ID: ' . Auth::id());
            
            $subscriptions = Auth::user()->pushSubscriptions()->where('is_active', true)->get();
            Log::info('Found subscriptions: ' . $subscriptions->count());

            if ($subscriptions->isEmpty()) {
                Log::warning('No active push subscriptions found');
                return response()->json([
                    'success' => false,
                    'message' => 'Keine aktiven Push-Subscriptions gefunden'
                ], 404);
            }

            // Check VAPID config
            $vapidSubject = config('webpush.vapid.subject');
            $vapidPublic = config('webpush.vapid.public_key');
            $vapidPrivate = config('webpush.vapid.private_key');
            
            Log::info('VAPID Subject: ' . ($vapidSubject ?: 'NOT SET'));
            Log::info('VAPID Public Key: ' . (strlen($vapidPublic) > 0 ? 'SET (' . strlen($vapidPublic) . ' chars)' : 'NOT SET'));
            Log::info('VAPID Private Key: ' . (strlen($vapidPrivate) > 0 ? 'SET (' . strlen($vapidPrivate) . ' chars)' : 'NOT SET'));
            
            if (!$vapidSubject || !$vapidPublic || !$vapidPrivate) {
                Log::error('VAPID keys not configured properly!');
                return response()->json([
                    'success' => false,
                    'message' => 'VAPID-Keys sind nicht konfiguriert. Bitte .env prüfen!'
                ], 500);
            }

            $auth = [
                'VAPID' => [
                    'subject' => $vapidSubject,
                    'publicKey' => $vapidPublic,
                    'privateKey' => $vapidPrivate,
                ],
            ];

            $webPush = new WebPush($auth);

            $payload = json_encode([
                'title' => 'Test-Benachrichtigung',
                'body' => 'Push-Benachrichtigungen funktionieren! 🎉',
                'icon' => '/logo-thwtrainer.png',
                'url' => '/dashboard'
            ]);
            
            Log::info('Payload: ' . $payload);

            $sentCount = 0;
            $failedCount = 0;

            foreach ($subscriptions as $subscription) {
                try {
                    Log::info('Sending to endpoint: ' . substr($subscription->endpoint, 0, 50) . '...');
                    
                    $pushSubscription = Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'publicKey' => $subscription->public_key,
                        'authToken' => $subscription->auth_token,
                        'contentEncoding' => $subscription->content_encoding,
                    ]);

                    $webPush->queueNotification($pushSubscription, $payload);
                } catch (\Exception $e) {
                    Log::error('Failed to queue notification: ' . $e->getMessage());
                    $failedCount++;
                }
            }

            Log::info('Flushing notifications...');
            $results = $webPush->flush();
            
            // Check results
            foreach ($results as $result) {
                if ($result->isSuccess()) {
                    Log::info('Push sent successfully to: ' . substr($result->getEndpoint(), 0, 50));
                    $sentCount++;
                } else {
                    Log::error('Push failed: ' . $result->getReason() . ' to: ' . substr($result->getEndpoint(), 0, 50));
                    $failedCount++;
                }
            }
            
            Log::info('=== Push Test End === Sent: ' . $sentCount . ', Failed: ' . $failedCount);

            if ($sentCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test-Benachrichtigung gesendet! (' . $sentCount . ' erfolgreich, ' . $failedCount . ' fehlgeschlagen)'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Alle Benachrichtigungen fehlgeschlagen. Details im Log.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Push notification send failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Senden: ' . $e->getMessage()
            ], 500);
        }
    }
}
