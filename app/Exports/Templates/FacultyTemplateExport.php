<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacultyTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function array(): array
    {
        return [
            ['FTI', 'Fakultas Teknologi Informasi', 'Dr. Budi Santoso', 'fti@university.ac.id', '0274-123456', '1'],
            ['FEB', 'Fakultas Ekonomi dan Bisnis', 'Dr. Ani Wulandari', 'feb@university.ac.id', '0274-234567', '1'],
        ];
    }

    public function headings(): array
    {
        return [
            'kode *',
            'nama *',
            'nama_dekan',
            'email',
            'telepon',
            'status_aktif (1=Aktif, 0=Nonaktif)',
        ];
    }

    public function title(): string
    {
        return 'Template Fakultas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 40,
            'C' => 30,
            'D' => 30,
            'E' => 18,
            'F' => 35,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
            ],
            2 => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EEF2FF']]],
            3 => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EEF2FF']]],
        ];
    }
}
