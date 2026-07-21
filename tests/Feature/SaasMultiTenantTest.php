<?php

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantWebhookSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SaasMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_tenant_data_isolation(): void
    {
        $tenantA = Tenant::create(['name' => 'Company A', 'tenant_token' => 'TOKEN_AAA_111']);
        $tenantB = Tenant::create(['name' => 'Company B', 'tenant_token' => 'TOKEN_BBB_222']);

        // Create devices under different tenants
        $deviceA = Device::create(['tenant_id' => $tenantA->id, 'serial_number' => 'DEV_A100']);
        $deviceB = Device::create(['tenant_id' => $tenantB->id, 'serial_number' => 'DEV_B200']);

        // Scope context to Tenant A
        app()->instance('current_tenant_id', $tenantA->id);

        $scopedDevices = Device::all();

        $this->assertTrue($scopedDevices->contains($deviceA));
        $this->assertFalse($scopedDevices->contains($deviceB));
    }

    public function test_tokenized_adms_endpoint_handshake(): void
    {
        $tenant = Tenant::create(['name' => 'SaaS Subscriber', 'tenant_token' => 'MY_SAAS_TOKEN_123']);

        $response = $this->get('/iclock/MY_SAAS_TOKEN_123/cdata?SN=SAAS_DEV_001&options=all');

        $response->assertStatus(200);
        $this->assertStringContainsString('GET OPTION FROM: SAAS_DEV_001', $response->getContent());

        $this->assertDatabaseHas('devices', [
            'tenant_id' => $tenant->id,
            'serial_number' => 'SAAS_DEV_001',
        ]);
    }

    public function test_subscriber_cannot_access_admin_dashboard_routes(): void
    {
        Role::create(['name' => 'Subscriber']);
        $subscriberUser = User::factory()->create();
        $subscriberUser->assignRole('Subscriber');

        // Attempting to access admin routes should redirect to subscriber dashboard
        $response = $this->actingAs($subscriberUser)->get('/admin/devices');
        $response->assertRedirect(route('subscriber.dashboard'));

        $responseSystem = $this->actingAs($subscriberUser)->get('/admin/system');
        $responseSystem->assertRedirect(route('subscriber.dashboard'));
    }

    public function test_subscriber_can_access_dedicated_subscriber_views(): void
    {
        Role::create(['name' => 'Subscriber']);
        $subscriberUser = User::factory()->create();
        $subscriberUser->assignRole('Subscriber');

        $this->actingAs($subscriberUser)->get('/subscriber/dashboard')->assertStatus(200);
        $this->actingAs($subscriberUser)->get('/subscriber/devices')->assertStatus(200);
        $this->actingAs($subscriberUser)->get('/subscriber/attendance')->assertStatus(200);
        $this->actingAs($subscriberUser)->get('/subscriber/users')->assertStatus(200);
        $this->actingAs($subscriberUser)->get('/subscriber/webhook')->assertStatus(200);
        $this->actingAs($subscriberUser)->get('/subscriber/mock-remote-viewer')->assertStatus(200);
        $this->actingAs($subscriberUser)->get('/subscriber/plans')->assertStatus(200);
    }

    public function test_system_admin_panel_routes_access(): void
    {
        Role::create(['name' => 'System Admin']);
        $sysAdminUser = User::factory()->create();
        $sysAdminUser->assignRole('System Admin');

        $this->actingAs($sysAdminUser)->get('/admin/system/dashboard')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/users')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/roles')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/website')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/monitoring')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/database')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/security')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/gateways')->assertStatus(200);
        $this->actingAs($sysAdminUser)->get('/admin/system/network')->assertStatus(200);
    }

    public function test_subscriber_external_webhook_push_execution(): void
    {
        Http::fake([
            'https://external-erp.test/*' => Http::response(['status' => 'success', 'received' => 1], 200),
        ]);

        Role::create(['name' => 'Subscriber']);
        $subscriberUser = User::factory()->create();
        $subscriberUser->assignRole('Subscriber');

        $tenant = Tenant::create(['name' => 'Push Tenant', 'tenant_token' => 'PUSH_TOKEN_123', 'user_id' => $subscriberUser->id]);
        $subscriberUser->update(['tenant_id' => $tenant->id]);

        TenantWebhookSetting::create([
            'tenant_id' => $tenant->id,
            'endpoint_url' => 'https://external-erp.test/webhook',
            'data_format' => 'json',
            'push_schedule' => 'realtime',
            'auth_type' => 'bearer',
            'auth_token' => 'sample_token_xyz',
            'is_enabled' => true,
        ]);

        $response = $this->actingAs($subscriberUser)->post('/subscriber/webhook/test');

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tenant_push_logs', [
            'tenant_id' => $tenant->id,
            'endpoint_url' => 'https://external-erp.test/webhook',
            'status_code' => 200,
            'is_success' => 1,
        ]);
    }
}
