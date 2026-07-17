<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class BackupController extends Controller
{
    // === FITUR OTOMATISASI BACKUP DATABASE KE .ZIP (Poin 18) ===
    public function downloadBackup()
    {
        try {
            // 1. Ambil info nama database yang sedang aktif dari .env
            $dbName = config('database.connections.mysql.database');
            $tables = DB::select('SHOW TABLES');
            $keyName = 'Tables_in_' . $dbName;

            $sqlContent = "-- JETHREE BASKETBALL SYSTEM AUTO-BACKUP\n";
            $sqlContent .= "-- Tanggal Eksport: " . now()->toDateTimeString() . "\n\n";
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // 2. Looping ambil Struktur & Data dari setiap tabel database
            foreach ($tables as $table) {
                $tableName = $table->$keyName;

                // A. Ambil Struktur Query Pembuatan Tabel (CREATE TABLE)
                $createTableStructure = DB::select("SHOW CREATE TABLE `$tableName`")[0];
                $sqlContent .= "\n\n-- Structure untuk tabel: $tableName\n";
                $sqlContent .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sqlContent .= $createTableStructure->{'Create Table'} . ";\n\n";

                // B. Ambil Isi Data dari Tabel tersebut
                $rows = DB::select("SELECT * FROM `$tableName`");
                if (count($rows) > 0) {
                    $sqlContent .= "-- Dumping data untuk tabel: $tableName\n";
                    foreach ($rows as $row) {
                        $arrayRow = (array) $row;
                        $escapedValues = array_map(function ($value) {
                            if (is_null($value)) return 'NULL';
                            return "'" . addslashes($value) . "'";
                        }, $arrayRow);

                        $sqlContent .= "INSERT INTO `$tableName` VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                }
            }
            
            $sqlContent .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

            // 3. Buat nama file cadangan unik berdasarkan waktu saat ini
            $filenameSql = 'backup-jethree-' . date('Y-m-d-His') . '.sql';
            $filenameZip = 'backup-jethree-' . date('Y-m-d-His') . '.zip';

            // Tentukan folder penyimpanan sementara di laptop/server
            $storagePath = storage_path('app/public/');
            
            // Buat filenya sementara di storage
            file_put_contents($storagePath . $filenameSql, $sqlContent);

            // 4. Bungkus file .sql tadi ke dalam kemasan .zip rapi
            $zip = new ZipArchive;
            if ($zip->open($storagePath . $filenameZip, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($storagePath . $filenameSql, $filenameSql);
                $zip->close();
            }

            // Hapus file mentahan .sql biar tidak nyampah di server
            if (file_exists($storagePath . $filenameSql)) {
                unlink($storagePath . $filenameSql);
            }

            // 5. Lempar file .zip tersebut ke browser Admin agar otomatis terunduh
            return response()->download($storagePath . $filenameZip)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup database: ' . $e->getMessage());
        }
    }
}