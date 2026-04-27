<?php

use App\Livewire\Bookings\Create as BookingCreate;
use App\Livewire\Bookings\Index as BookingsIndex;
use App\Livewire\Bookings\Show as BookingShow;
use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\Employees\Attendance;
use App\Livewire\Employees\Index as EmployeesIndex;
use App\Livewire\Inventory\Index as InventoryIndex;
use App\Livewire\PurchaseOrders\Create as PurchaseOrderCreate;
use App\Livewire\PurchaseOrders\Index as PurchaseOrdersIndex;
use App\Livewire\Roles\Index as RolesIndex;
use App\Livewire\Suppliers\Index as SuppliersIndex;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/inventory', InventoryIndex::class)->name('inventory.index');

    Route::get('/customers', CustomersIndex::class)->name('customers.index');

    Route::get('/employees', EmployeesIndex::class)->name('employees.index');
    Route::get('/attendance', Attendance::class)->name('attendance.index');

    Route::get('/bookings', BookingsIndex::class)->name('bookings.index');
    Route::get('/bookings/create', BookingCreate::class)->name('bookings.create');
    Route::get('/bookings/{booking}', BookingShow::class)->name('bookings.show');
    Route::get('/bookings/{booking}/edit', BookingCreate::class)->name('bookings.edit');

    Route::get('/suppliers', SuppliersIndex::class)->name('suppliers.index');

    Route::get('/purchase-orders', PurchaseOrdersIndex::class)->name('purchase-orders.index');
    Route::get('/purchase-orders/create', PurchaseOrderCreate::class)->name('purchase-orders.create');
    Route::get('/purchase-orders/{purchaseOrder}/edit', PurchaseOrderCreate::class)->name('purchase-orders.edit');

    Route::get('/roles', RolesIndex::class)->name('roles.index');
});

require __DIR__.'/settings.php';
