<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk melengkapi komponen gaji di tabel salaries.
     * Penambahan kolom ini disesuaikan dengan input Master Gaji Kolektif.
     */
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            // 1. Tunjangan Keluarga (Fixed)
            if (!Schema::hasColumn('salaries', 'family_allowance')) {
                $table->decimal('family_allowance', 15, 2)->default(0)->after('position_allowance');
            }

            // 2. Tunjangan Harian (Variable)
            if (!Schema::hasColumn('salaries', 'meal_allowance')) {
                $table->decimal('meal_allowance', 15, 2)->default(0)->after('family_allowance');
            }
            if (!Schema::hasColumn('salaries', 'transport_allowance')) {
                $table->decimal('transport_allowance', 15, 2)->default(0)->after('meal_allowance');
            }

            // 3. Pendapatan Non-Rutin
            if (!Schema::hasColumn('salaries', 'thr_amount')) {
                $table->decimal('thr_amount', 15, 2)->default(0)->after('transport_allowance');
            }
            if (!Schema::hasColumn('salaries', 'bonus_amount')) {
                $table->decimal('bonus_amount', 15, 2)->default(0)->after('thr_amount');
            }
            if (!Schema::hasColumn('salaries', 'other_allowance')) {
                $table->decimal('other_allowance', 15, 2)->default(0)->after('bonus_amount');
            }

            // 4. Potongan Rutin & Persentase BPJS (DPI)
            if (!Schema::hasColumn('salaries', 'routine_deduction')) {
                $table->decimal('routine_deduction', 15, 2)->default(0)->after('other_allowance');
            }
            if (!Schema::hasColumn('salaries', 'jht_percent')) {
                $table->decimal('jht_percent', 5, 2)->default(2.00)->after('routine_deduction')->comment('Jaminan Hari Tua %');
            }
            if (!Schema::hasColumn('salaries', 'kes_percent')) {
                $table->decimal('kes_percent', 5, 2)->default(1.00)->after('jht_percent')->comment('BPJS Kesehatan %');
            }
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn([
                'family_allowance',
                'meal_allowance',
                'transport_allowance',
                'thr_amount',
                'bonus_amount',
                'other_allowance',
                'routine_deduction',
                'jht_percent',
                'kes_percent'
            ]);
        });
    }
};