<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\Onboard\Facades\Onboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Superadmin bypasses all permission gates.
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('superadmin')) {
                return true;
            }
        });

        $this->onBoardUser();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    private function onBoardUser()
    {
        Onboard::addStep('Define Variant Types')
            ->link('/inventory')
            ->cta('Configure Types')
            ->completeIf(function (User $model) {
                return VariantType::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Configure Variant Values')
            ->link('/inventory')
            ->cta('Add Values')
            ->completeIf(function (User $model) {
                return VariantType::query()->exists() && VariantTypeValue::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Create Inventory Categories')
            ->link('/inventory')
            ->cta('Add Categories')
            ->completeIf(function (User $model) {
                return InventoryCategory::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Add Inventory Items')
            ->link('/inventory')
            ->cta('Register Items')
            ->completeIf(function (User $model) {
                return InventoryItem::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Register Suppliers')
            ->link('/suppliers')
            ->cta('Add Suppliers')
            ->completeIf(function (User $model) {
                return Supplier::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('suppliers.create');
            });

        Onboard::addStep('Initiate Purchase Orders')
            ->link('/purchase-orders/create')
            ->cta('Create Order')
            ->completeIf(function (User $model) {
                return PurchaseOrder::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('procurement.create');
            });

        Onboard::addStep('Register Your First Customer')
            ->link('/customers')
            ->cta('Add Customer')
            ->completeIf(function (User $model) {
                return Customer::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('customers.create');
            });

        Onboard::addStep('Create a Customer Booking')
            ->link('/bookings/create')
            ->cta('New Booking')
            ->completeIf(function (User $model) {
                return Booking::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('bookings.create');
            });
    }
}
