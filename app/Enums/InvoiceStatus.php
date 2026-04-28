<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case NotInvoiced = 'not_invoiced';
    case PartiallyInvoiced = 'partially_invoiced';
    case FullyInvoiced = 'fully_invoiced';

    public function label(): string
    {
        return match ($this) {
            self::NotInvoiced => 'Not Invoiced',
            self::PartiallyInvoiced => 'Partially Invoiced',
            self::FullyInvoiced => 'Fully Invoiced',
        };
    }

    public function pillClasses(): string
    {
        return match ($this) {
            self::NotInvoiced => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
            self::PartiallyInvoiced => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
            self::FullyInvoiced => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::NotInvoiced => 'bg-zinc-400',
            self::PartiallyInvoiced => 'bg-amber-500',
            self::FullyInvoiced => 'bg-emerald-500',
        };
    }
}
