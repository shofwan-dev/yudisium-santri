<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\ClassRoom;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentsTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    /**
     * Export existing students data for editing/updating.
     */
    public function array(): array
    {
        $students = Student::with('classRoom')->orderBy('kelas_id')->orderBy('no')->get();

        return $students->map(function ($s) {
            return [
                $s->id,
                $s->no,
                $s->nama,
                $s->classRoom?->nama_kelas,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['id', 'no', 'nama', 'kelas'];
    }

    public function title(): string
    {
        return 'Data Santri';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Header styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Data area borders
        if ($lastRow > 1) {
            $sheet->getStyle("A2:D{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);
        }

        // ID column note
        $sheet->getComment('A1')->getText()->createTextRun(
            "Kolom ID wajib diisi untuk UPDATE data yang sudah ada.\nKosongkan ID untuk menambah data santri BARU."
        );

        // Kelas column note
        $sheet->getComment('D1')->getText()->createTextRun(
            "Isi dengan nama kelas yang sudah terdaftar.\nContoh: 6B, 6C, 6D"
        );

        return [];
    }
}
