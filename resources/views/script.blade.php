<!-- <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script> -->
<script>
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

    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        console.log('AJAX Error:', thrownError || jqxhr.status);
        console.log('Error details:', {
            status: jqxhr.status,
            statusText: jqxhr.statusText,
            url: settings.url,
            thrownError: thrownError
        });

        // Handle 101 Switching Protocols / Connection Timeout / Status 0
        if (jqxhr.status === 101 || jqxhr.status === 0 || thrownError === 'timeout' || thrownError === 'error') {
            console.warn('Connection lost or timeout. Redirecting to login...');

            // Show notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Koneksi Terputus',
                    text: 'Sesi Anda telah berakhir atau koneksi terputus. Silakan login kembali.',
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(function() {
                    window.location.href = '/login';
                });
            } else {
                alert('Koneksi terputus. Silakan login kembali.');
                window.location.href = '/login';
            }

            // Fallback redirect if Swal doesn't close
            setTimeout(function() {
                window.location.href = '/login';
            }, 3000);
        }

        // Handle 304 Not Modified (Cache Hit - Not an error, just log it)
        if (jqxhr.status === 304) {
            console.info('304 Not Modified - Using cached response for:', settings.url);
            // Don't show error to user, this is normal cache behavior
            return;
        }

        // Handle 401 Unauthorized (session expired)
        if (jqxhr.status === 401) {
            console.warn('Session expired (401). Redirecting to login...');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesi Berakhir',
                    text: 'Silakan login kembali.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(function() {
                    window.location.href = '/login';
                });
            }
            setTimeout(function() {
                window.location.href = '/login';
            }, 2000);
        }

        // Handle 403 Forbidden
        if (jqxhr.status === 403) {
            console.warn('Access forbidden (403). Redirecting...');
            window.location.href = '/home';
        }

        // Handle 404 Not Found
        if (jqxhr.status === 404) {
            console.error('Resource not found (404):', settings.url);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Data Tidak Ditemukan',
                    text: 'Data yang Anda cari tidak tersedia.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }

        // Handle 422 Unprocessable Entity (Validation Error)
        if (jqxhr.status === 422) {
            console.warn('Validation error (422):', jqxhr.responseJSON);
            // Let the form handle validation errors
            return;
        }

        // Handle 500 Server Error
        if (jqxhr.status === 500) {
            console.error('Server error (500) occurred');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                });
            } else {
                alert('Terjadi kesalahan pada server. Silakan coba lagi.');
            }
        }

        // Handle 502 Bad Gateway / 503 Service Unavailable / 504 Gateway Timeout
        if ([502, 503, 504].includes(jqxhr.status)) {
            console.error('Server unavailable:', jqxhr.status);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Server Tidak Tersedia',
                    text: 'Server sedang sibuk atau dalam perawatan. Silakan coba beberapa saat lagi.',
                    timer: 5000,
                    showConfirmButton: true
                });
            } else {
                alert('Server sedang sibuk. Silakan coba beberapa saat lagi.');
            }
        }
    });

    // Global AJAX Success Handler (for logging)
    $(document).ajaxSuccess(function(event, jqxhr, settings, response) {
        // Log 304 responses for debugging
        if (jqxhr.status === 304) {
            console.log('✓ Cached response used:', settings.url);
        }
    });

    // Global AJAX Complete Handler (cleanup)
    $(document).ajaxComplete(function(event, jqxhr, settings) {
        // Can be used for cleanup or logging
        // console.log('AJAX request completed:', settings.url, 'Status:', jqxhr.status);
    });

    // Hearthbeat
    let heartbeatInterval;
    let idleTime = 0;
    const MAX_IDLE_TIME = 181; 

    $(document).on('click keypress mousemove scroll', function() {
        idleTime = 0;
    });

    // Start heartbeat checker
    function startHeartbeat() {
        heartbeatInterval = setInterval(function() {
            idleTime++;

            // if (idleTime % 10 === 0) {
            //     console.log('Idle time:', idleTime, 'seconds');
            // }

            // Check connection after MAX_IDLE_TIME seconds of idle
            if (idleTime >= MAX_IDLE_TIME) {
                console.log('Checking connection status... (idle for', idleTime, 'seconds)');

                $.ajax({
                    url: '/home',
                    method: 'HEAD',
                    timeout: 5000,
                    success: function() {
                        console.log('Connection OK - Session still active');
                        idleTime = 0;
                    },
                    error: function(jqxhr, status, error) {
                        console.warn('Connection check failed:', jqxhr.status, status, error);
                        if (jqxhr.status === 401 || jqxhr.status === 0 || status === 'timeout') {
                            console.warn('Session expired or connection lost. Redirecting to login...');
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sesi Berakhir',
                                    text: 'Sesi Anda telah berakhir karena tidak ada aktivitas.',
                                    timer: 5000,
                                    showConfirmButton: false
                                }).then(function() {
                                    window.location.href = '/login';
                                });
                            } else {
                                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                                window.location.href = '/login';
                            }
                        }
                    }
                });
            }
        }, 1000);
    }

    $(document).ready(function() {
        console.log('Heartbeat started - Will check connection after', MAX_IDLE_TIME, 'seconds of idle');
        startHeartbeat();
    });

    $(document).ready(function() {
        console.log('Tab persistence script loaded');
        
        let activeTab = window.location.hash || localStorage.getItem('activeTab');
        
        if (activeTab) {
            activeTab = activeTab.replace('#', '');
            console.log('Restoring tab:', activeTab);
            
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

$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    console.log('AJAX Error:', thrownError || jqxhr.status);
    
    // Handle 101 Switching Protocols / Connection Timeout
    if (jqxhr.status === 101 || jqxhr.status === 0 || thrownError === 'timeout') {
        console.warn('Connection lost or timeout. Redirecting to login...');
        
        // Show notification
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Koneksi Terputus',
                text: 'Sesi Anda telah berakhir atau koneksi terputus. Silakan login kembali.',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert('Koneksi terputus. Silakan login kembali.');
        }
        
        // Redirect to login after delay
        setTimeout(function() {
            window.location.href = '{{ route('login') }}';
        }, 3000);
    }
    
    // Handle 401 Unauthorized (session expired)
    if (jqxhr.status === 401) {
        console.warn('Session expired. Redirecting to login...');
        window.location.href = '{{ route('login') }}';
    }
    
    // Handle 403 Forbidden
    if (jqxhr.status === 403) {
        console.warn('Access forbidden. Redirecting...');
        window.location.href = '{{ route('home') }}';
    }
    
    // Handle 500 Server Error
    if (jqxhr.status === 500) {
        console.error('Server error occurred');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
            });
        }
    }
});
</script>