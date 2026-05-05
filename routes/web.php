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
use App\Http\Controllers\Auth\AuthController;



// ============================================================================
// 🪝 WEBHOOK ROUTES (no auth, no csrf)
// ============================================================================
Route::post('/api/webhook/pay2s', \App\Http\Controllers\Api\Pay2sWebhookController::class)
    ->name('webhook.pay2s');

// ============================================================================
// 🔐 AUTHENTICATION ROUTES
// ============================================================================

// Trang chủ — public lookup (khách tra cứu trực tiếp). Không redirect login.
// Login chỉ hiện khi vào /admin/* (middleware auth tự bật) hoặc trực tiếp /login.
Route::get('/', [LookupController::class, 'index'])->name('home');

// Login routes (truy cập trực tiếp /login hoặc qua /admin)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2FA challenge (sau login khi user có 2FA bật) — KHÔNG apply middleware two-factor
// vì bản thân các route này phục vụ verify, sẽ loop nếu enforced.
Route::middleware('auth')->group(function () {
    Route::get('/2fa/challenge', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('/2fa/verify', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'verify'])
        ->middleware('throttle:5,1')
        ->name('two-factor.verify');
});

// Các route post-2FA: yêu cầu auth + đã verify 2FA (nếu user có 2FA bật)
Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

    // 2FA setup/manage
    Route::get('/2fa/setup', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/2fa/enable', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'enable'])
        ->middleware('throttle:10,1')
        ->name('two-factor.enable');
    Route::get('/2fa/settings', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'settings'])->name('two-factor.settings');
    Route::post('/2fa/disable', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'disable'])->name('two-factor.disable');
});

// ============================================================================
// 🔍 PUBLIC ROUTES (Không cần đăng nhập)
// ============================================================================

// Trang tra cứu công khai cho khách hàng
Route::get('/tra-cuu', [LookupController::class, 'index'])->name('lookup.index');
Route::post('/tra-cuu/search', [LookupController::class, 'search'])
    ->middleware('throttle:10,1') // 10 requests/phút - chống spam
    ->name('lookup.search');

// ============================================================================
// ⚠️  CÁC ROUTE TEST/DEMO ĐÃ BỊ XÓA CHO PRODUCTION
// Nếu cần test, hãy uncomment và thêm middleware auth
// ============================================================================

// ============================================================================
// 🔒 ADMIN ROUTES (Yêu cầu đăng nhập)
// ============================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'two-factor', 'prevent.caching', \App\Http\Middleware\EnsureDailyBackup::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Cấu hình trang chủ (override stats hiển thị trên trang tra cứu công khai)
    Route::get('/home-settings', [\App\Http\Controllers\Admin\HomeSettingsController::class, 'edit'])->name('home-settings.edit');
    Route::put('/home-settings', [\App\Http\Controllers\Admin\HomeSettingsController::class, 'update'])->name('home-settings.update');

    // Quản lý khách hàng
    Route::resource('customers', CustomerController::class);
    Route::get('customers/check-code/{code}', [CustomerController::class, 'checkCustomerCode'])
        ->name('customers.check-code');
    Route::get('customers-search-api', [CustomerController::class, 'searchApi'])
        ->name('customers.search-api');
    Route::post('customers-quick-create', [CustomerController::class, 'quickCreate'])
        ->name('customers.quick-create');

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

    // Quản lý đơn pending (mã đơn nhanh từ Telegram bot)
    Route::prefix('pending-orders')->name('pending-orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PendingOrderController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\PendingOrderController::class, 'store'])->name('store');
        Route::get('/{pendingOrder}/fill', [\App\Http\Controllers\Admin\PendingOrderController::class, 'fillForm'])->name('fill-form');
        Route::post('/{pendingOrder}/fill', [\App\Http\Controllers\Admin\PendingOrderController::class, 'fill'])->name('fill');
        Route::post('/{pendingOrder}/mark-paid', [\App\Http\Controllers\Admin\PendingOrderController::class, 'markPaid'])->name('mark-paid');
        Route::delete('/{pendingOrder}', [\App\Http\Controllers\Admin\PendingOrderController::class, 'destroy'])->name('destroy');
        Route::get('/{pendingOrder}/qr', [\App\Http\Controllers\Admin\PendingOrderController::class, 'qr'])->name('qr');
    });

    // Quản lý dịch vụ khách hàng
    // Các route literal PHẢI đặt TRƯỚC resource route để tránh xung đột với {customerService}
    Route::delete('customer-services/bulk-delete', [CustomerServiceController::class, 'bulkDelete'])
        ->name('customer-services.bulk-delete');

    // ===== THÙNG RÁC =====
    Route::get('customer-services/{customerService}/audit', [CustomerServiceController::class, 'audit'])
        ->name('customer-services.audit');

    // Refund (tính + xác nhận hoàn tiền cho đơn lỗi)
    Route::get('customer-services/{customerService}/refund', [\App\Http\Controllers\Admin\RefundController::class, 'preview'])
        ->name('customer-services.refund');
    Route::post('customer-services/{customerService}/refund', [\App\Http\Controllers\Admin\RefundController::class, 'confirm'])
        ->name('customer-services.refund.confirm');

    // Bảo hành (đổi TK / gia hạn / ghi chú) — list lịch sử + form thêm mới
    Route::get('customer-services/{customerService}/warranty', [\App\Http\Controllers\Admin\WarrantyController::class, 'index'])
        ->name('customer-services.warranty');
    Route::post('customer-services/{customerService}/warranty', [\App\Http\Controllers\Admin\WarrantyController::class, 'store'])
        ->name('customer-services.warranty.store');
    Route::get('customer-services/trash', [CustomerServiceController::class, 'trash'])
        ->name('customer-services.trash');
    Route::post('customer-services/trash/{id}/restore', [CustomerServiceController::class, 'restore'])
        ->name('customer-services.trash.restore');
    Route::delete('customer-services/trash/{id}/force-delete', [CustomerServiceController::class, 'forceDelete'])
        ->name('customer-services.trash.force-delete');
    Route::post('customer-services/trash/bulk-restore', [CustomerServiceController::class, 'bulkRestore'])
        ->name('customer-services.trash.bulk-restore');
    Route::delete('customer-services/trash/bulk-force-delete', [CustomerServiceController::class, 'bulkForceDelete'])
        ->name('customer-services.trash.bulk-force-delete');
    Route::delete('customer-services/trash/empty', [CustomerServiceController::class, 'emptyTrash'])
        ->name('customer-services.trash.empty');

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

    // ========================================================================
    // 🗃️ DỊCH VỤ ĐÃ LƯU TRỮ (Archived Services)
    // ========================================================================
    Route::prefix('archived-services')->name('archived-services.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'index'])->name('index');
        Route::post('/{id}/restore', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'forceDelete'])->name('force-delete');
        Route::post('/bulk-restore', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'bulkRestore'])->name('bulk-restore');
        Route::delete('/bulk-force-delete', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'bulkForceDelete'])->name('bulk-force-delete');
        Route::post('/run-cleanup', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'runCleanup'])->name('run-cleanup');
        Route::get('/expired-stats', [\App\Http\Controllers\Admin\ArchivedServiceController::class, 'getExpiredStats'])->name('expired-stats');
    });



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

    // Account Conversion Routes (commented out - controller not found)
    // Route::get('account-conversion', [\App\Http\Controllers\Admin\AccountConversionController::class, 'index'])
    //     ->name('account-conversion.index');
    // Route::post('account-conversion/preview', [\App\Http\Controllers\Admin\AccountConversionController::class, 'preview'])
    //     ->name('account-conversion.preview');
    // Route::post('account-conversion/convert', [\App\Http\Controllers\Admin\AccountConversionController::class, 'convert'])
    //     ->name('account-conversion.convert');

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
