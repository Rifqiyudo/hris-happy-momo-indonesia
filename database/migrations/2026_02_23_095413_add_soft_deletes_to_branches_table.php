<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk memperbarui struktur tabel branches.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // 1. Menambahkan kolom deleted_at untuk fitur SoftDeletes
            // Ini akan memperbaiki error "Unknown column branches.deleted_at"
            if (!Schema::hasColumn('branches', 'deleted_at')) {
                $table->softDeletes()->after('is_active');
            }

            // 2. Memastikan kolom pendukung lainnya ada (untuk sinkronisasi Model)
            if (!Schema::hasColumn('branches', 'pic_name')) {
                $table->string('pic_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('branches', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('branches', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'province')) {
                $table->string('province')->nullable()->after('city');
            }
            if (!Schema::hasColumn('branches', 'timezone')) {
                $table->string('timezone')->default('Asia/Jakarta')->after('radius');
            }
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Menghapus kembali kolom yang ditambahkan jika di-rollback
            $table->dropSoftDeletes();
            $table->dropColumn([
                'pic_name',
                'email',
                'city',
                'province',
                'timezone'
            ]);
        });
    }
};