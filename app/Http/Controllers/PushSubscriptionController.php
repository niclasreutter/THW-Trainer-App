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

            // Deaktiviere alte Subscription falls Endpoint gewechselt hat (SW re-subscribe)
            $oldEndpoint = $request->input('old_endpoint');
            if ($oldEndpoint) {
                PushSubscription::where('user_id', auth()->id())
                    ->where('endpoint', $oldEndpoint)
                    ->update(['is_active' => false]);
            }

            if ($subscription) {
                // Reaktiviere existierende Subscription
                $subscription->update([
                    'is_active' => true,
                    'public_key' => $validated['keys']['p256dh'],
                    'auth_token' => $validated['keys']['auth'],
                    // IMMER aes128gcm — iOS Safari meldet faelschlicherweise aesgcm,
                    // aber Apple Push Service verlangt aes128gcm
                    'content_encoding' => 'aes128gcm',
                ]);
            } else {
                // Erstelle neue Subscription
                $subscription = PushSubscription::create([
                    'user_id' => auth()->id(),
                    'endpoint' => $validated['endpoint'],
                    'public_key' => $validated['keys']['p256dh'],
                    'auth_token' => $validated['keys']['auth'],
                    'content_encoding' => 'aes128gcm',
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
     * Push debug/test page — shows subscription status and allows sending test push.
     */
    public function debugPage()
    {
        $user = auth()->user();
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        return view('admin.push-test', [
            'subscriptions' => $subscriptions,
            'vapidConfigured' => !empty(config('services.vapid.public_key')),
        ]);
    }

    /**
     * Send a test push to the current user.
     */
    public function testPush(): JsonResponse
    {
        $user = auth()->user();
        $subscriptions = PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        if ($subscriptions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine aktiven Push-Subscriptions gefunden',
            ]);
        }

        $details = $subscriptions->map(fn ($s) => [
            'id' => $s->id,
            'is_apple' => str_contains($s->endpoint, 'apple') || str_contains($s->endpoint, 'push.apple'),
            'endpoint_short' => substr($s->endpoint, 0, 80) . '...',
            'encoding' => $s->content_encoding,
        ]);

        try {
            app(\App\Services\WebPushService::class)->sendToUser(
                $user,
                'Test Push',
                'Wenn du das siehst, funktionieren Push-Notifications!',
                '/notifications'
            );

            return response()->json([
                'success' => true,
                'message' => 'Test-Push gesendet — pruefe Logs fuer Details',
                'subscriptions' => $details,
            ]);
        } catch (\Throwable $e) {
            Log::error('Test push failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Push fehlgeschlagen: ' . $e->getMessage(),
                'subscriptions' => $details,
            ], 500);
        }
    }

    /**
     * Debug ping from service worker — logs that push was received on device.
     */
    public function debugPing(Request $request): JsonResponse
    {
        Log::warning('PUSH DEBUG PING — SW received push on device', [
            'sw_version' => $request->input('sw_version'),
            'title' => $request->input('title'),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 100),
        ]);

        return response()->json(['ok' => true]);
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

