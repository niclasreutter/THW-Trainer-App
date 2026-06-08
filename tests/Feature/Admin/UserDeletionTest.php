<?php

use App\Models\DataDeletionLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Models\XpHistory;
use App\Services\UserDeletionService;
use Illuminate\Support\Facades\DB;

function makeAdmin(): User
{
    return User::factory()->create(['useroll' => 'admin']);
}

function makeUser(): User
{
    return User::factory()->create(['useroll' => 'user']);
}

test('löscht user und kaskadierte daten', function () {
    $user = makeUser();

    XpHistory::create([
        'user_id'      => $user->id,
        'points'       => 50,
        'reason'       => 'test',
        'total_before' => 0,
        'total_after'  => 50,
        'level_before' => 1,
        'level_after'  => 1,
    ]);

    Notification::create([
        'user_id' => $user->id,
        'type'    => 'test',
        'title'   => 'Hi',
        'message' => 'Test',
    ]);

    expect(XpHistory::where('user_id', $user->id)->count())->toBe(1);
    expect(Notification::where('user_id', $user->id)->count())->toBe(1);

    app(UserDeletionService::class)->deleteUser($user);

    expect(User::find($user->id))->toBeNull();
    expect(XpHistory::where('user_id', $user->id)->count())->toBe(0);
    expect(Notification::where('user_id', $user->id)->count())->toBe(0);
});

test('invalidiert aktive sessions des users', function () {
    $user = makeUser();

    DB::table('sessions')->insert([
        'id'            => 'session-abc',
        'user_id'       => $user->id,
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'Test',
        'payload'       => 'test',
        'last_activity' => now()->timestamp,
    ]);

    expect(DB::table('sessions')->where('user_id', $user->id)->count())->toBe(1);

    app(UserDeletionService::class)->deleteUser($user);

    expect(DB::table('sessions')->where('user_id', $user->id)->count())->toBe(0);
});

test('erstellt compliance-log ohne personenbezogene daten', function () {
    $admin = makeAdmin();
    $user = makeUser();

    app(UserDeletionService::class)->deleteUser($user, $admin, 'Antrag des Nutzers');

    $log = DataDeletionLog::latest()->first();

    expect($log)->not->toBeNull()
        ->and($log->admin_id)->toBe($admin->id)
        ->and($log->reason)->toBe('Antrag des Nutzers')
        ->and($log->deletion_type)->toBe('user_account');
});

test('compliance-log enthält keine referenz auf gelöschten user', function () {
    $admin = makeAdmin();
    $user = makeUser();
    $deletedUserId = $user->id;
    $deletedUserEmail = $user->email;
    $deletedUserName = $user->name;

    app(UserDeletionService::class)->deleteUser($user, $admin);

    $log = DataDeletionLog::latest()->first();

    expect($log->getAttributes())->not->toHaveKey('user_id');

    $serialized = json_encode($log->getAttributes());
    expect($serialized)
        ->not->toContain($deletedUserEmail)
        ->not->toContain($deletedUserName);
});

test('löscht user-fragenfortschritt kaskadiert', function () {
    $user = makeUser();
    $question = \App\Models\Question::create([
        'nummer'        => 9999,
        'lernabschnitt' => '1',
        'frage'         => 'Test?',
        'antwort_a'     => 'A',
        'antwort_b'     => 'B',
        'antwort_c'     => 'C',
        'loesung'       => 'A',
    ]);

    UserQuestionProgress::create([
        'user_id'             => $user->id,
        'question_id'         => $question->id,
        'consecutive_correct' => 5,
        'solved'              => true,
    ]);

    expect(UserQuestionProgress::where('user_id', $user->id)->count())->toBe(1);

    app(UserDeletionService::class)->deleteUser($user);

    expect(UserQuestionProgress::where('user_id', $user->id)->count())->toBe(0);
});

test('admin kann eigenen account nicht über admin-route löschen', function () {
    $admin = makeAdmin();

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin->id));

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('error');
    expect(User::find($admin->id))->not->toBeNull();
});

test('controller verlangt lösch-grund', function () {
    $admin = makeAdmin();
    $victim = makeUser();

    $response = $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $victim->id));

    $response->assertSessionHasErrors('reason');
    expect(User::find($victim->id))->not->toBeNull();
});

test('controller löscht user und persistiert grund', function () {
    $admin = makeAdmin();
    $victim = makeUser();

    $response = $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $victim->id), [
            'reason' => 'Inaktiver Account, vom Nutzer per Mail beantragt',
        ]);

    $response->assertRedirect(route('admin.users.index'));
    expect(User::find($victim->id))->toBeNull();

    $log = DataDeletionLog::latest()->first();
    expect($log->reason)->toBe('Inaktiver Account, vom Nutzer per Mail beantragt')
        ->and($log->admin_id)->toBe($admin->id);
});
