# Feature: Einzelfragen-Ansicht in der Prüfung

## Übersicht
Die Prüfungsansicht wurde komplett überarbeitet, um eine bessere Benutzererfahrung zu bieten. Statt alle 40 Fragen auf einer langen scrollbaren Seite anzuzeigen, wird jetzt eine Frage pro Seite dargestellt.

## Hauptmerkmale

### 1. Eine Frage pro Seite
- ✅ Übersichtliche Darstellung ohne Scrollen
- ✅ Fokus auf die aktuelle Frage
- ✅ Klare Navigation zwischen Fragen

### 2. Fixierte Navigation
- **Oben:** Timer und Fortschrittsbalken (immer sichtbar)
- **Mitte:** Aktuell angezeigte Frage (scrollbar bei langen Texten)
- **Unten:** Navigationsbuttons (fixiert am unteren Rand)

### 3. Fragenübersicht
Kompakte Übersicht über alle 40 Fragen mit Status-Indikatoren:

```
┌─────────────────────────────┐
│ [1✅][2✅][3⚪][4🔖][5✅]... │
│ ✅ = Beantwortet            │
│ ⚪ = Offen                  │
│ 🔖 = Markiert               │
└─────────────────────────────┘
```

#### Status-Farben:
- **Grün (✅):** Frage wurde beantwortet
- **Grau (⚪):** Frage wurde noch nicht beantwortet
- **Gelb (🔖):** Frage wurde zur Überprüfung markiert
- **Blauer Rand:** Aktuell angezeigte Frage

### 4. Markierungsfunktion
- Jede Frage kann mit einem 🔖-Button markiert werden
- Markierte Fragen sind in der Übersicht gelb hervorgehoben
- Nützlich, um unsichere Antworten später nochmal zu prüfen

### 5. Abschluss-Übersicht
Nach der letzten Frage oder beim Klick auf "Nächste" erscheint eine Übersichtsseite mit:
- Anzahl beantworteter Fragen (z.B. "35 von 40 beantwortet")
- Liste der noch offenen Fragen (z.B. "Fragen: 3, 12, 15, 28, 40")
- Anzahl markierter Fragen
- Buttons: "Zurück zur Prüfung" oder "Prüfung abgeben"

### 6. Timer mit Warnung
- 30-Minuten-Countdown (wie bisher)
- Bei 5 Minuten verbleibender Zeit: Rote Farbe mit Puls-Animation
- Bei Ablauf der Zeit: Automatische Abgabe

### 7. Fortschrittsbalken
- Zeigt grafisch den Fortschritt an (z.B. "15/40 beantwortet")
- Wird automatisch aktualisiert bei jeder Antwort
- Immer sichtbar im oberen Bereich

## Technische Details

### Layout-Struktur
```
┌─────────────────────────────────┐
│ Header (fixiert)                │
│ - Titel + Timer                 │
│ - Fortschrittsbalken            │
├─────────────────────────────────┤
│ Fragenbereich (scrollbar)       │
│ - Aktuell angezeigte Frage      │
│ - Antwortoptionen               │
│ - Markieren-Button              │
├─────────────────────────────────┤
│ Navigation (fixiert)            │
│ - Vorherige/Nächste Buttons     │
│ - Fragenübersicht (klappbar)    │
└─────────────────────────────────┘
```

### CSS-Besonderheiten
- `body { overflow: hidden; }` - Verhindert Scrollen der Seite
- `.exam-container { height: 100vh; }` - Nimmt volle Viewport-Höhe
- Flexbox-Layout für fixierte Header/Footer
- Nur der Fragenbereich ist scrollbar

### JavaScript-Funktionen
- `showQuestion(index)` - Zeigt spezifische Frage an
- `nextQuestion()` - Navigation zur nächsten Frage
- `previousQuestion()` - Navigation zur vorherigen Frage
- `goToQuestion(index)` - Direkte Navigation über Übersicht
- `updateAnswerStatus(index)` - Aktualisiert Status nach Antwort
- `toggleMark(index)` - Markiert/Entmarkiert eine Frage
- `updateProgress()` - Aktualisiert Fortschrittsbalken
- `toggleOverview()` - Blendet Fragenübersicht ein/aus
- `showSubmitOverview()` - Zeigt Abschluss-Übersicht
- `submitExam()` - Gibt Prüfung ab

### State Management
```javascript
let currentQuestion = 0;           // Aktuell angezeigte Frage (0-39)
let answers = Array(40).fill(false); // Ob Frage beantwortet wurde
let marked = Array(40).fill(false);  // Ob Frage markiert ist
let timeLeft = 30 * 60;             // Verbleibende Zeit in Sekunden
```

## Vorteile gegenüber alter Version

### Alt (alle Fragen auf einer Seite)
- ❌ Lange scrollbare Seite → unübersichtlich
- ❌ Schwierig, Überblick zu behalten
- ❌ Timer scrollt mit → nicht immer sichtbar
- ❌ Keine Markierungsfunktion
- ❌ Keine Übersicht über Status aller Fragen

### Neu (eine Frage pro Seite)
- ✅ Fokussierte, übersichtliche Darstellung
- ✅ Kein Scrollen nötig (außer bei sehr langen Fragen)
- ✅ Timer und Fortschritt immer sichtbar
- ✅ Markierungsfunktion für unsichere Antworten
- ✅ Visuelle Übersicht über alle 40 Fragen
- ✅ Direkte Navigation zu beliebigen Fragen
- ✅ Warnung bei noch offenen Fragen vor Abgabe

## Responsive Design
- Mobile: Optimiert für kleine Bildschirme
- Tablet: Fragenübersicht passt sich an
- Desktop: Volle Features mit großen Klickflächen

## Browser-Kompatibilität
- Moderne CSS (Flexbox, Grid)
- JavaScript ES6+ Features
- Getestet in: Chrome, Firefox, Safari, Edge

## Migration

### Backup
Die alte Version wurde gesichert in:
```
resources/views/exam-old-backup.blade.php
```

### Rollback (falls nötig)
```bash
cd /Users/niclasreutter/THW-Trainer-App
mv resources/views/exam-old-backup.blade.php resources/views/exam.blade.php
```

## Zukünftige Erweiterungen (Optional)

### Mögliche Verbesserungen:
1. **Keyboard-Navigation**
   - Pfeiltasten für Vor/Zurück
   - Zahlen 1-9 für Markierung
   - Enter für Weiter

2. **Lokale Speicherung**
   - Automatisches Speichern der Antworten im Browser
   - Wiederherstellung bei Verbindungsabbruch

3. **Statistiken während der Prüfung**
   - Durchschnittliche Zeit pro Frage
   - Verbleibende Zeit pro Frage bei gleichmäßiger Verteilung

4. **Erweiterte Markierungen**
   - Verschiedene Markierungsarten (unsicher, nochmal prüfen, etc.)
   - Notizen zu Fragen

5. **Touch-Gesten** (Mobile)
   - Wischen für Vor/Zurück
   - Doppeltippen für Markierung

## Änderungsverlauf

**Version 2.0 - 21. Oktober 2025**
- Komplette Neugestaltung der Prüfungsansicht
- Eine Frage pro Seite statt aller Fragen auf einer Seite
- Fragenübersicht mit Status-Indikatoren
- Markierungsfunktion
- Abschluss-Übersicht vor Abgabe
- Fixierte Navigation und Timer
- Verbesserte UX ohne Scrollen

**Version 1.0 - Ursprüngliche Version**
- Alle 40 Fragen auf einer scrollbaren Seite
- Gesichert in: exam-old-backup.blade.php
