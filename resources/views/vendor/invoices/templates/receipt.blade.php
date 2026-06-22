<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->name }}</title>

    <style>
        @page {
            margin: 3mm;
        }

        body {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .large {
            font-size: 14px;
            font-weight: bold;
        }

        .amount {
            font-size: 13px;
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .divider-bold {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        img {
            max-width: 120px;
            margin-bottom: 5px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 3px;
        }

        .line {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

{{-- Logo --}}
@if($invoice->logo)
    <div class="center">
        <img src="{{ $invoice->getLogo() }}" alt="logo">
    </div>
@endif

@if($invoice->seller)

    @if($invoice->seller->name)
        <div class="large" style="font-size: 20px;">
            <strong>
            {{ $invoice->seller->name }}
            </strong>
        </div>
    @endif

    @if($invoice->seller->address)
        <div class="line">
            <strong>
            {{ $invoice->seller->address }}
            </strong>
        </div>
    @endif

    @if($invoice->seller->code)
        <div class="line">
            {{ $invoice->seller->code }}
        </div>
    @endif

    @if($invoice->seller->vat)
        <div class="line">
            <strong>VAT:</strong>
            {{ $invoice->seller->vat }}
        </div>
    @endif

    @if($invoice->seller->phone)
        <div class="line">
            <strong>
            {{ $invoice->seller->phone }}
            </strong>
        </div>
    @endif

    @foreach($invoice->seller->custom_fields as $key => $value)
        <div class="line">
            <strong>{{ ucfirst($key) }}:</strong>
            {{ $value }}
        </div>
    @endforeach

@endif

<div class="divider-bold"></div>
{{-- Title --}}
<div >
    <div class="large">{{ strtoupper($invoice->name) }}</div>

    @if($invoice->status)
        <div class="bold">
            {{ strtoupper($invoice->status) }}
        </div>
    @endif
</div>

<div class="divider-bold"></div>

{{-- Header --}}
<div class="line">
    <strong>Serial:</strong>
    {{ $invoice->getSerialNumber() }}
</div>

<div class="line">
    <strong>Date:</strong>
    {{ $invoice->getDate() }}
</div>

@if(method_exists($invoice,'getPayUntilDate') && $invoice->getPayUntilDate())
    <div class="line">
        <strong>Return By:</strong>
        {{ $invoice->getPayUntilDate() }}
    </div>
@endif

<div class="divider"></div>

{{-- Seller --}}
{{--<div class="section-title">SELLER</div>--}}



<div class="divider"></div>

{{-- Buyer --}}
<div class="section-title">CUSTOMER</div>

@if($invoice->buyer)

    @if($invoice->buyer->name)
        <div class="line">
            <strong>Name:</strong>
            {{ $invoice->buyer->name }}
        </div>
    @endif

    @if($invoice->buyer->address)
        <div class="line">
            <strong>Address:</strong>
            {{ $invoice->buyer->address }}
        </div>
    @endif

    @if($invoice->buyer->code)
        <div class="line">
            <strong>Code:</strong>
            {{ $invoice->buyer->code }}
        </div>
    @endif

    @if($invoice->buyer->vat)
        <div class="line">
            <strong>VAT:</strong>
            {{ $invoice->buyer->vat }}
        </div>
    @endif

    @if($invoice->buyer->phone)
        <div class="line">
            <strong>Phone:</strong>
            {{ $invoice->buyer->phone }}
        </div>
    @endif

    @foreach($invoice->buyer->custom_fields as $key => $value)
        <div class="line">
            <strong>{{ ucfirst($key) }}:</strong>
            {{ $value }}
        </div>
    @endforeach

@endif

<div class="divider"></div>

{{-- Items --}}
<div class="section-title">ITEMS</div>

@foreach($invoice->items as $item)

    <div class="line">
        <strong>{{ $item->title }}</strong>
    </div>

    @if($item->description)
        <div class="line">
            {{ $item->description }}
        </div>
    @endif

    <div class="line">
        <strong>Quantity:</strong>
        {{ $item->quantity }}
    </div>

    @if($invoice->hasItemUnits)
        <div class="line">
            <strong>Units:</strong>
            {{ $item->units }}
        </div>
    @endif

    <div class="line">
        <strong>Hire Price:</strong>
        {{ $invoice->formatCurrency($item->price_per_unit) }}
    </div>

    @if($invoice->hasItemDiscount)
        <div class="line">
            <strong>Discount:</strong>
            {{ $invoice->formatCurrency($item->discount) }}
        </div>
    @endif

    @if($invoice->hasItemTax)
        <div class="line">
            <strong>Tax:</strong>
            {{ $invoice->formatCurrency($item->tax) }}
        </div>
    @endif

    <div class="line">
        <strong>Subtotal:</strong>
        {{ $invoice->formatCurrency($item->sub_total_price) }}
    </div>

    <div class="divider"></div>

@endforeach

{{-- Summary --}}
<div class="section-title">SUMMARY</div>

@if($invoice->hasItemOrInvoiceDiscount())
    <div class="line">
        <strong>Total Discount:</strong>
        {{ $invoice->formatCurrency($invoice->total_discount) }}
    </div>
@endif

@if($invoice->taxable_amount)
    <div class="line">
        <strong>Taxable Amount:</strong>
        {{ $invoice->formatCurrency($invoice->taxable_amount) }}
    </div>
@endif

@if($invoice->tax_rate)
    <div class="line">
        <strong>Tax Rate:</strong>
        {{ $invoice->tax_rate }}%
    </div>
@endif

@if($invoice->hasItemOrInvoiceTax())
    <div class="line">
        <strong>Total Taxes:</strong>
        {{ $invoice->formatCurrency($invoice->total_taxes) }}
    </div>
@endif

@if($invoice->shipping_amount)
    <div class="line">
        <strong>Shipping:</strong>
        {{ $invoice->formatCurrency($invoice->shipping_amount) }}
    </div>
@endif

<div class="divider-bold"></div>

<div class="amount">
    TOTAL:
    {{ $invoice->formatCurrency($invoice->total_amount) }}
</div>

<div class="divider-bold"></div>

{{-- Notes --}}
@if($invoice->notes)
    <div class="section-title">NOTES</div>

    <div>
        {!! nl2br($invoice->notes) !!}
    </div>

    <div class="divider"></div>
@endif

{{-- Amount In Words --}}
@if(method_exists($invoice, 'getTotalAmountInWords'))
    <div class="section-title">AMOUNT IN WORDS</div>

    <div>
        {{ $invoice->getTotalAmountInWords() }}
    </div>

    <div class="divider"></div>
@endif

<div>
    <strong>THANK YOU FOR CHOOSING US!</strong>
</div>

</body>
</html>
