<?php

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\User;
use App\Models\ZktecoUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdmsProtocolAndAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_adms_device_handshake_and_registration(): void
    {
        $response = $this->get('/iclock/cdata?SN=SNTEST999&options=all&pushver=3.1.1');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('GET OPTION FROM: SNTEST999', $response->getContent());

        $this->assertDatabaseHas('devices', [
            'serial_number' => 'SNTEST999',
            'status' => 'online',
        ]);
    }

    public function test_adms_attendance_log_submission(): void
    {
        $device = Device::create([
            'serial_number' => 'SNTEST888',
            'name' => 'Test Gate',
        ]);

        $rawLogs = "1001\t2026-07-21 09:00:00\t0\t1\t0\r\n1002\t2026-07-21 09:05:00\t1\t2\t0";

        $response = $this->call('POST', '/iclock/cdata?SN=SNTEST888&table=ATTLOG', [], [], [], [], $rawLogs);

        $response->assertStatus(200);
        $response->assertContent('OK');

        $this->assertDatabaseHas('attendance_logs', [
            'device_id' => $device->id,
            'pin' => '1001',
            'status' => 0,
        ]);

        $this->assertDatabaseHas('attendance_logs', [
            'device_id' => $device->id,
            'pin' => '1002',
            'status' => 1,
        ]);
    }

    public function test_adms_getrequest_command_polling(): void
    {
        $device = Device::create([
            'serial_number' => 'SNTEST777',
            'name' => 'Office Door',
        ]);

        $command = $device->commands()->create([
            'command' => 'REBOOT',
            'type' => 'REBOOT',
            'status' => 'pending',
        ]);

        $response = $this->get('/iclock/getrequest?SN=SNTEST777');

        $response->assertStatus(200);
        $response->assertContent("C:{$command->id}:REBOOT");

        $this->assertDatabaseHas('device_commands', [
            'id' => $command->id,
            'status' => 'sent',
        ]);
    }

    public function test_adms_devicecmd_return_processing(): void
    {
        $device = Device::create([
            'serial_number' => 'SNTEST666',
        ]);

        $command = $device->commands()->create([
            'command' => 'REBOOT',
            'type' => 'REBOOT',
            'status' => 'sent',
        ]);

        $payload = "ID={$command->id}&Return=0&CMD=REBOOT";

        $response = $this->call('POST', '/iclock/devicecmd?SN=SNTEST666', [], [], [], [], $payload);

        $response->assertStatus(200);
        $response->assertContent('OK');

        $this->assertDatabaseHas('device_commands', [
            'id' => $command->id,
            'status' => 'executed',
            'return_code' => 0,
        ]);
    }

    public function test_admin_dashboard_and_views_render_successfully(): void
    {
        Role::create(['name' => 'System Admin']);
        $adminUser = User::factory()->create();
        $adminUser->assignRole('System Admin');

        $this->get('/')->assertStatus(200);
        $this->actingAs($adminUser)->get('/admin/dashboard')->assertRedirect(route('admin.system.dashboard'));
        $this->actingAs($adminUser)->get('/admin/devices')->assertStatus(200);
        $this->actingAs($adminUser)->get('/admin/attendance')->assertStatus(200);
        $this->actingAs($adminUser)->get('/admin/users')->assertStatus(200);
        $this->actingAs($adminUser)->get('/admin/commands')->assertStatus(200);
        $this->actingAs($adminUser)->get('/admin/settings')->assertStatus(200);
    }
}
