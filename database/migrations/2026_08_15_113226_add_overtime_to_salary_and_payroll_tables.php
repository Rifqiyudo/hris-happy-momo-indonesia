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
        // Tambah kolom overtime_amount ke tabel salaries jika belum ada
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'overtime_amount')) {
                $table->double('overtime_amount')->default(0)->after('bonus_amount');
            }
        });

        // Tambah kolom overtime_amount ke tabel payrolls jika belum ada
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'overtime_amount')) {
                $table->double('overtime_amount')->default(0)->after('bonus_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom overtime_amount dari salaries secara aman jika kolomnya ada
        if (Schema::hasColumn('salaries', 'overtime_amount')) {
            Schema::table('salaries', function (Blueprint $table) {
                $table->dropColumn('overtime_amount');
            });
        }

        // Hapus kolom overtime_amount dari payrolls secara aman jika kolomnya ada
        if (Schema::hasColumn('payrolls', 'overtime_amount')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropColumn('overtime_amount');
            });
        }
    }
};