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
            ]);
            $this->webPush->setAutomaticPadding(false);
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
            if ($report->isSubscriptionExpired()) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                PushSubscription::where('endpoint', $endpoint)
                    ->update(['is_active' => false]);

                Log::info('Push subscription expired, deactivated', [
                    'endpoint' => substr($endpoint, 0, 80),
                ]);
            }
        }
    }
}
