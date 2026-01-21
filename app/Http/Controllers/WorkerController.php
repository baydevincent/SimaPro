<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = Worker::latest()->get();
        return view('worker.index', compact('workers'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_worker' => 'required|string|max:255',
            'jabatan'     => 'nullable|string|max:255',
            'no_hp'       => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $worker = Worker::create([
            'nama_worker' => $request->nama_worker,
            'jabatan'     => $request->jabatan,
            'no_hp'       => $request->no_hp,
            'aktif'       => true, // ⬅️ DEFAULT AKTIF
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Worker berhasil disimpan',
            'data'    => $worker
        ], 201);
    }

    public function toggle(Task $task)
    {
        $task->update(['is_done' => !$task->is_done]);
        return back();
    }

    public function destroy(Worker $worker)
    {
        $worker->delete();

        return response()->json([
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }
}
