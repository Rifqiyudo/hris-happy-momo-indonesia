<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat atau memperbarui tabel salaries.
     * Mendukung komponen gaji transparan (Earnings, Variable, Annual, Deductions).
     */
    public function up()
    {
        // 1. Cek jika tabel belum ada, buat baru dengan struktur lengkap
        if (!Schema::hasTable('salaries')) {
            Schema::create('salaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                
                // --- PENDAPATAN TETAP (FIXED EARNINGS) ---
                $table->decimal('base_salary', 15, 2)->default(0); // Gaji Pokok
                $table->decimal('position_allowance', 15, 2)->default(0); // Tj. Jabatan
                $table->decimal('family_allowance', 15, 2)->default(0); // Tj. Keluarga
                
                // --- PENDAPATAN VARIABEL / HARIAN (VARIABLE EARNINGS) ---
                // Disimpan nilai per harinya untuk transparansi perhitungan hariaan
                $table->decimal('meal_allowance_per_day', 15, 2)->default(0); 
                $table->decimal('transport_allowance_per_day', 15, 2)->default(0);
                $table->decimal('other_allowance', 15, 2)->default(0);
                
                // --- PENDAPATAN TAHUNAN / INSIDENTIL (ANNUAL EARNINGS) ---
                $table->decimal('thr_amount', 15, 2)->default(0);
                $table->decimal('bonus_amount', 15, 2)->default(0);
                
                // --- PERSENTASE POTONGAN BPJS (DEDUCTIONS RATE) ---
                // Disimpan dalam persen (%) untuk mempermudah audit slip
                $table->float('jht_percent')->default(2.0); // Default 2% Karyawan
                $table->float('kes_percent')->default(1.0); // Default 1% Karyawan
                $table->float('jp_percent')->default(1.0);  // Default 1% Karyawan
                
                // --- POTONGAN RUTIN LAINNYA ---
                $table->decimal('routine_deduction', 15, 2)->default(0); // Potongan tetap kasbon/asuransi lain
                
                $table->timestamps();
            });
        } else {
            // 2. Jika tabel sudah ada, lakukan sinkronisasi kolom agar tidak error
            Schema::table('salaries', function (Blueprint $table) {
                
                // Sinkronisasi Nama Kolom (Rename jika masih menggunakan format lama)
                if (Schema::hasColumn('salaries', 'gaji_pokok') && !Schema::hasColumn('salaries', 'base_salary')) {
                    $table->renameColumn('gaji_pokok', 'base_salary');
                }

                // Tambahkan kolom tunjangan tetap jika belum ada
                if (!Schema::hasColumn('salaries', 'position_allowance')) {
                    $table->decimal('position_allowance', 15, 2)->default(0)->after('base_salary');
                }
                if (!Schema::hasColumn('salaries', 'family_allowance')) {
                    $table->decimal('family_allowance', 15, 2)->default(0)->after('position_allowance');
                }

                // Update kolom tunjangan harian untuk transparansi
                if (!Schema::hasColumn('salaries', 'meal_allowance_per_day')) {
                    $table->decimal('meal_allowance_per_day', 15, 2)->default(0)->after('family_allowance');
                }
                if (!Schema::hasColumn('salaries', 'transport_allowance_per_day')) {
                    $table->decimal('transport_allowance_per_day', 15, 2)->default(0)->after('meal_allowance_per_day');
                }

                // Tambahkan komponen insidentil (THR/Bonus)
                if (!Schema::hasColumn('salaries', 'thr_amount')) {
                    $table->decimal('thr_amount', 15, 2)->default(0)->after('other_allowance');
                }
                if (!Schema::hasColumn('salaries', 'bonus_amount')) {
                    $table->decimal('bonus_amount', 15, 2)->default(0)->after('thr_amount');
                }
                
                // Sinkronisasi Penamaan BPJS (%)
                if (Schema::hasColumn('salaries', 'bpjs_jht') && !Schema::hasColumn('salaries', 'jht_percent')) {
                    $table->renameColumn('bpjs_jht', 'jht_percent');
                }
                if (Schema::hasColumn('salaries', 'bpjs_kes') && !Schema::hasColumn('salaries', 'kes_percent')) {
                    $table->renameColumn('bpjs_kes', 'kes_percent');
                }
                if (!Schema::hasColumn('salaries', 'jp_percent')) {
                    $table->float('jp_percent')->default(1.0)->after('kes_percent');
                }
            });
        }
    }

    /**
     * Batalkan migration.
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
};