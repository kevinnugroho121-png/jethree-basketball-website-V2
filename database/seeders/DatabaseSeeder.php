<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Atlet;
use App\Models\Pelatih;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // 1. AKUN OWNER (Pemilik / Ketua Yayasan)
        // ==========================================
        User::create([
            'name' => 'Owner Jethree',
            'email' => 'owner@gmail.com',      // Login Owner
            'password' => Hash::make('Jethree@2025'),
            'role' => 'owner',
        ]);

        // ==========================================
        // 2. AKUN ADMIN (Pengelola Harian)
        // ==========================================
        User::create([
            'name' => 'Admin Operasional',
            'email' => 'admin@gmail.com',      // Login Admin
            'password' => Hash::make('Jethree@2025'),
            'role' => 'admin',
        ]);

        // ==========================================
        // 3. AKUN PELATIH (Coach)
        // ==========================================
        $userPelatih = User::create([
            'name' => 'Coach Budi',
            'email' => 'pelatih@gmail.com',    // Login Pelatih
            'password' => Hash::make('Jethree@2025'),
            'role' => 'pelatih',
        ]);
        
        // Data Detail Pelatih
        Pelatih::create([
            'user_id' => $userPelatih->id,
            'nama_lengkap' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'lisensi' => 'Lisensi B',
            'status' => 'Aktif',
            'foto_profil' => null, 
        ]);

        // ==========================================
        // 4. AKUN ATLET (Siswa)
        // ==========================================
        $userAtlet = User::create([
            'name' => 'Kevin Nugroho',
            'email' => 'kevin@gmail.com',      // Login Atlet
            'password' => Hash::make('Jethree@2025'),
            'role' => 'atlet',
        ]);

        // Data Detail Atlet
        Atlet::create([
            'user_id' => $userAtlet->id,
            'nama_lengkap' => 'Kevin Nugroho',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '2012-05-20', // Umur sekitar 12-13 tahun (KU-12)
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Mawar Melati No. 10, Surabaya',
            'sekolah' => 'SMPN 1 Surabaya',
            'kategori_umur' => 'KU-12', // PENTING: Harus sama dengan Jadwal biar Notif Masuk
            'no_hp' => '085678901234',
            'nama_ortu' => 'Bapak Nugroho',
            'no_hp_ortu' => '08111222333',
            'status' => 'Aktif',
            'tanggal_gabung' => now(),
            'foto_profil' => null,
        ]);
    }
}