<?php

/**
 * User Count History - PRODUCTION
 * Zählt täglich alle User und speichert in user_count_history
 * Löscht Einträge älter als 30 Tage
 *
 * PLESK KONFIGURATION:
 * - Typ: PHP Skript ausführen
 * - PHP Version: PHP 8.2
 * - Pfad: /cronjob-user-count-prod.php
 * - Zeitplan: Täglich um 23:55 Uhr
 */

// Load Laravel
require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\UserCountHistory;
use Carbon\Carbon;

try {
    $today = Carbon::today();

    // Zähle User
    $totalUsers = User::count();
    $verifiedUsers = User::whereNotNull('email_verified_at')->count();

    // Erstelle oder aktualisiere Eintrag für heute
    UserCountHistory::updateOrCreate(
        ['date' => $today],
        [
            'total_users' => $totalUsers,
            'verified_users' => $verifiedUsers,
        ]
    );

    echo "✅ User Count gespeichert für {$today->format('d.m.Y')}\n";
    echo "   Total: {$totalUsers}, Verifiziert: {$verifiedUsers}\n";

    // Lösche Einträge älter als 30 Tage
    $cutoffDate = Carbon::today()->subDays(30);
    $deleted = UserCountHistory::where('date', '<', $cutoffDate)->delete();

    if ($deleted > 0) {
        echo "🗑️  {$deleted} alte Einträge gelöscht (älter als {$cutoffDate->format('d.m.Y')})\n";
    }

    echo "✅ Cronjob erfolgreich abgeschlossen\n";

} catch (\Exception $e) {
    echo "❌ Fehler: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
