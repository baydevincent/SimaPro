<?php

namespace App\Imports;

use App\Models\ProjectWorker;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class WorkersImport implements ToModel, WithHeadingRow, WithMapping, WithValidation, SkipsEmptyRows, WithLimit, WithEvents
{
    protected $project;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validasi nama worker tidak boleh kosong
        if (empty($row['nama_worker'])) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $this->importedCount + $this->skippedCount,
                'error' => 'Nama worker kosong'
            ];
            return null;
        }

        // Cek duplikasi nama worker di project yang sama
        $existingWorker = ProjectWorker::where('project_id', $this->project->id)
            ->where('nama_worker', $row['nama_worker'])
            ->first();

        if ($existingWorker) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $this->importedCount + $this->skippedCount,
                'nama_worker' => $row['nama_worker'],
                'error' => 'Nama worker sudah ada di project ini'
            ];
            return null;
        }

        $this->importedCount++;

        return new ProjectWorker([
            'project_id'    => $this->project->id,
            'nama_worker'   => $row['nama_worker'],
            'posisi'        => $row['posisi'] ?? null,
            'no_hp'         => $row['no_hp'] ?? null,
            'aktif'         => isset($row['aktif']) ? ($row['aktif'] == 1 || strtolower($row['aktif']) === 'ya' || strtolower($row['aktif']) === 'true' || strtolower($row['aktif']) === 'yes') : true,
        ]);
    }

    /**
     * @param array $row
     * @return array
     */
    public function map($row): array
    {
        $this->importedCount++;

        return [
            'nama_worker'   => $row['nama_worker'] ?? null,
            'posisi'        => $row['posisi'] ?? null,
            'no_hp'         => $row['no_hp'] ?? null,
            'aktif'         => $row['aktif'] ?? 1,
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_worker'   => 'required|string|max:255',
            'posisi'        => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:20',
            'aktif'         => 'nullable',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_worker.required' => 'Nama worker wajib diisi',
            'nama_worker.max' => 'Nama worker maksimal 255 karakter',
            'posisi.max' => 'Posisi maksimal 255 karakter',
            'no_hp.max' => 'No HP maksimal 20 karakter',
        ];
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return 1000; // Max 1000 rows per import
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                // Log setelah import selesai
            },
        ];
    }

    /**
     * Get import stats
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get skipped count
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
