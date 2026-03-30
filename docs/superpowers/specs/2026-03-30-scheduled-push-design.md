# Scheduled Push Notifications — Design Spec

## Zusammenfassung

4 neue Push-Notification-Typen: Guten-Morgen-Erinnerung, Pruefungs-Tagesplan, Streak-Erinnerungen (3x taeglich), Lernsession-Start-Push. Alle nutzen die bestehende `PushNotification`-Klasse und den Laravel Scheduler.

## 1. Guten-Morgen Push

- **Zeit:** 08:30 taeglich
- **Empfaenger:** Alle User mit aktiver Push-Subscription
- **Inhalt:** Titel: "Guten Morgen!" / Body: "Zeit zum Lernen — starte jetzt deine taegliche Session."
- **Implementierung:** Neuer Artisan Command `app:send-morning-push`

## 2. Pruefungs-Tagesplan Push

- **Zeit:** 09:00 taeglich
- **Empfaenger:** User mit `exam_date` >= today UND Push-Subscription
- **Inhalt:** Titel: "Pruefung in X Tagen" / Body: "Dein Tagesziel: Y Fragen"
- **Berechnung:**
  - `X` = `exam_date` - today (in Tagen)
  - `Y` = `daily_streak_goal` ?? 20
- **Nicht senden wenn:** `exam_date` < today (Pruefung vorbei)
- **Implementierung:** Neuer Artisan Command `app:send-exam-reminder-push`

## 3. Streak-Erinnerungen

- **Zeiten:** 12:00, 17:00, 21:00 taeglich
- **Empfaenger:** User mit `streak_days >= 1` UND `last_activity_date != today` UND Push-Subscription
- **Inhalt variiert nach Uhrzeit:**
  - 12:00 — Titel: "Streak-Erinnerung" / Body: "Du hast heute noch X Fragen offen fuer deinen Streak"
  - 17:00 — Titel: "Streak-Erinnerung" / Body: "Noch X Fragen fuer deinen Y-Tage-Streak"
  - 21:00 — Titel: "Letzte Chance!" / Body: "Noch X Fragen, sonst geht dein Y-Tage-Streak verloren"
- **Berechnung:**
  - `X` = `max(0, (daily_streak_goal ?? 20) - daily_questions_solved)`
  - `Y` = `streak_days`
- **Nicht senden wenn:** `last_activity_date == today` (User hat heute schon qualifiziert)
- **Implementierung:** Neuer Artisan Command `app:send-streak-push` mit `--time` Parameter (morning/afternoon/evening)

## 4. Lernsession-Start Push

- **Trigger:** Wenn eine LearningSessionInstance auf Status `active` wechselt (im bestehenden `lernsession:lifecycle` Command)
- **Empfaenger:** Scope-basiert
  - Globale Sessions → alle User mit Push-Subscription
  - Ortsverband-Sessions → nur Mitglieder des Ortsverbands mit Push-Subscription
- **Inhalt:** Titel: "Lernsession gestartet" / Body: "{Session-Titel} — Jetzt mitmachen!"
- **Implementierung:** Integration in `LernsessionService::sendSessionStartedEmails()` — Push-Dispatch nach dem E-Mail-Versand

## Technische Details

- Alle Commands werden in `routes/console.php` registriert
- Alle nutzen `\App\Notifications\PushNotification` (bereits vorhanden, queued)
- Alle pruefen `whereHas('pushSubscriptions')` um nur User mit Subscription zu selektieren
- Timezone: `Europe/Berlin` (bereits im Scheduler konfiguriert)
- Kein neues Model oder Migration noetig
