<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom contract_duration belum ada
            if (!Schema::hasColumn('users', 'contract_duration')) {
                $table->integer('contract_duration')->nullable()->after('join_date');
            }
            
            // Cek apakah kolom employee_status belum ada
            if (!Schema::hasColumn('users', 'employee_status')) {
                $table->string('employee_status')->default('Permanent')->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hanya drop jika kolom memang ada
            if (Schema::hasColumn('users', 'contract_duration')) {
                $table->dropColumn('contract_duration');
            }
            if (Schema::hasColumn('users', 'employee_status')) {
                $table->dropColumn('employee_status');
            }
        });
    }
};