# 🔥 Streak & Daily Questions Reset Cron-Job Fix

## 🔴 Problem #1 (Behoben: 16. Januar 2026)

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

## 🔴 Problem #2 (Behoben: 19. Januar 2026)

User haben gemeldet, dass ihre täglichen Fragen (20 Stück) manchmal nicht bzw. erst vormittags zurückgesetzt werden.

### Ursache

**Daily Questions wurden nur für inaktive User zurückgesetzt!**
- Der Cronjob setzte `daily_questions_solved` nur zurück, wenn auch der Streak zurückgesetzt wurde
- User die **jeden Tag** lernen, bekamen ihre Daily Questions **nicht um 00:01 Uhr** zurückgesetzt
- Der Reset erfolgte erst beim Beantworten der ersten Frage am nächsten Tag (durch `GamificationService::updateDailyQuestions()`)

```
Beispiel (ALTER Cron):
- User lernt am Montag 20 Fragen → daily_questions_solved = 20, daily_questions_date = Montag
- Dienstag 00:01 Uhr läuft der Cron
- User war gestern aktiv → Streak bleibt ✓
- ABER: daily_questions_solved wird NICHT zurückgesetzt! ❌
- Dienstag 08:00 Uhr: User loggt sich ein und sieht noch "20/20" vom Vortag
- Dienstag 08:01 Uhr: User beantwortet erste Frage → Counter wird zurückgesetzt auf 1
- User denkt: "Warum wurde mein Counter nicht um Mitternacht zurückgesetzt?"
```

## ✅ Lösung

### Neue Logik (Version 2.0 - 19. Januar 2026)

**1. Zwei getrennte Reset-Logiken**

Der Cronjob behandelt jetzt Daily Questions und Streaks unabhängig voneinander:

```php
foreach ($allUsers as $user) {
    // 1. RESET DAILY QUESTIONS (für ALLE User)
    if ($user->daily_questions_date && Carbon::parse($user->daily_questions_date)->lt($today)) {
        $user->daily_questions_solved = 0;
        $user->daily_questions_date = null;
    }

    // 2. RESET STREAK (nur für User die gestern NICHT aktiv waren)
    if ($user->streak_days > 0) {
        if (!$lastActivity || $lastActivity->lt($yesterday)) {
            $user->streak_days = 0;
        }
    }
}
```

**2. Konsistente Datenspalte für Streaks**
- Verwendet `last_activity_date` (wie GamificationService)
- Nicht mehr `daily_questions_date` für Streak-Reset

**3. Korrektes Timing**
- Läuft um **00:01 Uhr** (nach Mitternacht)
- Prüft ob User **gestern** gelernt hat
- Wenn `last_activity_date < gestern` → Streak wird zurückgesetzt
- Wenn `daily_questions_date < heute` → Daily Questions werden zurückgesetzt

**4. Kulanzzeit bis Mitternacht**
```
Beispiel (NEUER Cron v2.0):
MONTAG 14:00 Uhr:
- User lernt und beantwortet 20 Fragen
- last_activity_date = Montag
- daily_questions_solved = 20, daily_questions_date = Montag
- streak_days = 5

DIENSTAG 00:01 Uhr (Cronjob läuft):
- Heute = Dienstag
- Gestern = Montag
- daily_questions_date (Montag) < Heute (Dienstag) → Daily Questions werden zurückgesetzt! ✓
- last_activity_date (Montag) >= Gestern (Montag) → Streak bleibt! ✓
- Ergebnis: daily_questions_solved = 0, streak_days = 5

DIENSTAG 08:00 Uhr:
- User loggt sich ein
- Sieht "0/20 tägliche Fragen" ✓
- Streak bleibt bei 5 Tagen ✓

MITTWOCH 00:01 Uhr (User hat Dienstag NICHT gelernt):
- Gestern = Dienstag
- last_activity_date (Montag) < Gestern (Dienstag) → Streak wird zurückgesetzt! ✓
- Ergebnis: streak_days = 0
```

## 📋 Geänderte Dateien

### Version 1.0 (16. Januar 2026)
- `cronjob-daily-reset-prod.php` - Komplett neu geschrieben
- `cronjob-daily-reset-test.php` - Komplett neu geschrieben (mit Debug-Output)

### Version 2.0 (19. Januar 2026)
- `cronjob-daily-reset-prod.php` - Daily Questions Reset für ALLE User (nicht nur inaktive)
- `cronjob-daily-reset-test.php` - Erweiterte Debug-Ausgaben für Daily Questions

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

| Szenario | Alter Cron v1 (23:00) | Neuer Cron v1 (00:01) | Neuer Cron v2 (00:01) |
|----------|----------------------|----------------------|----------------------|
| User lernt Montag, Cron läuft Dienstag 23:00 | ❌ Streak zurückgesetzt (noch 1h Zeit!) | - | - |
| User lernt Montag, Cron läuft Dienstag 00:01 | - | ✅ Streak bleibt | ✅ Streak bleibt |
| Daily Questions nach Cron | - | ❌ Noch 20/20 (wenn Streak bleibt) | ✅ 0/20 (zurückgesetzt) |
| User lernt Montag, nicht Dienstag, Cron läuft Mittwoch 00:01 | - | ✅ Streak wird zurückgesetzt | ✅ Streak wird zurückgesetzt |
| User lernt täglich, Cron läuft täglich 00:01 | - | ❌ Daily Questions nicht zurückgesetzt | ✅ Daily Questions zurückgesetzt |

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

## 🎯 Erwartetes Verhalten (nach Fix v2.0)

### Daily Questions
1. **Täglich um 00:01 Uhr** werden Daily Questions für **ALLE User** zurückgesetzt
2. User sehen beim Login am Morgen "0/20 tägliche Fragen"
3. Unabhängig davon, ob der Streak bleibt oder nicht

### Streaks
1. User lernt täglich → Streak wächst, Daily Questions werden täglich zurückgesetzt
2. User lernt heute nicht → hat bis Mitternacht Zeit, Daily Questions werden morgen zurückgesetzt
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
**Aktualisiert:** 19. Januar 2026 (Daily Questions Fix)
**Status:** ✅ Bereit für Deployment
