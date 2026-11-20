<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk menambahkan fitur Visit/Kunjungan pada tabel Attendances.
 * Dilengkapi dengan pengecekan keamanan (Safe Migration) untuk mencegah error SQL 1091.
 */
class AddAddressToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom pendukung koordinat, alamat, foto, dan keterlambatan.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // 1. Kategori Absensi (Office/Visit)
            if (!Schema::hasColumn('attendances', 'category')) {
                $table->string('category')->default('office')->after('status');
            }

            // 2. Data Koordinat (Menggunakan double untuk presisi GPS)
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->double('latitude')->nullable()->after('category');
            }
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->double('longitude')->nullable()->after('latitude');
            }

            // 3. Alamat Lengkap Geocoding
            if (!Schema::hasColumn('attendances', 'address')) {
                $table->text('address')->nullable()->after('longitude');
            }

            // 4. Foto Bukti Kunjungan (Selfie/Visit)
            if (!Schema::hasColumn('attendances', 'image_visit')) {
                $table->string('image_visit')->nullable()->after('address');
            }

            // 5. Durasi Keterlambatan dalam Menit
            if (!Schema::hasColumn('attendances', 'late_minutes')) {
                $table->integer('late_minutes')->default(0)->after('image_visit');
            }
        });
    }

    /**
     * Reverse the migrations (Rollback).
     * Menggunakan looping agar proses tidak berhenti jika salah satu kolom sudah hilang.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $columns = [
                'category',
                'latitude',
                'longitude',
                'address',
                'image_visit',
                'late_minutes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}