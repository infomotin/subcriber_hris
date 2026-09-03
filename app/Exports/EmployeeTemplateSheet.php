<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeeTemplateSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $example = [
            'Rahim Ahmed',
            'rahim@example.com',
            'Password123',
            'EMP-1054',
            'Marketing',
            'Executive',
            '2024-01-15',
            'Male',
            '01712345678',
            '1990-05-20',
            'A+',
            'Islam',
            'Single',
            'House 12, Road 4, Apt 4B',
            'Dhaka',
            'Dhaka',
            'Dhanmondi',
            '1205',
            'Bangladesh',
            'Dhaka',
            'Dhaka',
            'Dhanmondi',
            '',
            '',
            'Worker',
            'Active',
            '',
            '',
            '',
            '50000',
            '20000',
            '10000',
            '5000',
            '0',
            '0',
            '0',
            '1',
            '250',
            'Dutch-Bangla Bank',
            'Dhanmondi Branch',
            'Rahim Ahmed',
            '1234567890',
            '',
            'bank_transfer',
            'Karim Ahmed',
            'Businessman',
            'Fatema Begum',
            'Homemaker',
            'Kamal Hossain',
            'Uncle',
            '01812345678',
        ];

        return [$example];
    }

    public function headings(): array
    {
        return [
            'Full Name *',
            'Email (Login) *',
            'Password *',
            'Employee ID *',
            'Department *',
            'Designation *',
            'Joining Date *',
            'Gender *',
            'Phone Number *',
            'Date of Birth *',
            'Blood Group',
            'Religion',
            'Marital Status',
            'Address Line *',
            'State / Division *',
            'District *',
            'Thana / Upazila *',
            'Zip Code *',
            'Country',
            'Permanent Address',
            'Permanent State',
            'Permanent District',
            'Permanent Zip',
            'NID Number',
            'Employee Type',
            'Status *',
            'Overtime Eligible (1/0)',
            'Overtime Rate (BDT/hr)',
            'Bank Name *',
            'Branch Name *',
            'Account Holder Name *',
            'Account Number *',
            'Routing Number',
            'Payment Mode *',
            'Father Name',
            'Father Occupation',
            'Mother Name',
            'Mother Occupation',
            'Guardian Name',
            'Guardian Relation',
            'Guardian Phone',
        ];
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
                    'startColor' => ['rgb' => '5F5AF6'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'border' => [
                    'borderType' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }
}
