<div class="card-body">
    {{-- Upload Form --}}
    <div class="mb-4">
        <h6 class="font-weight-bold mb-3">Upload Shop Drawing</h6>
        <form id="uploadShopDrawingForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="custom-file">
                        <input type="file" 
                               class="custom-file-input" 
                               id="shopDrawingFile" 
                               name="file" 
                               accept="image/*,.pdf,.dwg,.dxf"
                               required>
                        <label class="custom-file-label" for="shopDrawingFile" id="fileLabel">
                            Pilih file...
                        </label>
                    </div>
                    <small class="text-muted">
                        Format: JPG, PNG, PDF, DWG, DXF (Max 10MB)
                    </small>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <input type="text" 
                           name="deskripsi" 
                           class="form-control" 
                           placeholder="Deskripsi (opsional)"
                           maxlength="500">
                </div>
            </div>
        </form>
    </div>

    {{-- File List --}}
    <div>
        <h6 class="font-weight-bold mb-3">Daftar Shop Drawing</h6>
        
        @if($shopDrawings->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama File</th>
                            <th>Deskripsi</th>
                            <th>Ukuran</th>
                            <th>Diupload Oleh</th>
                            <th>Tanggal</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="shopDrawingList">
                        @foreach($shopDrawings as $index => $drawing)
                        <tr id="row-{{ $drawing->id }}">
                            <td>
                                @if($drawing->is_image)
                                    <i class="fas fa-image text-info"></i>
                                @elseif($drawing->is_pdf)
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @else
                                    <i class="fas fa-file text-secondary"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $drawing->nama_file_asli }}</strong>
                            </td>
                            <td>
                                {{ $drawing->deskripsi ?? '-' }}
                            </td>
                            <td>{{ $drawing->formatted_file_size }}</td>
                            <td>{{ $drawing->uploaded_by }}</td>
                            <td>{{ $drawing->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($drawing->is_image)
                                        <button class="btn btn-info"
                                                onclick="viewImage('{{ $drawing->file_url }}', '{{ $drawing->nama_file_asli }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('shopdrawing.download', ['project' => $project->id, 'shopDrawing' => $drawing->id]) }}"
                                       class="btn btn-success"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-danger"
                                            onclick="deleteShopDrawing({{ $drawing->id }})"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                Belum ada shop drawing. Upload file pertama Anda.
            </div>
        @endif
    </div>
</div>

{{-- Image Preview Modal --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitle">Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreview" src="" alt="Preview" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update file label when file is selected
document.getElementById('shopDrawingFile').addEventListener('change', function(e) {
    const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
    document.getElementById('fileLabel').textContent = fileName;
});

// Handle upload form submission
document.getElementById('uploadShopDrawingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const fileInput = document.getElementById('shopDrawingFile');
    
    if (!fileInput.files[0]) {
        alert('Silakan pilih file terlebih dahulu');
        return;
    }

    // Check file size (10MB max)
    const fileSize = fileInput.files[0].size / 1024 / 1024;
    if (fileSize > 10) {
        alert('Ukuran file terlalu besar. Maksimal 10MB.');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    submitBtn.disabled = true;

    fetch("{{ route('shopdrawing.store', $project->id) }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Upload gagal');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat upload');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// View image in modal
function viewImage(url, title) {
    document.getElementById('imagePreview').src = url;
    document.getElementById('imagePreviewTitle').textContent = title;
    $('#imagePreviewModal').modal('show');
}

// Delete shop drawing
function deleteShopDrawing(id) {
    if (!confirm('Yakin ingin menghapus shop drawing ini?')) {
        return;
    }

    const url = "{{ route('shopdrawing.destroy', ['project' => $project->id, 'shopDrawing' => ':id']) }}".replace(':id', id);
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('row-' + id).remove();
            
            // Check if no more drawings
            const tbody = document.getElementById('shopDrawingList');
            if (tbody.children.length === 0) {
                location.reload();
            }
        } else {
            alert(data.message || 'Hapus gagal');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus');
    });
}
</script>
@endpush
