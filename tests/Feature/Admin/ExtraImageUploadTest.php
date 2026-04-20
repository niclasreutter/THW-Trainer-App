<?php

use App\Models\ExtraQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class, RefreshDatabase::class);

function admin(): User
{
    return User::factory()->create(['useroll' => 'admin']);
}

function storeImageName(UploadedFile $file)
{
    return test()->actingAs(admin())
        ->from(route('admin.extra-questions.create'))
        ->post(route('admin.extra-questions.store'), [
            'typ' => 'image_name',
            'lernabschnitt' => '4',
            'frage' => 'Test-Bild',
            'image' => $file,
            'options' => [
                ['text' => 'A', 'is_correct' => '1'],
                ['text' => 'B', 'is_correct' => '0'],
            ],
        ]);
}

test('JPG wird akzeptiert', function () {
    Storage::fake('public');

    $response = storeImageName(UploadedFile::fake()->image('t.jpg')->size(50));
    $response->assertRedirect(route('admin.extra-questions.index'));
    $response->assertSessionHasNoErrors();
    expect(ExtraQuestion::count())->toBe(1);
});

test('PNG wird akzeptiert', function () {
    Storage::fake('public');

    $response = storeImageName(UploadedFile::fake()->image('t.png')->size(50));
    $response->assertRedirect(route('admin.extra-questions.index'));
    $response->assertSessionHasNoErrors();
    expect(ExtraQuestion::count())->toBe(1);
});

test('WebP wird akzeptiert', function () {
    Storage::fake('public');

    // WebP-MIME manuell setzen, da fake()->image('...') keine WebP-Signatur erzeugt
    $response = storeImageName(
        UploadedFile::fake()->create('t.webp', 50, 'image/webp')
    );
    $response->assertRedirect(route('admin.extra-questions.index'));
    $response->assertSessionHasNoErrors();
    expect(ExtraQuestion::count())->toBe(1);
});

test('GIF wird abgelehnt (422)', function () {
    Storage::fake('public');

    $response = storeImageName(UploadedFile::fake()->create('t.gif', 50, 'image/gif'));
    $response->assertSessionHasErrors('image');
    expect(ExtraQuestion::count())->toBe(0);
});

test('PDF wird abgelehnt (422)', function () {
    Storage::fake('public');

    $response = storeImageName(UploadedFile::fake()->create('t.pdf', 50, 'application/pdf'));
    $response->assertSessionHasErrors('image');
    expect(ExtraQuestion::count())->toBe(0);
});

test('Bild > 5MB wird abgelehnt (422)', function () {
    Storage::fake('public');

    $response = storeImageName(UploadedFile::fake()->image('big.jpg')->size(6000));
    $response->assertSessionHasErrors('image');
    expect(ExtraQuestion::count())->toBe(0);
});

test('Upload-Pfad ist relativ (extra-questions/...), kein Leading public/ oder Slash', function () {
    Storage::fake('public');

    storeImageName(UploadedFile::fake()->image('ok.jpg')->size(50));

    $q = ExtraQuestion::first();
    expect($q)->not->toBeNull();
    expect($q->image_path)->not->toBeNull();
    expect($q->image_path)->toStartWith('extra-questions/');
    expect($q->image_path)->not->toStartWith('public/');
    expect($q->image_path)->not->toStartWith('/');
    // Datei existiert im fake-Disk unter dem gespeicherten relativen Pfad
    Storage::disk('public')->assertExists($q->image_path);
});
