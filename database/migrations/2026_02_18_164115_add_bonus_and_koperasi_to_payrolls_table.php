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
            // Tambahkan kolom bonus setelah overtime_pay (jika ada) atau di akhir
            if (!Schema::hasColumn('payrolls', 'bonus')) {
                if (Schema::hasColumn('payrolls', 'overtime_pay')) {
                    $table->decimal('bonus', 15, 2)->default(0)->after('overtime_pay');
                } else {
                    $table->decimal('bonus', 15, 2)->default(0);
                }
            }

            // FIX ERROR: Tambahkan koperasi_deduction setelah net_salary 
            // agar lebih aman karena kolom net_salary pasti ada di tabel payroll
            if (!Schema::hasColumn('payrolls', 'koperasi_deduction')) {
                if (Schema::hasColumn('payrolls', 'net_salary')) {
                    $table->decimal('koperasi_deduction', 15, 2)->default(0)->after('net_salary');
                } else {
                    $table->decimal('koperasi_deduction', 15, 2)->default(0);
                }
            }

            if (!Schema::hasColumn('payrolls', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['bonus', 'koperasi_deduction', 'notes']);
        });
    }
};