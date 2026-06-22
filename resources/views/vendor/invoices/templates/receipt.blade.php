<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->name }}</title>

    <style>
        @page {
            margin: 3mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .large {
            font-size: 14px;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 1px 0;
            word-break: break-word;
        }

        .label {
            width: 40%;
            font-weight: bold;
        }

        .amount {
            font-size: 13px;
            font-weight: bold;
        }

        .item-row td {
            padding: 2px 0;
        }

        img {
            max-width: 120px;
        }
    </style>
</head>
<body>

{{-- LOGO --}}
@if($invoice->logo)
    <div class="center">
        <img src="{{ $invoice->getLogo() }}" alt="logo">
    </div>
@endif

{{-- TITLE --}}
<div class="center">
    <div class="large">{{ strtoupper($invoice->name) }}</div>

    @if($invoice->status)
        <div class="bold">
            {{ strtoupper($invoice->status) }}
        </div>
    @endif
</div>

<div class="divider-bold"></div>

{{-- HEADER --}}
<table>
    <tr>
        <td class="label">Serial</td>
        <td>{{ $invoice->getSerialNumber() }}</td>
    </tr>

    <tr>
        <td class="label">Date</td>
        <td>{{ $invoice->getDate() }}</td>
    </tr>

    @if(method_exists($invoice,'getPayUntilDate') && $invoice->getPayUntilDate())
        <tr>
            <td class="label">Pay Until</td>
            <td>{{ $invoice->getPayUntilDate() }}</td>
        </tr>
    @endif
</table>

<div class="divider"></div>

{{-- SELLER --}}
<div class="bold center">SELLER</div>

@if($invoice->seller)

    @if($invoice->seller->name)
        <div><strong>{{ $invoice->seller->name }}</strong></div>
    @endif

    @if($invoice->seller->address)
        <div>Address: {{ $invoice->seller->address }}</div>
    @endif

    @if($invoice->seller->code)
        <div>Code: {{ $invoice->seller->code }}</div>
    @endif

    @if($invoice->seller->vat)
        <div>VAT: {{ $invoice->seller->vat }}</div>
    @endif

    @if($invoice->seller->phone)
        <div>Phone: {{ $invoice->seller->phone }}</div>
    @endif

    @foreach($invoice->seller->custom_fields as $key => $value)
        <div>{{ ucfirst($key) }}: {{ $value }}</div>
    @endforeach

@endif

<div class="divider"></div>

{{-- BUYER --}}
<div class="bold center">BUYER</div>

@if($invoice->buyer)

    @if($invoice->buyer->name)
        <div><strong>{{ $invoice->buyer->name }}</strong></div>
    @endif

    @if($invoice->buyer->address)
        <div>Address: {{ $invoice->buyer->address }}</div>
    @endif

    @if($invoice->buyer->code)
        <div>Code: {{ $invoice->buyer->code }}</div>
    @endif

    @if($invoice->buyer->vat)
        <div>VAT: {{ $invoice->buyer->vat }}</div>
    @endif

    @if($invoice->buyer->phone)
        <div>Phone: {{ $invoice->buyer->phone }}</div>
    @endif

    @foreach($invoice->buyer->custom_fields as $key => $value)
        <div>{{ ucfirst($key) }}: {{ $value }}</div>
    @endforeach

@endif

<div class="divider"></div>

{{-- ITEMS --}}
<div class="bold center">ITEMS</div>

@foreach($invoice->items as $item)

    <table class="item-row">
        <tr>
            <td colspan="2">
                <strong>{{ $item->title }}</strong>
            </td>
        </tr>

        @if($item->description)
            <tr>
                <td colspan="2">
                    {{ $item->description }}
                </td>
            </tr>
        @endif

        <tr>
            <td>Qty</td>
            <td class="right">{{ $item->quantity }}</td>
        </tr>

        @if($invoice->hasItemUnits)
            <tr>
                <td>Units</td>
                <td class="right">{{ $item->units }}</td>
            </tr>
        @endif

        <tr>
            <td>Unit Price</td>
            <td class="right">
                {{ $invoice->formatCurrency($item->price_per_unit) }}
            </td>
        </tr>

        @if($invoice->hasItemDiscount)
            <tr>
                <td>Discount</td>
                <td class="right">
                    {{ $invoice->formatCurrency($item->discount) }}
                </td>
            </tr>
        @endif

        @if($invoice->hasItemTax)
            <tr>
                <td>Tax</td>
                <td class="right">
                    {{ $invoice->formatCurrency($item->tax) }}
                </td>
            </tr>
        @endif

        <tr>
            <td><strong>Subtotal</strong></td>
            <td class="right">
                <strong>
                    {{ $invoice->formatCurrency($item->sub_total_price) }}
                </strong>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

@endforeach

{{-- SUMMARY --}}
<table>

    @if($invoice->hasItemOrInvoiceDiscount())
        <tr>
            <td>Total Discount</td>
            <td class="right">
                {{ $invoice->formatCurrency($invoice->total_discount) }}
            </td>
        </tr>
    @endif

    @if($invoice->taxable_amount)
        <tr>
            <td>Taxable Amount</td>
            <td class="right">
                {{ $invoice->formatCurrency($invoice->taxable_amount) }}
            </td>
        </tr>
    @endif

    @if($invoice->tax_rate)
        <tr>
            <td>Tax Rate</td>
            <td class="right">
                {{ $invoice->tax_rate }}%
            </td>
        </tr>
    @endif

    @if($invoice->hasItemOrInvoiceTax())
        <tr>
            <td>Total Taxes</td>
            <td class="right">
                {{ $invoice->formatCurrency($invoice->total_taxes) }}
            </td>
        </tr>
    @endif

    @if($invoice->shipping_amount)
        <tr>
            <td>Shipping</td>
            <td class="right">
                {{ $invoice->formatCurrency($invoice->shipping_amount) }}
            </td>
        </tr>
    @endif

</table>

<div class="divider-bold"></div>

<table>
    <tr>
        <td class="amount">TOTAL</td>
        <td class="right amount">
            {{ $invoice->formatCurrency($invoice->total_amount) }}
        </td>
    </tr>
</table>

<div class="divider-bold"></div>

{{-- NOTES --}}
@if($invoice->notes)
    <div class="bold">NOTES</div>
    <div>{!! nl2br($invoice->notes) !!}</div>

    <div class="divider"></div>
@endif

{{-- AMOUNT IN WORDS --}}
@if(method_exists($invoice, 'getTotalAmountInWords'))
    <div class="bold">AMOUNT IN WORDS</div>
    <div>{{ $invoice->getTotalAmountInWords() }}</div>

    <div class="divider"></div>
@endif

<div class="center">
    <strong>THANK YOU</strong>
</div>

</body>
</html>
