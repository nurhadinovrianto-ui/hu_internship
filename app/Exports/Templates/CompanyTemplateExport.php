<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function array(): array
    {
        return [
            ['PT001', 'PT Maju Bersama', 'swasta', 'Teknologi Informasi', 'menengah', 'Jl. Raya Kemerdekaan No.1', 'Yogyakarta', 'DI Yogyakarta', '0274-999888', 'hr@majubersama.com', 'https://majubersama.com', 'Rina Dewi', 'HRD Manager', '081999888777', 'rina@majubersama.com', '10', 'Perusahaan software house'],
            ['BUMN001', 'PT Telkom Indonesia', 'bumn', 'Telekomunikasi', 'besar', 'Jl. Gatot Subroto Kav.52', 'Jakarta', 'DKI Jakarta', '021-5215109', 'info@telkom.co.id', 'https://telkom.co.id', 'Ahmad Fauzi', 'Campus Hiring Lead', '082111222333', 'ahmad.fauzi@telkom.co.id', '20', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'kode *',
            'nama *',
            'tipe * (swasta/bumn/pemerintah/startup/ngo)',
            'sektor_industri *',
            'skala * (kecil/menengah/besar)',
            'alamat *',
            'kota *',
            'provinsi *',
            'telepon *',
            'email *',
            'website',
            'nama_pic *',
            'jabatan_pic *',
            'telepon_pic *',
            'email_pic',
            'max_mahasiswa *',
            'catatan',
        ];
    }

    public function title(): string
    {
        return 'Template Perusahaan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 30,
            'C' => 35,
            'D' => 25,
            'E' => 30,
            'F' => 35,
            'G' => 20,
            'H' => 20,
            'I' => 18,
            'J' => 30,
            'K' => 30,
            'L' => 25,
            'M' => 25,
            'N' => 18,
            'O' => 30,
            'P' => 18,
            'Q' => 35,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DC2626']],
            ],
            2 => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEF2F2']]],
            3 => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEF2F2']]],
        ];
    }
}
