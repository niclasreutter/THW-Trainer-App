# OV Dashboard Card Redesign — Design Spec

## Goal

Replace the current Ausbilder-only OV card in `/dashboard` with an expanded card visible to **all OV members**, showing role-specific content: Lernpool progress + OV ranking for members, management stats for trainers.

## Current State

- OV card only visible to Ausbilder (trainers)
- Shows: OV name + "Verwalten" button
- Location: `dashboard.blade.php` lines 760-771
- Condition: `@if($isAusbilder && $userOV)`
- `$userOV` and `$isAusbilder` are computed in a `@php` block inside `dashboard.blade.php` (lines 496-501), **not** in the route closure

## Design Decisions

- **Approach A chosen**: Compact single card (no tabs)
- **Mitglied view**: Top 3 enrolled Lernpools with progress bars + Mini-Ranking (Top 3 users inline)
- **Ausbilder view**: Stats pills (Mitglieder, Aktiv 7d, Ø Fortschritt) + Verwalten button
- **Lernpool limit**: Max 3 shown, "Alle anzeigen" link to OV page

## Architecture

### Data Flow (in `dashboard.blade.php` @php block)

Add the following after the existing `$isAusbilder` computation (line 501), in the same `@php` block:

```php
$ovCardData = null;
if ($userOV) {
    if ($isAusbilder) {
        // Trainer: aggregate stats
        $ovCardData = [
            'type' => 'ausbilder',
            'stats' => $userOV->getAverageStats(),
        ];
    } else {
        // Member: enrolled lernpools + ranking
        $enrolledLernpools = $user->enrolledLernpools()
            ->where('ortsverband_id', $userOV->id)
            ->take(3)
            ->get()
            ->map(fn($lp) => [
                'id' => $lp->id,
                'name' => $lp->name,
                'progress' => $lp->enrollments()
                    ->where('user_id', $user->id)
                    ->first()?->getProgress() ?? 0,
            ]);

        $totalEnrolled = $user->enrolledLernpools()
            ->where('ortsverband_id', $userOV->id)
            ->count();

        // OV Ranking: only if ranking_visible is enabled
        $ovRanking = null;
        $userRank = null;
        if ($userOV->ranking_visible) {
            $memberProgress = $userOV->getMemberProgress();
            $sorted = collect($memberProgress)->sortByDesc('points')->values();
            $ovRanking = $sorted->take(3);
            $userRank = $sorted->search(fn($m) => $m['user_id'] === $user->id);
            $userRank = $userRank !== false ? $userRank + 1 : null;
        }

        $ovCardData = [
            'type' => 'member',
            'lernpools' => $enrolledLernpools,
            'totalEnrolled' => $totalEnrolled,
            'ranking' => $ovRanking,
            'userRank' => $userRank,
        ];
    }
}
```

### View Structure

Replace the existing `@if($isAusbilder && $userOV)` block (lines 760-771) with:

```
@if($userOV && $ovCardData)
    <div class="glass-blue" style="...">
        <!-- Header: always shown -->
        OV Name + conditional badge/link

        @if($ovCardData['type'] === 'ausbilder')
            <!-- Stats Pills Row -->
            3 pills: Mitglieder | Aktiv (7d) | Ø Fortschritt
            + Verwalten button

        @else
            <!-- Lernpool List (max 3) -->
            @if($ovCardData['lernpools']->isNotEmpty())
                Each: name + progress % + progress bar
                + "Alle anzeigen" link if totalEnrolled > 3
            @endif

            <!-- Mini Ranking (Top 3 inline) -->
            @if($ovCardData['ranking'] && $ovCardData['ranking']->isNotEmpty())
                Separator line
                "Ranking" label
                Top 3: rank + name + points (current user highlighted)
            @endif
        @endif
    </div>
@endif
```

### Styling

- Card: `glass-blue` (existing class)
- Progress bars: green (>=60%), yellow (30-59%), red (<30%) gradient
- Ranking: gold/silver/bronze rank numbers, current user in blue+bold
- Stats pills: same pattern as practice-summary stats (glass bg, border, centered)
- No new CSS classes needed — all inline styles following existing dashboard patterns

### Edge Cases

- **User not in any OV**: Card not shown (same as today)
- **User is Ausbilder AND member**: Show Ausbilder view (management takes priority)
- **No enrolled Lernpools**: Show ranking only, no lernpool section
- **No ranking data** (`ranking_visible = false`): Show lernpools only, no ranking section
- **Empty OV** (no other members): Ranking section hidden (empty collection)
- **Null safety**: `first()?->getProgress() ?? 0` prevents NPE on enrollment lookup

### Files Modified

1. `resources/views/dashboard.blade.php` — Add `$ovCardData` computation in existing `@php` block + replace OV card block (lines 760-771)

### No New Files

Everything stays in existing files. No new controllers, components, or views.
