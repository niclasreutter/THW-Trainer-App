<?php

use App\Models\Lootbox;
use App\Models\User;
use App\Services\LeagueService;

test('processWeeklyLeagues vergibt Wochenbelohnungen nur an User mit Zustimmung', function () {
    // Höchste Punktzahl, aber KEINE Zustimmung -> darf nicht gewinnen
    $ohneZustimmung = User::factory()->create([
        'name' => 'Alfred Müller',
        'leaderboard_consent' => false,
        'league' => 'diamant',
        'weekly_points' => 9000,
    ]);

    $ersterMitZustimmung = User::factory()->create([
        'name' => 'Hans Peter',
        'leaderboard_consent' => true,
        'league' => 'diamant',
        'weekly_points' => 7000,
    ]);

    $zweiterMitZustimmung = User::factory()->create([
        'name' => 'Max Mustermann',
        'leaderboard_consent' => true,
        'league' => 'diamant',
        'weekly_points' => 6000,
    ]);

    (new LeagueService())->processWeeklyLeagues();

    // Unsichtbarer Teilnehmer erhält trotz höchster Punktzahl KEINE Belohnung
    expect(Lootbox::where('user_id', $ohneZustimmung->id)->count())->toBe(0);

    // Belohnungen richten sich ausschließlich nach dem Ranking der zustimmenden User
    expect(Lootbox::where('user_id', $ersterMitZustimmung->id)->where('type', 'gold')->exists())->toBeTrue();
    expect(Lootbox::where('user_id', $zweiterMitZustimmung->id)->where('type', 'silber')->exists())->toBeTrue();
});

test('processWeeklyLeagues befördert keine User ohne Zustimmung', function () {
    // Höchste Punktzahl, aber keine Zustimmung -> darf nicht aufsteigen
    $ohneZustimmung = User::factory()->create([
        'name' => 'Alfred Müller',
        'leaderboard_consent' => false,
        'league' => 'bronze',
        'weekly_points' => 9000,
    ]);

    // Genug zustimmende User, damit ein Aufstieg grundsätzlich möglich wäre
    User::factory()->create(['leaderboard_consent' => true, 'league' => 'bronze', 'weekly_points' => 4000]);
    User::factory()->create(['leaderboard_consent' => true, 'league' => 'bronze', 'weekly_points' => 3000]);
    User::factory()->create(['leaderboard_consent' => true, 'league' => 'bronze', 'weekly_points' => 2000]);

    (new LeagueService())->processWeeklyLeagues();

    // Trotz höchster Punktzahl bleibt der User ohne Zustimmung in seiner Liga
    expect($ohneZustimmung->fresh()->league)->toBe('bronze');

    // Wochenpunkte werden dennoch für alle zurückgesetzt (kein "Einfrieren" mit altem Score)
    expect($ohneZustimmung->fresh()->weekly_points)->toBe(0);
});

test('processWeeklyLeagues befördert zustimmende User weiterhin normal', function () {
    // Sicherstellen, dass der Consent-Filter den normalen Aufstieg nicht kaputt macht
    $aufsteiger = User::factory()->create([
        'leaderboard_consent' => true,
        'league' => 'bronze',
        'weekly_points' => 5000,
    ]);
    User::factory()->create(['leaderboard_consent' => true, 'league' => 'bronze', 'weekly_points' => 400]);
    User::factory()->create(['leaderboard_consent' => true, 'league' => 'bronze', 'weekly_points' => 350]);

    (new LeagueService())->processWeeklyLeagues();

    expect($aufsteiger->fresh()->league)->toBe('silber');
});
