<?php

use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceCommandController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\NetworkSettingController;
use App\Http\Controllers\Admin\ZktecoUserController;
use App\Http\Controllers\Adms\DeviceCmdController;
use App\Http\Controllers\Adms\GetRequestController;
use App\Http\Controllers\Adms\RegistryController;
use App\Http\Controllers\Adms\TenantCDataController;
use App\Http\Controllers\Api\MockRemoteServerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BusinessAdmin\PackagePlanController;
use App\Http\Controllers\BusinessAdmin\SubscriberController;
use App\Http\Controllers\Demo\DemoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Subscriber\AttendanceController as SubscriberAttendanceController;
use App\Http\Controllers\Subscriber\DashboardController as SubscriberDashboardController;
use App\Http\Controllers\Subscriber\DeviceController as SubscriberDeviceController;
use App\Http\Controllers\Subscriber\SubscriptionCheckoutController;
use App\Http\Controllers\Subscriber\UserController as SubscriberUserController;
use App\Http\Controllers\Subscriber\WebhookPushController;
use App\Http\Controllers\SystemAdmin\DashboardController as SystemAdminDashboardController;
use App\Http\Controllers\SystemAdmin\DatabaseManagerController;
use App\Http\Controllers\SystemAdmin\GatewayConfigController;
use App\Http\Controllers\SystemAdmin\NetworkManagerController;
use App\Http\Controllers\SystemAdmin\RolePermissionController;
use App\Http\Controllers\SystemAdmin\SaasUserController;
use App\Http\Controllers\SystemAdmin\SecurityAuditController;
use App\Http\Controllers\SystemAdmin\SystemMonitoringController;
use App\Http\Controllers\SystemAdmin\WebsiteManagerController;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Controllers\Subscriber\Hris;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Home Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('/two-factor/send-otp', [TwoFactorController::class, 'resendOtp'])->name('two-factor.send-otp');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
});

/*
|--------------------------------------------------------------------------
| ZKTeco ADMS Device Communication Endpoints (Public for Physical Machines)
|--------------------------------------------------------------------------
*/
Route::any('/iclock/{token}/cdata', TenantCDataController::class)->name('adms.token.cdata');
Route::get('/iclock/{token}/getrequest', GetRequestController::class)->name('adms.token.getrequest');
Route::post('/iclock/{token}/devicecmd', DeviceCmdController::class)->name('adms.token.devicecmd');
Route::any('/iclock/{token}/registry', RegistryController::class)->name('adms.token.registry');

Route::any('/iclock/cdata', TenantCDataController::class)->name('adms.cdata');
Route::get('/iclock/getrequest', GetRequestController::class)->name('adms.getrequest');
Route::post('/iclock/devicecmd', DeviceCmdController::class)->name('adms.devicecmd');
Route::any('/iclock/registry', RegistryController::class)->name('adms.registry');

/*
|--------------------------------------------------------------------------
| Mock External Remote Server Endpoints (For Testing Data Push Webhooks)
|--------------------------------------------------------------------------
*/
Route::post('/api/mock-remote-server/no-auth', [MockRemoteServerController::class, 'receiveNoAuth']);
Route::post('/api/mock-remote-server/bearer', [MockRemoteServerController::class, 'receiveBearer']);
Route::post('/api/mock-remote-server/api-key', [MockRemoteServerController::class, 'receiveApiKey']);
Route::post('/api/mock-remote-server/basic', [MockRemoteServerController::class, 'receiveBasic']);

/*
|--------------------------------------------------------------------------
| Public Demo Sandbox Routes (/demo)
|--------------------------------------------------------------------------
*/
Route::get('/demo', [DemoController::class, 'index'])->name('demo.dashboard');
Route::post('/demo/destroy', [DemoController::class, 'destroyDemoSession'])->name('demo.destroy');

/*
|--------------------------------------------------------------------------
| SSLCommerz Callbacks
|--------------------------------------------------------------------------
*/
Route::prefix('subscription/ssl')->name('subscription.ssl.')->group(function () {
    Route::get('/checkout', [SubscriptionCheckoutController::class, 'mockCheckout'])->name('mock_checkout');
    Route::post('/success', [SubscriptionCheckoutController::class, 'success'])->name('success');
    Route::post('/fail', [SubscriptionCheckoutController::class, 'fail'])->name('fail');
    Route::post('/cancel', [SubscriptionCheckoutController::class, 'cancel'])->name('cancel');
    Route::post('/ipn', [SubscriptionCheckoutController::class, 'success'])->name('ipn');
});

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes (Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'two-factor'])->group(function () {

    // Role-based dashboard redirect
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->hasRole('System Admin')) return redirect()->route('admin.system.dashboard');
        if ($user->hasRole('Business Admin')) return redirect()->route('admin.business.dashboard');
        if ($user->hasRole('Subscriber')) return redirect()->route('subscriber.dashboard');
        if ($user->hasRole('Demo User')) return redirect()->route('demo.dashboard');
        return redirect('/');
    })->name('dashboard');

    // Dedicated Subscriber Panel Routes (/subscriber/*)
    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', [SubscriberDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [SubscriberDashboardController::class, 'index']);
        Route::get('/hr-dashboard', [SubscriberDashboardController::class, 'hrDashboard'])->name('hr-dashboard');

        // Subscriber Dedicated Scoped Views & Device Store
        Route::get('/devices', [SubscriberDeviceController::class, 'index'])->name('devices.index');
        Route::post('/devices', [SubscriberDeviceController::class, 'store'])->name('devices.store');
        Route::put('/devices/{id}', [SubscriberDeviceController::class, 'update'])->name('devices.update');
        Route::delete('/devices/{id}', [SubscriberDeviceController::class, 'destroy'])->name('devices.destroy');
        Route::get('/devices/{id}/status', [SubscriberDeviceController::class, 'checkStatus'])->name('devices.status');
        Route::get('/attendance', [SubscriberAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/export', [SubscriberAttendanceController::class, 'export'])->name('attendance.export');
        Route::get('/users', [SubscriberUserController::class, 'index'])->name('users.index');

        // Webhook Data Push to External Server
        Route::get('/webhook', [WebhookPushController::class, 'index'])->name('webhook.index');
        Route::post('/webhook', [WebhookPushController::class, 'update'])->name('webhook.update');
        Route::post('/webhook/test', [WebhookPushController::class, 'testPush'])->name('webhook.test');

        // Mock Remote Server Viewer
        Route::get('/mock-remote-viewer', [MockRemoteServerController::class, 'viewReceivedPayloads'])->name('mock.viewer');
        Route::post('/mock-remote-clear', [MockRemoteServerController::class, 'clearReceivedPayloads'])->name('mock.clear');

        // Real-time Attendance Feed (JSON for AJAX polling)
        Route::get('/attendance/live', [SubscriberAttendanceController::class, 'live'])->name('attendance.live');
        Route::get('/dashboard/stats', [SubscriberDashboardController::class, 'stats'])->name('dashboard.stats');

        // ADMS Management Sub-Pages
        Route::get('/adms/overview', [SubscriberDashboardController::class, 'admsOverview'])->name('adms.overview');
        Route::get('/adms/endpoint', [SubscriberDashboardController::class, 'admsEndpoint'])->name('adms.endpoint');
        Route::get('/adms/punch-logs', [SubscriberDashboardController::class, 'admsPunchLogs'])->name('adms.punch-logs');
        Route::get('/adms/handshake-test', [SubscriberDashboardController::class, 'admsHandshakeTest'])->name('adms.handshake-test');
        Route::post('/adms/device-command', [SubscriberDashboardController::class, 'sendDeviceCommand'])->name('adms.device-command');
        Route::get('/adms/command-status', [SubscriberDashboardController::class, 'checkCommandStatus'])->name('adms.command-status');
        Route::get('/adms/device-status', [SubscriberDashboardController::class, 'deviceStatus'])->name('adms.device-status');
        Route::post('/adms/regenerate-token', [SubscriberDashboardController::class, 'regenerateTenantToken'])->name('adms.regenerate-token');

        // ADMS Listener & Server Configuration
        Route::get('/adms/listener-config', [SubscriberDashboardController::class, 'admsListenerConfig'])->name('adms.listener-config');
        Route::post('/adms/listener-config', [SubscriberDashboardController::class, 'updateAdmsListenerConfig'])->name('adms.listener-config.update');


        // Subscription & Account Overview
        Route::get('/subscription', [SubscriberDashboardController::class, 'subscriptionOverview'])->name('subscription.overview');

        // Plans & Checkout
        Route::get('/plans', [SubscriptionCheckoutController::class, 'plans'])->name('plans');
        Route::post('/checkout', [SubscriptionCheckoutController::class, 'checkout'])->name('checkout');

        // HRIS Modules
        Route::prefix('hris')->name('hris.')->group(function () {
            Route::resource('departments', Hris\DepartmentController::class);
            Route::resource('designations', Hris\DesignationController::class);
            Route::resource('shifts', Hris\ShiftController::class);

            // Employee import (must be before resource to avoid {employee} catch-all)
            Route::get('employees/import', [Hris\EmployeeImportController::class, 'showImportForm'])->name('employees.import');
            Route::get('employees/import/template', [Hris\EmployeeImportController::class, 'downloadTemplate'])->name('employees.import.template');
            Route::post('employees/import/preview', [Hris\EmployeeImportController::class, 'preview'])->name('employees.import.preview');
            Route::post('employees/import/execute', [Hris\EmployeeImportController::class, 'doImport'])->name('employees.import.execute');

            // Employee draft save/load (before resource too)
            Route::post('employees/draft/save', [Hris\EmployeeController::class, 'saveDraft'])->name('employees.draft.save');
            Route::get('employees/draft/load', [Hris\EmployeeController::class, 'loadDraft'])->name('employees.draft.load');
            Route::delete('employees/draft/clear', [Hris\EmployeeController::class, 'clearDraft'])->name('employees.draft.clear');

            Route::resource('employees', Hris\EmployeeController::class);
            Route::resource('kpis', Hris\KpiController::class);
            Route::get('leaves/apply', [Hris\LeaveController::class, 'apply'])->name('leaves.apply');
            Route::get('leaves/balance', [Hris\LeaveController::class, 'getBalance'])->name('leaves.balance');
            Route::get('leaves/employee-info', [Hris\LeaveController::class, 'getEmployeeInfo'])->name('leaves.employee-info');
            Route::get('leaves/history', [Hris\LeaveController::class, 'getLeaveHistory'])->name('leaves.history');
            Route::get('leaves/{leave}/pdf', [Hris\LeaveController::class, 'downloadPdf'])->name('leaves.pdf');
            Route::post('leaves/{leave}/approve', [Hris\LeaveController::class, 'approve'])->name('leaves.approve');
            Route::post('leaves/{leave}/reject', [Hris\LeaveController::class, 'reject'])->name('leaves.reject');
            Route::resource('leaves', Hris\LeaveController::class);

            // Movement Types Setup
            Route::get('movement-types/limits', [Hris\MovementTypeController::class, 'limits'])->name('movement-types.limits');
            Route::post('movement-types/limits', [Hris\MovementTypeController::class, 'storeLimit'])->name('movement-types.limits.store');
            Route::delete('movement-types/limits/{limit}', [Hris\MovementTypeController::class, 'destroyLimit'])->name('movement-types.limits.destroy');
            Route::resource('movement-types', Hris\MovementTypeController::class)->except(['show']);

            // Movement Passes
            Route::get('movement-passes/apply', [Hris\MovementPassController::class, 'apply'])->name('movement-passes.apply');
            Route::get('movement-passes/employee-info', [Hris\MovementPassController::class, 'getEmployeeInfo'])->name('movement-passes.employee-info');
            Route::get('movement-passes/monthly-usage', [Hris\MovementPassController::class, 'getMonthlyUsage'])->name('movement-passes.monthly-usage');
            Route::get('movement-passes/history', [Hris\MovementPassController::class, 'getPassHistory'])->name('movement-passes.history');
            Route::post('movement-passes/{pass}/approve', [Hris\MovementPassController::class, 'approve'])->name('movement-passes.approve');
            Route::post('movement-passes/{pass}/reject', [Hris\MovementPassController::class, 'reject'])->name('movement-passes.reject');
            Route::resource('movement-passes', Hris\MovementPassController::class)->except(['edit', 'update', 'show']);

            Route::get('promotions/employee-search', [Hris\PromotionController::class, 'getEmployee'])->name('promotions.employee-search');
            Route::resource('promotions', Hris\PromotionController::class)->except(['edit', 'update', 'destroy']);
            Route::resource('increment-rules', Hris\IncrementRuleController::class);
            Route::get('increments/employee-search', [Hris\IncrementController::class, 'getEmployee'])->name('increments.employee-search');
            Route::get('increments/check-eligibility', [Hris\IncrementController::class, 'checkEligibility'])->name('increments.check-eligibility');
            Route::get('increments/enforce', [Hris\IncrementController::class, 'enforce'])->name('increments.enforce');
            Route::post('increments/do-enforce', [Hris\IncrementController::class, 'doEnforce'])->name('increments.do-enforce');
            Route::get('increments/{increment}/letter', [Hris\IncrementController::class, 'letter'])->name('increments.letter');
            Route::resource('increments', Hris\IncrementController::class)->except(['edit', 'update', 'destroy', 'show']);
        Route::post('general/verification/verify', [Hris\GeneralController::class, 'verify'])->name('general.verification.verify');
        Route::get('general/{module}', [Hris\GeneralController::class, 'show'])->name('general.show');
        Route::post('general/{module}', [Hris\GeneralController::class, 'submit'])->name('general.submit');

            // Bill Types Setup
            Route::resource('bill-types', Hris\BillTypeController::class)->except(['show']);

            // Bill Purposes Setup
            Route::resource('bill-purposes', Hris\BillPurposeController::class)->except(['show']);

            // Bills (Expense Applications)
            Route::get('bills/apply', [Hris\BillController::class, 'apply'])->name('bills.apply');
            Route::get('bills/employee-info', [Hris\BillController::class, 'employeeInfo'])->name('bills.employee-info');
            Route::get('bills/approval', [Hris\BillController::class, 'approval'])->name('bills.approval');
            Route::post('bills/{bill}/approve', [Hris\BillController::class, 'approve'])->name('bills.approve');
            Route::post('bills/{bill}/reject', [Hris\BillController::class, 'reject'])->name('bills.reject');
            Route::post('bills/{bill}/modify', [Hris\BillController::class, 'modify'])->name('bills.modify');
            Route::get('bills/{bill}/pdf', [Hris\BillController::class, 'pdf'])->name('bills.pdf');
            Route::resource('bills', Hris\BillController::class)->except(['edit', 'update', 'destroy']);

            // Roles & Permissions
            Route::get('users/employee-info', [Hris\UserController::class, 'getEmployeeInfo'])->name('users.employee-info');
            Route::resource('users', Hris\UserController::class)->except(['show']);
            Route::resource('roles', Hris\RoleController::class)->except(['show']);
            Route::get('permissions', [Hris\PermissionController::class, 'index'])->name('permissions.index');

            // System Parameters
            Route::resource('system-parameters', Hris\SystemParameterController::class)->except(['show', 'create', 'edit']);

            // Advance Types Setup
            Route::resource('advance-types', Hris\AdvanceTypeController::class)->except(['show']);

            // Advance Sources Setup
            Route::resource('advance-sources', Hris\AdvanceSourceController::class)->except(['show']);

            // Salary Advances
            Route::get('advances/apply', [Hris\AdvanceController::class, 'apply'])->name('advances.apply');
            Route::get('advances/employee-info', [Hris\AdvanceController::class, 'employeeInfo'])->name('advances.employee-info');
            Route::get('advances/approval', [Hris\AdvanceController::class, 'approval'])->name('advances.approval');
            Route::post('advances/{advance}/approve', [Hris\AdvanceController::class, 'approve'])->name('advances.approve');
            Route::post('advances/{advance}/reject', [Hris\AdvanceController::class, 'reject'])->name('advances.reject');
            Route::resource('advances', Hris\AdvanceController::class)->except(['edit', 'update', 'destroy']);

            // System Setup
            Route::get('setup/subscriber', [Hris\SystemSetupController::class, 'subscriberInfo'])->name('setup.subscriber');
            Route::put('setup/subscriber', [Hris\SystemSetupController::class, 'updateSubscriberInfo'])->name('setup.subscriber.update');
            Route::get('setup/theme', [Hris\SystemSetupController::class, 'theme'])->name('setup.theme');
            Route::put('setup/theme', [Hris\SystemSetupController::class, 'updateTheme'])->name('setup.theme.update');
            Route::get('setup/mail', [Hris\SystemSetupController::class, 'mailConfig'])->name('setup.mail');
            Route::put('setup/mail', [Hris\SystemSetupController::class, 'updateMailConfig'])->name('setup.mail.update');
            Route::post('setup/mail/test', [Hris\SystemSetupController::class, 'testMail'])->name('setup.mail.test');
            Route::get('setup/sms', [Hris\SystemSetupController::class, 'smsConfig'])->name('setup.sms');
            Route::put('setup/sms', [Hris\SystemSetupController::class, 'updateSmsConfig'])->name('setup.sms.update');
            Route::post('setup/sms/test', [Hris\SystemSetupController::class, 'testSms'])->name('setup.sms.test');
            Route::get('setup/backup', [Hris\SystemSetupController::class, 'backup'])->name('setup.backup');
            Route::post('setup/backup', [Hris\SystemSetupController::class, 'createBackup'])->name('setup.backup.create');
            Route::get('setup/backup/{filename}/download', [Hris\SystemSetupController::class, 'downloadBackup'])->name('setup.backup.download');
            Route::post('setup/backup/{filename}/restore', [Hris\SystemSetupController::class, 'restoreBackup'])->name('setup.backup.restore');
            Route::delete('setup/backup/{filename}', [Hris\SystemSetupController::class, 'deleteBackup'])->name('setup.backup.delete');

            // Master Setup Dashboard (Sex, Address Hierarchy, Board, Institution, Leave Reasons, Salary Relations, Leave Balance)
            Route::get('master-setup', [Hris\MasterSetupController::class, 'index'])->name('master.index');
            Route::post('master-setup/store/{type}', [Hris\MasterSetupController::class, 'store'])->name('master.store');
            Route::delete('master-setup/delete/{type}/{id}', [Hris\MasterSetupController::class, 'destroy'])->name('master.destroy');
            Route::post('master-setup/salary-relation', [Hris\MasterSetupController::class, 'storeSalaryRelation'])->name('master.salary-relation');
            Route::post('master-setup/leave-balance', [Hris\MasterSetupController::class, 'storeLeaveBalance'])->name('master.leave-balance');
        });

        // Payroll Module
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/salary-role', [\App\Http\Controllers\Subscriber\PayrollController::class, 'salaryRole'])->name('salary-role');
            Route::post('/salary-role', [\App\Http\Controllers\Subscriber\PayrollController::class, 'storeSalaryRole'])->name('salary-role.store');
            Route::post('/salary-role/{id}/activate', [\App\Http\Controllers\Subscriber\PayrollController::class, 'activateSalaryRole'])->name('salary-role.activate');
            Route::put('/salary-role/{id}', [\App\Http\Controllers\Subscriber\PayrollController::class, 'updateSalaryRole'])->name('salary-role.update');
            Route::delete('/salary-role/{id}', [\App\Http\Controllers\Subscriber\PayrollController::class, 'deleteSalaryRole'])->name('salary-role.delete');
            Route::post('/salary-role/assignments', [\App\Http\Controllers\Subscriber\PayrollController::class, 'storeRoleAssignment'])->name('salary-role.assignments.store');
            Route::delete('/salary-role/assignments/{id}', [\App\Http\Controllers\Subscriber\PayrollController::class, 'deleteRoleAssignment'])->name('salary-role.assignments.delete');
            Route::post('/salary-role/bonus-config', [\App\Http\Controllers\Subscriber\PayrollController::class, 'storeBonusConfig'])->name('salary-role.bonus-config.store');
            Route::delete('/salary-role/bonus-config/{id}', [\App\Http\Controllers\Subscriber\PayrollController::class, 'deleteBonusConfig'])->name('salary-role.bonus-config.delete');
            Route::get('/database', [\App\Http\Controllers\Subscriber\PayrollController::class, 'database'])->name('database');
            Route::get('/salary-generate', [\App\Http\Controllers\Subscriber\PayrollController::class, 'salaryGenerate'])->name('salary-generate');
            Route::post('/salary-generate', [\App\Http\Controllers\Subscriber\PayrollController::class, 'generateSalary'])->name('salary-generate.generate');
            Route::delete('/salary-generate/undo', [\App\Http\Controllers\Subscriber\PayrollController::class, 'deleteSalaryPayroll'])->name('salary-generate.undo');
            Route::post('/salary-generate/confirm', [\App\Http\Controllers\Subscriber\PayrollController::class, 'confirmSalaryPayroll'])->name('salary-generate.confirm');
            Route::get('/payslip', [\App\Http\Controllers\Subscriber\PayrollController::class, 'payslip'])->name('payslip');
            Route::get('/download-template/{format}', [\App\Http\Controllers\Subscriber\PayrollController::class, 'downloadTemplate'])->name('download-template');
            Route::get('/punch-data-upload', [\App\Http\Controllers\Subscriber\PayrollController::class, 'punchDataUpload'])->name('punch-data-upload');
            Route::post('/punch-data-upload', [\App\Http\Controllers\Subscriber\PayrollController::class, 'processPunchUpload'])->name('punch-data-upload.process');
            Route::post('/punch-data-upload/toggle-live-sync', [\App\Http\Controllers\Subscriber\PayrollController::class, 'toggleLiveSync'])->name('punch-data-upload.toggle-live-sync');
            Route::post('/punch-data-upload/sync-live', [\App\Http\Controllers\Subscriber\PayrollController::class, 'syncLivePunches'])->name('punch-data-upload.sync-live');
            Route::get('/punch-data-upload/month-count', [\App\Http\Controllers\Subscriber\PayrollController::class, 'monthPunchCount'])->name('punch-data-upload.month-count');
            Route::delete('/punch-data-upload/undo', [\App\Http\Controllers\Subscriber\PayrollController::class, 'undoPunchData'])->name('punch-data-upload.undo');
            Route::get('/process-attendance', [\App\Http\Controllers\Subscriber\PayrollController::class, 'processAttendance'])->name('process-attendance');
            Route::post('/process-attendance', [\App\Http\Controllers\Subscriber\PayrollController::class, 'runProcessAttendance'])->name('process-attendance.run');

            Route::delete('/process-attendance/undo', [\App\Http\Controllers\Subscriber\PayrollController::class, 'undoProcessedAttendance'])->name('process-attendance.undo');
            Route::get('/process-attendance/month-count', [\App\Http\Controllers\Subscriber\PayrollController::class, 'processedMonthCount'])->name('process-attendance.month-count');
            Route::get('/report', [\App\Http\Controllers\Subscriber\PayrollController::class, 'report'])->name('report');
            Route::get('/report/export/{type}', [\App\Http\Controllers\Subscriber\PayrollController::class, 'reportExport'])->name('report.export');
        });
    });

    // Protected Admin Routes (Strictly Restricted to System Admin & Business Admin)
    Route::middleware([EnsureAdminRole::class])->group(function () {

        // System Admin Panel Routes (/admin/system/*)
        Route::prefix('admin/system')->name('admin.system.')->group(function () {
            Route::get('/', [SystemAdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [SystemAdminDashboardController::class, 'index']);

            // User Manager (SaaS Application Users)
            Route::resource('users', SaasUserController::class);

            // Role & Permissions Matrix
            Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
            Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
            Route::put('/roles/{role}', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.update');

            // Website Manager
            Route::get('/website', [WebsiteManagerController::class, 'index'])->name('website.index');
            Route::post('/website', [WebsiteManagerController::class, 'update'])->name('website.update');
            Route::get('/website/preview', [WebsiteManagerController::class, 'preview'])->name('website.preview');

            // System Monitoring (Logs by Category, Realtime Requests, Health)
            Route::get('/monitoring', [SystemMonitoringController::class, 'index'])->name('monitoring.index');

            // Databases Audit
            Route::get('/database', [DatabaseManagerController::class, 'index'])->name('database.index');
            Route::post('/database/backup', [DatabaseManagerController::class, 'backup'])->name('database.backup');
            Route::post('/database/restore', [DatabaseManagerController::class, 'restore'])->name('database.restore');
            Route::get('/database/backup/{filename}/download', [DatabaseManagerController::class, 'downloadBackup'])->name('database.backup.download');
            Route::post('/database/backup/{filename}/delete', [DatabaseManagerController::class, 'deleteBackup'])->name('database.backup.delete');
            Route::post('/database/execute-sql', [DatabaseManagerController::class, 'executeSql'])->name('database.execute-sql');
            Route::get('/database/table/{table}', [DatabaseManagerController::class, 'showTable'])->name('database.table');
            Route::post('/database/table/{table}/insert', [DatabaseManagerController::class, 'insertRow'])->name('database.table.insert');
            Route::post('/database/table/{table}/{id}/update', [DatabaseManagerController::class, 'updateRow'])->name('database.table.update');
            Route::post('/database/table/{table}/{id}/delete', [DatabaseManagerController::class, 'deleteRow'])->name('database.table.delete');
            Route::get('/database/export-tenant/{tenant}', [DatabaseManagerController::class, 'exportTenantData'])->name('database.export-tenant');

            // System Security Audit
            Route::get('/security', [SecurityAuditController::class, 'index'])->name('security.index');
            Route::post('/security/block-ip', [SecurityAuditController::class, 'blockIp'])->name('security.block_ip');
            Route::post('/security/unblock-ip', [SecurityAuditController::class, 'unblockIp'])->name('security.unblock_ip');
            Route::post('/security/update', [SecurityAuditController::class, 'updateSecurity'])->name('security.update');

            // Gateway Configuration (SMS, Mail & SSLCommerz)
            Route::get('/gateways', [GatewayConfigController::class, 'index'])->name('gateways.index');
            Route::post('/gateways/sms', [GatewayConfigController::class, 'updateSms'])->name('gateways.update_sms');
            Route::post('/gateways/sms/test', [GatewayConfigController::class, 'testSms'])->name('gateways.test_sms');
            Route::post('/gateways/mail', [GatewayConfigController::class, 'updateMail'])->name('gateways.update_mail');
            Route::post('/gateways/mail/test', [GatewayConfigController::class, 'testMail'])->name('gateways.test_mail');
            Route::post('/gateways/sslcommerz', [GatewayConfigController::class, 'updateSslcommerz'])->name('gateways.update_sslcommerz');

            // Network Settings & ADMS Listener
            Route::get('/network', [NetworkManagerController::class, 'index'])->name('network.index');
            Route::post('/network', [NetworkManagerController::class, 'update'])->name('network.update');
        });

        // Business Admin Panel Routes (/admin/business/*)
        Route::prefix('admin/business')->name('admin.business.')->group(function () {
            Route::get('/', [SubscriberController::class, 'index'])->name('dashboard');

            // Subscriber Management
            Route::resource('subscribers', SubscriberController::class);
            Route::post('subscribers/{tenant}/reset-password', [SubscriberController::class, 'resetPassword'])->name('subscribers.reset_password');
            Route::post('subscribers/{tenant}/toggle-status', [SubscriberController::class, 'toggleStatus'])->name('subscribers.toggle_status');
            Route::post('subscribers/{tenant}/record-payment', [SubscriberController::class, 'recordPayment'])->name('subscribers.record_payment');
            Route::post('subscribers/{tenant}/send-email', [SubscriberController::class, 'sendEmail'])->name('subscribers.send_email');
            Route::post('subscribers/bulk-email', [SubscriberController::class, 'sendBulkEmail'])->name('subscribers.bulk_email');
            Route::get('subscribers/{tenant}/payments', [SubscriberController::class, 'paymentHistory'])->name('subscribers.payments');

            // Package Plans
            Route::resource('plans', PackagePlanController::class);
        });

        // Legacy Admin Dashboard Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Devices
            Route::resource('devices', DeviceController::class);
            Route::post('devices/{device}/reboot', [DeviceController::class, 'reboot'])->name('devices.reboot');
            Route::post('devices/{device}/clear-logs', [DeviceController::class, 'clearLogs'])->name('devices.clear-logs');
            Route::post('devices/{device}/query-info', [DeviceController::class, 'queryInfo'])->name('devices.query-info');

            // Attendance Logs & Export
            Route::get('attendance', [AttendanceLogController::class, 'index'])->name('attendance.index');
            Route::get('attendance/export', [AttendanceLogController::class, 'export'])->name('attendance.export');

            // Biometric Users
            Route::resource('users', ZktecoUserController::class);
            Route::post('users/{user}/push/{device}', [ZktecoUserController::class, 'pushToDevice'])->name('users.push');

            // Device Commands Queue
            Route::get('commands', [DeviceCommandController::class, 'index'])->name('commands.index');
            Route::delete('commands/{command}', [DeviceCommandController::class, 'destroy'])->name('commands.destroy');

            // Network & System Settings
            Route::get('settings', [NetworkSettingController::class, 'index'])->name('settings.index');
            Route::post('settings', [NetworkSettingController::class, 'update'])->name('settings.update');
            Route::post('settings/test-connection', [NetworkSettingController::class, 'testConnection'])->name('settings.test-connection');
        });
    });

});
