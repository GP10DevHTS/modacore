<?php

namespace App\Http\Controllers;

use App\Models\DepositRefund;
use App\Models\Payment;
use App\Services\ReceiptService;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    public function __construct(private readonly ReceiptService $receiptService) {}

    public function payment(Payment $payment): Response
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        return $this->receiptService->paymentReceipt($payment);
    }

    public function refund(DepositRefund $refund): Response
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        return $this->receiptService->refundReceipt($refund);
    }
}
