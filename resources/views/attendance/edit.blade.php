@extends('layouts.admin')

@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            Edit Absensi - {{ \Carbon\Carbon::parse($attendanceModel->tanggal)->format('d M Y') }}
        </h6>
        <a href="{{ route('attendance.index', $attendanceModel->project->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <div class="card-body">
        <form action="{{ route('attendance.update', ['project' => $attendanceModel->project->id, 'attendance' => $attendanceModel->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $attendanceModel->tanggal }}" readonly>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Pekerja</th>
                            <th>Hadir</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceModel->attendanceWorkers as $aw)
                            <tr>
                                <td>{{ $aw->projectWorker->nama_worker }}</td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input hadir-check" 
                                               type="checkbox" 
                                               name="kehadiran[{{ $aw->id }}][hadir]" 
                                               id="hadir_{{ $aw->id }}"
                                               value="1"
                                               {{ $aw->hadir ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hadir_{{ $aw->id }}">Hadir</label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" 
                                           name="kehadiran[{{ $aw->id }}][keterangan]" 
                                           class="form-control"
                                           value="{{ $aw->keterangan }}"
                                           placeholder="Keterangan">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data kehadiran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">Update Absensi</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle keterangan field when hadir checkbox is changed
    const hadirChecks = document.querySelectorAll('.hadir-check');

    hadirChecks.forEach(function(check) {
        check.addEventListener('change', function() {
            const row = this.closest('tr');
            const keteranganInput = row.querySelector('input[type="text"]');

            if(this.checked) {
                keteranganInput.disabled = false;
                keteranganInput.placeholder = "Keterangan (opsional)";
            } else {
                keteranganInput.disabled = false; // Tetap aktif agar bisa diisi keterangan ketidakhadiran
                keteranganInput.placeholder = "Keterangan ketidakhadiran";
            }
        });

        // Initialize based on current state
        if(!check.checked) {
            const row = check.closest('tr');
            const keteranganInput = row.querySelector('input[type="text"]');
            keteranganInput.disabled = false; // Tetap aktif agar bisa diisi keterangan ketidakhadiran
            keteranganInput.placeholder = "Keterangan ketidakhadiran";
        }
    });
});
</script>
@endpush