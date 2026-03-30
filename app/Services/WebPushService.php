<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    private ?WebPush $webPush = null;

    private function getWebPush(): WebPush
    {
        if ($this->webPush === null) {
            $this->webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('services.vapid.subject'),
                    'publicKey' => config('services.vapid.public_key'),
                    'privateKey' => config('services.vapid.private_key'),
                ],
            ], [], 30, [
                // Apple Push Service braucht explizites Timeout und akzeptiert
                // nur bestimmte TLS-Versionen
                'verify' => true,
            ]);
            $this->webPush->setAutomaticPadding(false);
            // TTL: 24 Stunden — Apple verwirft Push ohne TTL oder mit TTL=0
            $this->webPush->setDefaultOptions(['TTL' => 86400]);
        }

        return $this->webPush;
    }

    /**
     * Send push notification based on a DB Notification (called from Observer).
     */
    public function sendNotification(Notification $notification): void
    {
        $url = $this->resolveUrlForType($notification->type);

        $payload = json_encode([
            'title' => $notification->title,
            'body' => $notification->message,
            'url' => $url,
        ]);

        $this->sendToUserById($notification->user_id, $payload);
    }

    /**
     * Send a direct push to a user (for scheduler commands, no DB Notification needed).
     */
    public function sendToUser(User $user, string $title, string $body, string $url = '/notifications'): void
    {
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
        ]);

        $this->sendToUserById($user->id, $payload);
    }

    private function resolveUrlForType(string $type): string
    {
        return match (true) {
            str_contains($type, 'streak') => '/practice',
            str_contains($type, 'league') => '/leaderboard',
            default => '/notifications',
        };
    }

    private function sendToUserById(int $userId, string $payload): void
    {
        $subscriptions = PushSubscription::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        if ($subscriptions->isEmpty()) {
            Log::debug('No active push subscriptions for user', ['user_id' => $userId]);
            return;
        }

        $webPush = $this->getWebPush();

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding,
                ]),
                $payload
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            $isApple = str_contains($endpoint, 'apple') || str_contains($endpoint, 'push.apple');
            $service = $isApple ? 'Apple' : 'FCM';

            if ($report->isSuccess()) {
                Log::debug('Push sent successfully', [
                    'user_id' => $userId,
                    'service' => $service,
                    'endpoint' => substr($endpoint, 0, 80),
                ]);
            } elseif ($report->isSubscriptionExpired()) {
                PushSubscription::where('endpoint', $endpoint)
                    ->update(['is_active' => false]);

                Log::warning('Push subscription expired, deactivated', [
                    'user_id' => $userId,
                    'service' => $service,
                    'endpoint' => substr($endpoint, 0, 80),
                    'reason' => $report->getReason(),
                    'status' => $report->getResponse()?->getStatusCode(),
                ]);
            } else {
                Log::error('Push send failed', [
                    'user_id' => $userId,
                    'service' => $service,
                    'endpoint' => substr($endpoint, 0, 80),
                    'reason' => $report->getReason(),
                    'status' => $report->getResponse()?->getStatusCode(),
                    'expired' => $report->isSubscriptionExpired(),
                ]);
            }
        }
    }
}
