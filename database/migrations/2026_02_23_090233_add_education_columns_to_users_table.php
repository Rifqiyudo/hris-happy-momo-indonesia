<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom bidang jurusan.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan hanya kolom bidang jurusan
            // Diletakkan setelah kolom nik (atau sesuaikan dengan urutan tabel Anda)
            if (!Schema::hasColumn('users', 'educational_major')) {
                $table->string('educational_major')->nullable()->after('nik');
            }
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'educational_major')) {
                $table->dropColumn('educational_major');
            }
        });
    }
};