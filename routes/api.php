<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DesignationController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\BillTypeController;
use App\Http\Controllers\Api\BillPurposeController;
use App\Http\Controllers\Api\AdvanceController;
use App\Http\Controllers\Api\AdvanceTypeController;
use App\Http\Controllers\Api\AdvanceSourceController;
use App\Http\Controllers\Api\MovementTypeController;
use App\Http\Controllers\Api\MovementPassController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\IncrementController;
use App\Http\Controllers\Api\IncrementRuleController;
use App\Http\Controllers\Api\KpiController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\HrisUserController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\PayrollDatabaseController;
use App\Http\Controllers\Api\PayrollPunchDataController;
use App\Http\Controllers\Api\PayrollProcessAttendanceController;
use App\Http\Controllers\Api\PayrollSalaryGenerateController;
use App\Http\Controllers\Api\PayrollSalaryRoleController;
use App\Http\Controllers\Api\PayrollReportController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\AdmsController;
use App\Http\Controllers\Api\MockController;
use App\Http\Controllers\Api\SaasUserController;
use App\Http\Controllers\Api\SysRoleController;
use App\Http\Controllers\Api\DatabaseController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\SysMonitoringController;
use App\Http\Controllers\Api\SysNetworkController;
use App\Http\Controllers\Api\SysSecurityController;
use App\Http\Controllers\Api\SysWebsiteController;
use App\Http\Controllers\Api\BizSubscriberController;
use App\Http\Controllers\Api\BizPlanController;
use App\Http\Controllers\Api\AdminDeviceController;
use App\Http\Controllers\Api\AdminAttendanceController;
use App\Http\Controllers\Api\AdminCommandController;
use App\Http\Controllers\Api\AdminSettingController;
use App\Http\Controllers\Api\AdminUserController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/plans', [SubscriptionController::class, 'plans']);

// Protected API routes
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/hr-stats', [DashboardController::class, 'hrStats']);

    // HRIS Resources
    Route::apiResources([
        '/departments' => DepartmentController::class,
        '/designations' => DesignationController::class,
        '/shifts' => ShiftController::class,
        '/employees' => EmployeeController::class,
        '/leaves' => LeaveController::class,
        '/leave-types' => LeaveController::class,
        '/bills' => BillController::class,
        '/bill-types' => BillTypeController::class,
        '/bill-purposes' => BillPurposeController::class,
        '/advances' => AdvanceController::class,
        '/advance-types' => AdvanceTypeController::class,
        '/advance-sources' => AdvanceSourceController::class,
        '/movement-types' => MovementTypeController::class,
        '/movement-passes' => MovementPassController::class,
        '/promotions' => PromotionController::class,
        '/increments' => IncrementController::class,
        '/increment-rules' => IncrementRuleController::class,
        '/kpis' => KpiController::class,
        '/hris/roles' => RoleController::class,
        '/hris/users' => HrisUserController::class,
    ]);
    Route::get('/permissions', [PermissionController::class, 'index']);

    // Payroll
    Route::get('/payroll/salary-roles', [PayrollSalaryRoleController::class, 'index']);
    Route::post('/payroll/salary-roles', [PayrollSalaryRoleController::class, 'store']);
    Route::put('/payroll/salary-roles/{id}', [PayrollSalaryRoleController::class, 'update']);
    Route::delete('/payroll/salary-roles/{id}', [PayrollSalaryRoleController::class, 'destroy']);
    Route::get('/payroll/payslips', [PayrollController::class, 'payslips']);
    Route::get('/payroll/database', [PayrollDatabaseController::class, 'index']);
    Route::post('/payroll/database/upload', [PayrollDatabaseController::class, 'upload']);
    Route::get('/payroll/punch-data', [PayrollPunchDataController::class, 'index']);
    Route::post('/payroll/punch-data/upload', [PayrollPunchDataController::class, 'upload']);
    Route::post('/payroll/punch-data/toggle-live-sync', [PayrollPunchDataController::class, 'toggleLiveSync']);
    Route::post('/payroll/punch-data/sync-live', [PayrollPunchDataController::class, 'syncLive']);
    Route::get('/payroll/process-attendance', [PayrollProcessAttendanceController::class, 'index']);
    Route::post('/payroll/process-attendance/run', [PayrollProcessAttendanceController::class, 'run']);
    Route::post('/payroll/process-attendance/undo', [PayrollProcessAttendanceController::class, 'undo']);
    Route::get('/payroll/salary-generate', [PayrollSalaryGenerateController::class, 'index']);
    Route::post('/payroll/salary-generate/generate', [PayrollSalaryGenerateController::class, 'generate']);
    Route::post('/payroll/salary-generate/confirm', [PayrollSalaryGenerateController::class, 'confirm']);
    Route::post('/payroll/salary-generate/undo', [PayrollSalaryGenerateController::class, 'undo']);
    Route::get('/payroll/reports', [PayrollReportController::class, 'index']);
    Route::get('/payroll/reports/export/{type}', [PayrollReportController::class, 'export']);

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'overview']);

    // Webhook / ADMS / Mock
    Route::get('/webhook', [WebhookController::class, 'show']);
    Route::post('/webhook', [WebhookController::class, 'update']);
    Route::post('/webhook/test', [WebhookController::class, 'test']);
    Route::get('/adms/overview', [AdmsController::class, 'overview']);
    Route::get('/adms/endpoint', [AdmsController::class, 'endpoint']);
    Route::get('/adms/punch-logs', [AdmsController::class, 'punchLogs']);
    Route::get('/adms/handshake-test', [AdmsController::class, 'handshakeTest']);
    Route::get('/adms/listener-config', [AdmsController::class, 'listenerConfig']);
    Route::post('/adms/listener-config', [AdmsController::class, 'updateListenerConfig']);
    Route::get('/mock/viewer', [MockController::class, 'viewer']);
    Route::post('/mock/clear', [MockController::class, 'clear']);

    // Subscriber setup
    Route::get('/setup/subscriber', [\App\Http\Controllers\Api\SetupController::class, 'subscriberInfo']);
    Route::put('/setup/subscriber', [\App\Http\Controllers\Api\SetupController::class, 'updateSubscriberInfo']);
    Route::get('/setup/theme', [\App\Http\Controllers\Api\SetupController::class, 'theme']);
    Route::put('/setup/theme', [\App\Http\Controllers\Api\SetupController::class, 'updateTheme']);
    Route::get('/setup/mail', [\App\Http\Controllers\Api\SetupController::class, 'mailConfig']);
    Route::put('/setup/mail', [\App\Http\Controllers\Api\SetupController::class, 'updateMailConfig']);
    Route::post('/setup/mail/test', [\App\Http\Controllers\Api\SetupController::class, 'testMail']);
    Route::get('/setup/sms', [\App\Http\Controllers\Api\SetupController::class, 'smsConfig']);
    Route::put('/setup/sms', [\App\Http\Controllers\Api\SetupController::class, 'updateSmsConfig']);
    Route::post('/setup/sms/test', [\App\Http\Controllers\Api\SetupController::class, 'testSms']);
    Route::get('/setup/backup', [\App\Http\Controllers\Api\SetupController::class, 'backup']);
    Route::post('/setup/backup', [\App\Http\Controllers\Api\SetupController::class, 'createBackup']);
    Route::post('/setup/backup/restore', [\App\Http\Controllers\Api\SetupController::class, 'restoreBackup']);
    Route::delete('/setup/backup/{filename}', [\App\Http\Controllers\Api\SetupController::class, 'deleteBackup']);

    // Master setup / General / Verification / Calendar
    Route::get('/master-setup', [\App\Http\Controllers\Api\MasterSetupController::class, 'index']);
    Route::post('/master-setup/store/{type}', [\App\Http\Controllers\Api\MasterSetupController::class, 'store']);
    Route::delete('/master-setup/{type}/{id}', [\App\Http\Controllers\Api\MasterSetupController::class, 'destroy']);
    Route::get('/calendar', [\App\Http\Controllers\Api\GeneralController::class, 'calendar']);
    Route::get('/verification', [\App\Http\Controllers\Api\GeneralController::class, 'verification']);
    Route::post('/verification/verify', [\App\Http\Controllers\Api\GeneralController::class, 'verify']);

    // System Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\SysDashboardController::class, 'index']);
        Route::apiResource('/users', SaasUserController::class);
        Route::apiResource('/roles', SysRoleController::class);
        Route::get('/database', [DatabaseController::class, 'index']);
        Route::post('/database/backup', [DatabaseController::class, 'backup']);
        Route::post('/database/restore', [DatabaseController::class, 'restore']);
        Route::get('/database/table/{table}', [DatabaseController::class, 'showTable']);
        Route::get('/gateways', [GatewayController::class, 'index']);
        Route::post('/gateways/sms', [GatewayController::class, 'updateSms']);
        Route::post('/gateways/sms/test', [GatewayController::class, 'testSms']);
        Route::post('/gateways/mail', [GatewayController::class, 'updateMail']);
        Route::post('/gateways/mail/test', [GatewayController::class, 'testMail']);
        Route::post('/gateways/sslcommerz', [GatewayController::class, 'updateSslcommerz']);
        Route::get('/monitoring', [SysMonitoringController::class, 'index']);
        Route::get('/network', [SysNetworkController::class, 'show']);
        Route::post('/network', [SysNetworkController::class, 'update']);
        Route::get('/security', [SysSecurityController::class, 'index']);
        Route::post('/security/block-ip', [SysSecurityController::class, 'blockIp']);
        Route::post('/security/unblock-ip', [SysSecurityController::class, 'unblockIp']);
        Route::post('/security/update', [SysSecurityController::class, 'update']);
        Route::get('/website', [SysWebsiteController::class, 'index']);
        Route::post('/website', [SysWebsiteController::class, 'update']);
        Route::get('/website/preview', [SysWebsiteController::class, 'preview']);
    });

    // Business Admin routes
    Route::prefix('business')->group(function () {
        Route::get('/subscribers', [BizSubscriberController::class, 'index']);
        Route::post('/subscribers', [BizSubscriberController::class, 'store']);
        Route::put('/subscribers/{id}', [BizSubscriberController::class, 'update']);
        Route::post('/subscribers/{id}/toggle-status', [BizSubscriberController::class, 'toggleStatus']);
        Route::post('/subscribers/{id}/record-payment', [BizSubscriberController::class, 'recordPayment']);
        Route::get('/subscribers/{id}/payments', [BizSubscriberController::class, 'paymentHistory']);
        Route::apiResource('/plans', BizPlanController::class);
    });

    // Legacy Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/legacy/devices', [AdminDeviceController::class, 'index']);
        Route::post('/legacy/devices', [AdminDeviceController::class, 'store']);
        Route::put('/legacy/devices/{id}', [AdminDeviceController::class, 'update']);
        Route::delete('/legacy/devices/{id}', [AdminDeviceController::class, 'destroy']);
        Route::post('/legacy/devices/{id}/reboot', [AdminDeviceController::class, 'reboot']);
        Route::post('/legacy/devices/{id}/clear-logs', [AdminDeviceController::class, 'clearLogs']);
        Route::post('/legacy/devices/{id}/query-info', [AdminDeviceController::class, 'queryInfo']);
        Route::get('/legacy/attendance', [AdminAttendanceController::class, 'index']);
        Route::get('/legacy/attendance/export', [AdminAttendanceController::class, 'export']);
        Route::get('/legacy/commands', [AdminCommandController::class, 'index']);
        Route::delete('/legacy/commands/{id}', [AdminCommandController::class, 'destroy']);
        Route::get('/legacy/settings', [AdminSettingController::class, 'index']);
        Route::post('/legacy/settings', [AdminSettingController::class, 'update']);
        Route::post('/legacy/settings/test-connection', [AdminSettingController::class, 'testConnection']);
        Route::apiResource('/legacy/users', AdminUserController::class);
        Route::post('/legacy/users/{id}/push/{deviceId}', [AdminUserController::class, 'pushToDevice']);
    });
});
