@extends('layouts.admin')

@section('main-content')

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold mb-0">
            <i class="fas fa-calendar-plus"></i> Tambah Laporan Harian - {{ $project->nama_project }}
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

        <form action="{{ route('daily-reports.store', $project->id) }}" method="POST" enctype="multipart/form-data" data-project-id="{{ $project->id }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="created_by"><i class="fas fa-user"></i> Dibuat Oleh</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly disabled>
                        <small class="form-text text-muted">Pembuat laporan adalah user yang sedang login</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="created_at"><i class="fas fa-clock"></i> Tanggal Dibuat</label>
                        <input type="text" class="form-control" value="{{ date('d M Y, H:i') }}" readonly disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Kolom Kiri: Form Utama -->
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" 
                               class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" required>
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
                                  required>{{ old('uraian_kegiatan') }}</textarea>
                        @error('uraian_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Tambahan</label>
                        <textarea name="catatan" id="catatan" 
                                  class="form-control @error('catatan') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Catatan atau kendala (opsional)">{{ old('catatan') }}</textarea>
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
                                    <option value="Cerah" {{ old('cuaca') == 'Cerah' ? 'selected' : '' }}>Cerah</option>
                                    <option value="Berawan" {{ old('cuaca') == 'Berawan' ? 'selected' : '' }}>Berawan</option>
                                    <option value="Hujan Ringan" {{ old('cuaca') == 'Hujan Ringan' ? 'selected' : '' }}>Hujan Ringan</option>
                                    <option value="Hujan Lebat" {{ old('cuaca') == 'Hujan Lebat' ? 'selected' : '' }}>Hujan Lebat</option>
                                    <option value="Mendung" {{ old('cuaca') == 'Mendung' ? 'selected' : '' }}>Mendung</option>
                                </select>
                                @error('cuaca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jumlah_pekerja">Jumlah Pekerja</label>
                                <input type="number" name="jumlah_pekerja" id="jumlah_pekerja" 
                                       class="form-control @error('jumlah_pekerja') is-invalid @enderror" 
                                       value="{{ old('jumlah_pekerja', $totalWorkers ?? 0) }}" 
                                       min="0" readonly>
                                <small class="form-text text-muted">
                                    Otomatis dari data absensi tanggal {{ \Carbon\Carbon::parse(old('tanggal', date('Y-m-d')))->format('d M Y') }}
                                </small>
                                @error('jumlah_pekerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Tips:</strong><br>
                        Jumlah pekerja akan otomatis terisi berdasarkan data absensi pada tanggal yang dipilih.
                    </div>
                </div>
            </div>

            <hr>

            <!-- Upload Foto Section -->
            <div class="form-group">
                <label><i class="fas fa-camera"></i> Dokumentasi Foto</label>
                <div class="mb-3">
                    <div class="custom-file">
                        <input type="file" name="images[]" class="custom-file-input image-input" id="multiple-image-upload" accept="image/*" multiple>
                        <label class="custom-file-label" for="multiple-image-upload" data-browse="Browse">Pilih foto...</label>
                    </div>
                </div>
                
                <div id="image-preview-container" class="row mt-3"></div>
                
                <div id="image-upload-container">
                </div>
            </div>

            <hr>

            <div class="form-group text-right">
                <a href="{{ route('project.show', $project->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
