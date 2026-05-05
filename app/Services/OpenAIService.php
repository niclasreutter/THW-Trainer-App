<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenAIService
{
    public function suggestExplanation(Question $question): string
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('OpenAI API-Key ist nicht konfiguriert (OPENAI_API_KEY).');
        }

        $solutions = array_filter(array_map('trim', explode(',', (string) $question->loesung)));
        $correctTexts = [];

        foreach (['A', 'B', 'C'] as $letter) {
            $field = 'antwort_' . strtolower($letter);
            $text = (string) $question->{$field};
            if ($text === '' || !in_array($letter, $solutions, true)) {
                continue;
            }
            $correctTexts[] = $text;
        }

        $userPrompt = "Frage: {$question->frage}\n\n"
            . "Richtige Antwort(en):\n" . (count($correctTexts) ? implode("\n", $correctTexts) : '(keine markiert)') . "\n\n"
            . "Lernabschnitt: {$question->lernabschnitt}\n"
            . "Schwierigkeit: {$question->difficulty}\n\n"
            . "Schreibe eine knappe, sachliche Erklärung (3-5 Sätze) auf Deutsch, warum die richtige Antwort zutrifft. "
            . "Erkläre ausschließlich, warum die richtige Antwort korrekt ist – gehe NICHT auf falsche Antworten ein. "
            . "Erwähne NIEMALS die Buchstaben A, B oder C. Formuliere stattdessen mit dem eigentlichen Antworttext (z. B. \"Der Helfer ist verpflichtet, ... weil ...\"). "
            . "Ein Bezug zur THW-Dienstvorschrift oder zum THW-Gesetz (THW-G) ist erwünscht – aber NUR, wenn der genannte Inhalt dort tatsächlich verankert ist. "
            . "Wenn du dir nicht sicher bist, ob ein Inhalt in der THW-Dienstvorschrift oder im THW-Gesetz steht, lasse den Bezug weg und bleibe bei einer sachlichen Erklärung aus der Praxis der Grundausbildung. "
            . "Erfinde keine Paragraphen, Vorschriften-Nummern oder Quellen. "
            . "Keine Aufzählung, keine Floskeln, kein Markdown.";

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->acceptJson()
            ->asJson()
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.4,
                'max_tokens' => 400,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Du bist ein Ausbilder im THW (Technisches Hilfswerk). Du verfasst präzise, lehrbuchhafte Erklärungen für Prüfungsfragen der Grundausbildung auf Deutsch. Du erklärst ausschließlich, warum die richtige Antwort korrekt ist, und gehst nicht auf falsche Antworten ein. Du nennst keine Antwort-Buchstaben (A/B/C), sondern formulierst mit dem eigentlichen Antworttext. Quellen wie die THW-Dienstvorschrift oder das THW-Gesetz (THW-G) erwähnst du nur, wenn der Inhalt dort nachweislich verankert ist; im Zweifel lässt du die Quelle weg, statt sie zu erfinden.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::warning('OpenAI request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('OpenAI-Anfrage fehlgeschlagen (HTTP ' . $response->status() . ').');
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

        if ($content === '') {
            throw new RuntimeException('OpenAI hat eine leere Antwort zurückgegeben.');
        }

        return $content;
    }
}
