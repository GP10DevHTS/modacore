<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case PartiallyPaid = 'partially_paid';
    case FullyPaid = 'fully_paid';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Unpaid',
            self::PartiallyPaid => 'Partially Paid',
            self::FullyPaid => 'Fully Paid',
        };
    }

    public function pillClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
            self::PartiallyPaid => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
            self::FullyPaid => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-red-500',
            self::PartiallyPaid => 'bg-amber-500',
            self::FullyPaid => 'bg-emerald-500',
        };
    }
}
