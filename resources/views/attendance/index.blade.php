@extends('layouts.admin')

@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            @if(isset($project) && $project->nama_project)
                Absensi Project : {{ $project->nama_project }}
            @else
                Absensi Project
            @endif
        </h6>

    </div>

    <div class="card-body">
        @if(isset($project) && $project->id)
            @php
                $jumlahPekerja = $project->workers->count();
            @endphp
            <div class="mb-3">
                <span class="badge badge-info">Jumlah Pekerja dalam Proyek: {{ $jumlahPekerja }}</span>
            </div>
            @if($jumlahPekerja > 0)
                <a href="{{ route('attendance.create', ['project' => $project->id]) }}" class="btn btn-primary mb-3">Tambah Absensi Harian</a>
            @else
                <div class="alert alert-warning">
                    Tidak dapat membuat absensi karena belum ada pekerja dalam proyek ini.
                    Silakan tambahkan pekerja terlebih dahulu di tab "Worker" pada halaman detail proyek.
                </div>
            @endif
        @else
            <div class="alert alert-warning">Tidak dapat membuat absensi karena informasi proyek tidak lengkap.</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Pekerja</th>
                        <th>Hadir</th>
                        <th>Tidak Hadir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            $totalWorkers = $attendance->attendanceWorkers->count();
                            $hadirCount = $attendance->attendanceWorkers->where('hadir', true)->count();
                            $tidakHadirCount = $totalWorkers - $hadirCount;
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</td>
                            <td>{{ $totalWorkers }}</td>
                            <td>{{ $hadirCount }}</td>
                            <td>{{ $tidakHadirCount }}</td>
                            <td>
                                <a href="{{ route('attendance.show', ['project' => $project->id, 'attendance' => $attendance->id]) }}"
                                   class="btn btn-info btn-sm">Lihat Detail</a>
                                <a href="{{ route('attendance.edit', ['project' => $project->id, 'attendance' => $attendance->id]) }}"
                                   class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm btn-delete-attendance-index"
                                        data-id="{{ $attendance->id }}"
                                        data-url="{{ route('attendance.destroy', ['project' => $project->id, 'attendance' => $attendance->id]) }}">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data absensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle attendance deletion
    $(document).on('click', '.btn-delete-attendance-index', function () {
        const button = $(this);
        const row = button.closest('tr');
        const attendanceId = button.data('id');
        const url = button.data('url');
        
        // Get the date from the table row
        const tanggalCell = row.find('td:first').text();
        const confirmMessage = `Apakah Anda yakin ingin menghapus absensi tanggal ${tanggalCell}?\\n\\nData yang dihapus tidak dapat dikembalikan.`;

        if (confirm(confirmMessage)) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Disable button while deleting
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
                },
                success: function(response) {
                    // Tampilkan alert sukses
                    alert(response.message || 'Absensi berhasil dihapus');

                    // Hapus baris dari tabel dengan animasi
                    row.fadeOut(300, function() {
                        row.remove();

                        // Jika tidak ada data absensi lagi, tambahkan pesan
                        if ($('table tbody tr').length === 0) {
                            $('table tbody').html(`
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data absensi</td>
                                </tr>
                            `);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    // Tampilkan alert error
                    alert('Gagal menghapus absensi: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                    // Re-enable button on error
                    button.prop('disabled', false).html('Hapus');
                }
            });
        }
    });
});
</script>
@endpush
@endsection