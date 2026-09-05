<?php

namespace App\Exports;

use App\Models\GradeConversion;
use App\Models\Internship;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GradeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return GradeConversion::with([
            'internship.student.user',
            'internship.student.studyProgram',
            'internship.vacancy.industry',
            'internship.dplAssignment.lecturer.user'
        ])
        ->where('status', 'finalized')
        ->latest()
        ->get();
    }

    public function map($conversion): array
    {
        $internship = $conversion->internship;
        
        return [
            $internship->student->user->name,
            $internship->student->nim,
            $internship->student->studyProgram->name ?? '-',
            $internship->vacancy->industry->name,
            $internship->dplAssignment->lecturer->user->name ?? '-',
            $conversion->total_hours,
            $conversion->credits_earned,
            $conversion->final_score,
            $conversion->letter_grade,
            \Carbon\Carbon::parse($conversion->updated_at)->format('d-m-Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'NIM',
            'Program Studi',
            'Perusahaan Mitra',
            'DPL',
            'Total Jam Magang',
            'SKS Dikonversi',
            'Nilai Akhir',
            'Huruf Mutu',
            'Tgl Finalisasi BAAK',
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
