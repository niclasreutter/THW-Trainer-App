<?php

namespace App\Helpers;

/**
 * Anzeige-Texte der Lernseiten: ein Begriff („Wiederholung“ statt „Reviews“ / „Spaced Repetition“)
 * und keine Gedankenstriche. Ändert nur die Darstellung, nicht die Texte in Controllern.
 */
class LearnText
{
    public const SR_RULE = 'Richtig beantwortete Fragen kommen nach einem Tag wieder, dann nach drei Tagen. Nach dreimal richtig in Folge ist eine Frage gemeistert und muss nicht mehr wiederholt werden.';

    public static function wd(?string $text): string
    {
        $text = str_replace(
            [
                'Spaced Repetition — halte dein Wissen frisch',
                'Spaced Repetition hält dein Wissen langfristig frisch',
                'Fällige Spaced-Repetition Fragen',
                'durch Spaced Repetition geplant',
                'Spaced Repetition',
                'Reviews',
                ' — ',
                ' – ',
                'In 1 Tagen', // getUpcomingSchedule: Carbon 3 liefert Tage als Float, daher kein „Morgen“
            ],
            [
                'Halte dein Wissen frisch',
                'Richtige Antworten kommen nach 1 Tag, dann nach 3 Tagen wieder',
                'Fällige Wiederholungen',
                'für die Wiederholung geplant',
                'Wiederholung',
                'Wiederholungen',
                ', ',
                ', ',
                'Morgen',
            ],
            (string) $text
        );

        // Einzahl („1 Fragen“ → „1 Frage“) und „werden Morgen fällig“ → „werden morgen fällig“
        $text = preg_replace(['/\b1 Fragen\b/u', '/\b1 Wiederholungen warten\b/u', '/\b1 Wiederholungen\b/u'], ['1 Frage', '1 Wiederholung wartet', '1 Wiederholung'], $text);

        return preg_replace_callback('/(\S) (Morgen|In) /u', fn ($m) => $m[1].' '.lcfirst($m[2]).' ', $text);
    }

    /** Wann kommt eine Frage wieder: „heute“, „morgen“, „in 3 Tagen“ */
    public static function when(int $days): string
    {
        return match (true) {
            $days <= 0 => 'heute',
            $days === 1 => 'morgen',
            default => "in {$days} Tagen",
        };
    }
}
