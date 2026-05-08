# 🚨 THW-Trainer App

> **Intelligente Lernplattform für THW-Helfer zur Vorbereitung auf die Grundausbildung**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38bdf8.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)](#-lizenz)
[![Status](https://img.shields.io/badge/Status-Not%20Open%20Source-important.svg)](#-lizenz)

<div align="center">
  <p><i>Eine moderne Web-Anwendung zur effektiven Vorbereitung auf die THW-Grundausbildungsprüfung</i></p>

  **[Features](#-hauptfunktionen) • [Installation](#-installation) • [Dokumentation](#-projektstruktur)**

  <p><strong>⚠️ Proprietäres Projekt - Keine Nutzung ohne Genehmigung</strong></p>
</div>

---

## 📋 Überblick

Die **THW-Trainer App** ist eine speziell entwickelte Lernplattform für THW-Helfer, die sich auf die Grundausbildung vorbereiten möchten. Die App bietet intelligente Übungsmodi, Prüfungssimulationen und ein umfassendes Gamification-System, um das Lernen effektiv und motivierend zu gestalten.

### 🎯 Projektziele

- **Effiziente Prüfungsvorbereitung**: Strukturiertes Lernen basierend auf offiziellen THW-Inhalten
- **Adaptive Lernmethoden**: Intelligente Fragenauswahl basierend auf individuellem Fortschritt
- **Gamification**: Motivierende Elemente wie Punkte, Level und Achievements
- **Benutzerfreundlichkeit**: Intuitive Oberfläche für Desktop und Mobile

> **⚠️ WICHTIGER HINWEIS**: Dieses Projekt ist **NICHT Open Source**. Die Nutzung, Vervielfältigung, Modifikation oder Weitergabe des Codes ist ohne ausdrückliche schriftliche Genehmigung des Urhebers untersagt. Dieses Repository dient ausschließlich zu Demonstrationszwecken.

## ✨ Hauptfunktionen

### 🎯 **Intelligente Übungsmodi**
- **Priorisierte Fragenauswahl**: Zeigt zuerst fehlgeschlagene und ungelöste Fragen an
- **10 Lernabschnitte**: Strukturiert nach offiziellen THW-Grundausbildungsinhalten
- **Adaptives Lernen**: System passt sich an den Lernfortschritt an

### 📚 **Lernabschnitte**

Die App deckt alle 10 offiziellen THW-Grundausbildungsabschnitte ab:

<details>
<summary><b>Alle Lernabschnitte anzeigen</b></summary>

1. **Das THW im Gefüge des Zivil- und Katastrophenschutzes**
   _Grundlagen, Organisation, Rechtsgrundlagen_

2. **Arbeitssicherheit und Gesundheitsschutz**
   _Unfallverhütung, Schutzausrüstung, Sicherheitsvorschriften_

3. **Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen**
   _Knotenkunde, Anschlagmittel, Tragfähigkeit_

4. **Arbeiten mit Leitern**
   _Leiterarten, Aufbau, Sicherheitshinweise_

5. **Stromerzeugung und Beleuchtung**
   _Aggregate, Beleuchtungsgeräte, Elektrosicherheit_

6. **Metall-, Holz- und Steinbearbeitung**
   _Werkzeuge, Bearbeitungstechniken, Materialkunde_

7. **Bewegen von Lasten**
   _Hebezeuge, Transport, Lastverteilung_

8. **Arbeiten am und auf dem Wasser**
   _Bootskunde, Schwimmhilfen, Wasserrettung_

9. **Einsatzgrundlagen**
   _Einsatztaktik, Kommunikation, Dokumentation_

10. **Grundlagen der Rettung und Bergung**
    _Rettungstechniken, Erste Hilfe, Bergungsgeräte_

</details>

### 🏆 **Gamification-System**
- 🎯 **Punkte & Level**: Sammle Punkte durch richtige Antworten und steige im Level auf
- 🏅 **Achievements**: Erhalte Auszeichnungen für verschiedene Meilensteine
- 🔥 **Tägliche Streaks**: Belohnung für konsequentes Lernen
- 📈 **Fortschritts-Tracking**: Detaillierte Statistiken zu deinem Lernfortschritt

<details>
<summary><b>Verfügbare Achievements</b></summary>

| Achievement | Bedingung | Belohnung |
|-------------|-----------|-----------|
| 🥇 Erste Frage | Löse deine erste Frage | 10 Punkte |
| 📚 Fleißig | Löse 50 Fragen | 50 Punkte |
| 🎓 Wissensdurstig | Löse 100 Fragen | 100 Punkte |
| 🏆 Abschnittsmeister | Schließe einen Lernabschnitt ab | 200 Punkte |
| ⭐ Perfektionist | Löse 500 Fragen | 500 Punkte |

</details>

### 🎓 **Prüfungssimulation**
- **Realistische Prüfungen**: 40 zufällige Fragen pro Prüfung
- **Sofortige Auswertung**: Direktes Feedback zu Leistung
- **Fehleranalyse**: Detaillierte Aufschlüsselung falscher Antworten
- **Wiederholungsmodus**: Übe spezifisch die falschen Fragen

### 👤 **Benutzerfunktionen**
- **Registrierung & Anmeldung**: Vollständiges Account-Management
- **Profilverwaltung**: Persönliche Einstellungen und Fortschritt
- **Lesezeichen**: Markiere wichtige Fragen zum späteren Üben
- **Gastmodus**: Teste die App ohne Registrierung

## 🛠️ Technologie-Stack

<table>
<tr>
<td valign="top" width="50%">

### **Backend**
- ![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white) **Laravel 12.x**
- ![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white) **PHP 8.2+**
- ![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white) **MySQL 8.0**
- **Eloquent ORM** - Datenbankabstraktion
- **Laravel Policies** - Authorization

</td>
<td valign="top" width="50%">

### **Frontend**
- ![Tailwind](https://img.shields.io/badge/Tailwind-CSS-38B2AC?logo=tailwind-css&logoColor=white) **Tailwind CSS**
- ![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?logo=alpine.js&logoColor=black) **Alpine.js**
- **Blade Templates** - Server-side Rendering
- **Responsive Design** - Mobile-first Ansatz

</td>
</tr>
</table>

### **🔧 Schlüssel-Features**
| Feature | Technologie | Beschreibung |
|---------|-------------|--------------|
| 🎮 **Gamification** | Custom Service | Punkte, Achievements, Level-System |
| 🔐 **Authentication** | Laravel Breeze | Sichere Benutzerauthentifizierung |
| ✉️ **Email-Verification** | Laravel Mail | Registrierungsbestätigung |
| 👨‍💼 **Admin-Panel** | Custom Dashboard | Fragen- und Benutzerverwaltung |
| 📊 **Analytics** | Session Tracking | Fortschritt und Statistiken |

## 🚀 Installation

> **⚠️ Lizenzhinweis**: Die Installation und Nutzung dieses Projekts ist nur mit ausdrücklicher Genehmigung des Urhebers gestattet. Dieses Repository dient primär zu Demonstrationszwecken.

### **Voraussetzungen**
- PHP 8.2 oder höher
- Composer
- MySQL/MariaDB
- Node.js & NPM (für Frontend-Assets)

### **Setup**

<details>
<summary><b>📦 Schritt-für-Schritt Installation</b></summary>

```bash
# 1. Repository klonen
git clone https://github.com/IHR-USERNAME/thw-trainer-app.git
cd thw-trainer-app

# 2. Dependencies installieren
composer install
npm install

# 3. Environment konfigurieren
cp .env.example .env
php artisan key:generate

# 4. Datenbank konfigurieren (in .env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thw_trainer
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 5. Datenbank migrieren und Seed-Daten laden
php artisan migrate --seed

# 6. Storage-Link erstellen (für Uploads)
php artisan storage:link

# 7. Frontend-Assets kompilieren
npm run build

# 8. Server starten
php artisan serve
```

Die Anwendung ist nun unter `http://localhost:8000` erreichbar.

</details>

### **⚙️ Konfiguration**

<details>
<summary>Wichtige Konfigurationsoptionen</summary>

**Mail-Konfiguration** (für E-Mail-Verifizierung):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

**Cache-Konfiguration** (optional, für bessere Performance):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

</details>

---

## 🌐 Multi-Domain Architektur (Plesk-Deployment)

Die THW-Trainer App verwendet eine Multi-Domain-Architektur:

| Domain | Zweck | Design |
|--------|-------|--------|
| **thw-trainer.de** | Öffentliche Landingpage, SEO, Guest-Modus | Light Mode |
| **app.thw-trainer.de** | Authentifizierte App mit allen Features | Dark Mode (Glassmorphism) |

### **Plesk-Konfiguration Schritt für Schritt**

<details>
<summary><b>📋 1. Haupt-Domain einrichten (thw-trainer.de)</b></summary>

1. In Plesk eine neue **Website** anlegen für `thw-trainer.de`
2. **Document Root** auf `/httpdocs` setzen (Standard)
3. Das Laravel-Projekt in `/httpdocs` hochladen
4. **Document Root** anpassen auf `/httpdocs/public`
   - Websites & Domains → thw-trainer.de → Hosting-Einstellungen
   - Document Root: `/httpdocs/public`

</details>

<details>
<summary><b>📋 2. Subdomain einrichten (app.thw-trainer.de)</b></summary>

1. Websites & Domains → **Subdomain hinzufügen**
2. Subdomain-Name: `app`
3. **WICHTIG**: Document Root auf das gleiche Verzeichnis wie die Hauptdomain setzen:
   - Document Root: `thw-trainer.de/httpdocs/public`
   - **NICHT** ein separates Verzeichnis erstellen!

> Die Subdomain muss auf das **gleiche** `public/`-Verzeichnis zeigen, da beide Domains dieselbe Laravel-Installation nutzen.

</details>

<details>
<summary><b>📋 3. SSL-Zertifikate einrichten</b></summary>

Für beide Domains SSL aktivieren:

1. Websites & Domains → thw-trainer.de → SSL/TLS-Zertifikate
2. **Let's Encrypt** auswählen und Zertifikat anfordern
3. "HTTPS dauerhaft umleiten" aktivieren
4. Wiederholen für `app.thw-trainer.de`

**Oder**: Wildcard-Zertifikat für `*.thw-trainer.de` verwenden

</details>

<details>
<summary><b>📋 4. Environment-Variablen (.env) konfigurieren</b></summary>

In der `.env` Datei auf dem Server:

```env
# App-Konfiguration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.thw-trainer.de

# Domain-Konfiguration (WICHTIG!)
LANDING_DOMAIN=thw-trainer.de
APP_DOMAIN=app.thw-trainer.de

# Session-Domain für Cross-Domain Cookie-Sharing
SESSION_DOMAIN=.thw-trainer.de

# Datenbank
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=thw_trainer
DB_USERNAME=dein_db_user
DB_PASSWORD=dein_db_passwort

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
```

**Wichtige Hinweise:**
- `SESSION_DOMAIN=.thw-trainer.de` (mit Punkt am Anfang!) erlaubt Session-Sharing zwischen Domains
- `APP_URL` sollte auf `app.thw-trainer.de` zeigen (für URL-Generierung)

</details>

<details>
<summary><b>📋 5. PHP-Einstellungen in Plesk</b></summary>

Empfohlene PHP-Einstellungen:

1. Websites & Domains → PHP-Einstellungen
2. PHP-Version: **8.2** oder höher
3. Wichtige Einstellungen:
   ```
   memory_limit = 256M
   max_execution_time = 60
   upload_max_filesize = 10M
   post_max_size = 12M
   ```

</details>

<details>
<summary><b>📋 6. Deployment-Befehle (nach Upload)</b></summary>

Nach dem Hochladen des Codes auf den Server per SSH ausführen:

```bash
cd /var/www/vhosts/thw-trainer.de/httpdocs

# Dependencies installieren
composer install --no-dev --optimize-autoloader

# Environment-Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage-Link erstellen
php artisan storage:link

# Datenbank migrieren
php artisan migrate --force

# Frontend-Assets (falls auf Server gebaut)
npm install --production
npm run build
```

</details>

<details>
<summary><b>📋 7. Verzeichnisberechtigungen</b></summary>

Sicherstellen, dass folgende Verzeichnisse schreibbar sind:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Oder in Plesk unter "Hosting-Einstellungen" den Benutzer prüfen.

</details>

### **So funktioniert das Routing**

```
┌─────────────────────────────────────────────────────────────────┐
│                        DNS                                       │
│  thw-trainer.de      →  Server IP                               │
│  app.thw-trainer.de  →  Server IP                               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Plesk/Apache                                │
│  Beide Domains zeigen auf: /httpdocs/public/index.php           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel Routing                               │
│                                                                  │
│  Request: thw-trainer.de/*                                       │
│    → routes/landing.php (Light Mode, SEO, Guest-Modus)          │
│                                                                  │
│  Request: app.thw-trainer.de/*                                  │
│    → routes/web.php (Dark Mode, Auth, Dashboard)                │
└─────────────────────────────────────────────────────────────────┘
```

### **Troubleshooting**

| Problem | Lösung |
|---------|--------|
| Session wird nicht geteilt | `SESSION_DOMAIN=.thw-trainer.de` prüfen (mit Punkt!) |
| 404 auf Subdomain | Document Root muss auf gleiches `/public` zeigen |
| CSS/JS laden nicht | `APP_URL` in .env prüfen, `npm run build` ausführen |
| Login leitet falsch weiter | `LANDING_DOMAIN` und `APP_DOMAIN` in .env prüfen |
| Mixed Content Warnung | SSL für beide Domains aktivieren |

### **Lokale Entwicklung**

In der lokalen Entwicklungsumgebung (`APP_ENV=local`) werden beide Route-Files geladen und sind unter `localhost` erreichbar:
- Landing-Routes mit `landing.*` Prefix
- App-Routes ohne Prefix
- Kein Domain-basiertes Routing nötig

## 📁 Projektstruktur

<details>
<summary><b>Verzeichnisübersicht anzeigen</b></summary>

```
thw-trainer-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Request Handler (Practice, Exams, Admin)
│   │   ├── Middleware/           # Request-Middleware
│   │   └── Requests/             # Form Validation
│   ├── Models/                   # Eloquent Models (User, Question, Answer)
│   ├── Policies/                 # Authorization Logic
│   ├── Services/                 # Business Logic (GamificationService)
│   └── Mail/                     # Email Templates
├── database/
│   ├── migrations/               # Datenbankschema-Versionen
│   ├── seeders/                  # Seed-Daten für Development
│   └── factories/                # Model Factories für Testing
├── resources/
│   ├── views/                    # Blade Templates
│   │   ├── practice/             # Übungsmodus-Views
│   │   ├── exams/                # Prüfungs-Views
│   │   ├── admin/                # Admin-Panel Views
│   │   └── components/           # Wiederverwendbare Components
│   ├── css/                      # Tailwind CSS
│   └── js/                       # Frontend JavaScript
├── routes/
│   ├── web.php                   # Web Routes
│   ├── auth.php                  # Authentication Routes
│   └── api.php                   # API Routes (falls benötigt)
├── public/                       # Öffentlich zugängliche Assets
├── tests/                        # PHPUnit Tests
└── docs/                         # Zusätzliche Dokumentation
```

</details>

### 🗂️ Wichtige Dateien

| Datei/Verzeichnis | Beschreibung |
|-------------------|--------------|
| [`app/Services/GamificationService.php`](app/Services/GamificationService.php) | Gamification-Logik (Punkte, Achievements) |
| [`app/Http/Controllers/PracticeController.php`](app/Http/Controllers/PracticeController.php) | Übungsmodus-Controller |
| [`resources/views/practice.blade.php`](resources/views/practice.blade.php) | Hauptansicht für Übungen |
| [`database/migrations/`](database/migrations/) | Datenbankschema-Evolution |
| [`CLAUDE.md`](CLAUDE.md) | KI-Entwickler-Anleitung (Claude Code) |
| [`AGENTS.md`](AGENTS.md) | Allgemeine AI-Assistant-Anleitung |
| [`LLM/`](LLM/) | KI-/LLM-spezifische Dokumente (Feature-Notes, Fix-Reports, Setup-Guides) |

## 🎮 Gamification im Detail

Das Gamification-System ist darauf ausgelegt, langfristige Motivation zu fördern:

### **Punkte-System**
```
Richtige Antwort:        +10 Punkte
Täglicher Login:         +5 Punkte
7-Tage-Streak:          +50 Punkte
Lernabschnitt komplett: +100 Punkte
```

### **Level-Progression**
```
Level 1:     0 - 100 Punkte   (Anfänger)
Level 2:   101 - 300 Punkte   (Lernender)
Level 3:   301 - 600 Punkte   (Fortgeschrittener)
Level 4:   601 - 1000 Punkte  (Experte)
Level 5:  1001+ Punkte        (Meister)
```

## 🔒 Sicherheit & Datenschutz

- **Sichere Authentifizierung** mit Laravel's eingebautem System
- **CSRF-Schutz** für alle Formulare
- **Password-Hashing** mit bcrypt
- **Email-Verification** für neue Accounts
- **DSGVO-konform** mit Datenschutzerklärung

## 👥 Benutzerrollen

### **Registrierte Benutzer**
- Vollzugriff auf alle Lernfunktionen
- Persönlicher Fortschritt wird gespeichert
- Gamification-Features verfügbar
- Prüfungen und Statistiken

### **Gäste**
- Begrenzter Zugriff auf Übungsmodi
- Keine Fortschrittsspeicherung
- Einfache Prüfungssimulation

### **Administratoren**
- Vollzugriff auf alle Funktionen
- Verwaltung von Fragen und Benutzern
- Systemstatistiken und -einstellungen

## 📊 Features im Detail

### **Intelligente Übungsmodi**
1. **Fehlgeschlagene Fragen** - Wiederhole falsche Antworten
2. **Ungelöste Fragen** - Übe neue Inhalte
3. **Alle Fragen** - Zufällige Reihenfolge für Wiederholung

### **Statistiken & Tracking**
- **Gesamtfortschritt** pro Lernabschnitt
- **Tägliche Aktivität** und Streaks
- **Punktestand** und Level-Status
- **Achievement-Übersicht**

### **Responsive Design**
- **Mobile-optimiert** für Smartphones und Tablets
- **Desktop-freundlich** für größere Bildschirme
- **Touch-freundlich** für alle Interaktionen

## ❓ FAQ

<details>
<summary><b>Ist die App offiziell vom THW?</b></summary>

Nein, dies ist ein unabhängiges Projekt zur Unterstützung der THW-Grundausbildung. Es ist nicht offiziell vom THW lizenziert oder unterstützt.

</details>

<details>
<summary><b>Kann ich die App ohne Registrierung nutzen?</b></summary>

Ja, es gibt einen Gastmodus mit eingeschränkten Funktionen. Für volles Tracking und Gamification ist jedoch eine Registrierung erforderlich.

</details>

<details>
<summary><b>Sind die Fragen aktuell und korrekt?</b></summary>

Die Fragen basieren auf offiziellen THW-Schulungsunterlagen. Dennoch kann keine Garantie für Vollständigkeit oder Aktualität übernommen werden. Nutzen Sie die App als Ergänzung zur offiziellen Ausbildung.

</details>

<details>
<summary><b>Ist eine Mobile-App geplant?</b></summary>

Das ist auf der Roadmap. Aktuell ist die Web-App responsive und funktioniert auf Mobilgeräten.

</details>

<details>
<summary><b>Wie kann ich eigene Fragen hinzufügen?</b></summary>

Als Administrator können Sie über das Admin-Panel Fragen hinzufügen und verwalten. Für normale Benutzer ist diese Funktion nicht verfügbar.

</details>

## 🛣️ Roadmap

Geplante Features und Verbesserungen:

- [ ] **📱 Mobile App** - Native iOS/Android Apps
- [ ] **🔌 Offline-Modus** - Lernen ohne Internetverbindung
- [ ] **👥 Soziale Features** - Lerngruppen und Ranglisten
- [ ] **📊 Erweiterte Statistiken** - Detaillierte Lernanalysen mit Diagrammen
- [ ] **🔊 Audio-Unterstützung** - Fragen vorlesen lassen (Accessibility)
- [ ] **🌍 Mehrsprachigkeit** - Unterstützung für weitere Sprachen
- [ ] **🎯 Adaptive Algorithmen** - KI-gestützte Fragenanpassung
- [ ] **🏅 Erweiterte Gamification** - Badges, Challenges, Turniere

## 🤝 Contributions & Support

### ⚠️ Projekt-Status: Proprietäres Portfolio-Projekt

Dieses Projekt ist **NICHT Open Source** und dient ausschließlich zu Demonstrations- und Portfolio-Zwecken.

**Was bedeutet das?**
- ❌ **Keine Nutzung** ohne ausdrückliche Genehmigung
- ❌ **Keine Forks** oder Klone für eigene Projekte
- ❌ **Keine Contributions** - Das Projekt ist nicht für externe Beiträge geöffnet
- ❌ **Keine kommerzielle Nutzung** ohne Lizenzvereinbarung
- ✅ **Code-Ansicht** zu Lern- und Evaluierungszwecken

### Issues & Bug Reports
Falls Sie Fehler finden, können Sie diese zu Informationszwecken melden. Bitte beachten Sie, dass keine aktive Wartung oder Implementierung von Feature-Requests garantiert wird.

### Lizenzanfragen
Für Anfragen zur Nutzung, Lizenzierung oder Zusammenarbeit:
- **GitHub**: [@IHR-USERNAME](https://github.com/IHR-USERNAME)
- **Kontakt**: Über GitHub Issues oder Direktnachricht

### Dokumentation
Die Projektdokumentation ist verfügbar unter:
- [CLAUDE.md](CLAUDE.md) - Projekt-Kontext und Entwicklungsrichtlinien
- [AGENTS.md](AGENTS.md) - Allgemeine AI-Assistant-Anleitung
- [LLM/](LLM/) - Weitere KI-/LLM-spezifische Dokumente
- [docs/](docs/) - Technische Dokumentation

## 🤖 KI-/LLM-Anleitungen

Die zentralen Einstiegspunkte für KI-Assistenten liegen — wie üblich — im Projekt-Root, damit sie automatisch gefunden werden:
- **[`CLAUDE.md`](CLAUDE.md)** - Projekt-Kontext und Entwicklungsrichtlinien für Claude Code
- **[`AGENTS.md`](AGENTS.md)** - Allgemeine Anleitung für AI Assistants

Alle weiteren KI-/LLM-bezogenen Dokumente (Feature-Notes, Fix-Reports, Setup-Guides, TODO_AI etc.) liegen gebündelt im Ordner **[`LLM/`](LLM/)**.

## 📚 Dokumentation

Weitere technische Dokumentation finden Sie hier:
- **[CLAUDE.md](CLAUDE.md)** - AI-Assistant Anleitung und Projekt-Kontext
- **[docs/PATTERNS.md](docs/PATTERNS.md)** - Code Patterns & Naming Conventions
- **[docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)** - Fehlerbehebung
- **[docs/FILE-GUIDE.md](docs/FILE-GUIDE.md)** - Datei-Navigation und Architektur

## ⭐ Acknowledgments

Besonderer Dank an:
- Das **THW** für die Inspiration zu diesem Projekt
- Die **Laravel-Community** für das großartige Framework
- Alle **Open-Source-Contributors**, deren Bibliotheken dieses Projekt ermöglichen

## 📄 Lizenz

**Copyright © 2026 - Alle Rechte vorbehalten**

### 🔒 Proprietäre Software - Keine freie Nutzung

Dieses Projekt ist **NICHT unter einer Open-Source-Lizenz** veröffentlicht. Alle Rechte am Quellcode, der Dokumentation und den Assets liegen beim Urheber.

#### Verboten ohne ausdrückliche Genehmigung:
- ❌ Verwendung des Codes für eigene Projekte
- ❌ Forken und Weiterentwicklung
- ❌ Kommerzielle Nutzung
- ❌ Vervielfältigung und Weitergabe
- ❌ Modifikation und Ableitung
- ❌ Deployment auf eigener Infrastruktur

#### Erlaubt:
- ✅ Ansicht des Codes zu Lern- und Evaluierungszwecken
- ✅ Code-Review und Analyse
- ✅ Referenzierung in Bewerbungen/Portfolio-Präsentationen (mit Quellenangabe)

**Für jegliche Nutzung, die über die reine Code-Ansicht hinausgeht, ist eine schriftliche Genehmigung erforderlich.**

📧 **Lizenzanfragen**: Kontaktieren Sie den Entwickler über GitHub für Lizenzvereinbarungen.

---

<div align="center">
  <p><b>Entwickelt mit ❤️ für das THW</b></p>
  <p>
    <a href="#-thw-trainer-app">Zurück nach oben ↑</a>
  </p>
</div>