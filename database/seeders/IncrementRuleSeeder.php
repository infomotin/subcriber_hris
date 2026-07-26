<?php

namespace Database\Seeders;

use App\Models\IncrementRule;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class IncrementRuleSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $tenant = Tenant::create(['name' => 'Demo SaaS Subscriber', 'tenant_token' => 'DEMO_SUB_TOKEN_123']);
            $tenants = collect([$tenant]);
        }

        foreach ($tenants as $tenant) {
            $rules = [
                [
                    'name' => 'Annual Increment FY 2026-27',
                    'joining_date_from' => null,
                    'joining_date_to' => null,
                    'increment_based_on' => 'basic',
                    'year_start_date' => '2026-07-01',
                    'special_max_percentage' => null,
                    'is_active' => true,
                ],
                [
                    'name' => 'Annual Increment FY 2025-26',
                    'joining_date_from' => '2024-01-01',
                    'joining_date_to' => '2025-06-30',
                    'increment_based_on' => 'basic',
                    'year_start_date' => '2025-07-01',
                    'special_max_percentage' => null,
                    'is_active' => false,
                ],
                [
                    'name' => 'Special Performance Bonus',
                    'joining_date_from' => null,
                    'joining_date_to' => null,
                    'increment_based_on' => 'gross',
                    'year_start_date' => null,
                    'special_max_percentage' => 20.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Merit Based Increment - Staff',
                    'joining_date_from' => null,
                    'joining_date_to' => null,
                    'increment_based_on' => 'basic',
                    'year_start_date' => null,
                    'special_max_percentage' => 15.00,
                    'is_active' => true,
                ],
                [
                    'name' => 'Bulk Annual Adjustment',
                    'joining_date_from' => '2025-01-01',
                    'joining_date_to' => '2026-06-30',
                    'increment_based_on' => 'gross',
                    'year_start_date' => '2026-01-01',
                    'special_max_percentage' => null,
                    'is_active' => true,
                ],
            ];

            foreach ($rules as $rule) {
                IncrementRule::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $rule['name']],
                    $rule
                );
            }
        }
    }
}
