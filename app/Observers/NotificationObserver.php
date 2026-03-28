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
            app(WebPushService::class)->sendNotification($notification);
        } catch (\Throwable $e) {
            Log::error('Web push failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
