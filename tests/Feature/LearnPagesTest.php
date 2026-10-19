<?php

use App\Models\Question;
use App\Models\User;
use App\Models\UserQuestionProgress;

// Lernseiten im Design „D“: rendern ohne Fehler, ein Begriff („Wiederholung“), Erklärung wann Fragen wiederkommen.

function learnQuestions(int $count): array
{
    $ids = [];
    for ($i = 1; $i <= $count; $i++) {
        $ids[] = Question::create([
            'lernabschnitt' => (string) (($i - 1) % 10 + 1),
            'nummer' => $i,
            'frage' => "Testfrage $i",
            'antwort_a' => 'Antwort A', 'antwort_b' => 'Antwort B', 'antwort_c' => 'Antwort C',
            'loesung' => 'A',
        ])->id;
    }
    return $ids;
}

test('Dashboard zeigt Navigation, Bühne und den Wiederholungsplan', function () {
    $user = User::factory()->create();
    $ids = learnQuestions(4);
    UserQuestionProgress::create(['user_id' => $user->id, 'question_id' => $ids[0], 'consecutive_correct' => 1, 'next_review_at' => now()->subHour()]);
    UserQuestionProgress::create(['user_id' => $user->id, 'question_id' => $ids[1], 'consecutive_correct' => 1, 'next_review_at' => now()->startOfDay()->addDays(3)]);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('data-tour-step="sidebar"', false)
        ->assertSee('data-tour-step="practice"', false)
        ->assertSee('Heute fällig')
        ->assertSee('In 3 Tagen')
        ->assertSee('Nach dreimal richtig in Folge ist eine Frage gemeistert')
        ->assertDontSee('Spaced Repetition')
        ->assertDontSee('Reviews');
});

test('Übungsmenü rendert Lernmodus und Lernabschnitte', function () {
    $user = User::factory()->create();
    learnQuestions(3);

    $this->actingAs($user)->get(route('practice.menu'))
        ->assertOk()
        ->assertSee('Lernmodus')
        ->assertSee('Lernabschnitte')
        ->assertDontSee('Reviews');
});

test('Üben: Antwort zeigt im Sheet, wann die Frage wiederkommt', function () {
    $user = User::factory()->create(['points' => 0, 'level' => 1]);
    learnQuestions(1);

    $this->actingAs($user)->followingRedirects()->get(route('practice.all'))
        ->assertOk()
        ->assertSee('l-focus-root', false)
        ->assertSee('Antwort absenden');

    $question = Question::first();
    $this->actingAs($user)->followingRedirects()->post(route('practice.submit'), [
        'question_id' => $question->id,
        'question_kind' => 'official',
        'answer_mapping' => json_encode([0 => 'A', 1 => 'B', 2 => 'C']),
        'answer' => [0],
    ])
        ->assertOk()
        ->assertSee('Nächste Frage')
        ->assertSee('Kommt morgen wieder. Noch 2× richtig in Folge, dann gemeistert.');
});

test('Prüfung rendert Band, Fragenleiste und Bedienung', function () {
    $user = User::factory()->create();
    foreach (learnQuestions(12) as $id) {
        UserQuestionProgress::create(['user_id' => $user->id, 'question_id' => $id, 'consecutive_correct' => 3]);
    }

    $this->actingAs($user)->get(route('exam.index'))
        ->assertOk()
        ->assertSee('id="exam-strip"', false)
        ->assertSee('40 Fragen, 30 Minuten, 80 % zum Bestehen')
        ->assertSee('Markieren');
});

test('Gast-Üben nutzt das Startseiten-Layout mit Topleiste', function () {
    learnQuestions(3);

    $this->followingRedirects()->get(route('landing.guest.practice.all'))
        ->assertOk()
        ->assertSee('<header class="d-nav"', false)
        ->assertSee('l-focus-root', false)
        ->assertSee('Übungsmenü')
        ->assertDontSee('landing-navbar', false);
});

test('Gast-Prüfung: Band, Fragenleiste und Auswertung im neuen Design', function () {
    learnQuestions(40);

    $this->get(route('landing.guest.exam.index'))
        ->assertOk()
        ->assertSee('<header class="d-nav"', false)
        ->assertSee('id="exam-strip"', false)
        ->assertSee('Markieren');

    $ids = Question::pluck('id')->all();
    $this->post(route('landing.guest.exam.submit'), [
        'fragen_ids' => $ids,
        'answer_mappings' => array_fill(0, 40, json_encode([0 => 'A', 1 => 'B', 2 => 'C'])),
        'answer' => array_fill(0, 40, ['0']),
    ])
        ->assertOk()
        ->assertSee('Prüfung bestanden.')
        ->assertSee('id="frage-40"', false)
        ->assertSee('Kostenlos registrieren');
});
