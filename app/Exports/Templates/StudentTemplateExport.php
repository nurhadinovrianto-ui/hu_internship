<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'NIM',
            'Batch',
            'Current Semester',
            'Total SKS',
            'GPA',
            'Study Program ID'
        ];
    }

    public function array(): array
    {
        return [
            ['Budi Santoso', 'budi@example.com', '08123456789', '20230001', '2023', '6', '120', '3.50', '1'],
        ];
    }
}
