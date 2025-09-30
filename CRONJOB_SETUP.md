# Cronjob-Setup für THW-Trainer.de

## Automatische Account-Bereinigung

Dieses System benachrichtigt und löscht automatisch unbestätigte Accounts:

- **Tag 7**: Warnung per E-Mail (Account wird in 2 Tagen gelöscht)
- **Tag 9**: Account wird automatisch gelöscht

## Plesk Cronjob einrichten

### 1. In Plesk anmelden
- Gehe zu deinem Hosting-Panel
- Wähle deine Domain aus

### 2. Cronjob erstellen
- Gehe zu **"Cronjobs"** oder **"Geplante Aufgaben"**
- Klicke auf **"Neuer Cronjob"**

### 3. Cronjob konfigurieren

**Befehl:**
```bash
cd /var/www/vhosts/thw-trainer.de/httpdocs && php artisan schedule:run
```

**Ausführungszeit:**
- **Minute**: `*`
- **Stunde**: `*` 
- **Tag**: `*`
- **Monat**: `*`
- **Wochentag**: `*`

Das bedeutet: **Jede Minute** wird der Laravel Scheduler ausgeführt, der dann entscheidet, welche geplanten Aufgaben zu dieser Zeit laufen sollen.

### 4. Alternative: Direkter Command

Falls der Scheduler nicht funktioniert, kannst du auch direkt den Command ausführen:

**Befehl:**
```bash
cd /var/www/vhosts/thw-trainer.de/httpdocs && php artisan accounts:cleanup-unconfirmed
```

**Ausführungszeit:**
- **Minute**: `0`
- **Stunde**: `9`
- **Tag**: `*`
- **Monat**: `*`
- **Wochentag**: `*`

Das bedeutet: **Täglich um 09:00 Uhr**

## Überprüfung

### 1. Command testen
```bash
# Auf dem Server ausführen:
cd /var/www/vhosts/thw-trainer.de/httpdocs
php artisan accounts:cleanup-unconfirmed
```

### 2. Logs prüfen
```bash
# Laravel Logs:
tail -f storage/logs/laravel.log

# Cronjob Logs:
tail -f /var/log/cron.log
```

## Funktionsweise

### Timeline:
- **Tag 0**: User registriert sich
- **Tag 7**: Warnung wird gesendet (falls E-Mail noch nicht bestätigt)
- **Tag 9**: Account wird gelöscht (falls E-Mail noch nicht bestätigt)

### E-Mail-Templates:
- **Warnung**: `resources/views/emails/account-deletion-warning.blade.php`
- **Löschung**: `resources/views/emails/account-deleted.blade.php`

### Database:
- Neue Spalte `deletion_warning_sent_at` in `users` Tabelle
- Verhindert mehrfache Warnungen

## Troubleshooting

### Problem: Command läuft nicht
```bash
# Prüfe PHP-Pfad:
which php

# Prüfe Laravel-Pfad:
ls -la /var/www/vhosts/thw-trainer.de/httpdocs/artisan

# Prüfe Berechtigungen:
chmod +x /var/www/vhosts/thw-trainer.de/httpdocs/artisan
```

### Problem: E-Mails werden nicht gesendet
```bash
# Teste E-Mail-Konfiguration:
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('deine@email.de')->subject('Test'); });
```

### Problem: Scheduler läuft nicht
```bash
# Prüfe ob Scheduler aktiv ist:
php artisan schedule:list

# Teste Scheduler manuell:
php artisan schedule:run
```

## Wichtige Hinweise

1. **Backup**: Stelle sicher, dass regelmäßige Backups laufen
2. **Logs**: Überwache die Logs auf Fehler
3. **E-Mail-Konfiguration**: Teste die E-Mail-Versendung
4. **Zeitzone**: Cronjob läuft in Europe/Berlin Zeitzone

## Support

Bei Problemen:
1. Prüfe die Laravel Logs
2. Teste Commands manuell
3. Überprüfe Server-Logs
4. Kontaktiere den Hosting-Provider
