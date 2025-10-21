<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    /**
     * Subscribe to push notifications.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        try {
            // Prüfe ob diese Subscription bereits existiert
            $subscription = PushSubscription::where('user_id', auth()->id())
                ->where('endpoint', $validated['endpoint'])
                ->first();

            if ($subscription) {
                // Reaktiviere existierende Subscription
                $subscription->update(['is_active' => true]);
            } else {
                // Erstelle neue Subscription
                $subscription = PushSubscription::create([
                    'user_id' => auth()->id(),
                    'endpoint' => $validated['endpoint'],
                    'public_key' => $validated['keys']['p256dh'],
                    'auth_token' => $validated['keys']['auth'],
                    'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                ]);
            }

            Log::info('Push subscription created/updated', [
                'user_id' => auth()->id(),
                'subscription_id' => $subscription->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push-Benachrichtigungen aktiviert',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create push subscription', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktivieren der Push-Benachrichtigungen',
            ], 500);
        }
    }

    /**
     * Unsubscribe from push notifications.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        try {
            $subscription = PushSubscription::where('user_id', auth()->id())
                ->where('endpoint', $validated['endpoint'])
                ->first();

            if ($subscription) {
                $subscription->deactivate();
                
                Log::info('Push subscription deactivated', [
                    'user_id' => auth()->id(),
                    'subscription_id' => $subscription->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Push-Benachrichtigungen deaktiviert',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Subscription nicht gefunden',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe from push', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Deaktivieren der Push-Benachrichtigungen',
            ], 500);
        }
    }

    /**
     * Get the VAPID public key for the client.
     */
    public function getPublicKey(): JsonResponse
    {
        $publicKey = config('services.vapid.public_key');

        if (!$publicKey) {
            return response()->json([
                'success' => false,
                'message' => 'VAPID public key nicht konfiguriert',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'publicKey' => $publicKey,
        ]);
    }
}

