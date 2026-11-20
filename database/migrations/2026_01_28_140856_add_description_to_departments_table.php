<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom deskripsi ke tabel departments.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada (untuk menghindari error Duplicate Column)
            if (!Schema::hasColumn('departments', 'description')) {
                // Tipe 'text' dipilih karena deskripsi bisa panjang
                // 'nullable' artinya boleh dikosongkan
                // 'after' menempatkan kolom setelah 'name' agar rapi
                $table->text('description')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus kolom jika dilakukan Rollback.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Hapus kolom description jika ada
            if (Schema::hasColumn('departments', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};