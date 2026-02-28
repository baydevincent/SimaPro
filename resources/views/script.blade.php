<!-- <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script> -->
<script>
// Wait for jQuery to be available
function waitForJQuery(callback) {
    if (typeof jQuery !== 'undefined') {
        callback();
    } else {
        setTimeout(function() {
            waitForJQuery(callback);
        }, 50);
    }
}

waitForJQuery(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        console.log('Tab persistence script loaded');
        
        // 1. Get active tab from URL hash first, then localStorage
        let activeTab = window.location.hash || localStorage.getItem('activeTab');
        
        if (activeTab) {
            // Remove # if present
            activeTab = activeTab.replace('#', '');
            console.log('Restoring tab:', activeTab);
            
            // Find and show the tab
            let tabLink = $('a[href="#' + activeTab + '"][data-toggle="tab"]');
            if (tabLink.length) {
                console.log('Tab link found:', tabLink);
                tabLink.tab('show');
            } else {
                console.log('Tab link not found for:', activeTab);
            }
        }

        $('a[data-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            let href = $(this).attr('href');
            let tabId = href.replace('#', '');
            
            localStorage.setItem('activeTab', tabId);
            window.location.hash = tabId;
            
            $(this).tab('show');
        });
    });
});



waitForJQuery(function() {
$('#formCreateProject').on('submit', function(e){
    e.preventDefault();

    $('#errorBox').addClass('d-none').html('');
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    $.ajax({
        url: "{{ route('project.store') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res){
            $('#modalCreateProject').modal('hide');
            alert(res.message);
            location.reload();
        },
        error: function(xhr){
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let list = '<ul>';

                $.each(errors, function(field, messages){
                    list += `<li>${messages[0]}</li>`;

                    let input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').html(messages[0]);
                });

                list += '</ul>';

                $('#errorBox')
                    .removeClass('d-none')
                    .html(list);
            }
        }
    });
});

$(document).off('submit', '#formCreateTask');
$(document).on('submit', '#formCreateTask', function (e) {
    e.preventDefault();

    let form = $(this);
    let url  = form.data('action');

    $('#errorBox').addClass('d-none').html('');
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    $.ajax({
        url: url,
        method: 'POST',
        data: form.serialize(),

        success: function (res) {
            alert(res.message);
            form[0].reset();
            location.reload();
        },

        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;

                $.each(errors, function (field, messages) {
                    let input = form.find(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').html(messages[0]);
                });
            }
        }
    });
});

$(document).on('click', '.btn-delete-project', function () {

    let url = $(this).data('url');

    if (!confirm('Yakin ingin menghapus project ini?')) return;

    $.ajax({
        url: url,
        type: 'DELETE',

        success: function (res) {
            alert(res.message);
            location.reload();
        },

        error: function (xhr) {
            alert('Gagal menghapus project');
            console.error(xhr.responseText);
        }
    });
});

$(document).on('click', '.btn-delete-task', function () {

    let url = $(this).data('url');

    if (!confirm('Yakin ingin menghapus Task ini?')) return;

    $.ajax({
        url: url,
        type: 'DELETE',

        success: function (res) {
            alert(res.message);
            location.reload();
        },

        error: function (xhr) {
            alert('Gagal menghapus Task');
            console.error(xhr.responseText);
        }
    });
});

//WORKER Section
$(document).on('submit', '#formCreateWorker', function (e) {
    e.preventDefault();

    let form = $(this);
    let url  = form.data('action');

    $('#errorBox').addClass('d-none').html('');
    form.find('.form-control').removeClass('is-invalid');
    form.find('.invalid-feedback').html('');

    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),

        success: function (res) {
            $('#modalCreateWorker').modal('hide');
            location.reload(); // sementara
        },

        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;

                $.each(errors, function (field, messages) {
                    let input = form.find(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').html(messages[0]);
                });
            }
        }
    });
});

$(document).on('click', '.btn-delete-worker', function () {

    let url = $(this).data('url');

    if (!confirm('Yakin ingin menghapus Karyawan ini?')) return;

    $.ajax({
        url: url,
        type: 'DELETE',

        success: function (res) {
            alert(res.message);
            location.reload();
        },

        error: function (xhr) {
            alert('Gagal menghapus Karyawan');
            console.error(xhr.responseText);
        }
    });
});

//Absensi Section
$(document).on('submit', '#formAttendance', function (e) {
    e.preventDefault();

    let form = $(this);
    let tanggal = form.find('[name="tanggal"]').val();
    let projectId = form.data('project');

    $.each(form.find('select'), function () {
        let workerId = $(this).attr('name').match(/\d+/)[0];

        $.post(form.data('action'), {
            worker_id: workerId,
            project_id: projectId,
            tanggal: tanggal,
            status: $(this).val()
        });
    });

    alert('Absensi berhasil disimpan');
});

$(document).on('click', '.btn-delete-absen', function () {
        const button = $(this);
        const workerId = button.data('id');
        const url = button.data('url');

        if (confirm('Yakin ingin menghapus Absen?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert(response.message || 'Absen berhasil dihapus');

                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Gagal menghapus Absen: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        }
    });

$(document).on('click', '.btn-edit-worker', function() {
        const button = $(this);
        const workerId = button.data('id');
        const url = button.data('url');
        
        $('#formEditWorker')[0].reset();
        $('#editErrorBox').addClass('d-none');
        $('#editSuccessBox').addClass('d-none');
        
        $('#btnUpdateWorker').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    $('#edit_worker_id').val(data.id);
                    $('#edit_nama_worker').val(data.nama_worker);
                    $('#edit_posisi').val(data.posisi || '');
                    $('#edit_no_hp').val(data.no_hp || '');
                    $('#edit_aktif').prop('checked', data.aktif);
                    
                    // Enable button
                    $('#btnUpdateWorker').prop('disabled', false).html('Update');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading worker data:', error);
                $('#editErrorBox').removeClass('d-none').text('Gagal memuat data worker');
                $('#btnUpdateWorker').prop('disabled', false).html('Update');
            }
        });
    });

    // Handle Submit Edit Worker
    $('#formEditWorker').on('submit', function(e) {
        e.preventDefault();

        const workerId = $('#edit_worker_id').val();
        const projectId = $('#edit_project_id').val() || window.currentProjectId;
        
        if (!projectId) {
            alert('Project ID tidak ditemukan');
            return;
        }
        
        const url = `/project/${projectId}/workers/${workerId}`;
        
        $('#editErrorBox').addClass('d-none');
        $('#editSuccessBox').addClass('d-none');
        
        $('#btnUpdateWorker').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content'),
                nama_worker: $('#edit_nama_worker').val(),
                posisi: $('#edit_posisi').val(),
                no_hp: $('#edit_no_hp').val(),
                aktif: $('#edit_aktif').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    $('#editSuccessBox').removeClass('d-none').text(response.message || 'Data worker berhasil diperbarui');
                    
                    setTimeout(function() {
                        $('#modalEditWorker').modal('hide');
                        location.reload();
                    }, 1000);
                } else {
                    $('#editErrorBox').removeClass('d-none').text(response.message || 'Gagal memperbarui data worker');
                    $('#btnUpdateWorker').prop('disabled', false).html('Update');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating worker:', error);
                let errorMsg = 'Gagal memperbarui data worker';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }

                $('#editErrorBox').removeClass('d-none').text(errorMsg);
                $('#btnUpdateWorker').prop('disabled', false).html('Update');
            }
        });
    });

    $(document).on('change', '#multiple-image-upload', function() {
        const files = this.files;
        const previewContainer = $('#image-preview-container');
        
        let label = $(this).next('.custom-file-label');
        if (!label.length) {
            label = $(this).siblings('.custom-file-label');
        }
        if (!label.length) {
            label = $(this).parent().find('.custom-file-label');
        }
        
        previewContainer.empty();
        
        if (files && files.length > 0) {
            if (files.length === 1) {
                label.text(files[0].name);
            } else {
                label.text(files.length + ' foto dipilih');
            }
            label.addClass('selected');
            
            $.each(files, function(index, file) {
                if (!file.type.match('image.*')) {
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <input type="text" name="captions[]" class="form-control form-control-sm mb-2" placeholder="Keterangan foto ${index + 1}">
                                    <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                                </div>
                            </div>
                        </div>
                    `;
                    previewContainer.append(previewHtml);
                };
                reader.readAsDataURL(file);
            });
        } else {
            label.removeClass('selected').text('Pilih foto...');
        }
    });

    $(document).on('click', '#add-image-input', function(e) {
        e.preventDefault();
        const newInput = `
            <div class="image-input-wrapper mb-3">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <div class="custom-file">
                            <input type="file" name="additional_images[]" class="custom-file-input image-input" accept="image/*" multiple>
                            <label class="custom-file-label" data-browse="Browse">Pilih foto tambahan...</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="additional_captions[]" class="form-control mb-2" placeholder="Keterangan foto (opsional)">
                        <button type="button" class="btn btn-sm btn-danger btn-block remove-image">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#image-upload-container').append(newInput);
    });

    $(document).on('click', '.remove-image', function(e) {
        e.preventDefault();
        $(this).closest('.image-input-wrapper').remove();
    });

    $(document).on('change', '.custom-file-input', function() {
        const input = $(this);
        const label = input.next('.custom-file-label');
        const files = this.files;
        
        if (files && files.length > 0) {
            if (files.length === 1) {
                label.text(files[0].name);
            } else {
                let fileNames = Array.from(files).map(f => f.name).slice(0, 3).join(', ');
                if (files.length > 3) {
                    fileNames += ' dan ' + (files.length - 3) + ' foto lainnya';
                }
                label.text(fileNames);
            }
            label.addClass('selected');
        } else {
            label.removeClass('selected').text('Pilih foto...');
        }
    });

    $(document).on('change', '#tanggal', function() {
        const date = $(this).val();
        const form = $(this).closest('form');
        const projectId = form.data('project-id') || window.currentProjectId;

        if (!projectId) {
            return;
        }

        $.ajax({
            url: `/project/${projectId}/daily-reports/workers-count`,
            method: 'GET',
            data: { tanggal: date },
            success: function(response) {
                $('#jumlah_pekerja').val(response.total_workers || 0);
                const dateObj = new Date(date);
                const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                $('#jumlah_pekerja').siblings('.form-text').text(
                    'Otomatis dari data absensi tanggal ' + dateStr
                );
            },
            error: function() {
                $('#jumlah_pekerja').val(0);
            }
        });
    });

    $(document).ready(function() {
        if ($('#tanggal').length && $('#jumlah_pekerja').length) {
            $('#tanggal').trigger('change');
        }
    });

    // Handle multiple file upload for edit form
    $(document).on('change', '#edit-multiple-image-upload', function() {
        const files = this.files;
        const previewContainer = $('#edit-image-preview-container');
        const label = $(this).next('.custom-file-label');
        
        previewContainer.empty();
        
        if (files && files.length > 0) {
            // Update label
            if (files.length === 1) {
                label.text(files[0].name);
            } else {
                label.text(files.length + ' foto dipilih');
            }
            label.addClass('selected');
            
            // Create preview
            $.each(files, function(index, file) {
                if (!file.type.match('image.*')) {
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <input type="text" name="captions[]" class="form-control form-control-sm mb-2" placeholder="Keterangan foto ${index + 1}">
                                    <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                                </div>
                            </div>
                        </div>
                    `;
                    previewContainer.append(previewHtml);
                };
                reader.readAsDataURL(file);
            });
        } else {
            label.removeClass('selected').text('Pilih foto (bisa multiple)...');
        }
    });
}); 
</script>