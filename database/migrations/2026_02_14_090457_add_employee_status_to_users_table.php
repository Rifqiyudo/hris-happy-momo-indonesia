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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan status karyawan: Permanent (Tetap), Contract (Kontrak), Probation (Percobaan)
            // Diletakkan setelah kolom 'role' agar struktur database rapi
            if (!Schema::hasColumn('users', 'employee_status')) {
                $table->string('employee_status')
                      ->default('Permanent')
                      ->after('role')
                      ->comment('Permanent, Contract, Probation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_status')) {
                $table->dropColumn('employee_status');
            }
        });
    }
};