<?php

namespace App\Services;

use App\Models\DataDeletionLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DSGVO-konforme Löschung eines Nutzer-Accounts mit allen verbundenen Daten.
 *
 * Die meisten user-bezogenen Tabellen sind via Foreign Key bereits mit
 * cascadeOnDelete konfiguriert und werden automatisch beim User-Delete
 * entfernt. Dieser Service kümmert sich um die Lücken:
 *
 *   - Sessions-Tabelle (Laravel) hat user_id ohne FK
 *   - Verifikation, dass nichts vergessen wird (zentrale Stelle)
 *   - Compliance-Trail in data_deletion_logs (ohne personenbezogene Daten
 *     zum gelöschten User, damit Art. 17 DSGVO gewahrt bleibt)
 */
class UserDeletionService
{
    public function deleteUser(
        User $user,
        ?User $admin = null,
        ?string $reason = null,
        ?Request $request = null
    ): void {
        $userId = $user->id;
        $ipAddress = $request?->ip();

        DB::transaction(function () use ($user, $userId, $admin, $reason, $ipAddress) {
            $this->invalidateSessions($userId);

            $user->delete();

            DataDeletionLog::create([
                'admin_id'      => $admin?->id,
                'deletion_type' => 'user_account',
                'reason'        => $reason,
                'ip_address'    => $ipAddress,
                'deleted_at'    => now(),
            ]);
        });

        Log::info('User account deleted (DSGVO)', [
            'admin_id'   => $admin?->id,
            'deleted_at' => now()->toIso8601String(),
            'reason'     => $reason,
        ]);
    }

    private function invalidateSessions(int $userId): void
    {
        DB::table('sessions')->where('user_id', $userId)->delete();
    }
}
