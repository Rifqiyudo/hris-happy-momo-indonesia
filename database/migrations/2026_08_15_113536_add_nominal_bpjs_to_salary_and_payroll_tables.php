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
        // 1. UPDATE TABEL SALARIES DENGAN PENGECEKAN AMAN
        Schema::table('salaries', function (Blueprint $table) {
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

        // 2. UPDATE TABEL PAYROLLS DENGAN PENGECEKAN AMAN
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'jht_deduction')) {
                $table->double('jht_deduction')->default(0)->after('bonus_amount');
            }
            if (!Schema::hasColumn('payrolls', 'kes_deduction')) {
                $table->double('kes_deduction')->default(0)->after('jht_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'jp_deduction')) {
                $table->double('jp_deduction')->default(0)->after('kes_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'jp_percent_snapshot')) {
                $table->double('jp_percent_snapshot')->default(0)->after('jp_deduction');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. ROLLBACK TABEL SALARIES DENGAN PENGECEKAN EKSISTENSI
        $salariesColumns = ['jht_deduction', 'kes_deduction', 'jp_deduction'];
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

        // 2. ROLLBACK TABEL PAYROLLS DENGAN PENGECEKAN EKSISTENSI
        $payrollsColumns = ['jht_deduction', 'kes_deduction', 'jp_deduction', 'jp_percent_snapshot'];
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