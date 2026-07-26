<?php

use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceCommandController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\NetworkSettingController;
use App\Http\Controllers\Admin\ZktecoUserController;
use App\Http\Controllers\Adms\DeviceCmdController;
use App\Http\Controllers\Adms\GetRequestController;
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

Route::any('/iclock/cdata', TenantCDataController::class)->name('adms.cdata');
Route::get('/iclock/getrequest', GetRequestController::class)->name('adms.getrequest');
Route::post('/iclock/devicecmd', DeviceCmdController::class)->name('adms.devicecmd');

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

        // Subscriber Dedicated Scoped Views & Device Store
        Route::get('/devices', [SubscriberDeviceController::class, 'index'])->name('devices.index');
        Route::post('/devices', [SubscriberDeviceController::class, 'store'])->name('devices.store');
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

        // Plans & Checkout
        Route::get('/plans', [SubscriptionCheckoutController::class, 'plans'])->name('plans');
        Route::post('/checkout', [SubscriptionCheckoutController::class, 'checkout'])->name('checkout');

        // HRIS Modules
        Route::prefix('hris')->name('hris.')->group(function () {
            Route::resource('departments', Hris\DepartmentController::class);
            Route::resource('designations', Hris\DesignationController::class);
            Route::resource('shifts', Hris\ShiftController::class);
            Route::resource('employees', Hris\EmployeeController::class);
            Route::resource('kpis', Hris\KpiController::class);
            Route::resource('leaves', Hris\LeaveController::class);
            Route::get('general/{module}', [Hris\GeneralController::class, 'show'])->name('general.show');
            Route::post('general/{module}', [Hris\GeneralController::class, 'submit'])->name('general.submit');

            // Master Setup Dashboard (Sex, Address Hierarchy, Board, Institution, Leave Reasons, Salary Relations, Leave Balance)
            Route::get('master-setup', [Hris\MasterSetupController::class, 'index'])->name('master.index');
            Route::post('master-setup/store/{type}', [Hris\MasterSetupController::class, 'store'])->name('master.store');
            Route::delete('master-setup/delete/{type}/{id}', [Hris\MasterSetupController::class, 'destroy'])->name('master.destroy');
            Route::post('master-setup/salary-relation', [Hris\MasterSetupController::class, 'storeSalaryRelation'])->name('master.salary-relation');
            Route::post('master-setup/leave-balance', [Hris\MasterSetupController::class, 'storeLeaveBalance'])->name('master.leave-balance');
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
