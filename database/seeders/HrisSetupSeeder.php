<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\Gender;
use App\Models\EducationBoard;
use App\Models\LeaveReason;
use App\Models\SalaryRelation;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class HrisSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Bangladesh Address Hierarchy
        $divisionsData = [
            'Dhaka' => [
                'Dhaka' => ['Mirpur', 'Uttara', 'Gulshan', 'Dhanmondi', 'Motijheel', 'Savar'],
                'Gazipur' => ['Gazipur Sadar', 'Kaliakair', 'Sreepur', 'Tongi'],
                'Narayanganj' => ['Narayanganj Sadar', 'Rupganj', 'Araihazar']
            ],
            'Chittagong' => [
                'Chittagong' => ['Double Mooring', 'Halishahar', 'Panchlaish', 'Kotwali', 'Hathazari'],
                'Cox\'s Bazar' => ['Cox\'s Bazar Sadar', 'Teknaf', 'Ukhia', 'Ramu']
            ],
            'Rajshahi' => [
                'Rajshahi' => ['Boalia', 'Rajpara', 'Paba', 'Godagari'],
                'Bogra' => ['Bogra Sadar', 'Shajahanpur', 'Sherpur', 'Kahaloo']
            ],
            'Khulna' => [
                'Khulna' => ['Khulna Sadar', 'Daulatpur', 'Sonadanga', 'Khalishpur'],
                'Jessore' => ['Jessore Sadar', 'Jhikargachha', 'Sharsha']
            ],
            'Sylhet' => [
                'Sylhet' => ['Sylhet Sadar', 'Beanibazar', 'Golapganj', 'Fenchuganj'],
                'Moulvibazar' => ['Moulvibazar Sadar', 'Sreemangal', 'Kulaura']
            ],
            'Barisal' => [
                'Barisal' => ['Barisal Sadar', 'Babuganj', 'Bakerganj']
            ],
            'Rangpur' => [
                'Rangpur' => ['Rangpur Sadar', 'Mithapukur', 'Pirganj']
            ],
            'Mymensingh' => [
                'Mymensingh' => ['Mymensingh Sadar', 'Muktagachha', 'Trishal']
            ]
        ];

        foreach ($divisionsData as $divName => $districts) {
            $division = Division::firstOrCreate(['name' => $divName]);

            foreach ($districts as $distName => $thanas) {
                $district = District::firstOrCreate([
                    'division_id' => $division->id,
                    'name' => $distName
                ]);

                foreach ($thanas as $thanaName) {
                    Thana::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $thanaName
                    ]);
                }
            }
        }

        // 2. Tenant Specific default configurations
        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            // Create fallback if none exists
            $tenant = Tenant::create(['name' => 'Demo SaaS Subscriber', 'tenant_token' => 'DEMO_SUB_TOKEN_123']);
            $tenants = collect([$tenant]);
        }

        foreach ($tenants as $tenant) {
            // Seed Sex/Genders
            $genders = ['Male', 'Female', 'Other'];
            foreach ($genders as $g) {
                Gender::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'name' => $g
                ]);
            }

            // Seed Education Boards
            $boards = ['Dhaka', 'Chittagong', 'Rajshahi', 'Comilla', 'Jessore', 'Sylhet', 'Barisal', 'Dinajpur', 'Mymensingh', 'Madrasah', 'Technical'];
            foreach ($boards as $b) {
                EducationBoard::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'name' => $b
                ]);
            }

            // Seed Leave Reasons
            $reasons = ['Medical / Illness', 'Casual / Personal Work', 'Family Emergency', 'Maternity / Paternity', 'Marriage Ceremony', 'Bereavement Leave'];
            foreach ($reasons as $r) {
                LeaveReason::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'reason' => $r
                ]);
            }

            // Seed Salary Relation (HR Option Master Role)
            SalaryRelation::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => 'Standard Salary Structure Matrix'],
                [
                    'basic_percent' => 50.00,
                    'house_rent_percent' => 25.00,
                    'medical_percent' => 10.00,
                    'tada_percent' => 15.00, // Conveyance TA/DA
                    'is_active' => true
                ]
            );
        }
    }
}
