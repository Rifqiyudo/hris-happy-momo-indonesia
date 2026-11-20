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
        Schema::table('allowances', function (Blueprint $table) {
            // 1. Tambahkan kolom target (Bisa Null karena target bisa jadi Global/Semua Karyawan)
            $table->unsignedBigInteger('position_id')->nullable()->after('type');
            $table->unsignedBigInteger('user_id')->nullable()->after('position_id');

            // 2. Tambahkan Foreign Key untuk menjaga integritas data database
            // onDelete('set null') berarti jika jabatan/user dihapus, target tunjangan kembali menjadi Global
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowances', function (Blueprint $table) {
            // 1. Drop Foreign Key terlebih dahulu (wajib sebelum drop kolom)
            $table->dropForeign(['position_id']);
            $table->dropForeign(['user_id']);
            
            // 2. Drop Kolom
            $table->dropColumn(['position_id', 'user_id']);
        });
    }
};