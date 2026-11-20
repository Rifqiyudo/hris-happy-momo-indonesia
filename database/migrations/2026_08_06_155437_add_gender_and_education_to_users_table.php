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
            // Menambahkan kolom gender jika belum ada di tabel users
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['Laki-laki', 'Perempuan'])
                      ->nullable()
                      ->after('name')
                      ->comment('Menyimpan data jenis kelamin karyawan');
            }

            // Menambahkan kolom education jika belum ada di tabel users
            if (!Schema::hasColumn('users', 'education')) {
                $table->string('education', 50)
                      ->nullable()
                      ->after('gender')
                      ->comment('Menyimpan data tingkat pendidikan terakhir (SD, SMP, SMA/SMK, S1, S2, S3)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom gender jika proses rollback/migrate:fresh dijalankan
            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }

            // Menghapus kolom education jika proses rollback/migrate:fresh dijalankan
            if (Schema::hasColumn('users', 'education')) {
                $table->dropColumn('education');
            }
        });
    }
};