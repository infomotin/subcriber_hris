<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GatewayConfigSeeder::class,
            SaasSeeder::class,
            HrisSetupSeeder::class,
            HrisEmployeeSeeder::class,
            HrisMoreEmployeeSeeder::class,
            IncrementRuleSeeder::class,
            IncrementSeeder::class,
        ]);
    }
}
