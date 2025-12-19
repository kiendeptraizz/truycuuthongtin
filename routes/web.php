<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServicePackageController;
use App\Http\Controllers\Admin\CustomerServiceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ContentSchedulerController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\Admin\CollaboratorController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\ZaloAccountController;
use App\Http\Controllers\TargetGroupController;
use App\Http\Controllers\MessageCampaignController;
use App\Http\Controllers\ZaloDashboardController;
use App\Http\Controllers\Admin\ResourceController;



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

// Icon test route
Route::get('/icon-test', function () {
    return view('icon-test');
})->name('icon-test');

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

// Admin routes (Không cần đăng nhập)
Route::prefix('admin')->name('admin.')->middleware(['prevent.caching'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Icon demo page
    Route::get('icon-demo', function () {
        return view('admin.icon-demo');
    })->name('icon-demo');

    // Quản lý khách hàng
    Route::resource('customers', CustomerController::class);
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
    Route::get('customer-services-statistics', [CustomerServiceController::class, 'statistics'])
        ->name('customer-services.statistics');
    Route::delete('customer-services-expired/bulk-delete', [CustomerServiceController::class, 'deleteExpiredServices'])
        ->name('customer-services.delete-expired');

    // Route để gán dịch vụ cho khách hàng
    Route::get('customers/{customer}/assign-service', [CustomerServiceController::class, 'assignForm'])
        ->name('customers.assign-service');
    Route::post('customers/{customer}/assign-service', [CustomerServiceController::class, 'assignService'])
        ->name('customers.store-service');



    // Content Scheduler
    Route::resource('content-scheduler', ContentSchedulerController::class);
    Route::get('content-scheduler-calendar', [ContentSchedulerController::class, 'calendar'])->name('content-scheduler.calendar');
    Route::patch('content-scheduler/{content_scheduler}/mark-posted', [ContentSchedulerController::class, 'markAsPosted'])->name('content-scheduler.mark-posted');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::patch('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');



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

    // Shared Account Credentials Management
    Route::get('shared-accounts/credentials', [\App\Http\Controllers\Admin\SharedAccountController::class, 'credentials'])
        ->name('shared-accounts.credentials');
    Route::get('shared-accounts/credentials/create', [\App\Http\Controllers\Admin\SharedAccountController::class, 'createCredential'])
        ->name('shared-accounts.credentials.create');
    Route::post('shared-accounts/credentials', [\App\Http\Controllers\Admin\SharedAccountController::class, 'storeCredential'])
        ->name('shared-accounts.credentials.store');
    Route::post('shared-accounts/credentials/bulk-import', [\App\Http\Controllers\Admin\SharedAccountController::class, 'bulkImportCredentials'])
        ->name('shared-accounts.credentials.bulk-import');
    Route::get('shared-accounts/credentials/{credential}/edit', [\App\Http\Controllers\Admin\SharedAccountController::class, 'editCredential'])
        ->name('shared-accounts.credentials.edit');
    Route::put('shared-accounts/credentials/{credential}', [\App\Http\Controllers\Admin\SharedAccountController::class, 'updateCredential'])
        ->name('shared-accounts.credentials.update');
    Route::delete('shared-accounts/credentials/{credential}', [\App\Http\Controllers\Admin\SharedAccountController::class, 'destroyCredential'])
        ->name('shared-accounts.credentials.destroy');
    Route::get('shared-accounts/credentials/by-package/{packageId}', [\App\Http\Controllers\Admin\SharedAccountController::class, 'getCredentialsByPackage'])
        ->name('shared-accounts.credentials.by-package');

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
    Route::get('family-accounts/{familyAccount}/members/{member}/edit', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'editMemberForm'])
        ->name('family-accounts.edit-member-form');
    Route::delete('family-accounts/{familyAccount}/members/{member}', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'removeMember'])
        ->name('family-accounts.remove-member');
    Route::put('family-accounts/{familyAccount}/members/{member}', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'updateMember'])
        ->name('family-accounts.update-member');

    // Family Accounts Report
    Route::get('family-accounts-report', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'report'])
        ->name('family-accounts.report');

    // Revenue Statistics Routes (thay thế Profit Management)
    Route::prefix('revenue')->name('revenue.')->group(function () {
        Route::get('/', [RevenueController::class, 'index'])->name('index');
        Route::get('/data', [RevenueController::class, 'getRevenueData'])->name('data');
        Route::get('/service-stats', [RevenueController::class, 'getServiceStats'])->name('service-stats');
        Route::get('/customer-stats', [RevenueController::class, 'getCustomerStats'])->name('customer-stats');
        Route::get('/category-stats', [RevenueController::class, 'getCategoryStats'])->name('category-stats');
        Route::get('/performance-stats', [RevenueController::class, 'getPerformanceStats'])->name('performance-stats');
        Route::get('/hourly-stats', [RevenueController::class, 'getHourlyStats'])->name('hourly-stats');
        Route::get('/growth-stats', [RevenueController::class, 'getGrowthStats'])->name('growth-stats');
        Route::get('/forecast-stats', [RevenueController::class, 'getForecastStats'])->name('forecast-stats');
        Route::get('/export', [RevenueController::class, 'exportReport'])->name('export');
        Route::post('/update-profit', [RevenueController::class, 'updateProfit'])->name('update-profit');
        Route::delete('/delete-profit', [RevenueController::class, 'deleteProfit'])->name('delete-profit');
    });

    // Giữ lại Profit Management Routes cũ cho compatibility (deprecated)
    Route::prefix('profits')->name('profits.')->group(function () {
        Route::get('/', [ProfitController::class, 'index'])->name('index');
        Route::get('/today-orders', [ProfitController::class, 'getTodayOrders'])->name('today-orders');
        Route::get('/today-statistics', [ProfitController::class, 'getTodayStatistics'])->name('today-statistics');
        Route::post('/store', [ProfitController::class, 'storeProfit'])->name('store');
        Route::delete('/delete', [ProfitController::class, 'deleteProfit'])->name('delete');
    });

    // Test route for debugging
    Route::get('test-profits', function () {
        return view('test-profits');
    })->name('test-profits');

    // Direct test route
    Route::get('test-profits-direct', function () {
        return view('test-profits-direct');
    })->name('test-profits-direct');

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

    // ========================================================================
    // 📱 ZALO MARKETING MANAGEMENT
    // ========================================================================
    Route::prefix('zalo')->name('zalo.')->group(function () {
        // Dashboard
        Route::get('/', [ZaloDashboardController::class, 'index'])->name('dashboard');
        Route::get('/conversion-funnel', [ZaloDashboardController::class, 'conversionFunnel'])->name('conversion-funnel');

        // Zalo Accounts Management
        Route::resource('accounts', ZaloAccountController::class);
        Route::post('accounts/{account}/reset-counter', [ZaloAccountController::class, 'resetCounter'])
            ->name('accounts.reset-counter');

        // Target Groups Management
        Route::resource('groups', TargetGroupController::class);
        Route::get('groups/{group}/members', [TargetGroupController::class, 'members'])
            ->name('groups.members');

        // Message Campaigns Management
        Route::resource('campaigns', MessageCampaignController::class);
        Route::post('campaigns/{campaign}/update-stats', [MessageCampaignController::class, 'updateStats'])
            ->name('campaigns.update-stats');
        Route::get('campaigns/{campaign}/report', [MessageCampaignController::class, 'report'])
            ->name('campaigns.report');
    });

    // ========================================================================
    // 📦 QUẢN LÝ TÀI NGUYÊN (Resource Management)
    // ========================================================================
    Route::prefix('resources')->name('resources.')->group(function () {
        // Danh mục tài nguyên
        Route::get('/', [ResourceController::class, 'index'])->name('index');
        Route::get('/create', [ResourceController::class, 'create'])->name('create');
        Route::post('/', [ResourceController::class, 'store'])->name('store');
        Route::get('/{resource}', [ResourceController::class, 'show'])->name('show');
        Route::get('/{resource}/edit', [ResourceController::class, 'edit'])->name('edit');
        Route::put('/{resource}', [ResourceController::class, 'update'])->name('update');
        Route::delete('/{resource}', [ResourceController::class, 'destroy'])->name('destroy');

        // Cập nhật hàng loạt tài khoản hết hạn
        Route::post('/update-expired', [ResourceController::class, 'updateExpiredAccounts'])->name('update-expired');

        // Tài khoản trong danh mục
        Route::get('/{resource}/accounts/create', [ResourceController::class, 'createAccount'])->name('accounts.create');
        Route::post('/{resource}/accounts', [ResourceController::class, 'storeAccount'])->name('accounts.store');

        // Bulk import
        Route::get('/{resource}/accounts/bulk-import', [ResourceController::class, 'bulkImportForm'])->name('accounts.bulk-import-form');
        Route::post('/{resource}/accounts/bulk-import', [ResourceController::class, 'bulkImport'])->name('accounts.bulk-import');
        Route::get('/{resource}/accounts/{account}/edit', [ResourceController::class, 'editAccount'])->name('accounts.edit');
        Route::put('/{resource}/accounts/{account}', [ResourceController::class, 'updateAccount'])->name('accounts.update');
        Route::delete('/{resource}/accounts/{account}', [ResourceController::class, 'destroyAccount'])->name('accounts.destroy');
        Route::post('/{resource}/accounts/{account}/toggle', [ResourceController::class, 'toggleAvailable'])->name('accounts.toggle');
        Route::post('/{resource}/accounts/{account}/mark-sold', [ResourceController::class, 'markAsSold'])->name('accounts.mark-sold');

        // Bulk actions
        Route::post('/{resource}/accounts/bulk-delete', [ResourceController::class, 'bulkDeleteAccounts'])->name('accounts.bulk-delete');
        Route::post('/{resource}/accounts/bulk-update', [ResourceController::class, 'bulkUpdateAccounts'])->name('accounts.bulk-update');

        // Subcategories (danh mục con)
        Route::get('/{resource}/subcategories', [ResourceController::class, 'getSubcategories'])->name('subcategories.list');
        Route::post('/{resource}/subcategories', [ResourceController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::put('/{resource}/subcategories/{subcategory}', [ResourceController::class, 'updateSubcategory'])->name('subcategories.update');
        Route::delete('/{resource}/subcategories/{subcategory}', [ResourceController::class, 'destroySubcategory'])->name('subcategories.destroy');
    });
});
Route::get("/test-filter", function () {
    $request = request();
    $request->merge(["login_email" => "gaschburdab0@outlook.com"]);
    $controller = new \App\Http\Controllers\Admin\CustomerController();
    return $controller->index($request);
});
