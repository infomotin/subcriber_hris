<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillType;
use App\Models\BillPurpose;
use App\Models\Tenant;

class BillTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        $types = [
            ['name' => 'Travel', 'code' => 'TRAVEL'],
            ['name' => 'Office Supply', 'code' => 'OFFSUP'],
            ['name' => 'Utility Bill', 'code' => 'UTIL'],
            ['name' => 'Communication', 'code' => 'COMM'],
            ['name' => 'Equipment', 'code' => 'EQUIP'],
            ['name' => 'Medical', 'code' => 'MED'],
        ];

        foreach ($types as $type) {
            BillType::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $type['code']],
                ['name' => $type['name'], 'is_active' => true]
            );
        }

        $purposes = [
            ['name' => 'Office Stationery Purchase', 'description' => 'Pens, papers, toner, and other office supplies'],
            ['name' => 'Client Visit Travel', 'description' => 'Travel expenses for client meetings and visits'],
            ['name' => 'Internet & Phone Bill', 'description' => 'Monthly internet and phone line charges'],
            ['name' => 'Team Outing', 'description' => 'Team building and outing expenses'],
            ['name' => 'Software Subscription', 'description' => 'SaaS and software license renewals'],
            ['name' => 'Courier & Postage', 'description' => 'Document delivery and postal charges'],
        ];

        foreach ($purposes as $purpose) {
            BillPurpose::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $purpose['name']],
                ['description' => $purpose['description'], 'is_active' => true]
            );
        }
    }
}
