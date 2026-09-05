<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\AcademicPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $period;

    public function __construct(AcademicPeriod $period)
    {
        $this->period = $period;
    }

    public function collection()
    {
        return Student::with(['user', 'studyProgram', 'requirements' => function($q) {
            $q->where('academic_period_id', $this->period->id);
        }])->get();
    }

    public function map($student): array
    {
        $requirement = $student->requirements->first();
        
        $status = 'Belum Lunas';
        $verifiedAt = '-';
        if ($requirement && $requirement->payment_cleared) {
            $status = 'Lunas';
            $verifiedAt = $requirement->payment_verified_at ? \Carbon\Carbon::parse($requirement->payment_verified_at)->format('d-m-Y') : '-';
        }

        return [
            $student->user->name,
            $student->nim,
            $student->studyProgram->name ?? '-',
            $status,
            $verifiedAt,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'NIM',
            'Program Studi',
            'Status Pembayaran',
            'Tanggal Verifikasi',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F172A']]
            ],
        ];
    }
}
