<?php

use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceCommandController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\NetworkSettingController;
use App\Http\Controllers\Admin\ZktecoUserController;
use App\Http\Controllers\Adms\CDataController;
use App\Http\Controllers\Adms\DeviceCmdController;
use App\Http\Controllers\Adms\GetRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ZKTeco ADMS Device Communication Protocol Endpoints
|--------------------------------------------------------------------------
*/
Route::any('/iclock/cdata', CDataController::class)->name('adms.cdata');
Route::get('/iclock/getrequest', GetRequestController::class)->name('adms.getrequest');
Route::post('/iclock/devicecmd', DeviceCmdController::class)->name('adms.devicecmd');

/*
|--------------------------------------------------------------------------
| Custom Skote Admin Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
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
