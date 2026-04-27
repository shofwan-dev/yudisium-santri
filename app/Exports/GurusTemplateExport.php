<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GurusTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    /**
     * Export existing guru data for editing/updating.
     */
    public function array(): array
    {
        $gurus = User::role('guru')->orderBy('name')->get();

        return $gurus->map(function ($g) {
            return [
                $g->id,
                $g->name,
                $g->email,
                '', // Password kosong (hanya diisi jika ingin ganti)
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['id', 'nama', 'email', 'password'];
    }

    public function title(): string
    {
        return 'Data Guru';
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
            "Kolom ID wajib diisi untuk UPDATE data guru yang sudah ada.\nKosongkan ID untuk menambah guru BARU."
        );

        // Password column note
        $sheet->getComment('D1')->getText()->createTextRun(
            "Kolom password bersifat opsional untuk UPDATE.\nWajib diisi untuk guru BARU.\nKosongkan jika tidak ingin mengubah password."
        );

        return [];
    }
}
