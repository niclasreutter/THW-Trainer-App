<?php

use App\Models\ExtraMatchCategory;
use App\Models\ExtraMatchItem;
use App\Models\ExtraQuestion;
use App\Models\ExtraQuestionOption;
use App\Models\User;
use App\Models\UserExtraQuestionProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Worktree-Bootstrap: Pest.php im Haupt-Repo kennt diesen Ordner nicht
// (vendor ist symlinked → rootPath zeigt aufs Haupt-Repo). Daher binden wir
// TestCase + RefreshDatabase hier explizit.
uses(Tests\TestCase::class, RefreshDatabase::class);

/**
 * Helper: Baut eine vollständige Matching-Frage mit 3 Kategorien + 3 Items.
 */
function makeMatchingQuestion(): ExtraQuestion
{
    $q = ExtraQuestion::create([
        'typ' => 'matching',
        'lernabschnitt' => '5',
        'frage' => 'Ordne die Werkzeuge den Einsatzzwecken zu.',
    ]);

    $catA = ExtraMatchCategory::create(['extra_question_id' => $q->id, 'name' => 'Kat A', 'sort_order' => 1]);
    $catB = ExtraMatchCategory::create(['extra_question_id' => $q->id, 'name' => 'Kat B', 'sort_order' => 2]);
    $catC = ExtraMatchCategory::create(['extra_question_id' => $q->id, 'name' => 'Kat C', 'sort_order' => 3]);

    ExtraMatchItem::create(['extra_question_id' => $q->id, 'text' => 'Item 1', 'correct_category_id' => $catA->id, 'sort_order' => 1]);
    ExtraMatchItem::create(['extra_question_id' => $q->id, 'text' => 'Item 2', 'correct_category_id' => $catB->id, 'sort_order' => 2]);
    ExtraMatchItem::create(['extra_question_id' => $q->id, 'text' => 'Item 3', 'correct_category_id' => $catC->id, 'sort_order' => 3]);

    return $q->fresh(['matchItems', 'matchCategories']);
}

/**
 * Helper: image_name Frage mit 3 Optionen (1 korrekt).
 */
function makeImageNameQuestion(): ExtraQuestion
{
    $q = ExtraQuestion::create([
        'typ' => 'image_name',
        'lernabschnitt' => '4',
        'frage' => 'Wie heißt dieses Werkzeug?',
        'image_path' => 'dummy.jpg',
    ]);

    ExtraQuestionOption::create(['extra_question_id' => $q->id, 'text' => 'Hammer', 'is_correct' => true,  'sort_order' => 1]);
    ExtraQuestionOption::create(['extra_question_id' => $q->id, 'text' => 'Säge',   'is_correct' => false, 'sort_order' => 2]);
    ExtraQuestionOption::create(['extra_question_id' => $q->id, 'text' => 'Zange',  'is_correct' => false, 'sort_order' => 3]);

    return $q->fresh('options');
}

test('matching: alle korrekt -> isCorrect=true und Progress-Row wird angelegt', function () {
    $user = User::factory()->create();
    $q = makeMatchingQuestion();

    // Alle Items korrekt zuordnen
    $assignments = [];
    foreach ($q->matchItems as $item) {
        $assignments[$item->id] = $item->correct_category_id;
    }

    $response = $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'assignments' => json_encode($assignments),
        'answer_time_ms' => 2500,
    ]);

    $response->assertRedirect(route('practice.index'));

    $progress = UserExtraQuestionProgress::where('user_id', $user->id)
        ->where('extra_question_id', $q->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->consecutive_correct)->toBe(1);

    $answerResult = session('practice_answer_result');
    expect($answerResult)->toBeArray();
    expect($answerResult['is_correct'])->toBeTrue();
    expect($answerResult['question_kind'])->toBe('extra');
    expect($answerResult['question_typ'])->toBe('matching');
});

test('matching: ein Item falsch -> isCorrect=false', function () {
    $user = User::factory()->create();
    $q = makeMatchingQuestion();

    $items = $q->matchItems;
    $assignments = [];
    foreach ($items as $i => $item) {
        // erstes Item absichtlich falsch zuordnen
        if ($i === 0) {
            $wrongCatId = $q->matchCategories->firstWhere('id', '!=', $item->correct_category_id)->id;
            $assignments[$item->id] = $wrongCatId;
        } else {
            $assignments[$item->id] = $item->correct_category_id;
        }
    }

    $response = $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'assignments' => json_encode($assignments),
    ]);

    $response->assertRedirect(route('practice.index'));

    $progress = UserExtraQuestionProgress::where('user_id', $user->id)
        ->where('extra_question_id', $q->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->consecutive_correct)->toBe(0);

    expect(session('practice_answer_result')['is_correct'])->toBeFalse();
});

test('image_name: korrekte Option -> isCorrect=true', function () {
    $user = User::factory()->create();
    $q = makeImageNameQuestion();

    $correctId = $q->options->firstWhere('is_correct', true)->id;

    $response = $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'answer' => [$correctId],
    ]);

    $response->assertRedirect(route('practice.index'));

    expect(session('practice_answer_result')['is_correct'])->toBeTrue();

    $progress = UserExtraQuestionProgress::where('user_id', $user->id)
        ->where('extra_question_id', $q->id)
        ->first();
    expect($progress->consecutive_correct)->toBe(1);
});

test('image_name: falsche Option -> isCorrect=false', function () {
    $user = User::factory()->create();
    $q = makeImageNameQuestion();

    $wrongId = $q->options->firstWhere('is_correct', false)->id;

    $response = $this->actingAs($user)->post(route('practice.submit'), [
        'question_id' => $q->id,
        'question_kind' => 'extra',
        'answer' => [$wrongId],
    ]);

    $response->assertRedirect(route('practice.index'));

    expect(session('practice_answer_result')['is_correct'])->toBeFalse();

    $progress = UserExtraQuestionProgress::where('user_id', $user->id)
        ->where('extra_question_id', $q->id)
        ->first();
    expect($progress->consecutive_correct)->toBe(0);
});

test('invalid input: fehlende assignments bei matching -> 422', function () {
    $user = User::factory()->create();
    $q = makeMatchingQuestion();

    $response = $this->actingAs($user)
        ->from(route('practice.index'))
        ->post(route('practice.submit'), [
            'question_id' => $q->id,
            'question_kind' => 'extra',
            // fehlt: assignments
        ]);

    $response->assertSessionHasErrors('assignments');
});

test('invalid input: nicht-existierende question_id -> 422', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('practice.index'))
        ->post(route('practice.submit'), [
            'question_id' => 9999999,
            'question_kind' => 'extra',
            'answer' => [1],
        ]);

    $response->assertSessionHasErrors('question_id');
});
