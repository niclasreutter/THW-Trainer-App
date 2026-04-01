# Scheduled Push Notifications Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 4 zeitgesteuerte Push-Notification-Typen: Guten-Morgen, Pruefungs-Tagesplan, Streak-Erinnerungen (3x), Lernsession-Start.

**Architecture:** 3 neue Artisan Commands im Laravel Scheduler + Integration in bestehenden LernsessionService. Alle nutzen die vorhandene `PushNotification`-Klasse (queued). User-Selektion via `whereHas('pushSubscriptions')`.

**Tech Stack:** Laravel 12 Scheduler, Artisan Commands, PushNotification (bereits vorhanden)

---

## File Structure

### New Files
| File | Responsibility |
|------|---------------|
| `app/Console/Commands/SendMorningPush.php` | Guten-Morgen Push um 08:30 |
| `app/Console/Commands/SendExamReminderPush.php` | Pruefungs-Tagesplan Push um 09:00 |
| `app/Console/Commands/SendStreakPush.php` | Streak-Erinnerungen um 12:00, 17:00, 21:00 |

### Modified Files
| File | Change |
|------|--------|
| `routes/console.php` | 5 neue Schedule-Eintraege |
| `app/Services/LernsessionService.php:252-279` | Push-Dispatch in sendSessionStartedEmails() |

---

### Task 1: Guten-Morgen Push Command

**Files:**
- Create: `app/Console/Commands/SendMorningPush.php`
- Modify: `routes/console.php` (nach Zeile 58, nach spaced-repetition-reminders)

- [ ] **Step 1: Create the SendMorningPush command**

Create `app/Console/Commands/SendMorningPush.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Console\Command;

class SendMorningPush extends Command
{
    protected $signature = 'push:send-morning';

    protected $description = 'Send good morning push notification to all users with push subscriptions';

    public function handle()
    {
        $this->info('Sending morning push notifications...');

        $users = User::whereHas('pushSubscriptions')->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $user->notify(new PushNotification(
                    'Guten Morgen!',
                    'Zeit zum Lernen — starte jetzt deine taegliche Session.',
                    '/dashboard'
                ));
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Morning push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
```

- [ ] **Step 2: Register in scheduler**

In `routes/console.php`, add after the spaced-repetition-reminders block (after line 58):

```php

// Guten-Morgen Push
// Laeuft taeglich um 08:30 Uhr
Schedule::command('push:send-morning')
    ->dailyAt('08:30')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-morning'))
    ->description('Sendet Guten-Morgen Push an alle User mit Push-Subscription')
    ->emailOutputOnFailure($adminEmail);
```

- [ ] **Step 3: Verify command is registered**

Run:
```bash
php artisan list | grep push:send-morning
```
Expected: `push:send-morning` listed.

- [ ] **Step 4: Commit**

```bash
git add app/Console/Commands/SendMorningPush.php routes/console.php
git commit -m "✨: Guten-Morgen Push Command"
```

---

### Task 2: Pruefungs-Tagesplan Push Command

**Files:**
- Create: `app/Console/Commands/SendExamReminderPush.php`
- Modify: `routes/console.php` (nach dem Morning-Push-Eintrag)

- [ ] **Step 1: Create the SendExamReminderPush command**

Create `app/Console/Commands/SendExamReminderPush.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendExamReminderPush extends Command
{
    protected $signature = 'push:send-exam-reminder';

    protected $description = 'Send daily exam reminder push to users with upcoming exam date';

    public function handle()
    {
        $this->info('Sending exam reminder push notifications...');

        $today = Carbon::today();

        $users = User::whereHas('pushSubscriptions')
            ->whereNotNull('exam_date')
            ->where('exam_date', '>=', $today)
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $daysLeft = $today->diffInDays(Carbon::parse($user->exam_date));
                $dailyGoal = $user->daily_streak_goal ?? 20;

                $user->notify(new PushNotification(
                    "Pruefung in {$daysLeft} Tagen",
                    "Dein Tagesziel: {$dailyGoal} Fragen — bleib dran!",
                    '/dashboard'
                ));
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Exam reminder push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
```

- [ ] **Step 2: Register in scheduler**

In `routes/console.php`, add after the morning push entry:

```php

// Pruefungs-Tagesplan Push
// Laeuft taeglich um 09:00 Uhr
Schedule::command('push:send-exam-reminder')
    ->dailyAt('09:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-exam-reminder'))
    ->description('Sendet Pruefungs-Tagesplan Push an User mit Pruefungsdatum')
    ->emailOutputOnFailure($adminEmail);
```

- [ ] **Step 3: Verify command is registered**

Run:
```bash
php artisan list | grep push:send-exam
```
Expected: `push:send-exam-reminder` listed.

- [ ] **Step 4: Commit**

```bash
git add app/Console/Commands/SendExamReminderPush.php routes/console.php
git commit -m "✨: Pruefungs-Tagesplan Push Command"
```

---

### Task 3: Streak-Erinnerungen Push Command

**Files:**
- Create: `app/Console/Commands/SendStreakPush.php`
- Modify: `routes/console.php` (nach dem Exam-Reminder-Push-Eintrag)

- [ ] **Step 1: Create the SendStreakPush command**

Create `app/Console/Commands/SendStreakPush.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendStreakPush extends Command
{
    protected $signature = 'push:send-streak {time : morning, afternoon, or evening}';

    protected $description = 'Send streak reminder push to users who have not completed their daily goal';

    private const MESSAGES = [
        'morning' => [
            'title' => 'Streak-Erinnerung',
            'body' => 'Du hast heute noch %d Fragen offen fuer deinen Streak.',
        ],
        'afternoon' => [
            'title' => 'Streak-Erinnerung',
            'body' => 'Noch %d Fragen fuer deinen %d-Tage-Streak!',
        ],
        'evening' => [
            'title' => 'Letzte Chance!',
            'body' => 'Noch %d Fragen, sonst geht dein %d-Tage-Streak verloren!',
        ],
    ];

    public function handle()
    {
        $time = $this->argument('time');

        if (!isset(self::MESSAGES[$time])) {
            $this->error("Invalid time argument: {$time}. Use morning, afternoon, or evening.");
            return Command::FAILURE;
        }

        $this->info("Sending streak push ({$time})...");

        $today = Carbon::today();

        $users = User::whereHas('pushSubscriptions')
            ->where('streak_days', '>=', 1)
            ->where(function ($query) use ($today) {
                $query->whereNull('last_activity_date')
                      ->orWhere('last_activity_date', '!=', $today);
            })
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $dailyGoal = $user->daily_streak_goal ?? 20;
                $remaining = max(0, $dailyGoal - $user->daily_questions_solved);

                if ($remaining === 0) {
                    continue;
                }

                $template = self::MESSAGES[$time];

                if ($time === 'morning') {
                    $body = sprintf($template['body'], $remaining);
                } else {
                    $body = sprintf($template['body'], $remaining, $user->streak_days);
                }

                $user->notify(new PushNotification(
                    $template['title'],
                    $body,
                    '/dashboard'
                ));
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Streak push ({$time}) sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
```

- [ ] **Step 2: Register all three schedule entries**

In `routes/console.php`, add after the exam-reminder push entry:

```php

// Streak-Erinnerung Push (3x taeglich)
Schedule::command('push:send-streak morning')
    ->dailyAt('12:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-streak morning'))
    ->description('Streak-Push Mittag: Erinnerung an offene Fragen')
    ->emailOutputOnFailure($adminEmail);

Schedule::command('push:send-streak afternoon')
    ->dailyAt('17:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-streak afternoon'))
    ->description('Streak-Push Nachmittag: Erinnerung an offene Fragen')
    ->emailOutputOnFailure($adminEmail);

Schedule::command('push:send-streak evening')
    ->dailyAt('21:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-streak evening'))
    ->description('Streak-Push Abend: Letzte Chance fuer den Streak')
    ->emailOutputOnFailure($adminEmail);
```

- [ ] **Step 3: Verify commands are registered**

Run:
```bash
php artisan list | grep push:send-streak
```
Expected: `push:send-streak` listed.

- [ ] **Step 4: Commit**

```bash
git add app/Console/Commands/SendStreakPush.php routes/console.php
git commit -m "✨: Streak-Erinnerungen Push Command"
```

---

### Task 4: Lernsession-Start Push

**Files:**
- Modify: `app/Services/LernsessionService.php:252-279` (sendSessionStartedEmails method)

- [ ] **Step 1: Read the current sendSessionStartedEmails method**

Read `app/Services/LernsessionService.php` lines 252-279 to understand the current implementation. The method:
1. Gets the session from the instance
2. Builds a query for users with `email_consent = true`
3. If OV session, filters to OV members only
4. Sends `LernsessionStartedMail` to each user

- [ ] **Step 2: Add push dispatch after email sending**

In `app/Services/LernsessionService.php`, modify the `sendSessionStartedEmails` method. After the email sending loop (after line 275, before the outer catch), add the push dispatch block:

Find this code (the closing of the foreach and outer try):
```php
            }

            $users = $query->get();

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new LernsessionStartedMail($user, $session, $instance));
                } catch (\Exception $e) {
                    Log::warning("Lernsession-Start-Mail an {$user->email} fehlgeschlagen: {$e->getMessage()}");
                }
            }
        } catch (\Exception $e) {
```

Replace with:
```php
            }

            $users = $query->get();

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new LernsessionStartedMail($user, $session, $instance));
                } catch (\Exception $e) {
                    Log::warning("Lernsession-Start-Mail an {$user->email} fehlgeschlagen: {$e->getMessage()}");
                }
            }

            // Push an User mit Push-Subscription senden
            $pushQuery = User::whereHas('pushSubscriptions');
            if ($session->isOvSession()) {
                $pushQuery->whereHas('ortsverbände', function ($q) use ($session) {
                    $q->where('ortsverbände.id', $session->ortsverband_id);
                });
            }
            $pushUsers = $pushQuery->get();

            foreach ($pushUsers as $pushUser) {
                try {
                    $pushUser->notify(new \App\Notifications\PushNotification(
                        'Lernsession gestartet',
                        "{$session->title} — Jetzt mitmachen!",
                        '/lernsessions'
                    ));
                } catch (\Exception $e) {
                    Log::warning("Lernsession-Start-Push an User {$pushUser->id} fehlgeschlagen: {$e->getMessage()}");
                }
            }
        } catch (\Exception $e) {
```

- [ ] **Step 3: Verify no syntax errors**

Run:
```bash
php artisan route:list > /dev/null 2>&1 && echo "OK" || echo "FAIL"
```
Expected: `OK`

- [ ] **Step 4: Commit**

```bash
git add app/Services/LernsessionService.php
git commit -m "✨: Lernsession-Start Push senden"
```
