<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

function adminInLeague(): User
{
    return User::factory()->create(['useroll' => 'admin']);
}

test('user mit leaderboard-consent zeigt name in admin-ranking', function () {
    $admin = adminInLeague();
    $consented = User::factory()->create([
        'name' => 'Max Mustermann',
        'leaderboard_consent' => true,
        'league' => 'bronze',
        'weekly_points' => 100,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.leagues.show', 'bronze'));

    $response->assertOk();
    $response->assertSee('Max Mustermann');
});

test('user ohne leaderboard-consent ist anonymisiert in admin-ranking', function () {
    $admin = adminInLeague();
    $noConsent = User::factory()->create([
        'name' => 'Maria Geheim',
        'leaderboard_consent' => false,
        'league' => 'bronze',
        'weekly_points' => 100,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.leagues.show', 'bronze'));

    $response->assertOk();
    $response->assertDontSee('Maria Geheim');
    $response->assertSee('Anonymer Teilnehmer');
});

test('leaderboardDisplayName respektiert consent', function () {
    $withConsent = User::factory()->make([
        'name' => 'Mit Consent',
        'leaderboard_consent' => true,
    ]);
    $withoutConsent = User::factory()->make([
        'name' => 'Ohne Consent',
        'leaderboard_consent' => false,
    ]);

    expect($withConsent->leaderboardDisplayName())->toBe('Mit Consent')
        ->and($withoutConsent->leaderboardDisplayName())->toBe('Anonymer Teilnehmer');
});
