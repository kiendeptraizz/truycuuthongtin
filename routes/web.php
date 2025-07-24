<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CustomerController;
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

    // Family Accounts Management
    Route::resource('family-accounts', \App\Http\Controllers\Admin\FamilyAccountController::class);
    Route::get('family-accounts/{familyAccount}/add-member', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'addMemberForm'])
        ->name('family-accounts.add-member-form');
    Route::post('family-accounts/{familyAccount}/add-member', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'addMember'])
        ->name('family-accounts.add-member');
    Route::delete('family-accounts/{familyAccount}/members/{member}', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'removeMember'])
        ->name('family-accounts.remove-member');
    Route::put('family-accounts/{familyAccount}/members/{member}', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'updateMember'])
        ->name('family-accounts.update-member');
    Route::get('family-accounts-report', [\App\Http\Controllers\Admin\FamilyAccountController::class, 'report'])
        ->name('family-accounts.report');

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
});
Route::get("/test-filter", function () {
    $request = request();
    $request->merge(["login_email" => "gaschburdab0@outlook.com"]);
    $controller = new \App\Http\Controllers\Admin\CustomerController();
    return $controller->index($request);
});
