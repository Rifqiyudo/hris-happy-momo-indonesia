<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update status karyawan secara default ke 'Permanent' bagi yang masih NULL
        DB::table('users')
            ->whereNull('employee_status')
            ->update(['employee_status' => 'Permanent']);

        // 2. Update cabang PT. Aimi Plastik (ID 2) berdasarkan pola NIK
        DB::table('users')
            ->where('nik', 'LIKE', 'AIMI%')
            ->update(['branch_id' => 2]);

        // 3. Update cabang PT. Panda Playhouse (ID 3) berdasarkan pola NIK
        DB::table('users')
            ->where('nik', 'LIKE', 'PANDA%')
            ->update(['branch_id' => 3]);
            
        // Catatan: Sisanya akan tetap di ID 1 (PT. Happy Momo Indonesia) sesuai default sebelumnya
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opsional: Logika untuk membatalkan perubahan jika diperlukan
        // Biasanya untuk data, kita membiarkannya saja atau mengembalikan ke null
    }
};