@extends('layouts.admin')

@section('main-content')
<div class="card shadow">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kalender</h6>
                </div>
                <div class="card-body">
                    <div id='calendar' class="calendar-responsive" style="min-height: 600px; border: 0px solid #ccc;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .calendar-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* Toolbar responsive */
        .fc .fc-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1rem;
        }

        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin: 0.25rem 0;
        }

        .fc .fc-toolbar-chunk:first-child {
            order: 1;
        }

        .fc .fc-toolbar-chunk:nth-child(2) {
            order: 3;
        }

        .fc .fc-toolbar-chunk:last-child {
            order: 2;
        }

        /* Mobile view adjustments */
        @media (max-width: 767.98px) {
            .fc .fc-toolbar {
                flex-wrap: wrap;
            }

            .fc .fc-button-group {
                margin: 0.25rem 0;
                width: 100%;
                justify-content: center;
            }

            .fc .fc-button {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
                margin: 0 0.125rem;
            }

            .fc .fc-toolbar-title {
                font-size: 1.1rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Perbesar ukuran teks tanggal di mobile */
            .fc-daygrid-day-number {
                font-size: 1rem !important;
                padding: 0.3rem !important;
                line-height: 1.5 !important;
            }

            .fc-daygrid-event {
                font-size: 0.75rem;
                padding: 0.125rem 0.25rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* Tambahkan margin untuk meningkatkan ruang antar elemen */
            .fc-daygrid-day {
                padding: 0.1rem !important;
            }
        }

        @media (min-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: row;
                align-items: center;
            }

            .fc .fc-toolbar-chunk {
                width: auto;
                justify-content: flex-start;
            }

            .fc .fc-toolbar-chunk:first-child {
                order: initial;
            }

            .fc .fc-toolbar-chunk:nth-child(2) {
                order: initial;
            }

            .fc .fc-toolbar-chunk:last-child {
                order: initial;
            }
        }

        /* Calendar cell adjustments for mobile */
        .fc-daygrid-day-number {
            font-size: 0.875rem;
            padding: 0.25rem;
        }

        .fc-daygrid-event {
            font-size: 0.75rem;
            padding: 0.125rem 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Mobile view container */
        .fc-view-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Fix for overlapping with sidebar/navbar */
        #calendar {
            z-index: 1;
        }

        .fc-view-container {
            z-index: 1;
        }

        .fc-popover {
            z-index: 1000 !important;
        }

        .fc-event {
            z-index: 2;
        }

        /* Mobile-specific calendar view adjustments */
        @media (max-width: 575.98px) {
            #calendar {
                font-size: 0.9rem; /* Perbesar ukuran font umum */
            }

            .fc .fc-button {
                padding: 0.3rem 0.6rem; /* Sesuaikan ukuran tombol */
                font-size: 0.8rem;
                min-height: 1.6rem;
            }

            .fc-daygrid-day-number {
                font-size: 1.1rem !important; /* Perbesar ukuran angka tanggal */
                padding: 0.35rem !important;
            }

            .fc-daygrid-event {
                font-size: 0.7rem;
                padding: 0.1rem 0.2rem;
            }
            
            /* Tambahkan jarak antar kolom di tampilan mobile */
            .fc-dayGridWeek-view .fc-daygrid-day,
            .fc-dayGridDay-view .fc-daygrid-day {
                padding: 0.2rem !important;
            }
        }

        /* Ensure proper spacing from sidebar */
        @media (min-width: 768px) {
            #calendar {
                padding-left: 10px;
            }
        }
        
        .responsive-popup {
            width: 90% !important;
            max-width: 500px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded and parsed');

            var calendarEl = document.getElementById('calendar');
            console.log('Calendar element found:', !!calendarEl);

            if (!calendarEl) {
                console.error('Calendar element not found');
                return;
            }

            if (typeof FullCalendar !== 'undefined' && typeof FullCalendar.Calendar !== 'undefined') {
                console.log('FullCalendar is ready');
                console.log('Creating calendar instance...');

                // Deteksi ukuran layar untuk menentukan tampilan awal
                function getInitialView() {
                    if (window.innerWidth < 768) {
                        return 'timeGridWeek'; // Tampilan mingguan untuk mobile
                    } else {
                        return 'dayGridMonth'; // Tampilan bulanan untuk desktop
                    }
                }

                var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: getInitialView(), // Gunakan fungsi untuk menentukan tampilan awal
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
                                width: 'auto',
                                customClass: {
                                    popup: 'responsive-popup'
                                }
                            });
                        },
                        dateClick: function(info) {
                            // Tambahkan fungsi jika pengguna mengklik tanggal
                            console.log('Clicked on: ' + info.dateStr);
                        },
                        locale: 'id', // Bahasa Indonesia
                        // Responsif settings - ubah aspect ratio untuk mobile
                        height: 'auto',
                        aspectRatio: window.innerWidth < 768 ? 1.2 : 1.8, // Rasio layar
                        stickyHeaderDates: true,

                        // Tampilan yang dioptimalkan untuk mobile
                        views: {
                            dayGridMonth: {
                                eventTimeFormat: {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    omitZeroMinute: true,
                                    meridiem: 'short'
                                }
                            },
                            timeGridWeek: {
                                slotDuration: '01:00:00', // Durasi slot 1 jam
                                slotLabelInterval: '02:00:00', // Label setiap 2 jam
                            },
                            dayGridWeek: {
                                dayHeaderFormat: { weekday: 'short' } // Format header hari lebih pendek
                            }
                        }
                    });

                    console.log('Rendering calendar...');
                    calendar.render();
                    console.log('Calendar rendered successfully');

                    // Fungsi untuk menyesuaikan kalender saat ukuran window berubah
                    window.addEventListener('resize', function() {
                        setTimeout(function() {
                            // Ubah tampilan dan rasio aspek berdasarkan ukuran layar baru
                            if (window.innerWidth < 768) {
                                calendar.setOption('aspectRatio', 1.2);
                                if (calendar.view.type !== 'dayGridWeek' && calendar.view.type !== 'timeGridWeek') {
                                    calendar.changeView('dayGridWeek');
                                }
                            } else {
                                calendar.setOption('aspectRatio', 1.8);
                                if (calendar.view.type === 'dayGridWeek' || calendar.view.type === 'timeGridWeek') {
                                    calendar.changeView('dayGridMonth');
                                }
                            }

                            calendar.updateSize();
                        }, 300);
                    });

                    // Fungsi untuk menyesuaikan kalender saat sidebar toggle
                    document.addEventListener('click', function(e) {
                        if(e.target && (e.target.id === 'sidebarToggle' || e.target.closest('#sidebarToggle') ||
                           e.target.id === 'sidebarToggleTop' || e.target.closest('#sidebarToggleTop'))) {
                            setTimeout(function() {
                                calendar.updateSize();
                            }, 350); // Beri sedikit waktu untuk animasi toggle selesai
                        }
                    });
                } else {
                    console.error('FullCalendar is not loaded properly');
                    alert('Error: FullCalendar tidak dimuat dengan benar. Silakan refresh halaman.');
                    console.log('FullCalendar object:', typeof FullCalendar);
                    console.log('FullCalendar.Calendar:', typeof FullCalendar?.Calendar);
                    console.log('Available properties on window:', Object.keys(window).filter(key => key.toLowerCase().includes('calendar')));
                }
        });
    </script>

    <!-- SweetAlert2 for modal dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush