# 📬 Kontakt & Feedback Formular

## Übersicht
Ein vollständiges Kontakt- und Feedbackformular mit Spam-Schutz, E-Mail-Benachrichtigungen und THW-Design.

## Features

### ✅ Kategorien
- **💭 Feedback** - Lob, Kritik oder Verbesserungsvorschläge
- **✨ Feature-Wunsch** - Neue Funktionen vorschlagen
- **🐛 Fehler melden** - Bugs und Probleme melden
- **📧 Sonstiges** - Allgemeine Anfragen

### 🛡️ Spam-Schutz
1. **Honeypot** - Unsichtbares Feld für Bots
2. **Rate Limiting** - Max 3 Anfragen pro Stunde pro IP
3. **XSS-Schutz** - Alle HTML-Tags werden entfernt

### 📧 E-Mail-System
- Benachrichtigung an: `niclas@thw-trainer.de`
- CC an Absender (Bestätigung)
- Schönes HTML-Design
- Alle Daten in E-Mail enthalten

### 📱 Hermine-Integration
- Checkbox für Hermine-Kontakt
- Konditionale Felder für:
  - Vorname
  - Nachname
  - Ortsverband

### 🐛 Bug-Reports
- Spezielle Felder für Fehlerberichte
- Dropdown für Fehlerort:
  - Dashboard
  - Fragen üben
  - Fehler wiederholen
  - Statistiken
  - Achievements
  - Profil
  - Login/Registrierung
  - Sonstiges

### 💾 Datenbank
Alle Anfragen werden in `contact_messages` Tabelle gespeichert mit:
- User ID (falls eingeloggt)
- Kategorie
- E-Mail
- Hermine-Daten
- Fehlerlokation
- Nachricht
- IP-Adresse
- User-Agent
- Zeitstempel

## Installation

### 1. Migration ausführen
```bash
php artisan migrate
```

### 2. E-Mail Konfiguration
Stelle sicher, dass deine `.env` korrekt konfiguriert ist:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@thw-trainer.de"
MAIL_FROM_NAME="THW-Trainer"
```

## Zugriff

### Routes
- **GET** `/kontakt` - Formular anzeigen
- **POST** `/kontakt` - Formular absenden (Rate Limited: 3/Stunde)

### Dashboard-Link
Ein Link zum Kontaktformular wurde automatisch im Dashboard hinzugefügt.

## Verwendung

### Als Nutzer
1. Gehe zu `/kontakt` oder klicke im Dashboard auf "📬 Kontakt & Feedback"
2. Wähle eine Kategorie aus
3. Gib deine E-Mail-Adresse an
4. Optional: Aktiviere Hermine-Kontakt
5. Bei Fehler: Wähle Fehlerort aus
6. Schreibe deine Nachricht (mindestens 10 Zeichen)
7. Klicke auf "Absenden"

### Als Admin
Du erhältst eine E-Mail an `niclas@thw-trainer.de` mit allen Details:
- Kategorie mit Badge
- Absender-Informationen
- Hermine-Daten (falls gewünscht)
- Bug-Details (falls Fehler)
- Nachricht
- Technische Details (IP, User-Agent, Timestamp)

## Sicherheit

### Implementierte Schutzmaßnahmen
1. **CSRF-Token** - Laravel Schutz gegen Cross-Site Request Forgery
2. **Honeypot** - Unsichtbares Feld fängt Bots ab
3. **Rate Limiting** - Verhindert Spam durch Begrenzung auf 3 Anfragen/Stunde
4. **XSS-Prevention** - `strip_tags()` entfernt alle HTML-Tags
5. **SQL-Injection** - Eloquent ORM verhindert SQL-Injection
6. **Validation** - Alle Eingaben werden validiert

### Rate Limiting
```php
Route::post('/kontakt', [ContactController::class, 'store'])
    ->middleware('throttle:3,60') // 3 Anfragen pro 60 Minuten
    ->name('contact.submit');
```

Bei zu vielen Anfragen erhält der Nutzer:
> ⏱️ Zu viele Anfragen. Bitte versuche es in X Minuten erneut.

## Dateien

### Backend
- `app/Http/Controllers/ContactController.php` - Controller mit Validation & Logic
- `app/Models/ContactMessage.php` - Eloquent Model
- `app/Mail/ContactMail.php` - Mailable für E-Mail-Versand
- `database/migrations/*_create_contact_messages_table.php` - Datenbank-Schema

### Frontend
- `resources/views/contact.blade.php` - Kontaktformular
- `resources/views/emails/contact.blade.php` - E-Mail-Template

### Routes
- `routes/web.php` - GET/POST Routes mit Rate Limiting

## Anpassungen

### E-Mail-Empfänger ändern
In `ContactController.php`:
```php
Mail::to('deine@email.de')
    ->cc($validated['email'])
    ->send(new ContactMail($contactMessage));
```

### Rate Limit ändern
In `routes/web.php`:
```php
->middleware('throttle:5,60') // 5 Anfragen pro Stunde
```

### Kategorien ändern
In `resources/views/contact.blade.php` neue Radio-Buttons hinzufügen und in `ContactController.php` Validation anpassen:
```php
'type' => 'required|in:feedback,feature,bug,other,neue_kategorie',
```

## Admin-Bereich (Optional)

Um eingegangene Nachrichten zu verwalten, kannst du einen Admin-Bereich erstellen:

```php
// routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/contacts', function() {
        $messages = \App\Models\ContactMessage::latest()->paginate(20);
        return view('admin.contacts', compact('messages'));
    })->name('admin.contacts');
});
```

## Support
Bei Fragen zum Kontaktformular: niclas@thw-trainer.de 😊
