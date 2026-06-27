<?php

namespace App\Services;

use App\Models\DepositRefund;
use App\Models\Payment;
use Illuminate\Http\Response;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class ReceiptService
{
    public function paymentReceipt(Payment $payment): Response
    {
        $payment->load(['booking.customer', 'createdBy']);

        $customer = $payment->booking->customer;
        $booking = $payment->booking;

        $type = $payment->is_deposit ? 'Deposit Receipt' : 'Payment Receipt';

        $buyer = new Party([
            'name' => $customer->name,
            'phone' => $customer->phone,
        ]);

        $description = $payment->is_deposit
            ? 'Security deposit for booking '.$booking->booking_number
            : 'Payment for booking '.$booking->booking_number;

        //        $item = InvoiceItem::make($description)
        //            ->pricePerUnit((float) $payment->amount)
        //            ->quantity(1);

        $items = [];
        foreach ($payment->booking?->items as $item) {
            $items[] = InvoiceItem::make(
                $item->inventoryItem->name.($item->variant ? '('.$item->variant->name.')' : '')
            )
                ->pricePerUnit((float) $item->unit_price)
                ->quantity($item->quantity);
        }

        $notes = collect([
            'Booking: '.$booking->booking_number,
            'Book From: '.$booking->hire_from->format('D d M Y'),
            'Return Date: '.$booking->hire_to->format('D d M Y'),
            'Method: '.ucwords(str_replace('_', ' ', $payment->payment_method)),
            $payment->reference ? 'Reference: '.$payment->reference : null,
            $payment->notes ? 'Notes: '.$payment->notes : null,
        ])->filter()->implode("\n");

        $invoice = Invoice::make($type)
            ->template('receipt')
            ->serialNumberFormat('{SERIES}')
//            ->payUntilDays(3)
            ->series($payment->receipt_number)
            ->buyer($buyer)
            ->date($payment->paid_at)
            ->addItems($items)
            ->totalAmount($payment->amount)
            ->notes($notes)
            ->filename($payment->receipt_number);

        $invoice->custom_fields = [
            'invoice_total' => $payment->booking?->total_amount ?? '0.00',
            'previous_payment' => $payment->booking?->payments()->where('id', '<', $payment->id)->sum('amount') ?? '0.00',
            //            'payment_received' => $payment->amount,
            'balance_due' => max(0, $payment->booking?->total_amount - $payment->booking?->payments()->sum('amount')),
        ];

        return $invoice->stream();
    }

    public function refundReceipt(DepositRefund $refund): Response
    {
        $refund->load(['booking.customer', 'payment', 'createdBy']);

        $customer = $refund->booking->customer;
        $booking = $refund->booking;

        $buyer = new Party([
            'name' => $customer->name,
            'phone' => $customer->phone,
        ]);

        $item = InvoiceItem::make('Deposit refund for booking '.$booking->booking_number)
            ->pricePerUnit((float) $refund->amount)
            ->quantity(1);

        $notes = collect([
            'Booking: '.$booking->booking_number,
            'Original deposit: '.$refund->payment->receipt_number,
            $refund->reason ? 'Reason: '.$refund->reason : null,
        ])->filter()->implode("\n");

        $invoice = Invoice::make('Refund Receipt')
            ->serialNumberFormat('{SERIES}')
            ->series($refund->refund_number)
            ->buyer($buyer)
            ->date($refund->refunded_at)
            ->addItem($item)
            ->notes($notes)
            ->filename($refund->refund_number);

        return $invoice->stream();
    }
}
