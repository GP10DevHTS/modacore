<?php

use App\Livewire\Expenses\Index;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function createExpensePermissions(): void
{
    foreach (['expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
}

function userWithExpensePermissions(array $permissions): User
{
    createExpensePermissions();
    $role = Role::firstOrCreate(['name' => 'test-expense-role', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

// ─── Authorization ────────────────────────────────────────────────────────────

test('guests cannot visit expenses index', function () {
    $this->get(route('expenses.index'))->assertRedirect(route('login'));
});

test('users without expenses.view cannot visit expenses index', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('expenses.index'))
        ->assertForbidden();
});

test('users with expenses.view can visit expenses index', function () {
    $user = userWithExpensePermissions(['expenses.view']);
    $this->actingAs($user)
        ->get(route('expenses.index'))
        ->assertOk();
});

// ─── Listing ─────────────────────────────────────────────────────────────────

test('expenses appear in the list', function () {
    $user = userWithExpensePermissions(['expenses.view']);
    $expense = Expense::factory()->create([
        'title' => 'Office Rent',
        'created_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('Office Rent');
});

test('expenses can be filtered by search', function () {
    $user = userWithExpensePermissions(['expenses.view']);
    Expense::factory()->create(['title' => 'Electricity Bill', 'created_by' => $user->id]);
    Expense::factory()->create(['title' => 'Transport Allowance', 'created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('search', 'Electricity')
        ->assertSee('Electricity Bill')
        ->assertDontSee('Transport Allowance');
});

test('expenses can be filtered by status', function () {
    $user = userWithExpensePermissions(['expenses.view']);
    Expense::factory()->draft()->create(['title' => 'Draft Expense', 'created_by' => $user->id]);
    Expense::factory()->approved()->create(['title' => 'Approved Expense', 'created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('statusFilter', 'draft')
        ->assertSee('Draft Expense')
        ->assertDontSee('Approved Expense');
});

// ─── Creating ─────────────────────────────────────────────────────────────────

test('users with expenses.create can record an expense', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.create']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openCreate')
        ->set('title', 'Monthly Internet')
        ->set('amount', '50000')
        ->set('expenseDate', today()->toDateString())
        ->set('paymentMethod', 'mobile_money')
        ->set('status', 'approved')
        ->call('save');

    $this->assertDatabaseHas('expenses', ['title' => 'Monthly Internet', 'status' => 'approved']);
});

test('creating an expense without expenses.create is forbidden', function () {
    $user = userWithExpensePermissions(['expenses.view']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openCreate')
        ->assertForbidden();
});

test('expense creation validates required fields', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.create']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openCreate')
        ->set('title', '')
        ->set('amount', '')
        ->call('save')
        ->assertHasErrors(['title', 'amount']);
});

test('expense numbers are sequential', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.create']);
    $year = now()->year;

    Livewire::actingAs($user)->test(Index::class)
        ->call('openCreate')
        ->set('title', 'First Expense')
        ->set('amount', '10000')
        ->set('expenseDate', today()->toDateString())
        ->set('paymentMethod', 'cash')
        ->set('status', 'draft')
        ->call('save');

    Livewire::actingAs($user)->test(Index::class)
        ->call('openCreate')
        ->set('title', 'Second Expense')
        ->set('amount', '20000')
        ->set('expenseDate', today()->toDateString())
        ->set('paymentMethod', 'cash')
        ->set('status', 'draft')
        ->call('save');

    $numbers = Expense::pluck('expense_number');
    expect($numbers)->toContain("EXP-{$year}-0001")
        ->and($numbers)->toContain("EXP-{$year}-0002");
});

// ─── Editing ──────────────────────────────────────────────────────────────────

test('users with expenses.edit can update an expense', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.edit']);
    $expense = Expense::factory()->create(['title' => 'Old Title', 'created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openEdit', $expense->id)
        ->set('title', 'New Title')
        ->call('save');

    $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'title' => 'New Title']);
});

// ─── Deleting ─────────────────────────────────────────────────────────────────

test('users with expenses.delete can soft-delete an expense', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.delete']);
    $expense = Expense::factory()->create(['created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('delete', $expense->id);

    $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
});

test('deleting without expenses.delete is forbidden', function () {
    $user = userWithExpensePermissions(['expenses.view']);
    $expense = Expense::factory()->create(['created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('delete', $expense->id)
        ->assertForbidden();
});

// ─── Categories ───────────────────────────────────────────────────────────────

test('users with expenses.create can add a category', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.create']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openCategories')
        ->set('categoryName', 'Utilities')
        ->call('saveCategory');

    $this->assertDatabaseHas('expense_categories', ['name' => 'Utilities']);
});

test('cannot delete a category that has expenses', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.create', 'expenses.delete']);
    $category = ExpenseCategory::factory()->create();
    Expense::factory()->create(['category_id' => $category->id, 'created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteCategory', $category->id);

    $this->assertModelExists($category);
});

test('can delete a category with no expenses', function () {
    $user = userWithExpensePermissions(['expenses.view', 'expenses.delete']);
    $category = ExpenseCategory::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteCategory', $category->id);

    $this->assertModelMissing($category);
});

// ─── Summary Computed Properties ──────────────────────────────────────────────

test('summary cards show correct totals', function () {
    $user = userWithExpensePermissions(['expenses.view']);
    Expense::factory()->approved()->create(['amount' => 100000, 'created_by' => $user->id]);
    Expense::factory()->draft()->create(['amount' => 50000, 'created_by' => $user->id]);

    $component = Livewire::actingAs($user)->test(Index::class);

    expect($component->get('summaryApproved'))->toBe(100000.0)
        ->and($component->get('summaryDraft'))->toBe(50000.0);
});
