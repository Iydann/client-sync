<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax Rates Configuration
    |--------------------------------------------------------------------------
    |
    | Default tax rates (PPN and PPH) based on client type.
    | These values can be overridden per project during creation.
    |
    */

    'rates' => [
        'individual' => [
            'ppn' => 11.00,  // PPN 11%
            'pph' => 2.50,   // PPH 2.5%
        ],
        'corporate' => [
            'ppn' => 11.00,  // PPN 11%
            'pph' => 2.00,   // PPH 2%
        ],
        'government' => [
            'ppn' => 0.00,   // PPN exempt untuk pemerintah
            'pph' => 1.50,   // PPH 1.5%
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Descriptions
    |--------------------------------------------------------------------------
    */

    'descriptions' => [
        'ppn' => 'Value Added Tax (Pajak Pertambahan Nilai)',
        'pph' => 'Income Tax (Pajak Penghasilan)',
    ],
];
