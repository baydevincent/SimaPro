@extends('layouts.admin')

@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            Tambah Absensi Harian - {{ $project->nama_project }}
        </h6>
        <a href="{{ route('project.show', ['project' => $project->id]) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <form id="attendanceForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}" required>
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
                        @forelse($projectWorkers->where('aktif', 1) as $pw)
                            <tr>
                                <td>{{ $pw->nama_worker }}</td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input hadir-check"
                                               type="checkbox"
                                               name="kehadiran[{{ $pw->id }}][hadir]"
                                               id="hadir_{{ $pw->id }}"
                                               value="1"
                                               {{ (isset($existingAttendance[$pw->id]) && $existingAttendance[$pw->id] !== null) || old("kehadiran.{$pw->id}.hadir") ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hadir_{{ $pw->id }}">Hadir</label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text"
                                           name="kehadiran[{{ $pw->id }}][keterangan]"
                                           class="form-control"
                                           value="{{ old("kehadiran.{$pw->id}.keterangan", isset($existingAttendance[$pw->id]) ? $existingAttendance[$pw->id] : '') }}"
                                           placeholder="Keterangan">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada pekerja dalam proyek ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Absensi</button>
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

    // Handle form submission with AJAX
    const form = document.getElementById('attendanceForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Show loading indicator
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Menyimpan...';
            submitButton.disabled = true;

            // Prepare form data
            const formData = new FormData(form);

            // Add the route to the form action
            form.action = "{{ route('attendance.store', $project->id) }}";

            // Send AJAX request
            const baseUrl = '{{ url("/") }}';
            fetch(baseUrl + '/project/' + {{ $project->id }} + '/attendance', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, return an error object
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        return { success: false, message: 'Server mengembalikan respon yang tidak valid.' };
                    });
                }
            })
            .then(data => {
                if(data.success) {
                    // Show success message
                    alert(data.message || 'Absensi berhasil disimpan.');

                    // Redirect to project detail page, focusing on attendance tab
                    window.location.href = "{{ route('project.show', ['project' => $project->id]) }}#attendance";
                } else {
                    // Show error message
                    alert('Error: ' + (data.message || 'Terjadi kesalahan saat menyimpan absensi.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan absensi. Silakan coba lagi.');
            })
            .finally(() => {
                // Restore button state
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        });
    }
});
</script>
@endpush