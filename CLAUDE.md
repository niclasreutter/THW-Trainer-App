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

**Zwei Container-Varianten:**
- `.dash-container` (max 1100px) - Dashboard, Practice-Menu, Admin Dashboard
- `.dashboard-container` (max 1200px) - Admin Sub-Seiten (Users, Fragen, etc.)

**Standard-Layout (Admin Sub-Seiten):**
```html
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Prefix <span>Gold-Text</span></h1>
        <p class="page-subtitle">Beschreibung</p>
    </header>

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">42</div>
                <div class="stat-pill-label">Label</div>
            </div>
        </div>
    </div>

    <div class="bento-grid">
        <div class="glass-gold bento-main">Hauptinhalt</div>
        <div class="glass-tl bento-side">Widget</div>
    </div>
</div>
```

**Dashboard-Layout (mit Stagger-Animation):**
```html
<div class="dash-container">
<div class="space-y-4">
    <header class="dashboard-header">
        <h1 class="page-title">Prefix <span>Gold-Text</span></h1>
        <p class="page-subtitle">Beschreibung</p>
    </header>

    <div class="stats-row">
        <div class="stat-pill">...</div>
    </div>

    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
        <h2 class="section-title">Abschnitt</h2>
    </div>

    <div class="bento-grid">
        <div class="glass-blue bento-2of3">Hauptinhalt</div>
        <div class="glass-tl bento-third">Sidebar</div>
    </div>
</div>
</div>
```

**Glass Card Varianten:**
- `.glass` - Standard
- `.glass-gold`, `.glass-blue`, `.glass-purple`, `.glass-cyan`, `.glass-green` - Lensflare-Glow
- `.glass-tl`, `.glass-br`, `.glass-slash`, `.glass-organic` - Asymmetrisch
- `.glass-thw` - THW-Blau getönt
- `.glass-accent` - Gold-Akzent links
- `.glass-featured` - Groß mit Gold-Top-Line
- `.glass-success`, `.glass-error`, `.glass-warning` - Semantisch

**Bento Grid Spans:**
- `.bento-main` (2x2) | `.bento-wide` (4 Spalten) | `.bento-half` (2) | `.bento-2of3` (2) | `.bento-third` (1) | `.bento-side` (1)

**Section Headers:** Immer Gold-Left-Border Pattern:
```html
<div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start);">
    <h2 class="section-title">Titel</h2>
</div>
```

**Buttons:** `.btn-primary` (THW-Blau Gradient) | `.btn-secondary` (Soft-Blau) | `.btn-ghost` | `.btn-danger`

**Wichtig:**
- **Keine Emojis** im UI verwenden
- **Icons nur sinnvoll** einsetzen (z.B. Status-Badges in stat-pills)
- **Buttons ohne Icons** - cleaner und professioneller
- **Asymmetrie nutzen** - Vermeide generischen "AI-Look"
- Bootstrap Icons (`bi bi-*`) für notwendige Icons
- **Stagger-Animation**: `.space-y-4` Wrapper für `dash-rise` Effekt
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
*Letzte Aktualisierung: 3. April 2026*
