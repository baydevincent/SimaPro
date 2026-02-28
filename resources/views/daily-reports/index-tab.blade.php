<div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="font-weight-bold mb-0">Laporan Harian Project</h6>
        <a href="{{ route('daily-reports.create', $project->id) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Laporan Harian
        </a>
    </div>

    @if($reports->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="12%">Tanggal</th>
                        <th width="38%">Uraian Kegiatan</th>
                        <th width="15%">Cuaca</th>
                        <th width="10%">Pekerja</th>
                        <th width="15%">Dokumentasi</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</td>
                        <td>{{ Str::limit($report->uraian_kegiatan, 80) }}</td>
                        <td>{{ $report->cuaca ?? '-' }}</td>
                        <td class="text-center">{{ $report->jumlah_pekerja }}</td>
                        <td>
                            @if($report->images->count() > 0)
                                <span class="badge badge-info">{{ $report->images->count() }} foto</span>
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('daily-reports.show-detail', ['project' => $project->id, 'report' => $report->id]) }}" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('daily-reports.download-pdf', ['project' => $project->id, 'report' => $report->id]) }}" 
                               class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @if(Auth::user() && Auth::user()->hasRole('administrator'))
                            <a href="{{ route('daily-reports.edit', ['project' => $project->id, 'report' => $report->id]) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('daily-reports.destroy', ['project' => $project->id, 'report' => $report->id]) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan harian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p>Belum ada laporan harian</p>
                            <a href="{{ route('daily-reports.create', $project->id) }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Buat Laporan Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $reports->links() }}
        </div>
    @endif
</div>
