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

## Dashboard Design Pattern

```html
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">📚 <span>Titel mit Gradient</span></h1>
            <p class="dashboard-subtitle">Beschreibung</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ $count }}</div>
                <div class="stat-label">Label</div>
            </div>
        </div>

        <div class="info-card">
            <h2 class="info-title">Abschnitt</h2>
            <!-- Inhalt -->
        </div>
    </div>
</div>
```

**Wichtige CSS-Klassen:**
- `.dashboard-greeting span` - Gold-Gradient Text
- `.stat-card` - Hover-Effekt mit translateY(-4px)
- `.btn-primary` - Gradient Background + Hover-Animation
- `.action-btn-progress` - Gold-Gradient für wichtige Aktionen
- `.action-btn-delete` - Rot-Gradient für Lösch-Aktionen
