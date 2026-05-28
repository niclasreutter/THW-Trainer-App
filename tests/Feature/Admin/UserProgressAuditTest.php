<?php

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

function makeAdminForAudit(): User
{
    return User::factory()->create(['useroll' => 'admin']);
}

function makeUserForAudit(): User
{
    return User::factory()->create(['useroll' => 'user']);
}

test('öffnen der detail-fortschrittsseite wird geloggt', function () {
    $admin = makeAdminForAudit();
    $victim = makeUserForAudit();

    $this->actingAs($admin)->get(route('admin.users.progress.edit', $victim->id))
        ->assertOk();

    $log = AdminAuditLog::where('user_id', $victim->id)
        ->where('action', 'view_progress_detail')
        ->latest()
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->admin_id)->toBe($admin->id)
        ->and($log->new_value)->toContain('Fortschrittsansicht');
});

test('progress-json zugriff wird geloggt', function () {
    $admin = makeAdminForAudit();
    $victim = makeUserForAudit();

    $this->actingAs($admin)->get(route('admin.users.progress-json', $victim->id))
        ->assertOk();

    $log = AdminAuditLog::where('user_id', $victim->id)
        ->where('action', 'view_progress_detail')
        ->latest()
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->admin_id)->toBe($admin->id);
});

test('xp-history zugriff wird geloggt', function () {
    $admin = makeAdminForAudit();
    $victim = makeUserForAudit();

    $this->actingAs($admin)->get(route('admin.users.xp-history', $victim->id))
        ->assertOk();

    $log = AdminAuditLog::where('user_id', $victim->id)
        ->where('action', 'view_xp_history')
        ->latest()
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->admin_id)->toBe($admin->id);
});

test('audit-log enthält ip-adresse des admins', function () {
    $admin = makeAdminForAudit();
    $victim = makeUserForAudit();

    $this->actingAs($admin)
        ->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])
        ->get(route('admin.users.xp-history', $victim->id));

    $log = AdminAuditLog::where('user_id', $victim->id)
        ->where('action', 'view_xp_history')
        ->first();

    expect($log->ip_address)->toBe('203.0.113.42');
});
