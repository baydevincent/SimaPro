@extends('layouts.admin')

@section('main-content')

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            <i class="fas fa-file-alt"></i> Detail Laporan Harian
        </h6>
        <a href="{{ route('project.show', $project->id) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150"><i class="fas fa-user"></i> Dibuat Oleh</th>
                        <td>: {{ $report->creator ? $report->creator->name : '-' }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-calendar"></i> Tanggal</th>
                        <td>: {{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-cloud-sun"></i> Cuaca</th>
                        <td>: {{ $report->cuaca ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-users"></i> Jumlah Pekerja</th>
                        <td>: {{ $report->jumlah_pekerja }} orang</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150"><i class="fas fa-building"></i> Project</th>
                        <td>: {{ $project->nama_project }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-clock"></i> Dibuat</th>
                        <td>: {{ $report->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-edit"></i> Diupdate</th>
                        <td>: {{ $report->updated_at->format('d M Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <h6 class="font-weight-bold"><i class="fas fa-list"></i> Uraian Kegiatan:</h6>
        <div class="p-3 bg-light rounded mb-4">
            <p class="mb-0">{{ nl2br(e($report->uraian_kegiatan)) }}</p>
        </div>

        @if($report->catatan)
        <h6 class="font-weight-bold"><i class="fas fa-sticky-note"></i> Catatan Tambahan:</h6>
        <div class="p-3 bg-warning rounded mb-4">
            <p class="mb-0">{{ nl2br(e($report->catatan)) }}</p>
        </div>
        @endif

        <hr>

        <h6 class="font-weight-bold mb-3"><i class="fas fa-images"></i> Dokumentasi Foto ({{ $report->images->count() }})</h6>
        
        @if($report->images->count() > 0)
        <div class="row">
            @foreach($report->images as $image)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <a href="/storage/{{ $image->image_path }}" target="_blank">
                        <img src="/storage/{{ $image->image_path }}" 
                             class="card-img-top" 
                             alt="Foto {{ $loop->iteration }}" 
                             style="height: 250px; object-fit: cover; cursor: pointer;">
                    </a>
                    @if($image->caption)
                    <div class="card-body">
                        <p class="card-text small mb-0 text-muted">{{ $image->caption }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Tidak ada foto dokumentasi untuk laporan ini.
        </div>
        @endif

        <hr>

        <div class="form-group text-right">
            <a href="{{ route('daily-reports.download-pdf', ['project' => $project->id, 'report' => $report->id]) }}" 
               class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            @if(Auth::user() && Auth::user()->hasRole('administrator'))
            <a href="{{ route('daily-reports.edit', ['project' => $project->id, 'report' => $report->id]) }}" 
               class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Laporan
            </a>
            @endif
            <a href="{{ route('project.show', $project->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

@endsection
