<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index()
    {
        return view('admin.kalender.index');
    }

    public function getEvents(Request $request)
    {
        // Eager loading 'pelatih' agar tidak lambat (N+1 Problem)
        $jadwals = Jadwal::with('pelatih')->get(); 
        $events = [];
        
        $datesWithBackground = [];

        foreach ($jadwals as $jadwal) {
            // Memastikan jam hanya ambil 5 karakter (HH:mm)
            $jamMulai   = substr($jadwal->jam_mulai, 0, 5);
            $jamSelesai = substr($jadwal->jam_selesai, 0, 5);

            $startIso = $jadwal->tanggal->format('Y-m-d') . 'T' . $jamMulai;
            $endIso   = $jadwal->tanggal->format('Y-m-d') . 'T' . $jamSelesai;
            $dateOnly = $jadwal->tanggal->format('Y-m-d');

            // --- 1. LOGIKA BACKGROUND BIRU ---
            if (!in_array($dateOnly, $datesWithBackground) && strcasecmp($jadwal->status, 'Dibatalkan') != 0) {
                $events[] = [
                    'start'           => $dateOnly,
                    'display'         => 'background',
                    'backgroundColor' => '#dbeafe', 
                    'allDay'          => true
                ];
                $datesWithBackground[] = $dateOnly;
            }

            // --- 2. LOGIKA WARNA EVENT ---
            $color = '#166534'; // Hijau Default
            if (strcasecmp($jadwal->status, 'Dibatalkan') == 0) {
                $color = '#ef4444'; // Merah
            }

            // --- 3. MENYUSUN DATA EVENT ---
            $events[] = [
                'id'    => $jadwal->id,
                'title' => 'Latihan: ' . $jadwal->kategori,
                'start' => $startIso,
                'end'   => $endIso,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'allDay'          => false, 
                'extendedProps' => [
                    'kategori' => $jadwal->kategori,
                    'edit_url' => route('jadwal.edit', $jadwal->id),
                    'lokasi'   => $jadwal->lokasi,
                    // PERBAIKAN: Gunakan ?? untuk jaga-jaga jika pelatih dihapus
                    'pelatih'  => $jadwal->pelatih->nama_lengkap ?? 'Coach Sudah Dihapus',
                    'materi'   => $jadwal->materi ?? '-', 
                    'jam_text' => $jamMulai . ' - ' . $jamSelesai,
                    'status'   => $jadwal->status
                ]
            ];
        }

        return response()->json($events);
    }
}