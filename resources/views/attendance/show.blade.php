@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Absensi</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Tanggal: {{ \Carbon\Carbon::parse($attendanceModel->tanggal)->format('d F Y') }}
                    </h6>
                    <a href="{{ route('project.show', ['project' => $attendanceModel->project->id]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold text-primary">Project: {{ $attendanceModel->project->nama_project }}</h5>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h5 class="font-weight-bold">Status Kehadiran</h5>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Pekerja</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $attendanceModel->attendanceWorkers->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Hadir</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $attendanceModel->attendanceWorkers->where('hadir', true)->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Tidak Hadir</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $attendanceModel->attendanceWorkers->where('hadir', false)->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Persentase</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $attendanceModel->attendanceWorkers->count() > 0 ? round(($attendanceModel->attendanceWorkers->where('hadir', true)->count() / $attendanceModel->attendanceWorkers->count()) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pekerja</th>
                                    <th>Posisi</th>
                                    <th>Status Kehadiran</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceModel->attendanceWorkers as $index => $aw)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $aw->projectWorker ? $aw->projectWorker->nama_worker : 'N/A' }}</td>
                                        <td>{{ $aw->projectWorker ? $aw->projectWorker->posisi : 'N/A' }}</td>
                                        <td>{{ $aw->projectWorker ? $aw->projectWorker->no_hp : 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $aw->hadir ? 'badge-success' : 'badge-danger' }} px-3 py-2">
                                                <i class="fas {{ $aw->hadir ? 'fa-check' : 'fa-times' }}"></i>
                                                {{ $aw->hadir ? 'Hadir' : 'Tidak Hadir' }}
                                            </span>
                                        </td>
                                        <td>{{ $aw->keterangan ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data kehadiran</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection