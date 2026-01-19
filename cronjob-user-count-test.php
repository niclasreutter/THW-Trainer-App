<?php

/**
 * User Count History - TEST VERSION
 * Zählt täglich alle User und speichert in user_count_history
 * Löscht Einträge älter als 30 Tage
 *
 * TEST KONFIGURATION:
 * - Kann manuell aufgerufen werden: php cronjob-user-count-test.php
 * - Zeigt detaillierte Debug-Info
 */

// Load Laravel
require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\UserCountHistory;
use Carbon\Carbon;

echo "=== USER COUNT HISTORY CRONJOB TEST ===\n\n";
echo "Zeit: " . now()->format('d.m.Y H:i:s') . "\n\n";

try {
    $today = Carbon::today();

    // Zähle User
    $totalUsers = User::count();
    $verifiedUsers = User::whereNotNull('email_verified_at')->count();

    echo "📊 User-Statistiken:\n";
    echo "   Total User: {$totalUsers}\n";
    echo "   Verifiziert: {$verifiedUsers}\n";
    echo "   Unverifiziert: " . ($totalUsers - $verifiedUsers) . "\n\n";

    // Erstelle oder aktualisiere Eintrag für heute
    UserCountHistory::updateOrCreate(
        ['date' => $today],
        [
            'total_users' => $totalUsers,
            'verified_users' => $verifiedUsers,
        ]
    );

    echo "✅ Eintrag für {$today->format('d.m.Y')} gespeichert\n\n";

    // Zeige letzte Einträge
    echo "📈 Letzte 7 Einträge:\n";
    $recentEntries = UserCountHistory::orderBy('date', 'desc')->take(7)->get();
    foreach ($recentEntries as $entry) {
        echo "   {$entry->date->format('d.m.Y')}: {$entry->total_users} User ({$entry->verified_users} verifiziert)\n";
    }
    echo "\n";

    // Lösche Einträge älter als 30 Tage
    $cutoffDate = Carbon::today()->subDays(30);
    $deleted = UserCountHistory::where('date', '<', $cutoffDate)->delete();

    if ($deleted > 0) {
        echo "🗑️  {$deleted} alte Einträge gelöscht (älter als {$cutoffDate->format('d.m.Y')})\n";
    } else {
        echo "ℹ️  Keine alten Einträge zum Löschen gefunden\n";
    }

    echo "\n✅ Cronjob erfolgreich abgeschlossen\n";

} catch (\Exception $e) {
    echo "\n❌ FEHLER: " . $e->getMessage() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
