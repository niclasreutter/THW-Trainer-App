# 📧 Inaktivitäts-Erinnerungen Setup

## 🎯 Übersicht

Das Inaktivitäts-Erinnerungssystem sendet automatisch E-Mails an User, die seit mindestens 4 Tagen nicht mehr aktiv waren und ihre E-Mail-Zustimmung gegeben haben.

### Warum diese Funktion?

- ✅ **User-Retention**: Bringt inaktive User zurück
- ✅ **Motivation**: Zeigt Fortschritt und verbleibende Fragen
- ✅ **Personalisiert**: Unterschiedliche Texte je nach Fortschritt
- ✅ **Kein Spam**: Mail wird nur 1x gesendet (oder max. alle 30 Tage)

---

## 🔧 Technische Implementierung

### 1. Neue Datenbank-Migration

**Datei:** `database/migrations/2025_10_16_000000_add_inactive_reminder_to_users_table.php`

Fügt neues Feld hinzu:
```php
$table->timestamp('inactive_reminder_sent_at')->nullable();
```

**Migration ausführen:**
```bash
php artisan migrate
```

### 2. Mail-Klasse

**Datei:** `app/Mail/InactiveReminderMail.php`

Berechnet automatisch:
- Anzahl Tage inaktiv
- Verbleibende Fragen
- Fortschritt in Prozent
- Personalisierte Subject Line

### 3. Email-Template

**Datei:** `resources/views/emails/inactive-reminder.blade.php`

Features:
- 📊 Visueller Fortschrittsbalken
- 📈 Statistiken (verbleibende Fragen, Fortschritt %)
- 💪 Motivierende Texte je nach Fortschritt
- 🎯 Call-to-Action Button
- 📱 Responsive Design

### 4. Artisan Command

**Datei:** `app/Console/Commands/SendInactiveReminders.php`

**Command:**
```bash
php artisan app:send-inactive-reminders
```

**Mit Custom-Parametern:**
```bash
# Standard: 4 Tage Inaktivität
php artisan app:send-inactive-reminders

# Custom: 7 Tage Inaktivität
php artisan app:send-inactive-reminders --days=7
```

### 5. Cronjob-Scripts

**Für Produktion:**
- `cronjob-inactive-reminders-prod.php`

**Für Test:**
- `cronjob-inactive-reminders-test.php`

---

## 📋 Logik-Ablauf

### User-Auswahl-Kriterien

Ein User erhält eine Inaktivitäts-Mail, wenn:

1. ✅ `email_consent = true` (E-Mail-Zustimmung gegeben)
2. ✅ `last_activity_date < (heute - 4 Tage)` (mind. 4 Tage inaktiv)
3. ✅ `inactive_reminder_sent_at IS NULL` (noch nie Mail bekommen)
   **ODER**
   `inactive_reminder_sent_at < (heute - 30 Tage)` (letzte Mail >30 Tage her)

### Beispiel-Szenarien

#### ✅ Szenario 1: Erste Mail
```
User-Daten:
- email_consent: true
- last_activity_date: 2025-10-01
- inactive_reminder_sent_at: NULL
- Heute: 2025-10-06

→ Inaktiv seit: 5 Tagen (≥4 Tage)
→ Keine Mail bisher gesendet
→ ✅ Mail wird gesendet
→ inactive_reminder_sent_at = 2025-10-06
```

#### ❌ Szenario 2: Mail vor kurzem gesendet
```
User-Daten:
- email_consent: true
- last_activity_date: 2025-09-01
- inactive_reminder_sent_at: 2025-10-01
- Heute: 2025-10-06

→ Inaktiv seit: 35 Tagen
→ Letzte Mail vor 5 Tagen gesendet
→ ❌ Keine Mail (Spam-Schutz: 30-Tage-Regel)
```

#### ✅ Szenario 3: User wieder inaktiv nach 30+ Tagen
```
User-Daten:
- email_consent: true
- last_activity_date: 2025-09-01
- inactive_reminder_sent_at: 2025-09-05
- Heute: 2025-10-15

→ Inaktiv seit: 44 Tagen
→ Letzte Mail vor 40 Tagen gesendet (>30 Tage)
→ ✅ Mail wird erneut gesendet
→ inactive_reminder_sent_at = 2025-10-15
```

#### ❌ Szenario 4: Kein E-Mail-Consent
```
User-Daten:
- email_consent: false
- last_activity_date: 2025-09-01
- inactive_reminder_sent_at: NULL
- Heute: 2025-10-15

→ Inaktiv seit: 44 Tagen
→ Aber: email_consent = false
→ ❌ Keine Mail (Datenschutz)
```

---

## 📧 Mail-Inhalte

### Bei wenigen verbleibenden Fragen (≤10)
```
🎯 Dein Fortschritt: 95%
Nur noch 5 Fragen bis zum Ziel!

💪 Du bist so nah dran!
Nur noch 5 Fragen! Das schaffst du locker in ein paar Minuten. 🚀
```

### Bei mittlerem Fortschritt (≤50)
```
🎯 Dein Fortschritt: 75%
Nur noch 30 Fragen bis zum Ziel!

💪 Du bist so nah dran!
Mit nur 10 Fragen pro Tag bist du in wenigen Tagen durch! Du packst das! 💪
```

### Bei vielen verbleibenden Fragen (>50)
```
🎯 Dein Fortschritt: 25%
Nur noch 150 Fragen bis zum Ziel!

💪 Du bist so nah dran!
Schritt für Schritt kommst du ans Ziel. Jede Frage bringt dich weiter! 🎯
```

### Bei 100% Fortschritt
```
🎉 Alle Fragen gemeistert!
Du hast bereits alle 200 Fragen gemeistert!

💡 Bleib dran!
Wiederhole deine Fragen regelmäßig, um dein Wissen frisch zu halten.
Übung macht den Meister! 🏆
```

---

## 🚀 Deployment

### 1. Migration ausführen
```bash
# Lokal testen
php artisan migrate

# Auf Prod via SSH
cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de
php artisan migrate
```

### 2. Cronjob in Plesk einrichten

#### Produktion
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/thw-trainer.de && php cronjob-inactive-reminders-prod.php
Zeitplan: Täglich um 10:00 Uhr
Minute: 0
Stunde: 10
Tag: *
Monat: *
Wochentag: *
```

#### Test
```
Befehl: cd /var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de && php cronjob-inactive-reminders-test.php
Zeitplan: Täglich um 10:00 Uhr
Minute: 0
Stunde: 10
Tag: *
Monat: *
Wochentag: *
```

### 3. Manuell testen

```bash
# Via Artisan
php artisan app:send-inactive-reminders

# Via Cronjob-Script
php cronjob-inactive-reminders-prod.php

# Mit Custom-Tagen (z.B. 1 Tag für Test)
php artisan app:send-inactive-reminders --days=1
```

---

## 🧪 Testing

### Test-Szenarien

#### Test 1: User manuell inaktiv setzen
```php
// In tinker
php artisan tinker

$user = User::find(1);
$user->last_activity_date = now()->subDays(5); // 5 Tage inaktiv
$user->email_consent = true;
$user->inactive_reminder_sent_at = null; // Noch keine Mail gesendet
$user->save();

// Jetzt Cronjob ausführen
exit
php artisan app:send-inactive-reminders
```

#### Test 2: Spam-Schutz prüfen
```php
$user = User::find(1);
$user->last_activity_date = now()->subDays(10);
$user->inactive_reminder_sent_at = now()->subDays(5); // Vor 5 Tagen Mail gesendet
$user->save();

// Sollte KEINE Mail senden (weniger als 30 Tage)
php artisan app:send-inactive-reminders
```

#### Test 3: 30-Tage-Regel prüfen
```php
$user = User::find(1);
$user->last_activity_date = now()->subDays(50);
$user->inactive_reminder_sent_at = now()->subDays(35); // Vor 35 Tagen Mail gesendet
$user->save();

// Sollte Mail senden (mehr als 30 Tage)
php artisan app:send-inactive-reminders
```

---

## 📊 Monitoring

### Log-Output
```
[2025-10-16 10:00:00] Starte Inaktivitäts-Erinnerungs-Check (PRODUKTION)...
[2025-10-16 10:00:01] DEBUG: Gesamt Benutzer: 150
[2025-10-16 10:00:01] Gefunden: 12 inaktive Benutzer für Erinnerungen.
[2025-10-16 10:00:02] DEBUG: Max Mustermann - Letzte Aktivität: 2025-10-01, Tage inaktiv: 15, Letzte Reminder-Mail: NIEMALS
[2025-10-16 10:00:03] E-Mail gesendet an: Max Mustermann (max@example.com) - Inaktiv seit: 15 Tagen
[2025-10-16 10:00:10] Inaktivitäts-Erinnerungs-Check abgeschlossen!
[2025-10-16 10:00:10] E-Mails gesendet: 12
[2025-10-16 10:00:10] Übersprungen: 0
[2025-10-16 10:00:10] Fehler: 0
```

### Wichtige Metriken

```sql
-- Wie viele User haben Mail bekommen?
SELECT COUNT(*) as users_with_reminder
FROM users 
WHERE inactive_reminder_sent_at IS NOT NULL;

-- Wann wurde die letzte Mail-Welle gesendet?
SELECT 
    DATE(inactive_reminder_sent_at) as date,
    COUNT(*) as emails_sent
FROM users 
WHERE inactive_reminder_sent_at IS NOT NULL
GROUP BY DATE(inactive_reminder_sent_at)
ORDER BY date DESC
LIMIT 10;

-- Wie viele User sind aktuell inaktiv (≥4 Tage)?
SELECT COUNT(*) as inactive_users
FROM users 
WHERE email_consent = true
  AND last_activity_date < DATE_SUB(NOW(), INTERVAL 4 DAY);

-- Success Rate: User die nach Mail zurückkamen
SELECT 
    u.name,
    u.inactive_reminder_sent_at as mail_sent,
    u.last_activity_date as last_active,
    CASE 
        WHEN u.last_activity_date > u.inactive_reminder_sent_at THEN 'Zurückgekommen'
        ELSE 'Noch inaktiv'
    END as status
FROM users u
WHERE u.inactive_reminder_sent_at IS NOT NULL
ORDER BY u.inactive_reminder_sent_at DESC
LIMIT 20;
```

---

## ⚙️ Konfiguration

### Anpassbare Parameter

#### Inaktivitäts-Schwellenwert ändern
```php
// In cronjob-inactive-reminders-prod.php
$inactiveDays = 4; // Ändere auf 7 für 7 Tage

// Oder via Command
php artisan app:send-inactive-reminders --days=7
```

#### Spam-Schutz-Intervall ändern
```php
// In SendInactiveReminders.php, Zeile 47
->orWhere('inactive_reminder_sent_at', '<', Carbon::now()->subDays(30));
// Ändere 30 auf gewünschte Anzahl Tage
```

#### Mail-Subject anpassen
```php
// In InactiveReminderMail.php, Zeile 48-51
$subject = $this->remainingQuestions > 0 
    ? 'Du fehlst uns! Nur noch ' . $this->remainingQuestions . ' Fragen bis zum Ziel 🎯'
    : 'Du fehlst uns! Bleib dran mit deinem Wissen 💪';
```

---

## 🐛 Troubleshooting

### Problem: Mails werden nicht gesendet

**Lösung 1: Prüfe Mail-Konfiguration**
```bash
php artisan tinker
Mail::raw('Test', function($msg) { 
    $msg->to('test@example.com')->subject('Test'); 
});
```

**Lösung 2: Prüfe User-Daten**
```sql
SELECT 
    id, name, email, email_consent, 
    last_activity_date, inactive_reminder_sent_at
FROM users 
WHERE email_consent = true 
  AND last_activity_date < DATE_SUB(NOW(), INTERVAL 4 DAY)
LIMIT 5;
```

**Lösung 3: Debug-Mode**
```php
// In cronjob-inactive-reminders-prod.php
// Debug-Output ist bereits eingebaut, prüfe die Logs
```

### Problem: Zu viele Mails gesendet

**Lösung: Reset inactive_reminder_sent_at**
```sql
-- VORSICHT: Nur im Notfall!
UPDATE users 
SET inactive_reminder_sent_at = NOW()
WHERE inactive_reminder_sent_at IS NULL;
```

### Problem: User beschweren sich über Mails

**Lösung: Opt-Out respektieren**
```sql
-- User hat email_consent = false gesetzt?
SELECT email_consent FROM users WHERE id = X;

-- Falls trotzdem Mail gesendet → Bug im Code prüfen
```

---

## 📈 Erfolgsmetriken

### KPIs zum Tracken

1. **Rückkehr-Rate**
   ```sql
   -- User die nach Mail wieder aktiv wurden
   SELECT 
       COUNT(*) as returned_users,
       (COUNT(*) * 100.0 / total.cnt) as return_rate_percent
   FROM users u
   CROSS JOIN (SELECT COUNT(*) as cnt FROM users WHERE inactive_reminder_sent_at IS NOT NULL) total
   WHERE u.inactive_reminder_sent_at IS NOT NULL
     AND u.last_activity_date > u.inactive_reminder_sent_at;
   ```

2. **Durchschnittliche Reaktionszeit**
   ```sql
   -- Wie schnell kommen User zurück?
   SELECT 
       AVG(DATEDIFF(last_activity_date, inactive_reminder_sent_at)) as avg_days_to_return
   FROM users
   WHERE inactive_reminder_sent_at IS NOT NULL
     AND last_activity_date > inactive_reminder_sent_at;
   ```

3. **Conversion nach Fortschritt**
   ```sql
   -- Kommen User mit mehr Fortschritt eher zurück?
   -- (Manuelle Analyse nötig)
   ```

---

## 🎉 Zusammenfassung

Das Inaktivitäts-Erinnerungssystem ist ein **wichtiges Retention-Tool** für die THW-Trainer-App!

**Key Points:**
- ✅ Sendet Mail nur an User mit E-Mail-Zustimmung
- ✅ Mindestens 4 Tage Inaktivität
- ✅ Einmalig (oder max. alle 30 Tage)
- ✅ Personalisierte Inhalte je nach Fortschritt
- ✅ Visueller Fortschrittsbalken
- ✅ Motivierende Texte

**Nächste Schritte:**
1. Migration ausführen (`php artisan migrate`)
2. Cronjobs in Plesk einrichten
3. Ersten Testlauf überwachen
4. Erfolgsmetriken nach 1-2 Wochen analysieren
5. Ggf. Schwellenwerte anpassen

