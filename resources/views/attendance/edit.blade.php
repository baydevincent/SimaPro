@extends('layouts.admin')

@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            Edit Absensi - {{ \Carbon\Carbon::parse($attendanceModel->tanggal)->format('d M Y') }}
        </h6>
        <a href="{{ route('project.show', ['project' => $attendanceModel->project->id]) }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <div class="card-body">
        <form id="editAttendanceForm" action="{{ route('attendance.update', ['project' => $attendanceModel->project->id, 'attendance' => $attendanceModel->id]) }}" method="POST">
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

            <button type="submit" id="btnUpdate" class="btn btn-primary">Update Absensi</button>
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
                keteranganInput.disabled = false;
                keteranganInput.placeholder = "Keterangan ketidakhadiran";
            }
        });

        // Initialize based on current state
        if(!check.checked) {
            const row = check.closest('tr');
            const keteranganInput = row.querySelector('input[type="text"]');
            keteranganInput.disabled = false;
            keteranganInput.placeholder = "Keterangan ketidakhadiran";
        }
    });

    // Handle form submission with AJAX
    const form = document.getElementById('editAttendanceForm');
    const btnUpdate = document.getElementById('btnUpdate');

    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const originalText = btnUpdate.textContent;
            btnUpdate.textContent = 'Menyimpan...';
            btnUpdate.disabled = true;

            // Prepare form data
            const formData = new FormData(form);

            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        return { success: false, message: 'Server mengembalikan respon yang tidak valid.' };
                    });
                }
            })
            .then(data => {
                if(data.success || data.message) {
                    alert(data.message || 'Absensi berhasil diperbarui.');
                    window.location.href = "{{ route('attendance.index', ['project' => $attendanceModel->project->id]) }}";
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan saat memperbarui absensi.'));
                    btnUpdate.textContent = originalText;
                    btnUpdate.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui absensi. Silakan coba lagi.');
                btnUpdate.textContent = originalText;
                btnUpdate.disabled = false;
            });
        });
    }
});
</script>
@endpush