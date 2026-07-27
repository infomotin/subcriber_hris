<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\MovementType;
use App\Models\MovementMonthlyLimit;
use Illuminate\Database\Seeder;

class MovementTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        $types = [
            [
                'name' => 'Short Leave',
                'code' => 'SL',
                'duration_type' => 'short_leave',
                'max_hours' => 3.0,
                'requires_return' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Day Out',
                'code' => 'DO',
                'duration_type' => 'day_out',
                'max_hours' => 8.0,
                'requires_return' => false,
                'is_active' => true,
            ],
        ];

        foreach ($types as $t) {
            $mt = MovementType::firstOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $t['code']],
                array_merge($t, ['tenant_id' => $tenant->id])
            );

            MovementMonthlyLimit::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'movement_type_id' => $mt->id,
                    'month' => now()->month,
                    'year' => now()->year,
                ],
                ['max_allowed' => 3]
            );
        }
    }
}
