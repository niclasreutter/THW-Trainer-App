# 🔧 Cronjob-Optimierung - Komplette Analyse & Fixes

## 📋 Übersicht

Komplette Überprüfung und Optimierung aller Cronjobs der THW-Trainer-App.

**Datum:** 16. Januar 2026
**Status:** ✅ Abgeschlossen

---

## 🔴 Gefundene Probleme

### 1. Inkonsistente Datenspalten in Streak-Logik

**Problem:**
- `GamificationService` verwendet `last_activity_date` für Streak-Updates
- `cronjob-daily-reset` verwendete `daily_questions_date` ❌
- `cronjob-streak-reminders` verwendete `daily_questions_date` ❌

**Auswirkung:**
- User-Streaks wurden zurückgesetzt obwohl sie gelernt haben
- Streak-Reminder-Mails wurden nicht korrekt versendet
- Inkonsistente Daten in der Datenbank

**Fix:**
Alle Cronjobs verwenden jetzt `last_activity_date` (konsistent mit GamificationService)

---

### 2. Falsches Timing beim Daily-Reset

**Problem:**
- Alter Cron lief um **23:00 Uhr**
- Prüfte `daily_questions_date != heute`
- User hatten keine Kulanzzeit bis Mitternacht

**Beispiel:**
```
Montag 14:00: User lernt
Dienstag 23:00: Cron läuft
→ Streak wird zurückgesetzt ❌
→ User hatte noch 1h Zeit!
```

**Fix:**
- Cron läuft jetzt um **00:01 Uhr** (nach Mitternacht)
- Prüft ob User **gestern** gelernt hat
- Gibt Kulanzzeit bis Mitternacht

---

### 3. Veraltete & unsichere Debug-Files

**Problem:**
- `public/cronjob-debug.php` - öffentlich zugänglich! 🚨
- `cronjob-debug.php` - nicht mehr benötigt
- `cronjob-plesk-debug.php` - nicht mehr benötigt
- `cronjob-simple.php` - alte Test-Version
- `cronjob-cleanup.php` - alte Version ohne -prod/-test
- `cronjob-streak-reminders.php` - alte Version ohne -prod/-test

**Fix:**
Alle veralteten Files gelöscht

---

## ✅ Durchgeführte Fixes

### 1. Daily-Reset Cronjob (✅ GEFIXT)

**Geänderte Files:**
- `cronjob-daily-reset-prod.php`
- `cronjob-daily-reset-test.php`

**Änderungen:**
```php
// ❌ VORHER
->where('daily_questions_date', '!=', $today)
// Lief um 23:00 Uhr

// ✅ NACHHER
->where('last_activity_date', '<', $yesterday)
// Läuft um 00:01 Uhr
```

**Plesk-Konfiguration:**
- **Zeit geändert:** 23:00 → 00:01 Uhr (Minute: 1, Stunde: 0)

---

### 2. Streak-Reminders Cronjob (✅ GEFIXT)

**Geänderte Files:**
- `cronjob-streak-reminders-prod.php`
- `cronjob-streak-reminders-test.php`
- `app/Console/Commands/SendStreakReminders.php`

**Änderungen:**
```php
// ❌ VORHER
$lastDailyActivity = $user->daily_questions_date ? ...
->where('daily_questions_date', '!=', $today)

// ✅ NACHHER
$lastActivity = $user->last_activity_date ? ...
->where('last_activity_date', '!=', $today)
```

**Plesk-Konfiguration:**
- Keine Zeitänderung nötig (läuft korrekt um 18:00 Uhr)

---

### 3. Cleanup - Veraltete Files (✅ GELÖSCHT)

**Gelöschte Files:**
- ❌ `public/cronjob-debug.php` - SICHERHEITSRISIKO!
- ❌ `cronjob-debug.php`
- ❌ `cronjob-plesk-debug.php`
- ❌ `cronjob-simple.php`
- ❌ `cronjob-cleanup.php` (alte Version)
- ❌ `cronjob-streak-reminders.php` (alte Version)

---

## 📊 Cronjob-Übersicht (nach Optimierung)

| Cronjob | Zeit | Datenspalte | Status |
|---------|------|-------------|--------|
| **Daily Reset** | 00:01 | `last_activity_date` | ✅ GEFIXT |
| **Streak Reminders** | 18:00 | `last_activity_date` | ✅ GEFIXT |
| **Inactive Reminders** | 10:00 | `last_activity_date` | ✅ OK |
| **Cleanup** | 09:00 | - | ✅ OK |
| **Admin Report** | 08:00 | - | ✅ OK |
| **Performance** | 00:00, 06:00, 12:00, 18:00 | - | ✅ OK |
| **DB Backup** | Sonntag 02:00 | - | ✅ OK |
| **System Maintenance** | Sonntag 03:00 | - | ✅ OK |

---

## 🎯 Konsistenz-Regeln

### Verwendung von Datenspalten

| Spalte | Zweck | Verwendet von |
|--------|-------|---------------|
| `last_activity_date` | **Generelle User-Aktivität** (auch falsche Antworten) | GamificationService, Daily Reset, Streak Reminders, Inactive Reminders |
| `daily_questions_date` | **Daily Questions Counter** (nur für tägliche Statistik) | Dashboard, Daily Questions Feature |
| `streak_days` | **Streak-Zähler** | Wird von Daily Reset verwaltet |

### Wichtig!

- **IMMER** `last_activity_date` für Streak-Logik verwenden
- **NIE** `daily_questions_date` für Streak-Entscheidungen verwenden
- `daily_questions_date` ist nur für den Daily Questions Counter

---

## 🚀 Deployment-Checkliste

### 1. Files auf Server hochladen

```bash
git pull origin main
```

### 2. Plesk Cronjobs aktualisieren

#### ⚠️ WICHTIG: Daily Reset Zeit ändern!

**Produktion (thw-trainer.de):**
- Cronjob: "Tägliche Streak-Reset"
- **Minute:** 1 (vorher: 0)
- **Stunde:** 0 (vorher: 23)
- **Dateiname:** `cronjob-daily-reset-prod.php` (unverändert)

**Test (test.thw-trainer.de):**
- Cronjob: "Tägliche Streak-Reset"
- **Minute:** 1 (vorher: 0)
- **Stunde:** 0 (vorher: 23)
- **Dateiname:** `cronjob-daily-reset-test.php` (unverändert)

#### Streak Reminders (keine Änderung nötig)

**Produktion:**
- Zeit: 18:00 Uhr ✓ (bleibt unverändert)
- Dateiname: `cronjob-streak-reminders-prod.php` ✓

**Test:**
- Zeit: 18:00 Uhr ✓ (bleibt unverändert)
- Dateiname: `cronjob-streak-reminders-test.php` ✓

### 3. Test durchführen

```bash
# Test-Scripts manuell ausführen
php cronjob-daily-reset-test.php
php cronjob-streak-reminders-test.php

# Logs prüfen
tail -f storage/logs/laravel.log
```

### 4. Erste Nacht überwachen

- Ersten automatischen Daily-Reset-Lauf abwarten (heute Nacht 00:01 Uhr)
- Morgen prüfen ob Streaks korrekt sind
- User-Feedback monitoren

---

## 🐛 Troubleshooting

### Problem: Streaks werden immer noch falsch zurückgesetzt

**Prüfen:**
```sql
SELECT id, name, email, streak_days,
       last_activity_date, daily_questions_date
FROM users
WHERE streak_days > 0
ORDER BY last_activity_date DESC;
```

**Lösung:**
- Prüfe ob `last_activity_date` korrekt befüllt wird
- Prüfe Cronjob-Zeit in Plesk (muss 00:01 sein!)
- Prüfe Cronjob-Logs in Plesk

### Problem: `last_activity_date` ist NULL

**Lösung:**
```bash
# Migration fehlt oder nicht ausgeführt
php artisan migrate

# Prüfe Spalte
php artisan tinker
>>> User::first()->last_activity_date
```

### Problem: Cronjob läuft nicht

**Lösung:**
1. Prüfe Plesk-Logs
2. Teste Script manuell: `php cronjob-daily-reset-test.php`
3. Prüfe Pfad in Plesk-Konfiguration
4. Prüfe PHP-Version in Plesk

---

## 📈 Erwartetes Verhalten (nach Fix)

### Daily Reset (00:01 Uhr)

| Szenario | Verhalten |
|----------|-----------|
| User lernt Montag, Dienstag 00:01 Uhr | ✅ Streak bleibt (hat bis Mitternacht Zeit) |
| User lernt Montag, nicht Dienstag, Mittwoch 00:01 Uhr | ✅ Streak wird zurückgesetzt (1 Tag Pause) |
| User lernt Montag + Dienstag, Mittwoch 00:01 Uhr | ✅ Streak bleibt |

### Streak Reminders (18:00 Uhr)

| Szenario | Verhalten |
|----------|-----------|
| User mit Streak > 1, heute nicht gelernt | ✅ Erhält Reminder-Mail |
| User mit Streak > 1, heute gelernt | ✅ Keine Mail |
| User ohne Streak | ✅ Keine Mail |
| User ohne E-Mail-Zustimmung | ✅ Keine Mail |

---

## 📝 Lessons Learned

### 1. Konsistenz ist kritisch
- Eine Datenspalte pro Konzept verwenden
- Nicht zwischen verschiedenen Spalten für gleiche Logik wechseln

### 2. Timing ist wichtig
- Streak-Resets müssen nach Mitternacht laufen
- User brauchen Kulanzzeit bis Ende des Tages

### 3. Debug-Files aufräumen
- Öffentliche Debug-Files sind Sicherheitsrisiken
- Alte Versionen löschen um Verwirrung zu vermeiden

### 4. Test-Umgebung nutzen
- Immer erst in Test-Umgebung testen
- Debug-Output in Test-Version behalten

---

## ✅ Checkliste

- [x] Daily Reset Cronjob gefixt
- [x] Streak Reminders Cronjob gefixt
- [x] Veraltete Files gelöscht
- [x] PHP-Syntax geprüft
- [x] Dokumentation erstellt
- [ ] Plesk Daily-Reset Zeit auf 00:01 geändert (PROD)
- [ ] Plesk Daily-Reset Zeit auf 00:01 geändert (TEST)
- [ ] Test-Scripts manuell ausgeführt
- [ ] Erste Nacht überwacht
- [ ] User-Feedback eingeholt

---

## 📞 Support

Bei Problemen:
1. Prüfe Cronjob-Logs in Plesk
2. Teste Script manuell
3. Prüfe `storage/logs/laravel.log`
4. Prüfe Datenbank: `last_activity_date` vs `daily_questions_date`

---

**Erstellt:** 16. Januar 2026
**Author:** Claude Code
**Status:** ✅ Bereit für Deployment
