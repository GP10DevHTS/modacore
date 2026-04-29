<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// ─── Unauthenticated guests ───────────────────────────────────────────────────

test('guests are redirected from invoices', function () {
    $this->get(route('invoices.index'))->assertRedirect(route('login'));
});

test('guests are redirected from reports', function () {
    $this->get(route('reports.index'))->assertRedirect(route('login'));
});

test('guests are redirected from analytics', function () {
    $this->get(route('analytics.dashboard'))->assertRedirect(route('login'));
});

// ─── Authenticated but no permissions ────────────────────────────────────────

test('authenticated user without payments.view gets 403 on invoices', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('invoices.index'))->assertForbidden();
});

test('authenticated user without reports.view gets 403 on reports', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('reports.index'))->assertForbidden();
});

test('authenticated user without reports.view gets 403 on analytics', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('analytics.dashboard'))->assertForbidden();
});

// ─── Authorised users ─────────────────────────────────────────────────────────

test('user with payments.view can access invoices', function () {
    Permission::firstOrCreate(['name' => 'payments.view', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->givePermissionTo('payments.view');
    $this->actingAs($user);

    $this->get(route('invoices.index'))->assertOk();
});

test('user with reports.view can access reports', function () {
    Permission::firstOrCreate(['name' => 'reports.view', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->givePermissionTo('reports.view');
    $this->actingAs($user);

    $this->get(route('reports.index'))->assertOk();
});

test('user with reports.view can access analytics', function () {
    Permission::firstOrCreate(['name' => 'reports.view', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->givePermissionTo('reports.view');
    $this->actingAs($user);

    $this->get(route('analytics.dashboard'))->assertOk();
});

// ─── Superadmin bypass ────────────────────────────────────────────────────────

test('superadmin can access all finance modules', function () {
    Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole('superadmin');
    $this->actingAs($user);

    $this->get(route('invoices.index'))->assertOk();
    $this->get(route('reports.index'))->assertOk();
    $this->get(route('analytics.dashboard'))->assertOk();
});
