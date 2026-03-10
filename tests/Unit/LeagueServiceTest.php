<?php

use App\Services\LeagueService;

test('getLeagues returns all 8 leagues', function () {
    $leagues = LeagueService::getLeagues();

    expect($leagues)->toHaveCount(8);
    expect(array_keys($leagues))->toBe(['bronze', 'silber', 'gold', 'platin', 'smaragd', 'rubin', 'saphir', 'diamant']);
});

test('getLeagueInfo returns correct info', function () {
    $info = LeagueService::getLeagueInfo('gold');

    expect($info['name'])->toBe('Gold');
    expect($info['order'])->toBe(3);
});

test('getLeagueInfo returns bronze for unknown league', function () {
    $info = LeagueService::getLeagueInfo('unknown');

    expect($info['name'])->toBe('Bronze');
});

test('getNextLeague returns correct next league', function () {
    expect(LeagueService::getNextLeague('bronze'))->toBe('silber');
    expect(LeagueService::getNextLeague('gold'))->toBe('platin');
    expect(LeagueService::getNextLeague('saphir'))->toBe('diamant');
    expect(LeagueService::getNextLeague('diamant'))->toBeNull();
});

test('getPreviousLeague returns correct previous league', function () {
    expect(LeagueService::getPreviousLeague('silber'))->toBe('bronze');
    expect(LeagueService::getPreviousLeague('bronze'))->toBeNull();
    expect(LeagueService::getPreviousLeague('diamant'))->toBe('saphir');
    expect(LeagueService::getPreviousLeague('platin'))->toBe('gold');
});

test('getLeagueName returns correct name', function () {
    expect(LeagueService::getLeagueName('bronze'))->toBe('Bronze');
    expect(LeagueService::getLeagueName('diamant'))->toBe('Diamant');
    expect(LeagueService::getLeagueName('smaragd'))->toBe('Smaragd');
});

test('rollLootboxReward returns valid bronze rewards', function () {
    $service = new LeagueService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('rollLootboxReward');
    $method->setAccessible(true);

    $validTypes = ['xp', 'streak_freeze'];

    for ($i = 0; $i < 50; $i++) {
        $reward = $method->invoke($service, 'bronze');
        expect($reward)->toHaveKeys(['type', 'label']);
        expect($validTypes)->toContain($reward['type']);
    }
});

test('rollLootboxReward returns valid silber rewards', function () {
    $service = new LeagueService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('rollLootboxReward');
    $method->setAccessible(true);

    $validTypes = ['xp', 'streak_freeze', 'double_xp'];

    for ($i = 0; $i < 50; $i++) {
        $reward = $method->invoke($service, 'silber');
        expect($reward)->toHaveKeys(['type', 'label']);
        expect($validTypes)->toContain($reward['type']);
    }
});

test('rollLootboxReward returns valid gold rewards', function () {
    $service = new LeagueService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('rollLootboxReward');
    $method->setAccessible(true);

    $validTypes = ['xp', 'streak_freeze', 'double_xp'];

    for ($i = 0; $i < 50; $i++) {
        $reward = $method->invoke($service, 'gold');
        expect($reward)->toHaveKeys(['type', 'label']);
        expect($validTypes)->toContain($reward['type']);
    }
});

test('league order is correct from lowest to highest', function () {
    $leagues = LeagueService::getLeagues();

    $orders = array_column($leagues, 'order');
    expect($orders)->toBe([1, 2, 3, 4, 5, 6, 7, 8]);
});

test('promotion min points increase with league tier', function () {
    $minPoints = LeagueService::PROMOTION_MIN_POINTS;

    // Alle Ligen ausser Diamant muessen Mindest-Punkte haben
    expect($minPoints)->toHaveCount(7);
    expect($minPoints)->not->toHaveKey('diamant');

    // Punkte muessen aufsteigend sein
    $values = array_values($minPoints);
    for ($i = 1; $i < count($values); $i++) {
        expect($values[$i])->toBeGreaterThan($values[$i - 1]);
    }
});

test('min users for movement is at least 3', function () {
    expect(LeagueService::MIN_USERS_FOR_MOVEMENT)->toBeGreaterThanOrEqual(3);
});
