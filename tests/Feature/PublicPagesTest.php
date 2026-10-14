<?php

use App\Http\Controllers\LandingController;

function assertValidPublicPage(string $html): void
{
    expect(strtolower(substr(ltrim($html), 0, 15)))->toBe('<!doctype html>')
        ->and(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('<?php')
        ->and($html)->not->toContain('&amp;amp;');

    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
    expect($m[1])->not->toBeEmpty();
    foreach ($m[1] as $json) {
        expect(json_decode($json, true, flags: JSON_THROW_ON_ERROR))->toHaveKey('@context');
    }
}

it('rendert öffentliche Seiten mit gültigem Head', function (string $path) {
    assertValidPublicPage($this->get($path)->assertOk()->getContent());
})->with(['/thw-theorie', '/thw-grundausbildung', '/thw-pruefungsfragen', '/thw-theoriepruefung', '/statistik', '/wiki', '/wiki/pruefungssimulation', '/guest/practice-menu']);

// "/" gehört lokal und im Test der App, die Landing-Startseite daher direkt rendern
it('rendert die Startseite mit gültigem Head', function () {
    assertValidPublicPage(app(LandingController::class)->startseite()->render());
});
