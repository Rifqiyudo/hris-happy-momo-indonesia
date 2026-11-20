<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk memperbaiki tabel payrolls.
     * Menambahkan kolom snapshot persentase BPJS (JHT, KES, JP)
     * dan memastikan default value aman bagi MySQL.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // 1. Snapshot Persentase JHT (Default 2%)
            if (!Schema::hasColumn('payrolls', 'jht_percent_snapshot')) {
                $table->decimal('jht_percent_snapshot', 5, 2)->default(2.00)->after('jht_deduction');
            }

            // 2. Snapshot Persentase Kesehatan (Default 1%)
            if (!Schema::hasColumn('payrolls', 'kes_percent_snapshot')) {
                $table->decimal('kes_percent_snapshot', 5, 2)->default(1.00)->after('kes_deduction');
            }

            // 3. Nominal Potongan Jaminan Pensiun (JP)
            if (!Schema::hasColumn('payrolls', 'jp_deduction')) {
                $table->decimal('jp_deduction', 15, 2)->default(0)->after('kes_percent_snapshot');
            }

            // 4. Snapshot Persentase Jaminan Pensiun (JP) (Default 1%)
            if (!Schema::hasColumn('payrolls', 'jp_percent_snapshot')) {
                $table->decimal('jp_percent_snapshot', 5, 2)->default(1.00)->after('jp_deduction');
            }

            // 5. Rekap Absensi Tambahan
            if (!Schema::hasColumn('payrolls', 'total_late_minutes')) {
                $table->integer('total_late_minutes')->default(0)->after('total_days_worked');
            }
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     * Diperkuat dengan pengecekan hasColumn untuk mencegah error SQL 1091.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [
                'jht_percent_snapshot',
                'kes_percent_snapshot',
                'jp_percent_snapshot',
                'jp_deduction',
                'total_late_minutes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};