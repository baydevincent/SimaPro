<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Data contoh
        return collect([
            [
                'nama_task'       => 'Task Contoh 1',
                'bobot_rupiah'    => 1000000,
                'is_done'         => 0,
            ],
            [
                'nama_task'       => 'Task Contoh 2',
                'bobot_rupiah'    => 2500000,
                'is_done'         => 0,
            ],
            [
                'nama_task'       => 'Task Contoh 3',
                'bobot_rupiah'    => 1500000,
                'is_done'         => 0,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Task *',
            'Bobot Rupiah',
            'Is Done (0/1)',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['nama_task'],
            $row['bobot_rupiah'],
            $row['is_done'],
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4287f5'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style data rows
        $sheet->getStyle('A2:E4')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Style untuk kolom bobot rupiah (angka)
        $sheet->getStyle('B2:B4')->applyFromArray([
            'numberFormat' => [
                'formatCode' => '#,##0',
            ],
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Template Tasks';
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'E' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
