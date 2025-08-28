<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServicePackageController;
use App\Http\Controllers\Admin\CustomerServiceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ContentSchedulerController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PotentialSupplierController;
use App\Http\Controllers\Admin\CollaboratorController;



// Trang chủ - chuyển hướng đến admin dashboard trực tiếp
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Trang tra cứu công khai
Route::get('/tra-cuu', [LookupController::class, 'index'])->name('lookup.index');
Route::post('/tra-cuu/search', [LookupController::class, 'search'])->name('lookup.search');

// Demo routes (for testing components)
Route::get('/demo/customer-search', function () {
    return view('demo.customer-search-test');
})->name('demo.customer-search');

Route::get('/demo/search-spacebar-test', function () {
    return view('demo.search-spacebar-test');
})->name('demo.search-spacebar-test');

// Test route để tạo dữ liệu tra cứu
Route::get('/test/create-lookup-data', function () {
    $customer = \App\Models\Customer::where('customer_code', 'LOOKUP001')->first();
    if (!$customer) {
        $customer = \App\Models\Customer::create([
            'name' => 'Khách hàng test tra cứu',
            'email' => 'test@tracuu.com',
            'phone' => '0123456789',
            'customer_code' => 'LOOKUP001',
            'status' => 'active'
        ]);
    }

    $package = \App\Models\ServicePackage::first();
    if ($package) {
        \App\Models\CustomerService::create([
            'customer_id' => $customer->id,
            'service_package_id' => $package->id,
            'login_email' => 'test@tracuu.com',
            'login_password' => 'password123',
            'status' => 'active',
            'activated_at' => now(),
            'expires_at' => now()->addMonths(1)
        ]);
    }

    return 'Created test customer with code: LOOKUP001';
});

// Admin auth routes (không cần middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin routes (cần đăng nhập)
Route::prefix('admin')->name('admin.')->middleware(['admin.auth', 'prevent.caching'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý khách hàng
    Route::resource('customers', CustomerController::class);

    // Test route
    Route::get('test-quick-add', function () {
        return view('test-quick-add');
    })->name('test.quick.add');
    Route::get('customers/check-code/{code}', [CustomerController::class, 'checkCustomerCode'])
        ->name('customers.check-code');

    // ========================================================================
    // 🛡️ QUẢN LÝ BACKUP
    // ========================================================================
    Route::prefix('backup-management')->name('backup.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::get('/list', [App\Http\Controllers\Admin\BackupController::class, 'list'])->name('list');
        Route::post('/create', [App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        Route::delete('/delete/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'delete'])->name('delete');
        Route::post('/restore', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
        Route::get('/report', [App\Http\Controllers\Admin\BackupController::class, 'report'])->name('report');
        Route::get('/history', [App\Http\Controllers\Admin\BackupController::class, 'history'])->name('history');
        Route::get('/settings', [App\Http\Controllers\Admin\BackupController::class, 'settings'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\Admin\BackupController::class, 'updateSettings'])->name('settings.update');
        Route::get('/status', [App\Http\Controllers\Admin\BackupController::class, 'status'])->name('status');
    });

    // Quản lý danh mục dịch vụ
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::get('service-categories-api', [ServiceCategoryController::class, 'getCategories'])
        ->name('service-categories.api');

    // Quản lý gói dịch vụ
    Route::resource('service-packages', ServicePackageController::class);
    Route::patch('service-packages/{servicePackage}/toggle-status', [ServicePackageController::class, 'toggleStatus'])
        ->name('service-packages.toggle-status');

    // Quản lý dịch vụ khách hàng
    Route::resource('customer-services', CustomerServiceController::class);

    // Route nhắc nhở khách hàng
    Route::post('customer-services/{customerService}/mark-reminded', [CustomerServiceController::class, 'markReminded'])
        ->name('customer-services.mark-reminded');
    Route::post('customer-services/{customerService}/reset-reminder', [CustomerServiceController::class, 'resetReminder'])
        ->name('customer-services.reset-reminder');
    Route::get('customer-services-reminder-report', [CustomerServiceController::class, 'reminderReport'])
        ->name('customer-services.reminder-report');
    Route::get('customer-services-daily-report', [CustomerServiceController::class, 'dailyReport'])
        ->name('customer-services.daily-report');

    // Route để gán dịch vụ cho khách hàng
    Route::get('customers/{customer}/assign-service', [CustomerServiceController::class, 'assignForm'])
        ->name('customers.assign-service');
    Route::post('customers/{customer}/assign-service', [CustomerServiceController::class, 'assignService'])
        ->name('customers.store-service');

    // Quản lý Lead (khách hàng tiềm năng)
    Route::resource('leads', LeadController::class);
    Route::post('leads/{lead}/activity', [LeadController::class, 'addActivity'])->name('leads.add-activity');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::patch('leads/{lead}/mark-lost', [LeadController::class, 'markAsLost'])->name('leads.mark-lost');
    Route::post('leads/bulk-action', [LeadController::class, 'bulkAction'])->name('leads.bulk-action');

    // Báo cáo
    Route::get('reports/profit', [ReportController::class, 'profit'])->name('reports.profit');

    // Content Scheduler
    Route::resource('content-scheduler', ContentSchedulerController::class);
    Route::get('content-scheduler-calendar', [ContentSchedulerController::class, 'calendar'])->name('content-scheduler.calendar');
    Route::patch('content-scheduler/{content_scheduler}/mark-posted', [ContentSchedulerController::class, 'markAsPosted'])->name('content-scheduler.mark-posted');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::patch('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');

    // Suppliers routes
    Route::delete('suppliers/bulk-delete', [SupplierController::class, 'bulkDelete'])->name('suppliers.bulk-delete');
    Route::get('suppliers/combined', [SupplierController::class, 'combinedIndex'])->name('suppliers.combined');
    Route::get('suppliers/original', [SupplierController::class, 'originalIndex'])->name('suppliers.original');
    Route::get('suppliers/statistics', [SupplierController::class, 'statistics'])->name('suppliers.statistics');
    Route::get('suppliers/api/current', [SupplierController::class, 'apiCurrentSuppliers'])->name('suppliers.api.current');
    Route::resource('suppliers', SupplierController::class);

    // Potential Suppliers routes
    Route::delete('potential-suppliers/bulk-delete', [PotentialSupplierController::class, 'bulkDelete'])->name('potential-suppliers.bulk-delete');
    Route::post('potential-suppliers/{potentialSupplier}/convert', [PotentialSupplierController::class, 'convertToSupplier'])->name('potential-suppliers.convert');
    Route::get('potential-suppliers/api/list', [PotentialSupplierController::class, 'apiPotentialSuppliers'])->name('potential-suppliers.api.list');
    Route::resource('potential-suppliers', PotentialSupplierController::class);

    // Collaborators routes
    Route::resource('collaborators', CollaboratorController::class);

    // Collaborator Service Account routes
    Route::post('collaborators/{collaborator}/services/{service}/accounts', [CollaboratorController::class, 'storeAccount'])
        ->name('collaborators.services.accounts.store');

    // Shared Accounts routes
    Route::get('shared-accounts', [\App\Http\Controllers\Admin\SharedAccountController::class, 'index'])
        ->name('shared-accounts.index');
    Route::get('shared-accounts/report', [\App\Http\Controllers\Admin\SharedAccountController::class, 'report'])
        ->name('shared-accounts.report');
    Route::get('shared-accounts/{email}/edit', [\App\Http\Controllers\Admin\SharedAccountController::class, 'edit'])
        ->name('shared-accounts.edit');
    Route::put('shared-accounts/{email}', [\App\Http\Controllers\Admin\SharedAccountController::class, 'update'])
        ->name('shared-accounts.update');
    Route::get('shared-accounts/{email}', [\App\Http\Controllers\Admin\SharedAccountController::class, 'show'])
        ->name('shared-accounts.show');
    Route::get('shared-accounts/{email}/logout-form', [\App\Http\Controllers\Admin\SharedAccountController::class, 'showLogoutForm'])
        ->name('shared-accounts.logout-form');
    Route::post('shared-accounts/{email}/logout', [\App\Http\Controllers\Admin\SharedAccountController::class, 'logoutAllDevices'])
        ->name('shared-accounts.logout');
    Route::get('shared-accounts/{email}/logout-logs', [\App\Http\Controllers\Admin\SharedAccountController::class, 'getLogoutLogs'])
        ->name('shared-accounts.logout-logs');

    // Family Accounts Management (Refactored)
    Route::resource('family-accounts', \App\Http\Controllers\Admin\FamilyAccountController::class);

    // Family Account Member Management Routes
    Route::get('family-accounts/{familyAccount}/add-member', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'addMemberForm'])
        ->name('family-accounts.add-member-form');
    Route::post('family-accounts/{familyAccount}/add-member', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'addMember'])
        ->name('family-accounts.add-member');
    Route::delete('family-accounts/{familyAccount}/members/{member}', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'removeMember'])
        ->name('family-accounts.remove-member');
    Route::put('family-accounts/{familyAccount}/members/{member}', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'updateMember'])
        ->name('family-accounts.update-member');

    // Family Accounts Report
    Route::get('family-accounts-report', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'report'])
        ->name('family-accounts.report');

    // Test route for debugging
    Route::post('test-update-member', function (\Illuminate\Http\Request $request) {
        Log::info('Test update member request', $request->all());
        return response()->json(['success' => true, 'message' => 'Test successful']);
    })->name('test.update-member');

    // Test route with model binding
    Route::put('test-family-accounts/{familyAccount}/members/{member}', function (\Illuminate\Http\Request $request, \App\Models\FamilyAccount $familyAccount, \App\Models\FamilyMember $member) {
        Log::info('Test model binding', [
            'family_account_id' => $familyAccount->id,
            'member_id' => $member->id,
            'request_data' => $request->all()
        ]);
        return response()->json(['success' => true, 'message' => 'Model binding test successful']);
    })->name('test.family-member-update');
    Route::get('family-accounts-report', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'report'])
        ->name('family-accounts.report');

    // Account Conversion Routes (commented out - controller not found)
    // Route::get('account-conversion', [\App\Http\Controllers\Admin\AccountConversionController::class, 'index'])
    //     ->name('account-conversion.index');
    // Route::post('account-conversion/preview', [\App\Http\Controllers\Admin\AccountConversionController::class, 'preview'])
    //     ->name('account-conversion.preview');
    // Route::post('account-conversion/convert', [\App\Http\Controllers\Admin\AccountConversionController::class, 'convert'])
    //     ->name('account-conversion.convert');

    // Quick conversion route for direct execution
    Route::get('quick-convert', function () {
        $emailsToConvert = [
            '64jxcb2c@taikhoanvip.io.vn',
            'gaschburdab0@outlook.com',
            'leehoangtung435@gmail.com',
            'Tanyaweatherfordpqxuw3hmn@hotmail.com',
            '5fsyvtx1@taikhoanvip.io.vn',
            'kiennezz18@gmail.com',
            'dtkien18@gmail.com',
            'kien83667@gmail.com',
            'hainguyenthi2110@gmail.com',
            'ohlhua1276@gmail.com',
            'nguyenendummn789@gmail.com',
        ];

        $executeConversion = request()->get('execute', false);

        $output = "🔄 SCRIPT TỰ ĐỘNG CHUYỂN ĐỔI TÀI KHOẢN\n";
        $output .= str_repeat('=', 50) . "\n";
        $output .= "Chế độ: " . ($executeConversion ? "THỰC HIỆN" : "KIỂM TRA") . "\n\n";

        $totalStats = [
            'services_found' => 0,
            'services_converted' => 0,
            'services_already_shared' => 0,
            'services_no_mapping' => 0,
            'errors' => 0
        ];

        foreach ($emailsToConvert as $index => $email) {
            $output .= "📧 [" . ($index + 1) . "/" . count($emailsToConvert) . "] {$email}\n";

            try {
                $services = \App\Models\CustomerService::with(['servicePackage.category', 'customer'])
                    ->where('login_email', $email)
                    ->get();

                if ($services->isEmpty()) {
                    $output .= "  ⚠️  Không tìm thấy dịch vụ nào\n";
                    continue;
                }

                $output .= "  📊 Tìm thấy {$services->count()} dịch vụ\n";
                $totalStats['services_found'] += $services->count();

                foreach ($services as $service) {
                    $currentAccountType = $service->servicePackage->account_type;
                    $currentPackageName = $service->servicePackage->name;
                    $customerName = $service->customer->name;

                    if ($currentAccountType === 'Tài khoản dùng chung') {
                        $output .= "    ✓ {$customerName} - {$currentPackageName} (đã là tài khoản dùng chung)\n";
                        $totalStats['services_already_shared']++;
                        continue;
                    }

                    // Tìm gói tương ứng
                    $targetPackage = \App\Models\ServicePackage::where('name', $currentPackageName)
                        ->where('account_type', 'Tài khoản dùng chung')
                        ->where('is_active', true)
                        ->first();

                    if (!$targetPackage) {
                        // Thử tìm gói tương tự
                        $baseName = trim(str_ireplace(['Plus', 'Pro', 'Premium', 'Advanced', 'Basic', '(Add Mail)', 'Add Mail'], '', $currentPackageName));
                        $targetPackage = \App\Models\ServicePackage::where('account_type', 'Tài khoản dùng chung')
                            ->where('is_active', true)
                            ->where('name', 'like', "%{$baseName}%")
                            ->first();
                    }

                    if ($targetPackage) {
                        if ($executeConversion) {
                            // Thực hiện chuyển đổi
                            $service->update([
                                'service_package_id' => $targetPackage->id,
                                'internal_notes' => ($service->internal_notes ?? '') .
                                    "\n[" . now()->format('d/m/Y H:i') . "] AUTO CONVERT: Chuyển đổi từ '{$currentPackageName}' ({$currentAccountType}) sang '{$targetPackage->name}' (Tài khoản dùng chung)"
                            ]);
                            $output .= "    ✅ {$customerName} - {$currentPackageName} → {$targetPackage->name} (ĐÃ CHUYỂN ĐỔI)\n";
                        } else {
                            $output .= "    🔄 {$customerName} - {$currentPackageName} → {$targetPackage->name} (SẼ CHUYỂN ĐỔI)\n";
                        }
                        $totalStats['services_converted']++;
                    } else {
                        $output .= "    ⚠️  {$customerName} - {$currentPackageName} (không tìm thấy gói tương ứng)\n";
                        $totalStats['services_no_mapping']++;
                    }
                }
            } catch (Exception $e) {
                $output .= "  ❌ Lỗi: " . $e->getMessage() . "\n";
                $totalStats['errors']++;
            }

            $output .= "\n";
        }

        $output .= str_repeat('=', 50) . "\n";
        $output .= "📊 TỔNG KẾT:\n";
        $output .= "- Tổng dịch vụ: {$totalStats['services_found']}\n";
        $output .= "- " . ($executeConversion ? "Đã chuyển đổi" : "Sẽ chuyển đổi") . ": {$totalStats['services_converted']}\n";
        $output .= "- Đã là dùng chung: {$totalStats['services_already_shared']}\n";
        $output .= "- Không thể chuyển: {$totalStats['services_no_mapping']}\n";
        $output .= "- Lỗi: {$totalStats['errors']}\n";
        $output .= str_repeat('=', 50) . "\n";

        if ($executeConversion) {
            $output .= "✅ HOÀN THÀNH CHUYỂN ĐỔI!\n";
        } else {
            $output .= "✅ HOÀN THÀNH KIỂM TRA!\n";
            $output .= "💡 Thêm ?execute=1 để thực hiện chuyển đổi thật.\n";
        }

        return response($output)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Demo pages
    Route::get('/demo/service-package-selector', function () {
        $servicePackages = \App\Models\ServicePackage::with('category')->active()->get();

        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        return view('admin.demo.service-package-selector', compact('servicePackages', 'accountTypePriority'));
    })->name('demo.service-package-selector');

    // Demo page for new grid selector
    Route::get('/demo/service-package-grid', function () {
        $servicePackages = \App\Models\ServicePackage::with('category')->active()->get();

        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        return view('admin.demo.service-package-grid-demo', compact('servicePackages', 'accountTypePriority'));
    })->name('demo.service-package-grid');

    // Demo page for category selector
    Route::get('/demo/service-package-category', function () {
        $servicePackages = \App\Models\ServicePackage::with('category')->active()->get();

        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4
        ];

        return view('admin.demo.service-package-category-demo', compact('servicePackages', 'accountTypePriority'));
    })->name('demo.service-package-category');

    // Simple test page for category selector
    Route::get('/test-category-selector', function () {
        $servicePackages = \App\Models\ServicePackage::with('category')->active()->get();
        return view('test-category-selector', compact('servicePackages'));
    });

    // Simple test page for category selector (inline)
    Route::get('/simple-category-test', function () {
        $servicePackages = \App\Models\ServicePackage::with('category')->active()->get();
        return view('simple-category-test', compact('servicePackages'));
    });

    // Comparison page for all selectors
    Route::get('/selector-comparison', function () {
        $servicePackages = \App\Models\ServicePackage::with('category')->active()->get();
        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4
        ];
        return view('selector-comparison', compact('servicePackages', 'accountTypePriority'));
    });
});
Route::get("/test-filter", function () {
    $request = request();
    $request->merge(["login_email" => "gaschburdab0@outlook.com"]);
    $controller = new \App\Http\Controllers\Admin\CustomerController();
    return $controller->index($request);
});
