# 📚 Lehrgang System (Courses)

## Übersicht

Das **Lehrgang System** ermöglicht Benutzern, strukturierte Kurse zu belegen mit:
- 📖 Organisierten Lernabschnitten und Fragen
- 🎯 Persönliches Tracking von Fortschritt und Punkten
- 🏆 Gamification mit Punkte-Vergabe
- 📊 Dashboard-Integration zur Überwachung mehrerer Kurse

## Architektur

### Database-Tabellen

1. **lehrgaenge** - Haupttabelle für Kurse
   - `id` - Eindeutige ID
   - `lehrgang` - Name des Kurses (einzigartig)
   - `slug` - URL-freundliche Version (einzigartig)
   - `beschreibung` - Kursbeschreibung
   - `ziel_punkte` - Punkte zum Bestehen des Kurses
   - `timestamps` - Created/Updated At

2. **lehrgaenge_lernabschnitte** - Kursabschnitte/Lektionen
   - `id` - Eindeutige ID
   - `lehrgang_id` - FK zu lehrgaenge
   - `lernabschnitt_nr` - Abschnittsnummer
   - `lernabschnitt` - Abschnittsname
   - `timestamps`
   - **Unique**: [lehrgang_id, lernabschnitt_nr]

3. **lehrgaenge_questions** - Kursfragen
   - `id` - Eindeutige ID
   - `lehrgang_id` - FK zu lehrgaenge
   - `lernabschnitt` - Abschnittsnummer
   - `nummer` - Fragennummer im Abschnitt
   - `frage` - Fragetext
   - `antwort_a`, `antwort_b`, `antwort_c` - Antwortoptionen
   - `loesung` - Korrekte Antwort(en) (A, B, C oder komma-getrennt)
   - `timestamps`
   - **Index**: [lehrgang_id, lernabschnitt] für Performance

4. **user_lehrgaenge** - Benutzer-Kurs-Anmeldungen
   - `id` - Eindeutige ID
   - `user_id` - FK zu users
   - `lehrgang_id` - FK zu lehrgaenge
   - `punkte` - Vom Benutzer verdiente Punkte
   - `completed` - Boolean: Ist der Kurs abgeschlossen?
   - `enrolled_at` - Anmeldungsdatum
   - `completed_at` - Abschluss-Datum
   - `timestamps`
   - **Unique**: [user_id, lehrgang_id] - Verhindert doppelte Anmeldungen

5. **user_lehrgang_progress** - Detaillierter Fragenprogress pro Benutzer/Kurs
   - `id` - Eindeutige ID
   - `user_id` - FK zu users
   - `lehrgang_question_id` - FK zu lehrgaenge_questions
   - `consecutive_correct` - Zähler für aufeinanderfolgende richtige Antworten
   - `solved` - Boolean: Frage gelöst (2x richtig)?
   - `failed` - Boolean: Wurde mindestens einmal falsch beantwortet?
   - `timestamps`
   - **Unique**: [user_id, lehrgang_question_id] - Ein Einträg pro Benutzer/Frage

### Models

#### Lehrgang
```php
// Beziehungen
$lehrgang->questions()           // hasMany LehrgangQuestion
$lehrgang->lernabschnitte()      // hasMany LehrgangLernabschnitt
$lehrgang->users()               // belongsToMany User via pivot
```

#### LehrgangQuestion
```php
// Beziehungen
$question->lehrgang()            // belongsTo Lehrgang
$question->userProgress()        // hasMany UserLehrgangProgress
$question->getSolutionArray()    // Helper: Parst Lösung als Array
```

#### UserLehrgangProgress
```php
// Beziehungen
$progress->user()                // belongsTo User
$progress->lehrgangQuestion()    // belongsTo LehrgangQuestion
```

#### User (erweitert)
```php
// Neue Beziehungen
$user->enrolledLehrgaenge()      // belongsToMany Lehrgang
$user->lehrgangProgress()        // hasMany UserLehrgangProgress
```

## Routes

Alle Routes sind unter `/lehrgaenge` verfügbar und erfordern Authentifizierung:

### GET /lehrgaenge
- **Name**: `lehrgaenge.index`
- **Beschreibung**: Zeigt Liste aller verfügbaren Kurse
- **Return**: `lehrgaenge/index.blade.php`

### GET /lehrgaenge/{slug}
- **Name**: `lehrgaenge.show`
- **Beschreibung**: Zeigt Kursdetails, Fortschritt und Enrollment-Button
- **Parameters**: `slug` - Kurs-Slug
- **Return**: `lehrgaenge/show.blade.php`

### POST /lehrgaenge/{slug}/enroll
- **Name**: `lehrgaenge.enroll`
- **Beschreibung**: Meldet Benutzer in Kurs an
- **Parameters**: `slug` - Kurs-Slug
- **Redirect**: Zur Practice-Seite oder mit Fehlermeldung zurück

### GET /lehrgaenge/{slug}/practice
- **Name**: `lehrgaenge.practice`
- **Beschreibung**: Zeigt nächste Frage zum Üben
- **Parameters**: `slug` - Kurs-Slug
- **Return**: `lehrgaenge/practice.blade.php` oder `lehrgaenge/complete.blade.php`

### POST /lehrgaenge/{slug}/submit
- **Name**: `lehrgaenge.submit`
- **Beschreibung**: Verarbeitet Antwort, aktualisiert Fortschritt, vergibt Punkte
- **Parameters**: 
  - `slug` - Kurs-Slug
  - `question_id` - Fragen-ID
  - `answer` - Benutzerantwort (A, B, oder C)
- **Return**: JSON mit Ergebnis und Gamification-Daten

### POST /lehrgaenge/{slug}/unenroll
- **Name**: `lehrgaenge.unenroll`
- **Beschreibung**: Meldet Benutzer aus Kurs ab und löscht seinen Fortschritt
- **Parameters**: `slug` - Kurs-Slug
- **Redirect**: Zurück zur Kurs-Seite mit Bestätigungsmeldung

## Lernmechanik

### Abschlussregel (Ähnlich wie Practice)
- Jede Frage muss **2x richtig in Folge** beantwortet werden
- `consecutive_correct` Counter wird bei richtiger Antwort erhöht, bei falscher auf 0 gesetzt
- Bei `consecutive_correct == 2` wird die Frage als `solved = true` markiert

### Punkte-System
- **Basis-Punkte**: 10 Punkte pro gelöster Frage
- Punkte werden **GamificationService** angerechnet
- Benutzer-Punkte im Kurs aktualisiert in `user_lehrgaenge.punkte`

### Kurs-Abschluss
- Ein Kurs ist abgeschlossen wenn **alle Fragen gelöst sind** (solved=true)
- `user_lehrgaenge.completed = true`
- `user_lehrgaenge.completed_at` wird auf `now()` gesetzt
- Abgeschlossene Kurse zeigen ✓ Badge im Dashboard

## Gamification Integration

Der `LehrgangController` integriert den `GamificationService`:

```php
$gamification = new GamificationService();

// Beim Lösen einer Frage
$gamification->awardPoints($user, $points, "Lehrgang: {$lehrgang->lehrgang}");
```

### Toast Notifications
- Zeigen sofortiges Feedback mit farbcodierten Meldungen
- ✓ Richtig (Grün)
- ❌ Falsch (Rot)
- 🎉 Frage gelöst (Gelb)

### Achievement Unlocking
- Wenn Kurs abgeschlossen: Achievement-String
- Beispiel: "Grundlagen der Sicherheit abgeschlossen!"

## Dashboard Integration

Das Dashboard zeigt:
- Alle angemeldeten Kurse des Benutzers
- Fortschrittsbalken (gelöste Fragen / Gesamtfragen)
- Aktuelle Punkte / Ziel-Punkte
- "Weitermachen" Button für aktive Kurse
- "Abgeschlossen" Badge für fertige Kurse
- Link zu `/lehrgaenge` für mehr Kurse

## Seed-Daten

Der `LehrgangSeeder` erstellt Test-Daten:

### Lehrgang 1: Grundlagen der Sicherheit
- URL: `/lehrgaenge/grundlagen-sicherheit`
- Ziel: 50 Punkte
- Inhalte: 5 Fragen in 2 Abschnitten

### Lehrgang 2: Technische Rettung
- URL: `/lehrgaenge/technische-rettung`
- Ziel: 70 Punkte
- Inhalte: 3 Fragen in 2 Abschnitten

**Seeder ausführen:**
```bash
php artisan db:seed --class=LehrgangSeeder
```

## Verwendungsbeispiel

### Als Admin - Neuen Kurs hinzufügen

```bash
# Via Tinker
php artisan tinker

// Neuen Kurs erstellen
$lehrgang = App\Models\Lehrgang::create([
    'lehrgang' => 'Neue Ausbildung',
    'slug' => 'neue-ausbildung',
    'beschreibung' => 'Kursbeschreibung',
    'ziel_punkte' => 100,
]);

// Fragen hinzufügen
$lehrgang->questions()->create([
    'lernabschnitt' => 1,
    'nummer' => 1,
    'frage' => 'Beispielfrage?',
    'antwort_a' => 'Antwort A',
    'antwort_b' => 'Antwort B',
    'antwort_c' => 'Antwort C',
    'loesung' => 'A',
]);
```

### Als Benutzer - Kurs belegen

1. Navigiere zu `/lehrgaenge`
2. Klicke "Details anschauen" bei einem Kurs
3. Klicke "Jetzt beitreten" (POST zu `/lehrgaenge/{slug}/enroll`)
4. Beantworte Fragen in `/lehrgaenge/{slug}/practice`
5. Nach 2x richtig: Nächste Frage
6. Nach allen Fragen gelöst: Completion Screen

## Frontend Features

### Practice-Seite (lehrgaenge/practice.blade.php)
- Multiple Choice Fragen (A, B, C)
- Radio-Button Selection
- AJAX Form Submission
- Toast Notifications mit Feedback
- Auto-Reload nach Antwort
- Fortschrittsanzeige

### Index-Seite (lehrgaenge/index.blade.php)
- Kursliste mit Cards
- Enrollment-Status Badges
- Ziel-Punkte Anzeige
- "Details anschauen" und "Weitermachen" Buttons

### Show-Seite (lehrgaenge/show.blade.php)
- Kursinformation und Beschreibung
- Fortschrittsbalken
- Lernabschnitte-Übersicht
- Für nicht-angemeldete: Enroll-Button
- Für angemeldete: Practice-Button und Abmelden-Option

### Complete-Seite (lehrgaenge/complete.blade.php)
- Celebration Screen nach allen Fragen
- Statistik-Anzeige
- Links zu weiteren Kursen

## Fehlerbehandlung

- Ungültige Kurse: 404 Fehler
- Nicht angemeldet + versucht zu üben: Redirect mit Error-Message
- Ungültige Frage-ID: 400 JSON-Response
- Doppel-Anmeldung: Info-Message

## Performance-Optimierungen

- Index auf `lehrgaenge_questions.lehrgang_id, lernabschnitt` für schnelle Abfragen
- Lazy-Loading von Beziehungen wo möglich
- Query-Optimierung mit `whereHas()` für Progress-Zählung
- Unique Constraints verhindern Duplikate

## Zukünftige Erweiterungen

- [ ] Offline-Support (Service Worker)
- [ ] Fortschritt-Export (PDF/CSV)
- [ ] Leaderboard pro Lehrgang
- [ ] Video-Inhalte pro Abschnitt
- [ ] Timed Challenges
- [ ] Gruppenlerngruppen
- [ ] Teacher/Admin Dashboard zum Kurse-Management

## Troubleshooting

**Problem**: Kurse zeigen sich nicht im Dashboard
- **Lösung**: Cache leeren: `php artisan view:clear`

**Problem**: Punkte werden nicht vergeben
- **Lösung**: Überprüfe ob GamificationService initialisiert ist (muss neuer Instanz sein)

**Problem**: Progress wird nicht gespeichert
- **Lösung**: Überprüfe `unique` Constraint in user_lehrgang_progress Tabelle

**Problem**: Fragen erscheinen doppelt
- **Lösung**: Starte Seeder neu: `php artisan db:seed --class=LehrgangSeeder`
