# Admin Report Fix - Zeigt jetzt "Gestern" statt "Heute"

## 🔴 Problem

Der Admin Report zeigte unvollständige Daten:
- Lief um **08:00 Uhr**
- Zeigte Daten von **"heute"** (00:00 - 08:00 Uhr)
- **Nur 8 Stunden** Daten statt voller Tag
- Die meisten User sind um 08:00 Uhr noch nicht aktiv
- Verwendete `updated_at` statt `last_activity_date` (inkonsistent)

**Beispiel:**
```
Report läuft Dienstag 08:00 Uhr
→ Zeigt "active_today": User die Dienstag 00:00-08:00 aktiv waren
→ Aber die meisten lernen nachmittags/abends!
→ Report zeigt fast keine Aktivität ❌
```

---

## ✅ Lösung (Option A)

Report zeigt jetzt **"Gestern"** (kompletter Tag):
- Läuft weiterhin um **08:00 Uhr**
- Zeigt **gestrigen Tag** (00:00 - 23:59 Uhr)
- **Komplette 24h** Daten
- Verwendet `last_activity_date` (konsistent mit anderen Cronjobs)

**Beispiel:**
```
Report läuft Dienstag 08:00 Uhr
→ Zeigt "Gestern" = Montag (00:00 - 23:59 Uhr)
→ Komplette Aktivitätsdaten von Montag ✓
→ Du siehst morgens was gestern passiert ist ✓
```

---

## 📝 Geänderte Files

### 1. `app/Console/Commands/DailyAdminReport.php`

**Zeiträume:**
```php
// ❌ VORHER
$today = now()->startOfDay();        // 00:00 heute
$yesterday = now()->subDay()->startOfDay();

// ✅ NACHHER
$yesterday = now()->subDay()->startOfDay();      // 00:00 gestern
$yesterdayEnd = now()->subDay()->endOfDay();     // 23:59 gestern
$twoDaysAgo = now()->subDays(2)->startOfDay();
$lastWeek = now()->subWeek()->startOfDay();
$lastMonth = now()->subMonth()->startOfDay();
```

**Datenspalten:**
```php
// ❌ VORHER - Inkonsistent!
'active_today' => User::where('updated_at', '>=', $today)->count()

// ✅ NACHHER - Konsistent mit anderen Cronjobs
'active_yesterday' => User::whereBetween('last_activity_date', [$yesterday, $yesterdayEnd])->count()
```

**Neue Felder:**
```php
'date' => now()->subDay()->format('d.m.Y'),  // Gestern
'report_day' => 'Gestern',

// Benutzer
'active_yesterday' => ...,
'active_last_7_days' => ...,
'active_last_30_days' => ...,
'new_yesterday' => ...,
'new_last_7_days' => ...,

// Aktivität
'questions_answered_yesterday' => ...,
'questions_answered_2_days_ago' => ...,
'correct_answers_yesterday' => ...,
```

**E-Mail-Betreff:**
```php
// ❌ VORHER
"THW-Trainer Tagesreport - {$reportData['date']}"

// ✅ NACHHER
"THW-Trainer Tagesreport - {$reportData['date']} (Gestern)"
```

### 2. `resources/views/emails/admin-daily-report.blade.php`

**Header:**
```blade
{{-- ❌ VORHER --}}
<p>Automatischer Bericht für {{ $date }}</p>

{{-- ✅ NACHHER --}}
<p>Automatischer Bericht für {{ $date }} ({{ $report_day }})</p>
```

**Benutzer-Statistiken:**
```blade
{{-- ❌ VORHER --}}
<span class="stat-number">{{ number_format($users['active_today']) }}</span>
<div class="stat-label">Aktiv heute</div>

{{-- ✅ NACHHER --}}
<span class="stat-number">{{ number_format($users['active_yesterday']) }}</span>
<div class="stat-label">Aktiv gestern</div>
```

**Aktivitäts-Statistiken:**
```blade
{{-- ❌ VORHER --}}
{{ number_format($activity['questions_answered_today']) }}
<div class="stat-label">Fragen heute beantwortet</div>

{{-- ✅ NACHHER --}}
{{ number_format($activity['questions_answered_yesterday']) }}
<div class="stat-label">Fragen gestern beantwortet</div>
```

---

## 📊 Vergleich: Vorher vs. Nachher

| Aspekt | Vorher (Heute) | Nachher (Gestern) |
|--------|----------------|-------------------|
| **Report-Zeit** | 08:00 Uhr | 08:00 Uhr (unverändert) |
| **Zeitraum** | Heute 00:00-08:00 (8h) | Gestern 00:00-23:59 (24h) |
| **Datenqualität** | ❌ Unvollständig | ✅ Vollständig |
| **Datenspalte** | ❌ `updated_at` | ✅ `last_activity_date` |
| **Konsistenz** | ❌ Inkonsistent | ✅ Konsistent |
| **Nutzen** | Gering (nur 8h) | Hoch (voller Tag) |

---

## 🎯 Vorteile der neuen Lösung

1. **Vollständige Daten**
   - 24h statt 8h
   - Realistische User-Aktivität
   - Kompletter Tagesüberblick

2. **Konsistenz**
   - Verwendet `last_activity_date` (wie alle anderen Cronjobs)
   - Einheitliche Datenquelle

3. **Bessere Insights**
   - Zeigt echte Lern-Aktivität
   - Nicht durch Profil-Updates verfälscht
   - Vergleichbar mit anderen Tagen

4. **Klare Zeiträume**
   - "Gestern" = eindeutig
   - "Letzte 7 Tage" = klar definiert
   - "Letzte 30 Tage" = voller Monat

---

## 🧪 Testing

### Manueller Test

```bash
# Test-Umgebung
php artisan admin:daily-report protokolle@thw-trainer.de

# Prüfe Logs
tail -f storage/logs/laravel.log
```

### Erwartetes Ergebnis

**E-Mail-Betreff:**
```
THW-Trainer Tagesreport - 15.01.2026 (Gestern)
```

**E-Mail-Inhalt:**
- Zeigt Datum von gestern
- Alle Zahlen von gestern (00:00-23:59)
- Labels zeigen "gestern" statt "heute"
- Zusätzlich: "Letzte 7 Tage" und "Letzte 30 Tage"

---

## 📅 Plesk-Konfiguration

**Keine Änderung nötig!**

Der Cronjob läuft weiterhin um **08:00 Uhr**:
```
Minute: 0
Stunde: 8
Tag: *
Monat: *
Wochentag: *
Script: cronjob-admin-report-prod.php
```

---

## 📈 Beispiel-Report

```
Report läuft: Mittwoch, 16.01.2026 08:00 Uhr
Zeigt Daten von: Dienstag, 15.01.2026 (00:00 - 23:59 Uhr)

Benutzer-Übersicht:
- Aktiv gestern: 47 User
- Neu registriert gestern: 3 User
- Aktiv letzte 7 Tage: 152 User
- Aktiv letzte 30 Tage: 431 User

Lernaktivität:
- Fragen gestern beantwortet: 1.247
- Richtige Antworten gestern: 923
- Erfolgsquote gestern: 74.0%
```

---

## ✅ Checkliste

- [x] `DailyAdminReport.php` geändert (Zeiträume)
- [x] `DailyAdminReport.php` geändert (Datenspalten)
- [x] E-Mail-Vorlage angepasst (Labels)
- [x] E-Mail-Betreff angepasst
- [x] PHP-Syntax geprüft
- [ ] Manuell getestet
- [ ] Ersten Report nach Deployment geprüft

---

**Erstellt:** 16. Januar 2026
**Status:** ✅ Bereit für Deployment
