@php
if (!isset($project) || !isset($reports)) {
    return;
}
@endphp

<div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold mb-0">Laporan Harian Project</h6>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreateReport">
            <i class="fas fa-plus"></i> Tambah Laporan
        </button>
    </div>

    @if($reports->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="10%">Tanggal</th>
                        <th width="35%">Uraian Kegiatan</th>
                        <th width="15%">Cuaca</th>
                        <th width="10%">Pekerja</th>
                        <th width="20%">Dokumentasi</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</td>
                        <td>{{ Str::limit($report->uraian_kegiatan, 100) }}</td>
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
                            <button class="btn btn-info btn-sm btn-view-report"
                                    data-id="{{ $report->id }}"
                                    data-toggle="modal"
                                    data-target="#modalViewReport">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if(Auth::user() && Auth::user()->hasRole('administrator'))
                            <button class="btn btn-warning btn-sm btn-edit-report"
                                    data-id="{{ $report->id }}"
                                    data-toggle="modal"
                                    data-target="#modalEditReport">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete-report"
                                    data-id="{{ $report->id }}"
                                    data-project="{{ $project->id }}"
                                    data-url="/project/{{ $project->id }}/daily-reports/{{ $report->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada laporan harian
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $reports->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0"><i class="fas fa-info-circle"></i> Belum ada laporan harian untuk project ini.</p>
            <p class="mb-0 mt-2">Klik tombol "Tambah Laporan" untuk membuat laporan harian pertama.</p>
        </div>
    @endif
</div>

<!-- Modal Create Report -->
<div class="modal fade" id="modalCreateReport" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formCreateReport" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Laporan Harian</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" class="form-control @error('uraian_kegiatan') is-invalid @enderror" rows="4" placeholder="Deskripsikan kegiatan hari ini..." required>{{ old('uraian_kegiatan') }}</textarea>
                        @error('uraian_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cuaca</label>
                                <input type="text" name="cuaca" class="form-control @error('cuaca') is-invalid @enderror" placeholder="Cerah, Berawan, Hujan..." value="{{ old('cuaca') }}">
                                @error('cuaca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jumlah Pekerja</label>
                                <input type="number" name="jumlah_pekerja" class="form-control @error('jumlah_pekerja') is-invalid @enderror" value="{{ old('jumlah_pekerja', 0) }}" min="0">
                                @error('jumlah_pekerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan Tambahan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="2" placeholder="Catatan atau kendala (opsional)">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Dokumentasi Foto</label>
                        <div id="image-upload-container">
                            <div class="image-input-wrapper mb-2">
                                <div class="custom-file">
                                    <input type="file" name="images[]" class="custom-file-input image-input" accept="image/*">
                                    <label class="custom-file-label">Pilih foto...</label>
                                </div>
                                <input type="text" name="captions[]" class="form-control mt-2" placeholder="Keterangan foto (opsional)">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-image-input">
                            <i class="fas fa-plus"></i> Tambah Foto
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Report -->
<div class="modal fade" id="modalViewReport" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Laporan Harian</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="view-report-content">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Report -->
<div class="modal fade" id="modalEditReport" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formEditReport" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Laporan Harian</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit-report-body">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>

</script>
@endpush
