<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode</title>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px;
            font-family: sans-serif;
            flex-direction: column;
        }

        .barcode-card {
            border: 1px solid #ddd;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            width: 300px;
        }

        .barcode-card img {
            max-height: 120px;
            object-fit: contain;
        }

        .barcode-card .code {
            margin-top: 8px;
            font-size: 14px;
            color: #555;
        }

        .barcode-card .label {
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* ✅ Hide anything marked no-print during printing */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

<!-- ✅ BACK LINK (ONLY ON SCREEN) -->
<div class="no-print" style="margin-bottom: 12px;">
    <a href="{{ route('inventory.show', $variationValue->item) }}"
       style="color:#2563eb;text-decoration:none;font-size:14px;">
        ← Back to Inventory
    </a>
</div>

<!-- BARCODE CARD -->
<div class="barcode-card">
    <div class="label">
        {{ $variationValue->label
            ? "{$variationValue->item->name} ({$variationValue->label})"
            : $variationValue->sku }}
    </div>

{{--    <img src="{{ $variationValue->getBarcodeImageUrl() }}"--}}
{{--         alt="{{ $variationValue->sku }}" />--}}
    {!! $variationValue->getBarcodeSvg() !!}
    <div class="code">
        {{ $variationValue->sku }}
    </div>
</div>

<!-- AUTO PRINT + CLOSE -->
<script>
    (function () {
        let printed = false;

        window.addEventListener("load", function () {
            printed = true;

            window.print();

            // fallback close
            setTimeout(() => {
                window.close();
            }, 1200);
        });

        window.addEventListener("afterprint", function () {
            window.close();
        });

        // extra safety fallback
        window.addEventListener("focus", function () {
            if (printed) {
                setTimeout(() => window.close(), 300);
            }
        });
    })();
</script>

</body>
</html>
