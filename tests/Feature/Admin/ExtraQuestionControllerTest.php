<?php

use App\Models\ExtraMatchCategory;
use App\Models\ExtraMatchItem;
use App\Models\ExtraQuestion;
use App\Models\ExtraQuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Worktree-Bootstrap: Pest.php im Haupt-Repo kennt diesen Ordner nicht.
uses(Tests\TestCase::class, RefreshDatabase::class);

function adminUser(): User
{
    return User::factory()->create(['useroll' => 'admin']);
}

function regularUser(): User
{
    return User::factory()->create(['useroll' => 'user']);
}

// --- Authorization ---

test('admin kann Index sehen', function () {
    $response = $this->actingAs(adminUser())->get(route('admin.extra-questions.index'));
    $response->assertOk();
});

test('non-admin bekommt 403 auf Index', function () {
    $response = $this->actingAs(regularUser())->get(route('admin.extra-questions.index'));
    $response->assertForbidden();
});

test('non-admin bekommt 403 auf create', function () {
    $response = $this->actingAs(regularUser())->get(route('admin.extra-questions.create'));
    $response->assertForbidden();
});

test('non-admin bekommt 403 auf store', function () {
    $response = $this->actingAs(regularUser())->post(route('admin.extra-questions.store'), []);
    $response->assertForbidden();
});

test('non-admin bekommt 403 auf edit', function () {
    $q = ExtraQuestion::factory()->matching()->create();
    $response = $this->actingAs(regularUser())->get(route('admin.extra-questions.edit', $q));
    $response->assertForbidden();
});

test('non-admin bekommt 403 auf update', function () {
    $q = ExtraQuestion::factory()->matching()->create();
    $response = $this->actingAs(regularUser())->put(route('admin.extra-questions.update', $q), []);
    $response->assertForbidden();
});

test('non-admin bekommt 403 auf destroy', function () {
    $q = ExtraQuestion::factory()->matching()->create();
    $response = $this->actingAs(regularUser())->delete(route('admin.extra-questions.destroy', $q));
    $response->assertForbidden();
});

// --- Store: Matching ---

test('store matching: legt Frage + 2 Kategorien + 3 Items an', function () {
    $response = $this->actingAs(adminUser())->post(route('admin.extra-questions.store'), [
        'typ' => 'matching',
        'lernabschnitt' => '5',
        'frage' => 'Ordne die Werkzeuge zu.',
        'categories' => [
            ['name' => 'Kategorie A'],
            ['name' => 'Kategorie B'],
        ],
        'items' => [
            ['text' => 'Item 1', 'category_index' => 0],
            ['text' => 'Item 2', 'category_index' => 1],
            ['text' => 'Item 3', 'category_index' => 0],
        ],
    ]);

    $response->assertRedirect(route('admin.extra-questions.index'));

    $q = ExtraQuestion::first();
    expect($q)->not->toBeNull();
    expect($q->typ)->toBe('matching');
    expect($q->matchCategories()->count())->toBe(2);
    expect($q->matchItems()->count())->toBe(3);
});

// --- Store: image_name ---

test('store image_name: Bild hochgeladen und Options gespeichert', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('werkzeug.jpg')->size(100);

    $response = $this->actingAs(adminUser())->post(route('admin.extra-questions.store'), [
        'typ' => 'image_name',
        'lernabschnitt' => '4',
        'frage' => 'Wie heißt dieses Werkzeug?',
        'image' => $file,
        'options' => [
            ['text' => 'Hammer', 'is_correct' => '1'],
            ['text' => 'Säge', 'is_correct' => '0'],
            ['text' => 'Zange', 'is_correct' => '0'],
        ],
    ]);

    $response->assertRedirect(route('admin.extra-questions.index'));

    $q = ExtraQuestion::first();
    expect($q)->not->toBeNull();
    expect($q->typ)->toBe('image_name');
    expect($q->image_path)->not->toBeNull();
    expect($q->image_path)->toStartWith('extra-questions/');
    expect($q->image_path)->not->toStartWith('public/');
    expect($q->image_path)->not->toStartWith('/');

    Storage::disk('public')->assertExists($q->image_path);

    expect($q->options()->count())->toBe(3);
    expect($q->options()->where('is_correct', true)->count())->toBe(1);
});

// --- Store: image_select ---

test('store image_select: mehrere Bilder hochgeladen + Options persistent', function () {
    Storage::fake('public');

    $file1 = UploadedFile::fake()->image('a.png')->size(80);
    $file2 = UploadedFile::fake()->image('b.png')->size(80);

    $response = $this->actingAs(adminUser())->post(route('admin.extra-questions.store'), [
        'typ' => 'image_select',
        'lernabschnitt' => '6',
        'frage' => 'Welches Bild zeigt einen Hammer?',
        'options' => [
            ['image' => $file1, 'is_correct' => '1'],
            ['image' => $file2, 'is_correct' => '0'],
        ],
    ]);

    $response->assertRedirect(route('admin.extra-questions.index'));

    $q = ExtraQuestion::first();
    expect($q)->not->toBeNull();
    expect($q->typ)->toBe('image_select');
    expect($q->options()->count())->toBe(2);

    foreach ($q->options as $opt) {
        expect($opt->image_path)->not->toBeNull();
        expect($opt->image_path)->toStartWith('extra-questions/');
        Storage::disk('public')->assertExists($opt->image_path);
    }
});

// --- Update: Typ immutable ---

test('update matching: typ ist immutable (submitted typ wird ignoriert)', function () {
    $q = ExtraQuestion::factory()->matching()->create(['lernabschnitt' => '5']);

    $response = $this->actingAs(adminUser())->put(route('admin.extra-questions.update', $q), [
        'typ' => 'image_name', // Angriffs-Attempt: typ verändern
        'lernabschnitt' => '7',
        'frage' => 'Aktualisierte Frage',
        'categories' => [
            ['name' => 'Neue Kat A'],
            ['name' => 'Neue Kat B'],
        ],
        'items' => [
            ['text' => 'Neu Item 1', 'category_index' => 0],
            ['text' => 'Neu Item 2', 'category_index' => 1],
            ['text' => 'Neu Item 3', 'category_index' => 0],
        ],
    ]);

    $response->assertRedirect(route('admin.extra-questions.index'));

    $q->refresh();
    expect($q->typ)->toBe('matching'); // unverändert
    expect($q->lernabschnitt)->toBe('7');
    expect($q->frage)->toBe('Aktualisierte Frage');
});

// --- Update: image_name, Bild ersetzen löscht altes ---

test('update image_name: neues Bild wird hochgeladen, altes entfernt', function () {
    Storage::fake('public');

    // Altes Bild setzen
    $oldPath = UploadedFile::fake()->image('old.jpg')->size(50)
        ->store('extra-questions', 'public');

    $q = ExtraQuestion::create([
        'typ' => 'image_name',
        'lernabschnitt' => '4',
        'frage' => 'Test',
        'image_path' => $oldPath,
    ]);
    ExtraQuestionOption::create([
        'extra_question_id' => $q->id, 'text' => 'A', 'is_correct' => true, 'sort_order' => 1,
    ]);
    ExtraQuestionOption::create([
        'extra_question_id' => $q->id, 'text' => 'B', 'is_correct' => false, 'sort_order' => 2,
    ]);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->image('new.jpg')->size(50);

    $response = $this->actingAs(adminUser())->put(route('admin.extra-questions.update', $q), [
        'lernabschnitt' => '4',
        'frage' => 'Test',
        'image' => $newFile,
        'options' => [
            ['text' => 'X', 'is_correct' => '1'],
            ['text' => 'Y', 'is_correct' => '0'],
        ],
    ]);

    $response->assertRedirect(route('admin.extra-questions.index'));

    $q->refresh();
    expect($q->image_path)->not->toBe($oldPath);
    expect($q->image_path)->toStartWith('extra-questions/');
    Storage::disk('public')->assertExists($q->image_path);
    Storage::disk('public')->assertMissing($oldPath);
});

// --- Destroy: Cascade ---

test('destroy: löscht matching-Frage inkl. Kategorien und Items (Cascade)', function () {
    $q = ExtraQuestion::factory()->matching()->create();
    $qId = $q->id;
    expect(ExtraMatchCategory::where('extra_question_id', $qId)->count())->toBeGreaterThan(0);
    expect(ExtraMatchItem::where('extra_question_id', $qId)->count())->toBeGreaterThan(0);

    $response = $this->actingAs(adminUser())->delete(route('admin.extra-questions.destroy', $q));
    $response->assertRedirect(route('admin.extra-questions.index'));

    expect(ExtraQuestion::find($qId))->toBeNull();
    expect(ExtraMatchCategory::where('extra_question_id', $qId)->count())->toBe(0);
    expect(ExtraMatchItem::where('extra_question_id', $qId)->count())->toBe(0);
});

test('destroy: löscht image_name-Frage inkl. Optionen (Cascade)', function () {
    $q = ExtraQuestion::factory()->imageName()->create();
    $qId = $q->id;
    expect(ExtraQuestionOption::where('extra_question_id', $qId)->count())->toBe(3);

    $this->actingAs(adminUser())->delete(route('admin.extra-questions.destroy', $q))
        ->assertRedirect(route('admin.extra-questions.index'));

    expect(ExtraQuestion::find($qId))->toBeNull();
    expect(ExtraQuestionOption::where('extra_question_id', $qId)->count())->toBe(0);
});

// --- Validation ---

test('store: fehlender lernabschnitt -> 422', function () {
    $response = $this->actingAs(adminUser())
        ->from(route('admin.extra-questions.create'))
        ->post(route('admin.extra-questions.store'), [
            'typ' => 'matching',
            'frage' => 'Test',
            'categories' => [['name' => 'A'], ['name' => 'B']],
            'items' => [
                ['text' => 'I1', 'category_index' => 0],
                ['text' => 'I2', 'category_index' => 1],
                ['text' => 'I3', 'category_index' => 0],
            ],
        ]);

    $response->assertSessionHasErrors('lernabschnitt');
});

test('store: ungültiger typ -> 422', function () {
    $response = $this->actingAs(adminUser())
        ->from(route('admin.extra-questions.create'))
        ->post(route('admin.extra-questions.store'), [
            'typ' => 'bogus',
            'lernabschnitt' => '5',
            'frage' => 'Test',
        ]);

    $response->assertSessionHasErrors('typ');
});

test('store matching: weniger als 2 Kategorien -> 422', function () {
    $response = $this->actingAs(adminUser())
        ->from(route('admin.extra-questions.create'))
        ->post(route('admin.extra-questions.store'), [
            'typ' => 'matching',
            'lernabschnitt' => '5',
            'frage' => 'Test',
            'categories' => [['name' => 'Nur eine']],
            'items' => [
                ['text' => 'I1', 'category_index' => 0],
                ['text' => 'I2', 'category_index' => 0],
                ['text' => 'I3', 'category_index' => 0],
            ],
        ]);

    $response->assertSessionHasErrors('categories');
});

test('store image_name: Bild zu groß (>5MB) -> 422', function () {
    Storage::fake('public');

    // max:5120 KB → 6000 KB ist zu groß
    $bigFile = UploadedFile::fake()->image('big.jpg')->size(6000);

    $response = $this->actingAs(adminUser())
        ->from(route('admin.extra-questions.create'))
        ->post(route('admin.extra-questions.store'), [
            'typ' => 'image_name',
            'lernabschnitt' => '4',
            'frage' => 'Test',
            'image' => $bigFile,
            'options' => [
                ['text' => 'A', 'is_correct' => '1'],
                ['text' => 'B', 'is_correct' => '0'],
            ],
        ]);

    $response->assertSessionHasErrors('image');
});
