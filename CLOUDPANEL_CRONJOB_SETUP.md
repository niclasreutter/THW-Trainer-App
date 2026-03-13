# ☁️ CloudPanel Cronjob-Setup für THW-Trainer.de

> Umstellung von Plesk auf CloudPanel — Stand: März 2026

## Unterschiede Plesk → CloudPanel

| | Plesk | CloudPanel |
|---|---|---|
| **PHP-Pfad** | `/opt/plesk/php/8.3/bin/php` | `/usr/bin/php8.5` |
| **Site-Pfad (Dev)** | `/var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de` | `/home/thw-trainer-dev/htdocs/dev.thw-trainer.de` |
| **Site-Pfad (Prod)** | `/var/www/vhosts/thw-trainer.de/httpdocs` | `/home/thw-trainer-prod/htdocs/thw-trainer.de` |
| **Cronjob-UI** | Geplante Aufgaben → Befehl | Sites → Cron Jobs → Command |

---

## 🚀 Setup: NUR 1 Cronjob nötig!

Alle 15 Jobs sind im Laravel Scheduler registriert (`routes/console.php`).
Du brauchst **nur einen einzigen Cronjob** in CloudPanel:

### DEV-Umgebung (dev.thw-trainer.de)

In CloudPanel: **Sites → dev.thw-trainer.de → Cron Jobs → New Cron Job**

| Feld | Wert |
|------|------|
| **Template** | Every minute |
| **Minute** | `*` |
| **Hour** | `*` |
| **Day** | `*` |
| **Month** | `*` |
| **Weekday** | `*` |
| **Command** | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/artisan schedule:run` |

### PROD-Umgebung (thw-trainer.de)

| Feld | Wert |
|------|------|
| **Template** | Every minute |
| **Minute** | `*` |
| **Hour** | `*` |
| **Day** | `*` |
| **Month** | `*` |
| **Weekday** | `*` |
| **Command** | `/usr/bin/php8.5 /home/thw-trainer-prod/htdocs/thw-trainer.de/artisan schedule:run` |

> ⚠️ Passe den Site-User (`thw-trainer-prod`) und PHP-Pfad an deine Server-Konfiguration an!

---

## 📋 Alle 15 Jobs im Laravel Scheduler

Der Scheduler (`routes/console.php`) verwaltet diese Jobs automatisch:

```
┌─────── Minute (0-59)
│ ┌───── Stunde (0-23)
│ │ ┌─── Tag (1-31)
│ │ │ ┌─ Monat (1-12)
│ │ │ │ ┌ Wochentag (0=So, 1=Mo, ..., 6=Sa)
│ │ │ │ │

TÄGLICHE JOBS:
1  0  * * *   gamification:daily-reset              → 00:01 Streak + Daily Questions Reset
15 0  * * *   user-count:record                     → 00:15 User-Anzahl aufzeichnen
0  8  * * *   app:send-spaced-repetition-reminders  → 08:00 Spaced Repetition Erinnerungen
0  8  * * *   admin:daily-report                    → 08:00 Admin-Tagesbericht
0  9  * * *   accounts:cleanup-unconfirmed          → 09:00 Unbestätigte Accounts bereinigen
0  10 * * *   app:send-inactive-reminders           → 10:00 Inaktive User Erinnerungen
0  10 * * *   exam:send-feedback-requests           → 10:00 Prüfungs-Feedback-Anfragen
0  17 * * *   exam:send-goodluck                    → 17:00 Prüfungs-Viel-Erfolg-Mail
0  18 * * *   exam:send-reminders                   → 18:00 Tagespensum-Erinnerungen
0  18 * * *   gamification:send-streak-reminders    → 18:00 Streak-Erinnerungen

HÄUFIGE JOBS:
*/5 * * * *   lernsession:lifecycle                 → alle 5 Min Lernsessions verwalten
0  0,6,12,18  system:performance-optimization       → alle 6h Performance-Optimierung

WÖCHENTLICHE JOBS:
0  0  * * 1   league:process-weekly                 → Mo 00:00 Liga Auf-/Abstiege
0  2  * * 0   database:backup                       → So 02:00 Datenbank-Backup
0  3  * * 0   system:maintenance                    → So 03:00 System-Wartung
```

### Artisan Commands Übersicht

| # | Command | Datei | Neu? |
|---|---------|-------|------|
| 1 | `gamification:daily-reset` | `DailyReset.php` | ✅ NEU |
| 2 | `user-count:record` | `RecordUserCount.php` | bestehend |
| 3 | `app:send-spaced-repetition-reminders` | `SendSpacedRepetitionReminders.php` | bestehend |
| 4 | `admin:daily-report` | `DailyAdminReport.php` | bestehend |
| 5 | `accounts:cleanup-unconfirmed` | `CleanupUnconfirmedAccounts.php` | bestehend |
| 6 | `app:send-inactive-reminders` | `SendInactiveReminders.php` | bestehend |
| 7 | `exam:send-feedback-requests` | `SendExamFeedbackRequests.php` | bestehend |
| 8 | `exam:send-goodluck` | `SendExamGoodLuck.php` | bestehend |
| 9 | `exam:send-reminders` | `SendExamReminders.php` | ✅ NEU |
| 10 | `gamification:send-streak-reminders` | `SendStreakReminders.php` | Signature gefixt |
| 11 | `lernsession:lifecycle` | `LernsessionLifecycle.php` | ✅ NEU |
| 12 | `system:performance-optimization` | `PerformanceOptimization.php` | bestehend |
| 13 | `league:process-weekly` | `ProcessWeeklyLeagues.php` | ✅ NEU |
| 14 | `database:backup` | `DatabaseBackup.php` | bestehend |
| 15 | `system:maintenance` | `SystemMaintenance.php` | bestehend |

---

## 🔧 Testen

### Schedule überprüfen

```bash
# Alle geplanten Jobs anzeigen
/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/artisan schedule:list

# Scheduler manuell ausführen (führt fällige Jobs aus)
/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/artisan schedule:run

# Einzelnen Command testen
/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/artisan gamification:daily-reset
/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/artisan lernsession:lifecycle
/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/artisan league:process-weekly
```

### PHP-Version prüfen

```bash
/usr/bin/php8.5 -v
# Falls 8.5 nicht vorhanden, prüfe:
ls /usr/bin/php*
```

### Logs prüfen

```bash
# Laravel Logs
tail -f /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/storage/logs/laravel.log

# CloudPanel Cron Logs
tail -f /var/log/syslog | grep CRON
```

---

## 🔄 Deployment

Für CloudPanel statt `deploy-plesk.sh` das `deploy-cloudpanel.sh` verwenden.

---

## 💡 Hinweise

1. **Nur 1 Cronjob in CloudPanel** — `schedule:run` jede Minute. Laravel entscheidet selbst welche Jobs fällig sind.
2. **PHP-Version prüfen** — Im Screenshot steht `php8.5`. Falls andere Version, Pfad anpassen.
3. **Reihenfolge** — League Weekly (Mo 00:00) läuft automatisch VOR Daily Reset (00:01).
4. **Alte PHP-Scripts** — Die `cronjob-*.php` Dateien können perspektivisch entfernt werden, sobald alles über den Scheduler läuft.
5. **Timezone** — Alle Jobs sind auf `Europe/Berlin` konfiguriert.

---

*Letzte Aktualisierung: 12. März 2026*
