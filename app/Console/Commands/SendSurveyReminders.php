<?php

namespace App\Console\Commands;

use App\Mail\SurveyReminderMail;
use App\Models\Survey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSurveyReminders extends Command
{
    protected $signature = 'survey:send-reminders';

    protected $description = 'Sendet Umfrage-Erinnerungen an User die mindestens 3 Wochen registriert sind';

    public function handle()
    {
        $this->info('Starte Umfrage-Erinnerungsversand...');

        $activeSurvey = Survey::active()->first();

        if (!$activeSurvey) {
            $this->info('Keine aktive Umfrage vorhanden. Abbruch.');
            return Command::SUCCESS;
        }

        $threeWeeksAgo = Carbon::now('Europe/Berlin')->subWeeks(3);
        $emailsSent = 0;
        $errors = 0;

        $users = User::where('email_consent', true)
            ->where('created_at', '<=', $threeWeeksAgo)
            ->whereNull('survey_reminder_sent_at')
            ->whereDoesntHave('surveyResponses', function ($query) use ($activeSurvey) {
                $query->where('survey_id', $activeSurvey->id);
            })
            ->get();

        $this->info("Gefunden: {$users->count()} User für Umfrage-Erinnerung.");

        foreach ($users as $user) {
            try {
                $surveyUrl = url('/umfrage');

                Mail::to($user->email)->send(new SurveyReminderMail($user, $surveyUrl));

                $user->survey_reminder_sent_at = Carbon::now();
                $user->save();

                $emailsSent++;
                $this->info("Umfrage-Erinnerung gesendet an: {$user->name} ({$user->email})");

                \Log::info('Survey reminder sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'survey_id' => $activeSurvey->id,
                ]);
            } catch (\Exception $e) {
                $errors++;
                $this->error("Fehler bei {$user->email}: " . $e->getMessage());
                \Log::error('Failed to send survey reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Fertig! Gesendet: {$emailsSent}, Fehler: {$errors}");

        return Command::SUCCESS;
    }
}
