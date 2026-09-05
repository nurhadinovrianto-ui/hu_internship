<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LecturerTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'NIP',
            'NIDN',
            'Position',
            'Specialization',
            'Max Mentee',
            'Study Program ID'
        ];
    }

    public function array(): array
    {
        return [
            ['Dr. Andi Suryanto', 'andi@example.com', '08111222333', '198001012010121001', '0401018001', 'Lektor', 'Software Engineering', '10', '1'],
        ];
    }
}
