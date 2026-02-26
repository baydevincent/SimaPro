<?php

namespace App\Imports;

use App\Models\Task;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Facades\Auth;

class TasksImport implements ToModel, WithHeadingRow, WithMapping, WithValidation, SkipsEmptyRows, WithLimit, WithEvents
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
        // Cek duplikasi nama task
        $existingTask = Task::where('project_id', $this->project->id)
            ->where('nama_task', $row['nama_task'])
            ->first();

        if ($existingTask) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $this->importedCount + $this->skippedCount,
                'nama_task' => $row['nama_task'],
                'error' => 'Nama task sudah ada'
            ];
            return null;
        }

        $this->importedCount++;

        return new Task([
            'project_id'      => $this->project->id,
            'nama_task'       => $row['nama_task'],
            'bobot_rupiah'    => $row['bobot_rupiah'] ?? 0,
            'is_done'         => isset($row['is_done']) ? ($row['is_done'] == 1 || strtolower($row['is_done']) === 'ya' || strtolower($row['is_done']) === 'true') : false,
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
            'nama_task'       => $row['nama_task'] ?? null,
            'bobot_rupiah'    => $row['bobot_rupiah'] ?? 0,
            'is_done'         => $row['is_done'] ?? 0,
            'tanggal_mulai'   => $row['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $row['tanggal_selesai'] ?? null,
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_task'    => 'required|string|max:255',
            'bobot_rupiah' => 'nullable|numeric|min:0',
            'is_done'      => 'nullable',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_task.required' => 'Nama task wajib diisi',
            'nama_task.max' => 'Nama task maksimal 255 karakter',
            'bobot_rupiah.numeric' => 'Bobot rupiah harus berupa angka',
            'bobot_rupiah.min' => 'Bobot rupiah tidak boleh negatif',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid',
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
     * Parse date dari Excel
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Jika sudah format date
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        // Jika angka (Excel date serial)
        if (is_numeric($value)) {
            $unixDate = ($value - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        }

        // Jika string, coba parse
        try {
            return date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
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
