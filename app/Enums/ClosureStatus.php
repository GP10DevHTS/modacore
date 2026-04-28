<?php

namespace App\Enums;

enum ClosureStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
    case ForceClosed = 'force_closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
            self::ForceClosed => 'Force Closed',
        };
    }

    public function pillClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
            self::Closed => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
            self::ForceClosed => 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
        };
    }
}
