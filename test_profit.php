<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test parseCurrency function
echo "Testing parseCurrency function:\n";
echo "================================\n\n";

$testCases = [
    '1000000',
    '1,000,000',
    '1.000.000',
    '2000000',
    '2,000,000',
    '2.000.000',
    '5000000',
    '10000000',
    '10,000,000',
    '10.000.000',
];

foreach ($testCases as $input) {
    $result = parseCurrency($input);
    echo sprintf("parseCurrency('%s') = %s\n", $input, number_format($result, 0, ',', '.'));
}

echo "\n\nTesting validation:\n";
echo "====================\n\n";

$validator = Illuminate\Support\Facades\Validator::make([
    'profit_amount' => '2,000,000',
], [
    'profit_amount' => 'nullable|numeric|min:0',
]);

if ($validator->fails()) {
    echo "Validation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation PASSED\n";
    echo "Value would be: " . parseCurrency('2,000,000') . "\n";
}

echo "\n\nDone!\n";
