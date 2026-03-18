# Admin Dashboard Redesign

## Zusammenfassung

Redesign der `/admin` Seite (Dashboard), um optisch an das bestehende Design-System (`/dashboard`, `/bookmarks`, `/exam-history`, `/practice-menu`) anzupassen. Command-Center-Layout mit THW-Blau als Hauptfarbe, Spaced Repetition Statistiken, Leaderboard mit Avataren, und Dark/Light Mode Support.

## Scope

### In Scope
- **Admin Dashboard View** (`resources/views/admin/dashboard.blade.php`) - kompletter Redesign
- **Admin DashboardController** - SR-Daten ergaenzen, `avatar_url` im Leaderboard
- **Admin Sidebar Links** - Quick Actions aus Dashboard entfernen, stattdessen in Sidebar

### Out of Scope
- Andere Admin-Seiten (users, questions, lehrgaenge, etc.)
- Neue Routes oder Controller
- Datenbankmigrationen

## Design-Entscheidungen

### Layout: Command Center (Variante C)
- **Container:** `dash-container` (1100px max) statt custom `dashboard-container` (1200px)
- **Header:** Standard-Pattern (Prefix + Barlow Condensed Gradient-Title + Subtitle)
- **Reihenfolge:** Header > KPI-Bar > Activity+Stats > SR-Sektion > Leaderboard > Charts

### Farbgebung: THW-Blau dominant
- **Primaer:** `#5b9aff` / `#0055cc` / `#00337F` fuer KPIs, Icons, Werte
- **Gold nur fuer:** Trophy-Icons (Top 3), Gold-Gradient-Text (Platz 1), NEU/OFFEN Badges
- **Purple (#8b5cf6):** SR-Sektion als eigene Farbwelt
- **Semantisch:** Success (#22c55e), Error (#ef4444), Warning (#f59e0b)

### Glass Card Zuordnung
| Sektion | Card-Klasse | Grund |
|---------|-------------|-------|
| KPI-Bar | `.glass-thw` | THW-Blau-Tint, asymmetrischer Radius |
| Activity Feed | `.glass-blue` | Blauer Lensflare, breit (3/4) |
| Uebersicht Stats | `.glass-tl` | Top-left asymmetrisch |
| SR Uebersicht | `.glass-purple` | Lila Lensflare, neues Feature |
| SR Due Reviews | `.glass-slash` | Slash-Asymmetrie |
| Leaderboard | `.glass-br` | Bottom-right asymmetrisch, full-width |
| Charts | `.glass` / `.glass-slash` | Standard + Slash fuer Wachstum |

## Sektionen im Detail

### 1. Header + System Status
- Links: Prefix "Administration" + Title "System Dashboard" + Subtitle
- Rechts: System-Pills (DB, Cache, Online, Issues, Ungelesen)
- System-Pills als `.sys-pill` (kompakte Rounded-Pills mit Status-Dots)
- Issues/Ungelesen als klickbare Links mit farbigem Border

### 2. KPI-Bar
- `.glass-thw` mit `border-radius: 2rem 0.75rem 2rem 0.75rem`
- Horizontales Layout mit Divider-Linien
- Werte: Benutzer, Verifiziert (%), Beantwortet, Erfolgsrate (%), Heute neu
- Font: Barlow Condensed 800, 1.75rem
- Labels: IBM Plex Mono, 0.5625rem, uppercase
- Responsive: Divider ausblenden auf Mobile, Gap verkleinern

### 3. Activity Feed (3/4) + Stats (1/4)
- Activity: `glass-blue bento-2of3`, 2-Spalten-Grid fuer Items
- Stats: `glass-tl bento-third`, Stat-Rows fuer Aktivitaet + Fortschritt
- Activity-Items: Icon (farbcodiert) + Titel/Desc + Zeitstempel
- Mobile: 1-Spalte fuer beide

### 4. Spaced Repetition (NEU)
- **SR Uebersicht** (`glass-purple bento-half`):
  - 3 Gami-Pills: User aktiv, Fragen im SR, Gemeistert
  - Stat-Rows: Ø Intervall, Ø Easiness Factor, Mastery-Rate
- **Faellige Reviews** (`glass-slash bento-half`):
  - Prominente Zahl: Reviews faellig heute (2rem, purple)
  - Stat-Rows: Morgen, Woche, Ueberfaellig
  - Intervall-Verteilungs-Balken (segmentiert: 1-3d, 4-7d, 8-14d, 15d+)

### 5. Leaderboard (Full-Width)
- `glass-br` mit 2-Spalten-Grid
- Jeder Eintrag: Rang-Badge + Avatar (28px, rund) + Name/Level + Score/Fragen
- Top 3: Differenzierte Hintergruende (Gold/Silber/Bronze Gradient)
- Platz 1: Gold-Gradient-Text fuer Score
- Mobile: 1-Spalte

### 6. Charts (Chart.js - bestehend)
- 3 Charts bleiben erhalten mit identischer Funktionalitaet
- Benutzeraktivitaet (Line: Aktive + Registrierungen)
- Beantwortete Fragen (Line: Gesamt + Richtig + Falsch + Trend)
- Benutzer-Wachstum (Line: Gesamt + Unbestaetigt)
- Chart-Styling: THW-Blau Palette statt gemischt

### Quick Actions -> Sidebar
- Quick Actions Block komplett entfernen
- Admin-Links in Sidebar (`layouts/app.blade.php`) sind bereits vorhanden
- Keine Aenderung an Sidebar noetig (Links existieren schon)

## Controller-Aenderungen

### DashboardController
Neue Daten fuer SR-Sektion:

```php
// Spaced Repetition Stats
$srStats = [
    'active_users' => User::whereHas('questionProgress', fn($q) =>
        $q->whereNotNull('next_review_at')
    )->count(),

    'total_in_sr' => UserQuestionProgress::whereNotNull('next_review_at')
        ->orWhere('review_interval', '>', 0)
        ->count(),

    'mastered' => UserQuestionProgress::whereNull('next_review_at')
        ->where('consecutive_correct', '>=', 3)
        ->count(),

    'due_today' => UserQuestionProgress::whereNotNull('next_review_at')
        ->where('next_review_at', '<=', now())
        ->count(),

    'due_tomorrow' => UserQuestionProgress::whereNotNull('next_review_at')
        ->whereBetween('next_review_at', [now(), now()->addDay()])
        ->count(),

    'due_this_week' => UserQuestionProgress::whereNotNull('next_review_at')
        ->whereBetween('next_review_at', [now(), now()->endOfWeek()])
        ->count(),

    'overdue' => UserQuestionProgress::whereNotNull('next_review_at')
        ->where('next_review_at', '<', now()->startOfDay())
        ->count(),

    'avg_interval' => round(UserQuestionProgress::where('review_interval', '>', 0)
        ->avg('review_interval'), 1),

    'avg_easiness' => round(UserQuestionProgress::where('review_interval', '>', 0)
        ->avg('easiness_factor'), 2),

    'interval_distribution' => [
        '1-3' => UserQuestionProgress::whereBetween('review_interval', [1, 3])->count(),
        '4-7' => UserQuestionProgress::whereBetween('review_interval', [4, 7])->count(),
        '8-14' => UserQuestionProgress::whereBetween('review_interval', [8, 14])->count(),
        '15+' => UserQuestionProgress::where('review_interval', '>', 14)->count(),
    ],
];
```

### Leaderboard: Avatar hinzufuegen
```php
// In getLeaderboard() - avatar_path zum select hinzufuegen
$users = User::select('id', 'name', 'email', 'avatar_path', 'solved_questions', ...)
```

Die View nutzt dann `$user->avatar_url` (Accessor im User Model vorhanden).

## Fonts

Bunny Fonts Import (DSGVO-konform):
```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
```

## Dark/Light Mode

- Dark Mode: Standard (CSS Variables aus app.css)
- Light Mode: `html.light-mode` Overrides fuer alle custom Styles
- Glass Cards: Weisser Hintergrund, keine Backdrop-Filter, subtile Box-Shadows
- Text/Icons: Angepasste Farben (#00337F statt #5b9aff)

## Responsive Breakpoints

| Breakpoint | Verhalten |
|-----------|-----------|
| > 900px | 4-Spalten Bento Grid, 2-Col Activity/Leaderboard |
| 600-900px | 2-Spalten Bento Grid |
| < 600px | 1-Spalte, KPI-Divider hidden, kleinere Fonts |

## Animations

- Stagger-Animation (`dash-rise`) auf alle Sektionen via `.space-y-4 > *`
- Hover-Lift auf interaktive Elemente
- `prefers-reduced-motion` Support

## Dateien die geaendert werden

1. `resources/views/admin/dashboard.blade.php` - Komplett-Redesign
2. `app/Http/Controllers/Admin/DashboardController.php` - SR-Stats + Avatar im Leaderboard
3. `npm run build` nach Aenderung

## Mockup-Referenz

Finales Mockup: `.superpowers/brainstorm/41477-1773865471/admin-v3.html`
