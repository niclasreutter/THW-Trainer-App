# 🚨 THW-Trainer App

> **Intelligente Lernplattform für THW-Helfer zur Vorbereitung auf die Grundausbildung**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Überblick

Die **THW-Trainer App** ist eine speziell entwickelte Lernplattform für THW-Helfer, die sich auf die Grundausbildung vorbereiten möchten. Die App bietet intelligente Übungsmodi, Prüfungssimulationen und ein umfassendes Gamification-System, um das Lernen effektiv und motivierend zu gestalten.

## ✨ Hauptfunktionen

### 🎯 **Intelligente Übungsmodi**
- **Priorisierte Fragenauswahl**: Zeigt zuerst fehlgeschlagene und ungelöste Fragen an
- **10 Lernabschnitte**: Strukturiert nach offiziellen THW-Grundausbildungsinhalten
- **Adaptives Lernen**: System passt sich an den Lernfortschritt an

### 📚 **Lernabschnitte**
1. Das THW im Gefüge des Zivil- und Katastrophenschutzes
2. Arbeitssicherheit und Gesundheitsschutz
3. Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen
4. Arbeiten mit Leitern
5. Stromerzeugung und Beleuchtung
6. Metall-, Holz- und Steinbearbeitung
7. Bewegen von Lasten
8. Arbeiten am und auf dem Wasser
9. Einsatzgrundlagen
10. Grundlagen der Rettung und Bergung

### 🏆 **Gamification-System**
- **Punkte & Level**: Sammle Punkte durch richtige Antworten
- **Achievements**: Erhalte Auszeichnungen für verschiedene Meilensteine
- **Tägliche Streaks**: Belohnung für konsequentes Lernen
- **Fortschritts-Tracking**: Detaillierte Statistiken zu Lernfortschritt

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

### **Backend**
- **Laravel 12.x** - Modernes PHP-Framework
- **MySQL** - Datenbank für Fragen und Benutzerdaten
- **Eloquent ORM** - Elegante Datenbankabstraktion

### **Frontend**
- **Tailwind CSS** - Utility-first CSS Framework
- **Blade Templates** - Laravel's Template Engine
- **JavaScript** - Interaktive Benutzeroberfläche
- **Responsive Design** - Optimiert für alle Geräte

### **Features**
- **Gamification Service** - Punkte, Achievements, Level-System
- **Session Management** - Sichere Benutzerauthentifizierung
- **Email-Verification** - Registrierungsbestätigung per E-Mail
- **Admin-Panel** - Verwaltung von Fragen und Benutzern

## 🚀 Installation

### **Voraussetzungen**
- PHP 8.2 oder höher
- Composer
- MySQL/MariaDB
- Node.js & NPM (für Frontend-Assets)

### **Setup**
```bash
# Repository klonen
git clone [PRIVATE-REPO-URL] thw-trainer-app
cd thw-trainer-app

# Dependencies installieren
composer install
npm install

# Environment konfigurieren
cp .env.example .env
php artisan key:generate

# Datenbank konfigurieren (in .env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thw_trainer
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Datenbank migrieren
php artisan migrate

# Frontend-Assets kompilieren
npm run build

# Server starten
php artisan serve
```

## 📁 Projektstruktur

```
thw-trainer-app/
├── app/
│   ├── Http/Controllers/     # API & Web Controllers
│   ├── Models/               # Eloquent Models
│   ├── Services/             # Business Logic (Gamification)
│   └── Mail/                 # Email Templates
├── database/
│   ├── migrations/           # Datenbankschema
│   └── seeders/             # Testdaten
├── resources/
│   ├── views/               # Blade Templates
│   ├── css/                 # Styling
│   └── js/                  # JavaScript
└── routes/
    ├── web.php              # Web Routes
    └── auth.php             # Authentication Routes
```

## 🎮 Gamification-System

### **Punkte & Level**
- **+10 Punkte** pro richtige Antwort
- **Level-System** basierend auf gesammelten Punkten
- **Tägliche Streaks** für konsequentes Lernen

### **Achievements**
- 🥇 **Erste Frage** - Löse deine erste Frage
- 📚 **Fleißig** - Löse 50 Fragen
- 🎓 **Wissensdurstig** - Löse 100 Fragen
- 🏆 **Abschnittsmeister** - Löse alle Fragen in einem Lernabschnitt
- ⭐ **Perfektionist** - Löse 500 Fragen

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

## 🤝 Support & Kontakt

Bei Fragen oder Problemen:
- **Issues** über das private Git-Repository
- **Email** an den Entwickler
- **Dokumentation** in den Code-Kommentaren

---

## 📄 Lizenz

Dieses Projekt ist privat entwickelt und nicht für die Öffentlichkeit bestimmt. Alle Rechte vorbehalten.

---

**Entwickelt mit ❤️ für das THW**