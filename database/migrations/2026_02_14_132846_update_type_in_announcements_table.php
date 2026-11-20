<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * UP: Menambahkan 'success' ke dalam ENUM
     */
    public function up(): void
    {
        // Gunakan Raw SQL untuk modifikasi ENUM agar lebih stabil
        DB::statement("ALTER TABLE announcements MODIFY COLUMN type ENUM('info', 'warning', 'danger', 'success') NOT NULL DEFAULT 'info'");
    }

    /**
     * DOWN: Mengembalikan ke ENUM lama
     */
    public function down(): void
    {
        // 1. AMANKAN DATA: Ubah semua data 'success' menjadi 'info' terlebih dahulu
        // Tanpa ini, migrasi akan FAIL karena "Data truncated"
        DB::table('announcements')->where('type', 'success')->update(['type' => 'info']);

        // 2. Kembalikan definisi kolom ke ENUM lama
        DB::statement("ALTER TABLE announcements MODIFY COLUMN type ENUM('info', 'warning', 'danger') NOT NULL DEFAULT 'info'");
    }
};