<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\SupplierInvoice;
use App\Services\PurchaseOrderService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PaymentForm extends Component
{
    public SupplierInvoice $invoice;

    public string $amount = '';

    public string $paymentDate = '';

    public string $paymentMethod = 'bank_transfer';

    public string $externalReference = '';

    public string $notes = '';

    public function mount(SupplierInvoice $invoice): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $this->invoice = $invoice->load('purchaseOrder');
        $this->paymentDate = now()->format('Y-m-d');
        $this->amount = (string) $invoice->outstandingBalance();
    }

    #[Computed]
    public function paymentMethods(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'mobile_money' => 'Mobile Money',
            'cheque' => 'Cheque',
            'other' => 'Other',
        ];
    }

    public function save(PurchaseOrderService $service): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $this->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paymentDate' => ['required', 'date'],
            'paymentMethod' => ['required', 'string'],
            'externalReference' => ['nullable', 'string'],
        ]);

        $payment = $service->recordPayment(
            $this->invoice,
            (float) $this->amount,
            $this->paymentDate,
            $this->paymentMethod,
            $this->externalReference ?: null,
            $this->notes ?: null,
        );

        Flux::toast("Payment {$payment->payment_reference} recorded.");
        $this->redirectRoute('purchase-orders.show', $this->invoice->purchase_order_id);
    }

    public function render()
    {
        return view('livewire.purchase-orders.payment-form');
    }
}
