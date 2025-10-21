# Cronjob Fix - Änderungen für neue Verzeichnisstruktur

## Problem
Die Cronjob-Scripts konnten Laravel nicht finden, weil sie hardcodierte Pfade hatten.

## Lösung
Alle Scripts verwenden jetzt `__DIR__` statt hardcodierte Pfade.

## Geänderte Scripts

### Neue Scripts (mit Laravel Bootstrap Fix)
- `cronjob-admin-report-prod.php` ✅
- `cronjob-admin-report-test.php` ✅
- `cronjob-performance-optimization-prod.php` ✅
- `cronjob-performance-optimization-test.php` ✅
- `cronjob-database-backup-prod.php` ✅
- `cronjob-database-backup-test.php` ✅
- `cronjob-system-maintenance-prod.php` ✅
- `cronjob-system-maintenance-test.php` ✅

### Alte Scripts (Pfad-Fix)
- `cronjob-daily-reset-prod.php` ✅
- `cronjob-daily-reset-test.php` ✅
- `cronjob-cleanup-prod.php` ✅
- `cronjob-cleanup-test.php` ✅
- `cronjob-streak-reminders-prod.php` ✅
- `cronjob-streak-reminders-test.php` ✅
- `cronjob-inactive-reminders-prod.php` ✅
- `cronjob-inactive-reminders-test.php` ✅

## Was wurde geändert?

### Bei neuen Scripts (z.B. admin-report):
**Vorher:**
```php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
```

**Nachher:**
```php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot Laravel Application
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
```

### Bei alten Scripts (z.B. daily-reset):
**Vorher:**
```php
$laravelPath = '/var/www/vhosts/web22867.bero-web.de/thw-trainer.de';
```

**Nachher:**
```php
$laravelPath = __DIR__;
```

## Plesk Cronjob Konfiguration

### PRODUKTION (THW-Trainer)
Scripts liegen in: `/var/www/vhosts/web22867.bero-web.de/THW-Trainer/`

Beispiel-Pfad in Plesk:
```
THW-Trainer/cronjob-admin-report-prod.php
THW-Trainer/cronjob-performance-optimization-prod.php
THW-Trainer/cronjob-database-backup-prod.php
...
```

### TEST (THW-Trainer_dev)
Scripts liegen in: `/var/www/vhosts/web22867.bero-web.de/THW-Trainer_dev/`

Beispiel-Pfad in Plesk:
```
THW-Trainer_dev/cronjob-admin-report-test.php
THW-Trainer_dev/cronjob-performance-optimization-test.php
THW-Trainer_dev/cronjob-database-backup-test.php
...
```

## Nächste Schritte

1. **Dateien hochladen**: Alle geänderten Cronjob-Scripts auf den Server laden
2. **Plesk Cronjobs aktualisieren**: 
   - PROD: Pfade auf `THW-Trainer/cronjob-*.php` ändern
   - TEST: Pfade auf `THW-Trainer_dev/cronjob-*.php` ändern
3. **Test**: Einen Cronjob manuell in Plesk ausführen
4. **Logs prüfen**: `storage/logs/` auf Fehler checken
5. **Debug-Script löschen**: `public/cronjob-debug.php` vom Server entfernen

## Debug-Tool
Falls noch Probleme auftreten:
```
https://deine-domain.de/cronjob-debug.php?token=debug2025
```

**WICHTIG**: Nach dem Debuggen das Script `public/cronjob-debug.php` wieder löschen!

## Log-Dateien Locations
- Admin Report: `storage/logs/admin-report.log`
- Performance: `storage/logs/performance-optimization.log`
- Database Backup: `storage/logs/database-backup.log`
- System Maintenance: `storage/logs/system-maintenance.log`
- Daily Reset: Siehe Artisan Command Output
- Streak Reminders: Siehe Artisan Command Output
- Inactive Reminders: Siehe Artisan Command Output
- Cleanup: Siehe Artisan Command Output

Viel Erfolg! 🚀
