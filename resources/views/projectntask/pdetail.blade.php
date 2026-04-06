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
                <div class="row align-items-center">
                    <div class="col-md-6">
                        @auth
                            @if(!Auth::user()->hasRole('mandor'))
                        <form id="formCreateTask"
                            data-action="{{ route('task.store',$project->id) }}">
                            @csrf

                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input name="nama_task"
                                        class="form-control"
                                        placeholder="Nama Task">
                                    <div class="invalid-feedback"></div>
                                </div>

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

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </form>
                            @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle"></i> Hanya administrator yang bisa menambah task.
                        </p>
                            @endif
                        @else
                        <p class="text-muted mb-0">Silakan login untuk menambah task.</p>
                        @endauth
                    </div>
                    @auth
                        @if(!Auth::user()->hasRole('mandor'))
                    <div class="col-md-2 text-right">
                        <button class="btn btn-success w-100" data-toggle="modal" data-target="#importTaskModal">
                            <i class="fas fa-file-excel mr-2"></i>Import Excel
                        </button>
                    </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- TASK -->
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
                                @auth
                                    @if(!Auth::user()->hasRole('mandor'))
                                <a href="{{ route('task.edit', $task->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button
                                    class="btn btn-danger btn-sm btn-delete-task"
                                    data-id="{{ $task->id }}"
                                    data-name="{{ $task->nama_task }}"
                                    data-url="{{ route('task.destroy', $task->id) }}"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                                    @else
                                <span class="text-muted">-</span>
                                    @endif
                                @endauth
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

        <!-- WORKER -->
        <div class="tab-pane fade"
             id="projectworker"
             role="tabpanel">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold mb-3">
                        List Pekerja
                    </h6>
                    <div>
                        <button class="btn btn-success mr-2" data-toggle="modal" data-target="#importWorkerModal">
                            <i class="fas fa-file-excel mr-2"></i>Import Excel
                        </button>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreateWorker">
                            + Tambah Worker
                        </button>
                    </div>
                </div>

                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                    @forelse($project->workers as $worker)
                    <tr>
                        <td>{{ $worker->nama_worker }}</td>
                        <td>{{ $worker->posisi }}</td>
                        <td>{{ $worker->no_hp }}</td>
                        <td>
                            <span class="badge {{ $worker->aktif ? 'badge-success':'badge-secondary' }} px-3 py-2">
                                {{ $worker->aktif ? 'Aktif':'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit-worker"
                                    data-id="{{ $worker->id }}"
                                    data-url="{{ route('project.workers.edit', ['project' => $project->id, 'worker' => $worker->id]) }}"
                                    data-toggle="modal"
                                    data-target="#modalEditWorker">
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
            @include('worker.edit')
            @include('worker.import-modal')
        </div>

        <!-- ABSENSI -->
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
                                        <button class="btn btn-danger btn-sm btn-delete-absen"
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

        <!-- LAPORAN HARIAN -->
        <div class="tab-pane fade"
             id="report"
             role="tabpanel">
            @include('daily-reports.index-tab')
        </div>

    </div>

</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@include('task.import-modal')
@endpush

@endsection