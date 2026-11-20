<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk memperbarui struktur tabel salaries.
     */
    public function up(): void
    {
        // TAHAP 1: SINKRONISASI NAMA KOLOM (RENAME)
        Schema::table('salaries', function (Blueprint $table) {
            if (Schema::hasColumn('salaries', 'gaji_pokok') && !Schema::hasColumn('salaries', 'base_salary')) {
                $table->renameColumn('gaji_pokok', 'base_salary');
            }
            if (Schema::hasColumn('salaries', 'tunjangan_tetap') && !Schema::hasColumn('salaries', 'position_allowance')) {
                $table->renameColumn('tunjangan_tetap', 'position_allowance');
            }
            if (Schema::hasColumn('salaries', 'tunjangan_harian') && !Schema::hasColumn('salaries', 'meal_allowance')) {
                $table->renameColumn('tunjangan_harian', 'meal_allowance');
            }
            if (Schema::hasColumn('salaries', 'tunjangan_lainnya') && !Schema::hasColumn('salaries', 'other_allowance')) {
                $table->renameColumn('tunjangan_lainnya', 'other_allowance');
            }
            if (Schema::hasColumn('salaries', 'iuran_rutin') && !Schema::hasColumn('salaries', 'routine_deduction')) {
                $table->renameColumn('iuran_rutin', 'routine_deduction');
            }
            if (Schema::hasColumn('salaries', 'bpjs_jht') && !Schema::hasColumn('salaries', 'jht_percent')) {
                $table->renameColumn('bpjs_jht', 'jht_percent');
            }
            if (Schema::hasColumn('salaries', 'bpjs_kes') && !Schema::hasColumn('salaries', 'kes_percent')) {
                $table->renameColumn('bpjs_kes', 'kes_percent');
            }
        });

        // TAHAP 2: PENAMBAHAN KOLOM BARU (Berdasarkan kolom yang sudah di-rename)
        Schema::table('salaries', function (Blueprint $table) {
            // Cek family_allowance
            if (!Schema::hasColumn('salaries', 'family_allowance')) {
                // Letakkan setelah position_allowance (yang baru di-rename)
                $table->decimal('family_allowance', 15, 2)->default(0)->after('position_allowance');
            }

            // Cek transport_allowance
            if (!Schema::hasColumn('salaries', 'transport_allowance')) {
                // Pastikan meal_allowance ada (hasil rename) sebelum menggunakan AFTER
                if (Schema::hasColumn('salaries', 'meal_allowance')) {
                    $table->decimal('transport_allowance', 15, 2)->default(0)->after('meal_allowance');
                } else {
                    $table->decimal('transport_allowance', 15, 2)->default(0);
                }
            }

            // Cek thr_amount
            if (!Schema::hasColumn('salaries', 'thr_amount')) {
                if (Schema::hasColumn('salaries', 'other_allowance')) {
                    $table->decimal('thr_amount', 15, 2)->default(0)->after('other_allowance');
                } else {
                    $table->decimal('thr_amount', 15, 2)->default(0);
                }
            }

            // Cek bonus_amount
            if (!Schema::hasColumn('salaries', 'bonus_amount')) {
                $table->decimal('bonus_amount', 15, 2)->default(0)->after('thr_amount');
            }
        });
    }

    /**
     * Batalkan migration (Rollback).
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            // 1. Hapus kolom baru yang ditambahkan
            $columnsToDrop = [];
            if (Schema::hasColumn('salaries', 'family_allowance')) $columnsToDrop[] = 'family_allowance';
            if (Schema::hasColumn('salaries', 'transport_allowance')) $columnsToDrop[] = 'transport_allowance';
            if (Schema::hasColumn('salaries', 'thr_amount')) $columnsToDrop[] = 'thr_amount';
            if (Schema::hasColumn('salaries', 'bonus_amount')) $columnsToDrop[] = 'bonus_amount';
            
            if (count($columnsToDrop) > 0) {
                $table->dropColumn($columnsToDrop);
            }

            // 2. Kembalikan nama ke Indonesia (Hanya jika kolom Inggris ada)
            if (Schema::hasColumn('salaries', 'base_salary')) $table->renameColumn('base_salary', 'gaji_pokok');
            if (Schema::hasColumn('salaries', 'position_allowance')) $table->renameColumn('position_allowance', 'tunjangan_tetap');
            if (Schema::hasColumn('salaries', 'meal_allowance')) $table->renameColumn('meal_allowance', 'tunjangan_harian');
            if (Schema::hasColumn('salaries', 'other_allowance')) $table->renameColumn('other_allowance', 'tunjangan_lainnya');
            if (Schema::hasColumn('salaries', 'routine_deduction')) $table->renameColumn('routine_deduction', 'iuran_rutin');
            if (Schema::hasColumn('salaries', 'jht_percent')) $table->renameColumn('jht_percent', 'bpjs_jht');
            if (Schema::hasColumn('salaries', 'kes_percent')) $table->renameColumn('kes_percent', 'bpjs_kes');
        });
    }
};