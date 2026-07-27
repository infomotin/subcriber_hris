<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdvanceType;
use App\Models\AdvanceSource;
use App\Models\Tenant;

class AdvanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        $types = [
            ['name' => 'Emergency Advance', 'code' => 'EMERG', 'payment_mode' => 'one_time'],
            ['name' => 'Salary Advance', 'code' => 'SALADV', 'payment_mode' => 'monthly_installment'],
            ['name' => 'Medical Advance', 'code' => 'MEDADV', 'payment_mode' => 'one_time'],
            ['name' => 'Travel Advance', 'code' => 'TRADV', 'payment_mode' => 'one_time'],
            ['name' => 'Education Loan', 'code' => 'EDULOAN', 'payment_mode' => 'monthly_installment'],
        ];

        foreach ($types as $type) {
            AdvanceType::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $type['code']],
                ['name' => $type['name'], 'payment_mode' => $type['payment_mode'], 'is_active' => true]
            );
        }

        $sources = [
            ['name' => 'Salary Deduction', 'code' => 'SALARY'],
            ['name' => 'Manual Payment', 'code' => 'MANUAL'],
        ];

        foreach ($sources as $source) {
            AdvanceSource::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $source['code']],
                ['name' => $source['name'], 'is_active' => true]
            );
        }
    }
}
