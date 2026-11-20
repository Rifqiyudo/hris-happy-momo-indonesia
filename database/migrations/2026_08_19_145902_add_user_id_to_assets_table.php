<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom relasi pemegang aset dan tanggal penyerahan.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // 1. Menambahkan kolom user_id (Karyawan pemegang aset)
            if (!Schema::hasColumn('assets', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('status');
                
                // Membuat relasi Foreign Key agar data konsisten (Jika user dihapus, aset kembali menganggur/NULL)
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }

            // 2. Menambahkan kolom tanggal penyerahan
            if (!Schema::hasColumn('assets', 'assigned_date')) {
                $table->dateTime('assigned_date')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Fitur rollback (mengembalikan kondisi database ke semula jika dibatalkan).
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Menghapus Foreign Key dan Kolom user_id
            if (Schema::hasColumn('assets', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            // Menghapus Kolom assigned_date
            if (Schema::hasColumn('assets', 'assigned_date')) {
                $table->dropColumn('assigned_date');
            }
        });
    }
};