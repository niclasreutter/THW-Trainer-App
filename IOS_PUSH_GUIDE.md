# 📱 iOS Safari PWA & Push - Wichtige Hinweise

## ⚠️ Problem: Push-Abfrage erscheint nicht auf iPhone

### Grund
Safari auf iOS hat **spezielle Anforderungen** für Push-Benachrichtigungen in PWAs:

1. **iOS Version**: Mindestens **iOS 16.4** erforderlich
2. **Installation**: App MUSS als PWA installiert sein
3. **Start**: App MUSS über **Home-Screen** geöffnet werden (NICHT über Safari!)
4. **Service Worker**: Muss korrekt registriert sein

---

## ✅ Schritt-für-Schritt Anleitung (iOS)

### 1️⃣ iOS Version prüfen
- Einstellungen → Allgemein → Info
- **iOS 16.4 oder neuer** erforderlich
- Falls älter: iOS aktualisieren

### 2️⃣ PWA installieren (Safari)
1. THW-Trainer Website in **Safari** öffnen
2. **Teilen-Button** (□↑) unten in der Mitte tippen
3. Nach unten scrollen
4. **"Zum Home-Bildschirm"** antippen
5. **"Hinzufügen"** bestätigen
6. App-Icon erscheint auf dem Home-Screen

### 3️⃣ App über Home-Screen öffnen
- ⚠️ **WICHTIG**: Die App MUSS über das Icon auf dem Home-Screen geöffnet werden!
- **NICHT** über Safari → Lesezeichen/Tabs öffnen
- Nur dann läuft sie als PWA im Standalone-Modus

### 4️⃣ Push-Abfrage sollte erscheinen
- Nach 3 Sekunden sollte der Banner erscheinen
- Falls nicht: Siehe Troubleshooting unten

---

## 🔍 Debug: Ist die App als PWA geöffnet?

Öffne die Debug-Seite in der PWA:
```
https://deine-domain.de/push-debug
```

**Was du sehen solltest (wenn alles korrekt ist):**
- ✅ iOS erkannt: **Ja**
- ✅ window.navigator.standalone: **true**
- ✅ Is PWA: **Ja**
- ✅ Push API: **Unterstützt**
- ✅ Notification API: **Unterstützt**

**Was du siehst wenn es NICHT als PWA läuft:**
- ❌ window.navigator.standalone: **false** oder **undefined**
- ❌ Is PWA: **Nein**
- ❌ Fehlermeldung: "APP LÄUFT NICHT ALS PWA!"

---

## 🐛 Troubleshooting

### Banner erscheint trotzdem nicht

**1. Cache leeren (in der PWA)**
- Auch PWAs haben einen Cache
- Schließe die App komplett (vom Home-Screen wegwischen)
- Neu öffnen

**2. PWA neu installieren**
- App vom Home-Screen löschen (langes Drücken → "App entfernen")
- Safari Cache leeren:
  - Safari öffnen
  - Einstellungen → Safari → "Verlauf und Websitedaten löschen"
- PWA neu installieren (siehe Schritt 2 oben)
- App über Home-Screen öffnen

**3. iOS-Einstellungen prüfen**
- Einstellungen → Safari → Erweitert → Experimentelle Features
- Stelle sicher dass nichts Push/Notifications blockiert

**4. Browser-Konsole prüfen (Safari Developer)**
Falls du einen Mac hast:
- iPhone per Kabel verbinden
- Mac: Safari → Entwickler → [Dein iPhone] → [THW-Trainer]
- Konsole öffnen und nach Fehlern suchen

### Push-Permission wird nicht abgefragt (iOS-Spezifisch)

iOS Safari hat eine **Besonderheit**:
- Push MUSS von einer **User-Aktion** ausgelöst werden
- Banner mit Button erfüllt diese Anforderung
- Falls Banner nicht erscheint: Manuell im Profil aktivieren

**Lösung:**
1. Im Profil scrollen
2. Gelber Bereich "🔔 Push-Benachrichtigungen (PWA)"
3. Auf "Push-Benachrichtigungen aktivieren" tippen
4. iOS fragt dann nach Permission

---

## 📊 Bekannte iOS-Limitierungen

### Was funktioniert:
- ✅ PWA-Installation
- ✅ Offline-Modus
- ✅ Push-Benachrichtigungen (ab iOS 16.4)
- ✅ Service Worker
- ✅ Benachrichtigungen auch wenn App geschlossen ist

### Was NICHT funktioniert:
- ❌ Push in normalem Safari-Browser (nur in PWA)
- ❌ Push auf iOS < 16.4
- ❌ Background Sync (noch nicht von Apple unterstützt)

---

## 🧪 Test-Ablauf

### Kompletter Test auf iOS

1. **Installation**
   ```
   Safari → THW-Trainer → Teilen → Zum Home-Bildschirm
   ```

2. **App öffnen**
   ```
   Home-Screen → THW-Trainer Icon antippen
   ```

3. **Debug-Seite öffnen**
   ```
   In der App: /push-debug aufrufen
   ```

4. **Status prüfen**
   - Alle Werte sollten ✅ grün sein
   - "Is PWA" muss "Ja" sein

5. **Push aktivieren**
   - Im Banner auf "Aktivieren" tippen
   - ODER im Profil auf "Push-Benachrichtigungen aktivieren"
   - iOS fragt nach Permission → "Zulassen"

6. **Test senden**
   - Im Profil auf "Test-Benachrichtigung senden"
   - Benachrichtigung sollte erscheinen (auch wenn App minimiert ist)

---

## 💡 Warum erscheint der Banner nicht?

**Mögliche Gründe:**

1. **Nicht als PWA geöffnet**
   - App über Safari-Tab geöffnet statt über Home-Screen Icon
   - Lösung: App schließen, über Home-Screen neu öffnen

2. **Banner wurde bereits dismissed**
   - localStorage hat "push_prompt_dismissed_at" gesetzt
   - Lösung: 7 Tage warten ODER localStorage leeren:
     ```javascript
     // In Browser-Konsole:
     localStorage.removeItem('push_prompt_dismissed_at');
     ```

3. **Permission bereits granted oder denied**
   - Wenn bereits aktiviert/blockiert, erscheint kein Banner
   - Lösung: Im Profil den Status prüfen

4. **iOS-Version zu alt**
   - Push benötigt iOS 16.4+
   - Lösung: iOS Update

5. **Service Worker nicht geladen**
   - PWA wurde nicht korrekt installiert
   - Lösung: PWA neu installieren (siehe oben)

---

## 🔧 Manuelle Aktivierung (Falls Banner nicht erscheint)

### Option 1: Über Profil
1. In der PWA einloggen
2. Profil öffnen
3. Nach unten scrollen
4. Gelber Bereich: "🔔 Push-Benachrichtigungen (PWA)"
5. "Push-Benachrichtigungen aktivieren" antippen

### Option 2: Über Debug-Seite
1. `/push-debug` aufrufen
2. "🔔 Push-Benachrichtigungen aktivieren" Button
3. Permission erlauben

### Option 3: Browser-Konsole (für Entwickler)
```javascript
window.pushNotifications.requestPushPermission()
```

---

## 📝 Häufige Fehler & Lösungen

| Fehler | Ursache | Lösung |
|--------|---------|--------|
| "Is PWA: Nein" | App über Safari statt Home-Screen geöffnet | App schließen, über Home-Screen öffnen |
| "Push API: Nicht unterstützt" | iOS < 16.4 | iOS aktualisieren |
| "Permission: Blockiert" | User hat Push abgelehnt | iOS-Einstellungen → THW-Trainer → Benachrichtigungen aktivieren |
| Banner erscheint nicht | Bereits dismissed oder granted | localStorage leeren oder im Profil aktivieren |
| "Service Worker failed" | Installation fehlerhaft | PWA neu installieren |

---

## 📞 Support

Falls es immer noch nicht funktioniert:

1. **Screenshot von Debug-Seite** (`/push-debug`) machen
2. **iOS-Version** notieren
3. **Beschreibung** was genau nicht funktioniert
4. An Support senden mit allen Infos

---

## ✅ Checkliste

Stelle sicher dass:
- [ ] iOS 16.4 oder neuer
- [ ] App als PWA installiert (Teilen → Zum Home-Bildschirm)
- [ ] App über Home-Screen Icon geöffnet (NICHT über Safari)
- [ ] `/push-debug` zeigt "Is PWA: Ja"
- [ ] `/push-debug` zeigt "Push API: Unterstützt"
- [ ] Banner wurde nicht dismissed (localStorage prüfen)
- [ ] Permission nicht bereits denied

Wenn alle Punkte ✅ sind, sollte der Banner erscheinen!

---

## 🎯 Erwartetes Verhalten

### Beim ersten Öffnen der PWA (iOS)

1. **0-3 Sekunden**: PWA lädt
2. **Nach 3 Sekunden**: Banner erscheint unten
3. **User tippt "Aktivieren"**: iOS fragt nach Permission
4. **User tippt "Zulassen"**: Push aktiviert
5. **Fertig!**: Test-Benachrichtigung kann gesendet werden

### Falls Banner nicht erscheint

1. Debug-Seite öffnen: `/push-debug`
2. Alle Werte prüfen
3. Falls "Is PWA: Nein" → App über Home-Screen öffnen
4. Falls "Push API: Nicht unterstützt" → iOS aktualisieren
5. Sonst: Im Profil manuell aktivieren

---

**Stand:** 21. Oktober 2025  
**Getestet auf:** iOS 16.4 - iOS 17.x
