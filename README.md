# 🏀 JeThree Basketball Academy - Core Backend & Web System

<p align="center">
  <b style="font-size: 20px;">Sistem Informasi Manajemen Akademi Basket, Finansial, & RESTful API Central</b>
  <br>
  <i>Dikembangkan khusus untuk JeThree Basketball Academy</i>
</p>

---

## 📌 Ringkasan Sistem

**JeThree Website & Backend API** adalah pusat kendali (*central hub*) untuk JeThree Basketball Academy. Berbasis framework Laravel, sistem ini menangani seluruh manajemen data utama, finansial akademi, operasional jadwal, pencatatan presensi, hingga penyediaan **RESTful API Endpoint** aman (dengan sistem token Sanitium/Bearer) yang dikonsumsi oleh aplikasi mobile (`jethree_mobile`).

---

## 🚀 Fitur Utama & Modul Backend

### 1. Management Information System (Web Admin)
- **Dashboard Eksekutif (Owner & Admin):** Visualisasi statistik jumlah atlet aktif, pelatih, pendapatan bulanan, dan ringkasan kehadiran.
- **Manajemen Atlet & Kelompok Umur (KU):** Pengelolaan basis data atlet lengkap dengan status keanggotaan dan pembagian kelompok umur (U-10, U-12, U-14, U-16, KU Putra/Putri).
- **Manajemen Pelatih & Jadwal:** Penjadwalan latihan harian, penetapan pelatih utama, dan pengaturan alokasi lapangan.
- **Modul Finansial & Pembayaran SPP:**
  - Pencatatan tagihan SPP bulanan atlet.
  - Integrasi pembayaran dengan verifikasi bukti transfer/QRIS.
  - Rekapitulasi porsi honorarium mengajar pelatih berdasarkan kehadiran dan porsi *Takeover*.

### 2. RESTful API Gateway (Mobile Integration)
- **Authentication Services:** Endpoint Login, Logout, dan Pengelolaan Profil Pelatih/Owner/Atlet berbasis *Bearer Token*.
- **Sync Absensi & Evaluasi:** Endpoint penyedia data jadwal harian, penyerapan input presensi massal, dan penyimpanan 4 komponen nilai kompetensi skill atlet (*Dribble, Passing, Shooting, IQ/Mental*).
- **Automated Takeover Engine:** Algoritma pemrosesan pengalihan hak melatih harian, otomatisasi kalkulasi porsi honorarium, dan penguncian status *ter-takeover*.

---

## 🛠️ Tech Stack & Dependencies

- **Framework:** Laravel 11 (PHP ^8.1)
- **Database:** MySQL / MariaDB
- **Authentication:** Laravel Sanctum (Token-Based REST API)
- **Asset Bundler:** Vite
- **UI Components:** Blade Templates, Bootstrap / Tailwind CSS, FontAwesome / Tabler Icons

---