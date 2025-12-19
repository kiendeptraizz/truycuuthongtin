<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing CustomerService store with large profit amount\n";
echo "========================================================\n\n";

// Simulate request
$request = new Illuminate\Http\Request([
    'customer_id' => 527,
    'service_package_id' => 1,
    'login_email' => 'test@example.com',
    'login_password' => 'test123',
    'activated_at' => '2025-11-07',
    'expires_at' => '2026-11-07',
    'status' => 'active',
    'duration_days' => 365,
    'cost_price' => '100000',
    'price' => '200000',
    'profit_amount' => '2,000,000',  // With comma separator
    'profit_notes' => 'Test profit with large amount',
]);

echo "Original profit_amount: " . $request->profit_amount . "\n";

// Parse currency BEFORE validation
if ($request->filled('profit_amount')) {
    $parsedValue = parseCurrency($request->profit_amount);
    echo "Parsed profit_amount: " . $parsedValue . "\n";

    $request->merge([
        'profit_amount' => $parsedValue
    ]);

    echo "Merged profit_amount: " . $request->profit_amount . "\n";
}

// Now validate
$validator = Illuminate\Support\Facades\Validator::make($request->all(), [
    'customer_id' => 'required|exists:customers,id',
    'service_package_id' => 'required|exists:service_packages,id',
    'login_email' => 'required|email|max:255',
    'profit_amount' => 'nullable|numeric|min:0',
]);

echo "\nValidation result: ";
if ($validator->fails()) {
    echo "FAILED\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "PASSED ✓\n";
    echo "\nValue that would be saved: " . number_format($request->profit_amount, 0, ',', '.') . " VNĐ\n";
}

echo "\nDone!\n";
