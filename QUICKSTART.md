# 🚀 PWA & Push-Benachrichtigungen - Schnellstart

## ✅ Was wurde implementiert?

✔️ Progressive Web App (PWA) Support  
✔️ Push-Benachrichtigungen (nur in PWA)  
✔️ Automatische PWA-Erkennung  
✔️ Opt-in Dialog (nur in PWA)  
✔️ Profil-Einstellungen für Push  
✔️ DSGVO-konforme Datenschutzerklärung  
✔️ E-Mail-Benachrichtigungen dokumentiert  

---

## 🏃 Sofort loslegen (3 Schritte)

### 1️⃣ VAPID-Keys zur .env hinzufügen

**Option A: Automatisch (empfohlen)**
```bash
bash add-vapid-keys.sh
```

**Option B: Manuell**
Öffne `.env` und füge hinzu:
```env
# VAPID Keys für Push-Benachrichtigungen
VAPID_SUBJECT=mailto:niclas@thw-trainer.de
VAPID_PUBLIC_KEY=BBbF_AH9rF_1KPspaZ_blQgxkElPP3INrBBErFeNoVw7zyMj6m7Votl-UzPiq3u7Vib0OE02WseQkWfI07IQJ4s
VAPID_PRIVATE_KEY=ADU_xBryHePpnfumIR87CRNedFnTHrAsjZEGRTbQU50
```

### 2️⃣ Config-Cache aktualisieren
```bash
php artisan config:clear
```

### 3️⃣ Fertig! 🎉

Die Migration wurde bereits ausgeführt (Batch 21).  
Alle Dateien sind an Ort und Stelle.

---

## 📱 Testen

### PWA installieren (Chrome Desktop)
1. THW-Trainer öffnen: `http://localhost:8000` oder deine Domain
2. Adressleiste → Install-Icon (⊕) klicken
3. "Installieren" bestätigen
4. App öffnet sich als eigenständiges Fenster

### Push-Benachrichtigungen testen
1. **PWA muss installiert sein** und im Standalone-Modus laufen
2. Nach 3 Sekunden erscheint Banner: "Push-Benachrichtigungen aktivieren?"
3. Auf "Aktivieren" klicken
4. Browser fragt nach Permission → "Zulassen"
5. Im Profil → "Test-Benachrichtigung senden" klicken
6. Push sollte erscheinen! 🔔

### Alternativ: Profil-Einstellungen nutzen
1. Als User einloggen
2. Profil öffnen
3. **Nur in PWA sichtbar**: Gelber Bereich "Push-Benachrichtigungen"
4. "Push-Benachrichtigungen aktivieren" klicken
5. "Test-Benachrichtigung senden" klicken

---

## ⚠️ Wichtig: PWA vs. normaler Browser

| Feature | Normaler Browser | PWA (Standalone) |
|---------|-----------------|------------------|
| Push-Abfrage Banner | ❌ Nicht sichtbar | ✅ Erscheint nach 3 Sek |
| Push-Einstellungen im Profil | ❌ Versteckt | ✅ Sichtbar |
| Push-Benachrichtigungen | ❌ Nicht möglich | ✅ Funktioniert |
| Offline-Funktionen | ✅ Teilweise | ✅ Vollständig |

**Die Push-Abfrage erscheint NUR in der PWA!** Das ist Absicht und DSGVO-konform.

### 📱 iOS Safari - Besondere Hinweise

Auf iOS (iPhone/iPad) gelten **besondere Regeln**:

1. **iOS 16.4 oder neuer** erforderlich
2. App MUSS als PWA installiert sein (Safari → Teilen → Zum Home-Bildschirm)
3. App MUSS über **Home-Screen Icon** geöffnet werden (NICHT über Safari!)
4. Erst dann erscheint die Push-Abfrage

**Probleme auf iOS?** → Siehe ausführliche Anleitung: `IOS_PUSH_GUIDE.md`

### 🔍 Debug-Seite nutzen

Öffne in der PWA: `/push-debug`

Diese Seite zeigt dir:
- ✅ Läuft die App als PWA?
- ✅ Wird Push unterstützt?
- ✅ Welcher Permission-Status?
- 🔔 Button zum manuellen Aktivieren

**Für iOS-User besonders wichtig!**

---

## 🔒 Datenschutz (DSGVO)

### ✅ Was ist bereits erledigt?

1. **Datenschutzerklärung aktualisiert** (`resources/views/datenschutz.blade.php`)
   - Abschnitt 3.3: E-Mail-Benachrichtigungen (Opt-In)
   - Abschnitt 3.4: Push-Benachrichtigungen (nur PWA)
   - Abschnitt 7.2: Push-Dienste (FCM, APNs, Mozilla)

2. **Opt-In Mechanismus**
   - Push: Nur in PWA, User muss aktiv zustimmen
   - E-Mail: Checkbox im Profil, bereits implementiert

3. **Transparenz**
   - Klare Info über Datenverarbeitung
   - Links zu Datenschutzerklärungen von Google, Apple, Mozilla

4. **Kontrolle für User**
   - Jederzeit deaktivierbar
   - Im Profil einsehbar
   - Keine automatische Aktivierung

### E-Mail-Benachrichtigungen

**WICHTIG**: E-Mail-Benachrichtigungen waren bereits implementiert!

✅ Bereits DSGVO-konform:
- Opt-In via Checkbox im Profil
- Feld `email_consent` in Datenbank
- Zeitstempel `email_consent_at`
- Banner im Dashboard bei neuen Usern
- In Datenschutzerklärung dokumentiert

✅ Wird verwendet für:
- Newsletter (Admin)
- Inactive Reminders (Cronjob)
- Streak Reminders (Cronjob)

---

## 📂 Erstellte/Bearbeitete Dateien

### Neue Dateien
- ✅ `public/js/push-notifications.js` - JavaScript für PWA-Erkennung & Push
- ✅ `config/webpush.php` - VAPID-Konfiguration
- ✅ `app/Http/Controllers/PushNotificationController.php` - Backend-Logik
- ✅ `database/migrations/2025_10_21_090333_create_push_subscriptions_table.php` - Datenbank
- ✅ `PWA_PUSH_SETUP.md` - Ausführliche Dokumentation
- ✅ `VAPID_KEYS.txt` - Generierte VAPID-Keys (NICHT committen!)
- ✅ `add-vapid-keys.sh` - Script zum Hinzufügen der Keys
- ✅ `QUICKSTART.md` - Diese Datei

### Bearbeitete Dateien
- ✅ `resources/views/layouts/app.blade.php` - Script eingebunden
- ✅ `resources/views/profile.blade.php` - Push-Einstellungen
- ✅ `resources/views/datenschutz.blade.php` - Datenschutzerklärung erweitert
- ✅ `routes/web.php` - Push-Endpoints hinzugefügt
- ✅ `app/Models/User.php` - pushSubscriptions Relation
- ✅ `app/Models/PushSubscription.php` - Bereits vorhanden, keine Änderung nötig

---

## 🎯 Was kann ich jetzt machen?

### 1. Für User
- ✅ PWA installieren auf Smartphone/Desktop
- ✅ Push-Benachrichtigungen aktivieren (nur in PWA)
- ✅ Test-Benachrichtigung senden
- ✅ Im Profil verwalten

### 2. Für Admins
- 📧 E-Mail-Newsletter weiterhin nutzen (bereits vorhanden)
- 🔔 Push-Benachrichtigungen manuell senden (TODO: noch zu implementieren)
- 📊 Statistiken einsehen (TODO: noch zu implementieren)

### 3. Optional erweitern
Siehe `PWA_PUSH_SETUP.md` → Abschnitt "Nächste Schritte"

---

## 🐛 Troubleshooting

### Push-Banner erscheint nicht
➡️ Läuft die App als PWA? (Standalone-Modus)  
➡️ Check: `window.pushNotifications.isPWA()` in Browser-Konsole

### "Push nicht unterstützt"
➡️ HTTPS erforderlich (oder localhost)  
➡️ Service Worker muss aktiv sein  
➡️ Check: `navigator.serviceWorker.ready`

### VAPID-Keys funktionieren nicht
➡️ Config-Cache leeren: `php artisan config:clear`  
➡️ Keys in `.env` korrekt eingefügt?  
➡️ Server neu starten

### Profil zeigt keine Push-Einstellungen
➡️ Nur in PWA sichtbar!  
➡️ Im normalen Browser versteckt (by design)

---

## 📚 Weitere Dokumentation

- **Ausführliche Anleitung**: `PWA_PUSH_SETUP.md`
- **Datenschutz**: `resources/views/datenschutz.blade.php`
- **VAPID-Keys**: `VAPID_KEYS.txt` (NICHT committen!)

---

## ✨ Fertig!

Du kannst jetzt:
1. ✅ Die App als PWA nutzen
2. ✅ Push-Benachrichtigungen erhalten (nur in PWA)
3. ✅ DSGVO-konform E-Mails versenden

Viel Erfolg! 🚀

---

**Fragen?** → Siehe `PWA_PUSH_SETUP.md` → Abschnitt "Troubleshooting"
