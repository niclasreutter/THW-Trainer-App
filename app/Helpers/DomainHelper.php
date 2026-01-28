<?php

namespace App\Helpers;

/**
 * Helper für Domain-übergreifende URL-Generierung
 *
 * Generiert korrekte URLs für die Multi-Domain-Architektur:
 * - thw-trainer.de (Landing)
 * - app.thw-trainer.de (Application)
 */
class DomainHelper
{
    /**
     * Generiert URL für die App-Subdomain
     *
     * @param string $path Pfad (z.B. '/dashboard' oder 'login')
     * @return string Vollständige URL
     */
    public static function appUrl(string $path = ''): string
    {
        if (config('domains.development')) {
            return url($path);
        }

        $protocol = request()->secure() ? 'https://' : 'http://';
        return $protocol . config('domains.app') . '/' . ltrim($path, '/');
    }

    /**
     * Generiert URL für die Landing-Domain
     *
     * @param string $path Pfad (z.B. '/kontakt' oder 'impressum')
     * @return string Vollständige URL
     */
    public static function landingUrl(string $path = ''): string
    {
        if (config('domains.development')) {
            return url($path);
        }

        $protocol = request()->secure() ? 'https://' : 'http://';
        return $protocol . config('domains.landing') . '/' . ltrim($path, '/');
    }

    /**
     * Prüft ob aktuell auf der Landing-Domain
     *
     * @return bool
     */
    public static function isLandingDomain(): bool
    {
        if (config('domains.development')) {
            // In Development: prüfe ob Route mit 'landing.' beginnt
            return str_starts_with(request()->route()?->getName() ?? '', 'landing.');
        }

        return request()->getHost() === config('domains.landing');
    }

    /**
     * Prüft ob aktuell auf der App-Domain
     *
     * @return bool
     */
    public static function isAppDomain(): bool
    {
        if (config('domains.development')) {
            // In Development: prüfe ob Route NICHT mit 'landing.' beginnt
            return !str_starts_with(request()->route()?->getName() ?? '', 'landing.');
        }

        return request()->getHost() === config('domains.app');
    }
}
