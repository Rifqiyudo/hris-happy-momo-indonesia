<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // 1. Kolom Metadata Absensi (Gunakan 'id' sebagai patokan paling aman)
            if (!Schema::hasColumn('payrolls', 'total_days_worked')) {
                $table->integer('total_days_worked')->default(0)->after('id');
            }

            // 2. Kolom Tunjangan Harian
            if (!Schema::hasColumn('payrolls', 'meal_allowance_per_day')) {
                $table->decimal('meal_allowance_per_day', 15, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('payrolls', 'transport_allowance_per_day')) {
                $table->decimal('transport_allowance_per_day', 15, 2)->default(0)->after('meal_allowance_per_day');
            }

            // 3. Kolom Jaminan Pensiun (JP) - BUAT DULU NOMINALNYA
            if (!Schema::hasColumn('payrolls', 'jp_deduction')) {
                $table->decimal('jp_deduction', 15, 2)->default(0)->after('kes_deduction');
            }

            // 4. Kolom Snapshot Persentase (BARU LETAKKAN SETELAH NOMINALNYA)
            if (!Schema::hasColumn('payrolls', 'jht_percent_snapshot')) {
                $table->decimal('jht_percent_snapshot', 5, 2)->default(2.00)->after('jht_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'kes_percent_snapshot')) {
                $table->decimal('kes_percent_snapshot', 5, 2)->default(1.00)->after('kes_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'jp_percent_snapshot')) {
                $table->decimal('jp_percent_snapshot', 5, 2)->default(1.00)->after('jp_deduction');
            }
        });

        // 5. Tahap Update: Pastikan kolom lama tidak NULL
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('family_allowance', 15, 2)->default(0)->change();
            $table->decimal('thr_amount', 15, 2)->default(0)->change();
            $table->decimal('bonus_amount', 15, 2)->default(0)->change();
            $table->decimal('jht_deduction', 15, 2)->default(0)->change();
            $table->decimal('kes_deduction', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations (Rollback).
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [
                'total_days_worked',
                'meal_allowance_per_day',
                'transport_allowance_per_day',
                'jp_deduction',
                'jht_percent_snapshot',
                'kes_percent_snapshot',
                'jp_percent_snapshot'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};