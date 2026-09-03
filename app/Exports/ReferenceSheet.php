<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Gender;
use App\Models\WorkShift;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReferenceSheet implements FromCollection, WithHeadings, WithStyles
{
    private $tenantId;

    public function __construct(int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function collection()
    {
        $departments = Department::orderBy('name')->pluck('name')->implode(', ');
        $designations = Designation::orderBy('title')->pluck('title')->implode(', ');
        $divisions = Division::orderBy('name')->get();
        $divStr = $divisions->pluck('name')->implode(', ');

        $districtStr = '';
        foreach ($divisions as $div) {
            $districtStr .= $div->name . ': ' . $div->districts->pluck('name')->implode(', ') . ' | ';
        }
        $districtStr = rtrim($districtStr, ' | ');

        $thanaStr = '';
        foreach ($divisions as $div) {
            foreach ($div->districts as $dist) {
                if ($dist->thanas->count()) {
                    $thanaStr .= $dist->name . ': ' . $dist->thanas->pluck('name')->implode(', ') . ' | ';
                }
            }
        }
        $thanaStr = rtrim($thanaStr, ' | ');

        $shifts = WorkShift::orderBy('name')->pluck('name')->implode(', ');
        $genders = Gender::orderBy('name')->pluck('name')->implode(', ');

        $rows = [
            ['Departments', $departments],
            ['Designations', $designations],
            ['States / Divisions', $divStr],
            ['Districts (Division: District1, District2 | ...)', $districtStr],
            ['Thanas (District: Thana1, Thana2 | ...)', $thanaStr],
            ['Work Shifts', $shifts],
            ['Genders', $genders],
            ['Blood Groups', 'A+, A-, B+, B-, AB+, AB-, O+, O-'],
            ['Religions', 'Islam, Hinduism, Christianity, Buddhism, Other'],
            ['Marital Status', 'Single, Married, Divorced, Widowed'],
            ['Employee Types', 'worker, staff, manager'],
            ['Statuses', 'active, probation, terminated, resigned'],
            ['Payment Modes', 'bank_transfer, cash, mobile_banking'],
            ['Joining Date Format', 'YYYY-MM-DD (e.g. 2024-01-15)'],
            ['DOB Format', 'YYYY-MM-DD (e.g. 1990-05-20)'],
            ['', ''],
            ['RULES', ''],
            ['Required Fields (*)', 'Must be filled. Rows with missing required fields will be skipped.'],
            ['Email', 'Must be unique. Duplicate emails will be skipped.'],
            ['Employee ID', 'Must be unique per tenant. Duplicates will be skipped.'],
            ['Password', 'Minimum 8 characters.'],
            ['Department / Designation', 'Must match an existing name exactly (case-insensitive).'],
            ['State / Division', 'Must match an existing Division name.'],
            ['District', 'Must match an existing District name under the given Division.'],
            ['Thana', 'Must match an existing Thana name under the given District.'],
            ['Joining Date / DOB', 'Use YYYY-MM-DD format.'],
            ['Status', 'Must be one of: active, probation, terminated, resigned.'],
            ['Employee Type', 'Must be one of: worker, staff, manager (or leave empty).'],
            ['Payment Mode', 'Must be one of: bank_transfer, cash, mobile_banking.'],
            ['Overtime Eligible', 'Use 1 for yes, 0 for no (or leave empty for no).'],
        ];

        return $rows;
    }

    public function headings(): array
    {
        return ['Field / Rule', 'Valid Values / Notes'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
