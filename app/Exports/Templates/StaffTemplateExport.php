<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StaffTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Role'
        ];
    }

    public function array(): array
    {
        return [
            ['Admin Keuangan', 'finance@example.com', '08555666777', 'finance'],
            ['Staf BAAK', 'baak@example.com', '08555888999', 'baak'],
        ];
    }
}
