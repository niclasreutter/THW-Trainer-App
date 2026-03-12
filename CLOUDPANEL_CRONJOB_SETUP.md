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

## 🚀 Empfohlen: Einzelne Cronjobs in CloudPanel

Da nicht alle Cronjobs im Laravel Scheduler registriert sind (viele nutzen Inline-Logik),
ist es am einfachsten, jeden Cronjob einzeln in CloudPanel anzulegen.

### DEV-Umgebung (dev.thw-trainer.de)

In CloudPanel: **Sites → dev.thw-trainer.de → Cron Jobs → New Cron Job**

| # | Name | Minute | Hour | Day | Month | Weekday | Command |
|---|------|--------|------|-----|-------|---------|---------|
| 1 | Daily Reset | `1` | `0` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-daily-reset-test.php` |
| 2 | League Weekly | `0` | `0` | `*` | `*` | `1` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-league-weekly-test.php` |
| 3 | User Count | `15` | `0` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-user-count-test.php` |
| 4 | Spaced Repetition | `0` | `8` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-spaced-repetition-reminders-test.php` |
| 5 | Admin Report | `0` | `8` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-admin-report-test.php` |
| 6 | Cleanup | `0` | `9` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-cleanup-test.php` |
| 7 | Inactive Reminders | `0` | `10` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-inactive-reminders-test.php` |
| 8 | Exam Feedback | `0` | `10` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-exam-feedback-test.php` |
| 9 | Exam Good Luck | `0` | `17` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-exam-goodluck-test.php` |
| 10 | Exam Reminders | `0` | `18` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-exam-reminders-test.php` |
| 11 | Streak Reminders | `0` | `18` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-streak-reminders-test.php` |
| 12 | Lernsession | `*/5` | `*` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-lernsession-test.php` |
| 13 | Performance | `0` | `0,6,12,18` | `*` | `*` | `*` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-performance-optimization-test.php` |
| 14 | DB Backup | `0` | `2` | `*` | `*` | `0` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-database-backup-test.php` |
| 15 | System Maintenance | `0` | `3` | `*` | `*` | `0` | `/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-system-maintenance-test.php` |

### PROD-Umgebung (thw-trainer.de)

> ⚠️ Passe den Site-User und Domain-Pfad an deine Prod-Konfiguration an!

Ersetze in allen Commands:
- `thw-trainer-dev` → `thw-trainer-prod` (oder wie dein Prod-Site-User heißt)
- `dev.thw-trainer.de` → `thw-trainer.de`
- `*-test.php` → `*-prod.php`

**Beispiel:**
```
/usr/bin/php8.5 /home/thw-trainer-prod/htdocs/thw-trainer.de/cronjob-daily-reset-prod.php
```

---

## 🔧 Testen

### Einzelnen Cronjob manuell testen (per SSH)

```bash
# Als Site-User einloggen
ssh thw-trainer-dev@dev.thw-trainer.de

# Oder via CloudPanel → SSH/FTP → Terminal

# Dann Cronjob testen:
/usr/bin/php8.5 /home/thw-trainer-dev/htdocs/dev.thw-trainer.de/cronjob-cleanup-test.php
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

## 📋 Übersicht: Zeitplan aller Cronjobs

```
┌─────── Minute (0-59)
│ ┌───── Stunde (0-23)
│ │ ┌─── Tag (1-31)
│ │ │ ┌─ Monat (1-12)
│ │ │ │ ┌ Wochentag (0=So, 1=Mo, ..., 6=Sa)
│ │ │ │ │

1  0  * * *   Daily Reset (Streak-Check)         → 00:01 täglich
0  0  * * 1   League Weekly (Auf-/Abstieg)        → 00:00 Montags
15 0  * * *   User Count                          → 00:15 täglich
0  8  * * *   Spaced Repetition Reminders         → 08:00 täglich
0  8  * * *   Admin Report                        → 08:00 täglich
0  9  * * *   Cleanup (unbestätigte Accounts)     → 09:00 täglich
0  10 * * *   Inactive Reminders                  → 10:00 täglich
0  10 * * *   Exam Feedback                       → 10:00 täglich
0  17 * * *   Exam Good Luck                      → 17:00 täglich
0  18 * * *   Exam Reminders                      → 18:00 täglich
0  18 * * *   Streak Reminders                    → 18:00 täglich
*/5 * * * *   Lernsession Lifecycle               → alle 5 Minuten
0  0,6,12,18 * * *  Performance Optimization      → alle 6 Stunden
0  2  * * 0   Database Backup                     → 02:00 Sonntags
0  3  * * 0   System Maintenance                  → 03:00 Sonntags
```

---

## 💡 Hinweise

1. **Die PHP-Scripts funktionieren unverändert** — Sie bootstrappen Laravel selbst und finden das Verzeichnis über `__DIR__`.
2. **PHP-Version prüfen** — Im Screenshot steht `php8.5`. Falls dein Server eine andere Version hat, passe den Pfad an (`php8.3`, `php8.4`, etc.).
3. **Keine `schedule:run` nötig** — Da die meisten Cronjobs Inline-Logik verwenden und nicht im Laravel Scheduler registriert sind, ist der direkte PHP-Aufruf zuverlässiger.
4. **Reihenfolge beachten** — League Weekly (00:00) muss VOR Daily Reset (00:01) laufen!
5. **CloudPanel Template** — Wähle zunächst "Every minute" als Template, dann passe die Felder manuell an.

---

## 🔄 Deployment

Für CloudPanel muss auch das Deploy-Script angepasst werden. Statt `deploy-plesk.sh` verwende `deploy-cloudpanel.sh`:

```bash
#!/bin/bash
# CloudPanel Deployment Script

cd /home/thw-trainer-dev/htdocs/dev.thw-trainer.de

echo "=== Starting Deployment ==="

/usr/bin/php8.5 /usr/bin/composer install --no-dev --optimize-autoloader

/usr/bin/php8.5 artisan config:clear
/usr/bin/php8.5 artisan cache:clear
/usr/bin/php8.5 artisan view:clear
/usr/bin/php8.5 artisan route:clear

/usr/bin/php8.5 artisan config:cache
/usr/bin/php8.5 artisan route:cache
/usr/bin/php8.5 artisan view:cache

/usr/bin/php8.5 artisan migrate --force
/usr/bin/php8.5 artisan storage:link

echo "=== Deployment Complete ==="
```

---

*Letzte Aktualisierung: 12. März 2026*
