# Code Patterns & Conventions

## Naming Conventions

| Context | Pattern | Example |
|---------|---------|---------|
| Models | PascalCase, English | `OrtsverbandLernpool` |
| Controllers | PascalCase + Controller | `OrtsverbandLernpoolController` |
| Views | kebab-case.blade.php | `show-modal.blade.php` |
| Routes | dot.notation, German | `ortsverband.lernpools.practice` |
| Database | snake_case, German | `ortsverband_lernpools` |
| Methods | camelCase | `getMemberProgress()` |

## German vs. English

**Deutsch für:** Domain-Begriffe, Routes, DB-Tabellen, UI-Text, Commits
**Englisch für:** Laravel-Konventionen, technische Terme, Variablennamen

## Progress Tracking (Zwei-Tier-System)

```php
// Tier 1: UserQuestionProgress Model
$progress = UserQuestionProgress::getOrCreate($userId, $questionId);
$progress->updateProgress($isCorrect);
// consecutive_correct: 0 = nicht versucht, 1 = einmal richtig, 2 = GEMEISTERT

// Tier 2: User JSON Arrays
$user->solved_questions      // [1, 2, 3, ...]
$user->exam_failed_questions // [5, 7, ...]
$user->bookmarked_questions  // [10, 15, ...]
```

## Gamification Integration

```php
use App\Services\GamificationService;

public function __construct(GamificationService $gamification)
{
    $this->gamification = $gamification;
}

// Nach richtiger Antwort:
$result = $this->gamification->addPoints($user, 10, 'Frage richtig beantwortet');
$this->gamification->checkAchievements($user);
session()->flash('gamification_result', $result);
```

## Modal System Pattern

```php
// Controller - AJAX Detection
public function show(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
{
    $this->authorize('view', $lernpool);

    if (request()->ajax() || request()->query('ajax') === '1') {
        return view('ortsverband.lernpools.show-modal', compact('lernpool'));
    }
    return view('ortsverband.lernpools.show', compact('ortsverband', 'lernpool'));
}
```

```javascript
// JavaScript - Cache-Busting PFLICHT
const url = link.href + '?ajax=1&_t=' + Date.now();
fetch(url, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    cache: 'no-store'
});
```

## Practice Mode Pattern

```php
// 1. Eine Frage auswählen (Priorität: failed > unsolved > random)
$question = Question::whereNotIn('id', $solvedIds)->inRandomOrder()->first();

// 2. Antworten mischen
$answers = ['A' => $question->antwort_a, 'B' => $question->antwort_b, 'C' => $question->antwort_c];
$shuffled = collect($answers)->shuffle();
$mapping = $shuffled->keys()->all(); // z.B. ['B', 'A', 'C']

// 3. Im View
<input type="hidden" name="answer_mapping" value="{{ json_encode($mapping) }}">

// 4. Bei Submit - Antwort umwandeln
$userPositions = $request->input('answer');
$mapping = json_decode($request->input('answer_mapping'), true);
$userLetters = array_map(fn($pos) => $mapping[$pos], $userPositions);
sort($userLetters);
$userAnswer = implode(',', $userLetters); // z.B. "A,C"
```

## Session Flash Pattern

```php
// Controller
session()->flash('answer_result', [
    'is_correct' => true,
    'user_answer' => 'A,B',
    'correct_answer' => 'A,B',
]);
return redirect()->route('...');

// View
@if (session('answer_result'))
    @php $result = session('answer_result'); @endphp
    // ... anzeigen
@endif
```

## Blade Components

```blade
<x-primary-button>Speichern</x-primary-button>
<x-secondary-button>Abbrechen</x-secondary-button>
<x-danger-button>Löschen</x-danger-button>
<x-input-label for="name" value="Name" />
<x-text-input id="name" name="name" :value="old('name')" />
<x-input-error :messages="$errors->get('name')" />
<x-achievement-popup />
<x-gamification-notifications />
```

## Design System: Dark Mode Glassmorphism + Bento Grid

Das Standard-Design verwendet ein modernes Dark-Mode-Glassmorphism mit asymmetrischen Formen und Bento-Grid-Layout.

### Seitenstruktur

```html
<div class="dashboard-container">
    <!-- Header: Links ausgerichtet, nicht zentriert -->
    <header class="dashboard-header">
        <h1 class="page-title">Prefix <span>Gold-Gradient Text</span></h1>
        <p class="page-subtitle">Beschreibung</p>
    </header>

    <!-- Stats als horizontale Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-warning"><i class="bi bi-fire"></i></span>
            <div>
                <div class="stat-pill-value">42</div>
                <div class="stat-pill-label">Label</div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Layout -->
    <div class="bento-grid">
        <div class="glass-gold bento-main">Hauptinhalt (2 Spalten)</div>
        <div class="glass-tl bento-side">Sidebar-Widget</div>
        <div class="glass-br bento-side">Sidebar-Widget</div>
        <div class="glass-slash bento-wide">Volle Breite</div>
    </div>

    <!-- Section Headers -->
    <div class="section-header">
        <h2 class="section-title">Abschnitt</h2>
        <a href="#" class="section-link">Alle anzeigen</a>
    </div>
</div>
```

### Bento Grid Klassen

| Klasse | Spalten | Verwendung |
|--------|---------|------------|
| `.bento-main` | 2 cols, 2 rows | Hauptkarte (Feature) |
| `.bento-side` | 1 col | Sidebar-Widgets |
| `.bento-wide` | 3 cols | Volle Breite |
| `.bento-2of3` | 2 cols | Zwei Drittel |
| `.bento-1of3` | 1 col | Ein Drittel |

### Glass Card Varianten

```html
<!-- Standard Glass -->
<div class="glass">Inhalt</div>

<!-- Asymmetrische Varianten (wichtig: kein generischer AI-Look!) -->
<div class="glass-tl">Top-Left betont (2rem/0.75rem Ecken)</div>
<div class="glass-br">Bottom-Right betont</div>
<div class="glass-slash">Diagonal (0.5rem/2rem/0.5rem/2rem)</div>
<div class="glass-organic">Organische Form (blob-artig)</div>

<!-- Lensflare-Glow Cards (farbiger Glow im Hintergrund) -->
<div class="glass-gold">Gold-Glow - für Premium/Highlights</div>
<div class="glass-blue">Blau-Glow</div>
<div class="glass-purple">Lila-Glow</div>
<div class="glass-cyan">Cyan-Glow</div>
<div class="glass-green">Grün-Glow (z.B. Success)</div>

<!-- Semantische Cards -->
<div class="glass-thw">THW-Blau getönt</div>
<div class="glass-success">Erfolg (grün)</div>
<div class="glass-error">Fehler (rot)</div>
<div class="glass-warning">Warnung (orange)</div>
```

### Button Varianten

```html
<!-- Primär: Gold-Gradient -->
<button class="btn-primary">Hauptaktion</button>
<button class="btn-primary btn-sm">Klein</button>
<button class="btn-primary btn-lg">Groß</button>

<!-- Sekundär: THW-Blau -->
<button class="btn-secondary">Sekundäre Aktion</button>

<!-- Ghost: Transparent mit Border -->
<button class="btn-ghost">Tertiäre Aktion</button>

<!-- Danger: Rot -->
<button class="btn-danger">Löschen</button>
```

### Formular-Elemente

```html
<label class="label-glass">Label</label>
<input type="text" class="input-glass" placeholder="Eingabe...">
<select class="select-glass">...</select>
<textarea class="textarea-glass"></textarea>
<input type="checkbox" class="checkbox-glass">
```

### Badges

```html
<span class="badge-glass">Standard</span>
<span class="badge-gold">Gold/Premium</span>
<span class="badge-thw">THW-Blau</span>
<span class="badge-success">Erfolg</span>
<span class="badge-error">Fehler</span>
```

### Utility-Klassen

```html
<!-- Text -->
<span class="text-gradient-gold">Gold-Gradient Text</span>
<span class="text-gold">Gold Text</span>
<span class="text-dark-primary">Primärtext</span>
<span class="text-dark-secondary">Sekundärtext</span>
<span class="text-dark-muted">Gedämpfter Text</span>

<!-- Hover-Effekte -->
<div class="glass hover-lift">Hebt sich bei Hover</div>

<!-- Glow-Effekte -->
<div class="glow-gold">Gold-Glow</div>
<div class="glow-thw">THW-Blau-Glow</div>

<!-- Progress -->
<div class="progress-glass">
    <div class="progress-fill-gold" style="width: 75%"></div>
</div>
```

### CSS Custom Properties (wichtigste)

```css
/* Hintergründe */
--bg-base: #0a0a0b;
--bg-elevated: #121214;
--bg-surface: #1a1a1d;

/* Gold-Gradient */
--gold-start: #fbbf24;
--gold-end: #f59e0b;
--gradient-gold: linear-gradient(90deg, var(--gold-start), var(--gold-end));

/* THW-Blau */
--thw-blue: #00337F;

/* Text */
--text-primary: #f5f5f5;
--text-secondary: #a1a1aa;
--text-muted: #71717a;
```

### Wichtige Design-Regeln

1. **Asymmetrie nutzen** - Vermeide generischen "AI-Look" durch unterschiedliche border-radius
2. **Links ausrichten** - Header nicht zentrieren, sondern links ausgerichtet
3. **Stats als Pills** - Horizontal, nicht als Grid-Cards
4. **Section Headers** - Mit linkem Gold-Akzent (`border-left: 3px solid var(--gold-start)`)
5. **Hover-Effekte** - `.hover-lift` für interaktive Cards
6. **Light Mode** - Automatisch via `html.light-mode` Klasse (CSS überschreibt Variablen)
