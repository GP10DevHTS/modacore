<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Sent = 'sent';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Approved => 'Approved',
            self::Sent => 'Sent',
            self::Cancelled => 'Cancelled',
        };
    }

    public function pillClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
            self::Approved => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400',
            self::Sent => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
            self::Cancelled => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-zinc-400',
            self::Approved => 'bg-violet-500',
            self::Sent => 'bg-blue-500',
            self::Cancelled => 'bg-red-500',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::Draft, self::Approved]);
    }

    public function canCancel(): bool
    {
        return $this !== self::Cancelled;
    }
}
