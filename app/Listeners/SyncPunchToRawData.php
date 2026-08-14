<?php

namespace App\Listeners;

use App\Events\AttendanceReceived;
use App\Models\EmployeeProfile;
use App\Models\TenantConfig;
use Illuminate\Support\Facades\DB;

class SyncPunchToRawData
{
    public function handle(AttendanceReceived $event): void
    {
        $device = $event->device;
        $log = $event->attendanceLog;
        $tenantId = $device->tenant_id;

        $enabled = TenantConfig::where('tenant_id', $tenantId)
            ->where('group', 'payroll')
            ->where('key', 'punch_live_sync')
            ->value('value');

        if ($enabled !== '1') return;

        $employee = EmployeeProfile::where('tenant_id', $tenantId)
            ->where('employee_id', $log->pin)
            ->first();

        try {
            DB::table('raw_punch_data')->insert([
                'tenant_id' => $tenantId,
                'employee_profile_id' => $employee?->id,
                'employee_id' => $log->pin,
                'punch_machine_serial' => $device->serial_number,
                'punch_date_time' => $log->punched_at,
                'status' => (string) $log->status,
                'verify_type' => (string) $log->verify_type,
                'source_file' => 'live:' . $device->serial_number,
                'is_matched' => $employee ? true : false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // silently skip duplicates
        }
    }
}
