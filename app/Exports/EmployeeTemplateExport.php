<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Gender;
use App\Models\WorkShift;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeeTemplateExport implements WithMultipleSheets, WithStyles
{
    use Exportable;

    private $tenantId;

    public function __construct(int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function sheets(): array
    {
        return [
            'Import Data' => new EmployeeTemplateSheet(),
            'Reference'   => new ReferenceSheet($this->tenantId),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }
}
