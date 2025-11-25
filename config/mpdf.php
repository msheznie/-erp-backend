<?php

return [
    /*
    |--------------------------------------------------------------------------
    | mPDF Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for mPDF library including font settings.
    | For more information, see: https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html
    |
    */

    // Font data configuration
    // Font files should be placed in: vendor/mpdf/mpdf/ttfonts/
    'fontDir' => [
        base_path('vendor/mpdf/mpdf/ttfonts'),
        // Custom fonts directory (user uploaded fonts)
        public_path('fonts'),
    ],
    'fontdata' => [
        // Poppins font family (English)
        'poppins' => [
            'R' => 'Poppins-Regular.ttf',
            'B' => 'Poppins-Bold.ttf',
            'I' => 'Poppins-Italic.ttf',
            'BI' => 'Poppins-BoldItalic.ttf',
        ],

        // Noto Sans Arabic font family (Arabic)
        'notosansarabic' => [
            'R' => 'NotoSansArabic-Regular.ttf',
            'B' => 'NotoSansArabic-Bold.ttf',
            'I' => 'NotoSansArabic-Regular.ttf', // Use Regular if Italic not available
            'BI' => 'NotoSansArabic-Bold.ttf',   // Use Bold if BoldItalic not available
            'useKashida' => 75, // Enable Kashida (Tatweel) for Arabic text justification
        ],
    ],
];
