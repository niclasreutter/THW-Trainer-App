<?php

use App\Models\ExtraQuestion;
use App\Models\Question;
use App\Models\User;
use App\Models\UserExtraQuestionProgress;
use App\Models\UserQuestionProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Worktree-Bootstrap: Pest.php im Haupt-Repo kennt diesen Ordner nicht.
uses(Tests\TestCase::class, RefreshDatabase::class);

/**
 * Erstellt N Official-Fragen (optional spezifischer Lernabschnitt).
 */
function seedOfficialQuestions(int $count, string $lernabschnitt = '5'): array
{
    $ids = [];
    for ($i = 1; $i <= $count; $i++) {
        $q = Question::create([
            'lernabschnitt' => $lernabschnitt,
            'nummer' => $i,
            'frage' => "Offizielle Frage $i",
            'antwort_a' => 'A', 'antwort_b' => 'B', 'antwort_c' => 'C',
            'loesung' => 'A',
        ]);
        $ids[] = $q->id;
    }
    return $ids;
}

test('extras_enabled=false -> Queue enthält NUR type=official', function () {
    $user = User::factory()->create(['extras_enabled' => false]);
    seedOfficialQuestions(5);
    ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);

    $this->actingAs($user)->get(route('practice.all'));

    $queue = session('practice_queue');
    expect($queue)->toBeArray();
    expect($queue)->not->toBeEmpty();
    foreach ($queue as $item) {
        expect($item['type'])->toBe('official');
    }
});

test('extras_enabled=true mit Official+Extras -> Queue enthält mind. 1 type=extra', function () {
    $user = User::factory()->create(['extras_enabled' => true]);
    seedOfficialQuestions(8);
    ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);
    ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);

    $this->actingAs($user)->get(route('practice.all'));

    $queue = session('practice_queue');
    expect($queue)->toBeArray();
    $extraCount = collect($queue)->where('type', 'extra')->count();
    expect($extraCount)->toBeGreaterThanOrEqual(1);
});

test('extras_enabled=true, keine Extras in DB -> Queue ist official-only (kein Fehler)', function () {
    $user = User::factory()->create(['extras_enabled' => true]);
    seedOfficialQuestions(4);
    // KEINE Extra-Fragen

    $response = $this->actingAs($user)->get(route('practice.all'));

    // Kein Fehler-Redirect (Standard-Flow rendert practice-View)
    expect($response->status())->toBe(200);

    $queue = session('practice_queue');
    expect($queue)->toBeArray();
    foreach ($queue as $item) {
        expect($item['type'])->toBe('official');
    }
});

test('extras_only Mode -> Queue enthält nur type=extra', function () {
    $user = User::factory()->create(['extras_enabled' => true]);
    seedOfficialQuestions(3); // existieren, dürfen aber nicht in die Queue kommen
    ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);
    ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);

    $this->actingAs($user)->get(route('practice.extras-only'));

    $queue = session('practice_queue');
    expect($queue)->toBeArray();
    expect($queue)->not->toBeEmpty();
    foreach ($queue as $item) {
        expect($item['type'])->toBe('extra');
    }
});

test('extras_only ohne extras_enabled -> Redirect zum Profil', function () {
    $user = User::factory()->create(['extras_enabled' => false]);

    $response = $this->actingAs($user)->get(route('practice.extras-only'));
    $response->assertRedirect(route('profile'));
});

// --- Prüfungs-Freischaltung: Mastery zählt nur Official (UserQuestionProgress) ---

test('Prüfungs-Freischaltung: 40 gemeisterte Extras + 0 Official -> KEIN Start', function () {
    $user = User::factory()->create(['extras_enabled' => true]);
    // 40 offizielle Fragen existieren
    seedOfficialQuestions(40);

    // 40 extras gemeistert (zählt NICHT für Prüfung)
    for ($i = 1; $i <= 40; $i++) {
        $extra = ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);
        UserExtraQuestionProgress::create([
            'user_id' => $user->id,
            'extra_question_id' => $extra->id,
            'consecutive_correct' => UserExtraQuestionProgress::MASTERY_THRESHOLD,
            'last_answered_at' => now(),
        ]);
    }

    // 0 official gemeistert
    expect(UserQuestionProgress::countMastered($user->id))->toBe(0);

    $response = $this->actingAs($user)->get(route('exam.index'));
    $response->assertRedirect(route('dashboard'));
    // Fehlermeldung: "Du musst zuerst alle Fragen meistern"
    $response->assertSessionHas('error');
});

test('Prüfungs-Freischaltung: 40 gemeisterte Official (egal ob Extras) -> Start erlaubt', function () {
    $user = User::factory()->create(['extras_enabled' => true]);
    $qIds = seedOfficialQuestions(40);

    // Alle 40 Official gemeistert
    foreach ($qIds as $qid) {
        UserQuestionProgress::create([
            'user_id' => $user->id,
            'question_id' => $qid,
            'consecutive_correct' => UserQuestionProgress::MASTERY_THRESHOLD,
            'last_answered_at' => now(),
        ]);
    }

    expect(UserQuestionProgress::countMastered($user->id))->toBe(40);

    $response = $this->actingAs($user)->get(route('exam.index'));

    // Start erlaubt → kein Redirect zum Dashboard mit Error.
    // Bei erfolgreicher Freischaltung wird die Exam-View gerendert (200).
    expect($response->status())->toBe(200);
});
