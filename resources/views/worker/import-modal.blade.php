{{-- Modal Import Worker --}}
<div class="modal fade" id="importWorkerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-excel mr-2"></i>Import Workers dari Excel
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="importWorkerForm" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Step 1: Download Template --}}
                    <div class="mb-4">
                        <label class="font-weight-bold">
                            <span class="badge badge-success mr-2">1</span>
                            Download Template
                        </label>
                        <p class="text-muted small mb-2">
                            Download template Excel untuk memastikan format data yang benar
                        </p>
                        <a href="{{ route('project.workers.import.template', ['project' => $project->id]) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-download mr-2"></i>Download Template Excel
                        </a>
                    </div>

                    <hr>

                    {{-- Step 2: Upload File --}}
                    <div class="mb-4">
                        <label class="font-weight-bold">
                            <span class="badge badge-success mr-2">2</span>
                            Upload File Excel/CSV
                        </label>
                        <p class="text-muted small mb-2">
                            Format yang didukung: .xlsx, .xls, .csv (Max 10MB)
                        </p>
                        <div class="input-group">
                            <input type="file"
                                   class="form-control"
                                   id="importWorkerFile"
                                   name="file"
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-file-excel"></i></span>
                            </div>
                        </div>
                        <div id="workerFileInfo" class="mt-2 small"></div>
                    </div>

                    <hr>

                    {{-- Step 3: Info --}}
                    <div class="alert alert-info mb-0">
                        <div class="mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Kolom yang diperlukan:</strong>
                        </div>
                        <ul class="small mb-0 pl-3">
                            <li><strong>Nama Worker</strong> - Wajib diisi (max 255 karakter)</li>
                            <li><strong>Posisi</strong> - Opsional (max 255 karakter)</li>
                            <li><strong>No HP</strong> - Opsional (max 20 karakter)</li>
                            <li><strong>Aktif</strong> - Opsional (1 = Aktif, 0 = Nonaktif)</li>
                        </ul>
                    </div>

                    {{-- Preview Area --}}
                    <div id="workerImportPreview" class="mt-3" style="display: none;">
                        <div class="alert alert-success py-2 px-3 mb-0">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>File siap diimport!</strong> Klik tombol "Import Workers" untuk melanjutkan.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="btnImportWorker">
                        <i class="fas fa-upload mr-2"></i>Import Workers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hasil Import --}}
<div class="modal fade" id="workerImportResultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Import Worker</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="workerImportResultContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                    <i class="fas fa-sync mr-2"></i>Refresh Halaman
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update file info when file is selected
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('importWorkerFile');
    const fileInfo = document.getElementById('workerFileInfo');
    const importPreview = document.getElementById('workerImportPreview');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileType = file.name.split('.').pop().toUpperCase();

                fileInfo.innerHTML =
                    `<div class="alert alert-success py-2 px-3 mb-0">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>${file.name}</strong>
                        <span class="text-muted">(${fileSize} MB - ${fileType})</span>
                    </div>`;

                // Check file size
                if (file.size > 10 * 1024 * 1024) {
                    fileInfo.innerHTML =
                        `<div class="alert alert-danger py-2 px-3 mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Ukuran file terlalu besar! Maksimal 10MB.
                        </div>`;
                    this.value = '';
                    if (importPreview) importPreview.style.display = 'none';
                    return;
                }

                // Show preview
                if (importPreview) {
                    importPreview.style.display = 'block';
                    importPreview.innerHTML = `
                        <div class="alert alert-primary mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>File siap diimport!</strong> Klik tombol "Import Workers" untuk melanjutkan.
                        </div>
                    `;
                }
            } else {
                fileInfo.innerHTML = '';
                if (importPreview) importPreview.style.display = 'none';
            }
        });
    }
});

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.getElementById('importWorkerForm');
    const submitBtn = document.getElementById('btnImportWorker');
    const fileInput = document.getElementById('importWorkerFile');

    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate file is selected
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Silakan pilih file Excel terlebih dahulu');
                return;
            }

            const file = fileInput.files[0];
            const originalText = submitBtn.innerHTML;

            // Disable button
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengimport...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const uploadUrl = "{{ route('project.workers.import', ['project' => $project->id]) }}";

            const xhr = new XMLHttpRequest();

            xhr.open('POST', uploadUrl, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);

                        if (data.success) {
                            showWorkerImportResult(data);
                        } else {
                            let errorMsg = data.message || 'Import gagal';
                            if (data.errors && data.errors.length > 0) {
                                errorMsg += '<br><br>Error:<br>';
                                data.errors.forEach(err => {
                                    errorMsg += `- Row ${err.row}: ${err.error}<br>`;
                                });
                            }
                            alert(errorMsg);
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    } catch (e) {
                        alert('Response tidak valid: ' + xhr.responseText);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                } else {
                    alert('Error ' + xhr.status + ': ' + xhr.statusText);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            };

            xhr.onerror = function() {
                alert('Terjadi kesalahan saat upload. Periksa koneksi Anda.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            };

            xhr.send(formData);
        });
    }
});

// Show import result
function showWorkerImportResult(data) {
    const content = document.getElementById('workerImportResultContent');

    let html = `
        <div class="text-center mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3">Import Worker Berhasil!</h4>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>${data.imported}</h3>
                        <p class="mb-0">Worker Ditambahkan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3>${data.skipped}</h3>
                        <p class="mb-0">Worker Di-skip (Duplikat)</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    if (data.errors && data.errors.length > 0) {
        html += `
            <div class="mt-4">
                <h6 class="font-weight-bold">Worker yang Di-skip:</h6>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Row</th>
                                <th>Nama Worker</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        data.errors.forEach(error => {
            html += `
                <tr>
                    <td>${error.row}</td>
                    <td>${error.nama_worker || '-'}</td>
                    <td><span class="text-danger">${error.error}</span></td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    content.innerHTML = html;

    // Close import modal and open result modal
    $('#importWorkerModal').modal('hide');
    $('#workerImportResultModal').modal('show');

    // Reset form
    document.getElementById('importWorkerForm').reset();
    document.getElementById('workerFileInfo').innerHTML = '';
    document.getElementById('workerImportPreview').style.display = 'none';
}

// Reset modal when closed
$('#importWorkerModal').on('hidden.bs.modal', function() {
    document.getElementById('importWorkerForm').reset();
    document.getElementById('workerFileInfo').innerHTML = '';
    document.getElementById('workerImportPreview').style.display = 'none';
    const submitBtn = document.getElementById('btnImportWorker');
    submitBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Import Workers';
    submitBtn.disabled = false;
});
</script>
@endpush
