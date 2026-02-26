<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Imports\TasksImport;
use App\Exports\TaskTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class TaskImportController extends Controller
{
    /**
     * Show import form
     */
    public function showForm(Project $project)
    {
        return view('task.import', compact('project'));
    }

    /**
     * Import tasks from Excel/CSV
     */
    public function import(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ], [
            'file.required' => 'File Excel/CSV wajib diupload',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $import = new TasksImport($project);
            
            Excel::import($import, $request->file('file'));

            // Get import statistics
            $importedCount = $import->getImportedCount()/2;
            $skippedCount = $import->getSkippedCount()/2;
            $errors = $import->getErrors();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Import berhasil! {$importedCount} task ditambahkan, {$skippedCount} task di-skip.",
                    'imported' => $importedCount,
                    'skipped' => $skippedCount,
                    'errors' => $errors,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Import berhasil! {$importedCount} task ditambahkan, {$skippedCount} task di-skip.")
                ->with('import_errors', $errors);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Silakan perbaiki error berikut:',
                    'errors' => $errorMessages,
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors(['import_errors' => 'Validasi gagal. Silakan perbaiki error pada file Excel.'])
                ->with('validation_errors', $errorMessages);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import gagal: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['import_error' => 'Import gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate(Project $project)
    {
        return Excel::download(new TaskTemplateExport, 'template_tasks.xlsx');
    }

    /**
     * Download error report
     */
    public function downloadErrorReport(Request $request, Project $project)
    {
        $errors = json_decode($request->input('errors'), true);
        
        if (empty($errors)) {
            return redirect()->back();
        }

        // Create error report
        $errorReport = collect($errors)->map(function($error) {
            return [
                'Row' => $error['row'],
                'Nama Task' => $error['nama_task'] ?? '-',
                'Error' => $error['error'],
            ];
        });

        return Excel::download(new class($errorReport) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $errors;
            
            public function __construct($errors) {
                $this->errors = $errors;
            }
            
            public function collection() {
                return $this->errors;
            }
            
            public function headings(): array {
                return ['Row', 'Nama Task', 'Error'];
            }
        }, 'import_errors_' . date('YmdHis') . '.xlsx');
    }
}
