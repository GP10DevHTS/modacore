<?php

namespace App\Enums;

enum CancellationType: string
{
    /** No goods received yet — cancel directly. */
    case Direct = 'direct';

    /** Goods were received — requires Return to Supplier. */
    case WithReturnToSupplier = 'with_rts';

    /** Invoice was raised — requires Credit Note. */
    case WithCreditNote = 'with_credit_note';

    /** Payment was made — requires Refund record. */
    case WithRefund = 'with_refund';

    public function label(): string
    {
        return match ($this) {
            self::Direct => 'Direct Cancellation',
            self::WithReturnToSupplier => 'Return to Supplier Required',
            self::WithCreditNote => 'Credit Note Required',
            self::WithRefund => 'Refund Required',
        };
    }
}
