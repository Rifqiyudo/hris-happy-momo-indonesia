<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom informasi penempatan cabang dan tanggal bergabung karyawan.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambah Kolom Branch (Cabang)
            // Menggunakan if untuk mencegah error "Duplicate column"
            if (!Schema::hasColumn('users', 'branch')) {
                $table->string('branch')->nullable()->after('position_id');
            }

            // 2. Tambah Kolom Tanggal Bergabung (Penting untuk hitung masa kerja)
            if (!Schema::hasColumn('users', 'join_date')) {
                $table->date('join_date')->nullable()->after('branch');
            }
        });
    }

    /**
     * Reverse the migrations (Rollback).
     * Menggunakan looping agar tidak menyebabkan error SQL 1091 jika kolom sudah hilang.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['branch', 'join_date'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};