<?php

use LaravelDaily\Invoices\Classes\Seller;

return [
    'date' => [

        /*
         * Carbon date format
         */
        'format' => 'Y-m-d',

        /*
         * Due date for payment since invoice's date.
         */
        'pay_until_days' => 7,
    ],

    'serial_number' => [
        'series' => 'AA',
        'sequence' => 1,

        /*
         * Sequence will be padded accordingly, for ex. 00001
         */
        'sequence_padding' => 5,
        'delimiter' => '.',

        /*
         * Supported tags {SERIES}, {DELIMITER}, {SEQUENCE}
         * Example: AA.00001
         */
        'format' => '{SERIES}{DELIMITER}{SEQUENCE}',
    ],

    'currency' => [
        'code' => 'ugx',

        /*
         * UGX has no fractional unit in common use.
         */
        'fraction' => '',
        'symbol' => 'UGX',

        /*
         * UGX amounts are whole numbers.
         */
        'decimals' => 0,

        'decimal_point' => '.',

        /*
         * Example: 1,000,000
         */
        'thousands_separator' => ',',

        /*
         * Supported tags {VALUE}, {SYMBOL}, {CODE}
         * Example: UGX 1,000,000
         */
        'format' => '{SYMBOL} {VALUE}',
    ],

    'paper' => [
        // A4 = 210 mm x 297 mm = 595 pt x 842 pt
        'size' => 'a4',
        'orientation' => 'portrait',
    ],

    'disk' => 'local',

    'seller' => [
        /*
         * Class used in templates via $invoice->seller
         *
         * Must implement LaravelDaily\Invoices\Contracts\PartyContract
         *      or extend LaravelDaily\Invoices\Classes\Party
         */
        'class' => Seller::class,

        /*
         * Default attributes for Seller::class
         */
        'attributes' => [
            'name' => env('APP_NAME', 'ModaCore'),
            'address' => env('COMPANY_ADDRESS', ''),
            'phone' => env('COMPANY_PHONE', ''),
            'custom_fields' => [],
        ],
    ],

    'dompdf_options' => [
        'enable_php' => true,
        /**
         * Do not write log.html or make it optional
         *
         *  @see https://github.com/dompdf/dompdf/issues/2810
         */
        'logOutputFile' => '',
    ],
];
