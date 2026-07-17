# AGENTS.md - THW-Trainer-App

> Kompakte Anleitung für AI Assistants. Details in `docs/*.md`.
>
> **Hinweis:** Alle weiteren KI-/LLM-spezifischen Dokumente (Feature-Notes, Fix-Reports, Setup-Guides, TODO_AI etc.) liegen im Ordner [`LLM/`](LLM/).

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

### 2. Design-Pattern (Admin/Dashboard-Seiten)
```html
<div class="dashboard-header">
    <h1 class="dashboard-greeting">📚 <span>Titel</span></h1>
    <p class="dashboard-subtitle">Beschreibung</p>
</div>
```
```css
.dashboard-greeting span {
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
```

### 3. Nach jeder Änderung
1. **CHANGELOG.md pflegen (PFLICHT):** Jede nutzersichtbare Änderung bekommt einen Eintrag
   unter `## [Unreleased]` (Kategorien: `Neue Features` / `Bugfixes` / `UI & Design` /
   `Performance` / `Sicherheit` / `Intern`). Rein interne Refactorings unter `Intern`.
2. Assets bauen und Caches leeren:
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

## Bekannte Gotchas

1. **DB-Spalte:** Immer `lernpool_id` (nicht `ortsverband_lernpool_id`)
2. **User-Rolle:** `$user->useroll` (Typo im Schema, nicht ändern)
3. **Lösung-Format:** Sortiert, komma-getrennt: `"A,B"` nicht `"B,A"`
4. **Flash-Data:** Nur für 1 Request, dann weg

## Versionierung & Changelog

- **`CHANGELOG.md`** (Repo-Root, Keep-a-Changelog-Format) wird öffentlich unter `/wiki/changelog` gerendert
- **`VERSION`**-Datei = Single Source of Truth für das Footer-Badge (`<x-version-badge />` in allen Footern)
- **Release erstellen:** `php artisan app:release X.Y.Z` verschiebt `[Unreleased]` in einen neuen
  Versionsabschnitt und aktualisiert VERSION, Vergleichs-Links und composer.json. Danach committen + taggen.
- **Wiki-Inhalte:** Markdown in `resources/wiki/`, Navigation in `config/wiki.php`

## Detail-Dokumentation

- **[docs/PATTERNS.md](docs/PATTERNS.md)** - Code Patterns, Naming Conventions
- **[docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)** - Fehlerbehebung
- **[docs/FILE-GUIDE.md](docs/FILE-GUIDE.md)** - Datei-Navigation, wo was ist

---
*Letzte Aktualisierung: 12. Januar 2026*
