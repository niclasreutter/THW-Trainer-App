# Unified Practice View — Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Unify three separate practice implementations (Global, Lehrgänge, Lernpools) into one shared view and centralized session service.

**Architecture:** A `ProgressResolverInterface` with three implementations abstracts progress tracking. A `PracticeSessionService` handles all session logic (start, current question, submit, summary). Controllers become thin wrappers that select questions and delegate to the service. One `practice.blade.php` and one `practice-summary.blade.php` serve all three contexts via a `$practiceData` array.

**Tech Stack:** Laravel 12, Blade, Tailwind CSS, Alpine.js

**Spec:** `docs/superpowers/specs/2026-03-17-unified-practice-view-design.md`

---

## Chunk 1: Service Layer (Tasks 1–4)

### Task 1: Create ProgressResolverInterface

**Files:**
- Create: `app/Contracts/ProgressResolverInterface.php`

- [ ] **Step 1: Create the interface file**

```php
<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ProgressResolverInterface
{
    /**
     * Get progress record for a user/question pair.
     * Returns object with at least `consecutive_correct` property, or null.
     */
    public function getProgress(int $userId, int $questionId): ?object;

    /**
     * Update progress after an answer. Handles consecutive_correct, solved flag,
     * and any model-specific columns (e.g. total_attempts for Lernpool).
     */
    public function updateProgress(int $userId, int $questionId, bool $correct): object;

    /**
     * Check if user has mastered this question.
     * Global: $progress->isMastered(), Lehrgang/Lernpool: $progress->solved === true
     */
    public function isMastered(int $userId, int $questionId): bool;

    public function getQuestionById(int $id): object;

    public function getQuestionsByIds(array $ids): Collection;

    /**
     * Record a statistic entry (answer history).
     */
    public function createStatistic(int $userId, int $questionId, bool $correct): void;
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Contracts/ProgressResolverInterface.php
git commit -m "✨: ProgressResolverInterface erstellt"
```

---

### Task 2: Create GlobalProgressResolver

**Files:**
- Create: `app/Services/ProgressResolvers/GlobalProgressResolver.php`
- Reference: `app/Http/Controllers/PracticeController.php:597-656` (existing progress logic)
- Reference: `app/Models/UserQuestionProgress.php`
- Reference: `app/Models/Question.php`
- Reference: `app/Models/QuestionStatistic.php`

- [ ] **Step 1: Create GlobalProgressResolver**

Extract the progress logic from `PracticeController::submit()` (lines 597-656). Key behaviors:
- `updateProgress()`: Creates or updates `UserQuestionProgress`. If correct, increments `consecutive_correct`. If wrong, resets to 0. Returns the progress object.
- `isMastered()`: Uses `UserQuestionProgress::isMastered()` method (checks `consecutive_correct >= MASTERY_THRESHOLD`).
- `createStatistic()`: Creates a `QuestionStatistic` record.

```php
<?php

namespace App\Services\ProgressResolvers;

use App\Contracts\ProgressResolverInterface;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use Illuminate\Support\Collection;

class GlobalProgressResolver implements ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object
    {
        return UserQuestionProgress::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function updateProgress(int $userId, int $questionId, bool $correct): object
    {
        $progress = UserQuestionProgress::firstOrCreate(
            ['user_id' => $userId, 'question_id' => $questionId],
            ['consecutive_correct' => 0]
        );

        if ($correct) {
            $progress->consecutive_correct++;
        } else {
            $progress->consecutive_correct = 0;
        }

        $progress->last_answered_at = now();
        $progress->save();

        return $progress;
    }

    public function isMastered(int $userId, int $questionId): bool
    {
        $progress = $this->getProgress($userId, $questionId);
        return $progress ? $progress->isMastered() : false;
    }

    public function getQuestionById(int $id): object
    {
        return Question::findOrFail($id);
    }

    public function getQuestionsByIds(array $ids): Collection
    {
        return Question::whereIn('id', $ids)->get();
    }

    public function createStatistic(int $userId, int $questionId, bool $correct): void
    {
        QuestionStatistic::create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'is_correct' => $correct,
            'source' => 'practice',
        ]);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Services/ProgressResolvers/GlobalProgressResolver.php
git commit -m "✨: GlobalProgressResolver erstellt"
```

---

### Task 3: Create LehrgangProgressResolver

**Files:**
- Create: `app/Services/ProgressResolvers/LehrgangProgressResolver.php`
- Reference: `app/Http/Controllers/LehrgangController.php:470-530` (existing progress logic)
- Reference: `app/Models/UserLehrgangProgress.php` — FK is `lehrgang_question_id`
- Reference: `app/Models/LehrgangQuestion.php`
- Reference: `app/Models/LehrgangQuestionStatistic.php`

- [ ] **Step 1: Create LehrgangProgressResolver**

Key difference from Global: FK column is `lehrgang_question_id` (not `question_id`). When mastered, sets `solved = true`.

```php
<?php

namespace App\Services\ProgressResolvers;

use App\Contracts\ProgressResolverInterface;
use App\Models\LehrgangQuestion;
use App\Models\LehrgangQuestionStatistic;
use App\Models\UserLehrgangProgress;
use Illuminate\Support\Collection;

class LehrgangProgressResolver implements ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object
    {
        return UserLehrgangProgress::where('user_id', $userId)
            ->where('lehrgang_question_id', $questionId)
            ->first();
    }

    public function updateProgress(int $userId, int $questionId, bool $correct): object
    {
        $progress = UserLehrgangProgress::firstOrCreate(
            ['user_id' => $userId, 'lehrgang_question_id' => $questionId],
            ['consecutive_correct' => 0, 'solved' => false]
        );

        if ($correct) {
            $progress->consecutive_correct++;
            $progress->failed = false;
            if ($progress->consecutive_correct >= 3) { // MASTERY_THRESHOLD
                $progress->solved = true;
            }
        } else {
            $progress->consecutive_correct = 0;
            $progress->failed = true;
        }

        $progress->save();

        return $progress;
    }

    public function isMastered(int $userId, int $questionId): bool
    {
        $progress = $this->getProgress($userId, $questionId);
        return $progress ? (bool) $progress->solved : false;
    }

    public function getQuestionById(int $id): object
    {
        return LehrgangQuestion::findOrFail($id);
    }

    public function getQuestionsByIds(array $ids): Collection
    {
        return LehrgangQuestion::whereIn('id', $ids)->get();
    }

    public function createStatistic(int $userId, int $questionId, bool $correct): void
    {
        LehrgangQuestionStatistic::create([
            'user_id' => $userId,
            'lehrgang_question_id' => $questionId,
            'is_correct' => $correct,
        ]);
    }
}
```

**Note:** Check that `UserLehrgangProgress::MASTERY_THRESHOLD` exists. If not, use the constant `3` directly or define it. The global model `UserQuestionProgress` has `MASTERY_THRESHOLD = 3`.

- [ ] **Step 2: Verify MASTERY_THRESHOLD exists on UserLehrgangProgress**

Read `app/Models/UserLehrgangProgress.php` and check for the constant. If missing, add:
```php
const MASTERY_THRESHOLD = 3;
```

- [ ] **Step 3: Commit**

```bash
git add app/Services/ProgressResolvers/LehrgangProgressResolver.php
git commit -m "✨: LehrgangProgressResolver erstellt"
```

---

### Task 4: Create LernpoolProgressResolver

**Files:**
- Create: `app/Services/ProgressResolvers/LernpoolProgressResolver.php`
- Reference: `app/Http/Controllers/OrtsverbandLernpoolPracticeController.php:230-249` (existing progress logic)
- Reference: `app/Models/OrtsverbandLernpoolProgress.php` — FK is `question_id`, has extra columns `total_attempts`, `correct_attempts`

- [ ] **Step 1: Create LernpoolProgressResolver**

Key differences:
- FK column is `question_id` (not `lehrgang_question_id`)
- Must also update `total_attempts` and `correct_attempts` columns
- Mastery check: `$progress->solved === true`

```php
<?php

namespace App\Services\ProgressResolvers;

use App\Contracts\ProgressResolverInterface;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\OrtsverbandLernpoolProgress;
use Illuminate\Support\Collection;

class LernpoolProgressResolver implements ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object
    {
        return OrtsverbandLernpoolProgress::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function updateProgress(int $userId, int $questionId, bool $correct): object
    {
        $progress = OrtsverbandLernpoolProgress::firstOrCreate(
            ['user_id' => $userId, 'question_id' => $questionId],
            ['consecutive_correct' => 0, 'total_attempts' => 0, 'correct_attempts' => 0, 'solved' => false]
        );

        $progress->total_attempts++;

        if ($correct) {
            $progress->correct_attempts++;
            $progress->consecutive_correct++;
            if ($progress->consecutive_correct >= 3) {
                $progress->solved = true;
            }
        } else {
            $progress->consecutive_correct = 0;
        }

        $progress->save();

        return $progress;
    }

    public function isMastered(int $userId, int $questionId): bool
    {
        $progress = $this->getProgress($userId, $questionId);
        return $progress ? (bool) $progress->solved : false;
    }

    public function getQuestionById(int $id): object
    {
        return OrtsverbandLernpoolQuestion::findOrFail($id);
    }

    public function getQuestionsByIds(array $ids): Collection
    {
        return OrtsverbandLernpoolQuestion::whereIn('id', $ids)->get();
    }

    public function createStatistic(int $userId, int $questionId, bool $correct): void
    {
        OrtsverbandLernpoolQuestionStatistic::create([
            'user_id' => $userId,
            'lernpool_question_id' => $questionId,
            'is_correct' => $correct,
        ]);
    }
}
```

**Note:** Verify `OrtsverbandLernpoolQuestionStatistic` model exists. If not, check for the correct model name by searching for files matching `*Statistic*` in `app/Models/`.

- [ ] **Step 2: Verify statistic model exists**

Run: `ls app/Models/*Statistic*` and `ls app/Models/*statistic*`

If `OrtsverbandLernpoolQuestionStatistic` doesn't exist, check how the existing controller records statistics (it may use a different approach or skip statistics entirely). Adjust accordingly.

- [ ] **Step 3: Commit**

```bash
git add app/Services/ProgressResolvers/LernpoolProgressResolver.php
git commit -m "✨: LernpoolProgressResolver erstellt"
```

---

### Task 5: Create PracticeSessionService

**Files:**
- Create: `app/Services/PracticeSessionService.php`
- Reference: `app/Http/Controllers/PracticeController.php:426-442` (session init), `567-738` (submit), `743-776` (summary)
- Reference: `app/Services/GamificationService.php` (awardQuestionPoints signature)

- [ ] **Step 1: Create PracticeSessionService with session management helpers**

```php
<?php

namespace App\Services;

use App\Contracts\ProgressResolverInterface;

class PracticeSessionService
{
    private ProgressResolverInterface $resolver;
    private GamificationService $gamification;

    public function __construct(ProgressResolverInterface $resolver, GamificationService $gamification)
    {
        $this->resolver = $resolver;
        $this->gamification = $gamification;
    }

    /**
     * Generate context-specific session key prefix.
     * Global: 'practice', Lehrgang: 'practice_lehrgang_5', Lernpool: 'practice_lernpool_3'
     */
    private function prefix(string $context, ?string $contextId): string
    {
        if ($context === 'global') {
            return 'practice';
        }
        return "practice_{$context}_{$contextId}";
    }

    private function sessionKey(string $context, ?string $contextId, string $key): string
    {
        return $this->prefix($context, $contextId) . "_{$key}";
    }

    public function startSession(
        string $context,
        ?string $contextId,
        array $questionIds,
        string $mode,
        string $requeueStrategy = 'requeue'
    ): void {
        $prefix = $this->prefix($context, $contextId);

        // Clean up any existing session for this context
        $this->cleanSession($context, $contextId);

        session([
            "{$prefix}_ids" => $questionIds,
            "{$prefix}_mode" => $mode,
            "{$prefix}_total" => count($questionIds),
            "{$prefix}_requeue" => $requeueStrategy,
            "{$prefix}_stats" => [
                'correct' => 0,
                'incorrect' => 0,
                'points' => 0,
                'mastered' => 0,
                'started_at' => now()->timestamp,
            ],
        ]);
    }

    /**
     * Get current question ID from session, or null if session is done.
     */
    public function getCurrentQuestionId(string $context, ?string $contextId): ?int
    {
        $ids = session($this->sessionKey($context, $contextId, 'ids'), []);
        return count($ids) > 0 ? (int) $ids[0] : null;
    }

    /**
     * Get the answer result from last submission (for feedback display), then clear it.
     */
    public function getAndClearAnswerResult(string $context, ?string $contextId): ?array
    {
        $key = $this->sessionKey($context, $contextId, 'answer_result');
        $result = session($key);
        session()->forget($key);
        return $result;
    }

    /**
     * Get gamification result from last submission, then clear it.
     */
    public function getAndClearGamificationResult(string $context, ?string $contextId): ?array
    {
        $key = $this->sessionKey($context, $contextId, 'gamification_result');
        $result = session($key);
        session()->forget($key);
        return $result;
    }

    /**
     * Submit an answer. Returns the answer_result array for feedback.
     *
     * @param array $userAnswer Already mapped letter array, e.g. ['A', 'B']
     * @param array $answerMapping Position-to-letter mapping from the view
     */
    public function submitAnswer(
        string $context,
        ?string $contextId,
        int $questionId,
        array $userAnswer,
        array $answerMapping
    ): array {
        $prefix = $this->prefix($context, $contextId);
        $userId = auth()->id();
        $question = $this->resolver->getQuestionById($questionId);

        // Compare answers
        $correctAnswer = array_map('trim', explode(',', $question->loesung));
        sort($correctAnswer);
        sort($userAnswer);
        $isCorrect = $userAnswer === $correctAnswer;

        // Update progress
        $progress = $this->resolver->updateProgress($userId, $questionId, $isCorrect);

        // Create statistic
        $this->resolver->createStatistic($userId, $questionId, $isCorrect);

        // Check mastery
        $mastered = $this->resolver->isMastered($userId, $questionId);

        // Gamification
        $gamificationResult = $this->gamification->awardQuestionPoints(auth()->user(), $isCorrect, $questionId);

        // Update session stats
        $stats = session("{$prefix}_stats", []);
        if ($isCorrect) {
            $stats['correct'] = ($stats['correct'] ?? 0) + 1;
        } else {
            $stats['incorrect'] = ($stats['incorrect'] ?? 0) + 1;
        }
        if (isset($gamificationResult['points'])) {
            $stats['points'] = ($stats['points'] ?? 0) + ($gamificationResult['points'] ?? 0);
        }
        if ($mastered) {
            $stats['mastered'] = ($stats['mastered'] ?? 0) + 1;
        }
        session(["{$prefix}_stats" => $stats]);

        // Handle queue: remove or requeue
        $ids = session("{$prefix}_ids", []);
        $requeueStrategy = session("{$prefix}_requeue", 'requeue');
        $currentIndex = array_search($questionId, $ids);

        if ($currentIndex !== false) {
            unset($ids[$currentIndex]);
            $ids = array_values($ids);

            // Requeue if not mastered and strategy is 'requeue'
            if (!$mastered && $requeueStrategy === 'requeue') {
                $ids[] = $questionId;
            }

            session(["{$prefix}_ids" => $ids]);
        }

        // Build answer result for feedback display
        $answerResult = [
            'question_id' => $questionId,
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'question_progress' => $progress->consecutive_correct,
            'answer_mapping' => $answerMapping,
            'mastered' => $mastered,
        ];

        // Store in session for feedback display
        session([
            "{$prefix}_answer_result" => $answerResult,
            "{$prefix}_gamification_result" => $gamificationResult,
        ]);

        return $answerResult;
    }

    /**
     * Get progress data for the view.
     */
    public function getProgress(string $context, ?string $contextId): array
    {
        $prefix = $this->prefix($context, $contextId);
        $stats = session("{$prefix}_stats", []);
        $ids = session("{$prefix}_ids", []);
        $total = session("{$prefix}_total", 0);

        $answered = ($stats['correct'] ?? 0) + ($stats['incorrect'] ?? 0);

        return [
            'current' => $answered + 1,
            'total' => $total,
            'correct' => $stats['correct'] ?? 0,
            'incorrect' => $stats['incorrect'] ?? 0,
            'points' => $stats['points'] ?? 0,
            'mastered' => $stats['mastered'] ?? 0,
            'remaining' => count($ids),
        ];
    }

    /**
     * End session and return summary data.
     */
    public function endSession(string $context, ?string $contextId): array
    {
        $prefix = $this->prefix($context, $contextId);
        $stats = session("{$prefix}_stats", []);
        $mode = session("{$prefix}_mode", 'all');

        $totalAnswered = ($stats['correct'] ?? 0) + ($stats['incorrect'] ?? 0);
        $accuracy = $totalAnswered > 0
            ? round(($stats['correct'] ?? 0) / $totalAnswered * 100)
            : 0;

        $startedAt = $stats['started_at'] ?? now()->timestamp;
        $durationMinutes = max(1, (int) round((now()->timestamp - $startedAt) / 60));

        $modeNames = [
            'all' => 'Alle Fragen',
            'unsolved' => 'Ungelöste Fragen',
            'section' => 'Lernabschnitt',
            'failed' => 'Fehlgeschlagene Fragen',
            'search' => 'Suche',
            'spaced_repetition' => 'Wiederholung',
            'bookmarked' => 'Lesezeichen',
        ];

        $summary = [
            'correct' => $stats['correct'] ?? 0,
            'incorrect' => $stats['incorrect'] ?? 0,
            'accuracy' => $accuracy,
            'points' => $stats['points'] ?? 0,
            'mastered' => $stats['mastered'] ?? 0,
            'totalAnswered' => $totalAnswered,
            'durationMinutes' => $durationMinutes,
            'modeName' => $modeNames[$mode] ?? $mode,
        ];

        // Clean up session
        $this->cleanSession($context, $contextId);

        return $summary;
    }

    /**
     * Check if session has questions remaining.
     */
    public function hasQuestionsRemaining(string $context, ?string $contextId): bool
    {
        $ids = session($this->sessionKey($context, $contextId, 'ids'), []);
        return count($ids) > 0;
    }

    /**
     * Clean up all session keys for a context.
     */
    public function cleanSession(string $context, ?string $contextId): void
    {
        $prefix = $this->prefix($context, $contextId);
        $keys = ['_ids', '_mode', '_total', '_requeue', '_stats', '_answer_result', '_gamification_result',
                 '_skipped', '_session_stats', '_total_in_mode', '_parameter'];

        foreach ($keys as $key) {
            session()->forget($prefix . $key);
        }
    }
}
```

- [ ] **Step 2: Verify GamificationService::awardQuestionPoints() signature**

Read `app/Services/GamificationService.php` and check the exact method signature and return value. Adjust the `submitAnswer()` call if needed (e.g., different parameter names or return keys).

- [ ] **Step 3: Commit**

```bash
git add app/Services/PracticeSessionService.php
git commit -m "✨: PracticeSessionService erstellt"
```

---

## Chunk 2: View Parametrization (Tasks 6–7)

### Task 6: Parametrize practice.blade.php

**Files:**
- Modify: `resources/views/practice.blade.php`
- Reference: Hardcoded routes at approximately lines 353, 389 and session reads at lines 6-26

This is the largest and most delicate task. The view is ~1000 lines. We need to replace hardcoded routes and add context conditionals WITHOUT breaking the existing global practice.

- [ ] **Step 1: Read practice.blade.php to identify all hardcoded references**

Search for these patterns in the file:
- `route('practice.submit')` → replace with `$submitUrl`
- `route('practice.index')` → replace with `$showUrl`
- `route('practice.summary')` → replace with `$summaryUrl`
- `route('practice.menu')` → conditional, only for global
- `route('bookmark.` → conditional, only for global
- Direct `session('answer_result')` reads → replace with `$answerResult` variable
- Direct `session('gamification_result')` reads → replace with `$gamificationResult` variable
- Any reference to `$mode`, `$progress`, `$total` etc. that might clash with new variable names

- [ ] **Step 2: Replace hardcoded route references with backward-compat fallbacks**

Replace ALL occurrences using fallback defaults so the view works before AND after controller refactor:
- `route('practice.submit')` → `{{ $submitUrl ?? route('practice.submit') }}`
- `route('practice.index')` → `{{ $showUrl ?? route('practice.index') }}`

Use `replace_all` for each pattern since they appear multiple times. The `?? route(...)` fallbacks will be removed in Task 12 (cleanup) once all controllers pass the new variables.

- [ ] **Step 3: Replace session reads with view variables**

Currently the view reads `session('answer_result')` directly. Change the top of the file so it receives these as view variables instead:
- `$answerResult` — passed from controller (already read and cleared)
- `$gamificationResult` — passed from controller (already read and cleared)

- [ ] **Step 4: Add context conditionals**

Wrap global-only elements:
```blade
@if($context === 'global')
    {{-- Bookmark button --}}
    {{-- Skip button --}}
    {{-- Practice menu link --}}
@endif

@if($difficultyInfo)
    {{-- Difficulty badge --}}
@endif

@if($isSpacedRepetition)
    {{-- SR indicator --}}
@endif

@if($issueUrl)
    {{-- Issue report button: use $issueUrl --}}
@endif
```

- [ ] **Step 5: Update header to use $contextLabel**

Replace any hardcoded title like "Übungsmodus" or mode-specific headers with `$contextLabel`.

- [ ] **Step 6: Run `npm run build && php artisan view:clear && php artisan cache:clear`**

Verify no Blade syntax errors.

- [ ] **Step 7: Commit**

```bash
git add resources/views/practice.blade.php
git commit -m "🎨: practice.blade.php parametrisiert"
```

---

### Task 7: Parametrize practice-summary.blade.php + Scroll Fix

**Files:**
- Modify: `resources/views/practice-summary.blade.php`

- [ ] **Step 1: Read practice-summary.blade.php**

Identify:
- Hardcoded routes (`route('practice.menu')`, `route('dashboard')`)
- Variables expected (`$stats`, `$totalAnswered`, `$accuracy`, etc.)
- What causes the scroll issue (likely excess padding/margin or stagger animation containers)

- [ ] **Step 2: Replace hardcoded routes**

- `route('practice.menu')` → `{{ $backUrl }}` (or `$menuUrl`)
- `route('dashboard')` → keep as-is (dashboard is always valid)

- [ ] **Step 3: Add $completed conditional**

For Lehrgang completion:
```blade
@if($completed ?? false)
    {{-- Completion celebration message --}}
    <div class="glass-gold ...">
        <h2>Lehrgang abgeschlossen!</h2>
        ...
    </div>
@endif
```

- [ ] **Step 4: Fix scroll issue**

Add `overflow-hidden` to the main container and ensure content fits viewport:
- Wrap the main content in a `h-screen overflow-hidden` container
- Remove excessive padding/margins
- Check stagger animations (lines ~222-235) — they may create invisible overflow

- [ ] **Step 5: Run `npm run build && php artisan view:clear && php artisan cache:clear`**

- [ ] **Step 6: Commit**

```bash
git add resources/views/practice-summary.blade.php
git commit -m "🎨: Summary parametrisiert + Scroll-Fix"
```

---

## Chunk 3: Refactor Global PracticeController (Task 8)

### Task 8: Refactor PracticeController to use PracticeSessionService

**Files:**
- Modify: `app/Http/Controllers/PracticeController.php`

This is the critical regression-risk task. The global practice must keep working exactly as before.

- [ ] **Step 1: Add constructor injection**

Add `PracticeSessionService` and `GlobalProgressResolver` to the controller constructor. Since the service needs the resolver injected, either:
- Instantiate the service manually with the resolver in the constructor, OR
- Use Laravel's container binding

Recommended: Manual instantiation in constructor for clarity:
```php
public function __construct()
{
    $this->practiceService = new PracticeSessionService(
        new GlobalProgressResolver(),
        new GamificationService()
    );
}
```

- [ ] **Step 2: Refactor practiceMode() to use service.startSession()**

Replace the session initialization block (approx lines 426-442) with:
```php
$this->practiceService->startSession('global', null, $idsToShow, $mode, 'remove');
```

Keep ALL the question selection logic (lines 221-365) intact — it stays in the controller.

- [ ] **Step 3: Refactor show() to build $practiceData array**

Replace the direct variable passing with the unified data array:
```php
$answerResult = $this->practiceService->getAndClearAnswerResult('global', null);
$gamificationResult = $this->practiceService->getAndClearGamificationResult('global', null);
$progress = $this->practiceService->getProgress('global', null);

// ... existing difficultyInfo, isSpacedRepetition, bookmarked logic stays ...

return view('practice', [
    'context' => 'global',
    'contextLabel' => $this->getModeLabel($mode),
    'backUrl' => route('practice.menu'),
    'submitUrl' => route('practice.submit'),
    'showUrl' => route('practice.index'),
    'summaryUrl' => route('practice.summary'),
    'issueUrl' => null, // Issue URL is per-question, handled via JS in view
    'question' => $question,
    'answerResult' => $answerResult,
    'gamificationResult' => $gamificationResult,
    'progress' => $progress,
    'mode' => $mode,
    'sectionName' => $sectionName ?? null,
    'difficultyInfo' => $difficultyInfo ?? null,
    'isSpacedRepetition' => $isSpacedRepetition ?? false,
    'bookmarked' => $bookmarked ?? false,
]);
```

- [ ] **Step 4: Refactor submit() to use service.submitAnswer()**

Replace the answer processing, progress tracking, and session management with:
```php
// Answer mapping (keep existing unmap logic)
$answerMapping = json_decode($request->answer_mapping, true);
$userAnswer = []; // ... existing mapping logic ...

$result = $this->practiceService->submitAnswer(
    'global', null, $question->id, $userAnswer, $answerMapping
);

// Global-only: SpacedRepetition + LernsessionService
$progress = (new GlobalProgressResolver())->getProgress(auth()->id(), $question->id);
(new SpacedRepetitionService())->processAnswer($progress, $result['is_correct']);

if ($lernsession = ...) {
    // LernsessionService::recordAnswer() — keep existing logic
}
```

**Important:** Keep `SpacedRepetitionService` and `LernsessionService` calls in the controller — they are global-only.

- [ ] **Step 5: Refactor summary() to use service.endSession()**

```php
public function summary()
{
    $summary = $this->practiceService->endSession('global', null);
    $streak = auth()->user()->streak_days ?? 0;

    return view('practice-summary', [
        'context' => 'global',
        'backUrl' => route('practice.menu'),
        ...$summary,
        'streak' => $streak,
        'completed' => false,
    ]);
}
```

- [ ] **Step 6: Run `npm run build && php artisan view:clear && php artisan cache:clear`**

- [ ] **Step 7: Manual test — verify global practice still works**

Test flow: `/practice-menu` → Select "Alle Fragen" → Answer question → See feedback → Answer more → Complete session → See summary. Verify:
- Questions load correctly
- Answer feedback displays
- Progress bar updates
- Points are awarded
- Summary shows correct stats
- Spaced Repetition still works
- Skip still works

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/PracticeController.php
git commit -m "⚡: PracticeController nutzt PracticeSessionService"
```

---

## Chunk 4: Refactor Lehrgang + Lernpool Controllers (Tasks 9–10)

### Task 9: Refactor LehrgangController Practice Methods

**Files:**
- Modify: `app/Http/Controllers/LehrgangController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add constructor injection**

```php
private PracticeSessionService $practiceService;

// In constructor or method:
$this->practiceService = new PracticeSessionService(
    new LehrgangProgressResolver(),
    new GamificationService()
);
```

- [ ] **Step 2: Refactor practice() method**

Replace session init with service call. Keep enrollment check and question selection:
```php
public function practice(string $slug)
{
    $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
    // ... existing enrollment check ...

    $openQuestionIds = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)
        ->whereDoesntHave('userProgress', fn($q) => $q->where('user_id', auth()->id())->where('solved', true))
        ->pluck('id')->toArray();

    if (empty($openQuestionIds)) {
        $openQuestionIds = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->pluck('id')->toArray();
    }

    shuffle($openQuestionIds);

    $this->practiceService->startSession('lehrgang', $lehrgang->id, $openQuestionIds, 'all', 'requeue');

    return redirect()->route('lehrgaenge.practice.show', $slug);
}
```

- [ ] **Step 3: Create practiceUnsolved() and practiceSection() methods**

```php
public function practiceUnsolved(string $slug)
{
    $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
    // enrollment check...

    $ids = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)
        ->whereDoesntHave('userProgress', fn($q) => $q->where('user_id', auth()->id())->where('solved', true))
        ->pluck('id')->toArray();

    shuffle($ids);
    $this->practiceService->startSession('lehrgang', $lehrgang->id, $ids, 'unsolved', 'requeue');

    return redirect()->route('lehrgaenge.practice.show', $slug);
}

public function practiceSection(string $slug, int $nr)
{
    $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
    // enrollment check...

    $ids = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)
        ->where('lernabschnitt', $nr)
        ->pluck('id')->toArray();

    shuffle($ids);
    $this->practiceService->startSession('lehrgang', $lehrgang->id, $ids, 'section', 'requeue');

    return redirect()->route('lehrgaenge.practice.show', $slug);
}
```

- [ ] **Step 4: Create practiceShow() method**

```php
public function practiceShow(string $slug)
{
    $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();

    if (!$this->practiceService->hasQuestionsRemaining('lehrgang', $lehrgang->id)) {
        return redirect()->route('lehrgaenge.practice.summary', $slug);
    }

    $questionId = $this->practiceService->getCurrentQuestionId('lehrgang', $lehrgang->id);
    $question = (new LehrgangProgressResolver())->getQuestionById($questionId);

    $answerResult = $this->practiceService->getAndClearAnswerResult('lehrgang', $lehrgang->id);
    $gamificationResult = $this->practiceService->getAndClearGamificationResult('lehrgang', $lehrgang->id);
    $progress = $this->practiceService->getProgress('lehrgang', $lehrgang->id);

    return view('practice', [
        'context' => 'lehrgang',
        'contextLabel' => $lehrgang->lehrgang,
        'backUrl' => route('lehrgaenge.show', $slug),
        'submitUrl' => route('lehrgaenge.practice.submit', $slug),
        'showUrl' => route('lehrgaenge.practice.show', $slug),
        'summaryUrl' => route('lehrgaenge.practice.summary', $slug),
        'issueUrl' => null, // Issue URL is per-question, handled via JS in view
        'question' => $question,
        'answerResult' => $answerResult,
        'gamificationResult' => $gamificationResult,
        'progress' => $progress,
        'mode' => session("practice_lehrgang_{$lehrgang->id}_mode", 'all'),
        'sectionName' => $this->getSectionName($lehrgang, session("practice_lehrgang_{$lehrgang->id}_mode")),
        'difficultyInfo' => null,
        'isSpacedRepetition' => false,
        'bookmarked' => false,
    ]);
}
```

- [ ] **Step 5: Create practiceSubmit() method**

```php
public function practiceSubmit(Request $request, string $slug)
{
    $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();

    // Answer mapping — same logic as existing submitAnswer()
    $answerMapping = json_decode($request->answer_mapping, true);
    $selectedPositions = $request->input('answer', []);
    $userAnswer = [];
    foreach ($selectedPositions as $pos) {
        if (isset($answerMapping[$pos])) {
            $userAnswer[] = $answerMapping[$pos];
        }
    }
    sort($userAnswer);

    $this->practiceService->submitAnswer(
        'lehrgang', $lehrgang->id, $request->question_id, $userAnswer, $answerMapping
    );

    // Check for lehrgang completion
    $totalQuestions = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
    $solvedCount = UserLehrgangProgress::where('user_id', auth()->id())
        ->whereIn('lehrgang_question_id', LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->pluck('id'))
        ->where('solved', true)->count();

    // Award enrollment points for mastered questions (+10 per mastery)
    $result = $this->practiceService->getAndClearAnswerResult('lehrgang', $lehrgang->id);
    // Re-store it so practiceShow() can display it
    if ($result) {
        session(["practice_lehrgang_{$lehrgang->id}_answer_result" => $result]);
        if ($result['mastered'] ?? false) {
            $enrollment = $lehrgang->users()->where('user_id', auth()->id())->first();
            if ($enrollment) {
                $lehrgang->users()->updateExistingPivot(auth()->id(), [
                    'punkte' => ($enrollment->pivot->punkte ?? 0) + 10,
                ]);
            }
        }
    }

    if ($solvedCount >= $totalQuestions) {
        $lehrgang->users()->updateExistingPivot(auth()->id(), [
            'completed' => true,
            'completed_at' => now(),
        ]);
    }

    return redirect()->route('lehrgaenge.practice.show', $slug);
}
```

- [ ] **Step 6: Create practiceSummary() method**

```php
public function practiceSummary(string $slug)
{
    $lehrgang = Lehrgang::where('slug', $slug)->firstOrFail();
    $summary = $this->practiceService->endSession('lehrgang', $lehrgang->id);

    // Check if lehrgang is fully completed
    $totalQuestions = LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
    $solvedCount = UserLehrgangProgress::where('user_id', auth()->id())
        ->whereIn('lehrgang_question_id', LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->pluck('id'))
        ->where('solved', true)->count();

    return view('practice-summary', [
        'context' => 'lehrgang',
        'backUrl' => route('lehrgaenge.show', $slug),
        ...$summary,
        'streak' => auth()->user()->streak_days ?? 0,
        'completed' => $solvedCount >= $totalQuestions,
    ]);
}
```

- [ ] **Step 7: Add new routes in web.php**

Find the existing Lehrgang practice routes (approx line 338-346) and replace/add:
```php
Route::get('/lehrgaenge/{slug}/practice', [LehrgangController::class, 'practice'])->name('lehrgaenge.practice');
Route::get('/lehrgaenge/{slug}/practice/unsolved', [LehrgangController::class, 'practiceUnsolved'])->name('lehrgaenge.practice.unsolved');
Route::get('/lehrgaenge/{slug}/practice/section/{nr}', [LehrgangController::class, 'practiceSection'])->name('lehrgaenge.practice.section');
Route::get('/lehrgaenge/{slug}/practice/show', [LehrgangController::class, 'practiceShow'])->name('lehrgaenge.practice.show');
Route::post('/lehrgaenge/{slug}/practice/submit', [LehrgangController::class, 'practiceSubmit'])->name('lehrgaenge.practice.submit');
Route::get('/lehrgaenge/{slug}/practice/summary', [LehrgangController::class, 'practiceSummary'])->name('lehrgaenge.practice.summary');
```

Remove old routes:
- `POST /lehrgaenge/{slug}/submit` (replaced by `/practice/submit`)
- `GET /lehrgaenge/{slug}/practice-section/{sectionNr}` (replaced by `/practice/section/{nr}`)

- [ ] **Step 8: Manual test**

Test Lehrgang practice flow: enroll in a course → Start "Alle üben" → Answer questions → Verify feedback → Complete → See summary. Check that the view looks identical to global practice.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/LehrgangController.php routes/web.php
git commit -m "✨: Lehrgang nutzt Unified Practice View"
```

---

### Task 10: Refactor OrtsverbandLernpoolPracticeController

**Files:**
- Modify: `app/Http/Controllers/OrtsverbandLernpoolPracticeController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add constructor injection**

Same pattern as LehrgangController but with `LernpoolProgressResolver`.

- [ ] **Step 2: Refactor show() → practice() + practiceShow()**

Split the current `show()` into:
- `practice()` — starts session (selects all questions, shuffles, calls startSession)
- `practiceUnsolved()` — only unsolved questions
- `practiceSection()` — by section
- `practiceShow()` — displays current question using unified view

```php
public function practice(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
{
    // enrollment check...
    $ids = OrtsverbandLernpoolQuestion::where('lernpool_id', $lernpool->id)->pluck('id')->toArray();
    shuffle($ids);

    $this->practiceService->startSession('lernpool', $lernpool->id, $ids, 'all', 'requeue');

    return redirect()->route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]);
}

public function practiceShow(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
{
    if (!$this->practiceService->hasQuestionsRemaining('lernpool', $lernpool->id)) {
        return redirect()->route('ortsverband.lernpools.practice.summary', [$ortsverband, $lernpool]);
    }

    $questionId = $this->practiceService->getCurrentQuestionId('lernpool', $lernpool->id);
    $question = (new LernpoolProgressResolver())->getQuestionById($questionId);

    $answerResult = $this->practiceService->getAndClearAnswerResult('lernpool', $lernpool->id);
    $gamificationResult = $this->practiceService->getAndClearGamificationResult('lernpool', $lernpool->id);
    $progress = $this->practiceService->getProgress('lernpool', $lernpool->id);

    return view('practice', [
        'context' => 'lernpool',
        'contextLabel' => $lernpool->name,
        'backUrl' => route('ortsverband.lernpools.show', [$ortsverband, $lernpool]),
        'submitUrl' => route('ortsverband.lernpools.practice.submit', [$ortsverband, $lernpool]),
        'showUrl' => route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]),
        'summaryUrl' => route('ortsverband.lernpools.practice.summary', [$ortsverband, $lernpool]),
        'issueUrl' => null,
        'question' => $question,
        'answerResult' => $answerResult,
        'gamificationResult' => $gamificationResult,
        'progress' => $progress,
        'mode' => session("practice_lernpool_{$lernpool->id}_mode", 'all'),
        'sectionName' => null,
        'difficultyInfo' => null,
        'isSpacedRepetition' => false,
        'bookmarked' => false,
    ]);
}
```

- [ ] **Step 3: Refactor answer() → practiceSubmit()**

Same pattern as LehrgangController's `practiceSubmit()` but simpler (no completion check needed).

- [ ] **Step 4: Create practiceSummary() method**

```php
public function practiceSummary(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
{
    $summary = $this->practiceService->endSession('lernpool', $lernpool->id);

    return view('practice-summary', [
        'context' => 'lernpool',
        'backUrl' => route('ortsverband.lernpools.show', [$ortsverband, $lernpool]),
        ...$summary,
        'streak' => auth()->user()->streak_days ?? 0,
        'completed' => false,
    ]);
}
```

- [ ] **Step 5: Add new routes in web.php**

Replace existing Lernpool practice routes (approx line 379-382):
```php
Route::get('/{ortsverband}/lernpools/{lernpool}/practice', [OrtsverbandLernpoolPracticeController::class, 'practice'])->name('ortsverband.lernpools.practice');
Route::get('/{ortsverband}/lernpools/{lernpool}/practice/unsolved', [OrtsverbandLernpoolPracticeController::class, 'practiceUnsolved'])->name('ortsverband.lernpools.practice.unsolved');
Route::get('/{ortsverband}/lernpools/{lernpool}/practice/section/{nr}', [OrtsverbandLernpoolPracticeController::class, 'practiceSection'])->name('ortsverband.lernpools.practice.section');
Route::get('/{ortsverband}/lernpools/{lernpool}/practice/show', [OrtsverbandLernpoolPracticeController::class, 'practiceShow'])->name('ortsverband.lernpools.practice.show');
Route::post('/{ortsverband}/lernpools/{lernpool}/practice/submit', [OrtsverbandLernpoolPracticeController::class, 'practiceSubmit'])->name('ortsverband.lernpools.practice.submit');
Route::get('/{ortsverband}/lernpools/{lernpool}/practice/summary', [OrtsverbandLernpoolPracticeController::class, 'practiceSummary'])->name('ortsverband.lernpools.practice.summary');
```

- [ ] **Step 6: Manual test**

Test Lernpool practice: join a Lernpool → Start "Alle üben" → Answer questions → Verify feedback → Complete → See summary (NEW! Lernpool had no summary before).

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/OrtsverbandLernpoolPracticeController.php routes/web.php
git commit -m "✨: Lernpool nutzt Unified Practice View"
```

---

## Chunk 5: Detail Pages, Cleanup, Final Test (Tasks 11–13)

### Task 11: Add Modi-Buttons to Detail Pages

**Files:**
- Modify: `resources/views/lehrgaenge/show.blade.php`
- Modify: Lernpool detail view (find via `grep -r "lernpools.show" resources/views/`)

- [ ] **Step 1: Read Lehrgang show.blade.php**

Find the existing "Üben" button and identify where to add the three mode buttons.

- [ ] **Step 2: Replace single button with three mode buttons**

```blade
<div class="flex flex-wrap gap-3">
    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary">
        Alle üben
    </a>
    <a href="{{ route('lehrgaenge.practice.unsolved', $lehrgang->slug) }}" class="btn-secondary">
        Ungelöste üben
    </a>
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="btn-ghost">
            Nach Abschnitt
        </button>
        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 glass rounded-lg p-2 min-w-[200px]">
            @foreach($lehrgang->lernabschnitte as $abschnitt)
                <a href="{{ route('lehrgaenge.practice.section', [$lehrgang->slug, $abschnitt->nummer]) }}"
                   class="block px-4 py-2 rounded hover:bg-white/10 transition">
                    {{ $abschnitt->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
```

- [ ] **Step 3: Apply same pattern to Lernpool detail view**

Same three buttons but with Lernpool routes. Adjust section dropdown to use Lernpool's sections.

- [ ] **Step 4: Run `npm run build && php artisan view:clear && php artisan cache:clear`**

- [ ] **Step 5: Commit**

```bash
git add resources/views/lehrgaenge/show.blade.php resources/views/ortsverband/lernpools/...
git commit -m "🎨: Modi-Buttons auf Detailseiten"
```

---

### Task 12: Delete Old Views + Cleanup

**Files:**
- Delete: `resources/views/lehrgaenge/practice.blade.php`
- Delete: `resources/views/lehrgaenge/complete.blade.php` (if exists)
- Delete: `resources/views/ortsverband/lernpools/practice.blade.php`
- Modify: Remove any remaining references to deleted views

- [ ] **Step 1: Search for references to deleted views**

Search codebase for:
- `lehrgaenge.practice` (as view name, not route)
- `ortsverband.lernpools.practice` (as view name)
- `lehrgaenge.complete`
- `return view('lehrgaenge/practice'`
- `return view('ortsverband/lernpools/practice'`

Ensure NO controller still references these views.

- [ ] **Step 2: Delete the old view files**

```bash
rm resources/views/lehrgaenge/practice.blade.php
rm resources/views/ortsverband/lernpools/practice.blade.php
```

Also check if `lehrgaenge/complete.blade.php` exists:
```bash
ls resources/views/lehrgaenge/complete.blade.php
```
If it exists, delete it.

- [ ] **Step 3: Remove old route definitions**

Clean up any old routes in `routes/web.php` that are now replaced:
- Old `POST /lehrgaenge/{slug}/submit`
- Old `GET /lehrgaenge/{slug}/practice-section/{sectionNr}`
- Old `POST /{ortsverband}/lernpools/{lernpool}/answer`

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "🔥: Alte Practice Views + Routes entfernt"
```

---

### Task 13: Final Build + Manual End-to-End Test

- [ ] **Step 1: Full build**

```bash
npm run build && php artisan view:clear && php artisan cache:clear && php artisan route:clear
```

- [ ] **Step 2: Verify no broken route references**

```bash
php artisan route:list | grep practice
```

Check all practice-related routes are defined and point to correct controllers.

- [ ] **Step 3: Manual end-to-end test checklist**

Test ALL three contexts:

**Global Practice:**
- [ ] `/practice-menu` loads
- [ ] "Alle Fragen" starts session
- [ ] Question displays with difficulty badge
- [ ] Answer → feedback displays correctly
- [ ] Progress bar updates
- [ ] Skip button works
- [ ] Bookmark button works
- [ ] Summary page shows after completion (no scroll)
- [ ] Spaced Repetition mode works

**Lehrgang Practice:**
- [ ] Lehrgang detail page shows 3 mode buttons
- [ ] "Alle üben" starts session
- [ ] "Ungelöste üben" filters correctly
- [ ] "Nach Abschnitt" dropdown works
- [ ] Same UI as global practice
- [ ] Wrong answers re-queue to end
- [ ] Summary page shows after completion
- [ ] Completion message shows when all mastered

**Lernpool Practice:**
- [ ] Lernpool detail page shows 3 mode buttons
- [ ] "Alle üben" starts session
- [ ] Same UI as global practice
- [ ] Wrong answers re-queue to end
- [ ] Session ENDS (no more infinite loop)
- [ ] Summary page shows (NEW feature)
- [ ] No issue-report button visible

- [ ] **Step 4: Final commit if any fixes needed**

```bash
git add -A
git commit -m "🐛: Unified Practice Bugfixes"
```
