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

### 2. Design-Pattern (Seitenüberschriften)
```html
<h1 class="page-title"><span>Titel</span></h1>
<p class="page-subtitle">Beschreibung</p>
```
```css
.page-title span {
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
```

**Wichtig:**
- **Keine Emojis** im UI verwenden
- **Icons nur sinnvoll** einsetzen (z.B. Status-Badges, nicht in Buttons)
- **Buttons ohne Icons** - cleaner und professioneller
- Bootstrap Icons (`bi bi-*`) für notwendige Icons

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

## Bekannte Gotchas

1. **DB-Spalte:** Immer `lernpool_id` (nicht `ortsverband_lernpool_id`)
2. **User-Rolle:** `$user->useroll` (Typo im Schema, nicht ändern)
3. **Lösung-Format:** Sortiert, komma-getrennt: `"A,B"` nicht `"B,A"`
4. **Flash-Data:** Nur für 1 Request, dann weg

## Detail-Dokumentation

- **[docs/PATTERNS.md](docs/PATTERNS.md)** - Code Patterns, Naming Conventions
- **[docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)** - Fehlerbehebung
- **[docs/FILE-GUIDE.md](docs/FILE-GUIDE.md)** - Datei-Navigation, wo was ist

---
*Letzte Aktualisierung: 27. Januar 2026*
