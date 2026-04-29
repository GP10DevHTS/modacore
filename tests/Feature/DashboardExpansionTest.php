<?php

use App\Models\User;
use App\Notifications\BookingAlertNotification;
use App\Notifications\OverdueInvoiceNotification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─── Dashboard ─────────────────────────────────────────────────────────────

test('guest is redirected from dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated user can view dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Welcome back');
});

test('revenue summary section is gated by reports.view permission', function () {
    Permission::firstOrCreate(['name' => 'reports.view', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $userWithPerm = User::factory()->create();
    $userWithPerm->givePermissionTo('reports.view');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Revenue Overview');

    $this->actingAs($userWithPerm)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Revenue Overview');
});

test('procurement section is gated by inventory.edit permission', function () {
    Permission::firstOrCreate(['name' => 'inventory.edit', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $manager = User::factory()->create();
    $manager->givePermissionTo('inventory.edit');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Procurement');

    $this->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Procurement');
});

// ─── Notifications Center ──────────────────────────────────────────────────

test('guest is redirected from notifications center', function () {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
});

test('authenticated user can view notifications center', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notifications');
});

test('notifications center shows unread count when unread exist', function () {
    $user = User::factory()->create();
    $user->notify(new BookingAlertNotification('BK-001', 'Draft booking needs attention', 1));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('1 unread');
});

test('user can only see their own notifications', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $userA->notify(new BookingAlertNotification('BK-PRIVATE', 'Draft booking needs attention', 999));

    $this->actingAs($userB)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertDontSee('BK-PRIVATE');
});

// ─── Notification classes ──────────────────────────────────────────────────

test('overdue invoice notification has correct structure', function () {
    $user = User::factory()->create();
    $notification = new OverdueInvoiceNotification('INV-001', 'ACME Ltd', 500_000, 3, 1);

    $data = $notification->toDatabase($user);

    expect($data)
        ->toHaveKeys(['title', 'message', 'action_url', 'icon_type', 'invoice_id'])
        ->and($data['icon_type'])->toBe('warning')
        ->and($data['invoice_id'])->toBe(1);
});

test('booking alert notification has correct structure', function () {
    $user = User::factory()->create();
    $notification = new BookingAlertNotification('BK-007', 'Draft booking needs attention', 42);

    $data = $notification->toDatabase($user);

    expect($data)
        ->toHaveKeys(['title', 'message', 'icon_type', 'booking_id'])
        ->and($data['icon_type'])->toBe('info')
        ->and($data['booking_id'])->toBe(42);
});

test('superadmin can view notifications center', function () {
    $superAdmin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin->assignRole($role);

    $this->actingAs($superAdmin)
        ->get(route('notifications.index'))
        ->assertOk();
});
