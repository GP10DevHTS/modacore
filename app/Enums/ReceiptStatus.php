<?php

namespace App\Enums;

enum ReceiptStatus: string
{
    case NotReceived = 'not_received';
    case PartiallyReceived = 'partially_received';
    case FullyReceived = 'fully_received';

    public function label(): string
    {
        return match ($this) {
            self::NotReceived => 'Not Received',
            self::PartiallyReceived => 'Partially Received',
            self::FullyReceived => 'Fully Received',
        };
    }

    public function pillClasses(): string
    {
        return match ($this) {
            self::NotReceived => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
            self::PartiallyReceived => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
            self::FullyReceived => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::NotReceived => 'bg-zinc-400',
            self::PartiallyReceived => 'bg-amber-500',
            self::FullyReceived => 'bg-emerald-500',
        };
    }
}
