<?php

use App\Models\ExtraQuestion;
use App\Models\User;
use App\Models\UserExtraQuestionProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

/**
 * Diese Datei ergänzt ExtraQuestionSubmitTest um Edge-Cases für Matching.
 * Basis-Fälle (alle korrekt / ein falsch / fehlende assignments) stehen dort.
 */

test('matching: partielle Zuordnung (item nicht zugeordnet) -> isCorrect=false', function () {
    $user = User::factory()->create();
    $q = ExtraQuestion::factory()->matching(3, 3)->create();
    $items = $q->matchItems;

    // Nur das erste Item zuordnen, Rest weglassen
    $assignments = [
        $items[0]->id => $items[0]->correct_category_id,
        // items[1] und items[2] fehlen
    ];

    $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'assignments' => json_encode($assignments),
    ])->assertRedirect(route('practice.index'));

    expect(session('practice_answer_result')['is_correct'])->toBeFalse();

    $progress = UserExtraQuestionProgress::where('user_id', $user->id)
        ->where('extra_question_id', $q->id)->first();
    expect($progress->consecutive_correct)->toBe(0);
});

test('matching: leeres assignments-JSON -> isCorrect=false', function () {
    $user = User::factory()->create();
    $q = ExtraQuestion::factory()->matching(3, 3)->create();

    // Leeres Objekt ist gültiges JSON (kein 422), aber Validierung schlägt für alle Items fehl
    $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'assignments' => json_encode(new stdClass()),
    ])->assertRedirect(route('practice.index'));

    expect(session('practice_answer_result')['is_correct'])->toBeFalse();
});

test('matching: zusätzliche unbekannte item_ids werden ignoriert, korrekte Items zählen', function () {
    $user = User::factory()->create();
    $q = ExtraQuestion::factory()->matching(3, 3)->create();
    $items = $q->matchItems;

    $assignments = [];
    foreach ($items as $item) {
        $assignments[$item->id] = $item->correct_category_id;
    }
    // Zusätzliche, nicht-existierende item_id mit Unsinn-Wert
    $assignments[999999] = 888888;

    $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'assignments' => json_encode($assignments),
    ])->assertRedirect(route('practice.index'));

    // Alle real existierenden Items korrekt → isCorrect bleibt true
    expect(session('practice_answer_result')['is_correct'])->toBeTrue();
});
