<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class FixUserBranch extends Command
{
    /**
     * Nama perintah yang akan dijalankan di terminal.
     */
    protected $signature = 'hris:fix-branch';

    /**
     * Deskripsi singkat perintah.
     */
    protected $description = 'Memperbaiki data branch_id yang kosong (N/A) pada tabel users secara masal';

    /**
     * Eksekusi logika perintah.
     */
    public function handle()
    {
        $this->info('--- Memulai Proses Sinkronisasi Unit Kerja ---');

        // 1. Cek apakah Branch ID 1 (Kantor Pusat) tersedia
        $defaultBranch = Branch::find(1);

        if (!$defaultBranch) {
            $this->error('Gagal: Data Cabang dengan ID 1 tidak ditemukan.');
            $this->warn('Pastikan Anda sudah menjalankan php artisan db:seed --class=BranchSeeder');
            return;
        }

        // 2. Hitung jumlah user yang branch_id nya NULL atau 0
        $invalidUsersCount = User::whereNull('branch_id')
                                ->orWhere('branch_id', 0)
                                ->count();

        if ($invalidUsersCount === 0) {
            $this->info('Semua data karyawan sudah memiliki Unit Kerja yang valid.');
            return;
        }

        if ($this->confirm("Ditemukan {$invalidUsersCount} karyawan tanpa Unit Kerja. Hubungkan ke [{$defaultBranch->name}]?")) {
            
            try {
                DB::beginTransaction();

                // 3. Update masal menggunakan Query Builder untuk kecepatan
                User::whereNull('branch_id')
                    ->orWhere('branch_id', 0)
                    ->update(['branch_id' => 1]);

                DB::commit();
                
                $this->info("Berhasil! {$invalidUsersCount} karyawan telah diperbarui ke Unit Kerja: {$defaultBranch->name}.");
                $this->comment('Sekarang silakan refresh halaman Monitoring Karyawan Anda.');

            } catch (\Exception $e) {
                DB::rollback();
                $this->error('Terjadi kesalahan saat update database: ' . $e->getMessage());
            }
        }
    }
}