<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom-kolom baru ke tabel salaries dan payrolls dengan pengecekan otomatis.
     */
    public function up(): void
    {
        // 1. UPDATE TABEL MASTER GAJI (salaries)
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'overtime_amount')) {
                $table->double('overtime_amount')->default(0)->after('bonus_amount');
            }
            if (!Schema::hasColumn('salaries', 'jht_deduction')) {
                $table->double('jht_deduction')->default(0)->after('kes_percent');
            }
            if (!Schema::hasColumn('salaries', 'kes_deduction')) {
                $table->double('kes_deduction')->default(0)->after('jht_deduction');
            }
            if (!Schema::hasColumn('salaries', 'jp_deduction')) {
                $table->double('jp_deduction')->default(0)->after('kes_deduction');
            }
        });

        // 2. UPDATE TABEL RIWAYAT GAJI BULANAN (payrolls)
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'overtime_amount')) {
                $table->double('overtime_amount')->default(0)->after('bonus_amount');
            }
            if (!Schema::hasColumn('payrolls', 'jht_deduction')) {
                // Bergantung pada urutan tabel Anda, kita letakkan setelah bonus/allowance
                $table->double('jht_deduction')->default(0)->after('other_allowance');
            }
            if (!Schema::hasColumn('payrolls', 'kes_deduction')) {
                $table->double('kes_deduction')->default(0)->after('jht_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'jp_deduction')) {
                $table->double('jp_deduction')->default(0)->after('kes_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'jp_percent_snapshot')) {
                $table->double('jp_percent_snapshot')->default(0)->after('kes_percent_snapshot');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Fitur rollback jika terjadi kesalahan.
     */
    public function down(): void
    {
        // 1. ROLLBACK TABEL salaries (Menghapus sekaligus secara aman)
        $salariesColumns = ['overtime_amount', 'jht_deduction', 'kes_deduction', 'jp_deduction'];
        $dropSalaries = [];
        
        foreach ($salariesColumns as $col) {
            if (Schema::hasColumn('salaries', $col)) {
                $dropSalaries[] = $col;
            }
        }
        
        if (!empty($dropSalaries)) {
            Schema::table('salaries', function (Blueprint $table) use ($dropSalaries) {
                $table->dropColumn($dropSalaries);
            });
        }

        // 2. ROLLBACK TABEL payrolls (Menghapus sekaligus secara aman)
        $payrollsColumns = ['overtime_amount', 'jht_deduction', 'kes_deduction', 'jp_deduction', 'jp_percent_snapshot'];
        $dropPayrolls = [];
        
        foreach ($payrollsColumns as $col) {
            if (Schema::hasColumn('payrolls', $col)) {
                $dropPayrolls[] = $col;
            }
        }
        
        if (!empty($dropPayrolls)) {
            Schema::table('payrolls', function (Blueprint $table) use ($dropPayrolls) {
                $table->dropColumn($dropPayrolls);
            });
        }
    }
};