<?php

namespace App\Console\Commands;

use App\Mail\StreakFreezeActivatedMail;
use App\Mail\StreakLostMail;
use App\Models\User;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gamification:daily-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Täglicher Reset: Streak-Prüfung + Daily-Questions-Reset für alle User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starte tägliche Reset-Prüfung...');

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $streakResetsCount = 0;
        $streakFreezeCount = 0;
        $dailyQuestionsResetsCount = 0;
        $errors = 0;
        $gamificationService = new GamificationService();

        $this->info("Heute: {$today->format('Y-m-d')}");
        $this->info("Gestern: {$yesterday->format('Y-m-d')}");

        $allUsers = User::all();
        $this->info("Benutzer gesamt: {$allUsers->count()}");

        foreach ($allUsers as $user) {
            try {
                $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;
                $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
                $changed = false;

                // 1. RESET DAILY QUESTIONS (für ALLE User)
                if ($user->daily_questions_date && Carbon::parse($user->daily_questions_date)->lt($today)) {
                    $oldDailyQuestions = $user->daily_questions_solved ?? 0;
                    $user->daily_questions_solved = 0;
                    $user->daily_questions_date = null;
                    $changed = true;
                    $dailyQuestionsResetsCount++;

                    $this->line("  Daily Questions zurückgesetzt: {$user->name} ({$oldDailyQuestions} → 0)");
                }

                // 2. STREAK: Prüfe ob Mindestaktivität gestern NICHT erreicht wurde
                if ($user->streak_days > 0) {
                    if (!$lastActivity || $lastActivity->lt($yesterday)) {
                        // Tagesziel war 0 → auto-qualify (keine Fragen verfügbar)
                        if ($user->daily_streak_goal === 0) {
                            $user->last_activity_date = $yesterday;
                            $changed = true;
                            $this->line("  Streak auto-qualifiziert (Tagesziel=0): {$user->name} (Streak: {$user->streak_days})");
                        } elseif (($freezeStatus = $gamificationService->getStreakFreezeStatus($user)) && $freezeStatus['remaining'] > 0) {
                            // Auto-Freeze: Streak wird geschützt
                            $freezeResult = $gamificationService->applyManualFreeze($user);

                            if ($freezeResult['success']) {
                                $streakFreezeCount++;
                                $this->info("  Streak Freeze auto-eingesetzt: {$user->name} (Streak: {$user->streak_days}, Remaining: {$freezeResult['remaining']})");

                                // E-Mail-Benachrichtigung senden
                                if ($user->email_consent) {
                                    try {
                                        Mail::to($user->email)->send(new StreakFreezeActivatedMail(
                                            $user,
                                            $user->streak_days,
                                            $freezeResult['remaining']
                                        ));
                                    } catch (\Exception $mailError) {
                                        $this->warn("  Mail-Fehler ({$user->email}): " . $mailError->getMessage());
                                    }
                                }
                            } else {
                                // Freeze fehlgeschlagen - Streak resetten
                                $oldStreak = $user->streak_days;
                                $user->streak_days = 0;
                                $changed = true;
                                $streakResetsCount++;
                                $this->warn("  Streak zurückgesetzt (Freeze fehlgeschlagen): {$user->name} ({$oldStreak} → 0)");

                                $this->sendStreakLostMail($user, $oldStreak);

                                if ($user->pushSubscriptions()->exists()) {
                                    $user->notify(new \App\Notifications\PushNotification(
                                        'Streak verloren',
                                        'Dein Streak wurde zurueckgesetzt. Starte heute einen neuen!',
                                        '/dashboard'
                                    ));
                                }
                            }
                        } else {
                            // Kein Freeze verfügbar - Streak resetten
                            $oldStreak = $user->streak_days;
                            $user->streak_days = 0;
                            $changed = true;
                            $streakResetsCount++;

                            $this->line("  Streak zurückgesetzt: {$user->name} ({$oldStreak} → 0, Aktivität: {$lastActivityStr})");

                            $this->sendStreakLostMail($user, $oldStreak);

                            if ($user->pushSubscriptions()->exists()) {
                                $user->notify(new \App\Notifications\PushNotification(
                                    'Streak verloren',
                                    'Dein Streak wurde zurueckgesetzt. Starte heute einen neuen!',
                                    '/dashboard'
                                ));
                            }
                        }
                    }
                }

                if ($changed) {
                    $user->save();
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("FEHLER bei {$user->email}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('=== Tägliche Reset-Prüfung abgeschlossen ===');
        $this->info("Daily Questions zurückgesetzt: {$dailyQuestionsResetsCount}");
        $this->info("Streaks zurückgesetzt: {$streakResetsCount}");
        $this->info("Streak Freezes auto-eingesetzt: {$streakFreezeCount}");
        $this->info("Fehler: {$errors}");

        return Command::SUCCESS;
    }

    /**
     * Sendet Streak-Verlust-Mail wenn E-Mail-Zustimmung vorhanden.
     */
    private function sendStreakLostMail(User $user, int $oldStreak): void
    {
        if ($user->email_consent) {
            try {
                Mail::to($user->email)->send(new StreakLostMail($user, $oldStreak));
            } catch (\Exception $mailError) {
                $this->warn("  Mail-Fehler ({$user->email}): " . $mailError->getMessage());
            }
        }
    }
}
