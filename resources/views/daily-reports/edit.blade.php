@extends('layouts.admin')

@section('main-content')

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            <i class="fas fa-edit"></i> Edit Laporan Harian
        </h6>
        <a href="{{ route('project.show', $project->id) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('daily-reports.update', ['project' => $project->id, 'report' => $report->id]) }}" method="POST" enctype="multipart/form-data" data-project-id="{{ $project->id }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="created_by"><i class="fas fa-user"></i> Dibuat Oleh</label>
                        <input type="text" class="form-control" value="{{ $report->creator ? $report->creator->name : '-' }}" readonly disabled>
                        <small class="form-text text-muted">Pembuat laporan tidak dapat diubah</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="created_at"><i class="fas fa-clock"></i> Tanggal Dibuat</label>
                        <input type="text" class="form-control" value="{{ $report->created_at->format('d M Y, H:i') }}" readonly disabled>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <!-- Kolom Kiri: Form Utama -->
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" 
                               class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', $report->tanggal->format('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="uraian_kegiatan">Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" id="uraian_kegiatan" 
                                  class="form-control @error('uraian_kegiatan') is-invalid @enderror" 
                                  rows="6" 
                                  placeholder="Deskripsikan kegiatan yang dilakukan hari ini..." 
                                  required>{{ old('uraian_kegiatan', $report->uraian_kegiatan) }}</textarea>
                        @error('uraian_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Tambahan</label>
                        <textarea name="catatan" id="catatan" 
                                  class="form-control @error('catatan') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Catatan atau kendala (opsional)">{{ old('catatan', $report->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kolom Kanan: Info & Upload -->
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-info-circle"></i> Informasi
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="cuaca">Cuaca</label>
                                <select name="cuaca" id="cuaca" class="form-control @error('cuaca') is-invalid @enderror">
                                    <option value="">Pilih Cuaca</option>
                                    <option value="Cerah" {{ old('cuaca', $report->cuaca) == 'Cerah' ? 'selected' : '' }}>Cerah</option>
                                    <option value="Berawan" {{ old('cuaca', $report->cuaca) == 'Berawan' ? 'selected' : '' }}>Berawan</option>
                                    <option value="Hujan Ringan" {{ old('cuaca', $report->cuaca) == 'Hujan Ringan' ? 'selected' : '' }}>Hujan Ringan</option>
                                    <option value="Hujan Lebat" {{ old('cuaca', $report->cuaca) == 'Hujan Lebat' ? 'selected' : '' }}>Hujan Lebat</option>
                                    <option value="Mendung" {{ old('cuaca', $report->cuaca) == 'Mendung' ? 'selected' : '' }}>Mendung</option>
                                </select>
                                @error('cuaca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jumlah_pekerja">Jumlah Pekerja</label>
                                <input type="number" name="jumlah_pekerja" id="jumlah_pekerja" 
                                       class="form-control @error('jumlah_pekerja') is-invalid @enderror" 
                                       value="{{ old('jumlah_pekerja', $report->jumlah_pekerja) }}" 
                                       min="0">
                                @error('jumlah_pekerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Tips:</strong><br>
                        Jumlah pekerja dapat diubah manual saat mengedit.
                    </div>
                </div>
            </div>

            <hr>

            <!-- Existing Photos Section -->
            <div class="form-group">
                <label><i class="fas fa-images"></i> Foto Existing ({{ $report->images->count() }})</label>
                @if($report->images->count() > 0)
                <div class="row">
                    @foreach($report->images as $image)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <img src="/storage/{{ $image->image_path }}" 
                                 class="card-img-top" 
                                 alt="Foto {{ $loop->iteration }}" 
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <input type="hidden" name="existing_images[{{ $image->id }}][id]" value="{{ $image->id }}">
                                <input type="text" name="existing_images[{{ $image->id }}][caption]" 
                                       class="form-control form-control-sm mb-2" 
                                       value="{{ old('existing_images.' . $image->id . '.caption', $image->caption) }}" 
                                       placeholder="Keterangan foto">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" 
                                           class="custom-control-input" id="delete_img_{{ $image->id }}">
                                    <label class="custom-control-label text-danger" for="delete_img_{{ $image->id }}">
                                        <i class="fas fa-trash"></i> Hapus foto ini
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Tidak ada foto existing</p>
                @endif
            </div>

            <hr>

            <div class="form-group">
                <label><i class="fas fa-camera"></i> Tambah Foto Baru</label>
                <div class="mb-3">
                    <div class="custom-file">
                        <input type="file" name="images[]" class="custom-file-input image-input" id="edit-multiple-image-upload" accept="image/*" multiple>
                        <label class="custom-file-label" for="edit-multiple-image-upload" data-browse="Browse">Pilih foto (bisa multiple)...</label>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                    </small>
                </div>
                
                <div id="edit-image-preview-container" class="row mt-3"></div>
                
                <div id="image-upload-container">
                </div>
            </div>

            <hr>

            <div class="form-group text-right">
                <a href="{{ route('project.show', $project->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Laporan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
