<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudyProgramTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function array(): array
    {
        return [
            ['FTI', 'SI', 'Sistem Informasi', 'S1', 'Dr. Ahmad Fauzi', 'si@university.ac.id', '0274-111222', 'A', '1'],
            ['FTI', 'TI', 'Teknik Informatika', 'S1', 'Dr. Budi Prasetyo', 'ti@university.ac.id', '0274-333444', 'A', '1'],
        ];
    }

    public function headings(): array
    {
        return [
            'kode_fakultas *',
            'kode *',
            'nama *',
            'jenjang * (D3/S1/S2/S3)',
            'nama_kaprodi',
            'email',
            'telepon',
            'akreditasi',
            'status_aktif (1=Aktif, 0=Nonaktif)',
        ];
    }

    public function title(): string
    {
        return 'Template Program Studi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 12,
            'C' => 35,
            'D' => 25,
            'E' => 30,
            'F' => 30,
            'G' => 18,
            'H' => 15,
            'I' => 35,
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
