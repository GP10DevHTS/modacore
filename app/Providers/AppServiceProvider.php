<?php

namespace App\Providers;

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

        Onboard::addStep('Create Variant Types')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return VariantType::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Add Variant Types Values')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return VariantTypeValue::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Add Inventory Categories')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return InventoryCategory::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Add Inventory Items')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return InventoryItem::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Add Supplier')
            ->link('/suppliers')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return VariantTypeValue::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });


        Onboard::addStep('Add Purchase Order')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return PurchaseOrder::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Register a customer')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return PurchaseOrder::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });

        Onboard::addStep('Add a customer booking')
            ->link('/inventory')
            ->cta('Add Now')
            ->completeIf(function (User $model) {
                return PurchaseOrder::query()->exists();
            })
            ->excludeIf(function (User $model) {
                return $model->cannot('inventory.create');
            });



    }
}
