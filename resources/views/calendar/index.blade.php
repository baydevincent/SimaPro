@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Project Calendar</h1>
        <a href="{{ route('project.create') }}" class="btn btn-sm btn-primary shadow-sm mt-2 mt-md-0">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Project
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline Project</h6>
                </div>
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($events),
                eventClick: function(info) {
                    // Tampilkan detail event saat diklik
                    const event = info.event;
                    let details = '<strong>' + event.title + '</strong><br>';
                    
                    if (event.extendedProps.type === 'project') {
                        details += 'Tipe: Proyek<br>';
                        details += 'Nilai: Rp ' + new Intl.NumberFormat('id-ID').format(event.extendedProps.nilai_project || 0) + '<br>';
                        details += 'Progress: ' + event.extendedProps.progress + '%<br>';
                        details += '<a href="/project/' + event.extendedProps.id + '" class="btn btn-sm btn-primary mt-2">Lihat Detail</a>';
                    } else if (event.extendedProps.type === 'task') {
                        details += 'Tipe: Tugas<br>';
                        details += 'Proyek: ' + event.extendedProps.project_name + '<br>';
                        details += '<a href="/project/' + event.extendedProps.project_id + '/task/' + event.extendedProps.id + '" class="btn btn-sm btn-info mt-2">Lihat Tugas</a>';
                    }
                    
                    // Tampilkan detail di modal atau alert
                    Swal.fire({
                        title: event.title,
                        html: details,
                        icon: 'info',
                        showCloseButton: true,
                        focusConfirm: false,
                        // confirmButtonText: 'Tutup',
                    });
                },
                dateClick: function(info) {
                    // Tambahkan fungsi jika pengguna mengklik tanggal
                    console.log('Clicked on: ' + info.dateStr);
                },
                locale: 'id' // Bahasa Indonesia
            });
            
            calendar.render();
        });
    </script>
    
    <!-- SweetAlert2 for modal dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush