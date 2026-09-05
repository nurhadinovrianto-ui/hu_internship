<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IndustrySupervisorTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Industry ID',
            'Position',
            'Division'
        ];
    }

    public function array(): array
    {
        return [
            ['Siti Aminah', 'siti@company.com', '081299998888', '1', 'Senior Developer', 'IT Department'],
        ];
    }
}
