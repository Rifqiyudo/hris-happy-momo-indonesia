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
            // 1. Tambah kolom unpaid_deduction_amount jika belum ada
            if (!Schema::hasColumn('payrolls', 'unpaid_deduction_amount')) {
                $table->decimal('unpaid_deduction_amount', 15, 2)->default(0);
            }

            // 2. Daftar kolom decimal untuk diubah/dipastikan default 0
            $decimalColumns = [
                'base_salary', 'position_allowance', 'family_allowance', 'meal_allowance_total', 
                'transport_allowance_total', 'thr_amount', 'bonus_amount', 'other_allowance', 
                'jht_deduction', 'kes_deduction', 'jp_deduction', 'routine_deduction', 
                'late_deduction', 'total_allowance', 'total_deduction', 'net_salary'
            ];

            foreach ($decimalColumns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->decimal($column, 15, 2)->default(0)->change();
                }
            }

            // 3. Tambahkan & Ubah kolom SNAPSHOT BPJS (Penyebab error 1364)
            $snapshotColumns = ['jht_percent_snapshot', 'kes_percent_snapshot', 'jp_percent_snapshot'];
            foreach ($snapshotColumns as $column) {
                if (!Schema::hasColumn('payrolls', $column)) {
                    $table->float($column)->default(0);
                } else {
                    $table->float($column)->default(0)->change();
                }
            }

            // 4. Daftar kolom integer untuk diubah
            $integerColumns = ['total_days_worked', 'total_leave'];
            foreach ($integerColumns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->integer($column)->default(0)->change();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan jika rollback
            if (Schema::hasColumn('payrolls', 'unpaid_deduction_amount')) {
                $table->dropColumn('unpaid_deduction_amount');
            }
            $snapshotColumns = ['jht_percent_snapshot', 'kes_percent_snapshot', 'jp_percent_snapshot'];
            foreach ($snapshotColumns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};