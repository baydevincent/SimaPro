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
</script>