# CLAUDE.md - THW-Trainer-App

> Kompakte Anleitung für AI Assistants. Details in `docs/*.md`.

## Projekt-Kontext

- **Domain:** THW Lern- und Prüfungsplattform
- **Stack:** Laravel 12 + Blade + Tailwind CSS + Alpine.js
- **Sprache:** Deutsch (Domain/UI) + Englisch (Code)

## Wichtigste Regeln

### 1. Commit-Format (IMMER DEUTSCH)
```bash
git commit -m "EMOJI: Beschreibung (max 4 Wörter)"
```
| Emoji | Bedeutung |
|-------|-----------|
| ✨ | Feature |
| 🐛 | Bug fix |
| 🎨 | UI/Design |
| ⚡ | Performance |
| 🔒 | Security |

### 2. Design-System: Dark Mode Glassmorphism

**Standard-Layout (Bento Grid):**
```html
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Prefix <span>Gold-Text</span></h1>
        <p class="page-subtitle">Beschreibung</p>
    </header>

    <div class="stats-row">
        <div class="stat-pill">...</div>
    </div>

    <div class="bento-grid">
        <div class="glass-gold bento-main">Hauptinhalt</div>
        <div class="glass-tl bento-side">Widget</div>
    </div>
</div>
```

**Glass Card Varianten:**
- `.glass` - Standard
- `.glass-gold`, `.glass-blue`, `.glass-purple` - Lensflare-Glow
- `.glass-tl`, `.glass-br`, `.glass-slash` - Asymmetrisch
- `.glass-success`, `.glass-error`, `.glass-warning` - Semantisch

**Buttons:** `.btn-primary` (Gold) | `.btn-secondary` (THW-Blau) | `.btn-ghost` | `.btn-danger`

**Wichtig:**
- **Keine Emojis** im UI verwenden
- **Icons nur sinnvoll** einsetzen (z.B. Status-Badges, nicht in Buttons)
- **Buttons ohne Icons** - cleaner und professioneller
- **Asymmetrie nutzen** - Vermeide generischen "AI-Look"
- Bootstrap Icons (`bi bi-*`) für notwendige Icons
- Details: **[docs/PATTERNS.md](docs/PATTERNS.md)**

### 3. Nach jeder Änderung
```bash
npm run build && php artisan view:clear && php artisan cache:clear
```

### 4. Authorization
```php
// IMMER Policies verwenden, NIE inline checks
$this->authorize('update', $lernpool);
```

### 5. Modal AJAX (Cache-Busting PFLICHT)
```javascript
const url = link.href + '?ajax=1&_t=' + Date.now();
fetch(url, { cache: 'no-store' });
```

## Wichtige Dateien

| Feature | Controller | Views |
|---------|------------|-------|
| Lernpools | `OrtsverbandLernpoolController` | `ortsverband/lernpools/` |
| Practice | `PracticeController` | `practice.blade.php` |
| Admin Users | `AdminController` | `admin/users.blade.php` |
| Gamification | `GamificationService` | `components/` |

## Cronjobs / Scheduler

- **Alle Cronjobs laufen über den Laravel Scheduler** (`routes/console.php`)
- **CloudPanel:** Nur 1 Cronjob nötig → `schedule:run` jede Minute
- **Keine separaten PHP-Cronjob-Dateien** mehr nötig (Legacy: `cronjob-*.php`)
- **Monitoring:** Fehler werden per Mail an `protokolle@thw-trainer.de` gesendet
- **Details:** [CLOUDPANEL_CRONJOB_SETUP.md](CLOUDPANEL_CRONJOB_SETUP.md)

## Bekannte Gotchas

1. **DB-Spalte:** Immer `lernpool_id` (nicht `ortsverband_lernpool_id`)
2. **User-Rolle:** `$user->useroll` (Typo im Schema, nicht ändern)
3. **Lösung-Format:** Sortiert, komma-getrennt: `"A,B"` nicht `"B,A"`
4. **Flash-Data:** Nur für 1 Request, dann weg
5. **Tailwind-Klassen:** Nur Klassen verwenden, die bereits im kompilierten CSS existieren oder nach Änderung `npm run build` ausführen. Neue Tailwind-Klassen (z.B. `pb-36`) werden ohne Build ignoriert! `public/build/` ist im Repo committet.

## Detail-Dokumentation

- **[docs/PATTERNS.md](docs/PATTERNS.md)** - Code Patterns, Naming Conventions
- **[docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)** - Fehlerbehebung
- **[docs/FILE-GUIDE.md](docs/FILE-GUIDE.md)** - Datei-Navigation, wo was ist

---
*Letzte Aktualisierung: 16. März 2026*
