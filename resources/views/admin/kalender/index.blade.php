<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kalender Latihan Tahunan') }}
        </h2>
    </x-slot>

    {{-- 1. LIBRARY --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        #calendar { font-family: 'Figtree', sans-serif; max-width: 100%; margin: 0 auto; }
        
        /* HEADER BULAN HIJAU */
        .fc-multimonth-title { 
            background-color: #166534; color: white; padding: 6px 0; 
            font-size: 0.85rem !important; font-weight: 800 !important; 
            text-transform: uppercase; border-radius: 4px 4px 0 0; 
        }

        .fc-theme-standard td, .fc-theme-standard th { border: 1px solid #cbd5e1 !important; }
        .fc-col-header-cell { background-color: #f3f4f6; color: #1f2937; font-size: 0.7rem; padding: 2px 0; font-weight: bold; }
        .fc-daygrid-day-number { font-size: 0.7rem; font-weight: 600; color: #374151; width: 100%; text-align: center; padding: 2px 0; }
        
        /* --- LOGIKA WARNA BARU --- */
        
        /* 1. Warna Biru (Background Event) agar terlihat jelas */
        .fc-bg-event {
            opacity: 1 !important; 
        }

        /* 2. Warna Kuning saat Mouse Hover (Prioritas) */
        .fc-daygrid-day:hover { 
            background-color: #fef08a !important; 
            cursor: pointer; 
            transition: background-color 0.1s;
        }

        /* 3. Trik: Sembunyikan biru saat hover agar kuning terlihat */
        .fc-daygrid-day:hover .fc-bg-event {
            opacity: 0 !important; 
        }
        
        /* 4. Hari Ini (Hijau) - Prioritas Tertinggi */
        .fc-day-today { background-color: #dcfce7 !important; } 
        /* Sembunyikan biru jika hari ini */
        .fc-day-today .fc-bg-event { opacity: 0 !important; }

        .fc-event { border: none; cursor: pointer; margin: 1px 0 !important; }
    </style>

    <div class="py-6 px-4 w-full">
        <div class="bg-white border border-gray-400 shadow-sm rounded-sm overflow-hidden">
            
            {{-- TOOLBAR --}}
            <div class="p-4 border-b border-gray-300 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-wider text-gray-600">
                    <span class="flex items-center gap-1">
                        <span class="w-4 h-4 rounded-sm bg-[#dcfce7] border border-gray-300"></span> Hari Ini
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-4 h-4 rounded-sm bg-[#dbeafe] border border-gray-300"></span> Ada Latihan
                    </span>
                </div>

                @if(Auth::user()->role === 'admin')
                <a href="{{ route('jadwal.create') }}" class="h-9 inline-flex items-center px-4 bg-blue-600 border border-blue-700 rounded-sm text-xs font-bold text-white uppercase tracking-widest hover:bg-blue-700 shadow-sm transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Jadwal
                </a>
                @endif
            </div>

            <div class="p-4 bg-white min-h-[500px]">
                <div class="mb-6 p-2 bg-yellow-50 border border-yellow-200 text-yellow-800 text-xs flex items-center justify-center rounded">
                    <span>💡 <strong>Tips:</strong> Klik pada tanggal berwarna biru untuk melihat detail jadwal latihan.</span>
                </div>

                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            const userRole = "{{ Auth::user()->role }}";
            
            // PERUBAHAN PENTING:
            // Kita menghapus baris "const datesWithEvents = ..." 
            // karena data warna biru sekarang sudah otomatis ikut di dalam events.
            
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'multiMonthYear',
                    multiMonthMaxColumns: 3, 
                    showNonCurrentDates: false,
                    locale: 'id',
                    firstDay: 1, 
                    dayMaxEvents: true, 

                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: '' 
                    },
                    buttonText: { today: 'Tahun Ini' },

                    events: {
                        url: "{{ route('kalender.events') }}",
                        // Tambahkan parameter waktu agar browser tidak menyimpan cache lama
                        extraParams: { custom_param: 'nocache_' + new Date().getTime() }
                    },

                    // --- LOGIKA KLIK TANGGAL ---
                    dateClick: function(info) {
                        if (userRole === 'admin') {
                            window.location.href = "{{ route('jadwal.create') }}" + "?date=" + info.dateStr;
                        }
                    },

                    // --- LOGIKA KLIK EVENT ---
                    eventClick: function(info) {
                        // Jangan bereaksi jika yang diklik cuma background biru
                        if (info.event.display === 'background') return;

                        const props = info.event.extendedProps;

                        if (userRole === 'admin') {
                            window.location.href = props.edit_url;
                        } else {
                            Swal.fire({
                                title: `<span class="text-green-800">${info.event.title}</span>`,
                                html: `
                                    <div class="text-left text-sm space-y-2 mt-4">
                                        <div class="grid grid-cols-3 gap-2">
                                            <span class="font-bold text-gray-500">Waktu:</span>
                                            <span class="col-span-2 font-medium">${props.jam_text}</span>
                                            <span class="font-bold text-gray-500">Lokasi:</span>
                                            <span class="col-span-2 font-medium">${props.lokasi}</span>
                                            <span class="font-bold text-gray-500">Coach:</span>
                                            <span class="col-span-2 font-medium">${props.pelatih}</span>
                                        </div>
                                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-700">
                                            <strong class="block mb-1 text-xs uppercase text-gray-400">Materi Latihan:</strong>
                                            <p>${props.materi}</p>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#166534'
                            });
                        }
                    },

                    // --- KUSTOMISASI TAMPILAN EVENT ---
                    eventContent: function(arg) {
                        // Jangan ubah tampilan jika itu cuma background biru
                        if (arg.event.display === 'background') return;

                        return { 
                            html: `
                                <div class="fc-event-main-frame flex justify-center items-center h-full w-full">
                                    <div class="text-[7px] md:text-[8px] font-bold text-white uppercase px-1 py-0.5 overflow-hidden text-ellipsis whitespace-nowrap">
                                        ${arg.event.title}
                                    </div>
                                </div>
                            ` 
                        };
                    },

                    eventDidMount: function(info) {
                        // Pastikan style hanya diterapkan ke event biasa, bukan background
                        if (info.event.display !== 'background') {
                            info.el.style.backgroundColor = info.event.backgroundColor;
                            info.el.style.borderRadius = '2px';
                        }
                    }
                });
                
                calendar.render();
            }
        });
    </script>
</x-app-layout>