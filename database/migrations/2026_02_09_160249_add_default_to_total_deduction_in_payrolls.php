<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menyelaraskan kolom tabel payrolls dengan kebutuhan Controller dan Model.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Mengubah kolom agar memiliki nilai default 0 guna mencegah error 'Field doesn't have a default value'
            if (Schema::hasColumn('payrolls', 'total_deduction')) {
                $table->decimal('total_deduction', 15, 2)->default(0)->change();
            } else {
                $table->decimal('total_deduction', 15, 2)->default(0)->after('total_allowance');
            }

            // Memastikan kolom overtime_pay tersedia untuk menampung data lembur
            if (!Schema::hasColumn('payrolls', 'overtime_pay')) {
                $table->decimal('overtime_pay', 15, 2)->default(0)->after('total_allowance');
            }

            // Memastikan kolom pendukung lainnya memiliki default value agar aman saat proses upsert
            $table->decimal('base_salary', 15, 2)->default(0)->change();
            $table->decimal('total_allowance', 15, 2)->default(0)->change();
            $table->decimal('net_salary', 15, 2)->default(0)->change();
            
            // Kolom untuk audit log kehadiran
            if (!Schema::hasColumn('payrolls', 'total_days_worked')) {
                $table->integer('total_days_worked')->default(0)->after('year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula jika diperlukan
            $table->decimal('total_deduction', 15, 2)->default(null)->change();
            
            if (Schema::hasColumn('payrolls', 'overtime_pay')) {
                $table->dropColumn('overtime_pay');
            }
            
            if (Schema::hasColumn('payrolls', 'total_days_worked')) {
                $table->dropColumn('total_days_worked');
            }
        });
    }
};