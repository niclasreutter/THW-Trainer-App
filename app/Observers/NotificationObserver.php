<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        try {
            Log::info('Push Observer fired', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
            ]);
            app(WebPushService::class)->sendNotification($notification);
            Log::info('Push Observer completed', [
                'notification_id' => $notification->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Web push failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
