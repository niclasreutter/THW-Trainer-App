# 📱 PWA & Push-Benachrichtigungen Setup

## 📋 Übersicht

Das THW-Trainer-System unterstützt jetzt:
- **Progressive Web App (PWA)** - Installation als eigenständige App möglich
- **Push-Benachrichtigungen** - Nur in der PWA verfügbar, vollständig Opt-In basiert
- **DSGVO-konform** - Separate Abfrage nur im PWA-Modus

---

## ✨ Features

### PWA (Progressive Web App)
- **Installation**: Auf Smartphone/Desktop installierbar
- **Offline-Fähigkeit**: Grundfunktionen auch ohne Internet nutzbar
- **App-ähnliches Erlebnis**: Standalone-Modus ohne Browser-UI
- **Service Worker**: Caching für schnellere Ladezeiten

### Push-Benachrichtigungen
- **Nur in PWA**: Abfrage erscheint ausschließlich im Standalone-Modus
- **Opt-In basiert**: User muss aktiv zustimmen
- **Einstellbar**: Aktivierung/Deaktivierung über Profil
- **Test-Funktion**: User kann Test-Benachrichtigung senden
- **Multi-Device**: Unterstützt mehrere Geräte pro User

---

## 🚀 Installation & Setup

### 1. VAPID-Keys in `.env` hinzufügen

Die VAPID-Keys wurden bereits generiert. Füge sie zu deiner `.env` hinzu:

```env
# VAPID Keys für Push-Benachrichtigungen
VAPID_SUBJECT=mailto:niclas@thw-trainer.de
VAPID_PUBLIC_KEY=BBbF_AH9rF_1KPspaZ_blQgxkElPP3INrBBErFeNoVw7zyMj6m7Votl-UzPiq3u7Vib0OE02WseQkWfI07IQJ4s
VAPID_PRIVATE_KEY=ADU_xBryHePpnfumIR87CRNedFnTHrAsjZEGRTbQU50
```

**WICHTIG**: Die Datei `VAPID_KEYS.txt` enthält diese Keys - bitte aus Sicherheitsgründen NICHT in Git committen!

### 2. Migration ausführen

```bash
php artisan migrate
```

Dies erstellt die Tabelle `push_subscriptions` für die Speicherung der Push-Tokens.

### 3. Composer-Packages installiert

Das Package `minishlink/web-push` wurde bereits installiert via:
```bash
composer require minishlink/web-push
```

---

## 📱 PWA Installation (für User)

### Android (Chrome/Edge)
1. Website öffnen
2. Menu → "Zum Startbildschirm hinzufügen"
3. App wird wie native App installiert

### iOS (Safari)
1. Website öffnen in Safari
2. Teilen-Button → "Zum Home-Bildschirm"
3. App erscheint auf dem Home-Screen

### Desktop (Chrome/Edge)
1. Website öffnen
2. Adressleiste → Install-Icon klicken
3. "Installieren" bestätigen

---

## 🔔 Push-Benachrichtigungen

### Wie funktioniert es?

1. **PWA-Erkennung**: JavaScript prüft ob App als PWA läuft
2. **Automatische Abfrage**: Nach 3 Sekunden erscheint Banner (nur in PWA!)
3. **User-Zustimmung**: User kann aktivieren oder ablehnen
4. **Subscription**: Token wird an Backend gesendet und gespeichert
5. **Versand**: Push-Benachrichtigungen können gesendet werden

### JavaScript-Integration

Die Datei `/public/js/push-notifications.js` enthält alle Funktionen:

```javascript
// PWA-Check
window.pushNotifications.isPWA()

// Push aktivieren
window.pushNotifications.requestPushPermission()

// Push deaktivieren
window.pushNotifications.unsubscribeFromPush()

// Test-Benachrichtigung senden
window.pushNotifications.sendTestPushNotification()
```

### Backend-Endpoints

```php
// VAPID Public Key abrufen
GET /push/vapid-public-key

// Push-Subscription speichern
POST /push/subscribe
{
  "endpoint": "https://...",
  "keys": {
    "p256dh": "...",
    "auth": "..."
  }
}

// Push-Subscription löschen
POST /push/unsubscribe
{
  "endpoint": "https://..."
}

// Test-Benachrichtigung senden
POST /push/test
```

---

## 🎨 User-Interface

### Automatischer Banner (nur in PWA)
- Erscheint 3 Sekunden nach Seitenaufruf
- Nur wenn nicht bereits aktiviert/abgelehnt
- Kann für 7 Tage ausgeblendet werden

### Profil-Einstellungen
- Bereich nur sichtbar in PWA
- Push-Status-Anzeige
- Aktivieren/Deaktivieren-Button
- Test-Benachrichtigung-Button

---

## 📊 Datenbank-Schema

### `push_subscriptions` Tabelle

| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | bigint | Primary Key |
| user_id | bigint | Foreign Key zu users |
| endpoint | varchar(500) | Push-Endpoint-URL |
| public_key | text | P256DH Public Key |
| auth_token | text | Auth Token |
| content_encoding | varchar | Encoding-Typ (default: aesgcm) |
| is_active | boolean | Aktiv/Inaktiv |
| last_used_at | timestamp | Letzte Nutzung |
| created_at | timestamp | Erstellt am |
| updated_at | timestamp | Aktualisiert am |

**Unique Constraint**: `user_id` + `endpoint` (ein User kann mehrere Devices haben)

---

## 🔒 Datenschutz (DSGVO)

### ✅ Was wurde berücksichtigt?

1. **Opt-In Pflicht**
   - User muss aktiv zustimmen
   - Keine automatische Aktivierung
   - Nur Abfrage in PWA-Modus

2. **Transparenz**
   - Datenschutzerklärung aktualisiert
   - Klare Info über Datenverarbeitung
   - Hinweis auf externe Push-Services (FCM, APNs, etc.)

3. **Kontrolle für User**
   - Jederzeit deaktivierbar
   - Profil-Einstellungen
   - Push-Status einsehbar

4. **E-Mail-Benachrichtigungen**
   - Bereits vorhanden und DSGVO-konform
   - Separates Opt-In via `email_consent`
   - Ebenfalls in Datenschutzerklärung dokumentiert

### Datenschutzerklärung

Die Datenschutzerklärung (`resources/views/datenschutz.blade.php`) wurde erweitert um:

- **3.3 E-Mail-Benachrichtigungen (Opt-In)**
  - Art der E-Mails
  - Rechtsgrundlage (Art. 6 Abs. 1 lit. a DSGVO)
  - Widerrufsmöglichkeit

- **3.4 Push-Benachrichtigungen (nur in der PWA)**
  - Gespeicherte Daten (Endpoint, Keys)
  - Nur in PWA-Modus
  - Rechtsgrundlage (Art. 6 Abs. 1 lit. a DSGVO)
  - Deaktivierungsmöglichkeit

- **7.2 Push-Dienste**
  - Google FCM (Chrome/Edge/Opera)
  - Apple APNs (Safari)
  - Mozilla Push (Firefox)
  - Links zu Datenschutzerklärungen

---

## 🧪 Testing

### Push-Benachrichtigung testen

1. **Als User**:
   - PWA installieren
   - Push-Benachrichtigungen aktivieren
   - Im Profil auf "Test-Benachrichtigung senden" klicken
   - Benachrichtigung sollte erscheinen

2. **Via Backend**:
   ```php
   use App\Models\User;
   use App\Http\Controllers\PushNotificationController;
   
   $user = User::find(1);
   $controller = new PushNotificationController();
   $request = new Request();
   
   // Simuliere authentifizierten User
   auth()->login($user);
   
   $result = $controller->sendTest($request);
   ```

### PWA-Modus prüfen

```javascript
// In Browser-Konsole
console.log('Is PWA:', window.pushNotifications.isPWA());
console.log('Push supported:', window.pushNotifications.isPushSupported());
console.log('Permission:', window.pushNotifications.getPushPermission());
```

---

## 🛠️ Technische Details

### Service Worker

Datei: `/public/sw.js`

Bereits vorhanden und erweitert mit:
- Push-Event-Handler
- Notification-Click-Handler
- Offline-Caching

### Web Push Library

- **Package**: `minishlink/web-push`
- **Version**: ^9.0
- **Dokumentation**: https://github.com/web-push-libs/web-push-php

### Browser-Kompatibilität

| Browser | PWA | Push |
|---------|-----|------|
| Chrome (Desktop) | ✅ | ✅ |
| Chrome (Android) | ✅ | ✅ |
| Edge | ✅ | ✅ |
| Safari (iOS 16.4+) | ✅ | ✅ |
| Safari (macOS) | ✅ | ✅ |
| Firefox | ✅ | ✅ |
| Opera | ✅ | ✅ |

---

## 📧 E-Mail-Benachrichtigungen

### Bestehende Implementierung

**WICHTIG**: E-Mail-Benachrichtigungen sind bereits implementiert und DSGVO-konform!

### Wo wird es verwendet?

1. **Newsletter-System**
   - Nur an User mit `email_consent = true`
   - Admin kann Newsletter versenden
   - Route: `/admin/newsletter/create`

2. **Inactive Reminders**
   - Erinnerung bei Inaktivität
   - Respektiert `email_consent`
   - Cronjob gesteuert

3. **Streak Reminders**
   - Erinnerung an Streak-Verlust
   - Opt-In via `email_consent`
   - Cronjob gesteuert

### Opt-In Mechanismus

- Checkbox im Profil: "Ich möchte E-Mail-Benachrichtigungen erhalten"
- Feld in Datenbank: `users.email_consent` (boolean)
- Zeitstempel: `users.email_consent_at` (timestamp)
- Banner im Dashboard bei neuen Usern

### Datenschutzkonform?

✅ **JA** - E-Mail-Benachrichtigungen müssen in die Datenschutzerklärung aufgenommen werden!

Bereits erledigt in `/resources/views/datenschutz.blade.php`:
- Abschnitt 3.3: E-Mail-Benachrichtigungen (Opt-In)
- Rechtsgrundlage: Art. 6 Abs. 1 lit. a DSGVO
- Widerrufsmöglichkeit dokumentiert

---

## 💡 Best Practices

### Wann Push versenden?

**Gute Use Cases:**
- ✅ Wichtige System-Updates
- ✅ Neue Features-Ankündigungen
- ✅ Lernfortschritt-Meilensteine
- ✅ Streak-Erinnerungen
- ✅ Prüfungsergebnisse

**Vermeiden:**
- ❌ Zu häufige Benachrichtigungen (Spam)
- ❌ Marketing-Push ohne Mehrwert
- ❌ Nachts senden (außer wichtig)

### Frequency Capping

Empfohlen: Max. 1-2 Push-Benachrichtigungen pro Woche

### User Experience

- **Timing**: Nicht sofort nach Installation nerven
- **Relevanz**: Nur senden wenn relevant
- **Personalisierung**: Name, Lernfortschritt, etc. nutzen
- **Abmelde-Option**: Immer klar kommunizieren

---

## 🚨 Troubleshooting

### Push-Benachrichtigung erscheint nicht

**1. PWA-Modus prüfen**
```javascript
console.log('PWA:', window.pushNotifications.isPWA());
```
→ Muss `true` sein!

**2. Permission prüfen**
```javascript
console.log('Permission:', Notification.permission);
```
→ Muss `'granted'` sein

**3. Subscription prüfen**
```javascript
navigator.serviceWorker.ready.then(reg => {
  reg.pushManager.getSubscription().then(sub => {
    console.log('Subscription:', sub);
  });
});
```

**4. Backend-Logs prüfen**
```bash
tail -f storage/logs/laravel.log
```

### VAPID-Keys funktionieren nicht

**Neu generieren:**
```bash
php artisan tinker
```
```php
\Minishlink\WebPush\VAPID::createVapidKeys()
```

### Browser zeigt keine Install-Option

- HTTPS erforderlich (oder localhost)
- Manifest.json muss korrekt sein
- Service Worker muss registriert sein
- Minimum: 2 Besuche innerhalb von 5 Minuten

---

## 📞 Support

Bei Problemen:
1. Browser-Konsole prüfen (F12)
2. Laravel-Logs prüfen (`storage/logs/laravel.log`)
3. VAPID-Keys in `.env` überprüfen
4. Service Worker neu registrieren (Cache leeren)

---

## 🎯 Nächste Schritte

### Optional erweitern

1. **Automatische Push bei Events**
   - Nach bestandener Prüfung
   - Bei neuem Achievement
   - Bei Streak-Verlust-Warnung

2. **Admin-Interface für Push**
   - Manuell Push an alle/bestimmte User senden
   - Ähnlich wie Newsletter-System
   - Zeitplanung für Push

3. **Push-Statistiken**
   - Erfolgreich zugestellt
   - Geklickt
   - Deaktiviert

4. **A/B-Testing**
   - Verschiedene Texte testen
   - Beste Zeit ermitteln
   - Engagement messen

---

## 📝 Changelog

### 2025-10-21 - Initial Release
- ✅ PWA-Unterstützung
- ✅ Push-Benachrichtigungen (nur PWA)
- ✅ VAPID-Keys generiert
- ✅ Backend-Endpoints erstellt
- ✅ JavaScript-Integration
- ✅ Profil-Einstellungen
- ✅ Datenschutzerklärung aktualisiert
- ✅ E-Mail-Benachrichtigungen dokumentiert

---

## 📄 Lizenz

Dieses Feature ist Teil des THW-Trainer-Projekts.

© 2025 Niclas Reutter - Alle Rechte vorbehalten.
