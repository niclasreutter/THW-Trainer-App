# CloudPanel Cronjob-Setup für THW-Trainer.de

> Umstellung von Plesk auf CloudPanel — Stand: März 2026

## Unterschiede Plesk → CloudPanel

| | Plesk | CloudPanel |
|---|---|---|
| **PHP-Pfad** | `/opt/plesk/php/8.3/bin/php` | `/usr/bin/php8.5` |
| **Site-Pfad (Dev)** | `/var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de` | `/home/thw-trainer-dev/htdocs/dev.thw-trainer.de` |
| **Site-Pfad (Prod)** | `/var/www/vhosts/thw-trainer.de/httpdocs` | `/home/thw-trainer-prod/htdocs/thw-trainer.de` |
| **Cronjob-UI** | Geplante Aufgaben → Befehl | Sites → Cron Jobs → Command |

---

## Setup: NUR 1 Cronjob nötig!

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

> Passe den Site-User (`thw-trainer-prod`) und PHP-Pfad an deine Server-Konfiguration an!

---

## Alle 15 Jobs im Laravel Scheduler

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
0  8  * * *   admin:daily-report                    → 08:00 Admin-Tagesbericht [nur Prod]
0  9  * * *   accounts:cleanup-unconfirmed          → 09:00 Unbestätigte Accounts bereinigen [nur Prod]
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
0  2  * * 0   database:backup                       → So 02:00 Datenbank-Backup [nur Prod]
0  3  * * 0   system:maintenance                    → So 03:00 System-Wartung
```

### Artisan Commands Übersicht

| # | Command | Datei | Features |
|---|---------|-------|----------|
| 1 | `gamification:daily-reset` | `DailyReset.php` | withoutOverlapping(15) |
| 2 | `user-count:record` | `RecordUserCount.php` | — |
| 3 | `app:send-spaced-repetition-reminders` | `SendSpacedRepetitionReminders.php` | — |
| 4 | `admin:daily-report` | `DailyAdminReport.php` | nur Production |
| 5 | `accounts:cleanup-unconfirmed` | `CleanupUnconfirmedAccounts.php` | nur Production |
| 6 | `app:send-inactive-reminders` | `SendInactiveReminders.php` | — |
| 7 | `exam:send-feedback-requests` | `SendExamFeedbackRequests.php` | — |
| 8 | `exam:send-goodluck` | `SendExamGoodLuck.php` | — |
| 9 | `exam:send-reminders` | `SendExamReminders.php` | — |
| 10 | `gamification:send-streak-reminders` | `SendStreakReminders.php` | — |
| 11 | `lernsession:lifecycle` | `LernsessionLifecycle.php` | withoutOverlapping(10), runInBackground |
| 12 | `system:performance-optimization` | `PerformanceOptimization.php` | withoutOverlapping(15), runInBackground |
| 13 | `league:process-weekly` | `ProcessWeeklyLeagues.php` | withoutOverlapping(15) |
| 14 | `database:backup` | `DatabaseBackup.php` | nur Production, withoutOverlapping(30), runInBackground |
| 15 | `system:maintenance` | `SystemMaintenance.php` | withoutOverlapping(20), runInBackground |

---

## Scheduler-Features

Alle Jobs nutzen folgende Features:

| Feature | Beschreibung |
|---------|-------------|
| **timezone** | `Europe/Berlin` für alle Jobs |
| **emailOutputOnFailure** | Fehler-Mails an `protokolle@thw-trainer.de` |
| **appendOutputTo** | Alle Ausgaben in `storage/logs/scheduler.log` |
| **onFailure** | Zusätzlich `Log::error()` für strukturiertes Logging |
| **withoutOverlapping** | Verhindert doppelte Läufe bei langsamen Jobs |
| **runInBackground** | Lange Jobs blockieren den Scheduler nicht |
| **environments** | Bestimmte Jobs laufen nur in Production |

---

## Testen

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

### Scheduler-Log prüfen

```bash
# Scheduler-spezifisches Log
tail -f /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/storage/logs/scheduler.log
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

## Queue Worker (Supervisor)

Alle 16 Mail-Klassen der App nutzen den `Queueable`-Trait. Damit Mails zuverlässig und nicht-blockend versandt werden, muss ein Queue Worker dauerhaft laufen.

### Supervisor installieren

```bash
sudo apt install supervisor
```

### Config einrichten

Eine fertige Konfigurationsvorlage liegt unter `supervisor/thw-trainer-worker.conf`.

**1. Platzhalter ersetzen:**

| Platzhalter | DEV | PROD |
|-------------|-----|------|
| `SITE_USER` | `thw-trainer-dev` | `thw-trainer-prod` |
| `DOMAIN` | `dev.thw-trainer.de` | `thw-trainer.de` |

**2. Config kopieren:**

```bash
# Platzhalter ersetzen und nach /etc/supervisor/conf.d/ kopieren
sudo sed \
  -e 's/SITE_USER/thw-trainer-prod/g' \
  -e 's/DOMAIN/thw-trainer.de/g' \
  supervisor/thw-trainer-worker.conf \
  | sudo tee /etc/supervisor/conf.d/thw-trainer-worker.conf
```

**3. Supervisor neu laden und Worker starten:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start thw-trainer-worker
```

**4. Status prüfen:**

```bash
sudo supervisorctl status thw-trainer-worker
# Erwartete Ausgabe: thw-trainer-worker    RUNNING   pid 1234, uptime 0:00:05
```

### Worker-Logs prüfen

```bash
# DEV
tail -f /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/storage/logs/worker.log

# PROD
tail -f /home/thw-trainer-prod/htdocs/thw-trainer.de/storage/logs/worker.log
```

### Worker nach Deployment neu starten

Nach einem Deployment muss der Worker neu gestartet werden, damit er neuen Code aufnimmt:

```bash
sudo supervisorctl restart thw-trainer-worker
```

> Dies ist bereits im `deploy-cloudpanel.sh` integriert.

---

## Deployment

Für CloudPanel statt `deploy-plesk.sh` das `deploy-cloudpanel.sh` verwenden.

---

## Hinweise

1. **Nur 1 Cronjob in CloudPanel** — `schedule:run` jede Minute. Laravel entscheidet selbst welche Jobs fällig sind.
2. **PHP-Version prüfen** — Im Screenshot steht `php8.5`. Falls andere Version, Pfad anpassen.
3. **Reihenfolge** — League Weekly (Mo 00:00) läuft automatisch VOR Daily Reset (00:01).
4. **Legacy-Dateien** — Die `cronjob-*.php` Dateien wurden entfernt. Alles läuft über den Laravel Scheduler.
5. **Timezone** — Alle Jobs sind auf `Europe/Berlin` konfiguriert.
6. **Overlap-Schutz** — Kritische Jobs nutzen `withoutOverlapping()` um doppelte Läufe zu verhindern.
7. **Environment** — `database:backup`, `admin:daily-report` und `accounts:cleanup-unconfirmed` laufen nur in Production.

---

*Letzte Aktualisierung: 13. März 2026*
