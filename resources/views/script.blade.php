<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

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
                    // Tampilkan alert sukses
                    alert(response.message || 'Absen berhasil dihapus');

                    // Refresh halaman atau hapus baris dari tabel
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Tampilkan alert error
                    alert('Gagal menghapus Absen: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        }
    });

$(document).on('click', '.btn-edit-worker', function() {
        const button = $(this);
        const workerId = button.data('id');
        const url = button.data('url');
        
        console.log('Edit worker clicked:', workerId, url);
        
        // Reset form dan alert
        $('#formEditWorker')[0].reset();
        $('#editErrorBox').addClass('d-none');
        $('#editSuccessBox').addClass('d-none');
        
        // Show loading state
        $('#btnUpdateWorker').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        // Fetch worker data
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Fill form dengan data worker
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
        
        // Reset alerts
        $('#editErrorBox').addClass('d-none');
        $('#editSuccessBox').addClass('d-none');
        
        // Show loading state
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
                    
                    // Tutup modal setelah 1 detik
                    setTimeout(function() {
                        $('#modalEditWorker').modal('hide');
                        // Reload halaman untuk update data
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
</script>