<?php

namespace App\Support;

/**
 * Aktuelle App-Version aus der VERSION-Datei im Projekt-Root.
 * Single Source of Truth für das Footer-Badge; gepflegt über `php artisan app:release`.
 */
final class AppVersion
{
    private static ?string $cached = null;

    private static bool $loaded = false;

    public static function current(): ?string
    {
        if (! self::$loaded) {
            self::$loaded = true;
            $path = base_path('VERSION');
            $version = is_file($path) ? trim((string) file_get_contents($path)) : '';
            self::$cached = $version !== '' ? $version : null;
        }

        return self::$cached;
    }
}
