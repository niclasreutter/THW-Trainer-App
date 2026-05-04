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
        $wrongTexts = [];

        foreach (['A', 'B', 'C'] as $letter) {
            $field = 'antwort_' . strtolower($letter);
            $text = (string) $question->{$field};
            if ($text === '') {
                continue;
            }
            if (in_array($letter, $solutions, true)) {
                $correctTexts[] = $letter . ': ' . $text;
            } else {
                $wrongTexts[] = $letter . ': ' . $text;
            }
        }

        $userPrompt = "Frage: {$question->frage}\n\n"
            . "Richtige Antworten:\n" . (count($correctTexts) ? implode("\n", $correctTexts) : '(keine markiert)') . "\n\n"
            . "Falsche Antworten:\n" . (count($wrongTexts) ? implode("\n", $wrongTexts) : '(keine)') . "\n\n"
            . "Lernabschnitt: {$question->lernabschnitt}\n"
            . "Schwierigkeit: {$question->difficulty}\n\n"
            . "Schreibe eine knappe, sachliche Erklärung (3-5 Sätze) auf Deutsch, warum die markierten Antworten richtig sind und die anderen nicht. "
            . "Bezug zur THW-Dienstvorschrift, dem THW-Helferrechtsgesetz oder der Praxis im Grundausbildungs-Theorieteil ist erwünscht. "
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
                        'content' => 'Du bist ein Ausbilder im THW (Technisches Hilfswerk). Du verfasst präzise, lehrbuchhafte Erklärungen für Prüfungsfragen der Grundausbildung auf Deutsch.',
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
