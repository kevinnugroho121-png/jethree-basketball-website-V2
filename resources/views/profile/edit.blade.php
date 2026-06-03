<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pengaturan Akun') }}
            </h2>
            
            {{-- TOMBOL KEMBALI KE DASHBOARD (Penting untuk Navigasi) --}}
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm font-bold hover:bg-gray-700 transition shadow-sm flex items-center">
                &laquo; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- GRID UTAMA: INFO PROFIL (KIRI) & PASSWORD (KANAN) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- KOLOM 1: EDIT INFORMASI PROFIL (Border Atas Biru) --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-t-4 border-blue-500">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Informasi Profil</h3>
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- KOLOM 2: UPDATE PASSWORD (Border Atas Kuning) --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-t-4 border-yellow-500">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">🔒 Ganti Password</h3>
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

            </div>

            {{-- ZONA BAHAYA: HAPUS AKUN (FULL WIDTH DI BAWAH) --}}
            {{-- Diberi background merah muda agar user waspada --}}
            <div class="p-4 sm:p-8 bg-red-50 shadow sm:rounded-lg border border-red-200">
                <div class="max-w-xl">
                    <h3 class="text-lg font-bold text-red-700 mb-2">⚠️ Danger Zone (Area Berbahaya)</h3>
                    <p class="text-sm text-red-600 mb-4">
                        Menghapus akun bersifat permanen. Semua data yang terkait dengan akun ini akan hilang selamanya. Harap berhati-hati.
                    </p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>