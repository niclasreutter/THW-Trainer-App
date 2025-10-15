# Newsletter-System - Dokumentation

## 📧 Übersicht

Das Newsletter-System ermöglicht es Administratoren, professionelle HTML-Mails an alle User mit E-Mail-Zustimmung zu senden.

## ✨ Features

- **WYSIWYG-Editor** mit TinyMCE
- **Vorgefertigte Komponenten** (Info-Cards, Warning-Cards, Success-Cards, Buttons, etc.)
- **Platzhalter-System** für personalisierte Inhalte
- **Live-Vorschau** beim Erstellen
- **Test-Funktion** (an sich selbst senden)
- **Massen-Versand** an alle User mit E-Mail-Zustimmung
- **Newsletter-Historie** in der Datenbank

## 🚀 Installation

### 1. Migration ausführen

**Option A: Mit Laravel Artisan (Empfohlen)**

```bash
php artisan migrate
```

**Option B: Manuell in der Datenbank**

Falls du die Migration manuell ausführen möchtest:

```sql
CREATE TABLE newsletters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    recipients_count INT NOT NULL DEFAULT 0,
    sent_at TIMESTAMP NULL,
    sent_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (sent_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Dies erstellt die `newsletters` Tabelle.

### 2. Admin-Berechtigung prüfen

**Nur Admins haben Zugriff auf das Newsletter-System!**

Falls dein User noch kein Admin ist:

```sql
UPDATE users SET useroll = 'admin' WHERE email = 'deine@email.de';
```

### 3. Zugriff

Als Administrator:
1. Gehe zu **Admin → Nutzerverwaltung**
2. Klicke auf **"📧 Newsletter senden"**

Oder direkt: `/admin/newsletter/create`

## 📝 Verfügbare Komponenten

### Platzhalter

Verwende diese Platzhalter für personalisierte Inhalte:

- `{{name}}` - Name des Users
- `{{email}}` - E-Mail-Adresse
- `{{level}}` - Level des Users
- `{{points}}` - Punkte des Users
- `{{streak}}` - Streak-Tage des Users

**Beispiel:**
```
Hallo {{name}},

du hast bereits {{points}} Punkte gesammelt und bist auf Level {{level}}!
```

### Vorgefertigte Komponenten

#### 1. Info-Card (Blau mit Glow)
```html
<div class="info-card">
    <p>Wichtige Information für dich!</p>
</div>
```

#### 2. Warning-Card (Gelb mit Glow)
```html
<div class="warning-card">
    <p>Achtung: Wichtiger Hinweis!</p>
</div>
```

#### 3. Success-Card (Grün mit Glow)
```html
<div class="success-card">
    <p>Glückwunsch! Das hast du toll gemacht!</p>
</div>
```

#### 4. Error-Card (Rot mit Glow)
```html
<div class="error-card">
    <p>Fehler: Etwas ist schiefgelaufen!</p>
</div>
```

#### 5. Glow-Button
```html
<p style="text-align: center;">
    <a href="https://thw-trainer.de" class="glow-button">
        Jetzt starten
    </a>
</p>
```

#### 6. Statistik-Box
```html
<div class="stat-box">
    <div class="stat-number">500</div>
    <div class="stat-label">Fragen</div>
</div>
```

## 🎨 Verwendung des Editors

### Komponente einfügen

1. Platziere den Cursor an die gewünschte Stelle
2. Klicke auf den entsprechenden Button in der Toolbar:
   - `ℹ️ Info-Card`
   - `⚠️ Warning-Card`
   - `✅ Success-Card`
   - `❌ Error-Card`
   - `🔘 Glow-Button`
   - `📊 Stat-Box`
3. Gib den Text/Inhalt im Dialog ein
4. Klicke auf "Einfügen"

### Platzhalter einfügen

1. Klicke auf das Dropdown `{{...}} Platzhalter`
2. Wähle den gewünschten Platzhalter aus
3. Er wird an der Cursor-Position eingefügt

## 🧪 Test-Funktion

**Empfohlen vor jedem Massen-Versand!**

1. Erstelle deinen Newsletter
2. Klicke auf **"🧪 Test-Mail an mich"**
3. Überprüfe die E-Mail in deinem Postfach
4. Kontrolliere:
   - Formatierung
   - Platzhalter korrekt ersetzt?
   - Links funktionieren?
   - Komponenten werden richtig angezeigt?

## 📧 Newsletter versenden

### An alle User mit Zustimmung

1. Erstelle deinen Newsletter
2. Teste ihn (siehe oben)
3. Klicke auf **"📧 An alle senden"**
4. Bestätige die Sicherheitsabfrage
5. Warte auf Bestätigung

**Wichtig:**
- Nur User mit `email_consent = true` erhalten die Mail
- Platzhalter werden für jeden User individuell ersetzt
- Der Newsletter wird in der Datenbank gespeichert

## 📊 Newsletter-Historie

Die letzten 10 gesendeten Newsletter werden auf der Seite angezeigt:
- Betreff
- Anzahl Empfänger
- Gesendet von (Admin-Name)
- Datum & Uhrzeit

## 🔒 Datenschutz

**DSGVO-konform:**
- Nur User mit expliziter E-Mail-Zustimmung erhalten Newsletter
- User können Zustimmung jederzeit in ihrem Profil widerrufen
- Footer-Text informiert über Abmelde-Möglichkeit

## 🛠️ Technische Details

### Datenbankstruktur

**Tabelle: `newsletters`**
- `id` - Newsletter ID
- `subject` - Betreff
- `content` - HTML-Inhalt
- `recipients_count` - Anzahl Empfänger
- `sent_at` - Versand-Zeitpunkt
- `sent_by` - Admin User ID
- `created_at` / `updated_at` - Timestamps

### Dateien

**Backend:**
- `app/Models/Newsletter.php` - Model
- `app/Mail/NewsletterMail.php` - Mailable
- `app/Http/Controllers/NewsletterController.php` - Controller

**Frontend:**
- `resources/views/admin/newsletter/create.blade.php` - Editor
- `resources/views/emails/newsletter.blade.php` - Email-Template

**Routen:**
- `GET /admin/newsletter/create` - Editor anzeigen
- `POST /admin/newsletter/test` - Test-Mail senden
- `POST /admin/newsletter/send` - An alle senden

## 💡 Tipps & Best Practices

### ✅ Do's

- Immer Test-Mail vor Massen-Versand senden
- Kurze, prägnante Betreffzeilen
- Klare Call-to-Actions mit Glow-Buttons
- Platzhalter für Personalisierung nutzen
- Mobile-freundliches Design (ist bereits optimiert)

### ❌ Don'ts

- Zu viele unterschiedliche Komponenten mischen
- Zu lange Texte (User lesen nicht alles)
- Spam vermeiden (max. 1-2 Newsletter pro Monat)
- Ohne Test versenden

## 🎯 Beispiel-Newsletter

```html
<h2>Hallo {{name}}! 🎉</h2>

<p>Wir haben tolle Neuigkeiten für dich!</p>

<div class="info-card">
    <p>
        <strong>Neue Features im THW-Trainer:</strong><br>
        - Fortschrittsbalken zeigt jetzt auch teilweise gelöste Fragen<br>
        - Neue Gamification-Elemente<br>
        - Verbesserte Performance
    </p>
</div>

<div class="stat-box">
    <div class="stat-number">{{points}}</div>
    <div class="stat-label">Deine Punkte</div>
</div>

<p>Du bist auf Level {{level}} und hast bereits großartige Fortschritte gemacht!</p>

<p style="text-align: center;">
    <a href="https://thw-trainer.de/practice-menu" class="glow-button">
        Jetzt weiterlernen! 🚀
    </a>
</p>

<p>Viel Erfolg weiterhin!<br>
Dein THW-Trainer Team</p>
```

## 🐛 Troubleshooting

### Newsletter wird nicht gesendet

1. **Prüfe E-Mail-Konfiguration:**
   - Ist `MAIL_*` in `.env` korrekt?
   - Funktionieren andere Mails (z.B. Passwort-Reset)?

2. **Prüfe Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Prüfe ob User E-Mail-Zustimmung haben:**
   ```sql
   SELECT COUNT(*) FROM users WHERE email_consent = 1;
   ```

### Komponenten werden nicht angezeigt

- Stelle sicher, dass die CSS-Klassen in `resources/views/emails/newsletter.blade.php` vorhanden sind
- Prüfe ob TinyMCE korrekt geladen wurde (Browser-Konsole)

## 📞 Support

Bei Problemen kontaktiere den Entwickler oder öffne ein Issue im Repository.

---

**Version:** 1.0  
**Erstellt:** 16.10.2025  
**Letztes Update:** 16.10.2025

