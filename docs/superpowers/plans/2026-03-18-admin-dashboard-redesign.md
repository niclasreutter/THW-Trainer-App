# Admin Dashboard Redesign - Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the admin dashboard to match the app's glassmorphism design system with THW-blue colors, SR stats, and avatar leaderboard.

**Architecture:** Two-file change — controller adds SR queries and avatar data, view gets complete rewrite using existing design-system CSS classes. No new routes, migrations, or components needed.

**Tech Stack:** Laravel 12, Blade, Tailwind CSS, Chart.js, existing design-system classes from `app.css`

**Spec:** `docs/superpowers/specs/2026-03-18-admin-dashboard-redesign.md`

**Mockup:** `.superpowers/brainstorm/41477-1773865471/admin-v3.html`

---

## File Map

| File | Action | Responsibility |
|------|--------|---------------|
| `app/Http/Controllers/Admin/DashboardController.php` | Modify | Add SR stats, add avatar_path to leaderboard select |
| `resources/views/admin/dashboard.blade.php` | Rewrite | Complete redesign with design-system classes |

---

## Chunk 1: Controller Changes

### Task 1: Add SR stats to DashboardController

**Files:**
- Modify: `app/Http/Controllers/Admin/DashboardController.php`

- [ ] **Step 1: Add UserQuestionProgress import**

Add to the imports at the top of the file:
```php
use App\Models\UserQuestionProgress;
```

- [ ] **Step 2: Add `getSpacedRepetitionStats()` private method**

Add after the `getActivityFeed()` method (around line 415):
```php
private function getSpacedRepetitionStats()
{
    $totalInSr = UserQuestionProgress::where('review_interval', '>', 0)->count();

    $mastered = UserQuestionProgress::whereNull('next_review_at')
        ->where('consecutive_correct', '>=', 3)
        ->count();

    return [
        'active_users' => UserQuestionProgress::whereNotNull('next_review_at')
            ->distinct('user_id')
            ->count('user_id'),
        'total_in_sr' => $totalInSr,
        'mastered' => $mastered,
        'mastery_rate' => $totalInSr > 0 ? round(($mastered / ($totalInSr + $mastered)) * 100, 1) : 0,
        'due_today' => UserQuestionProgress::whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now())
            ->count(),
        'due_tomorrow' => UserQuestionProgress::whereNotNull('next_review_at')
            ->whereBetween('next_review_at', [now()->endOfDay(), now()->addDay()->endOfDay()])
            ->count(),
        'due_this_week' => UserQuestionProgress::whereNotNull('next_review_at')
            ->whereBetween('next_review_at', [now(), now()->endOfWeek()])
            ->count(),
        'overdue' => UserQuestionProgress::whereNotNull('next_review_at')
            ->where('next_review_at', '<', now()->startOfDay())
            ->count(),
        'avg_interval' => round(UserQuestionProgress::where('review_interval', '>', 0)
            ->avg('review_interval') ?? 0, 1),
        'avg_easiness' => round(UserQuestionProgress::where('review_interval', '>', 0)
            ->avg('easiness_factor') ?? 0, 2),
        'interval_distribution' => [
            '1_3' => UserQuestionProgress::whereBetween('review_interval', [1, 3])->count(),
            '4_7' => UserQuestionProgress::whereBetween('review_interval', [4, 7])->count(),
            '8_14' => UserQuestionProgress::whereBetween('review_interval', [8, 14])->count(),
            '15_plus' => UserQuestionProgress::where('review_interval', '>', 14)->count(),
        ],
    ];
}
```

- [ ] **Step 3: Call SR stats in `index()` and pass to view**

In the `index()` method, add before the `return view(...)` call:
```php
$srStats = $this->getSpacedRepetitionStats();
```

Add `'srStats'` to the `compact()` call.

- [ ] **Step 4: Add `avatar_path` to leaderboard select**

In `getLeaderboard()` method, change the `select()` to include `avatar_path`:
```php
$users = User::select('id', 'name', 'email', 'avatar_path', 'solved_questions', 'exam_passed_count', 'points', 'level', 'streak_days')
```

Also add `'avatar_url' => $user->avatar_url` to the return array in the `map()` callback.

- [ ] **Step 5: Commit controller changes**

```bash
git add app/Http/Controllers/Admin/DashboardController.php
git commit -m "✨: SR-Stats + Avatar im Admin Dashboard Controller"
```

---

## Chunk 2: View Redesign

### Task 2: Rewrite admin dashboard view

**Files:**
- Rewrite: `resources/views/admin/dashboard.blade.php`

The complete view is based on the approved mockup (`.superpowers/brainstorm/41477-1773865471/admin-v3.html`).

- [ ] **Step 1: Write the new view file**

Complete rewrite of `resources/views/admin/dashboard.blade.php` with these sections in order:

1. **`@push('styles')`** — Bunny Fonts import + all custom CSS:
   - `.sys-pill` for system status pills
   - `.kpi-value`, `.kpi-label` for KPI bar
   - Activity feed styles (`.activity-item`, `.activity-icon`, etc.)
   - Leaderboard styles (`.lb-item`, `.lb-rank`, `.lb-avatar`, etc.)
   - SR section styles (purple gami-pills, due-today highlight, interval bar)
   - Chart container styles
   - Badge styles (`.badge-sm`, `.badge-error`, `.badge-warning`)
   - Light mode overrides (`html.light-mode ...`)
   - Responsive breakpoints (900px, 600px)
   - Stagger animations (`@keyframes dash-rise`)
   - `@media (prefers-reduced-motion: reduce)` support

2. **`@section('content')`** — HTML structure using `dash-container`:
   - **Header row:** Prefix "Administration" + gradient title "System Dashboard" + subtitle | System pills (DB, Cache, Online, Issues, Ungelesen)
   - **KPI bar:** `.glass-thw` with horizontal layout, 5 values with dividers
   - **Activity + Stats grid:** `.glass-blue.bento-2of3` (2-col feed) + `.glass-tl.bento-third` (stat rows)
   - **SR section header:** Section header with line
   - **SR grid:** `.glass-purple.bento-half` (overview pills + stats) + `.glass-slash.bento-half` (due today + distribution bar)
   - **Leaderboard:** `.glass-br` full-width, 2-col grid, avatars via `$user['avatar_url']`
   - **Charts section header + grid:** 3 Chart.js canvases in bento grid

3. **`@push('scripts')`** — Chart.js initialization (adapted from current, with THW-blue colors)

Key design-system patterns to follow:
- Use `dash-container` not `dashboard-container`
- Card headers: `.card-header` with `<i class="bi bi-..."></i>` + text
- Stat rows: `.stat-row` > `.stat-row-label` + `.stat-row-value`
- Section headers: `.section-header` > `.section-title` + `.section-line`
- All icons use Bootstrap Icons (`bi bi-*`)
- No emojis in UI
- Card header icons in blue (`color: #5b9aff`), not gold
- `space-y-4` wrapper for stagger animations

Avatar URLs in leaderboard:
```blade
<img class="lb-avatar" src="{{ $user['avatar_url'] }}" alt="" loading="lazy">
```

SR interval distribution bar uses inline flex segments with percentage widths calculated from `$srStats['interval_distribution']`.

Chart.js colors updated:
- User Activity line: `#0055cc` (was `#fbbf24`)
- Registrations line: `#22c55e` (keep)
- Questions correct: `#22c55e` (keep)
- Questions wrong: `#ef4444` (keep)
- Questions total: `#5b9aff` (was `#6b7280`)
- Trend line: `#f59e0b` (keep — gold accent OK for trend)
- User growth: `#00337F` (keep)
- Unverified: `#f59e0b` (keep)

- [ ] **Step 2: Verify view renders without errors**

```bash
php artisan view:clear && php artisan cache:clear
```

Open `/admin` in browser, check:
- All sections render
- No Blade errors
- System status pills show correct data
- KPI values display
- Activity feed populates
- SR stats show numbers
- Leaderboard shows avatars
- Charts render

- [ ] **Step 3: Test light mode**

Toggle to light mode in browser, verify:
- All glass cards switch to white backgrounds
- Text colors adjust
- Icons use `#00337F` instead of `#5b9aff`
- Charts are readable

- [ ] **Step 4: Test responsive**

Resize browser to mobile width (< 600px), verify:
- Single column layout
- KPI dividers hidden
- Activity feed single column
- Leaderboard single column
- Charts scale properly

- [ ] **Step 5: Build assets and commit**

```bash
npm run build
git add resources/views/admin/dashboard.blade.php public/build/
git commit -m "🎨: Admin Dashboard Redesign (Command Center)"
```

---

## Chunk 3: Final Verification

### Task 3: End-to-end check

- [ ] **Step 1: Clear all caches and verify**

```bash
npm run build && php artisan view:clear && php artisan cache:clear
```

- [ ] **Step 2: Cross-check all data points**

Open `/admin` and verify each data point matches a real database value:
- Total users matches `User::count()`
- Verification rate is correct
- SR stats show plausible numbers
- Leaderboard scores are ordered correctly
- Chart data renders for 30 days
- Activity feed shows recent items

- [ ] **Step 3: Final commit if any fixes needed**

```bash
git add -A
git commit -m "🐛: Admin Dashboard Fixes"
```
