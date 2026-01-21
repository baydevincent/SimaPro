@extends('layouts.admin')

@section('main-content')
<h5>Absensi Project - {{ $project->nama_project }}</h5>
<p>Tanggal - {{ $tanggal }} </p>


<table class="table">
    <thead>
        <tr>
            <th>Nama Worker</th>
            <th>Hadir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($workers as $worker)
        <tr>
            <td>{{ $worker->nama_worker }}</td>
            <td>
                <input type="checkbox"
                       class="toggle-absen"
                       data-worker="{{ $worker->id }}"
                       {{ $worker->hadir ? 'checked' : '' }}>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@push('scripts')
<script>
$(document).on('change', '.toggle-absen', function () {
    $.ajax({
        url: '/absensi/toggle',
        method: 'POST',
        data: {
            project_id: PROJECT_ID,
            worker_id: $(this).data('worker'),
            tanggal: TODAY
        }
    });
});
</script>
@endpush
