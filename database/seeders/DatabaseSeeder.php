<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Mengatur urutan eksekusi data agar integritas relasi antar tabel terjaga.
     */
    public function run(): void
    {
        /**
         * 1. PRE-CONDITION
         * Matikan foreign key checks untuk menghindari error relasi saat pembersihan/pengisian tabel.
         */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            /**
             * 2. EXECUTION LIST
             * Urutan wajib mutlak: Master data -> Relasional Master -> User -> Transactional.
             */
            $this->call([
                // --- Tahap 1: Dasar Organisasi ---
                BranchSeeder::class,       // 1. Mengisi data Cabang/PT (PT Happy Momo, dll)
                DepartmentSeeder::class,   // 2. Mengisi data Departemen (HRD, Produksi, Operasional)
                PositionSeeder::class,     // 3. Mengisi data Jabatan (Manager, Staff, Supervisor)
                
                // --- Tahap 2: Aturan Kerja, Pola, & Kompensasi ---
                ShiftSeeder::class,          // 4. Mengisi Master Jam Kerja (Pagi, Sabtu, Malam, Libur)
                WorkingPatternSeeder::class, // 5. Mengisi Pola Mingguan / Cluster Jadwal
                AllowanceSeeder::class,      // 6. Mengisi jenis tunjangan (Tetap/Harian)

                // --- Tahap 3: Sumber Daya Manusia (SDM) ---
                // Dijalankan SETELAH Organisasi (Tahap 1) dibuat agar relasi user mutlak valid.
                UserSeeder::class,         

                // --- Tahap 4: Informasi & Konten ---
                AnnouncementSeeder::class, // Pengumuman internal perusahaan
            ]);

            // Pesan sukses opsional di terminal
            $this->command->info('Seluruh Data Seeder Berhasil Dieksekusi Secara Berurutan!');

        } catch (\Exception $e) {
            /**
             * ERROR HANDLING
             * Tangkap error agar tidak merusak struktur tabel, dan rekam di log.
             */
            Log::error('Seeding Database Gagal: ' . $e->getMessage());
            $this->command->error('Seeding gagal! Terdapat error pada relasi atau data. Detail: ' . $e->getMessage());
        } finally {
            /**
             * 3. POST-CONDITION (CRITICAL)
             * Hidupkan kembali foreign key checks DALAM KONDISI APAPUN (berhasil maupun gagal)
             * untuk menjaga keamanan dan integritas database secara permanen.
             */
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}