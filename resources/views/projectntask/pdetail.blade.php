@extends('layouts.admin')

@section('main-content')

<div class="card shadow">

    {{-- ================= HEADER ================= --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            Detail Project : {{ $project->nama_project }}
        </h6>
        <a href="{{ route('project') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <ul class="nav nav-tabs" id="projectTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active"
               id="shopdrawing-tab"
               data-toggle="tab"
               href="#shopdrawing"
               role="tab">
                Shop Drawing
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               id="tasks-tab"
               data-toggle="tab"
               href="#tasks"
               role="tab">
                Task Project
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               id="project_worker"
               data-toggle="tab"
               href="#projectworker"
               role="tab">
                Worker
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               id="attendance-tab"
               data-toggle="tab"
               href="#attendance"
               role="tab">
                Absensi Project
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               id="report-tab"
               data-toggle="tab"
               href="#report"
               role="tab">
                Laporan Harian
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- TAB 1 : SHOP DRAWING -->
        <div class="tab-pane fade show active"
             id="shopdrawing"
             role="tabpanel">
            @include('shopdrawing.index')
        </div>

        <!-- TAB 2 : TASK PROJECT -->
        <div class="tab-pane fade"
             id="tasks"
             role="tabpanel">

            <div class="card-header">
                <form id="formCreateTask"
                    data-action="{{ route('task.store',$project->id) }}">
                    @csrf

                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <input name="nama_task"
                                class="form-control"
                                placeholder="Nama Task">
                            <div class="invalid-feedback"></div>
                        </div>

                        @auth
                            @if(Auth::user()->hasRole('administrator'))
                            <div class="col-md-4">
                                <input name="bobot_rupiah"
                                    class="form-control"
                                    placeholder="Bobot Rupiah">
                                <div class="invalid-feedback"></div>
                            </div>
                            @else
                            <div class="col-md-4">
                                <input type="hidden" name="bobot_rupiah" value="0">
                            </div>
                            @endif
                        @else
                        <div class="col-md-4">
                            <input type="hidden" name="bobot_rupiah" value="0">
                        </div>
                        @endauth

                        <div class="col-md-3">
                            <button type="submit"
                                    class="btn btn-primary w-100">
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- LIST TASK --}}
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>
                                @auth
                                    @if(Auth::user()->hasRole('administrator'))
                                        Bobot
                                    @else
                                        &nbsp; 
                                    @endif
                                @else
                                    &nbsp;
                                @endauth
                            </th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->tasks as $task)
                        <tr>
                            <td>{{ $task->nama_task }}</td>
                            <td>
                                @auth
                                    @if(Auth::user()->hasRole('administrator'))
                                        Rp {{ number_format($task->bobot_rupiah) }}
                                    @else
                                        <span class="text-muted">***</span>
                                    @endif
                                @else
                                    <span class="text-muted">***</span>
                                @endauth
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('task.toggle',$task->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm
                                        {{ $task->is_done ? 'btn-success':'btn-secondary' }}">
                                        {{ $task->is_done ? 'Selesai':'Belum' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('task.edit', $task->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button
                                    class="btn btn-danger btn-sm btn-delete-task"
                                    data-id="{{ $task->id }}"
                                    data-url="{{ route('task.destroy', $task->id) }}"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada task
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2 : WORKER PROJECT -->
        <div class="tab-pane fade"
             id="projectworker"
             role="tabpanel">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-3">
                        List Pekerja
                    </h6>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreateWorker">
                        + Tambah Worker
                    </button>
                </div>

                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                    @forelse($project->workers as $worker)
                    <tr>
                        <td>{{ $worker->nama_worker }}</td>
                        <td>{{ $worker->jabatan }}</td>
                        <td>
                            <span class="badge {{ $worker->aktif ? 'badge-success':'badge-secondary' }}">
                                {{ $worker->aktif ? 'Aktif':'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm" disabled>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete-worker"
                                    data-id="{{ $worker->id }}"
                                    data-url="{{ route('project.workers.destroy', ['project' => $project->id, 'worker' => $worker->id]) }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Belum ada pekerja
                        </td>
                    </tr>
                    @endforelse
                </table>
            </div>
            @include('worker.create')
        </div>

        <!-- TAB 3 : ABSENSI PROJECT -->
        <div class="tab-pane fade"
             id="attendance"
             role="tabpanel">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-0">
                        Absensi Project
                    </h6>
                    @if($project->workers->count() > 0)
                        <a href="{{ route('attendance.create', ['project' => $project->id]) }}" class="btn btn-primary">
                            Buat Absensi Baru
                        </a>
                    @else
                        <button class="btn btn-primary" disabled title="Tambahkan pekerja ke proyek terlebih dahulu">
                            Buat Absensi Baru
                        </button>
                    @endif
                </div>
                
                @if($project->workers->count() === 0)
                    <div class="alert alert-warning">
                        <p class="mb-0">Belum ada pekerja dalam proyek ini.</p>
                        <p class="mb-0">Silakan tambahkan pekerja terlebih dahulu di tab "Worker" sebelum membuat absensi.</p>
                    </div>
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
                            @forelse($project->attendances as $attendance)
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
                                        class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('attendance.edit', ['project' => $project->id, 'attendance' => $attendance->id]) }}"
                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btn-sm btn-delete-attendance"
                                            data-id="{{ $attendance->id }}"
                                            data-url="{{ route('attendance.destroy', ['project' => $project->id, 'attendance' => $attendance->id]) }}">
                                            <i class="fas fa-trash"></i>
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

        <div class="tab-pane fade"
             id="report"
             role="tabpanel">
        </div>

    </div>

</div>

@push('scripts')
<script>
// Script untuk tab absensi
document.addEventListener('DOMContentLoaded', function() {
    // Load attendance dates list when the attendance tab is shown
    $('#attendance-tab').on('shown.bs.tab', function() {
        loadAttendanceDates();
    });

    // Trigger the event if the attendance tab is already active
    if ($('#attendance').hasClass('show')) {
        loadAttendanceDates();
    });

    function loadAttendanceDates() {
        $.ajax({
            url: "{{ route('attendance.dates.list', ['project' => $project->id]) }}",
            method: 'GET',
            success: function(response) {
                let datesHtml = '';

                if (response.dates && response.dates.length > 0) {
                    datesHtml += '<ul class="list-group">';

                    response.dates.forEach(function(dateItem) {
                        datesHtml += `
                            <li class="list-group-item">
                                <a href="#" class="date-link" data-date="${dateItem.tanggal}">
                                    ${dateItem.tanggal_formatted}
                                </a>
                            </li>
                        `;
                    });

                    datesHtml += '</ul>';
                } else {
                    datesHtml = '<div class="alert alert-info">Belum ada data absensi</div>';
                }

                document.getElementById('attendance-dates-list').innerHTML = datesHtml;

                // Add click event to date links
                document.querySelectorAll('.date-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const selectedDate = this.getAttribute('data-date');
                        loadAttendanceDetails(selectedDate);
                    });
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading attendance dates:', error);
                document.getElementById('attendance-dates-list').innerHTML =
                    '<div class="alert alert-danger">Gagal memuat data absensi</div>';
            }
        });
    }

    function loadAttendanceDetails(date) {
        $.ajax({
            url: "{{ route('attendance.date.data', ['project' => $project->id]) }}",
            method: 'GET',
            data: {
                tanggal: date
            },
            success: function(response) {
                let detailsHtml = '';

                if (response.attendance_data && response.attendance_data.length > 0) {
                    detailsHtml += `
                        <h6 class="font-weight-bold">Tanggal: ${formatDisplayDate(date)}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Worker</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.attendance_data.forEach(function(item) {
                        const workerName = item.project_worker ? item.project_worker.nama_worker :
                                          (item.worker ? item.worker.nama_worker : 'N/A');
                        const hadir = item.hadir !== undefined ? item.hadir : (item.attendanceWorkers ? item.attendanceWorkers.hadir : null);
                        const keterangan = item.keterangan !== undefined ? item.keterangan : (item.attendanceWorkers ? item.attendanceWorkers.keterangan : '-');

                        detailsHtml += `
                            <tr>
                                <td>${workerName}</td>
                                <td>
                                    <span class="badge ${hadir ? 'badge-success' : 'badge-danger'}">
                                        ${hadir ? 'Hadir' : 'Tidak Hadir'}
                                    </span>
                                </td>
                                <td>${keterangan || '-'}</td>
                            </tr>
                        `;
                    });

                    detailsHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    detailsHtml = `<div class="alert alert-info">Tidak ada data kehadiran untuk tanggal ${formatDisplayDate(date)}</div>`;
                }

                document.getElementById('attendance-details').innerHTML = detailsHtml;
            },
            error: function(xhr, status, error) {
                console.error('Error loading attendance details:', error);
                document.getElementById('attendance-details').innerHTML =
                    '<div class="alert alert-danger">Gagal memuat detail kehadiran</div>';
            }
        });
    }

    // Helper function to format date for display
    function formatDisplayDate(dateString) {
        const date = new Date(dateString);
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Handle worker deletion
    $(document).on('click', '.btn-delete-worker', function () {
        const button = $(this);
        const workerId = button.data('id');
        const url = button.data('url');

        if (confirm('Yakin ingin menghapus pekerja ini dari proyek?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Tampilkan alert sukses
                    alert(response.message || 'Karyawan berhasil dihapus');

                    // Refresh halaman atau hapus baris dari tabel
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Tampilkan alert error
                    alert('Gagal menghapus karyawan: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        }
    });

    // Auto-open attendance tab if URL contains #attendance
    if(window.location.hash === '#attendance') {
        $('#attendance-tab').tab('show');
    }

    // Handle attendance deletion
    $(document).on('click', '.btn-delete-attendance', function (e) {
        e.preventDefault();
        const button = $(this);
        const attendanceId = button.data('id');
        const url = button.data('url');

        if (confirm('Yakin ingin menghapus absensi ini?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Tampilkan alert sukses
                    alert(response.message || 'Absensi berhasil dihapus');

                    // Hapus baris dari tabel tanpa reload
                    const row = button.closest('tr');
                    row.fadeOut(300, function() {
                        row.remove();

                        // Jika tidak ada data absensi lagi, tambahkan pesan
                        const remainingRows = $('#attendance tbody tr:not(:contains("Belum ada data absensi"))').length;
                        if (remainingRows === 0) {
                            $('#attendance tbody').html(`
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data absensi</td>
                                </tr>
                            `);
                        }

                        // Refresh attendance statistics
                        refreshAttendanceStats();
                    });
                },
                error: function(xhr, status, error) {
                    // Tampilkan alert error
                    alert('Gagal menghapus absensi: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        }
    });

    // Function to update attendance statistics after deletion
    function refreshAttendanceStats() {
        // In a more advanced implementation, we would fetch updated stats via AJAX
        // For now, we'll just notify the user that the page should be refreshed
        // to see updated statistics
        console.log('Attendance data removed. Statistics will be updated on next page load.');
    }
});
</script>
@endpush

@endsection