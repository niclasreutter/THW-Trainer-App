<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\LeagueService;
use Illuminate\Console\Command;

class SendLeagueStatusPush extends Command
{
    protected $signature = 'push:league-status';

    protected $description = 'Sendet Liga-Status Push an alle Liga-Teilnehmer (Mi, Fr, So)';

    public function handle(): int
    {
        $this->info('Starte Liga-Status Push...');

        $leagueService = new LeagueService();
        $sent = 0;
        $errors = 0;

        // Alle User mit aktiver Push-Subscription und Liga-Teilnahme
        $users = User::where('leaderboard_consent', true)
            ->where('weekly_points', '>', 0)
            ->whereHas('pushSubscriptions', fn($q) => $q->where('is_active', true))
            ->get();

        $this->info("Gefunden: {$users->count()} Liga-Teilnehmer mit Push.");

        // Leaderboards pro Liga cachen
        $leaderboards = [];

        foreach ($users as $user) {
            try {
                $league = $user->league ?? 'bronze';

                if (!isset($leaderboards[$league])) {
                    $leaderboards[$league] = $leagueService->getLeagueLeaderboard($league);
                }

                $leaderboard = $leaderboards[$league];
                $position = $leaderboard->search(fn($u) => $u->id === $user->id);

                if ($position === false) {
                    continue;
                }

                $rank = $position + 1;
                $leagueName = LeagueService::getLeagueName($league);
                $message = $this->buildMessage($rank, $leagueName, $leaderboard, $position, $user);

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'league_status',
                    'title' => "Liga-Update: {$leagueName}",
                    'message' => $message,
                    'icon' => 'bi-trophy',
                    'data' => ['league' => $league, 'rank' => $rank],
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $errors++;
                $this->error("Fehler bei User {$user->id}: {$e->getMessage()}");
                \Log::error('Liga-Status Push fehlgeschlagen', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Liga-Status Push abgeschlossen: {$sent} gesendet, {$errors} Fehler.");

        return Command::SUCCESS;
    }

    private function buildMessage(int $rank, string $leagueName, $leaderboard, int $position, User $user): string
    {
        $messages = [];

        if ($rank === 1) {
            $messages = [
                "Du fuehrst die {$leagueName}-Liga an! Halte deine Position.",
                "Platz 1 in der {$leagueName}-Liga! Weiter so.",
                "Spitzenposition in der {$leagueName}-Liga! Bleib dran.",
            ];
        } elseif ($position > 0) {
            $userAbove = $leaderboard[$position - 1];
            $pointsGap = $userAbove->weekly_points - $user->weekly_points;

            $messages = [
                "Du bist auf Platz {$rank} in der {$leagueName}-Liga. Noch {$pointsGap} Punkte bis Platz " . ($rank - 1) . "!",
                "Platz {$rank} in der {$leagueName}-Liga — mit ein paar Fragen kannst du weiter aufsteigen!",
                "Deine Liga-Position: Platz {$rank}. Ein bisschen Lernen bringt dich nach oben!",
            ];
        } else {
            $messages = [
                "Du bist auf Platz {$rank} in der {$leagueName}-Liga. Weiter lernen lohnt sich!",
            ];
        }

        return $messages[array_rand($messages)];
    }
}
