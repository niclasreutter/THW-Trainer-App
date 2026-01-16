# 🔥 Streak-Reset Cron-Job Fix

## 🔴 Problem

User haben gemeldet, dass ihre Streaks zurückgesetzt wurden, obwohl sie gestern gelernt haben.

### Ursachen

**1. Inkonsistente Datenspalten**
- `GamificationService` verwendet `last_activity_date` für Streak-Updates
- Alter Cron-Job verwendete `daily_questions_date` für Streak-Reset
- Diese beiden Spalten waren nicht synchronisiert!

**2. Falsche Timing-Logik**
- Alter Cron lief um **23:00 Uhr**
- Prüfte ob `daily_questions_date != heute`
- User hatten keine Kulanzzeit bis Mitternacht

**3. Zu früher Reset**
```
Beispiel (ALTER Cron):
- User lernt am Montag um 14:00 Uhr
- Dienstag um 23:00 Uhr läuft der Cron
- daily_questions_date (Montag) != heute (Dienstag)
- Streak wird zurückgesetzt! ❌
- User hatte noch 1 Stunde Zeit bis Mitternacht!
```

## ✅ Lösung

### Neue Logik

**1. Konsistente Datenspalte**
- Verwendet jetzt `last_activity_date` (wie GamificationService)

**2. Korrektes Timing**
- Läuft um **00:01 Uhr** (nach Mitternacht)
- Prüft ob User **gestern** gelernt hat
- Wenn `last_activity_date < gestern` → Streak wird zurückgesetzt

**3. Kulanzzeit bis Mitternacht**
```
Beispiel (NEUER Cron):
- User lernt am Montag um 14:00 Uhr → last_activity_date = Montag
- Dienstag um 00:01 Uhr läuft der Cron
- Gestern = Montag
- last_activity_date (Montag) >= Gestern (Montag) → Streak bleibt! ✓
- User hat bis Dienstag Mitternacht Zeit um zu lernen

- Mittwoch um 00:01 Uhr läuft der Cron
- Gestern = Dienstag
- last_activity_date (Montag) < Gestern (Dienstag) → Streak wird zurückgesetzt ✓
```

## 📋 Geänderte Dateien

- `cronjob-daily-reset-prod.php` - Komplett neu geschrieben
- `cronjob-daily-reset-test.php` - Komplett neu geschrieben (mit Debug-Output)

## ⚙️ Plesk Cronjob Konfiguration

### ⚠️ WICHTIG: Zeitpunkt ändern!

Die Cronjobs müssen jetzt um **00:01 Uhr** (nach Mitternacht) laufen, nicht mehr um 23:00 Uhr!

### Produktion

```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de && php cronjob-daily-reset-prod.php
Minute: 1
Stunde: 0
Tag: *
Monat: *
Wochentag: *
```

### Test

```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de && php cronjob-daily-reset-test.php
Minute: 1
Stunde: 0
Tag: *
Monat: *
Wochentag: *
```

## 🧪 Testen

### Test-Script ausführen

```bash
# Auf dem Server
cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de
php cronjob-daily-reset-test.php
```

### Erwartete Ausgabe

```
[2026-01-16 00:01:00] Laravel-Verzeichnis gefunden: /path/to/app (TEST)
[2026-01-16 00:01:00] Starte tägliche Streak-Reset-Prüfung (TEST)...
[2026-01-16 00:01:00] Heute: 2026-01-16
[2026-01-16 00:01:00] Gestern: 2026-01-15

[2026-01-16 00:01:00] DEBUG: Alle Benutzer mit Streak > 0: 5
[2026-01-16 00:01:00] DEBUG: Max Mustermann
  → Streak: 7 Tage
  → Letzte Aktivität: 2026-01-15
  → Wird zurückgesetzt: NEIN

[2026-01-16 00:01:00] DEBUG: Erika Musterfrau
  → Streak: 3 Tage
  → Letzte Aktivität: 2026-01-13
  → Wird zurückgesetzt: JA

[2026-01-16 00:01:00] Gefunden: 1 Benutzer für Streak-Reset.
[2026-01-16 00:01:01] Streak zurückgesetzt: Erika Musterfrau (erika@example.com)
  → Streak: 3 → 0 Tage
  → Daily Questions: 5 → 0
  → Letzte Aktivität: 2026-01-13

[2026-01-16 00:01:01] Tägliche Streak-Reset-Prüfung abgeschlossen!
[2026-01-16 00:01:01] Streaks zurückgesetzt: 1
[2026-01-16 00:01:01] Fehler: 0
[2026-01-16 00:01:01] Script beendet.
```

## 📊 Logik-Vergleich

| Szenario | Alter Cron (23:00) | Neuer Cron (00:01) |
|----------|-------------------|-------------------|
| User lernt Montag, Cron läuft Dienstag 23:00 | ❌ Streak zurückgesetzt (noch 1h Zeit!) | - |
| User lernt Montag, Cron läuft Dienstag 00:01 | - | ✅ Streak bleibt (hat bis Mitternacht Zeit) |
| User lernt Montag, nicht Dienstag, Cron läuft Mittwoch 00:01 | - | ✅ Streak wird zurückgesetzt (1 Tag Pause) |
| User lernt Montag + Dienstag, Cron läuft Mittwoch 00:01 | - | ✅ Streak bleibt |

## 🚀 Deployment

### 1. Dateien auf Server hochladen

```bash
# Per Git
git pull origin main

# Oder manuell
scp cronjob-daily-reset-*.php user@server:/path/to/app/
```

### 2. Plesk Cronjobs aktualisieren

1. Öffne Plesk → Domains → thw-trainer.de → Geplante Aufgaben
2. Finde "Tägliche Streak-Reset" Cronjob
3. Ändere Zeit von **23:00** auf **00:01** (Minute: 1, Stunde: 0)
4. Speichern

Wiederhole für Test-Umgebung (test.thw-trainer.de)

### 3. Test durchführen

```bash
# Test-Script manuell ausführen
php cronjob-daily-reset-test.php

# Logs prüfen
tail -f storage/logs/laravel.log
```

### 4. Überwachen

- Ersten automatischen Lauf abwarten (nächste Nacht um 00:01 Uhr)
- Am nächsten Tag prüfen ob Streaks korrekt sind
- User-Feedback monitoren

## 🐛 Troubleshooting

### Problem: Script findet Laravel nicht

**Lösung:** Prüfe ob `__DIR__` korrekt ist
```bash
php -r "echo realpath(__DIR__);"
```

### Problem: Alle Streaks werden zurückgesetzt

**Lösung:** Prüfe Zeitzone in `.env`
```bash
# In .env
APP_TIMEZONE=Europe/Berlin
```

### Problem: `last_activity_date` ist NULL

**Lösung:** Migration fehlt oder Spalte nicht befüllt
```bash
# Prüfe Spalte
php artisan tinker
>>> User::first()->last_activity_date
```

## 📝 Notes

- **WICHTIG:** Backup der Datenbank vor dem ersten Live-Run!
- **WICHTIG:** Informiere User über den Fix
- Test-Script hat zusätzliche Debug-Ausgaben
- Prod-Script läuft ohne Debug-Spam

## 🎯 Erwartetes Verhalten (nach Fix)

1. User lernt täglich → Streak wächst
2. User lernt heute nicht → hat bis Mitternacht Zeit
3. User pausiert 1 Tag → Streak wird am nächsten Tag um 00:01 zurückgesetzt
4. User pausiert mehrere Tage → Streak wird am nächsten Tag um 00:01 zurückgesetzt

## ✅ Checkliste

- [ ] Dateien auf Server hochgeladen
- [ ] Plesk Cronjob PROD auf 00:01 Uhr geändert
- [ ] Plesk Cronjob TEST auf 00:01 Uhr geändert
- [ ] Test-Script manuell ausgeführt und geprüft
- [ ] Ersten automatischen Lauf überwacht
- [ ] User-Feedback eingeholt

---

**Erstellt:** 16. Januar 2026
**Status:** ✅ Bereit für Deployment
