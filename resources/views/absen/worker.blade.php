@extends('layouts.admin')

@section('main-content')
<h5>Worker Project: {{ $project->nama_project }}</h5>

{{-- FORM TAMBAH WORKER --}}
<form method="POST" action="{{ route('project.workers.store', $project->id) }}">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <select name="worker_id" class="form-control" required>
                <option value="">-- Pilih Worker --</option>
                @foreach($workers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Tambah</button>
        </div>
    </div>
</form>

<hr>

{{-- LIST WORKER PROJECT --}}
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Worker</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($project->workers as $worker)
        <tr>
            <td>{{ $worker->nama_worker }}</td>
            <td>
                <form method="POST"
                      action="{{ route('project.workers.destroy', [$project->id, $worker->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2" class="text-center text-muted">
                Belum ada worker di project
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
