<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Atlet;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AtletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $tahunSekarang = date('Y');

        // Definisi 5 Kategori beserta rentang umurnya
        $kategoriList = [
            ['nama' => 'KU-10 Mix', 'min_umur' => 6, 'max_umur' => 10],
            ['nama' => 'KU-12', 'min_umur' => 11, 'max_umur' => 12],
            ['nama' => 'KU-14', 'min_umur' => 13, 'max_umur' => 14],
            ['nama' => 'KU-16', 'min_umur' => 15, 'max_umur' => 16],
            ['nama' => 'KU-18', 'min_umur' => 17, 'max_umur' => 18],
        ];

        // Looping untuk setiap kategori
        foreach ($kategoriList as $kat) {
            
            // Siapkan 50 data per kategori: 25 Laki-laki (male) dan 25 Perempuan (female)
            $genders = array_merge(array_fill(0, 25, 'male'), array_fill(0, 25, 'female'));

            foreach ($genders as $gender) {
                
                // 1. Buat Tanggal Lahir sesuai rentang umur kategori
                $tahunLahirMin = $tahunSekarang - $kat['max_umur'];
                $tahunLahirMax = $tahunSekarang - $kat['min_umur'];
                $tanggalLahir = $faker->dateTimeBetween("$tahunLahirMin-01-01", "$tahunLahirMax-12-31")->format('Y-m-d');
                
                // 2. Tentukan Jenjang Sekolah berdasarkan Umur
                $umur = Carbon::parse($tanggalLahir)->age;
                $jenjangSekolah = '';
                $namaSekolah = '';

                if ($umur <= 12) {
                    $jenjangSekolah = 'SD';
                    $namaSekolah = 'SDN ' . $faker->numberBetween(1, 10) . ' ' . $faker->city;
                } elseif ($umur <= 15) {
                    $jenjangSekolah = 'SMP';
                    $namaSekolah = 'SMPN ' . $faker->numberBetween(1, 10) . ' ' . $faker->city;
                } else {
                    $jenjangSekolah = 'SMA';
                    $namaSekolah = 'SMAN ' . $faker->numberBetween(1, 10) . ' ' . $faker->city;
                }

                // 3. Buat Akun User
                $namaAtlet = $faker->name($gender);
                // Biar email rapi: hilangkan spasi, gelar, dan titik
                $emailBersih = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $namaAtlet)); 
                
                $user = User::create([
                    'name' => $namaAtlet,
                    'email' => $emailBersih . uniqid() . '@gmail.com',
                    'password' => Hash::make('Jethree@2025'), 
                    'role' => 'atlet',
                ]);

                // 4. Simpan ke Tabel Atlet
                Atlet::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $namaAtlet,
                    'nama_panggilan' => $faker->firstName($gender),
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $gender === 'male' ? 'Laki-laki' : 'Perempuan',
                    'alamat' => $faker->address,
                    'no_hp_atlet' => '08' . $faker->randomNumber(8, true) . $faker->randomNumber(2, true),
                    
                    // Data Sekolah & Akademi
                    'jenjang_sekolah' => $jenjangSekolah,
                    'nama_sekolah' => $namaSekolah,
                    'kategori' => $kat['nama'],
                    'posisi' => $faker->randomElement(['Point Guard', 'Shooting Guard', 'Small Forward', 'Power Forward', 'Center', 'Belum Ditentukan']),
                    'status' => 'Aktif',
                    
                    // Data Orang Tua
                    'nama_orang_tua' => $faker->name('male'), // Anggap Bapaknya yang daftar
                    'no_hp_orang_tua' => '08' . $faker->randomNumber(8, true) . $faker->randomNumber(2, true),
                ]);
            }
        }
    }
}