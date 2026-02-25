import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

// Fungsi untuk inisialisasi FullCalendar
function initFullCalendar() {
    // Register plugins
    Calendar.plugin(dayGridPlugin, timeGridPlugin, listPlugin);
    
    // Export Calendar as global variable so it can be accessed in blade templates
    window.FullCalendar = Calendar;
    
    // Tandai bahwa FullCalendar telah siap
    window.fullCalendarReady = true;
    
    console.log('FullCalendar initialized and ready');
}

// Inisialisasi setelah DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFullCalendar);
} else {
    // Jika DOM sudah siap, langsung inisialisasi
    initFullCalendar();
}