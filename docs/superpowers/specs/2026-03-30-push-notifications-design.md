# Push-Benachrichtigungen — Design Spec

## Zusammenfassung

Browser/PWA Push-Benachrichtigungen für THW-Trainer-App. System-generierte Pushes (Streak, Liga, Einladungen) + manuelle Admin-Nachrichten an alle oder Ortsverbände.

## Technischer Ansatz

**Paket:** `laravel-notification-channels/webpush`
- Bringt PushSubscription-Model + Migration mit
- VAPID-basiert, aes128gcm Encoding (iOS Safari 16.4+ kompatibel)
- Integriert sich in Laravel's Notification-System via WebPush-Channel
- DSGVO-konform (alles auf eigenem Server)

## Datenmodell

### Tabelle `push_subscriptions` (vom Paket)

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| user_id | FK | Zugehöriger User |
| endpoint | text | Browser Push Endpoint URL |
| public_key | varchar | Client Public Key |
| auth_token | varchar | Client Auth Token |
| content_encoding | varchar | aes128gcm |

### Neue Tabelle `admin_push_messages`

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| id | bigint | PK |
| admin_user_id | FK | Wer hat gesendet |
| title | varchar | Nachricht-Titel |
| message | text | Nachricht-Body |
| target_type | enum | 'all' oder 'ortsverband' |
| target_id | bigint nullable | Ortsverband-ID (wenn target_type = ortsverband) |
| recipients_count | int | Anzahl gesendeter Pushes |
| created_at | timestamp | Sendezeitpunkt |

## Architektur

### Flow: User Opt-In

```
User loggt sich ein
  → Popup (Glass-Card Modal) erscheint (wenn keine aktive Subscription)
  → "Aktivieren" → Browser Permission Prompt
  → Permission granted → JS erstellt PushSubscription via Push API
  → POST /push/subscribe → Server speichert Subscription
  → "Später" → localStorage Flag, Popup erscheint nach 7 Tagen erneut
  → Nicht angezeigt wenn Browser Push nicht unterstützt
```

### Flow: Notification senden

```
Trigger (System-Event oder Admin-Aktion)
  → Laravel Notification erstellen mit WebPush-Channel
  → web-push-php verschlüsselt Payload mit VAPID
  → HTTP POST an jeden Browser Push Endpoint
  → Service Worker empfängt 'push' Event
  → Zeigt native Browser-Notification
  → User klickt → App öffnet sich
```

## API-Routen

| Method | Route | Controller | Beschreibung |
|--------|-------|-----------|-------------|
| GET | /push/public-key | PushController@publicKey | VAPID Public Key |
| POST | /push/subscribe | PushController@subscribe | Subscription speichern |
| POST | /push/unsubscribe | PushController@unsubscribe | Subscription löschen |

## Komponenten

### 1. Service Worker Erweiterung (`public/sw.js`)

Neue Event Listener:
- `push` — Payload parsen, native Notification anzeigen (Titel, Body, Icon, Badge)
- `notificationclick` — App öffnen / spezifische URL navigieren

### 2. Opt-In Popup (Blade Component)

- Glass-Card Modal im bestehenden Design-System
- Erscheint nach Login für User ohne Subscription
- "Aktivieren" (btn-primary) / "Später" (btn-ghost)
- "Später" speichert Dismiss-Timestamp in localStorage, erscheint nach 7 Tagen erneut
- Prüft `'PushManager' in window` und `'serviceWorker' in navigator`

### 3. Push-Controller (`PushController`)

- `publicKey()` — gibt VAPID Public Key zurück
- `subscribe()` — speichert PushSubscription für authentifizierten User
- `unsubscribe()` — löscht Subscription

### 4. Admin Push-UI

Neue Seite im Admin-Bereich:
- **Formular:** Titel (input) + Nachricht (textarea) + Empfänger (Select: Alle / Ortsverband-Dropdown)
- **Vorschau:** Zeigt wie die Notification aussehen wird
- **Senden:** Button mit Bestätigungsdialog
- **Historie:** Tabelle mit gesendeten Nachrichten (Titel, Empfänger, Anzahl, Datum)

### 5. System-Notifications erweitern

Bestehende Notification-Erstellung (LernsessionService, LeagueService, OrtsverbandInvitation) bekommt zusätzlich den WebPush-Channel:
- User erhält Push UND In-App Notification
- Nur wenn User eine aktive Subscription hat

## VAPID-Konfiguration

Neue `.env`-Keys:
```
VAPID_PUBLIC_KEY=...
VAPID_PRIVATE_KEY=...
```

Generierung via `php artisan webpush:vapid`

## Sicherheit

- Alle Push-Routen hinter `auth` Middleware
- Admin-Push nur für User mit Admin-Rolle
- VAPID Private Key nur serverseitig
- Subscriptions sind User-gebunden, kein Zugriff auf fremde Subscriptions
