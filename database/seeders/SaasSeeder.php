<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\PaymentLog;
use App\Models\SmsLog;
use App\Models\SubscriptionPlan;
use App\Models\SystemLog;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ZktecoUser;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SaasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Spatie Roles
        $roleSystemAdmin = Role::firstOrCreate(['name' => 'System Admin']);
        $roleBusinessAdmin = Role::firstOrCreate(['name' => 'Business Admin']);
        $roleSubscriber = Role::firstOrCreate(['name' => 'Subscriber']);
        $roleDemo = Role::firstOrCreate(['name' => 'Demo User']);

        // 2. Create Subscription Plans
        $planStarter = SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Plan',
                'price_monthly' => 2000.00,
                'price_yearly' => 20000.00,
                'max_devices' => 2,
                'description' => 'Ideal for small offices with up to 2 ZKTeco machines.',
                'features' => ['2 ZKTeco Devices', 'Realtime Attendance Push', 'CSV Reports Export', 'Basic Support'],
                'status' => 'active',
            ]
        );

        $planPro = SubscriptionPlan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Business Pro',
                'price_monthly' => 5000.00,
                'price_yearly' => 50000.00,
                'max_devices' => 10,
                'description' => 'Perfect for multi-location companies with up to 10 machines.',
                'features' => ['10 ZKTeco Devices', 'Realtime Punch Alerts', 'SMS Notifications', 'Priority Support'],
                'status' => 'active',
            ]
        );

        $planEnterprise = SubscriptionPlan::firstOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise Unlimited',
                'price_monthly' => 12000.00,
                'price_yearly' => 120000.00,
                'max_devices' => 50,
                'description' => 'For large enterprises with multiple branches and unlimited biometric machines.',
                'features' => ['50 ZKTeco Devices', 'Dedicated ADMS Gateway', '24/7 SLA Support', 'Custom API Integration'],
                'status' => 'active',
            ]
        );

        // 3. Create System Admin Account
        $systemAdminUser = User::firstOrCreate(
            ['email' => 'sysadmin@amds.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $systemAdminUser->syncRoles([$roleSystemAdmin]);

        // 4. Create Business Admin Account
        $businessAdminUser = User::firstOrCreate(
            ['email' => 'business@amds.test'],
            [
                'name' => 'Business Manager',
                'password' => Hash::make('password'),
            ]
        );
        $businessAdminUser->syncRoles([$roleBusinessAdmin]);

        // 5. Create Subscriber Account 1: Acme Corporation
        $acmeUser = User::firstOrCreate(
            ['email' => 'subscriber@amds.test'],
            [
                'name' => 'Acme Corporation Admin',
                'password' => Hash::make('password'),
            ]
        );
        $acmeUser->syncRoles([$roleSubscriber]);

        $acmeTenant = Tenant::firstOrCreate(
            ['slug' => 'acme-corp'],
            [
                'name' => 'Acme Corporation',
                'user_id' => $acmeUser->id,
                'tenant_token' => 'ACME1234567890TOKEN',
                'subscription_plan_id' => $planPro->id,
                'status' => 'active',
                'expires_at' => now()->addYear(),
                'max_devices' => 10,
            ]
        );
        $acmeUser->update(['tenant_id' => $acmeTenant->id]);

        // Seed Devices for Acme Corp
        $device1 = Device::firstOrCreate(
            ['serial_number' => 'ZKT-ACME-001'],
            [
                'tenant_id' => $acmeTenant->id,
                'name' => 'Main Gate Entrance',
                'ip_address' => '192.168.1.101',
                'status' => 'online',
                'last_heartbeat' => now(),
                'user_count' => 25,
                'att_count' => 750,
            ]
        );

        $device2 = Device::firstOrCreate(
            ['serial_number' => 'ZKT-ACME-002'],
            [
                'tenant_id' => $acmeTenant->id,
                'name' => '2nd Floor Office Door',
                'ip_address' => '192.168.1.102',
                'status' => 'online',
                'last_heartbeat' => now()->subMinute(),
                'user_count' => 25,
                'att_count' => 320,
            ]
        );

        // 6. Seed 25 Biometric Users for Acme Corp
        $userNames = [
            'Rahim Ahmed', 'Karim Hasan', 'Tanvir Islam', 'Nusrat Jahan', 'Mahmudur Rahman',
            'Sultana Razia', 'Arif Hossain', 'Farhana Chowdhury', 'Kamrul Islam', 'Nadia Sharmin',
            'Sabbir Rahman', 'Rashedul Karim', 'Tariqul Islam', 'Sadia Afrin', 'Mehedi Hasan',
            'Shuvo Roy', 'Mitu Akter', 'Imran Hossain', 'Bipasha Sen', 'Zahidul Alam',
            'Sharmin Sultana', 'Asif Iqbal', 'Nazmun Nahar', 'Shahriar Kabir', 'Ferdous Wahid',
        ];

        $zkUsers = [];
        foreach ($userNames as $index => $name) {
            $pin = (string) (1001 + $index);
            $zkUsers[] = ZktecoUser::firstOrCreate(
                ['tenant_id' => $acmeTenant->id, 'pin' => $pin],
                [
                    'device_id' => $device1->id,
                    'name' => $name,
                    'card_number' => '000109' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'privilege' => $index === 0 ? 14 : 0,
                    'is_synced' => true,
                ]
            );
        }

        // 7. Seed 15 Days of Attendance Punch Data for all 25 Users
        for ($day = 15; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);

            // Skip weekends for realistic work attendance
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($zkUsers as $index => $zkUser) {
                // Check In (~ 08:45 AM - 09:15 AM)
                $checkInTime = (clone $date)->setTime(8, 45, 0)->addMinutes(($index * 3) % 30);
                AttendanceLog::firstOrCreate(
                    [
                        'tenant_id' => $acmeTenant->id,
                        'device_id' => $device1->id,
                        'pin' => $zkUser->pin,
                        'punched_at' => $checkInTime->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 0, // Check In
                        'verify_type' => $index % 2 === 0 ? 1 : 15, // Fingerprint or Face
                        'work_code' => 0,
                    ]
                );

                // Check Out (~ 05:00 PM - 05:30 PM)
                $checkOutTime = (clone $date)->setTime(17, 0, 0)->addMinutes(($index * 2) % 30);
                AttendanceLog::firstOrCreate(
                    [
                        'tenant_id' => $acmeTenant->id,
                        'device_id' => $device1->id,
                        'pin' => $zkUser->pin,
                        'punched_at' => $checkOutTime->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 1, // Check Out
                        'verify_type' => $index % 2 === 0 ? 1 : 15,
                        'work_code' => 0,
                    ]
                );
            }
        }

        // 8. Create Demo Account
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@amds.test'],
            [
                'name' => 'Public Demo Sandbox User',
                'password' => Hash::make('password'),
            ]
        );
        $demoUser->syncRoles([$roleDemo]);

        $demoTenant = Tenant::firstOrCreate(
            ['slug' => 'demo-sandbox'],
            [
                'name' => 'Public Demo Sandbox',
                'user_id' => $demoUser->id,
                'tenant_token' => 'DEMOSANDBOXTOKEN123',
                'subscription_plan_id' => $planStarter->id,
                'status' => 'active',
                'expires_at' => now()->addDays(7),
                'max_devices' => 2,
                'is_demo' => true,
            ]
        );
        $demoUser->update(['tenant_id' => $demoTenant->id]);

        SystemLog::log('Seeded 25 biometric users and 15 days of attendance logs successfully.', 'info');
    }
}
