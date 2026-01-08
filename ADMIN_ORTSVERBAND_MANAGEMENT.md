# Admin Ortsverbände Management

## Übersicht

Der Admin kann jetzt über das Admin-Panel alle Ortsverbände verwalten, ohne selbst Mitglied sein zu müssen.

## Zugang

**Route:** `/admin/ortsverband`

## Features

### 1. Alle Ortsverbände ansehen
- Listet alle Ortsverbände mit Statistiken auf
- Zeigt Gründer, Anzahl der Mitglieder und Erstellen-Datum

### 2. Ortsverband Details
- **Route:** `/admin/ortsverband/{ortsverband}`
- Statistiken anzeigen:
  - Gesamte Mitglieder
  - Anzahl der Ausbilder
  - Durchschnittlicher Fortschritt
  - Mitglieder die Hilfe brauchen
- Mitgliederliste mit:
  - Name, Email, ID
  - Rolle (Mitglied oder Ausbilder)
  - Lernfortschritt
  - Aktionen (Rolle ändern, Entfernen)

### 3. Ortsverband bearbeiten
- **Route:** `/admin/ortsverband/{ortsverband}/edit`
- Name und Beschreibung ändern

### 4. Admin-View Modus
- **Button:** "🔍 Als Admin anzeigen"
- Der Admin kann die reguläre Ortsverband-View ansehen (ohne vollständiger Mitglied zu sein)
- Die Session speichert `admin_viewing_ortsverband_id`
- Ein Banner zeigt an, dass der Admin die Seite betrachtet
- Mit "Admin-View beenden" wird die Session geleert

### 5. Mitgliederverwaltung
- Mitglieder zu Ausbildern befördern/demotieren
- Mitglieder aus dem Ortsverband entfernen
- Pagination für große Mitgliederlisten

### 6. Ortsverband löschen
- Komplette Löschung eines Ortsverbands
- Mit Bestätigungsdialog

## Implementierte Änderungen

### Controller
- `App\Http\Controllers\Admin\OrtsverbandController`
  - `index()` - Listet alle Ortsverbände
  - `show()` - Zeigt Details eines Ortsverbands
  - `edit()` - Bearbeitungsformular
  - `update()` - Speichert Änderungen
  - `viewAs()` - Aktiviert Admin-View Modus
  - `exitView()` - Beendet Admin-View Modus
  - `removeMember()` - Entfernt Mitglieder
  - `updateMemberRole()` - Ändert Mitgliedsrolle
  - `destroy()` - Löscht einen Ortsverband

### Views
- `admin/ortsverband/index.blade.php` - Übersicht aller Ortsverbände
- `admin/ortsverband/show.blade.php` - Details eines Ortsverbands
- `admin/ortsverband/edit.blade.php` - Bearbeitungsformular

### Routes
- `GET /admin/ortsverband` - Liste aller Ortsverbände
- `GET /admin/ortsverband/{ortsverband}` - Details ansehen
- `GET /admin/ortsverband/{ortsverband}/edit` - Bearbeitungsformular
- `PUT /admin/ortsverband/{ortsverband}` - Speichern
- `POST /admin/ortsverband/{ortsverband}/view-as` - Admin-View starten
- `POST /admin/ortsverband/exit-view` - Admin-View beenden
- `DELETE /admin/ortsverband/{ortsverband}` - Löschen
- `DELETE /admin/ortsverband/{ortsverband}/member/{user}` - Mitglied entfernen
- `PATCH /admin/ortsverband/{ortsverband}/member/{user}/role` - Rolle ändern

### Modified Files
- `app/Http/Controllers/OrtsverbandController.php` - `show()` Methode angepasst für Admin-Zugriff
- `resources/views/ortsverband/show.blade.php` - Admin-View Banner hinzugefügt

## Sicherheit

- Alle Admin-Routes sind durch `\App\Http\Middleware\AdminMiddleware::class` geschützt
- Der Admin kann nur mittels Session einen Ortsverband "besuchen", hat aber keine echte Mitgliedschaft
- Normale Benutzer können die Admin-Routes nicht zugreifen
