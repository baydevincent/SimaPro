<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class WorkerTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Contoh data template
        return new Collection([
            [
                'nama_worker' => 'John Doe',
                'posisi' => 'Mandor',
                'no_hp' => '081234567890',
                'aktif' => 1,
            ],
            [
                'nama_worker' => 'Jane Smith',
                'posisi' => 'Tukang Batu',
                'no_hp' => '081234567891',
                'aktif' => 1,
            ],
            [
                'nama_worker' => 'Bob Wilson',
                'posisi' => 'Tukang Kayu',
                'no_hp' => '081234567892',
                'aktif' => 0,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Worker',
            'Posisi',
            'No HP',
            'Aktif',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['nama_worker'],
            $row['posisi'],
            $row['no_hp'],
            $row['aktif'],
        ];
    }
}
