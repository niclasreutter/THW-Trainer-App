# Neue Cronjobs für THW-Trainer.de

## Übersicht der implementierten Cronjobs

### 1. 🚀 Performance-Optimierung
**Command:** `system:performance-optimization`
**Zeitplan:** Alle 6 Stunden (00:00, 06:00, 12:00, 18:00)
**Zweck:** Optimiert System-Performance durch Cache-Bereinigung und Statistiken-Updates

**Was es macht:**
- Bereinigt alle Laravel Caches
- Aktualisiert Top-Wrong-Questions Cache
- Optimiert Datenbank-Indizes
- Bereinigt alte Sessions
- Überwacht Speicherverbrauch

### 2. 📊 Tägliche Admin-Übersicht
**Command:** `admin:daily-report`
**Zeitplan:** Täglich um 08:00 Uhr
**Zweck:** Sendet tägliche Admin-Übersicht per E-Mail an niclasreutter@icloud.com

**Was es macht:**
- Erstellt detaillierten Tagesreport
- Benutzer-Statistiken (neu, aktiv, verifiziert)
- Lernaktivität (Fragen beantwortet, Erfolgsquote)
- Gamification-Daten (Punkte, Level, Streaks)
- Top 5 Benutzer
- System-Status (DB-Größe, Cache-Hit-Rate, Uptime)
- Letzte 24h Aktivitäten

### 3. 💾 Datenbank-Backup
**Command:** `database:backup`
**Zeitplan:** Jeden Sonntag um 02:00 Uhr
**Zweck:** Erstellt wöchentliches Backup der Datenbank

**Was es macht:**
- Erstellt mysqldump der gesamten Datenbank
- Komprimiert Backup-Dateien (gzip)
- Speichert Backups in `storage/app/backups/`
- Bereinigt alte Backups (behält nur 4 neueste)
- Loggt Backup-Status und -Größe

### 4. 🔧 System-Wartung
**Command:** `system:maintenance`
**Zeitplan:** Jeden Sonntag um 03:00 Uhr
**Zweck:** Führt System-Wartung und Speicher-Optimierung durch

**Was es macht:**
- Bereinigt Log-Dateien (älter als 30 Tage)
- Leert alle Caches
- Löscht temporäre Dateien
- Bereinigt Datenbank (Sessions, Failed Jobs)
- Optimiert Datenbank-Tabellen
- Analysiert Speicherplatz-Verbrauch
- Bereinigt Queue-Jobs

## Plesk Cronjob Setup

### Performance-Optimierung (Prod)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de && php cronjob-performance-optimization-prod.php
Minute: 0
Stunde: 0,6,12,18
Tag: *
Monat: *
Wochentag: *
```

### Performance-Optimierung (Test)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de && php cronjob-performance-optimization-test.php
Minute: 0
Stunde: 0,6,12,18
Tag: *
Monat: *
Wochentag: *
```

### Admin-Report (Prod)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de && php cronjob-admin-report-prod.php
Minute: 0
Stunde: 8
Tag: *
Monat: *
Wochentag: *
```

### Admin-Report (Test)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de && php cronjob-admin-report-test.php
Minute: 0
Stunde: 8
Tag: *
Monat: *
Wochentag: *
```

### Datenbank-Backup (Prod)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de && php cronjob-database-backup-prod.php
Minute: 0
Stunde: 2
Tag: *
Monat: *
Wochentag: 0
```

### Datenbank-Backup (Test)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de && php cronjob-database-backup-test.php
Minute: 0
Stunde: 2
Tag: *
Monat: *
Wochentag: 0
```

### System-Wartung (Prod)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de && php cronjob-system-maintenance-prod.php
Minute: 0
Stunde: 3
Tag: *
Monat: *
Wochentag: 0
```

### System-Wartung (Test)
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de && php cronjob-system-maintenance-test.php
Minute: 0
Stunde: 3
Tag: *
Monat: *
Wochentag: 0
```

## Überwachung und Logs

### Log-Dateien
- `storage/logs/performance-optimization.log` - Performance-Optimierung
- `storage/logs/admin-report.log` - Admin-Reports
- `storage/logs/database-backup.log` - Backup-Status
- `storage/logs/system-maintenance.log` - System-Wartung

### Test-Logs
- `storage/logs/performance-optimization-test.log`
- `storage/logs/admin-report-test.log`
- `storage/logs/database-backup-test.log`
- `storage/logs/system-maintenance-test.log`

### Backup-Verzeichnis
- `storage/app/backups/` - Datenbank-Backups (automatische Bereinigung)

## Commands testen

### Manuell ausführen
```bash
# Performance-Optimierung
php artisan system:performance-optimization

# Admin-Report
php artisan admin:daily-report

# Datenbank-Backup
php artisan database:backup

# System-Wartung
php artisan system:maintenance
```

### Scheduler-Status prüfen
```bash
php artisan schedule:list
php artisan schedule:run
```

## Wichtige Hinweise

1. **Backup-Verzeichnis:** Stelle sicher, dass `storage/app/backups/` schreibbar ist
2. **mysqldump:** Muss auf dem Server verfügbar sein
3. **E-Mail-Konfiguration:** Admin-Reports benötigen funktionierende E-Mail-Einstellungen
4. **Speicherplatz:** Backups können viel Speicher verbrauchen
5. **Zeitzone:** Alle Cronjobs laufen in Europe/Berlin

## Fehlerbehebung

### Backup-Probleme
```bash
# Prüfe mysqldump
which mysqldump

# Teste Backup manuell
php artisan database:backup
```

### E-Mail-Probleme
```bash
# Teste E-Mail-Konfiguration
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### Performance-Probleme
```bash
# Prüfe Cache-Status
php artisan cache:clear
php artisan config:cache
```

## Monitoring

### Logs überwachen
```bash
# Live-Logs anzeigen
tail -f storage/logs/performance-optimization.log
tail -f storage/logs/admin-report.log
tail -f storage/logs/database-backup.log
tail -f storage/logs/system-maintenance.log
```

### Speicherplatz prüfen
```bash
# Backup-Verzeichnis
du -sh storage/app/backups/

# Logs-Verzeichnis
du -sh storage/logs/
```

## Support

Bei Problemen:
1. Prüfe die entsprechenden Log-Dateien
2. Teste Commands manuell
3. Überprüfe Server-Berechtigungen
4. Kontaktiere den Hosting-Provider bei Server-spezifischen Problemen
