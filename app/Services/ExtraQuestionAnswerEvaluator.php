<?php

namespace App\Services;

use App\Models\ExtraQuestion;

/**
 * Bewertet die Antworten auf Zusatz-Fragen (Strict-Match je Typ).
 *
 * Eine eigene Klasse, damit Practice-Submit und Try-Out-Vorschau dieselbe
 * Korrektheits-Regel teilen.
 */
class ExtraQuestionAnswerEvaluator
{
    /**
     * matching: jedes Item muss seiner korrekten Kategorie zugewiesen sein.
     *
     * @param array<int|string, int|string|null> $submitted [itemId => categoryId]
     */
    public static function evaluateMatching(ExtraQuestion $extra, array $submitted): bool
    {
        $items = $extra->matchItems;
        if ($items->isEmpty()) {
            return false;
        }

        foreach ($items as $item) {
            $submittedCat = $submitted[$item->id] ?? $submitted[(string) $item->id] ?? null;
            if ($submittedCat === null) {
                return false;
            }
            if ((int) $submittedCat !== (int) $item->correct_category_id) {
                return false;
            }
        }

        return true;
    }

    /**
     * pair_matching: jedes Paar muss 1:1 korrekt zugeordnet sein
     * (linke Pair-ID muss auf die gleiche rechte Pair-ID zeigen).
     *
     * @param array<int|string, int|string|null> $submitted [leftPairId => rightPairId]
     */
    public static function evaluatePairMatching(ExtraQuestion $extra, array $submitted): bool
    {
        $pairs = $extra->pairItems;
        if ($pairs->isEmpty()) {
            return false;
        }

        foreach ($pairs as $pair) {
            $submittedRight = $submitted[$pair->id] ?? $submitted[(string) $pair->id] ?? null;
            if ($submittedRight === null) {
                return false;
            }
            if ((int) $submittedRight !== (int) $pair->id) {
                return false;
            }
        }

        return true;
    }

    /**
     * image_name + image_select: Submitted-Set muss exakt dem Korrekt-Set entsprechen.
     *
     * @param array<int> $submittedIds
     */
    public static function evaluateOptionIds(ExtraQuestion $extra, array $submittedIds): bool
    {
        $correctIds = $extra->options
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->toArray();

        if (empty($correctIds)) {
            return false;
        }

        $submitted = collect($submittedIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return $submitted === $correctIds;
    }
}
