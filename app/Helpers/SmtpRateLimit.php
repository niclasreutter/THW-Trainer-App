<?php

namespace App\Helpers;

use Carbon\Carbon;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;
use Throwable;

/**
 * Hilfen für den Umgang mit dem SMTP-Hourly-Limit des Hosters.
 *
 * Der Hoster lehnt Mails bei Überschreitung des Stundenlimits mit
 * "554 5.7.0 ... limit on the number of allowed outgoing messages was exceeded"
 * ab. Solche Mails werden zur nächsten vollen Stunde + 10 Minuten erneut versendet.
 */
class SmtpRateLimit
{
    /**
     * Prüft (rekursiv über die Exception-Kette), ob es sich um das
     * SMTP-554-Hourly-Limit handelt.
     */
    public static function isRateLimitError(?Throwable $e): bool
    {
        while ($e !== null) {
            if ($e instanceof UnexpectedResponseException) {
                $message = $e->getMessage();
                if (str_contains($message, '554') && stripos($message, 'limit') !== false) {
                    return true;
                }
            }
            $e = $e->getPrevious();
        }

        return false;
    }

    /**
     * Berechnet die Sekunden bis zum nächsten Retry-Zeitpunkt
     * (= nächste volle Stunde + 10 Minuten).
     *
     * Beispiele:
     *   08:33 → 09:10  (37 min, 2220 s)
     *   09:05 → 10:10  (65 min, 3900 s)
     *   09:10 → 10:10  (60 min, 3600 s)
     *   09:59 → 10:10  (11 min,  660 s)
     */
    public static function calculateRetryDelay(?Carbon $now = null): int
    {
        $now = $now ?? Carbon::now();
        $retryAt = $now->copy()->addHour()->startOfHour()->addMinutes(10);

        return max(60, (int) $retryAt->diffInSeconds($now, true));
    }

    /**
     * Liefert den Carbon-Zeitpunkt, zu dem der nächste Retry erfolgen soll.
     */
    public static function nextRetryAt(?Carbon $now = null): Carbon
    {
        $now = $now ?? Carbon::now();

        return $now->copy()->addHour()->startOfHour()->addMinutes(10);
    }
}
