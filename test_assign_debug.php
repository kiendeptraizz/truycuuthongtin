<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing AssignService Logic\n";
echo "============================\n\n";

// Test các giá trị profit_amount khác nhau
$testValues = [
    '1000000',
    '1,000,000',
    '2.000.000',
    '5000000',
    '10,000,000',
];

foreach ($testValues as $value) {
    echo "Input: $value\n";
    $parsed = parseCurrency($value);
    echo "Parsed: $parsed\n";

    // Test validation
    $validator = Illuminate\Support\Facades\Validator::make([
        'profit_amount' => $parsed
    ], [
        'profit_amount' => 'nullable|numeric|min:0'
    ]);

    if ($validator->fails()) {
        echo "Validation: FAILED\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
    } else {
        echo "Validation: PASSED ✓\n";
    }

    echo "\n";
}

echo "Done!\n";
