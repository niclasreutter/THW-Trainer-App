<?php

use App\Http\Controllers\LandingController;
use App\Models\Question;

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
})->with(['/thw-theorie', '/thw-theorie/leitern', '/thw-grundausbildung', '/thw-pruefungsfragen', '/thw-theoriepruefung', '/statistik', '/wiki', '/wiki/pruefungssimulation', '/guest/practice-menu']);

function question(array $attributes = []): Question
{
    return Question::create($attributes + ['lernabschnitt' => '4', 'nummer' => 1, 'frage' => 'Wie wird eine Leiter angelegt?', 'antwort_a' => 'Im Winkel von 65 bis 75 Grad', 'antwort_b' => 'Senkrecht', 'antwort_c' => 'Waagerecht', 'loesung' => 'A']);
}

it('zeigt auf der Abschnittsseite alle veröffentlichten Fragen mit Lösung', function () {
    question(['nummer' => 12]);
    question(['nummer' => 2, 'frage' => 'Wer prüft Leitern?', 'loesung' => 'A,C']);
    question(['nummer' => 3, 'frage' => 'Noch im Entwurf', 'status' => 'draft']);
    question(['lernabschnitt' => '5', 'frage' => 'Stromfrage']);

    $html = $this->get('/thw-theorie/leitern')->assertOk()->getContent();

    expect($html)->toContain('Frage 4.2')->toContain('Richtig: A und C.')
        ->and(strpos($html, 'id="frage-4-2"'))->toBeLessThan(strpos($html, 'id="frage-4-12"'))
        ->and($html)->not->toContain('Noch im Entwurf')->not->toContain('Stromfrage');

    $this->get('/thw-theorie/gibt-es-nicht')->assertNotFound();
});

it('startet das Üben eines Lernabschnitts ohne Konto', function () {
    $leiter = question();
    question(['lernabschnitt' => '5']);

    $this->get('/guest/practice/section/4')
        ->assertRedirect(route('landing.guest.practice.index'))
        ->assertSessionHas('guest_practice_ids', [$leiter->id]);

    $this->get('/guest/practice/section/11')->assertNotFound();
});

it('listet die Abschnittsseiten in der Sitemap', function () {
    $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

    expect(substr_count($xml, '/thw-theorie/'))->toBe(10)
        ->and($xml)->toContain('/thw-theorie/leitern</loc>');
});

// "/" gehört lokal und im Test der App, die Landing-Startseite daher direkt rendern
it('rendert die Startseite mit gültigem Head', function () {
    assertValidPublicPage(app(LandingController::class)->startseite()->render());
});
